<?php
namespace manage\functions\report_safe;
require "../../../matrix_library/php/auto_loader.php";

use config\database_list;
use config\db;
use config\settings\application_names;
use manage\functions\report_safe\get\costs;
use manage\functions\report_safe\get\invoice;
use manage\functions\report_safe\get\safe;
use manage\functions\report_safe\get\safe_list;
use manage\functions\report_safe\get\trust;
use manage\functions\report_safe\get\trust_payments;
use manage\functions\report_safe\get\z_report;
use config\sessions\check;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use config\sessions;
use sameparts\php\ajax\echo_values;

/* CONST Values */
class post_keys {
    const GET_TYPE = "get_type";
}

class get_types {
    const SAFE_LIST = 0x0001, SAFE = 0x0002, INVOICE = 0x0003, TRUST = 0x0004, TRUST_PAYMENTS = 0x0005, COST = 0x0006, Z_REPORT = 0x0007;
}
/* end CONST Values */

if(user::check_sent_data([post_keys::GET_TYPE]) && (check::check(false) || check::check(false, application_names::POS))) {
    $db = new db(database_list::LIVE_MYSQL_1);
    $db_backup = new db(database_list::BACKUP_MYSQL_1);
    $echo = new echo_values();
    $sessions = new sessions();

    variable::clear_all_data($_POST);

    switch (user::post(post_keys::GET_TYPE)){
        case get_types::SAFE_LIST:
            (new safe_list($db, $sessions, $echo));
            break;
        case get_types::SAFE:
            (new safe($db, $db_backup, $sessions, $echo));
            break;
        case get_types::INVOICE:
            (new invoice($db, $db_backup, $sessions, $echo));
            break;
        case get_types::TRUST:
            (new trust($db, $db_backup, $sessions, $echo));
            break;
        case get_types::TRUST_PAYMENTS:
            (new trust_payments($db, $db_backup, $sessions, $echo));
            break;
        case get_types::COST:
            (new costs($db, $db_backup, $sessions, $echo));
            break;
        case get_types::Z_REPORT:
            (new z_report($db, $db_backup, $sessions, $echo));
            break;
    }

    $echo->return();
}
/* end Functions */