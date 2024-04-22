<?php
namespace pos\functions\settings\set;

use config\db;
use config\sessions;
use config\settings;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use sameparts\php\ajax\echo_values;
use config\table_helper\branch_users as tbl;
use sameparts\php\db_query\branch_users;

class post_keys {
    const PASSWORD = "password",
        NEW_PASSWORD = "new_password",
        RE_NEW_PASSWORD = "re_new_password";
}

class change_password {
    public function __construct(db $db, sessions $sessions, echo_values &$echo) {
        $this->check_values($db, $sessions, $echo);
        if($echo->status){
            $echo->custom_data = (array)$this->change($db, $sessions);
        }
    }


    /* Functions */
    private function change(db $db, sessions $sessions) : results{
        return $db->db_update(
            tbl::TABLE_NAME,
            array(
                tbl::PASSWORD => user::post(post_keys::NEW_PASSWORD)
            ),
            where: $db->where->equals(array(
                tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl::ID => $sessions->get->USER_ID
            ))
        );
    }

    private function check_values(db $db, sessions $sessions, echo_values &$echo){
        if(variable::is_empty(
                user::post(post_keys::PASSWORD),
                user::post(post_keys::NEW_PASSWORD),
                user::post(post_keys::RE_NEW_PASSWORD)
            ) ||
            user::post(post_keys::NEW_PASSWORD) != user::post(post_keys::RE_NEW_PASSWORD)
        ){
            $echo->error_code = settings::error_codes()::INCORRECT_DATA;
        }

        if($echo->error_code == settings::error_codes()::SUCCESS){
            if(count(branch_users::get(
                    $db,
                    $sessions->get->BRANCH_ID,
                    user::post(post_keys::PASSWORD),
                    custom_where: $db->where->equals([tbl::ID => $sessions->get->USER_ID]),
                    limit: [0, 1]
                )->rows) < 1) $echo->error_code = settings::error_codes()::WRONG_VALUE;
        }

        if($echo->error_code != settings::error_codes()::SUCCESS) $echo->status = false;
    }
}