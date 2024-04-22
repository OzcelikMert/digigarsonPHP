<?php
namespace language\functions\index\set;

use config\db;
use config\settings;
use config\table_helper\translate as tbl;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use language\sameparts\functions\sessions\get;
use sameparts\php\ajax\echo_values;

class post_keys {
    const ID = "id";
}

class delete {
    public function __construct(db $db, get $sessions, echo_values &$echo) {
        $this->check_values($db, $sessions, $echo);
        if($echo->status){
            $echo->custom_data = (array)$this->set($db, $sessions);
        }
    }


    /* Functions */
    private function set(db $db, get $sessions) : results{
        return $db->db_delete(
            tbl::TABLE_NAME,
            where: $db->where->equals([tbl::ID => user::post(post_keys::ID)])
        );
    }

    private function check_values(db $db, get $sessions, echo_values &$echo){
        if(variable::is_empty(
            user::post(post_keys::ID)
        )){
            $echo->error_code = settings::error_codes()::EMPTY_VALUE;
        }

        if($echo->error_code == settings::error_codes()::SUCCESS){
            if(count($db->db_select(
                    tbl::ALL,
                    tbl::TABLE_NAME,
                    where: $db->where->equals([tbl::ID => user::post(post_keys::ID)]),
                    limit: $db->limit([0,1]),
                )->rows) < 1) $echo->error_code = settings::error_codes()::INCORRECT_DATA;
        }

        if($echo->error_code != settings::error_codes()::SUCCESS) $echo->status = false;
    }
}