<?php
namespace manage\functions\settings_integration;
require "../../../matrix_library/php/auto_loader.php";

use config\database_list;
use config\db;
use integrations\companies\integrated\sameparts\functions\get\service;
use manage\functions\settings_integration\set\account;
use manage\functions\settings_integration\set\product;
use config\sessions\check;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use config\sessions;
use sameparts\php\ajax\echo_values;

/* CONST Values */

class set_types {
    const ACCOUNT = 0x0001, PRODUCT = 0x0002;

}

class post_keys {
    const SET_TYPE = "set_type", TYPE = "type";
}

/* end CONST Values */

if(user::check_sent_data([post_keys::SET_TYPE, post_keys::TYPE]) && check::check(false)) {
    $echo = new echo_values();

    $db = new db(database_list::LIVE_MYSQL_1);
    $sessions = new sessions();
    variable::clear_all_data($_POST);

    switch (user::post(post_keys::SET_TYPE)){
        case set_types::ACCOUNT: (new account($db, $sessions, $echo)); break;
        case set_types::PRODUCT: (new product($db, $sessions, $echo)); break;
    }

    $echo->return();
}

/* end Functions */