<?php
require "../../../../matrix_library/php/auto_loader.php";

use config\db;
use config\sessions;
use config\sessions\check;
use sameparts\php\db_query\products;
use config\table_helper\products as tbl;
use config\table_helper\quantity_types as tbl2;
use config\table_helper\product_categories as tbl3;
use config\table_helper\product_option as tbl4;
use config\table_helper\product_option_items as tbl5;
use matrix_library\php\operations\user;
use matrix_library\php\operations\clear_types;
use matrix_library\php\operations\variable;
use \sameparts\php\ajax\echo_values;
use sameparts\php\helper\page_names\pos;

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
    $echo->rows["branch_id"] = $sessions->get->BRANCH_ID;
    $echo->rows["currency"] = $sessions->get->CURRENCY;
    $echo->rows["permissions"] = $sessions->get->PERMISSION;
    $echo->rows["caller_id_active"] = $sessions->get->CALLER_ID_ACTIVE;
    $echo->return();
}

function set_echo_values(db $db, sessions $sessions, echo_values &$echo) : void{
    $type = variable::clear_method(post_keys::GET_TYPE, clear_types::INT);

    if(($type == get_types::ALL || $type ==  get_types::PRODUCT))        $echo->rows["products"] = (array)products::get($db, $sessions->get->LANGUAGE_TAG, $sessions->get->BRANCH_ID, order_by: $db->order_by($db->case->greater_than([tbl::RANK => 0], tbl::RANK, tbl::NAME.$sessions->get->LANGUAGE_TAG), db::ASC));
    if(($type == get_types::ALL || $type ==  get_types::QUANTITY_TYPES)) $echo->rows["quantity_types"] = (array)products::get_quantity_types($db, $sessions->get->LANGUAGE_TAG, order_by: tbl2::ID." ".db::ASC);
    if(($type == get_types::ALL || $type ==  get_types::CATEGORY))       $echo->rows["categories"] = (array)products::get_categories($db, $sessions->get->LANGUAGE_TAG, $sessions->get->BRANCH_ID, group_by: tbl3::ID.",".tbl3::MAIN_ID, order_by: $db->order_by($db->case->greater_than([tbl3::RANK => 0], tbl3::RANK, tbl3::NAME.$sessions->get->LANGUAGE_TAG), db::ASC));
    if(($type == get_types::ALL || $type ==  get_types::OPTIONS))        $echo->rows["options"] = (array)products::get_options($db, $sessions->get->LANGUAGE_TAG, $sessions->get->BRANCH_ID, ((user::post(post_keys::PAGE_NAME) != pos::FINANCE) ? $db->where->not_like([tbl4::IS_DELETED => 1]) : ""));
    if(($type == get_types::ALL || $type ==  get_types::OPTIONS))        $echo->rows["option_items"] = (array)products::get_options_items($db, $sessions->get->LANGUAGE_TAG, $sessions->get->BRANCH_ID, ((user::post(post_keys::PAGE_NAME) != pos::FINANCE) ? $db->where->not_like([tbl5::IS_DELETED => 1]) : ""));
    if(($type == get_types::ALL || $type ==  get_types::OPTIONS))        $echo->rows["option_types"] = (array)products::get_option_types($db, $sessions->get->LANGUAGE_TAG, $sessions->get->BRANCH_ID);
    if(($type == get_types::ALL || $type ==  get_types::PRODUCT || $type ==  get_types::LINKED_OPTIONS)) $echo->rows["linked_options"] = (array)products::get_linked_options($db,$sessions->get->BRANCH_ID);
}

class get_types {
    const ALL = 0x0001, PRODUCT = 0x0002, CATEGORY = 0x0003, QUANTITY_TYPES = 0x0004, OPTIONS = 0x0005, LINKED_OPTIONS = 0x0006;
}


