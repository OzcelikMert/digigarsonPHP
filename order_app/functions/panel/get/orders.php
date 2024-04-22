<?php
namespace order_app\functions\panel\get;

use config\db;
use config\table_helper\order_products as tbl2;
use config\table_helper\orders as tbl;
use config\table_helper\products as tbl3;
use config\table_helper\branch_info as tbl4;
use config\type_tables_values\account_types;
use matrix_library\php\db_helpers\results;
use order_app\sameparts\functions\sessions\get;
use sameparts\php\ajax\echo_values;


class orders{
    function __construct(db $db,get $session,echo_values $echo){
        $echo->rows = (array) $this->get_orders_for_user($db,$session,$echo)->rows;
    }

    public function get_orders_for_user(db $db, get $session, echo_values $echo): results{
        return $db->db_select(
            array(
                $db->as_name(tbl4::NAME ,"branch_name"),
                tbl::ID,
                tbl::DATE_START,
                tbl2::PRODUCT_ID,
                tbl2::QTY,
                tbl2::PRICE,
                $db->as_name(  tbl3::NAME."tr","name")
            ),tbl::TABLE_NAME,
            $db->join->inner(array(
                tbl2::TABLE_NAME => [tbl2::ORDER_ID => tbl::ID],
                tbl3::TABLE_NAME => [tbl3::ID => tbl2::PRODUCT_ID],
                tbl4::TABLE_NAME => [tbl4::ID => tbl2::BRANCH_ID],
            )),
            $db->where->equals([tbl2::ACCOUNT_TYPE => account_types::CUSTOMER, tbl2::ACCOUNT_ID => $session->USER_ID]),
            "",
            $db->order_by(tbl::ID,db::DESC)
        );
    }
}
