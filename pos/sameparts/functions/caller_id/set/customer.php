<?php
namespace pos\sameparts\functions\caller_id\set;

use config\db;
use config\settings;
use config\sessions;
use config\settings\error_codes;
use config\table_helper\customer_users as tbl;
use config\table_helper\customer_address as tbl2;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use sameparts\php\ajax\echo_values;
use sameparts\php\helper\variable_filters;

class post_keys {
    const USER_ID = "user_id",
        NAME = "name",
        PHONE = "phone",
        CITY = "city",
        TOWN = "town",
        DISTRICT = "district",
        NEIGHBORHOOD = "neighborhood",
        ADDRESS_DETAIL = "address_detail",
        BUILDING_NO = "building_no",
        FLOOR = "floor",
        APARTMENT_NO = "apartment_no",
        TITLE = "title",
        ADDRESS_TYPE = "address_type",
        STREET = "street";
}

class customer {
    public function __construct(db $db, sessions $sessions, echo_values &$echo) {
        $phone = variable_filters::phone(user::post(post_keys::PHONE));
        user::post(post_keys::PHONE, (count($phone) > 0) ? $phone[0] : "");

        $this->check_values($db, $sessions, $echo);
        if($echo->error_code == error_codes::SUCCESS){
            if(!user::post(post_keys::USER_ID))
                user::post(post_keys::USER_ID, $this->insert_user($db, $sessions));
            $this->insert_address($db, $sessions);
        }
    }

    private function insert_user(db $db, sessions $sessions) : int{
        return $db->db_insert(tbl::TABLE_NAME,
            array(
                tbl::PHONE => user::post(post_keys::PHONE),
                tbl::NAME => user::post(post_keys::NAME),
                tbl::LANGUAGE_ID => $sessions->get->LANGUAGE_ID,
                tbl::PHONE_CONFIRM_CODE => "",
            ),
        )->insert_id;
    }

    private function insert_address(db $db, sessions $sessions) {
        return $db->db_insert(
            tbl2::TABLE_NAME,array(
                tbl2::USER_ID => user::post(post_keys::USER_ID),
                tbl2::ADDRESS_TYPE => user::post(post_keys::ADDRESS_TYPE),
                tbl2::TITLE => user::post(post_keys::TITLE),
                tbl2::PHONE => user::post(post_keys::PHONE),
                tbl2::CITY => user::post(post_keys::CITY),
                tbl2::TOWN => user::post(post_keys::TOWN),
                tbl2::DISTRICT => user::post(post_keys::DISTRICT),
                tbl2::NEIGHBORHOOD => user::post(post_keys::NEIGHBORHOOD),
                tbl2::STREET => user::post(post_keys::STREET),
                tbl2::APARTMENT_NUMBER => user::post(post_keys::APARTMENT_NO),
                tbl2::FLOOR => user::post(post_keys::FLOOR),
                tbl2::HOME_NUMBER => user::post(post_keys::BUILDING_NO),
                tbl2::ADDRESS_DESCRIPTION => user::post(post_keys::ADDRESS_DETAIL),
            )
        );
    }

    private function check_values(db $db, sessions $sessions, echo_values &$echo){
        if(variable::is_empty(
            user::post(post_keys::NAME),
            user::post(post_keys::PHONE),
            user::post(post_keys::ADDRESS_DETAIL),
            user::post(post_keys::BUILDING_NO),
            user::post(post_keys::APARTMENT_NO),
            user::post(post_keys::TITLE),
            user::post(post_keys::ADDRESS_TYPE),
            user::post(post_keys::CITY),
            user::post(post_keys::DISTRICT),
            user::post(post_keys::FLOOR),
            user::post(post_keys::NEIGHBORHOOD),
            user::post(post_keys::STREET),
            user::post(post_keys::TOWN)
        )){
            $echo->error_code = settings::error_codes()::EMPTY_VALUE;
        }

        if($echo->error_code == settings::error_codes()::SUCCESS){
            $data = $db->db_select(
                tbl::ID,
                tbl::TABLE_NAME,
                where: $db->where->equals([
                    tbl::PHONE => user::post(post_keys::PHONE)
                ]),
                limit: $db->limit([0, 1])
            )->rows;
            if(count($data) > 0) {
                user::post(post_keys::USER_ID, $data[0]["id"]);
            }
        }

        if($echo->error_code != settings::error_codes()::SUCCESS) $echo->status = false;

    }
}