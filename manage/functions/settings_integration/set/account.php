<?php
namespace manage\functions\settings_integration\set;

use config\db;
use config\sessions\integrations\results;
use config\settings;
use config\table_helper\integrate_users as tbl;
use config\type_tables_values\integrate_types;
use config\sessions;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use sameparts\php\ajax\echo_values;
use sameparts\php\db_query\integrate;

class post_keys{
    const USER_NAME = "user_name",
        PASSWORD = "password",
        STATUS = "status",
        TYPE = "type",
        FUNCTION_TYPE = "function_type";
}

class function_types {
    const INSERT = 0x0001,
        UPDATE = 0x0002;
}

class account{
    function __construct(db $db, sessions $sessions,echo_values &$echo){
        $this->check_values($db, $sessions, $echo);
        if($echo->status){
            switch (user::post(post_keys::FUNCTION_TYPE)){
                case function_types::INSERT:
                    $this->insert($db, $sessions, $echo);
                    break;
                case function_types::UPDATE:
                    $this->update($db, $sessions, $echo);
                    break;
            }

            $key = match((int)user::post(post_keys::TYPE)){
                integrate_types::YEMEK_SEPETI => sessions::INTEGRATION_KEYS()::YEMEK_SEPETI,
                integrate_types::GETIR => sessions::INTEGRATION_KEYS()::GETIR
            };

            $sessions->set->INTEGRATION($key, new results(user::post(post_keys::USER_NAME), user::post(post_keys::PASSWORD), user::post(post_keys::STATUS)));
            $sessions->create();
        }
    }

    private function insert(db $db,sessions $sessions, echo_values &$echo): void{
        $db->db_insert(
            tbl::TABLE_NAME,
            array(
                tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl::TYPE      => user::post(post_keys::TYPE),
                tbl::IS_ACTIVE => user::post(post_keys::STATUS),
                tbl::USER_NAME => user::post(post_keys::USER_NAME),
                tbl::PASSWORD  => user::post(post_keys::PASSWORD)
            )
        );
    }

    private function update(db $db,sessions $sessions, echo_values &$echo): void{
        $db->db_update(
            tbl::TABLE_NAME,
            array(
                tbl::IS_ACTIVE => user::post(post_keys::STATUS),
                tbl::USER_NAME => user::post(post_keys::USER_NAME),
                tbl::PASSWORD  => user::post(post_keys::PASSWORD)
            ),
            where: $db->where->equals([
                tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl::TYPE      => user::post(post_keys::TYPE)
            ])
        );
    }

    private function check_values(db $db, sessions $sessions, echo_values &$echo){
        if(variable::is_empty(
            user::post(post_keys::USER_NAME),
            user::post(post_keys::PASSWORD)
        )){
            $echo->error_code = settings::error_codes()::EMPTY_VALUE;
        }

        if($echo->error_code == settings::error_codes()::SUCCESS){
            if(count(integrate::get_types(
                    $db,
                    user::post(post_keys::TYPE)
                )->rows) < 1){
                $echo->error_code = settings::error_codes()::INCORRECT_DATA;
            }
        }

        if($echo->error_code == settings::error_codes()::SUCCESS){
            if(count(integrate::get_users(
                    $db,
                    $sessions->get->BRANCH_ID,
                    user::post(post_keys::TYPE)
                )->rows) > 0){
                user::post(post_keys::FUNCTION_TYPE, function_types::UPDATE);
            }else{
                user::post(post_keys::FUNCTION_TYPE, function_types::INSERT);
            }
        }

        if($echo->error_code != settings::error_codes()::SUCCESS) $echo->status = false;
    }
}
