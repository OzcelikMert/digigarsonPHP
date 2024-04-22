<?php
namespace pos\functions\finance\get;

use config\db;
use config\sessions;
use config\type_tables_values\order_payment_status_types;
use config\type_tables_values\order_products_status_types;
use matrix_library\php\db_helpers\results;
use sameparts\php\ajax\echo_values;
use config\table_helper\order_payments as tbl;
use config\table_helper\payment_types as tbl2;
use config\table_helper\order_products as tbl3;
use config\table_helper\order_product_options as tbl4;
use config\table_helper\products as tbl5;
use config\table_helper\product_option_items as tbl6;
use sameparts\php\db_query\payments;


class z_report {
    public function __construct(db $db, sessions $sessions, echo_values &$echo) {
        $echo->message = "Z REPORT";
        $echo->rows["payments"] = (array)$this->get($db,$sessions,$echo)->rows;
        $echo->rows["trust_payments"] = (array)$this->get_trust_payments($db,$sessions,$echo)->rows;
        $echo->rows["products"] = (array)$this->get_products($db,$sessions,$echo)->rows;
        $echo->rows["cancel_products"] = (array)$this->get_cancel_products($db,$sessions,$echo)->rows;
        $echo->rows["product_options"] = (array)$this->get_product_options($db,$sessions,$echo)->rows;
        $echo->rows["cancel_product_options"] = (array)$this->get_cancel_product_options($db,$sessions,$echo)->rows;
        $echo->rows["costs"] = (array)$this->get_costs($db,$sessions)->rows;
    }

    private function get(db $db, sessions $sessions, echo_values $echo) : results{
       return $db->db_select(
           array(
               tbl::TYPE,
               $db->as_name(tbl2::NAME.$sessions->get->LANGUAGE_TAG,"name"),
               $db->as_name($db->sum(tbl::PRICE),"price")
           ),tbl::TABLE_NAME,
           $db->join->inner([
               tbl2::TABLE_NAME => [tbl::TYPE => tbl2::ID]
           ]),
           $db->where->equals([
               tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
               tbl::STATUS    => [
                   order_payment_status_types::PAID,
                   order_payment_status_types::CANCEL
               ],
               tbl::IS_DELETE => 0
           ]). " AND ".$db->where->greater_than([tbl::ORDER_ID => 0 ]),
           tbl::TYPE,
           tbl::TYPE
       );

    }
    private function get_products(db $db, sessions $sessions, echo_values $echo) : results{
       return $db->db_select(
            array(
                tbl3::PRODUCT_ID,
                tbl5::QUANTITY_ID,
                $db->as_name($db->sum( tbl3::QUANTITY),"quantity"),
                $db->as_name($db->sum( tbl3::QTY),"qty"),
                $db->as_name($db->sum( tbl3::PRICE),"price"),
                $db->as_name(tbl5::NAME.$sessions->get->LANGUAGE_TAG,"name"),
            ),
            tbl3::TABLE_NAME,
            $db->join->inner([
                tbl5::TABLE_NAME => [tbl5::ID => tbl3::PRODUCT_ID]
            ]),
            $db->where->equals([
                tbl3::BRANCH_ID => $sessions->get->BRANCH_ID
            ])." AND ".
            $db->where->not_like([
                tbl3::STATUS => order_products_status_types::CANCEL
            ]),
            tbl3::PRODUCT_ID,
            tbl3::PRODUCT_ID
        );
    }
    private function get_cancel_products(db $db, sessions $sessions, echo_values $echo) : results{
        return $db->db_select(
            array(
                tbl3::PRODUCT_ID,
                tbl5::QUANTITY_ID,
                $db->as_name($db->sum( tbl3::QUANTITY),"quantity"),
                $db->as_name($db->sum( tbl3::QTY),"qty"),
                $db->as_name($db->sum( tbl3::PRICE),"price"),
                $db->as_name(tbl5::NAME.$sessions->get->LANGUAGE_TAG,"name"),
            ),
            tbl3::TABLE_NAME,
            $db->join->inner([
                tbl5::TABLE_NAME => [tbl5::ID => tbl3::PRODUCT_ID]
            ]),
            $db->where->equals([
                tbl3::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl3::STATUS => order_products_status_types::CANCEL
            ]),
            tbl3::PRODUCT_ID,
            tbl3::PRODUCT_ID
        );
    }
    private function get_product_options(db $db, sessions $sessions, echo_values $echo) : results{
       return $db->db_select(
            array(
                tbl3::PRODUCT_ID,
                tbl4::OPTION_ITEM_ID,
                $db->as_name(tbl6::NAME.$sessions->get->LANGUAGE_TAG,"name"),
                $db->as_name($db->sum(tbl4::QTY),"qty"),
                $db->as_name($db->sum(tbl4::PRICE),"price"),
            ),
            tbl3::TABLE_NAME,
            $db->join->inner([
                tbl4::TABLE_NAME => [tbl4::ORDER_PRODUCT_ID => tbl3::ID],
                tbl6::TABLE_NAME => [tbl6::ID => tbl4::OPTION_ITEM_ID]
            ]),
            $db->where->equals([
                tbl3::BRANCH_ID => $sessions->get->BRANCH_ID
            ])." AND ".
            $db->where->not_like([
                tbl3::STATUS => order_products_status_types::CANCEL
            ]),
            tbl3::PRODUCT_ID.",".tbl4::OPTION_ITEM_ID,
            tbl3::PRODUCT_ID
        );
    }
    private function get_cancel_product_options(db $db, sessions $sessions, echo_values $echo) : results{
        return $db->db_select(
            array(
                tbl3::PRODUCT_ID,
                tbl4::OPTION_ITEM_ID,
                $db->as_name(tbl6::NAME.$sessions->get->LANGUAGE_TAG,"name"),
                $db->as_name($db->sum(tbl4::QTY),"qty"),
                $db->as_name($db->sum(tbl4::PRICE),"price"),
            ),
            tbl3::TABLE_NAME,
            $db->join->inner([
                tbl4::TABLE_NAME => [tbl4::ORDER_PRODUCT_ID => tbl3::ID],
                tbl6::TABLE_NAME => [tbl6::ID => tbl4::OPTION_ITEM_ID]
            ]),
            $db->where->equals([
                tbl3::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl3::STATUS => order_products_status_types::CANCEL
            ]),
            tbl3::PRODUCT_ID.",".tbl4::OPTION_ITEM_ID,
            tbl3::PRODUCT_ID
        );
    }
    private function get_trust_payments(db $db, sessions $sessions, echo_values $echo) : results{
        return $db->db_select(
            array(
                tbl::TYPE,
                $db->as_name(tbl2::NAME.$sessions->get->LANGUAGE_TAG,"name"),
                $db->as_name($db->sum(tbl::PRICE),"price")
            ),tbl::TABLE_NAME,
            $db->join->inner([
                tbl2::TABLE_NAME => [tbl::TYPE => tbl2::ID]
            ]),
            $db->where->equals([
                tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl::ORDER_ID  => 0,
                tbl::STATUS    => [
                    order_payment_status_types::PAID
                ],
                tbl::IS_DELETE => 0
            ]),
            tbl::TYPE,
            tbl::TYPE
        );

    }

    private function get_costs(db $db, sessions $sessions) : results{
        return payments::get(
            $db,
            $sessions->get->LANGUAGE_TAG,
            $sessions->get->BRANCH_ID,
            status_id: order_payment_status_types::COST,
            order_by: $db->order_by(tbl::DATE, db::DESC)
        );
    }
}