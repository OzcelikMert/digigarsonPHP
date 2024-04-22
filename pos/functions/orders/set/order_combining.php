<?php
namespace pos\functions\orders\set;

use config\db;
use config\sessions;
use config\settings;
use config\table_helper\orders as tbl2;
use config\table_helper\order_products as tbl3;
use config\type_tables_values\order_status_types;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use sameparts\php\ajax\echo_values;
use sameparts\php\db_query\branch_tables;
use sameparts\php\db_query\orders;
use sameparts\php\helper\date;

class post_keys {
    const TABLE_ID = "table_id",
        ORDER_ID = "order_id";
}

class order_combining {
    public function __construct(db $db, sessions $sessions, echo_values &$echo) {
        $this->check_values($db, $sessions, $echo);
        if($echo->status){
            $echo->custom_data[0] = (array)$this->update($db, $sessions);
            $echo->custom_data[1] = (array)$this->delete($db, $sessions);
        }
    }


    private function update(db $db, sessions $sessions) : results{
        return $db->db_update(
            tbl3::TABLE_NAME,
            array(
                tbl3::ORDER_ID => user::post(post_keys::ORDER_ID)
            ),
            $db->join->inner([
                tbl2::TABLE_NAME => [tbl2::ID => tbl3::ORDER_ID]
            ]),
            $db->where->equals([
                tbl2::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl2::TABLE_ID => user::post(post_keys::TABLE_ID),
            ])." AND ".$db->where->not_like([
                tbl2::STATUS => order_status_types::DELIVERED
            ])
        );
    }

    private function delete(db $db, sessions $sessions) : results{
        return $db->db_update(
            tbl2::TABLE_NAME,
            array(
                tbl2::BRANCH_ID => "-".$sessions->get->BRANCH_ID,
                tbl2::DATE_END => date::get(),
                tbl2::STATUS => order_status_types::ORDER_COMBINING
            ),
            where: $db->where->equals([
                tbl2::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl2::TABLE_ID => user::post(post_keys::TABLE_ID)
            ])." AND ".$db->where->not_like([
                tbl2::ID => user::post(post_keys::ORDER_ID)
            ])
        );
    }

    private function check_values(db $db, sessions $sessions, echo_values &$echo){
        if(variable::is_empty(
            user::post(post_keys::TABLE_ID),
            user::post(post_keys::ORDER_ID)
        )){
            $echo->error_code = settings::error_codes()::EMPTY_VALUE;
        }

        if($echo->error_code == settings::error_codes()::SUCCESS){
            if(count(branch_tables::get(
                    $db,
                    $sessions->get->BRANCH_ID,
                    user::post(post_keys::TABLE_ID),
                    limit: [0, 1]
                )->rows) < 1) $echo->error_code = settings::error_codes()::INCORRECT_DATA;
        }

        if($echo->error_code == settings::error_codes()::SUCCESS){
            if(count(orders::get(
                    $db,
                    $sessions->get->BRANCH_ID,
                    user::post(post_keys::ORDER_ID),
                    limit: [0, 1]
                )->rows) < 1) $echo->error_code = settings::error_codes()::INCORRECT_DATA;
        }

        if($echo->error_code != settings::error_codes()::SUCCESS) $echo->status = false;
    }
}