<?php
namespace language\functions\index\get;

use config\db;
use config\table_helper\translate as tbl;
use language\sameparts\functions\sessions\get;
use \sameparts\php\ajax\echo_values;

class languages {
    public function __construct(db $db, get $sessions, echo_values &$echo) {
        $echo->rows = $db->db_select(
            tbl::ALL,
            tbl::TABLE_NAME,
            order_by: $db->order_by(tbl::ID, $db::DESC)
        )->rows;
    }
}