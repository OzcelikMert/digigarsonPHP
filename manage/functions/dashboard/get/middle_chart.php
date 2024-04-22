<?php
namespace manage\functions\dashboard\get;

use config\db;
use config\sessions;
use sameparts\php\ajax\echo_values;
use config\table_helper\order_payments as tbl;
use sameparts\php\helper\date;

class data_keys {
    const PREVIOUS_WEEK = "prev_week",
        WEEK = "week";
}

class middle_chart {
    public function __construct(db $db, sessions $sessions, echo_values &$echo) {
        $echo->custom_data = $this->get($db, $sessions);
    }

    private function get(db $db, sessions $sessions) : array{
        return array(
            data_keys::WEEK => $db->db_select(
                array(
                    tbl::SAFE_ID,
                    $db->as_name($db->sum(tbl::PRICE), "total")
                ),
                tbl::TABLE_NAME,
                where: $db->where->equals([
                    tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                    tbl::IS_DELETE => 0
                ])." ".db::AND." ".$db->where->between([tbl::DATE => [date::get(timestamp: strtotime("-1 week")), date::get()]]),
                group_by: tbl::SAFE_ID,
                order_by: $db->order_by(tbl::SAFE_ID, db::ASC),
                limit: $db->limit([0, 7])
            )->rows,

            data_keys::PREVIOUS_WEEK => $db->db_select(
                array(
                    tbl::SAFE_ID,
                    $db->as_name($db->sum(tbl::PRICE), "total")
                ),
                tbl::TABLE_NAME,
                where: $db->where->equals([
                    tbl::BRANCH_ID => $sessions->get->BRANCH_ID
                ])." ".db::AND." ".$db->where->between([tbl::DATE => [date::get(timestamp: strtotime("-2 week")), date::get(timestamp: strtotime("-1 week"))]]),
                group_by: tbl::SAFE_ID,
                order_by: $db->order_by(tbl::SAFE_ID, db::ASC),
                limit: $db->limit([0, 7])
            )->rows,
        );
    }
}