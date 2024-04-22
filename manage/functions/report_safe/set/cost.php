<?php
namespace manage\functions\report_safe\set;

use config\db;
use config\settings;
use config\table_helper\order_payments as tbl;
use config\type_tables_values\account_types;
use config\type_tables_values\order_payment_status_types;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use config\sessions;
use sameparts\php\ajax\echo_values;
use sameparts\php\helper\date;

class post_keys {
    const ID = "id",
        COMMENT = "comment",
        PRICE = "price",
        SAFE_ID = "safe_id",
        FUNCTION_TYPE = "function_type";
}

class function_types {
    const INSERT = 1,
        DELETE = 2;
}

class cost {
    public function __construct(db $db, db $db_backup, sessions $sessions, echo_values &$echo) {
        $this->check_values($db, $sessions, $echo);
        if($echo->status){
            $echo->custom_data = (array)match ((int)user::post(post_keys::FUNCTION_TYPE)){
                function_types::INSERT => $this->insert($db, $sessions),
                function_types::DELETE => $this->delete(
                    ((user::post(post_keys::SAFE_ID) > 0) ? $db_backup : $db),
                    $sessions
                )
            };

        }
    }

    /* Functions */
    private function insert(db $db, sessions $sessions) : results{
        return $db->db_insert(
            tbl::TABLE_NAME,
            array(
                tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl::COMMENT => user::post(post_keys::COMMENT),
                tbl::PRICE => user::post(post_keys::PRICE) * -1,
                tbl::DATE => date::get(),
                tbl::ORDER_ID => 0,
                tbl::STATUS => order_payment_status_types::COST,
                tbl::TYPE => 0,
                tbl::ACCOUNT_ID => $sessions->get->USER_ID,
                tbl::ACCOUNT_TYPE => account_types::MANAGE
            )
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
                tbl::SAFE_ID   => user::post(post_keys::SAFE_ID),
                tbl::ID        => user::post(post_keys::ID),
                tbl::STATUS    => order_payment_status_types::COST
            ])
        );
    }

    private function check_values(db $db, sessions $sessions, echo_values &$echo){
        if(user::post(function_types::INSERT)){
            if(variable::is_empty(
                user::post(post_keys::COMMENT),
                user::post(post_keys::PRICE)
            )){
                $echo->error_code = settings::error_codes()::EMPTY_VALUE;
            }
        }else if(user::post(function_types::DELETE)){
            if(variable::is_empty(
                user::post(post_keys::ID),
                user::post(post_keys::SAFE_ID)
            )){
                $echo->error_code = settings::error_codes()::EMPTY_VALUE;
            }
        }




        if($echo->error_code != settings::error_codes()::SUCCESS) $echo->status = false;
    }
}