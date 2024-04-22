<?php
namespace manage\functions\branch_settings\get;

use config\db;
use config\settings;
use config\sessions;
use matrix_library\php\operations\user;
use sameparts\php\ajax\echo_values;
use sameparts\php\db_query\address as address_service;


class post_keys{ const TYPE = "next_type", ID = "id"; }
class address_types {const CITY = 1, TOWN = 2, DISTRICT = 3, NEIGHBORHOOD = 4; }

class address{
    function __construct(db $db, sessions $sessions, echo_values &$echo){
        if ($sessions->get->BRANCH_ID > 0) {
            $address = new address_service();
            switch (user::post(post_keys::TYPE)) {
                case address_types::CITY:
                    $echo->rows = (array)$address->get_city();
                    break;
                case address_types::TOWN:
                    $echo->rows = (array)$address->get_town(user::post(post_keys::ID))->rows;
                    break;
                case address_types::DISTRICT:
                    $echo->rows = (array)$address->get_district(user::post(post_keys::ID))->rows;
                    break;
                case address_types::NEIGHBORHOOD:
                    $echo->rows = (array)$address->get_neighborhood(user::post(post_keys::ID))->rows;
                    break;
                default:
                    $echo->message = user::post(post_keys::TYPE);
                    $echo->status = false;
                    $echo->error_code = settings::error_codes()::NOT_FOUND;
                    break;
            }
        }
    }
}
