<?php
require "../../../../matrix_library/php/auto_loader.php";

use config\db;
use config\sessions;
use config\sessions\check;
use sameparts\php\db_query\branch_tables;
use config\table_helper\branch_tables as tbl;
use config\table_helper\branch_sections as tbl2;
use config\table_helper\table_section_types as tbl3;
use matrix_library\php\operations\user;
use matrix_library\php\operations\clear_types;
use matrix_library\php\operations\variable;
use \sameparts\php\ajax\echo_values;

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

    if(($type == get_types::ALL || $type ==  get_types::TABLES)) $echo->rows["tables"] = (array)branch_tables::get($db, $sessions->get->BRANCH_ID, order_by:  $db->order_by_multi([
        $db->order_by($db->length(tbl::NO), db::ASC),
        $db->order_by(tbl::NO, db::ASC)
    ]));
    if(($type == get_types::ALL || $type ==  get_types::SECTIONS)) $echo->rows["sections"] = (array)branch_tables::get_sections($db, $sessions->get->BRANCH_ID, group_by: tbl2::ID.",".tbl2::SECTION_ID, order_by: tbl2::RANK." asc");
    if(($type == get_types::ALL || $type ==  get_types::SECTION_TYPES)) $echo->rows["section_types"] = (array)branch_tables::get_section_types($db, $sessions->get->LANGUAGE_TAG, order_by: tbl3::ID." desc");
}

class get_types {
    const ALL = 0x0001, TABLES = 0x0002, SECTIONS = 0x0003, SECTION_TYPES = 0x0004;
}