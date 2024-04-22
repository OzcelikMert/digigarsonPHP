<?php
namespace pos\functions\orders\set;

use config\db;
use config\sessions;
use config\settings;
use config\table_helper\branch_tables as tbl;
use config\table_helper\orders as tbl2;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use sameparts\php\ajax\echo_values;
use sameparts\php\db_query\branch_tables;

class post_keys {
    const TABLE_ID = "table_id",
        ORDER_ID = "order_id",
        TABLE_MOVE_ID = "table_move_id";
}

class table_move {
    public function __construct(db $db, sessions $sessions, echo_values &$echo) {
        $this->check_values($db, $sessions, $echo);
        if($echo->status){
            $echo->custom_data = (array)$this->move($db, $sessions);
        }
    }


    /* Functions */
    private function move(db $db, sessions $sessions) : results{
        $where = array(
            tbl2::BRANCH_ID => $sessions->get->BRANCH_ID,
            tbl2::TABLE_ID => user::post(post_keys::TABLE_ID)
        );
        if (user::post(post_keys::ORDER_ID) != 0) array_push($where, [tbl2::ID => user::post(post_keys::ORDER_ID)]);

        return $db->db_update(
            tbl2::TABLE_NAME,
            array(
                tbl2::TABLE_ID => user::post(post_keys::TABLE_MOVE_ID)
            ),
            where: $db->where->equals($where)
        );
    }

    private function check_values(db $db, sessions $sessions, echo_values &$echo){
        if(variable::is_empty(
            user::post(post_keys::TABLE_ID),
            user::post(post_keys::TABLE_MOVE_ID),
            user::post(post_keys::ORDER_ID)
        )){
            $echo->error_code = settings::error_codes()::EMPTY_VALUE;
        }

        if($echo->error_code == settings::error_codes()::SUCCESS){
            if(count(branch_tables::get(
                    $db,
                    $sessions->get->BRANCH_ID,
                    custom_where: $db->where->equals([
                        tbl::ID => array(
                            user::post(post_keys::TABLE_ID),
                            user::post(post_keys::TABLE_MOVE_ID)
                        )
                    ]),
                    limit: [0, 2]
                )->rows) < 2) $echo->error_code = settings::error_codes()::INCORRECT_DATA;
        }

        if($echo->error_code != settings::error_codes()::SUCCESS) $echo->status = false;
    }
}