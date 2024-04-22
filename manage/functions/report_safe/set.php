<?php
namespace manage\functions\report_safe;
require "../../../matrix_library/php/auto_loader.php";

use config\database_list;
use config\db;
use manage\functions\report_safe\set\cost;
use manage\functions\report_safe\set\invoice_edit;
use manage\functions\report_safe\set\trust_account_insert;
use manage\functions\report_safe\set\trust_payment;
use config\sessions\check;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use config\sessions;
use sameparts\php\ajax\echo_values;

/* CONST Values */
class post_keys {
    const SET_TYPE = "set_type";
}

class set_types {
    const TRUST_ACCOUNT_INSERT = 0x0001,
        CLOSE_SAFE = 0x0002,
        COST = 0x0003,
        INVOICE_EDIT = 0x0004,
        TRUST_ACCOUNT_PAYMENT = 0x0005;
}
/* end CONST Values */

if(user::check_sent_data([post_keys::SET_TYPE]) && check::check(false)) {
    $db = new db(database_list::LIVE_MYSQL_1);
    $db_backup = new db(database_list::BACKUP_MYSQL_1);
    $echo = new echo_values();
    $sessions = new sessions();

    variable::clear_all_data($_POST);

    switch (user::post(post_keys::SET_TYPE)){
        case set_types::TRUST_ACCOUNT_INSERT:
            (new trust_account_insert($db, $sessions, $echo));
            break;
        case set_types::COST:
            (new cost($db, $db_backup, $sessions, $echo));
            break;
        case set_types::INVOICE_EDIT:
            (new invoice_edit($db, $db_backup, $sessions, $echo));
            break;
        case set_types::TRUST_ACCOUNT_PAYMENT:
            (new trust_payment($db, $db_backup, $sessions, $echo));
            break;
    }

    $echo->return();
}
/* end Functions */