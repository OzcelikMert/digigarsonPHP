<?php
namespace manage\functions\report_safe\get;

use config\db;
use config\sessions;
use sameparts\php\ajax\echo_values;
use config\table_helper\branch_trust_accounts as tbl;
use config\table_helper\branch_trust_account_payments as tbl2;
use config\table_helper\order_payments as tbl3;

class trust {
    public function __construct(db $db, db $db_backup, sessions $sessions, echo_values &$echo) {
        $echo->custom_data["trust_accounts"] = $this->get($db, $sessions);
        $echo->custom_data["old_data"] = $this->get_old_data($db_backup, $sessions);
    }

    private function get(db $db, sessions $sessions) : array{
        return $db->db_select(
            array(
                tbl::ID,
                tbl::NAME,
                tbl::DISCOUNT,
                tbl::IS_DELETE,
                tbl::PHONE,
                tbl::TAX_NO,
                tbl::TAX_ADMINISTRATION,
                tbl::ADDRESS,
                $db->as_name(
                    $db->if_null(
                        $db->sum(
                            $db->case->equals(
                                [tbl3::IS_DELETE => 1],
                                0,
                                $db->case->greater_than([tbl3::ORDER_ID => 0], "(".tbl3::PRICE." * -1)", tbl3::PRICE)
                            )
                        ),
                        0
                    ),
                    "total"
                )
            ),
            tbl::TABLE_NAME,
            $db->join->left([
                tbl2::TABLE_NAME => [tbl2::TRUST_ACCOUNT_ID => tbl::ID],
                tbl3::TABLE_NAME => [tbl3::ID => tbl2::PAYMENT_ID]
            ]),
            $db->where->equals([
                tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl::IS_DELETE => 0
            ]),
            group_by: tbl::ID
        )->rows;
    }

    private function get_old_data(db $db, sessions $sessions) : array{
        return $db->db_select(
            array(
                tbl2::TRUST_ACCOUNT_ID,
                $db->as_name($db->sum($db->case->greater_than([tbl3::ORDER_ID => 0], "(".tbl3::PRICE." * -1)", tbl3::PRICE)), "total")
            ),
            tbl2::TABLE_NAME,
            $db->join->inner([
                tbl3::TABLE_NAME => [tbl3::ID => tbl2::PAYMENT_ID]
            ]),
            $db->where->equals([
                tbl2::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl2::IS_DELETE => 0
            ]),
            group_by: tbl2::TRUST_ACCOUNT_ID
        )->rows;
    }
}