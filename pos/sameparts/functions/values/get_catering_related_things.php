<?php
namespace pos\sameparts\functions;
require "../../../../matrix_library/php/auto_loader.php";

use config\db;
use config\sessions;
use config\sessions\check;
use sameparts\php\db_query\notification;
use sameparts\php\db_query\orders;
use config\table_helper\catering_owners as tbl;
use config\table_helper\catering_questions as tbl2;
use config\table_helper\caterings as tbl3;
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

    if(user::post(post_keys::PAGE_NAME) == page_names::POS()::FINANCE){
        if(
            $type == get_types::ALL ||
            $type ==  get_types::CATERINGS
        ) $echo->rows["caterings"] = (array)orders::get_caterings($db, $sessions->get->BRANCH_ID, order_by: $db->order_by(tbl3::ID, $db::DESC));
    }

    if(
        $type ==  get_types::ALL ||
        $type ==  get_types::CATERING_OWNERS
    ) $echo->rows["catering_owners"] = (array)orders::get_catering_owners($db, $sessions->get->BRANCH_ID, order_by: $db->order_by(tbl::ID, $db::DESC));
    if(
        $type ==  get_types::ALL ||
        $type ==  get_types::CATERING_QUESTIONS
    ) $echo->rows["catering_questions"] = (array)orders::get_catering_questions($db, $sessions->get->BRANCH_ID, order_by: $db->order_by(tbl2::ID, $db::DESC));
}


class get_types {
    const ALL = 0x0001, CATERING_OWNERS = 0x0002, CATERING_QUESTIONS = 0x0003, CATERINGS = 0x0004;
}