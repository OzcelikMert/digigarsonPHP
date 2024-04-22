<?php
namespace manage\functions\list_user\set;

use config\db;
use config\settings;
use config\table_helper\branch_users as tbl;
use config\table_helper\branch_user_permission_types as tbl2;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\array_list;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use config\sessions;
use sameparts\php\ajax\echo_values;
use sameparts\php\db_query\branch_users;

class post_keys {
    const ID = "id",
        NAME = "name",
        PASSWORD = "password",
        ACTIVE = "active",
        PERMISSIONS = "permissions",
        FUNCTION_TYPE = "function_type";
}

class function_types {
    const INSERT = 0x0001,
        DELETE = 0x0002;
}

class user_control {
    public function __construct(db $db, sessions $sessions, echo_values &$echo) {
        $this->check_values($db, $sessions, $echo);
        if($echo->status){
            if(user::post(post_keys::FUNCTION_TYPE) == function_types::INSERT && user::post(post_keys::ID) > 0){
                $echo->custom_data = (array)$this->update($db, $sessions);
            } else if (user::post(post_keys::FUNCTION_TYPE) == function_types::DELETE && user::post(post_keys::ID) > 0) {
                $echo->custom_data = (array)$this->delete($db, $sessions);
            }else{
                $echo->custom_data = (array)$this->insert($db, $sessions);
            }
        }
    }


    /* Functions */
    private function insert(db $db, sessions $sessions) : results{
        return $db->db_insert(
            tbl::TABLE_NAME,
            array(
                tbl::BRANCH_ID   => $sessions->get->BRANCH_ID,
                tbl::NAME        => user::post(post_keys::NAME),
                tbl::PASSWORD    => user::post(post_keys::PASSWORD),
                tbl::ACTIVE      => (int)user::post(post_keys::ACTIVE),
                tbl::PERMISSIONS => json_encode(user::post(post_keys::PERMISSIONS))
            )
        );
    }

    private function update(db $db, sessions $sessions) : results{
        return $db->db_update(
            tbl::TABLE_NAME,
            array(
                tbl::BRANCH_ID   => $sessions->get->BRANCH_ID,
                tbl::NAME        => user::post(post_keys::NAME),
                tbl::ACTIVE      => (int)user::post(post_keys::ACTIVE),
                tbl::PERMISSIONS => json_encode(user::post(post_keys::PERMISSIONS))
            ),
            where: $db->where->equals([
                tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl::ID        => user::post(post_keys::ID)
            ])
        );
    }

    private function delete(db $db, sessions $sessions) : results{
        return $db->db_update(
            tbl::TABLE_NAME,
            array(
                tbl::IS_DELETE => 1
            ),
            where: $db->where->equals([
            tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
            tbl::ID        => user::post(post_keys::ID)
        ])
        );
    }

    private function check_values(db $db, sessions $sessions, echo_values &$echo){
        if(variable::is_empty(
            user::post(post_keys::NAME),
            user::post(post_keys::ID)
        )){
            $echo->error_code = settings::error_codes()::EMPTY_VALUE;
        }

        if($echo->error_code == settings::error_codes()::SUCCESS && user::post(post_keys::ID) == 0){
            if(variable::is_empty(user::post(post_keys::PASSWORD))){
                $echo->error_code = settings::error_codes()::EMPTY_VALUE;
            }else{
                if(count(branch_users::get(
                        $db,
                        $sessions->get->BRANCH_ID,
                        user::post(post_keys::PASSWORD),
                        limit: [0, 1]
                    )->rows) > 0) $echo->error_code = settings::error_codes()::REGISTERED_VALUE;
            }
        }

        if($echo->error_code == settings::error_codes()::SUCCESS){
            if(user::post(post_keys::PERMISSIONS) && count(user::post(post_keys::PERMISSIONS)) > 0){
                $id = array();
                foreach (user::post(post_keys::PERMISSIONS) as $value){
                    if(array_list::index_of($id, $value) < 0) array_push($id, $value);
                }
                if(count(branch_users::get_permissions(
                    $db,
                    $sessions->get->LANGUAGE_TAG,
                    custom_where: $db->where->equals([tbl2::ID => $id])
                )->rows) != count($id)) $echo->error_code = settings::error_codes()::INCORRECT_DATA;
            }else{
                user::post(post_keys::PERMISSIONS, []);
            }
        }

        if($echo->error_code != settings::error_codes()::SUCCESS) $echo->status = false;
    }
}