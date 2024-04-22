<?php
namespace manage\functions\report_safe\set;

use config\db;
use config\settings;
use config\table_helper\branch_trust_account_payments as tbl;
use config\table_helper\order_payments as tbl2;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use config\sessions;
use sameparts\php\ajax\echo_values;

class post_keys {
    const ID = "id",
        ACCOUNT_ID = "account_id",
        SAFE_ID = "safe_id",
        PAYMENT_ID = "payment_id",
        FUNCTION_TYPE = "function_type";
}

class function_types {
    const INSERT = 1,
        DELETE = 2;
}

class trust_payment {
    public function __construct(db $db, db $db_backup, sessions $sessions, echo_values &$echo) {
        $db_ = (user::post(post_keys::SAFE_ID) > 0) ? $db_backup : $db;
        $this->check_values($db_, $sessions, $echo);
        if($echo->status){
            $echo->custom_data = match ((int)user::post(post_keys::FUNCTION_TYPE)){
                function_types::DELETE => $this->delete(
                    $db_,
                    $sessions
                )
            };

        }
    }

    private function delete(db $db, sessions $sessions) : array{
        return array(
            "trust_payment" => $db->db_update(
                tbl::TABLE_NAME,
                array(
                    tbl::IS_DELETE => 1,
                ),
                where: $db->where->equals([
                    tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                    tbl::ID => user::post(post_keys::ID),
                    tbl::TRUST_ACCOUNT_ID => user::post(post_keys::ACCOUNT_ID),
                    tbl::PAYMENT_ID => user::post(post_keys::PAYMENT_ID)
                ])
            ),
            "order_payment" => $db->db_update(
                tbl2::TABLE_NAME,
                array(
                    tbl2::IS_DELETE => 1,
                ),
                where: $db->where->equals([
                    tbl2::BRANCH_ID => $sessions->get->BRANCH_ID,
                    tbl2::ID => user::post(post_keys::PAYMENT_ID),
                    tbl2::SAFE_ID => user::post(post_keys::SAFE_ID)
                ])
            )
        );
    }

    private function check_values(db $db, sessions $sessions, echo_values &$echo){
        if(user::post(function_types::DELETE)){
            if(variable::is_empty(
                user::post(post_keys::ID),
                user::post(post_keys::ACCOUNT_ID)
            )){
                $echo->error_code = settings::error_codes()::EMPTY_VALUE;
            }
        }

        if($echo->error_code != settings::error_codes()::SUCCESS) $echo->status = false;
    }
}