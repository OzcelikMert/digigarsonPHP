<?php
namespace pos\functions\finance\set;

use config\db;
use config\sessions;
use matrix_library\php\operations\user;
use sameparts\php\ajax\echo_values;
use config\table_helper\orders as tbl;
use config\table_helper\order_products as tbl2;
use config\table_helper\order_product_options as tbl3;
use config\table_helper\order_payments as tbl4;
use config\table_helper\branch_trust_account_payments as tbl5;
use config\table_helper\branch_safe as tbl6;
use config\table_helper\print_invoices as tbl7;
use config\table_helper\integrate_orders as tbl8;
use config\table_helper\integrate_order_payments as tbl9;
use sameparts\php\helper\date;

class post_keys {
    const COMMENT = "comment",
        NEW_SAFE_ID = "new_safe_id";
}

class data_keys {
    const ORDERS = "orders",
        ORDER_PAYMENTS = "order_payments",
        ORDER_PRODUCTS = "order_products",
        ORDER_PRODUCT_OPTIONS = "order_product_options",
        TRUST_ACCOUNT_PAYMENTS = "branch_trust_account_payments",
        INTEGRATE_ORDERS = "integrate_orders",
        INTEGRATE_ORDER_PAYMENTS = "integrate_order_payments";
}

class close_safe {
    public function __construct(db $db, db $db_backup, sessions $sessions, echo_values &$echo) {
        $data = $this->get($db, $sessions);
        if(count($data[data_keys::ORDERS]) > 0){
            $insert_safe = $this->insert_safe($db, $sessions, $data);
            user::post(post_keys::NEW_SAFE_ID, $insert_safe);
            $set = $this->set($db_backup, $data);
            $this->delete($db, $data, $set, $sessions);
        }
    }

    private function get(db $db, sessions $sessions) : array{
        return array(
            data_keys::ORDERS => $db->db_select(
                tbl::ALL,
                tbl::TABLE_NAME,
                where: $db->where->equals([tbl::BRANCH_ID => $sessions->get->BRANCH_ID, tbl::SAFE_ID => 0])." AND ".$db->where->not_like([tbl::DATE_END => ""]),
                order_by: $db->order_by(tbl::ID, db::ASC)
            )->rows,

            data_keys::ORDER_PRODUCTS => $db->db_select(
                tbl2::ALL,
                tbl2::TABLE_NAME,
                joins: $db->join->inner([tbl::TABLE_NAME => [tbl::ID => tbl2::ORDER_ID]]),
                where: $db->where->equals([tbl2::BRANCH_ID => $sessions->get->BRANCH_ID, tbl::SAFE_ID => 0])." AND ".$db->where->not_like([tbl::DATE_END => ""]),
                order_by: $db->order_by(tbl::ID, db::ASC)
            )->rows,

            data_keys::ORDER_PRODUCT_OPTIONS => $db->db_select(
                tbl3::ALL,
                tbl3::TABLE_NAME,
                joins: $db->join->inner([
                    tbl2::TABLE_NAME => [tbl2::ID => tbl3::ORDER_PRODUCT_ID],
                    tbl::TABLE_NAME => [tbl::ID => tbl2::ORDER_ID]
                ]),
                where: $db->where->equals([tbl3::BRANCH_ID => $sessions->get->BRANCH_ID, tbl::SAFE_ID => 0])." AND ".$db->where->not_like([tbl::DATE_END => ""]),
                order_by: $db->order_by(tbl::ID, db::ASC)
            )->rows,

            data_keys::ORDER_PAYMENTS         => $db->db_select(
                tbl4::ALL,
                tbl4::TABLE_NAME,
                where: $db->where->equals([tbl4::BRANCH_ID => $sessions->get->BRANCH_ID, tbl4::SAFE_ID => 0]),
                order_by: $db->order_by(tbl4::ID, db::ASC)
            )->rows,

            data_keys::TRUST_ACCOUNT_PAYMENTS => $db->db_select(
                tbl5::ALL,
                tbl5::TABLE_NAME,
                joins: $db->join->inner([tbl4::TABLE_NAME => [tbl4::ID => tbl5::PAYMENT_ID]]),
                where: $db->where->equals([tbl5::BRANCH_ID => $sessions->get->BRANCH_ID, tbl4::SAFE_ID => 0]),
                order_by: $db->order_by(tbl4::ID, db::ASC)
            )->rows,

            data_keys::INTEGRATE_ORDERS => $db->db_select(
                tbl8::ALL,
                tbl8::TABLE_NAME,
                joins: $db->join->inner([tbl::TABLE_NAME => [tbl::ID => tbl8::ORDER_ID]]),
                where: $db->where->equals([tbl8::BRANCH_ID => $sessions->get->BRANCH_ID, tbl8::SAFE_ID => 0])." AND ".$db->where->not_like([tbl::DATE_END => ""]),
                order_by: $db->order_by(tbl::ID, db::ASC)
            )->rows,

            data_keys::INTEGRATE_ORDER_PAYMENTS => $db->db_select(
                tbl9::ALL,
                tbl9::TABLE_NAME,
                joins: $db->join->inner([
                    tbl8::TABLE_NAME => [tbl8::ID => tbl9::INTEGRATE_ORDER_ID],
                    tbl::TABLE_NAME => [tbl::ID => tbl8::ORDER_ID]
                ]),
                where: $db->where->equals([tbl9::BRANCH_ID => $sessions->get->BRANCH_ID, tbl9::SAFE_ID => 0]),
                order_by: $db->order_by(tbl9::ID, db::ASC)
            )->rows
        );
    }

    private function set(db $db_backup, array $data) : array {
        for ($i = 0; $i < count($data[data_keys::ORDERS]); $i++){
            $data[data_keys::ORDERS][$i]["safe_id"] = user::post(post_keys::NEW_SAFE_ID);
        }

        for ($i = 0; $i < count($data[data_keys::ORDER_PAYMENTS]); $i++){
            $data[data_keys::ORDER_PAYMENTS][$i]["safe_id"] = user::post(post_keys::NEW_SAFE_ID);
        }

        for ($i = 0; $i < count($data[data_keys::INTEGRATE_ORDERS]); $i++){
            $data[data_keys::INTEGRATE_ORDERS][$i]["safe_id"] = user::post(post_keys::NEW_SAFE_ID);
        }

        for ($i = 0; $i < count($data[data_keys::INTEGRATE_ORDER_PAYMENTS]); $i++){
            $data[data_keys::INTEGRATE_ORDER_PAYMENTS][$i]["safe_id"] = user::post(post_keys::NEW_SAFE_ID);
        }

        return array(
            data_keys::ORDERS                   => (array)$db_backup->db_insert(tbl::TABLE_NAME, $data[data_keys::ORDERS]),
            data_keys::ORDER_PRODUCTS           => (array)$db_backup->db_insert(tbl2::TABLE_NAME, $data[data_keys::ORDER_PRODUCTS]),
            data_keys::ORDER_PRODUCT_OPTIONS    => (array)$db_backup->db_insert(tbl3::TABLE_NAME, $data[data_keys::ORDER_PRODUCT_OPTIONS]),
            data_keys::ORDER_PAYMENTS           => (array)$db_backup->db_insert(tbl4::TABLE_NAME, $data[data_keys::ORDER_PAYMENTS]),
            data_keys::TRUST_ACCOUNT_PAYMENTS   => (array)$db_backup->db_insert(tbl5::TABLE_NAME, $data[data_keys::TRUST_ACCOUNT_PAYMENTS]),
            data_keys::INTEGRATE_ORDERS         => (array)$db_backup->db_insert(tbl8::TABLE_NAME, $data[data_keys::INTEGRATE_ORDERS]),
            data_keys::INTEGRATE_ORDER_PAYMENTS => (array)$db_backup->db_insert(tbl9::TABLE_NAME, $data[data_keys::INTEGRATE_ORDER_PAYMENTS]),
        );
    }

    private function delete(db $db, array $data, array $set, sessions $sessions){
        if($set[data_keys::ORDERS]["status"]){
            $id = array();
            foreach ($data[data_keys::ORDERS] as $value){
                array_push($id, $value["id"]);
            }
            $db->db_delete(tbl::TABLE_NAME, where: $db->where->equals([tbl::ID => $id]));
        }

        if($set[data_keys::ORDER_PRODUCTS]["status"]){
            $id = array();
            foreach ($data[data_keys::ORDER_PRODUCTS] as $value){
                array_push($id, $value["id"]);
            }
            $db->db_delete(tbl2::TABLE_NAME, where: $db->where->equals([tbl2::ID => $id]));
        }

        if($set[data_keys::ORDER_PRODUCT_OPTIONS]["status"]){
            $id = array();
            foreach ($data[data_keys::ORDER_PRODUCT_OPTIONS] as $value){
                array_push($id, $value["id"]);
            }
            $db->db_delete(tbl3::TABLE_NAME, where: $db->where->equals([tbl3::ID => $id]));
        }

        if($set[data_keys::ORDER_PAYMENTS]["status"]){
            $id = array();
            foreach ($data[data_keys::ORDER_PAYMENTS] as $value){
                array_push($id, $value["id"]);
            }
            $db->db_delete(tbl4::TABLE_NAME, where: $db->where->equals([tbl4::ID => $id]));
        }

        if($set[data_keys::TRUST_ACCOUNT_PAYMENTS]["status"]){
            $id = array();
            foreach ($data[data_keys::TRUST_ACCOUNT_PAYMENTS] as $value){
                array_push($id, $value["id"]);
            }
            $db->db_delete(tbl5::TABLE_NAME, where: $db->where->equals([tbl5::ID => $id]));
        }

        if($set[data_keys::INTEGRATE_ORDERS]["status"]){
            $id = array();
            foreach ($data[data_keys::INTEGRATE_ORDERS] as $value){
                array_push($id, $value["id"]);
            }
            $db->db_delete(tbl8::TABLE_NAME, where: $db->where->equals([tbl8::ID => $id]));
        }

        if($set[data_keys::INTEGRATE_ORDER_PAYMENTS]["status"]){
            $id = array();
            foreach ($data[data_keys::INTEGRATE_ORDER_PAYMENTS] as $value){
                array_push($id, $value["id"]);
            }
            $db->db_delete(tbl9::TABLE_NAME, where: $db->where->equals([tbl9::ID => $id]));
        }

        $db->db_delete(
            tbl7::TABLE_NAME,
            where: $db->where->equals([
                tbl7::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl7::IS_PRINT  => 1
            ])
        );
    }

    private function insert_safe(db $db, sessions $sessions, array $data) : int{
        return $db->db_insert(
            tbl6::TABLE_NAME,
            array(
                tbl6::BRANCH_ID     => $sessions->get->BRANCH_ID,
                tbl6::DATE_START    => $data[data_keys::ORDERS][0]["date_start"],
                tbl6::DATE_END      => date::get(),
                tbl6::COMMENT       => user::post(post_keys::COMMENT),
                tbl6::ACCOUNT_ID    => $sessions->get->USER_ID
            )
        )->insert_id;
    }
}