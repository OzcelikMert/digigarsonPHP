<?php

namespace pos\functions\products\set;


use config\db;
use config\sessions;
use config\settings;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use config\table_helper\products as tbl;
use sameparts\php\ajax\echo_values;
use sameparts\php\db_query\products;


class post_keys {
      CONST  ID = "id";
}

class update{
    function __construct(db $db, sessions $sessions, echo_values &$echo){
        if(user::check_sent_data([post_keys::ID])){
            $this->check_values_delete($db, $sessions, $echo);
            if ($echo->status) $echo->autofill((array)$this->update_favorite($db, $sessions));
        }
    }
    function check_values_delete(db $db, sessions $sessions, echo_values &$echo){
        if(variable::is_empty(user::post(post_keys::ID))) $echo->error_code = settings::error_codes()::EMPTY_VALUE;

        if($echo->error_code == settings::error_codes()::SUCCESS){
            if(count(products::get(
                    $db,
                    $sessions->get->LANGUAGE_TAG,
                    $sessions->get->BRANCH_ID,
                    (int)user::post(post_keys::ID),
                    limit: [0, 1]
                )->rows) < 1) $echo->error_code = settings::error_codes()::INCORRECT_DATA;
        }

        if($echo->error_code != settings::error_codes()::SUCCESS) $echo->status = false;
    }
    function update_favorite(db $db, sessions $sessions): results{
        return $db->db_update(
            tbl::TABLE_NAME,
            array(
                tbl::FAVORITE => $db->case->equals([tbl::FAVORITE => 1], 0, 1)
            ),
            where: $db->where->like([
            tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
            tbl::ID => (int)user::post(post_keys::ID)
        ])
        );
    }

}