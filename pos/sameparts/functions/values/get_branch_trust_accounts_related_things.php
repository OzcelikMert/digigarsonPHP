<?php
require "../../../../matrix_library/php/auto_loader.php";

use config\db;
use config\sessions;
use config\sessions\check;
use config\table_helper\branch_trust_accounts as tbl;
use config\table_helper\branch_trust_account_payments as tbl2;
use matrix_library\php\operations\user;
use matrix_library\php\operations\clear_types;
use matrix_library\php\operations\variable;
use \sameparts\php\ajax\echo_values;
use sameparts\php\db_query\branch_trust_accounts;
use sameparts\php\helper\page_names;

/* CONST Values */
class post_keys {
    const PAGE_NAME = "page_name",
        GET_TYPE = "get_type";
}
/* end Const Values */
$sessions = new sessions();

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
    $echo = new echo_values();
    set_echo_values($db, $sessions, $echo);
    $echo->return();
}

function set_echo_values(db $db, sessions $sessions, echo_values &$echo) : void{
    $type = variable::clear_method(post_keys::GET_TYPE, clear_types::INT);

    if(($type == get_types::ALL || $type ==  get_types::ACCOUNTS)) $echo->rows["accounts"] = (array)branch_trust_accounts::get($db, $sessions->get->BRANCH_ID, order_by: tbl::ID." ASC");
    if(user::post(post_keys::PAGE_NAME) == page_names::POS()::FINANCE && ($type == get_types::ALL || $type ==  get_types::PAYMENTS)) $echo->rows["payments"] = (array)branch_trust_accounts::get_payments($db, $sessions->get->BRANCH_ID, order_by: tbl2::ID." asc");
}

class get_types {
    const ALL = 0x0001, ACCOUNTS = 0x0002, PAYMENTS = 0x0003;
}