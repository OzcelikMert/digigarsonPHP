<?php
namespace pos\sameparts\functions\caller_id\get;

use config\db;
use config\settings;
use config\sessions;
use config\table_helper\customer_address as tbl;
use matrix_library\php\operations\user;
use sameparts\php\ajax\echo_values;
use sameparts\php\db_query\address as address_service;


class post_keys {
    const TYPE = "next_type",
         ID = "id",
        ADDRESS_ID = "address_id";
}
class login_types {
    const CITY = 1, TOWN = 2, DISTRICT = 3, NEIGHBORHOOD = 4, USER = 5;
}


class address{
    function __construct(db $db,sessions $sessions,echo_values &$echo){
        if ($sessions->get->BRANCH_ID > 0) {
            $address = new address_service();
            switch (user::post(post_keys::TYPE)) {
                case login_types::CITY:
                    $echo->rows = (array)$address->get_city()->rows;
                    break;
                case login_types::TOWN:
                    $echo->rows = (array)$address->get_town(user::post(post_keys::ID))->rows;
                    break;
                case login_types::DISTRICT:
                    $echo->rows = (array)$address->get_district(user::post(post_keys::ID))->rows;
                    break;
                case login_types::NEIGHBORHOOD:
                    $echo->rows = (array)$address->get_neighborhood(user::post(post_keys::ID))->rows;
                    break;
                case login_types::USER:
                    $this->get($db,$echo);
                    break;
                default:
                    $echo->message = user::post(post_keys::TYPE);
                    $echo->status = false;
                    $echo->error_code = settings::error_codes()::NOT_FOUND;
                    break;
            }
        }
    }

    function get(db $db,echo_values &$echo){
            $address = new address_service();
            $result = $db->db_select(
                tbl::ALL,
                tbl::TABLE_NAME,
                where: $db->where->equals([tbl::ID => user::post(post_keys::ADDRESS_ID)])
            );

            $echo->rows["user_address"] = $result;
            if ($result->status && count($result->rows) > 0){
                $address_rows = $address->get_multi_address($result->rows)->rows;

                foreach ($result->rows as $key => $item){
                    foreach ($address_rows as $value){
                        if ($item["city"] == $value["city_id"]
                            && $item["town"] == $value["town_id"]
                            && $item["district"] == $value["district_id"]
                            && $item["neighborhood"] == $value["neighborhood_id"]) {

                            $result->rows[$key]["city_name"] = $value["city"];
                            $result->rows[$key]["town_name"] = $value["town"];
                            $result->rows[$key]["district_name"] = $value["district"];
                            $result->rows[$key]["neighborhood_name"] = $value["neighborhood"];
                        }
                    }
                }
                $echo->rows = $result->rows;
                if (user::post(post_keys::ADDRESS_ID)){
                    $echo->custom_data["select"]["town"] =         $address->get_town($result->rows[0]["city"])->rows;
                    $echo->custom_data["select"]["district"] =     $address->get_district($result->rows[0]["town"])->rows;
                    $echo->custom_data["select"]["neighborhood"] = $address->get_neighborhood($result->rows[0]["district"])->rows;
                }
            } else { $echo->status = false; }
        }
}

