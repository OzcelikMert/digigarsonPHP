<?php
namespace pos\sameparts\functions;
require "../../../../matrix_library/php/auto_loader.php";

use config\db;
use config\sessions;
use config\sessions\check;
use config\type_tables_values\order_products_status_types;
use sameparts\php\db_query\orders;
use config\table_helper\orders as tbl;
use config\table_helper\order_products as tbl2;
use config\table_helper\order_types as tbl3;
use config\table_helper\order_status_types as tbl4;
use config\table_helper\order_product_options as tbl6;
use matrix_library\php\operations\user;
use matrix_library\php\operations\clear_types;
use matrix_library\php\operations\variable;
use \sameparts\php\ajax\echo_values;
use sameparts\php\helper\page_names;

/* CONST Values */
class post_keys {
    const PAGE_NAME = "page_name",
        GET_TYPE = "get_type";
}
/* end Const Values */

if(
    check::check(false) &&
    user::check_sent_data(
        array(
            post_keys::PAGE_NAME,
            post_keys::GET_TYPE
        )
    )
){
    $db = new db(\config\database_list::LIVE_MYSQL_1);
    $sessions = new sessions();
    $echo = new echo_values();
    set_echo_values($db, $sessions, $echo);
    $echo->return();
}

function set_echo_values(db $db, sessions $sessions, echo_values &$echo) : void{
    $type = variable::clear_method(post_keys::GET_TYPE, clear_types::INT);

    $where = "";
    $where_order_product = "";
    if(user::post(post_keys::PAGE_NAME) != page_names::POS()::FINANCE){
        /*$where = $db->where->not_like([
            $db->if_null(tbl5::STATUS, 0) => order_payment_status_types::PAID,
            [$db->if_null(tbl5::STATUS, 0) => order_payment_status_types::CANCEL]
        ]);*/
        $where = $db->where->equals([tbl::DATE_END => "",tbl::IS_CONFIRM => [0,1]]);
        $where_order_product = $db->where->not_like([
            $db->if_null(tbl2::STATUS, 0) => order_products_status_types::CANCEL
        ]);
    }

    $where_order_product_sub = "";
    if($type == get_types::ORDER_PRODUCTS_PRINTED || $type == get_types::ALL_WITH_ORDER_PRODUCTS_PRINTED){
        $where_order_product_sub = $db->where->equals([tbl2::PRINT => 1]);
    }else if(($type == get_types::ORDER_PRODUCTS_NOT_PRINTED || $type == get_types::ALL_WITH_ORDER_PRODUCTS_NOT_PRINTED)){
        $where_order_product_sub = $db->where->equals([tbl2::PRINT => 0]);
    }

    $where_order_product .= (strlen($where_order_product_sub) > 0) ? " AND ".$where_order_product_sub : "";

    if(
        $type ==  get_types::ALL ||
        $type ==  get_types::ORDERS ||
        $type ==  get_types::ALL_WITH_ORDER_PRODUCTS_PRINTED ||
        $type ==  get_types::ALL_WITH_ORDER_PRODUCTS_NOT_PRINTED ||
        $type ==  get_types::ORDER_AND_ORDER_PRODUCTS
    ) $echo->rows["orders"] = (array)orders::get(
        $db,
        $sessions->get->BRANCH_ID,
        custom_where: $where,
        order_by: tbl::TABLE_ID.",".tbl::ID." desc");

    if(
        $type ==  get_types::ALL ||
        $type ==  get_types::ORDER_PRODUCTS ||
        $type ==  get_types::ORDER_PRODUCTS_NOT_PRINTED ||
        $type ==  get_types::ORDER_PRODUCTS_PRINTED ||
        $type ==  get_types::ALL_WITH_ORDER_PRODUCTS_PRINTED ||
        $type ==  get_types::ALL_WITH_ORDER_PRODUCTS_NOT_PRINTED ||
        $type ==  get_types::ORDER_AND_ORDER_PRODUCTS
    ) $echo->rows["order_products"] = (array)orders::get_products(
        $db,
        $sessions->get->LANGUAGE_TAG,
        $sessions->get->BRANCH_ID,
        custom_where: $where_order_product.((strlen($where) > 0 && strlen($where_order_product) > 0) ? " AND " : "").$where,
        group_by: tbl2::ID.",".tbl2::ORDER_ID.",".tbl2::PRODUCT_ID,
        order_by: tbl2::ID." asc");

    if(
        $type ==  get_types::ALL ||
        $type ==  get_types::ORDER_PRODUCT_OPTIONS ||
        $type ==  get_types::ORDER_PRODUCTS ||
        $type ==  get_types::ORDER_PRODUCTS_NOT_PRINTED ||
        $type ==  get_types::ORDER_PRODUCTS_PRINTED ||
        $type ==  get_types::ALL_WITH_ORDER_PRODUCTS_PRINTED ||
        $type ==  get_types::ALL_WITH_ORDER_PRODUCTS_NOT_PRINTED ||
        $type ==  get_types::ORDER_AND_ORDER_PRODUCTS
    ) $echo->rows["order_product_options"] = (array)orders::get_product_options(
        $db,
        $sessions->get->BRANCH_ID,
        order_by: $db->order_by(tbl6::ID, $db::ASC));

    if(
        $type == get_types::ALL ||
        $type ==  get_types::ORDER_TYPES ||
        $type ==  get_types::ALL_WITH_ORDER_PRODUCTS_PRINTED ||
        $type ==  get_types::ALL_WITH_ORDER_PRODUCTS_NOT_PRINTED
    ) $echo->rows["order_types"] = (array)orders::get_types($db, $sessions->get->LANGUAGE_TAG, order_by: tbl3::ID." desc");

    if(
        $type == get_types::ALL ||
        $type ==  get_types::ORDER_STATUS_TYPES ||
        $type ==  get_types::ALL_WITH_ORDER_PRODUCTS_PRINTED ||
        $type ==  get_types::ALL_WITH_ORDER_PRODUCTS_NOT_PRINTED
    ) $echo->rows["order_status_types"] = (array)orders::order_status_types($db, $sessions->get->LANGUAGE_TAG, order_by: tbl4::ID." desc");


}

class get_types {
    const ALL = 0x0001,
        ORDERS = 0x0002,
        ORDER_PRODUCTS = 0x0003,
        ORDER_TYPES = 0x0004,
        ORDER_STATUS_TYPES = 0x0005,
        ORDER_PRODUCTS_NOT_PRINTED = 0x0006,
        ORDER_PRODUCTS_PRINTED = 0x0007,
        ALL_WITH_ORDER_PRODUCTS_NOT_PRINTED = 0x0008,
        ALL_WITH_ORDER_PRODUCTS_PRINTED = 0x0009,
        ORDER_AND_ORDER_PRODUCTS = 0x0010,
        ORDER_PRODUCT_OPTIONS = 0x0011;
}