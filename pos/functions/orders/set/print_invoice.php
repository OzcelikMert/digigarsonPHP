<?php
namespace pos\functions\orders\set;

use config\db;
use config\sessions;
use config\table_helper\print_invoices as tbl;
use sameparts\php\ajax\echo_values;


class print_invoice {
    public function __construct(db $db, sessions $sessions, echo_values &$echo) {
        $echo->rows = $this->get($db,$sessions);
    }

    private function get(db $db, sessions $sessions) : array{
       $result = $db->db_select(
           tbl::DATA,
           tbl::TABLE_NAME,
           where: $db->where->equals(
               [
                   tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                   tbl::IS_PRINT => 0
               ]
           ))->rows;

       $db->db_update(tbl::TABLE_NAME,array(tbl::IS_PRINT => 1),
           where: $db->where->equals([tbl::BRANCH_ID => $sessions->get->BRANCH_ID, tbl::IS_PRINT => 0])
       );

       return $result;
    }

}