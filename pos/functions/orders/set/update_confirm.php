<?php
namespace pos\functions\orders\set;

use config\db;
use config\sessions;
use config\settings;
use config\table_helper\orders as tbl;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use sameparts\php\ajax\echo_values;

class post_keys {
    const ORDER_ID = "order_id",
     CONFIRM = "confirm";
}

class update_confirm {
    public function __construct(db $db, sessions $sessions, echo_values &$echo) {
        $this->check_values($db, $sessions, $echo);
        if($echo->status){
            $echo->custom_data = (array)$this->update($db, $sessions);
        }
    }


    /* Functions */
    private function update(db $db, sessions $sessions) : results{
        return $db->db_update(
            tbl::TABLE_NAME,
            array(
                tbl::IS_CONFIRM => user::post(post_keys::CONFIRM)
            ),
            where: $db->where->equals([
                tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl::ID => user::post(post_keys::ORDER_ID)
            ])
        );
    }

    private function check_values(db $db, sessions $sessions, echo_values &$echo){
        if(variable::is_empty(
            user::post(post_keys::ORDER_ID)
        )){
            $echo->error_code = settings::error_codes()::EMPTY_VALUE;
        }

        if($echo->error_code != settings::error_codes()::SUCCESS) $echo->status = false;
    }
}