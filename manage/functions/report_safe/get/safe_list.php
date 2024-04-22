<?php
namespace manage\functions\report_safe\get;

use config\db;
use config\sessions;
use sameparts\php\ajax\echo_values;
use config\table_helper\branch_safe as tbl;
use config\table_helper\branch_users as tbl2;

class safe_list {
    public function __construct(db $db, sessions $sessions, echo_values &$echo) {
        $echo->custom_data = $this->get($db, $sessions);
    }

    private function get(db $db, sessions $sessions) : array{
        return $db->db_select(
            array(
                tbl::ID,
                tbl::DATE_START,
                tbl::DATE_END,
                tbl::COMMENT,
                tbl2::NAME
            ),
            tbl::TABLE_NAME,
            $db->join->inner([
                tbl2::TABLE_NAME => [tbl2::ID => tbl::ACCOUNT_ID]
            ]),
            $db->where->equals([
                tbl::BRANCH_ID => $sessions->get->BRANCH_ID
            ]),
            order_by: $db->order_by(tbl::ID, db::DESC)
        )->rows;
    }
}