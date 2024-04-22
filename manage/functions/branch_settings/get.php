<?php
namespace manage\functions\branch_settings;
require "../../../matrix_library/php/auto_loader.php";

use config\database_list;
use config\db;
use manage\functions\branch_settings\get\address;
use config\sessions\check;
use manage\functions\branch_settings\get\surveys;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use config\sessions;
use sameparts\php\ajax\echo_values;

/* CONST Values */
class post_keys {
    const GET_TYPE = "get_type";
}
class types {
    const
        GET_ADDRESS = 1,
        SURVEYS = 2;
}

/* end CONST Values */
$echo = new echo_values();
//$echo->custom_data["POST"] = $_POST;

if(user::check_sent_data([post_keys::GET_TYPE]) && check::check(false)) {
    $db = new db(database_list::LIVE_MYSQL_1);
    $sessions = new sessions();
    variable::clear_all_data($_POST);

    switch(user::post(post_keys::GET_TYPE)){
        case types::GET_ADDRESS: (new address($db, $sessions, $echo)); break;
        case types::SURVEYS:     (new surveys($db, $sessions, $echo)); break;

    }



}
$echo->return();

/* end Functions */