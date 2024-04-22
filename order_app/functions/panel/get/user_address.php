<?php
namespace order_app\functions\panel\get;

use config\db;
use config\settings;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use sameparts\php\ajax\echo_values;
use sameparts\php\db_query\address as address_service;


class post_keys {
    const TYPE = "next_type",
         ID = "id";
}
class login_types {
    const CITY = 1,
            TOWN = 2,
            DISTRICT = 3,
            NEIGHBORHOOD = 4;
}

class address{

    function __construct(echo_values &$echo){
        if ($echo->status){
            $address = new address_service();
            switch (user::post(post_keys::TYPE)){
                case login_types::CITY:
                    $echo->rows = (array)  $address->get_city();
                    break;
                case login_types::TOWN:
                    $echo->rows= (array)  $address->get_town(user::post(post_keys::ID))->rows;
                    break;
                case login_types::DISTRICT:
                    $echo->rows = (array)  $address->get_district(user::post(post_keys::ID))->rows;
                    break;
                case login_types::NEIGHBORHOOD:
                    $echo->rows = (array)  $address->get_neighborhood(user::post(post_keys::ID))->rows;
                    break;
                default:
                    $echo->status = false;
                    $echo->error_code = settings::error_codes()::NOT_FOUND;
                    break;
            }

        } else $echo->error_code = settings::error_codes()::WRONG_VALUE;
    }

}

