<?php
namespace manage\functions\dashboard\get;

use config\db;
use config\sessions;
use sameparts\php\ajax\echo_values;
use config\table_helper\orders as tbl;
use config\table_helper\order_payments as tbl3;
use config\table_helper\account_types as tbl4;
use config\table_helper\customer_users as tbl5;
use config\table_helper\branch_users as tbl6;
use config\table_helper\payment_types as tbl7;
use config\table_helper\order_payment_status_types as tbl8;


class last_payments {
    public function __construct(db $db, sessions $sessions, echo_values &$echo) {
        $echo->custom_data = $this->get($db, $sessions);
    }

    private function get(db $db, sessions $sessions) : array{
        return $db->db_select(
            array(
                tbl3::ID,
                tbl3::ORDER_ID,
                tbl3::PRICE,
                tbl3::DATE,
                tbl::NO,
                $db->as_name($db->case->equals([tbl3::ACCOUNT_TYPE => 1], tbl5::NAME, tbl6::NAME), "account_name"),
                $db->as_name(tbl4::NAME.$sessions->get->LANGUAGE_TAG, "account_type"),
                $db->as_name(tbl7::NAME.$sessions->get->LANGUAGE_TAG, "type"),
                $db->as_name(tbl8::NAME.$sessions->get->LANGUAGE_TAG, "status")
            ),
            tbl3::TABLE_NAME,
            $db->join->left(array(
                tbl5::TABLE_NAME => [tbl3::ACCOUNT_TYPE => 1, tbl5::ID => tbl3::ACCOUNT_ID],
                tbl6::TABLE_NAME => [tbl3::ACCOUNT_TYPE => 2, tbl6::ID => tbl3::ACCOUNT_ID],
                tbl4::TABLE_NAME => [tbl4::ID => tbl3::ACCOUNT_TYPE],
                tbl::TABLE_NAME  => [tbl::ID => tbl3::ORDER_ID],
                tbl7::TABLE_NAME => [tbl7::ID => tbl3::TYPE],
                tbl8::TABLE_NAME => [tbl8::ID => tbl3::STATUS]
            )),
            where: $db->where->equals([
                tbl3::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl3::IS_DELETE  => 0
            ]),
            order_by: $db->order_by(tbl3::ID, db::DESC),
            limit: $db->limit([0, 7])
        )->rows;
    }
}