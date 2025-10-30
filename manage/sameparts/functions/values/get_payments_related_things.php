<?php
namespace manage\sameparts\functions;
require "../../../../matrix_library/php/auto_loader.php";

use config\db;
use config\sessions;
use config\sessions\check;
use sameparts\php\db_query\payments;
use config\table_helper\order_payments as tbl;
use config\table_helper\order_payment_status_types as tbl2;
use matrix_library\php\operations\user;
use matrix_library\php\operations\clear_types;
use matrix_library\php\operations\variable;
use \sameparts\php\ajax\echo_values;

/* CONST Values */
class post_keys {
    const PAGE_NAME = "page_name",
        GET_TYPE = "get_type";
}
/* end Const Values */

if(
    check::check(false) &&
    user::check_sent_data(
        array(
            post_keys::PAGE_NAME,
            post_keys::GET_TYPE
        )
    )
){
    $db = new db(\config\database_list::LIVE_MYSQL_1);
    $sessions = new sessions();
    $echo = new echo_values();
    set_echo_values($db, $sessions, $echo);
    $echo->return();
}

function set_echo_values(db $db, sessions $sessions, echo_values &$echo) : void{
    $type = variable::clear_method(post_keys::GET_TYPE, clear_types::INT);

    if(($type == get_types::ALL || $type ==  get_types::PAYMENTS)) $echo->rows["order_payments"] = (array)payments::get($db, $sessions->get->LANGUAGE_TAG, $sessions->get->BRANCH_ID, safe_id: 0, order_by: tbl::ID." DESC");
    if(($type == get_types::ALL || $type ==  get_types::STATUS_TYPES)) $echo->rows["order_payment_status_types"] = (array)payments::get_status_types($db, $sessions->get->LANGUAGE_TAG, order_by: tbl2::ID." asc");
}

class get_types {
    const ALL = 0x0001, PAYMENTS = 0x0002, STATUS_TYPES = 0x0003;
}