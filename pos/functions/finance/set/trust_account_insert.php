<?php
namespace pos\functions\finance\set;

use config\db;
use config\sessions;
use config\settings;
use config\table_helper\branch_trust_accounts as tbl;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use sameparts\php\ajax\echo_values;

class post_keys {
    const ID = "id",
        NAME = "name",
        DISCOUNT = "discount",
        ADDRESS = "address",
        PHONE = "phone",
        TAX_NO = "tax_no",
        TAX_ADMINISTRATION = "tax_administration",
        FUNCTION_TYPE = "function_type";
}

class function_types {
    const INSERT = 0x0001,
        DELETE = 0x0002;
}

class trust_account_insert {
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
                tbl::BRANCH_ID          => $sessions->get->BRANCH_ID,
                tbl::NAME               => user::post(post_keys::NAME),
                tbl::DISCOUNT           => user::post(post_keys::DISCOUNT),
                tbl::PHONE              => user::post(post_keys::PHONE),
                tbl::ADDRESS            => user::post(post_keys::ADDRESS),
                tbl::TAX_ADMINISTRATION => user::post(post_keys::TAX_ADMINISTRATION),
                tbl::TAX_NO             => user::post(post_keys::TAX_NO)
            )
        );
    }

    private function update(db $db, sessions $sessions) : results{
        return $db->db_update(
            tbl::TABLE_NAME,
            array(
                tbl::NAME               => user::post(post_keys::NAME),
                tbl::DISCOUNT           => user::post(post_keys::DISCOUNT),
                tbl::PHONE              => user::post(post_keys::PHONE),
                tbl::ADDRESS            => user::post(post_keys::ADDRESS),
                tbl::TAX_ADMINISTRATION => user::post(post_keys::TAX_ADMINISTRATION),
                tbl::TAX_NO             => user::post(post_keys::TAX_NO)
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

        if($echo->error_code != settings::error_codes()::SUCCESS) $echo->status = false;
    }
}