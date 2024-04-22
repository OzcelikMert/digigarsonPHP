<?php
namespace manage\functions\dashboard\get;

use config\db;
use config\type_tables_values\account_types;
use config\type_tables_values\order_payment_status_types;
use config\sessions;
use sameparts\php\ajax\echo_values;
use config\table_helper\orders as tbl;
use config\table_helper\order_products as tbl2;
use config\table_helper\order_payments as tbl3;

class data_keys {
    const CUSTOMER = "customer",
        WAITER = "waiter",
        PRICE = "price",
        COST = "cost";
}

class top_charts {
    public function __construct(db $db, sessions $sessions, echo_values &$echo) {
        $echo->custom_data = $this->get($db, $sessions);
    }

    private function get(db $db, sessions $sessions) : array{
        return array(
            data_keys::CUSTOMER => $db->db_select(
                array(
                    tbl::SAFE_ID,
                    $db->as_name($db->count(tbl::ID), "`count`")
                ),
                tbl::TABLE_NAME,
                joins: $db->join->inner([
                    "(".$db->db_select(
                        array(
                            db::DISTINCT." ".tbl2::ORDER_ID,
                            tbl2::ACCOUNT_TYPE,
                        ),
                        tbl2::TABLE_NAME,
                        just_show_sql: true
                    )->sql.")".tbl2::TABLE_NAME => [tbl2::ORDER_ID => tbl::ID]
                ]),
                where: $db->where->equals([
                    tbl::BRANCH_ID      => $sessions->get->BRANCH_ID,
                    tbl2::ACCOUNT_TYPE  => account_types::CUSTOMER
                ]),
                group_by: tbl::SAFE_ID,
                order_by: $db->order_by(tbl::SAFE_ID, db::ASC),
                limit: $db->limit([0, 7])
            )->rows,

            data_keys::WAITER => $db->db_select(
                array(
                    tbl::SAFE_ID,
                    $db->as_name($db->count(tbl::ID), "`count`")
                ),
                tbl::TABLE_NAME,
                joins: $db->join->inner([
                "(".$db->db_select(
                    array(
                        db::DISTINCT." ".tbl2::ORDER_ID,
                        tbl2::ACCOUNT_TYPE,
                    ),
                    tbl2::TABLE_NAME,
                    just_show_sql: true
                )->sql.")".tbl2::TABLE_NAME => [tbl2::ORDER_ID => tbl::ID]
            ]),
                where: $db->where->equals([
                tbl::BRANCH_ID      => $sessions->get->BRANCH_ID,
                tbl2::ACCOUNT_TYPE  => account_types::WAITER
            ]),
                group_by: tbl::SAFE_ID,
                order_by: $db->order_by(tbl::SAFE_ID, db::ASC),
                limit: $db->limit([0, 7])
            )->rows,

            data_keys::PRICE => $db->db_select(
                array(
                    tbl3::SAFE_ID,
                    $db->as_name($db->sum(tbl3::PRICE), "total")
                ),
                tbl3::TABLE_NAME,
                where: $db->where->equals([
                    tbl3::BRANCH_ID => $sessions->get->BRANCH_ID,
                    tbl3::IS_DELETE => 0
                ]),
                group_by: tbl3::SAFE_ID,
                order_by: $db->order_by(tbl3::SAFE_ID, db::ASC),
                limit: $db->limit([0, 7])
            )->rows,

            data_keys::COST => $db->db_select(
                array(
                    tbl3::SAFE_ID,
                    $db->as_name($db->sum(tbl3::PRICE), "total")
                ),
                tbl3::TABLE_NAME,
                where: $db->where->equals([
                    tbl3::BRANCH_ID => $sessions->get->BRANCH_ID,
                    tbl3::STATUS    => order_payment_status_types::COST,
                    tbl3::IS_DELETE => 0
            ]),
                group_by: tbl3::SAFE_ID,
                order_by: $db->order_by(tbl3::SAFE_ID, db::ASC),
                limit: $db->limit([0, 7])
            )->rows,
        );
    }
}