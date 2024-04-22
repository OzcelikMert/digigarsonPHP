<?php
namespace pos\sameparts\functions;
require "../../../../matrix_library/php/auto_loader.php";

use config\db;
use config\sessions\check;
use config\sessions;
use sameparts\php\db_query\branch_payment_types;
use config\table_helper\branch_payment_types as tbl;
use config\table_helper\payment_types as tbl2;
use matrix_library\php\operations\user;
use matrix_library\php\operations\method_types;
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

    if(($type == get_types::ALL || $type ==  get_types::BRANCH_PAYMENT_TYPES)) $echo->rows["branch_payment_types"] = (array)branch_payment_types::get($db, $sessions->get->BRANCH_ID, order_by: tbl::ID." ASC");
    if(($type == get_types::ALL || $type ==  get_types::PAYMENT_TYPES)) $echo->rows["payment_types"] = (array)branch_payment_types::get_types($db, $sessions->get->LANGUAGE_TAG, order_by: tbl2::ID." asc");
}

class get_types {
    const ALL = 0x0001, PAYMENT_TYPES = 0x0002, BRANCH_PAYMENT_TYPES = 0x0003;
}