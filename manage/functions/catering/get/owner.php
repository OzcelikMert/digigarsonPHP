<?php
namespace manage\functions\catering\get;

use config\db;
use config\sessions;
use sameparts\php\ajax\echo_values;
use config\table_helper\catering_owners as tbl;
use sameparts\php\db_query\orders;

class owner {
    public function __construct(db $db, sessions $sessions, echo_values &$echo) {
        $echo->rows = $this->get($db, $sessions);
    }

    private function get(db $db, sessions $sessions) : array{
        return orders::get_catering_owners($db, $sessions->get->BRANCH_ID, order_by: $db->order_by(tbl::ID, $db::DESC))->rows;
    }
}