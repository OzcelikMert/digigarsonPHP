<?php
namespace manage\functions\list_device\set;

use config\db;
use config\settings;
use config\table_helper\branch_devices as tbl;
use config\table_helper\device_types as tbl2;
use config\type_tables_values\device_types;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use config\sessions;
use sameparts\php\ajax\echo_values;
use sameparts\php\db_query\branch_users;

class post_keys {
    const ID = "id",
        NAME = "name",
        SECURITY_CODE = "security_code",
        IS_CONNECT = "is_connect",
        CALLER_ID_ACTIVE = "caller_id_active",
        TYPE = "type",
        FUNCTION_TYPE = "function_type";
}

class function_types {
    const INSERT = 0x0001,
        DELETE = 0x0002;
}

class device_control {
    public function __construct(db $db, sessions $sessions, echo_values &$echo) {
        user::post(post_keys::SECURITY_CODE, dechex($sessions->get->BRANCH_ID+rand(1,99999999)+time()));
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
                tbl::BRANCH_ID        => $sessions->get->BRANCH_ID,
                tbl::NAME             => user::post(post_keys::NAME),
                tbl::SECURITY_CODE    => user::post(post_keys::SECURITY_CODE),
                tbl::TYPE             => user::post(post_keys::TYPE),
                tbl::TOKEN            => "",
                tbl::IS_CONNECT       => (int)user::post(post_keys::IS_CONNECT),
                tbl::CALLER_ID_ACTIVE => (int)user::post(post_keys::CALLER_ID_ACTIVE)
            )
        );
    }

    private function update(db $db, sessions $sessions) : results{
        return $db->db_update(
            tbl::TABLE_NAME,
            array(
                tbl::NAME             => user::post(post_keys::NAME),
                tbl::TYPE             => user::post(post_keys::TYPE),
                tbl::IS_CONNECT       => (int)user::post(post_keys::IS_CONNECT),
                tbl::CALLER_ID_ACTIVE => (int)user::post(post_keys::CALLER_ID_ACTIVE)
            ),
            where: $db->where->equals([
                tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl::ID        => user::post(post_keys::ID)
            ])
        );
    }

    private function delete(db $db, sessions $sessions) : results{
        return $db->db_delete(
            tbl::TABLE_NAME,
            where: $db->where->equals([
                tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl::ID        => user::post(post_keys::ID)
            ])
        );
    }

    private function check_values(db $db, sessions $sessions, echo_values &$echo){
        if(user::post(post_keys::FUNCTION_TYPE) == function_types::INSERT && variable::is_empty(
            user::post(post_keys::NAME),
            user::post(post_keys::ID),
            user::post(post_keys::SECURITY_CODE)
        )){
            $echo->error_code = settings::error_codes()::EMPTY_VALUE;
        }else if (user::post(post_keys::FUNCTION_TYPE) == function_types::DELETE && variable::is_empty(user::post(post_keys::ID))){
            $echo->error_code = settings::error_codes()::EMPTY_VALUE;
        }

        if(user::post(post_keys::FUNCTION_TYPE) == function_types::INSERT && $echo->error_code == settings::error_codes()::SUCCESS && user::post(post_keys::ID) == 0){
            $check_column = (user::post(post_keys::TYPE) == device_types::WAITER_TERMINAL)
                ? "waiter_app_limit"
                : "pos_app_limit";
            if($db->db_select(
                    $db->as_name($db->count(tbl::ID), "`count`"),
                    tbl::TABLE_NAME,
                    where: $db->where->equals([tbl::BRANCH_ID => $sessions->get->BRANCH_ID, tbl::TYPE => user::post(post_keys::TYPE)])
                )->rows[0]["count"] >= branch_users::get_branch_info($db, $sessions->get->BRANCH_ID, limit: [0, 1])->rows[0][$check_column]) $echo->error_code = settings::error_codes()::NO_PERM;
        }

        if($echo->error_code == settings::error_codes()::SUCCESS){
            if(count($db->db_select(
                    tbl::ID,
                    tbl::TABLE_NAME,
                    where: $db->where->equals([
                        tbl::SECURITY_CODE => user::post(post_keys::SECURITY_CODE)
                    ])." ".db::AND." ".$db->where->not_like([tbl::ID => user::post(post_keys::ID)])
                )->rows) > 0) $echo->error_code = settings::error_codes()::REGISTERED_VALUE;
        }

        if($echo->error_code == settings::error_codes()::SUCCESS && user::post(post_keys::FUNCTION_TYPE) == function_types::INSERT){
            if(count($db->db_select(
                    tbl2::ID,
                    tbl2::TABLE_NAME,
                    where: $db->where->equals([tbl2::ID => user::post(post_keys::TYPE)])
                )->rows) < 1) $echo->error_code = settings::error_codes()::INCORRECT_DATA;
        }

        if($echo->error_code != settings::error_codes()::SUCCESS) $echo->status = false;
    }
}