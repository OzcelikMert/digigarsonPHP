<?php
namespace manage\functions\list_device;
require "../../../matrix_library/php/auto_loader.php";

use config\database_list;
use config\db;
use manage\functions\list_device\set\device_control;
use config\sessions\check;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use config\sessions;
use sameparts\php\ajax\echo_values;

/* CONST Values */
class post_keys {
    const SET_TYPE = "set_type";
}

class set_types {
    const DEVICE_CONTROL = 0x0001;
}
/* end CONST Values */

if(user::check_sent_data([post_keys::SET_TYPE]) && check::check(false)) {
    $db = new db(database_list::LIVE_MYSQL_1);
    $echo = new echo_values();
    $sessions = new sessions();

    variable::clear_all_data($_POST);

    switch (user::post(post_keys::SET_TYPE)){
        case set_types::DEVICE_CONTROL:
            (new device_control($db, $sessions, $echo));
            break;
    }

    $echo->return();
}
/* end Functions */