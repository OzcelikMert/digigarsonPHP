<?php
namespace language\functions\index;
require "../../../matrix_library/php/auto_loader.php";

use config\database_list;
use config\db;
use language\functions\index\get\languages;
use matrix_library\php\operations\method_types;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use language\sameparts\functions\sessions\get;
use sameparts\php\ajax\echo_values;

/* CONST Values */
class post_keys {
    const GET_TYPE = "get_type";
}

class get_types {
    const LANGUAGES = 0x0001;
}
/* end CONST Values */

if(user::check_sent_data([post_keys::GET_TYPE]) && user::check_sent_data([get::USER_ID], method_type: method_types::SESSION)) {
    $db = new db(database_list::LIVE_MYSQL_1);
    $echo = new echo_values();
    $sessions = new get();

    variable::clear_all_data($_POST);

    switch (user::post(post_keys::GET_TYPE)){
        case get_types::LANGUAGES:
            (new languages($db, $sessions, $echo));
            break;
    }

    $echo->return();
}
/* end Functions */