<?php
namespace pos\functions\orders\set;

use config\db;
use config\sessions;
use config\settings;
use config\table_helper\order_products as tbl;
use config\table_helper\products as tbl2;
use config\table_helper\order_product_options as tbl3;
use config\type_tables_values\account_types;
use matrix_library\php\operations\array_list;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use sameparts\php\ajax\echo_values;
use sameparts\php\db_query\orders;

class post_keys {
    const ORDER_ID = "order_id",
        FUNCTION_TYPE = "function_type",
        PRODUCTS = "products";
}

class products_keys {
    const ID = "id",
        PRICE = "price";
}

class FUNCTION_TYPES {
    const NORMAL = 0x0001,
        SAFE = 0x0002,
        TAKE_AWAY = 0x0003,
        COME_TAKE = 0x0004,
        PERSONAL = 0x0005,
        OTHER = 0x0006,
        CUSTOM = 0x0007;
}

class change_price {
    public function __construct(db $db, sessions $sessions, echo_values &$echo) {
        $this->check_values($db, $sessions, $echo);
        if($echo->status){
            $this->set($db, $sessions, $echo);
        }
    }


    /* Functions */
    private function set(db $db, sessions $sessions, echo_values &$echo){
        $column_price = "";
        $column_vat = "";

        switch (user::post(post_keys::FUNCTION_TYPE)){
            case FUNCTION_TYPES::NORMAL:
                $column_price = tbl2::PRICE;
                $column_vat = tbl2::VAT;
                break;
            case FUNCTION_TYPES::SAFE:
                $column_price = tbl2::PRICE_SAFE;
                $column_vat = tbl2::VAT_SAFE;
                break;
            case FUNCTION_TYPES::TAKE_AWAY:
                $column_price = tbl2::PRICE_TAKE_AWAY;
                $column_vat = tbl2::VAT_TAKE_AWAY;
                break;
            case FUNCTION_TYPES::COME_TAKE:
                $column_price = tbl2::PRICE_COME_TAKE;
                $column_vat = tbl2::VAT_COME_TAKE;
                break;
            case FUNCTION_TYPES::PERSONAL:
                $column_price = tbl2::PRICE_PERSONAL;
                $column_vat = tbl2::VAT_PERSONAL;
                break;
            case FUNCTION_TYPES::OTHER:
                $column_price = tbl2::PRICE_OTHER;
                $column_vat = tbl2::VAT_OTHER;
                break;
        }

        foreach (user::post(post_keys::PRODUCTS) as $value){
            array_push($echo->custom_data, $db->db_update(
                tbl::TABLE_NAME,
                [
                    tbl::PRICE => (empty($column_price)) ? $value[products_keys::PRICE] :  $db->if_null("option_price", 0)." + ({$column_price} * ".tbl::QTY.")",
                    tbl::VAT => (empty($column_vat)) ? tbl::VAT : $column_vat,
                    tbl::ACCOUNT_ID => $sessions->get->USER_ID,
                    tbl::ACCOUNT_TYPE => account_types::WAITER,
                    tbl::PRICE_CHANGED => 1
                ],
                $db->join->inner(
                    [
                        tbl2::TABLE_NAME => [tbl2::ID => tbl::PRODUCT_ID],
                    ]
                ).$db->join->left(
                    [
                        "(".$db->db_select(
                            [
                                $db->as_name($db->sum(tbl3::PRICE), "option_price"),
                                tbl3::ORDER_PRODUCT_ID
                            ],
                            tbl3::TABLE_NAME,
                            where: $db->where->equals(
                                [
                                    tbl3::BRANCH_ID => $sessions->get->BRANCH_ID,
                                    tbl3::ORDER_PRODUCT_ID => (int)$value[products_keys::ID]
                                ]),
                            just_show_sql: true
                        )->sql.")".tbl3::TABLE_NAME => [tbl3::ORDER_PRODUCT_ID => tbl::ID]
                    ]
                ),
                $db->where->equals(
                    [
                        tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                        tbl::ID => (int)$value[products_keys::ID]
                    ]
                ),
                checked_varchar: false
            ));
        }
    }

    private function check_values(db $db, sessions $sessions, echo_values &$echo){
        if(variable::is_empty(
            user::post(post_keys::ORDER_ID),
            user::post(post_keys::FUNCTION_TYPE),
            user::post(post_keys::PRODUCTS)
        )){
            $echo->error_code = settings::error_codes()::EMPTY_VALUE;
        }

        if($echo->error_code == settings::error_codes()::SUCCESS){
            $id = array();
            foreach (user::post(post_keys::PRODUCTS) as $value){
                if(array_list::index_of($id, $value[products_keys::ID]) < 0) array_push($id, $value[products_keys::ID]);
            }
            if(count(orders::get_products(
                    $db,
                    $sessions->get->LANGUAGE_TAG,
                    $sessions->get->BRANCH_ID,
                    order_id: user::post(post_keys::ORDER_ID),
                    custom_where: $db->where->equals([tbl::ID => $id]),
                    limit: [0, count($id)]
                )->rows) != count($id)) $echo->error_code = settings::error_codes()::INCORRECT_DATA;
        }

        if($echo->error_code != settings::error_codes()::SUCCESS) $echo->status = false;
    }
}