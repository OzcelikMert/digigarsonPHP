<?php
namespace manage\functions\report_safe\set;

use config\db;
use config\settings;
use config\table_helper\order_payments as tbl;
use config\table_helper\order_products as tbl2;
use config\type_tables_values\order_payment_status_types;
use config\type_tables_values\order_products_status_types;
use config\type_tables_values\payment_types;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use config\sessions;
use sameparts\php\ajax\echo_values;

class post_keys {
    const PAYMENTS = "payments",
        ORDER_ID = "order_id",
        SAFE_ID = "safe_id";
}

class payments_keys {
    const ID = "id",
        OLD_TYPE = "old_type",
        NEW_TYPE = "new_type",
        PRICE = "price";
}

class invoice_edit {
    public function __construct(db $db, db $db_backup, sessions $sessions, echo_values &$echo) {
        $db_ = (user::post(post_keys::SAFE_ID) > 0) ? $db_backup : $db;
        $this->check_values($db_, $sessions, $echo);
        if($echo->status){
            $this->set($db_, $sessions);
        }
    }

    private function set(db $db, sessions $sessions){
        foreach (user::post(post_keys::PAYMENTS) as $payment){
            $price = 0;
            if($payment[payments_keys::NEW_TYPE] == payment_types::CANCEL) {
                $db->db_update(
                    tbl2::TABLE_NAME,
                    array(
                        tbl2::STATUS => order_products_status_types::CANCEL
                    ),
                    $db->join->inner([
                        tbl::TABLE_NAME => [tbl::ORDER_ID => tbl2::ORDER_ID]
                    ]),
                    $db->where->equals([
                        tbl::ID => $payment[payments_keys::ID],
                        tbl::SAFE_ID => user::post(post_keys::SAFE_ID),
                        tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                        tbl2::ORDER_ID => user::post(post_keys::ORDER_ID)
                    ])
                );

                $db->db_update(
                    tbl::TABLE_NAME,
                    array(
                        tbl::IS_DELETE => 1
                    ),
                    where: $db->where->equals([
                        tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                        tbl::SAFE_ID => user::post(post_keys::SAFE_ID),
                        tbl::ORDER_ID => user::post(post_keys::ORDER_ID)
                    ])." AND ".$db->where->not_like([
                        tbl::ID => $payment[payments_keys::ID]
                    ])
                );
            }else{
                $price = $payment[payments_keys::PRICE];
            }

            $db->db_update(
                tbl::TABLE_NAME,
                array(
                    tbl::TYPE => $payment[payments_keys::NEW_TYPE],
                    tbl::STATUS => ($payment[payments_keys::NEW_TYPE] == payment_types::CANCEL) ? order_payment_status_types::CANCEL : order_payment_status_types::PAID,
                    tbl::PRICE => $price
                ),
                where: $db->where->equals([
                    tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                    tbl::ID => $payment[payments_keys::ID],
                    tbl::SAFE_ID => user::post(post_keys::SAFE_ID),
                    tbl::ORDER_ID => user::post(post_keys::ORDER_ID)
                ])
            );
        }
    }

    private function check_values(db $db, sessions $sessions, echo_values &$echo){
        if(variable::is_empty(
            user::post(post_keys::PAYMENTS),
            user::post(post_keys::SAFE_ID),
            user::post(post_keys::ORDER_ID)
        )){
            $echo->error_code = settings::error_codes()::EMPTY_VALUE;
        }

        if($echo->error_code === settings::error_codes()::SUCCESS){
            user::post(post_keys::PAYMENTS, array_filter(user::post(post_keys::PAYMENTS)));
            $total = 0.0;
            $id = array();
            $is_cancel = false;
            foreach (user::post(post_keys::PAYMENTS) as $payment){
                if($payment[payments_keys::NEW_TYPE] == payment_types::CANCEL) {
                    $is_cancel = true;
                }else{
                    $total += (float)$payment[payments_keys::PRICE];
                    array_push($id, $payment[payments_keys::ID]);
                }
            }

            if(!$is_cancel){
                $result = $db->db_select(
                    $db->as_name($db->sum(tbl::PRICE), "total"),
                    tbl::TABLE_NAME,
                    where: $db->where->equals([
                    tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                    tbl::ID => $id,
                    tbl::SAFE_ID => user::post(post_keys::SAFE_ID),
                    tbl::ORDER_ID => user::post(post_keys::ORDER_ID)
                ])
                )->rows;

                if(count($result) < 1 || number_format((float)$result[0]["total"], 2) != number_format($total, 2)) $echo->error_code = settings::error_codes()::WRONG_VALUE;
            }
        }

        if($echo->error_code != settings::error_codes()::SUCCESS) $echo->status = false;
    }
}