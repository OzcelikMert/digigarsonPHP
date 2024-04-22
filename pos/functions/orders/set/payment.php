<?php
namespace pos\functions\orders\set;

use config\db;
use config\sessions;
use config\settings;
use config\table_helper\orders as tbl;
use config\table_helper\order_payments as tbl2;
use config\table_helper\order_products as tbl3;
use config\table_helper\branch_trust_account_payments as tbl4;
use config\type_tables_values\account_types;
use config\type_tables_values\order_payment_status_types;
use config\type_tables_values\order_products_status_types;
use config\type_tables_values\order_status_types;
use config\type_tables_values\order_types;
use config\type_tables_values\payment_types;
use matrix_library\php\operations\array_list;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use sameparts\php\ajax\echo_values;
use sameparts\php\db_query\branch_trust_accounts;
use sameparts\php\db_query\orders;
use sameparts\php\helper\date;

class post_keys_payment {
    const ORDERS = "orders",
        PRODUCTS = "products_normal_payment",
        PAYMENT_TYPE = "payment_type",
        TABLE_ID = "table_id",
        TRUST_ACCOUNT_ID = "trust_account_id",
        ORDER_TYPE = "order_type",
        SET_TYPE = "set_type";
}

class set_types {
    const PAYMENT_TRUST_ACCOUNT = 0x0008;
}

class orders_keys {
    const ID = "id",
        PRICE = "price",
        COMMENT = "comment";
}

class products_keys_payment {
    const ID = "id";
}

class payment {
    public function __construct(db $db, sessions $sessions, echo_values &$echo) {
        if((int)user::post(post_keys_payment::ORDER_TYPE) == order_types::SAFE){
            (new insert($db, $sessions, $echo));
            user::post(post_keys_payment::ORDERS, array(
                array(
                    orders_keys::ID    => (int)user::post(post_keys_insert::ORDER_ID),
                    orders_keys::PRICE => user::post(post_keys_insert::TOTAL_PRICE)
                )
            ));
        }else{
            $this->check_values($db, $sessions, $echo);
        }

        if($echo->status){
            $echo->custom_data["set"] = (array)$this->set($db, $sessions, $echo);
        }
    }

    /* Functions */
    private function set(db $db, sessions $sessions, echo_values &$echo){
        $trust_account = array();
        $id = array();
        $time = date::get();
        $time_2 = $time;

        if(user::post(post_keys_payment::TRUST_ACCOUNT_ID) > 0){
            $trust_account = branch_trust_accounts::get(
                $db,
                $sessions->get->BRANCH_ID,
                user::post(post_keys_payment::TRUST_ACCOUNT_ID),
                limit: [0, 1]
            )->rows[0];
        }

        foreach (user::post(post_keys_payment::ORDERS) as $value){
            $old_order_id = $value[orders_keys::ID];
            if(user::post(post_keys_payment::PRODUCTS)){
                $rows = orders::get(
                    $db,
                    $sessions->get->BRANCH_ID,
                    $value[orders_keys::ID]
                )->rows;

                foreach ($rows as $row){
                    $value[orders_keys::ID] = $db->db_insert(
                        tbl::TABLE_NAME,
                        array(
                            tbl::BRANCH_ID  => $sessions->get->BRANCH_ID,
                            tbl::TABLE_ID   => $row["table_id"],
                            tbl::NO         => orders::get_order_last_no($db, $sessions->get->BRANCH_ID),
                            tbl::TYPE       => $row["type"],
                            tbl::STATUS     => $row["status"],
                            tbl::DISCOUNT   => $row["discount"],
                            tbl::DATE_START => $row["date_start"],
                            tbl::SAFE_ID    => $row["safe_id"]
                        )
                    )->insert_id;
                    $echo->custom_data["new_order_id"] = $value[orders_keys::ID];
                }

                $id_product = array();
                foreach (user::post(post_keys_payment::PRODUCTS) as $product){
                    if(array_list::index_of($id_product, $product[products_keys_payment::ID]) < 0) array_push($id_product, $product[products_keys_payment::ID]);
                }
                $db->db_update(
                    tbl3::TABLE_NAME,
                    array(
                        tbl3::ORDER_ID => $value[orders_keys::ID],
                        tbl3::STATUS  => (user::post(post_keys_payment::PAYMENT_TYPE) == payment_types::CANCEL) ? order_products_status_types::CANCEL : order_products_status_types::ACTIVE
                    ),
                    where: $db->where->equals([tbl3::ID => $id_product])
                );

                if(count(orders::get_products(
                        $db,
                        $sessions->get->LANGUAGE_TAG,
                        $sessions->get->BRANCH_ID,
                        order_id: $old_order_id
                    )->rows) < 1)
                    $db->db_delete(
                        tbl::TABLE_NAME,
                        where: $db->where->equals([
                            tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                            tbl::ID => $old_order_id
                        ])
                    );
            }else{
                $db->db_update(
                    tbl3::TABLE_NAME,
                    array(
                        tbl3::STATUS  => (user::post(post_keys_payment::PAYMENT_TYPE) == payment_types::CANCEL) ? order_products_status_types::CANCEL : order_products_status_types::ACTIVE
                    ),
                    where: $db->where->equals([
                        tbl3::ORDER_ID => $value[orders_keys::ID],
                        tbl3::STATUS => order_products_status_types::ACTIVE
                    ])
                );
            }

            if(user::post(post_keys_payment::SET_TYPE) != set_types::PAYMENT_TRUST_ACCOUNT) {
                $total_money = (float)$db->db_select(
                    $db->as_name($db->sum(tbl3::PRICE), "total"),
                    tbl3::TABLE_NAME,
                    where: $db->where->equals([
                        tbl3::BRANCH_ID => $sessions->get->BRANCH_ID,
                        tbl3::ORDER_ID => $value[orders_keys::ID],
                        tbl3::STATUS => order_products_status_types::ACTIVE
                    ])
                )->rows[0]["total"];
                $payed_money = (float)$db->db_select(
                    $db->as_name($db->if_null($db->sum(tbl2::PRICE), 0), "total"),
                    tbl2::TABLE_NAME,
                    where: $db->where->equals([
                        tbl2::BRANCH_ID => $sessions->get->BRANCH_ID,
                        tbl2::ORDER_ID => $value[orders_keys::ID]
                    ])
                )->rows[0]["total"];
                $total_money -= $payed_money;

                $value[orders_keys::PRICE] = (number_format($total_money, 2) < number_format($value[orders_keys::PRICE], 2)) ? $total_money : $value[orders_keys::PRICE];

                if(number_format($total_money, 2) != number_format($value[orders_keys::PRICE], 2)){
                    $time = "";
                }
            }

            array_push($id, $value[orders_keys::ID]);
            $value[orders_keys::PRICE] = (user::post(post_keys_payment::TRUST_ACCOUNT_ID) > 0 && user::post(post_keys_payment::SET_TYPE) != set_types::PAYMENT_TRUST_ACCOUNT)
                ?  $value[orders_keys::PRICE] - ($value[orders_keys::PRICE] * ($trust_account["discount"] / 100))
                : $value[orders_keys::PRICE];
            $insert_id = $db->db_insert(
                tbl2::TABLE_NAME,
                array(
                    tbl2::BRANCH_ID     => $sessions->get->BRANCH_ID,
                    tbl2::ORDER_ID      => $value[orders_keys::ID],
                    tbl2::ACCOUNT_ID    => $sessions->get->USER_ID,
                    tbl2::ACCOUNT_TYPE  => account_types::WAITER,
                    tbl2::TYPE          => user::post(post_keys_payment::PAYMENT_TYPE),
                    tbl2::STATUS        => (user::post(post_keys_payment::PAYMENT_TYPE) == payment_types::CANCEL) ? order_payment_status_types::CANCEL : order_payment_status_types::PAID,
                    tbl2::PRICE         => (user::post(post_keys_payment::PAYMENT_TYPE) == payment_types::CANCEL) ? 0 : $value[orders_keys::PRICE],
                    tbl2::DATE          => (empty($time)) ? $time_2 : $time
                )
            )->insert_id;

            if(user::post(post_keys_payment::TRUST_ACCOUNT_ID) > 0){
                $db->db_insert(
                    tbl4::TABLE_NAME,
                    array(
                        tbl4::BRANCH_ID     => $sessions->get->BRANCH_ID,
                        tbl4::PAYMENT_ID      => $insert_id,
                        tbl4::TRUST_ACCOUNT_ID => user::post(post_keys_payment::TRUST_ACCOUNT_ID),
                        tbl4::DISCOUNT => $trust_account["discount"],
                        tbl4::COMMENT  => (isset($value[orders_keys::COMMENT])) ? $value[orders_keys::COMMENT] : "-"
                    )
                );
            }
        }

        if(user::post(post_keys_payment::SET_TYPE) != set_types::PAYMENT_TRUST_ACCOUNT)
            return $db->db_update(
            tbl::TABLE_NAME,
            array(
                tbl::STATUS   => order_status_types::DELIVERED,
                tbl::DATE_END => $time
            ),
            where: $db->where->equals([
                tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl::ID => $id
            ])
        );
    }

    private function check_values(db $db, sessions $sessions, echo_values &$echo){
        if(variable::is_empty(
            user::post(post_keys_payment::ORDERS),
            user::post(post_keys_payment::ORDER_TYPE),
            user::post(post_keys_payment::PAYMENT_TYPE),
            user::post(post_keys_payment::TABLE_ID)
        )){
            $echo->error_code = settings::error_codes()::EMPTY_VALUE;
        }

        if($echo->error_code == settings::error_codes()::SUCCESS && user::post(post_keys_payment::SET_TYPE) != set_types::PAYMENT_TRUST_ACCOUNT){
            $id = array();
            foreach (user::post(post_keys_payment::ORDERS) as $value){
                if(array_list::index_of($id, $value[orders_keys::ID]) < 0) array_push($id, $value[orders_keys::ID]);
            }
            if(count(orders::get(
                    $db,
                    $sessions->get->BRANCH_ID,
                    table_id: user::post(post_keys_payment::TABLE_ID),
                    custom_where: $db->where->equals([tbl::ID => $id]),
                    limit: [0, count($id)]
                )->rows) != count($id)) $echo->error_code = settings::error_codes()::INCORRECT_DATA;
        }

        if($echo->error_code == settings::error_codes()::SUCCESS && user::post(post_keys_payment::PRODUCTS)){
            $id = array();
            foreach (user::post(post_keys_payment::PRODUCTS) as $value){
                if(array_list::index_of($id, $value[products_keys_payment::ID]) < 0) array_push($id, $value[products_keys_payment::ID]);
            }
            if(count(orders::get_products(
                    $db,
                    $sessions->get->LANGUAGE_TAG,
                    $sessions->get->BRANCH_ID,
                    custom_where: $db->where->equals([tbl3::ID => $id]),
                    limit: [0, count($id)]
                )->rows) != count($id)) $echo->error_code = settings::error_codes()::INCORRECT_DATA;
        }

        if($echo->error_code == settings::error_codes()::SUCCESS && user::post(post_keys_payment::TRUST_ACCOUNT_ID) > 0){
            if(count(branch_trust_accounts::get(
                    $db,
                    $sessions->get->BRANCH_ID,
                    user::post(post_keys_payment::TRUST_ACCOUNT_ID),
                    limit: [0, 1]
                )->rows) < 1) $echo->error_code = settings::error_codes()::INCORRECT_DATA;
        }

        if($echo->error_code != settings::error_codes()::SUCCESS) $echo->status = false;
    }
}