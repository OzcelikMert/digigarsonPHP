<?php
namespace manage\functions\report_safe\get;

use config\db;
use config\sessions;
use matrix_library\php\operations\user;
use sameparts\php\ajax\echo_values;
use config\table_helper\branch_trust_account_payments as tbl2;
use config\table_helper\order_payments as tbl3;

class post_keys {
    const ACCOUNT_ID = "account_id";
}

class trust_payments {
    public function __construct(db $db, db $db_backup, sessions $sessions, echo_values &$echo) {
        $echo->custom_data["payments"] = $this->get($db, $sessions);
        $echo->custom_data["old_data"] = $this->get_old_data($db_backup, $sessions);
    }

    private function get(db $db, sessions $sessions) : array{
        return $db->db_select(
            array(
                tbl2::ID,
                tbl2::DISCOUNT,
                tbl2::COMMENT,
                tbl3::TYPE,
                tbl3::PRICE,
                tbl3::DATE,
                $db->as_name(tbl3::ID, "payment_id"),
                tbl3::SAFE_ID,
                tbl3::ORDER_ID
            ),
            tbl2::TABLE_NAME,
            $db->join->inner([
                tbl3::TABLE_NAME => [tbl3::ID => tbl2::PAYMENT_ID]
            ]),
            $db->where->equals([
                tbl2::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl2::TRUST_ACCOUNT_ID => user::post(post_keys::ACCOUNT_ID),
                tbl2::IS_DELETE => 0
            ])
        )->rows;
    }

    private function get_old_data(db $db, sessions $sessions) : array{
        return $db->db_select(
            array(
                tbl2::ID,
                tbl2::DISCOUNT,
                tbl2::COMMENT,
                tbl3::TYPE,
                tbl3::PRICE,
                tbl3::DATE,
                $db->as_name(tbl3::ID, "payment_id"),
                tbl3::SAFE_ID,
                tbl3::ORDER_ID
            ),
            tbl2::TABLE_NAME,
            $db->join->inner([
                tbl3::TABLE_NAME => [tbl3::ID => tbl2::PAYMENT_ID]
            ]),
            $db->where->equals([
                tbl2::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl2::TRUST_ACCOUNT_ID => user::post(post_keys::ACCOUNT_ID),
                tbl2::IS_DELETE => 0
            ])
        )->rows;
    }
}