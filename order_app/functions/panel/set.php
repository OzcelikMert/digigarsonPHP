<?php
namespace order_app\functions\panel;
require "../../../matrix_library/php/auto_loader.php";

use config\database_list;
use config\db;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;

use order_app\functions\panel\get\address as get_address;
use order_app\functions\panel\get\branch;
use order_app\functions\panel\get\orders;
use order_app\functions\panel\set\address as set_address;
use order_app\functions\panel\set\language;
use order_app\functions\panel\set\notification;
use order_app\functions\panel\set\send_order;
use order_app\sameparts\functions\sessions\get;
use sameparts\php\ajax\echo_values;
use order_app\functions\panel\set\register;


class post_keys {
    const SET_TYPE = "set_type";
}
class set_types {
    const REGISTER = 1,
        GET_BRANCH = 2,
        GET_ADDRESS = 3,
        SET_ADDRESS = 4,
        SEND_ORDER = 10,
        GET_ORDERS = 11,
        LANGUAGE = 12,
        NOTIFICATIONS = 13;
}
if ($_POST) {
    $db = new db(database_list::LIVE_MYSQL_1);
    $echo = new echo_values();
    //$echo->custom_data["post"] = $_POST;

    $sessions = new get();
    variable::clear_all_data($_POST);

    switch (user::post(post_keys::SET_TYPE)) {
        case set_types::REGISTER:      new register($db, $sessions,$echo);     break;
        case set_types::GET_BRANCH:    new branch($db,$sessions,$echo);       break;
        case set_types::GET_ADDRESS:   new get_address($db,$echo);      break;
        case set_types::SET_ADDRESS:   new set_address($db, $echo); break;
        case set_types::SEND_ORDER:    new send_order($db, $sessions,$echo); break;
        case set_types::GET_ORDERS:    new orders($db, $sessions,$echo); break;
        case set_types::LANGUAGE:      new language($db,$sessions,$echo); break;
        case set_types::NOTIFICATIONS: new notification($db,$sessions,$echo); break;
    }
    $echo->return();
}


