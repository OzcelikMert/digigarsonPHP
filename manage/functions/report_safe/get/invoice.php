<?php
namespace manage\functions\report_safe\get;

use config\db;
use config\type_tables_values\account_types;
use config\sessions;
use matrix_library\php\operations\array_list;
use matrix_library\php\operations\user;
use sameparts\php\ajax\echo_values;
use config\table_helper\orders as tbl;
use config\table_helper\order_payments as tbl3;
use config\table_helper\customer_users as tbl4;
use config\table_helper\branch_users as tbl5;
use config\table_helper\account_types as tbl6;

class post_keys {
    const SAFE_ID = "safe_id";
}

class invoice {
    public function __construct(db $db, db $db_backup, sessions $sessions, echo_values &$echo) {
        $echo->rows["payments"] = $this->get(
            ((user::post(post_keys::SAFE_ID) == 0)
                ? $db
                : $db_backup),
            $sessions);
        $echo->rows["customer_users"] = $this->get_customer_users($db, $sessions, $echo->rows["payments"]);
        $echo->rows["branch_users"] = $this->get_branch_users($db, $sessions, $echo->rows["payments"]);
    }

    private function get(db $db, sessions $sessions) : array{
        return $db->db_select(
            array(
                tbl3::ID,
                tbl3::ORDER_ID,
                tbl3::PRICE,
                tbl3::DATE,
                tbl3::TYPE,
                tbl3::ACCOUNT_ID,
                tbl3::ACCOUNT_TYPE
            ),
            tbl3::TABLE_NAME,
            where: $db->where->equals([
                tbl3::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl3::SAFE_ID   => user::post(post_keys::SAFE_ID),
                tbl3::IS_DELETE => 0
            ]),
            order_by: $db->order_by(tbl3::ID, db::DESC)
        )->rows;
    }

    private function get_customer_users(db $db, sessions $sessions, array $data) : array{
        $values = array();

        $id = array();
        foreach ($data as $data_){
            if($data_["account_type"] == account_types::CUSTOMER && array_list::index_of($id, $data_["account_id"]) < 0)
                array_push($id, $data_["account_id"]);
        }

        if(count($id) > 0){
            $values = $db->db_select(
                array(
                    tbl4::ID,
                    tbl4::NAME,
                    $db->as_name(tbl6::NAME.$sessions->get->LANGUAGE_TAG, "type")
                ),
                tbl4::TABLE_NAME,
                $db->join->inner([tbl6::TABLE_NAME => [tbl6::ID => account_types::CUSTOMER]]),
                $db->where->equals([
                    tbl4::ID => $id
                ])
            )->rows;
        }

        return $values;
    }

    private function get_branch_users(db $db, sessions $sessions, array $data) : array{
        $values = array();

        $id = array();
        foreach ($data as $data_){
            if($data_["account_type"] == account_types::WAITER && array_list::index_of($id, $data_["account_id"]) < 0)
                array_push($id, $data_["account_id"]);
        }

        if(count($id) > 0){
            $values = $db->db_select(
                array(
                    tbl5::ID,
                    tbl5::NAME,
                    $db->as_name(tbl6::NAME.$sessions->get->LANGUAGE_TAG, "type")
                ),
                tbl5::TABLE_NAME,
                $db->join->inner([tbl6::TABLE_NAME => [tbl6::ID => account_types::WAITER]]),
                $db->where->equals([
                    tbl5::ID => $id
                ])
            )->rows;
        }

        return $values;
    }
}