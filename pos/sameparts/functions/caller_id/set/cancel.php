<?php
namespace pos\sameparts\functions\caller_id\set;

use config\db;
use config\sessions;
use config\table_helper\branch_callers as tbl;
use config\type_tables_values\branch_caller_status_types;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\user;
use sameparts\php\ajax\echo_values;

class post_keys {
    const CALLER_ID = "caller_id";
}

class cancel {
    public function __construct(db $db, sessions $sessions, echo_values &$echo) {
        $this->update($db, $sessions);
        $echo->custom_data["POST"] = $_POST;
    }

    /* Functions */
    private function update(db $db, sessions $sessions) : results{
        return $db->db_update(
            tbl::TABLE_NAME,
            array(
                tbl::STATUS => branch_caller_status_types::CANCEL
            ),
            where: $db->where->equals([
                tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl::ID        => user::post(post_keys::CALLER_ID)
            ])
        );
    }
}