<?php
require "../../../../matrix_library/php/auto_loader.php";

use config\db;
use config\database_list;
use config\sessions;
use config\sessions\check;

use matrix_library\php\operations\clear_types;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use sameparts\php\ajax\echo_values;
use sameparts\php\db_query\surveys;

/* CONST Values */
class post_keys {
    const PAGE_NAME = "page_name",
        GET_TYPE = "get_type";
}
/* end Const Values */
$echo = new echo_values();

$echo->custom_data["post"] = $_POST;

if(check::check(false) && user::check_sent_data(array(post_keys::GET_TYPE))){
    $db = new db(database_list::LIVE_MYSQL_1);
    $sessions = new sessions();
    set_echo_values($db, $sessions, $echo);
}
$echo->return();

function set_echo_values(db $db, sessions $sessions, echo_values &$echo) : void{
    $type = variable::clear_method(post_keys::GET_TYPE, clear_types::INT);
    if(($type == get_types::ALL || $type ==  get_types::TYPES)) $echo->rows["survey_types"] = surveys::get_types($db,$sessions->get->LANGUAGE_TAG);
}

class get_types {
    const ALL = 0x0001, TYPES = 0x0002, CUSTOMER = 0x0003, BRANCH = 0x0004;
}