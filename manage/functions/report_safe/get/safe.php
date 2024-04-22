<?php
namespace manage\functions\report_safe\get;

use config\db;
use config\sessions;
use matrix_library\php\operations\user;
use sameparts\php\ajax\echo_values;
use config\table_helper\orders as tbl;
use config\table_helper\order_products as tbl2;
use config\table_helper\order_payments as tbl3;
use config\table_helper\order_product_options as tbl4;

class post_keys {
    const SAFE_ID = "safe_id";
}

class data_keys {
    const PAYMENTS = "payments",
        ORDERS = "orders",
        ORDER_PRODUCTS = "order_products",
        ORDER_PRODUCT_OPTIONS = "order_product_options";
}

class safe {
    public function __construct(db $db, db $db_backup, sessions $sessions, echo_values &$echo) {
        $echo->rows = $this->get(
            ((user::post(post_keys::SAFE_ID) == 0)
                ? $db
                : $db_backup),
            $sessions);
    }

    private function get(db $db, sessions $sessions) : array{
        return array(
            data_keys::PAYMENTS => $db->db_select(
                array(
                    tbl3::ID,
                    tbl3::PRICE,
                    tbl3::STATUS,
                    tbl3::TYPE,
                    tbl3::ORDER_ID,
                    tbl3::DATE
                ),
                tbl3::TABLE_NAME,
                where: $db->where->equals([
                    tbl3::BRANCH_ID => $sessions->get->BRANCH_ID,
                    tbl3::SAFE_ID   => user::post(post_keys::SAFE_ID),
                    tbl3::IS_DELETE => 0
                ]),
                order_by: $db->order_by(tbl3::TYPE, db::ASC)
            )->rows,

            data_keys::ORDERS => $db->db_select(
                tbl::ALL,
                tbl::TABLE_NAME,
                where: $db->where->equals([
                    tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                    tbl::SAFE_ID   => user::post(post_keys::SAFE_ID)
                ]),
                order_by: $db->order_by(tbl::ID, db::ASC)
            )->rows,

            data_keys::ORDER_PRODUCTS => $db->db_select(
                tbl2::ALL,
                tbl2::TABLE_NAME,
                $db->join->inner([
                    tbl::TABLE_NAME => [tbl::ID => tbl2::ORDER_ID]
                ]),
                $db->where->equals([
                    tbl2::BRANCH_ID => $sessions->get->BRANCH_ID,
                    tbl::SAFE_ID    => user::post(post_keys::SAFE_ID)
                ]),
                order_by: $db->order_by(tbl2::ID, db::ASC)
            )->rows,

            data_keys::ORDER_PRODUCT_OPTIONS => $db->db_select(
                tbl4::ALL,
                tbl4::TABLE_NAME,
                joins: $db->join->inner([
                    tbl2::TABLE_NAME => [tbl2::ID => tbl4::ORDER_PRODUCT_ID],
                    tbl::TABLE_NAME => [tbl::ID => tbl2::ORDER_ID]
                ]),
                where: $db->where->equals([
                    tbl4::BRANCH_ID => $sessions->get->BRANCH_ID,
                    tbl::SAFE_ID   => user::post(post_keys::SAFE_ID)
                ])
            )->rows

        );
    }
}