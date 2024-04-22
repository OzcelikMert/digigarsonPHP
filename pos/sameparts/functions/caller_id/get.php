<?php
namespace pos\sameparts\functions\caller_id;

require "../../../../matrix_library/php/auto_loader.php";

use config\database_list;
use config\db;
use config\sessions;
use config\sessions\check;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use pos\sameparts\functions\caller_id\get\caller;
use pos\sameparts\functions\caller_id\get\address;
use sameparts\php\ajax\echo_values;
/* CONST Values */
class post_keys {
    const GET_TYPE = "get_type";
}

class get_types {
    const CALLER = 0x0001,
     GET_ADDRESS = 0x0003;
}
/* end CONST Values */

if(user::check_sent_data([post_keys::GET_TYPE]) && check::check(false)) {
    $echo = new echo_values();
    $db = new db(database_list::LIVE_MYSQL_1);
    $sessions = new sessions();

    variable::clear_all_data($_POST);

    switch (user::post(post_keys::GET_TYPE)){
        case get_types::CALLER:
            (new caller($db, $sessions, $echo));
        break;

        case get_types::GET_ADDRESS:
            new address($db,$sessions, $echo);
        break;
    }

    $echo->return();
}
/* end Functions */