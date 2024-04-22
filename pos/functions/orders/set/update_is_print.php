<?php
namespace pos\functions\orders\set;

use config\db;
use config\sessions;
use config\table_helper\orders as tbl;
use matrix_library\php\operations\user;
use sameparts\php\ajax\echo_values;

class post_keys {
    const ID = "order_id";
}

class update_is_print {
    public function __construct(db $db, sessions $sessions, echo_values &$echo) {
        $echo->rows = $this->set($db,$sessions);
    }

    private function set(db $db, sessions $sessions) : array{
        return (array)$db->db_update(
           tbl::TABLE_NAME,
           array(tbl::IS_PRINT => 1),
           where: $db->where->equals([
               tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
               tbl::ID => user::post(post_keys::ID)
           ])
       );
    }




}