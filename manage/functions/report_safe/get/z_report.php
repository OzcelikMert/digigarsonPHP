<?php
namespace manage\functions\report_safe\get;

use config\db;
use config\type_tables_values\order_payment_status_types;
use config\type_tables_values\order_products_status_types;
use config\sessions;
use matrix_library\php\operations\array_list;
use matrix_library\php\operations\user;
use sameparts\php\ajax\echo_values;
use config\table_helper\order_payments as tbl;
use config\table_helper\payment_types as tbl2;
use config\table_helper\order_products as tbl3;
use config\table_helper\order_product_options as tbl4;
use config\table_helper\products as tbl5;
use config\table_helper\product_option_items as tbl6;
use config\table_helper\orders as tbl7;

class post_keys {
    const SAFE_ID = "safe_id";
}

class z_report {
    public function __construct(db $db, db $db_backup, sessions $sessions, echo_values &$echo) {
        $db_ = (user::post(post_keys::SAFE_ID) == 0) ? $db : $db_backup;
        $echo->message = "Z REPORT";
        $echo->rows = array(
            "payments"        => $this->get_payments($db, $db_, $sessions),
            "trust_payments"  => $this->get_trust_payments($db, $db_, $sessions),
            "products"        => $this->get_products($db, $db_, $sessions),
            "cancel_products" => $this->get_cancel_products($db, $db_, $sessions),
            "costs"           => $this->get_costs($db, $db_, $sessions)
        );
    }

    private function get_payments(db $db, db $db_, sessions $sessions) : array{
        return array(
            "data" => $db_->db_select(
                array(
                    tbl::TYPE,
                    $db->as_name($db->sum(tbl::PRICE),"price")
                ),tbl::TABLE_NAME,
                where: $db->where->equals([
                    tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                    tbl::SAFE_ID   => user::post(post_keys::SAFE_ID),
                    tbl::STATUS    => [
                        order_payment_status_types::PAID,
                        order_payment_status_types::CANCEL
                    ],
                    tbl::IS_DELETE => 0
                ]). " AND ".$db->where->greater_than([tbl::ORDER_ID => 0 ]),
                group_by: tbl::TYPE,
                order_by: tbl::TYPE
            )->rows,

            "names" => $db->db_select(
                array(
                    tbl2::ID,
                    $db->as_name(tbl2::NAME.$sessions->get->LANGUAGE_TAG, "name")
                ),
                tbl2::TABLE_NAME
            )->rows
        );
    }

    private function get_trust_payments(db $db, db $db_, sessions $sessions) : array{
        return array(
            "data" => $db_->db_select(
                array(
                    tbl::TYPE,
                    $db->as_name($db->sum(tbl::PRICE),"price")
                ),tbl::TABLE_NAME,
                where: $db->where->equals([
                    tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                    tbl::SAFE_ID   => user::post(post_keys::SAFE_ID),
                    tbl::ORDER_ID  => 0,
                    tbl::STATUS    => [
                        order_payment_status_types::PAID
                    ],
                    tbl::IS_DELETE => 0
                ]),
                group_by: tbl::TYPE,
                order_by: tbl::TYPE
            )->rows,

            "names" => $db->db_select(
                array(
                    tbl2::ID,
                    $db->as_name(tbl2::NAME.$sessions->get->LANGUAGE_TAG, "name")
                ),
                tbl2::TABLE_NAME
            )->rows
        );
    }

    private function get_products(db $db, db $db_, sessions $sessions) : array{
        $id = array();
        $id_options = array();

        $data = $db_->db_select(
            array(
                tbl3::PRODUCT_ID,
                $db->as_name($db->sum( tbl3::QUANTITY),"quantity"),
                $db->as_name($db->sum( tbl3::QTY),"qty"),
                $db->as_name($db->sum( tbl3::PRICE),"price"),
            ),
            tbl3::TABLE_NAME,
            $db->join->inner([
                tbl7::TABLE_NAME => [tbl7::ID => tbl3::ORDER_ID]
            ]),
            where: $db->where->equals([
                tbl3::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl7::SAFE_ID   => user::post(post_keys::SAFE_ID)
            ])." AND ".
            $db->where->not_like([
                tbl3::STATUS => order_products_status_types::CANCEL
            ]),
            group_by: tbl3::PRODUCT_ID,
            order_by: tbl3::PRODUCT_ID
        )->rows;

        foreach ($data as $data_){
            if(array_list::index_of($id, $data_["product_id"]) < 0) array_push($id, $data_["product_id"]);
        }

        $data_options = $db_->db_select(
            array(
                tbl3::PRODUCT_ID,
                tbl4::OPTION_ITEM_ID,
                $db->as_name($db->sum(tbl4::QTY),"qty"),
                $db->as_name($db->sum(tbl4::PRICE),"price"),
            ),
            tbl4::TABLE_NAME,
            $db->join->inner([
                tbl3::TABLE_NAME => [tbl3::ID => tbl4::ORDER_PRODUCT_ID],
                tbl7::TABLE_NAME => [tbl7::ID => tbl3::ORDER_ID]
            ]),
            $db->where->equals([
                tbl4::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl3::PRODUCT_ID => $id,
                tbl7::SAFE_ID   => user::post(post_keys::SAFE_ID)
            ])." AND ".
            $db->where->not_like([
                tbl3::STATUS => order_products_status_types::CANCEL
            ]),
            tbl3::PRODUCT_ID.",".tbl4::OPTION_ITEM_ID,
            tbl4::OPTION_ITEM_ID
        )->rows;

        foreach ($data_options as $data_){
            if(array_list::index_of($id_options, $data_["option_item_id"]) < 0) array_push($id_options, $data_["option_item_id"]);
        }

        return array(
            "data" => $data,

            "data_options" => $data_options,

            "names" => $db->db_select(
                array(
                    tbl5::ID,
                    tbl5::QUANTITY_ID,
                    tbl5::CODE,
                    $db->as_name(tbl5::NAME.$sessions->get->LANGUAGE_TAG, "name")
                ),
                tbl5::TABLE_NAME,
                where: $db->where->equals([
                    tbl5::ID => $id
                ])
            )->rows,

            "name_options" => $db->db_select(
                array(
                    tbl6::ID,
                    $db->as_name(tbl6::NAME.$sessions->get->LANGUAGE_TAG, "name")
                ),
                tbl6::TABLE_NAME,
                where: $db->where->equals([
                    tbl6::ID => $id_options
                ])
            )->rows
        );
    }

    private function get_cancel_products(db $db, db $db_, sessions $sessions) : array{
        $id = array();
        $id_options = array();

        $data = $db_->db_select(
            array(
                tbl3::PRODUCT_ID,
                $db->as_name($db->sum( tbl3::QUANTITY),"quantity"),
                $db->as_name($db->sum( tbl3::QTY),"qty"),
                $db->as_name($db->sum( tbl3::PRICE),"price"),
            ),
            tbl3::TABLE_NAME,
            $db->join->inner([
                tbl7::TABLE_NAME => [tbl7::ID => tbl3::ORDER_ID]
            ]),
            where: $db->where->equals([
                tbl3::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl3::STATUS    => order_products_status_types::CANCEL,
                tbl7::SAFE_ID   => user::post(post_keys::SAFE_ID)
            ]),
            group_by: tbl3::PRODUCT_ID,
            order_by: tbl3::PRODUCT_ID
        )->rows;

        foreach ($data as $data_){
            if(array_list::index_of($id, $data_["product_id"]) < 0) array_push($id, $data_["product_id"]);
        }

        $data_options = $db_->db_select(
            array(
                tbl3::PRODUCT_ID,
                tbl4::OPTION_ITEM_ID,
                $db->as_name($db->sum(tbl4::QTY),"qty"),
                $db->as_name($db->sum(tbl4::PRICE),"price"),
            ),
            tbl4::TABLE_NAME,
            $db->join->inner([
                tbl3::TABLE_NAME => [tbl3::ID => tbl4::ORDER_PRODUCT_ID],
                tbl7::TABLE_NAME => [tbl7::ID => tbl3::ORDER_ID]
            ]),
            $db->where->equals([
                tbl4::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl3::STATUS => order_products_status_types::CANCEL,
                tbl3::PRODUCT_ID => $id,
                tbl7::SAFE_ID   => user::post(post_keys::SAFE_ID)
            ]),
            tbl3::PRODUCT_ID.",".tbl4::OPTION_ITEM_ID,
            tbl4::OPTION_ITEM_ID
        )->rows;

        foreach ($data_options as $data_){
            if(array_list::index_of($id_options, $data_["option_item_id"]) < 0) array_push($id_options, $data_["option_item_id"]);
        }

        return array(
            "data" => $data,

            "data_options" => $data_options,

            "names" => $db->db_select(
                array(
                    tbl5::ID,
                    tbl5::QUANTITY_ID,
                    tbl5::CODE,
                    $db->as_name(tbl5::NAME.$sessions->get->LANGUAGE_TAG, "name")
                ),
                tbl5::TABLE_NAME,
                where: $db->where->equals([
                tbl5::ID => $id
            ])
            )->rows,

            "name_options" => $db->db_select(
                array(
                    tbl6::ID,
                    $db->as_name(tbl6::NAME.$sessions->get->LANGUAGE_TAG, "name")
                ),
                tbl6::TABLE_NAME,
                where: $db->where->equals([
                tbl6::ID => $id_options
            ])
            )->rows
        );
    }

    private function get_costs(db $db, db $db_, sessions $sessions) : array{
        return array(
            "data" => $db_->db_select(
                array(
                    $db->as_name($db->sum(tbl::PRICE),"price")
                ),tbl::TABLE_NAME,
                where: $db->where->equals([
                    tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                    tbl::SAFE_ID   => user::post(post_keys::SAFE_ID),
                    tbl::STATUS    => order_payment_status_types::COST,
                    tbl::IS_DELETE => 0
                ]),
            )->rows
        );
    }
}