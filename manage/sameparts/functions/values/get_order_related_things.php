<?php
require "../../../../matrix_library/php/auto_loader.php";

use config\db;
use config\sessions\check;
use sameparts\php\db_query\orders;
use config\table_helper\order_types as tbl3;
use config\table_helper\order_status_types as tbl4;
use matrix_library\php\operations\user;
use config\sessions\keys;
use matrix_library\php\operations\method_types;
use config\sessions;
use matrix_library\php\operations\clear_types;
use matrix_library\php\operations\variable;
use sameparts\php\ajax\echo_values;

/* CONST Values */
class post_keys {
    const PAGE_NAME = "page_name",
        GET_TYPE = "get_type",
        SAFE_ID = "safe_id";
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

    if(user::post(post_keys::PAGE_NAME) == "report_safe"){
        $echo->rows["order_status_types"] = (array)orders::order_status_types($db, $sessions->get->LANGUAGE_TAG, order_by: tbl4::ID." desc");
        $echo->rows["order_types"] = (array)orders::get_types($db, $sessions->get->LANGUAGE_TAG, order_by: tbl3::ID." desc");
    }
}

class get_types {
    const ALL = 0x0001,
        ORDERS = 0x0002;
}