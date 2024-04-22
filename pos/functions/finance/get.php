<?php
namespace pos\functions\finance;
require "../../../matrix_library/php/auto_loader.php";

use config\database_list;
use config\db;
use config\sessions;
use config\sessions\check;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use pos\functions\finance\get\costs;
use sameparts\php\ajax\echo_values;
use pos\functions\finance\get\z_report;

/* CONST Values */
class post_keys {
    const GET_TYPE = "get_type";
}

class get_types {
    const COST = 0x0001,
        Z_REPORT = 0x0002;
}
/* end CONST Values */

if(user::check_sent_data([post_keys::GET_TYPE]) && check::check(false)) {
    $db = new db(database_list::LIVE_MYSQL_1);
    $db_backup = new db(database_list::BACKUP_MYSQL_1);
    $echo = new echo_values();
    $sessions = new sessions();

    variable::clear_all_data($_POST);

    switch (user::post(post_keys::GET_TYPE)){
        case get_types::COST:
            (new costs($db, $db_backup, $sessions, $echo));
            break;
        case get_types::Z_REPORT:
            (new z_report($db, $sessions, $echo));
            break;
    }

    $echo->return();
}
/* end Functions */