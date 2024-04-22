<?php
namespace manage\functions\settings_integration;
require "../../../matrix_library/php/auto_loader.php";

use config\database_list;
use config\db;
use integrations\companies\integrated\sameparts\functions\get\service;
use manage\functions\settings_integration\get\products;
use config\sessions\check;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use config\sessions;
use sameparts\php\ajax\echo_values;

/* CONST Values */
class get_types {
    const PRODUCTS = 0x0001;

}

class post_keys {
    const GET_TYPE = "get_type", TYPE = "type";
}
/* end CONST Values */


if(user::check_sent_data([post_keys::GET_TYPE, post_keys::TYPE]) && check::check(false)) {
    $echo = new echo_values();
    $db = new db(database_list::LIVE_MYSQL_1);
    $sessions = new sessions();

    variable::clear_all_data($_POST);

    switch (user::post(post_keys::GET_TYPE)){
        case get_types::PRODUCTS: (new products($db, $sessions, $echo)); break;
    }

    $echo->return();
}

/* end Functions */