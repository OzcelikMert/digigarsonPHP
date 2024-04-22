<?php
namespace pos\functions\orders\set;

use config\db;
use config\sessions;
use config\settings;
use config\table_helper\order_products as tbl;
use config\table_helper\orders as tbl2;
use config\table_helper\order_payments as tbl3;
use config\table_helper\order_product_options as tbl7;
use config\table_helper\print_invoices as tbl10;
use config\table_helper\caterings as tbl6;
use config\type_tables_values\account_types;
use config\type_tables_values\order_payment_status_types;
use config\type_tables_values\order_products_status_types;
use config\type_tables_values\order_status_types;
use matrix_library\php\operations\array_list;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use sameparts\php\ajax\echo_values;
use sameparts\php\db_query\orders;
use sameparts\php\db_query\payments;
use sameparts\php\helper\date;

class post_keys {
    const COMMENT = "comment",
        PRODUCTS = "products",
        ORDERS = "orders",
        ORDER_ID = "order_id",
        OWNER = "owner",
        QUESTION = "question",
        FUNCTION_TYPE = "function_type";
}

class products_keys {
    const ID = "id",
        QTY = "qty",
        OLD_QTY = "old_qty",
        PRICE = "price";
}

class FUNCTION_TYPES {
    const DELETE = 0x0001,
        CATERING = 0x0002;
}

class print_types {
    const SAFE = 1,
        KITCHEN = 2,
        CANCEL = 3;
}

class cancel_and_catering {
    private array $cancel_invoice = array();

    public function __construct(db $db, sessions $sessions, echo_values &$echo) {
        $this->check_values($db, $sessions, $echo);
        $this->cancel_invoice["products"] = array();

        if($echo->status){
            $this->set($db, $sessions, $echo);
            $this->create_invoice( $db,$sessions,$echo);
        }
    }

    /* Functions */
    private function set(db $db, sessions $sessions, echo_values &$echo){
        $object = array();
        $date = date::get();
        $id = array();
        foreach (user::post(post_keys::PRODUCTS) as $value){
            if(array_list::index_of($id, $value[products_keys::ID]) < 0) array_push($id, $value[products_keys::ID]);
        }

        $rows = orders::get_products(
                $db,
                $sessions->get->LANGUAGE_TAG,
                $sessions->get->BRANCH_ID,
                order_id: user::post(post_keys::ORDER_ID),
                custom_where: $db->where->equals([tbl::ID => $id]),
                limit: [0, count($id)]
        );


        foreach ($rows->rows as $row){
            $product = array_list::find(user::post(post_keys::PRODUCTS), $row["id"], "id");

            if (user::post(post_keys::FUNCTION_TYPE) ==  FUNCTION_TYPES::DELETE){ //CANCEL INVOICE
                $product["options"] = json_decode($product["options"],true);

                array_push($this->cancel_invoice["products"],array(
                    "id"         => (int)$product[products_keys::ID],
                    "branch_id"  => (int)$sessions->get->BRANCH_ID,
                    "product_id" => (int)$row["product_id"],
                    "order_id"   => (int)user::post(post_keys::ORDER_ID),
                    "user_id"    => (int)$sessions->get->USER_ID,
                    "price"      => (int)$product[products_keys::PRICE],
                    "quantity"   => (int)$row["quantity"],
                    "qty"        => (int)$product[products_keys::QTY],
                    "time"       => $row["time"],
                    "options"    => $product["options"]
                ));
            }


            $insert_id = 0;
            if($row["qty"] > $product[products_keys::QTY]){  //Kendi Miktarından Daha Az ise
                $object = match (user::post(post_keys::FUNCTION_TYPE)) {
                    FUNCTION_TYPES::DELETE => array(
                        tbl::COMMENT       => user::post(post_keys::COMMENT),
                        tbl::STATUS        => order_products_status_types::CANCEL
                    ),
                    FUNCTION_TYPES::CATERING => array(
                        tbl::STATUS        => order_products_status_types::CATERING
                    ),
                };

                $insert_id = $db->db_insert(
                    tbl::TABLE_NAME,
                    array_merge(
                        array(
                            tbl::BRANCH_ID     => $sessions->get->BRANCH_ID,
                            tbl::PRODUCT_ID    => $row["product_id"],
                            tbl::ORDER_ID      => user::post(post_keys::ORDER_ID),
                            tbl::ACCOUNT_ID    => $sessions->get->USER_ID,
                            tbl::ACCOUNT_TYPE  => account_types::WAITER,
                            tbl::DISCOUNT      => $row["discount"],
                            tbl::PRICE         => $product[products_keys::PRICE],
                            tbl::QUANTITY      => $row["quantity"],
                            tbl::QTY           => $product[products_keys::QTY],
                            tbl::PRINT         => 0,
                            tbl::TIME          => $row["time"],
                        ),
                        $object
                    )
                )->insert_id;

                $order_product_options = orders::get_product_options($db, $sessions->get->BRANCH_ID, order_product_id: $product[products_keys::ID])->rows;
                $insert_data = array();
                foreach ($order_product_options as $order_product_option){
                    array_push($insert_data, array(
                        tbl7::BRANCH_ID         => $order_product_option["branch_id"],
                        tbl7::PRICE             => ($order_product_option["price"] / $order_product_option["qty"]) * $product[products_keys::QTY],
                        tbl7::QTY               => $product[products_keys::QTY],
                        tbl7::ORDER_PRODUCT_ID  => $insert_id,
                        tbl7::OPTION_ID         => $order_product_option["option_id"],
                        tbl7::OPTION_ITEM_ID    => $order_product_option["option_item_id"]
                    ));
                }
                if(count($insert_data) > 0){
                    array_push($echo->rows, $db->db_insert(
                        tbl7::TABLE_NAME,
                        $insert_data
                    ));
                    $insert_data = array();
                }
            }

            if(user::post(post_keys::FUNCTION_TYPE) == FUNCTION_TYPES::CATERING){
                $db->db_insert(
                    tbl6::TABLE_NAME,
                    array(
                        tbl6::BRANCH_ID     => $sessions->get->BRANCH_ID,
                        tbl6::PRODUCT_ID    => ($insert_id > 0) ? $insert_id : $row["id"],
                        tbl6::OWNER_ID      => user::post(post_keys::OWNER),
                        tbl6::QUESTION_ID   => user::post(post_keys::QUESTION),
                        tbl6::DATE          => $date
                    )
                );
            }

            $object = match (user::post(post_keys::FUNCTION_TYPE)) {
                FUNCTION_TYPES::DELETE => array(
                    tbl::COMMENT => $db->case->equals([$product[products_keys::QTY] => tbl::QTY], $db::convert_varchar(user::post(post_keys::COMMENT)), $db::convert_varchar($row["comment"])),
                    tbl::STATUS => $db->case->equals([$product[products_keys::QTY] => tbl::QTY], order_products_status_types::CANCEL, $row["status"])
                ),
                FUNCTION_TYPES::CATERING => array(
                    tbl::STATUS => $db->case->equals([$product[products_keys::QTY] => tbl::QTY], order_products_status_types::CATERING, $row["status"])
                )
            };

            $db->db_update(
                tbl::TABLE_NAME,
                array_merge(
                    array(
                        tbl::ACCOUNT_ID   => $sessions->get->USER_ID,
                        tbl::ACCOUNT_TYPE => account_types::WAITER,
                        tbl::QTY          => $db->case->equals([$product[products_keys::QTY] => tbl::QTY], $row["qty"], "(".$row["qty"]." - ".$product[products_keys::QTY].")"),
                        tbl::PRICE        => $db->case->equals([$product[products_keys::QTY] => tbl::QTY], $row["price"], "(".$row["price"]." - ".$product[products_keys::PRICE].")"),
                    ),
                    $object
                ),
                where: $db->where->equals([
                    tbl::BRANCH_ID  => $sessions->get->BRANCH_ID,
                    tbl::ORDER_ID   => user::post(post_keys::ORDER_ID),
                    tbl::ID         => $product[products_keys::ID]
                ])
            );

            array_push($echo->rows, $db->db_update(
                tbl7::TABLE_NAME,
                array(
                    tbl7::PRICE => $db->case->equals([$product[products_keys::QTY] => tbl7::QTY], tbl7::PRICE, "(".tbl7::PRICE." - ((".tbl7::PRICE." / ".tbl7::QTY.") * ".$product[products_keys::QTY]."))"),
                    tbl7::QTY   => $db->case->equals([$product[products_keys::QTY] => tbl7::QTY], tbl7::QTY, "(".tbl7::QTY." - ".$product[products_keys::QTY].")")
                ),
                where: $db->where->equals([
                tbl7::BRANCH_ID  => $sessions->get->BRANCH_ID,
                tbl7::ORDER_PRODUCT_ID => $product[products_keys::ID]
            ])
            ));
        }

        if(count(orders::get_products(
                $db,
                $sessions->get->LANGUAGE_TAG,
                $sessions->get->BRANCH_ID,
                order_id: user::post(post_keys::ORDER_ID),
                custom_where: $db->where->equals([tbl::STATUS => order_products_status_types::ACTIVE]),
                limit: [0, 1]
            )->rows) < 1) {
            $db->db_update(
                tbl2::TABLE_NAME,
                array(
                    tbl2::STATUS   => order_status_types::DELIVERED,
                    tbl2::DATE_END => $date
                ),
                where: $db->where->equals(
                    [
                        tbl2::BRANCH_ID => $sessions->get->BRANCH_ID,
                        tbl2::ID        => user::post(post_keys::ORDER_ID)
                    ]
                )
            );
        }

        $object = match (user::post(post_keys::FUNCTION_TYPE)) {
            FUNCTION_TYPES::DELETE => array(
                tbl3::STATUS => order_payment_status_types::CANCEL,
            ),
            FUNCTION_TYPES::CATERING => array(
                tbl3::STATUS => order_payment_status_types::CATERING,
            ),
        };

        if(count(payments::get(
                $db,
                $sessions->get->LANGUAGE_TAG,
                $sessions->get->BRANCH_ID,
                order_id: user::post(post_keys::ORDER_ID),
                custom_where: $db->where->equals([
                    tbl::STATUS => order_products_status_types::ACTIVE,
                    $object
                ]),
                limit: [0, 1]
            )->rows) < 1) {
            $db->db_insert(
                tbl3::TABLE_NAME,
                array_merge(
                    array(
                        tbl3::BRANCH_ID => $sessions->get->BRANCH_ID,
                        tbl3::ORDER_ID => user::post(post_keys::ORDER_ID),
                        tbl3::ACCOUNT_ID => $sessions->get->USER_ID,
                        tbl3::ACCOUNT_TYPE => account_types::WAITER,
                        tbl3::TYPE => 9,
                        tbl3::PRICE => 0,
                        tbl3::DATE => $date
                    ),
                    $object
                )
            );
        }
    }

    private function check_values(db $db, sessions $sessions, echo_values &$echo){
        if(variable::is_empty(
            user::post(post_keys::FUNCTION_TYPE),
            user::post(post_keys::PRODUCTS),
            user::post(post_keys::ORDER_ID)
        )){
            $echo->error_code = settings::error_codes()::EMPTY_VALUE;
        }

        if($echo->error_code == settings::error_codes()::SUCCESS){
            user::post(post_keys::FUNCTION_TYPE, (int)user::post(post_keys::FUNCTION_TYPE));
            switch (user::post(post_keys::FUNCTION_TYPE)){
                case FUNCTION_TYPES::DELETE:
                    if(variable::is_empty(
                        user::post(post_keys::COMMENT)
                    )){
                        $echo->error_code = settings::error_codes()::EMPTY_VALUE;
                    }
                    break;
                case FUNCTION_TYPES::CATERING:
                    if(variable::is_empty(
                        user::post(post_keys::OWNER),
                        user::post(post_keys::QUESTION)
                    )){
                        $echo->error_code = settings::error_codes()::EMPTY_VALUE;
                    }
                    break;
            }
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
                    ]),
                    limit: [0, count($id)]
                )->rows) != count($id)) $echo->error_code = settings::error_codes()::INCORRECT_DATA;
        }

        if($echo->error_code == settings::error_codes()::SUCCESS){
            if(user::post(post_keys::FUNCTION_TYPE) == FUNCTION_TYPES::CATERING) {
                if (count(orders::get_catering_owners(
                        $db,
                        $sessions->get->BRANCH_ID,
                        user::post(post_keys::OWNER),
                        limit: [0, 1]
                    )->rows) < 1) $echo->error_code = settings::error_codes()::INCORRECT_DATA;

                if (count(orders::get_catering_questions(
                        $db,
                        $sessions->get->BRANCH_ID,
                        user::post(post_keys::QUESTION),
                        limit: [0, 1]
                    )->rows) < 1) $echo->error_code = settings::error_codes()::INCORRECT_DATA;
            }
        }

        if($echo->error_code != settings::error_codes()::SUCCESS) $echo->status = false;
    }

    private function create_invoice(db $db, sessions $sessions, echo_values &$echo){
        if ( user::post(post_keys::FUNCTION_TYPE) ==  FUNCTION_TYPES::DELETE){
            $orders = user::post(post_keys::ORDERS)[0];
            $this->cancel_invoice["type"] = print_types::CANCEL;
            $this->cancel_invoice["order_id"] = user::post(post_keys::ORDER_ID);
            $this->cancel_invoice["user_name"] = $sessions->get->USER_NAME;
            $this->cancel_invoice["comment"] = user::post(post_keys::COMMENT);
            //orders[0].table_id
            $this->cancel_invoice["orders"] = [
                ["table_id" => (int)$orders["table_id"], "no" => (int)$orders["no"]]
            ];
            $db->db_insert(tbl10::TABLE_NAME,array(
                tbl10::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl10::DATA => json_encode($this->cancel_invoice,JSON_UNESCAPED_UNICODE)
            ));
        }





    }
}