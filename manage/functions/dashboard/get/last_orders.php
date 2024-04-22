<?php
namespace manage\functions\dashboard\get;

use config\db;
use config\sessions;
use sameparts\php\ajax\echo_values;
use config\table_helper\orders as tbl;
use config\table_helper\order_products as tbl2;
use config\table_helper\account_types as tbl3;
use config\table_helper\customer_users as tbl4;
use config\table_helper\branch_users as tbl5;
use config\table_helper\order_product_types as tbl6;
use config\table_helper\order_products_status_types as tbl7;
use config\table_helper\products as tbl8;
use config\table_helper\quantity_types as tbl9;

class last_orders {
    public function __construct(db $db, sessions $sessions, echo_values &$echo) {
        $echo->custom_data = $this->get($db, $sessions);
    }

    private function get(db $db, sessions $sessions) : array{
        return $db->db_select(
            array(
                tbl2::ID,
                tbl2::PRICE,
                tbl2::TIME,
                tbl2::QTY,
                tbl2::QUANTITY,
                tbl2::COMMENT,
                tbl::NO,
                tbl8::QUANTITY_ID,
                $db->as_name(tbl9::NAME.$sessions->get->LANGUAGE_TAG, "quantity_name"),
                $db->as_name(tbl8::NAME.$sessions->get->LANGUAGE_TAG, "name"),
                $db->as_name($db->case->equals([tbl2::ACCOUNT_TYPE => 1], tbl4::NAME, tbl5::NAME), "account_name"),
                $db->as_name(tbl3::NAME.$sessions->get->LANGUAGE_TAG, "account_type"),
                $db->as_name(tbl6::NAME.$sessions->get->LANGUAGE_TAG, "type"),
                $db->as_name(tbl7::NAME.$sessions->get->LANGUAGE_TAG, "status")
            ),
            tbl2::TABLE_NAME,
            $db->join->left(array(
                tbl4::TABLE_NAME => [tbl2::ACCOUNT_TYPE => 1, tbl4::ID => tbl2::ACCOUNT_ID],
                tbl5::TABLE_NAME => [tbl2::ACCOUNT_TYPE => 2, tbl5::ID => tbl2::ACCOUNT_ID],
                tbl3::TABLE_NAME => [tbl3::ID => tbl2::ACCOUNT_TYPE],
                tbl::TABLE_NAME  => [tbl::ID => tbl2::ORDER_ID],
                tbl6::TABLE_NAME => [tbl6::ID => tbl2::TYPE],
                tbl7::TABLE_NAME => [tbl7::ID => tbl2::STATUS],
                tbl8::TABLE_NAME => [tbl8::ID => tbl2::PRODUCT_ID],
                tbl9::TABLE_NAME => [tbl9::ID => tbl8::QUANTITY_ID]
            )),
            where: $db->where->equals([
                tbl2::BRANCH_ID => $sessions->get->BRANCH_ID
            ]),
            order_by: $db->order_by(tbl2::ID, db::DESC),
            limit: $db->limit([0, 7])
        )->rows;
    }
}