<?php
namespace integrations\companies\integrated\yemek_sepeti\php\functions;
require "../../../../../../matrix_library/php/auto_loader.php";

use config\sessions;
use config\type_tables_values\integrate_types;
use integrations\companies\integrated\sameparts\functions\get\service;
use integrations\companies\integrated\yemek_sepeti\php\functions\get\orders;
use integrations\companies\integrated\yemek_sepeti\php\functions\get\products;
use integrations\companies\integrated\yemek_sepeti\php\functions\get\restaurant_list;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use sameparts\php\ajax\echo_values;

/* CONST Values */
class post_keys {
    const GET_TYPE = "get_type", USER_NAME = "user_name", PASSWORD = "password";
}

class get_types {
    const ORDERS = 0x0001, PRODUCTS = 0x0002, RESTAURANT_LIST = 0x0003;
}
/* end CONST Values */

$echo = new echo_values();
$sessions = new sessions();

if(
    user::check_sent_data([post_keys::GET_TYPE])
) {
    variable::clear_all_data($_POST);

    if(user::post(post_keys::USER_NAME) && user::post(post_keys::PASSWORD)){
        $sessions->set->INTEGRATION(
            sessions::INTEGRATION_KEYS()::YEMEK_SEPETI,
            new \config\sessions\integrations\results(user::post(post_keys::USER_NAME), user::post(post_keys::PASSWORD))
        );
    }

    $service = service::get($sessions, integrate_types::YEMEK_SEPETI);

    switch (user::post(post_keys::GET_TYPE)){
        case get_types::ORDERS:
            (new orders($service, $sessions, $echo));
            break;
        case get_types::PRODUCTS:
            (new products($service, $sessions, $echo));
            break;
        case get_types::RESTAURANT_LIST:
            (new restaurant_list($service, $sessions, $echo));
            break;
    }

}
$echo->return();
/* end Functions */