<?php
namespace pos\functions\orders\set;

use config\db;
use config\sessions;
use config\settings;
use config\table_helper\order_products as tbl;
use config\table_helper\order_product_options as tbl3;
use config\type_tables_values\account_types;
use config\type_tables_values\order_products_status_types;
use matrix_library\php\operations\array_list;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use sameparts\php\ajax\echo_values;
use sameparts\php\db_query\orders;
use sameparts\php\helper\date;

class post_keys {
    const ORDER_ID = "order_id",
        FUNCTION_TYPE = "function_type",
        PRODUCTS = "products";
}

class products_keys {
    const ID = "id",
        QTY_SEPARATE = "qty_separate",
        PIECE_SEPARATE = "piece_separate";
}

class separate_product {
    public function __construct(db $db, sessions $sessions, echo_values &$echo) {
        $this->check_values($db, $sessions, $echo);
        if($echo->status){
            $this->set($db, $sessions, $echo);
        }
    }


    /* Functions */
    private function set(db $db, sessions $sessions, echo_values &$echo){
        $echo->custom_data["product_select"] = array();
        $echo->custom_data["product_update"] = array();
        $echo->custom_data["product_insert"] = array();
        $echo->custom_data["option_select"] = array();
        $echo->custom_data["option_update"] = array();
        $echo->custom_data["option_insert"] = array();
        $echo->custom_data["products"] = array();

        $time = date::get(date::date_type_simples()::HOUR_MINUTE);
        $data_product_options = array();

        $id = array();
        foreach (user::post(post_keys::PRODUCTS) as $value){
            if(array_list::index_of($id, $value[products_keys::ID]) < 0) array_push($id, $value[products_keys::ID]);
        }

        $rows = orders::get_products(
                    $db,
                    $sessions->get->LANGUAGE_TAG,
                    $sessions->get->BRANCH_ID,
                    order_id: user::post(post_keys::ORDER_ID),
                    custom_where: $db->where->equals([
                        tbl::ID => $id,
                    ]),
                    limit: [0, count($id)]
                )->rows;

        $echo->custom_data["product_select"] = $rows;

        foreach ($rows as $row){
            $product = array_list::find(user::post(post_keys::PRODUCTS), $row["id"], "id");
            array_push($echo->custom_data["products"], $product);
            $rows_options = array();
            $isset_rows_options = false;

            if(
                ($row["qty"] - ($product[products_keys::PIECE_SEPARATE] * $product[products_keys::QTY_SEPARATE])) <= 0 ||
                $row["qty"] < $product[products_keys::QTY_SEPARATE] ||
                $row["qty"] < $product[products_keys::PIECE_SEPARATE]
            ) continue;

            for ($i = 0; $i < $product[products_keys::QTY_SEPARATE]; $i++){
                $insert_data = $db->db_insert(
                    tbl::TABLE_NAME,
                    [
                        tbl::BRANCH_ID     => $sessions->get->BRANCH_ID,
                        tbl::PRODUCT_ID    => $row["product_id"],
                        tbl::ORDER_ID      => $row["order_id"],
                        tbl::ACCOUNT_ID    => $sessions->get->USER_ID,
                        tbl::ACCOUNT_TYPE  => account_types::WAITER,
                        tbl::DISCOUNT      => $row["discount"],
                        tbl::PRICE         => ($row["price"] / $row["qty"]) * $product[products_keys::PIECE_SEPARATE],
                        tbl::VAT           => $row["vat"],
                        tbl::QUANTITY      => $row["quantity"],
                        tbl::QTY           => $product[products_keys::PIECE_SEPARATE],
                        tbl::PRINT         => 1,
                        tbl::TIME          => $time,
                        tbl::COMMENT       => $row["comment"]
                    ]
                );

                array_push($echo->custom_data["product_insert"], (array)$insert_data);
                $insert_id = $insert_data->insert_id;

                if(!$isset_rows_options) {
                    $rows_options = orders::get_product_options(
                        $db,
                        $sessions->get->BRANCH_ID,
                        order_product_id: $row["id"]
                    )->rows;
                    array_push($echo->custom_data["option_select"], $rows_options);
                }

                foreach ($rows_options as $row_option){
                    array_push(
                        $data_product_options,
                        array(
                            tbl3::BRANCH_ID => $sessions->get->BRANCH_ID,
                            tbl3::ORDER_PRODUCT_ID => $insert_id,
                            tbl3::OPTION_ID => $row_option["option_id"],
                            tbl3::OPTION_ITEM_ID => $row_option["option_item_id"],
                            tbl3::PRICE => ($row_option["price"] / $row_option["qty"]) * $product[products_keys::PIECE_SEPARATE],
                            tbl3::QTY => $product[products_keys::PIECE_SEPARATE]
                        )
                    );

                    if(!$isset_rows_options)
                        array_push($echo->custom_data["option_update"], (array)$db->db_update(
                            tbl3::TABLE_NAME,
                            array(
                                tbl3::QTY   => ($row_option["qty"] - ($product[products_keys::PIECE_SEPARATE] * $product[products_keys::QTY_SEPARATE])),
                                tbl3::PRICE => ($row_option["price"] - (($row_option["price"] / $row_option["qty"]) * ($product[products_keys::PIECE_SEPARATE] * $product[products_keys::QTY_SEPARATE])))
                            ),
                            where: $db->where->equals([
                                tbl3::ID => $row_option["id"],
                                tbl3::BRANCH_ID => $sessions->get->BRANCH_ID
                            ])
                        ));
                }
                $isset_rows_options = true;
            }

            array_push($echo->custom_data["product_update"], (array)$db->db_update(
                tbl::TABLE_NAME,
                [
                    tbl::QTY    => ($row["qty"] - ($product[products_keys::PIECE_SEPARATE] * $product[products_keys::QTY_SEPARATE])),
                    tbl::PRICE  => ($row["price"] - (($row["price"] / $row["qty"]) * ($product[products_keys::PIECE_SEPARATE] * $product[products_keys::QTY_SEPARATE])))
                ],
                where: $db->where->equals([
                    tbl::ID => $row["id"],
                    tbl::BRANCH_ID => $sessions->get->BRANCH_ID
                ])
            ));
        }

        $echo->custom_data["option_insert"] = (array)$db->db_insert(
            tbl3::TABLE_NAME,
            $data_product_options
        );
    }

    private function check_values(db $db, sessions $sessions, echo_values &$echo){
        if(variable::is_empty(
            user::post(post_keys::ORDER_ID),
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
                    custom_where: $db->where->equals([
                        tbl::ID => $id,
                        tbl::STATUS => order_products_status_types::ACTIVE
                    ]).db::AND.$db->where->greater_than([tbl::QTY => 1]),
                    limit: [0, count($id)]
                )->rows) != count($id)) $echo->error_code = settings::error_codes()::INCORRECT_DATA;
        }

        if($echo->error_code != settings::error_codes()::SUCCESS) $echo->status = false;
    }
}