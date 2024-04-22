<?php
namespace pos\functions\finance;
require "../../../matrix_library/php/auto_loader.php";

use config\database_list;
use config\db;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use pos\functions\finance\set\close_safe;
use pos\functions\finance\set\cost;
use pos\functions\finance\set\trust_account_insert;
use config\sessions\check;
use config\sessions;
use sameparts\php\ajax\echo_values;

/* CONST Values */
class post_keys {
    const SET_TYPE = "set_type";
}

class set_types {
    const TRUST_ACCOUNT_INSERT = 0x0001,
        CLOSE_SAFE = 0x0002,
        COST = 0x0003;
}
/* end CONST Values */

if(user::check_sent_data([post_keys::SET_TYPE]) && check::check(false)) {
    $db = new db(database_list::LIVE_MYSQL_1);
    $sessions = new sessions();
    $echo = new echo_values();
    variable::clear_all_data($_POST);

    switch (user::post(post_keys::SET_TYPE)){
        case set_types::TRUST_ACCOUNT_INSERT:
            (new trust_account_insert($db, $sessions, $echo));
            break;
        case set_types::CLOSE_SAFE:
            $db_backup = new db(database_list::BACKUP_MYSQL_1);
            (new close_safe($db, $db_backup, $sessions, $echo));
            break;
        case set_types::COST:
            (new cost($db, $sessions, $echo));
            break;
    }
    $echo->return();
}


/* end Functions */