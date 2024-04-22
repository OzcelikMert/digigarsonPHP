<?php
namespace pos\functions\finance\get;

use config\db;
use config\sessions;
use config\type_tables_values\order_payment_status_types;
use sameparts\php\ajax\echo_values;
use config\table_helper\order_payments as tbl;
use sameparts\php\db_query\payments;

class costs {
    public function __construct(db $db, db $db_backup, sessions $sessions, echo_values &$echo) {
        $echo->rows = $this->get($db, $db_backup, $sessions);
    }

    private function get(db $db,  db $db_backup, sessions $sessions) : array{
        return payments::get(
                $db,
                $sessions->get->LANGUAGE_TAG,
                $sessions->get->BRANCH_ID,
                status_id: order_payment_status_types::COST,
                order_by: $db->order_by(tbl::DATE, db::DESC)
            )->rows;
    }
}