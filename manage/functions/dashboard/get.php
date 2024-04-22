<?php
namespace manage\functions\dashboard;
require "../../../matrix_library/php/auto_loader.php";

use config\database_list;
use config\db;
use manage\functions\dashboard\get\last_orders;
use manage\functions\dashboard\get\last_payments;
use manage\functions\dashboard\get\middle_chart;
use manage\functions\dashboard\get\top_charts;
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
    const TOP_CHARTS = 0x0001, MIDDLE_CHART = 0x0002, LAST_PAYMENTS = 0x0003, LAST_ORDERS = 0x0004;
}
/* end CONST Values */

if(user::check_sent_data([post_keys::GET_TYPE]) && check::check(false)) {
    $db = new db(database_list::LIVE_MYSQL_1);
    $db_backup = new db(database_list::BACKUP_MYSQL_1);
    $echo = new echo_values();
    $sessions = new sessions();

    variable::clear_all_data($_POST);

    switch (user::post(post_keys::GET_TYPE)){
        case get_types::TOP_CHARTS:
            (new top_charts($db_backup, $sessions, $echo));
            break;
        case get_types::MIDDLE_CHART:
            (new middle_chart($db_backup, $sessions, $echo));
            break;
        case get_types::LAST_PAYMENTS:
            (new last_payments($db, $sessions, $echo));
            break;
        case get_types::LAST_ORDERS:
            (new last_orders($db, $sessions, $echo));
            break;
    }

    $echo->return();
}
/* end Functions */