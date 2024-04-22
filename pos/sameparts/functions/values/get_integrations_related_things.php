<?php
require "../../../../matrix_library/php/auto_loader.php";

use config\db;
use config\sessions\check;
use matrix_library\php\operations\user;
use config\sessions;
use matrix_library\php\operations\clear_types;
use matrix_library\php\operations\variable;
use sameparts\php\ajax\echo_values;
use sameparts\php\db_query\integrate;

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
    $db = new db(config\database_list::LIVE_MYSQL_1);
    $sessions = new sessions();
    $echo = new echo_values();

    set_echo_values($db, $sessions, $echo);
    $echo->return();
}

function set_echo_values(db $db, sessions $sessions, echo_values &$echo) : void{
    $type = variable::clear_method(post_keys::GET_TYPE, clear_types::INT);

    if(user::post(post_keys::PAGE_NAME) == "orders"){
        if($type == get_types::ALL || $type == get_types::PRODUCTS) $echo->rows["products"] = integrate::get_products($db, $sessions->get->BRANCH_ID);
        if($type == get_types::ALL || $type == get_types::OPTIONS) $echo->rows["options"] = integrate::get_product_options($db, $sessions->get->BRANCH_ID);
        if($type == get_types::ALL || $type == get_types::ORDERS) $echo->rows["orders"] = integrate::get_orders($db, $sessions->get->BRANCH_ID);
        if($type == get_types::ALL || $type == get_types::PAYMENT_TYPES) $echo->rows["payment_types"] = integrate::get_payment_types($db);
    }

}

class get_types {
    const ALL = 0x0001,
        PRODUCTS = 0x0002,
        OPTIONS = 0x0003,
        ORDERS = 0x0004,
        PAYMENT_TYPES = 0x0005;
}
