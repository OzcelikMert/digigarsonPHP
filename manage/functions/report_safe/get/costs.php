<?php
namespace manage\functions\report_safe\get;

use config\db;
use config\type_tables_values\order_payment_status_types;
use config\sessions;
use sameparts\php\ajax\echo_values;
use config\table_helper\order_payments as tbl;

class costs {
    public function __construct(db $db, db $db_backup, sessions $sessions, echo_values &$echo) {
        $echo->rows = $this->get($db, $db_backup, $sessions);
    }

    private function get(db $db, db $db_backup, sessions $sessions) : array{
        return array(
            "new" => $db->db_select(
                array(
                    tbl::COMMENT,
                    tbl::ACCOUNT_ID,
                    tbl::ACCOUNT_TYPE,
                    tbl::DATE,
                    tbl::ID,
                    tbl::PRICE,
                    tbl::SAFE_ID,
                ),
                tbl::TABLE_NAME,
                where: $db->where->equals([
                    tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                    tbl::STATUS    => order_payment_status_types::COST,
                    tbl::IS_DELETE => 0
                ]),
                order_by: $db->order_by(tbl::DATE, db::DESC)
            )->rows,

            "old" => $db_backup->db_select(
                array(
                    tbl::COMMENT,
                    tbl::ACCOUNT_ID,
                    tbl::ACCOUNT_TYPE,
                    tbl::DATE,
                    tbl::ID,
                    tbl::PRICE,
                    tbl::SAFE_ID,
                ),
                tbl::TABLE_NAME,
                where: $db_backup->where->equals([
                    tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                    tbl::STATUS    => order_payment_status_types::COST,
                    tbl::IS_DELETE => 0
                ]),
                order_by: $db_backup->order_by(tbl::DATE, db::DESC)
            )->rows
        );
    }
}