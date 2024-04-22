<?php
namespace pos\functions\settings;
require "../../../matrix_library/php/auto_loader.php";

use config\database_list;
use config\db;
use config\sessions;
use config\sessions\check;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use pos\functions\settings\set\change_password;
use sameparts\php\ajax\echo_values;

/* CONST Values */
class post_keys {
    const SET_TYPE = "set_type";
}

class set_types {
    const CHANGE_PASSWORD = 0x0001;
}
/* end CONST Values */

if(user::check_sent_data([post_keys::SET_TYPE]) && check::check(false)) {
    $db = new db(database_list::LIVE_MYSQL_1);
    $echo = new echo_values();
    $sessions = new sessions();

    variable::clear_all_data($_POST);


    switch (user::post(post_keys::SET_TYPE)){
        case set_types::CHANGE_PASSWORD:
            (new change_password($db, $sessions, $echo));
            break;
    }

    $echo->return();
}
/* end Functions */