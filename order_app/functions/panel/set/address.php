<?php
namespace order_app\functions\panel\set;

use config\db;
use config\settings;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\user;
use order_app\sameparts\functions\sessions\keys;
use sameparts\php\ajax\echo_values;
use config\table_helper\customer_address as tbl;

class post_keys {
    const
        ADDRESS_TYPE = "address_type",
        TITLE = "title",
        PHONE = "phone",
        CITY = "city",
        TOWN = "town",
        DISTRICT = "district",
        NEIGHBORHOOD = "neighborhood",
        STREET = "street",
        FLOOR = "floor",
        HOME_NUMBER = "home_number",
        APARTMENT_NUMBER = "apartment_number",
        ADDRESS_DESCRIPTION = "address_description",
        ID = "id",
        ADDRESS_ID = "address_id",
        TYPE = "type";
}
class types{
    const DEL = 6;
}

class address{

    function __construct(db $db, echo_values &$echo){
        if ($echo->status) {

            if (user::post(post_keys::ADDRESS_ID) == 0){
                $echo->custom_data["add"] = $this->add($db,$echo);

            }else if (user::post(post_keys::ADDRESS_ID) > 0){
                if (user::post(post_keys::TYPE) == types::DEL){
                    $echo->custom_data["del"] = $this->delete($db,$echo);
                }else if(user::post(post_keys::ADDRESS_ID) > 0){
                    $echo->custom_data["update"] = $this->update($db,$echo);
                }
            }
            
        }
    }

    function check(echo_values &$echo_values){
            if (!user::check_sent_data(
                array(
                    post_keys::ID,
                    post_keys::ADDRESS_TYPE,
                    post_keys::TITLE,
                    post_keys::CITY,
                    post_keys::TOWN,
                    post_keys::DISTRICT,
                    post_keys::NEIGHBORHOOD,
                    post_keys::STREET,
                    post_keys::APARTMENT_NUMBER,
                    post_keys::FLOOR,
                    post_keys::HOME_NUMBER,
                    post_keys::ADDRESS_DESCRIPTION,
            ))) {
                $echo_values->status = false;
                $echo_values->error_code = settings::error_codes()::INCORRECT_DATA;
            }
    }
    function add(db $db,echo_values &$echo) : results{
        return $db->db_insert(
           tbl::TABLE_NAME,array(
                tbl::USER_ID => user::session(keys::USER_ID),
                tbl::ADDRESS_TYPE => user::post(post_keys::ADDRESS_TYPE),
                tbl::TITLE => user::post(post_keys::TITLE),
                tbl::PHONE => user::post(post_keys::PHONE),
                tbl::CITY => user::post(post_keys::CITY),
                tbl::TOWN => user::post(post_keys::TOWN),
                tbl::DISTRICT => user::post(post_keys::DISTRICT),
                tbl::NEIGHBORHOOD => user::post(post_keys::NEIGHBORHOOD),
                tbl::STREET => user::post(post_keys::STREET),
                tbl::APARTMENT_NUMBER => user::post(post_keys::APARTMENT_NUMBER),
                tbl::FLOOR => user::post(post_keys::FLOOR),
                tbl::HOME_NUMBER => user::post(post_keys::HOME_NUMBER),
                tbl::ADDRESS_DESCRIPTION => user::post(post_keys::ADDRESS_DESCRIPTION),
           )
       );
    }
    function update(db $db,echo_values &$echo): results{
        return $db->db_update(
            tbl::TABLE_NAME,array(
                tbl::ADDRESS_TYPE => user::post(post_keys::ADDRESS_TYPE),
                tbl::TITLE => user::post(post_keys::TITLE),
                tbl::PHONE => user::post(post_keys::PHONE),
                tbl::CITY => user::post(post_keys::CITY),
                tbl::TOWN => user::post(post_keys::TOWN),
                tbl::DISTRICT => user::post(post_keys::DISTRICT),
                tbl::NEIGHBORHOOD => user::post(post_keys::NEIGHBORHOOD),
                tbl::STREET => user::post(post_keys::STREET),
                tbl::APARTMENT_NUMBER => user::post(post_keys::APARTMENT_NUMBER),
                tbl::FLOOR => user::post(post_keys::FLOOR),
                tbl::HOME_NUMBER => user::post(post_keys::HOME_NUMBER),
                tbl::ADDRESS_DESCRIPTION => user::post(post_keys::ADDRESS_DESCRIPTION),
            ),where: $db->where->equals([tbl::USER_ID => user::session(keys::USER_ID), tbl::ID => user::post(post_keys::ADDRESS_ID)])
        );
    }
    function delete(db $db,echo_values &$echo): results{
        return $db->db_delete(
            tbl::TABLE_NAME,
            where: $db->where->equals([tbl::USER_ID => user::session(keys::USER_ID), tbl::ID => user::post(post_keys::ADDRESS_ID)])
        );
    }



}

