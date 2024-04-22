<?php
namespace manage\sameparts\functions\navbar\get;

use config\db;
use config\sessions;
use sameparts\php\ajax\echo_values;
use config\table_helper\branch_info as tbl;

class companies {
    public function __construct(db $db, sessions $sessions, echo_values &$echo) {
        $echo->rows = $this->get($db, $sessions);
    }

    private function get(db $db, sessions $sessions) : array{
        $where = ($sessions->get->BRANCH_ID_MAIN == 3 && $sessions->get->PERMISSION == "*")
            ? ""
            : $db->where->equals([
                tbl::MAIN_ID => $sessions->get->BRANCH_ID_MAIN
            ]);
        return $db->db_select(
                array(
                    tbl::ID,
                    tbl::NAME,
                    tbl::ACTIVE
                ),
                tbl::TABLE_NAME,
                where: $where,
                order_by: $db->order_by(tbl::NAME, db::ASC)
            )->rows;
    }
}