<?php
namespace manage\functions\list_device;
require "../../../matrix_library/php/auto_loader.php";

use config\database_list;
use config\db;
use manage\functions\list_device\get\devices;
use manage\functions\list_device\get\types;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use manage\sameparts\functions\sessions\get;
use sameparts\php\ajax\echo_values;
use waiter_terminal\sameparts\functions\sessions\check;

/* CONST Values */
class post_keys {
    const GET_TYPE = "get_type";
}

class get_types {
    const DEVICES = 0x0001, TYPES = 0x0002;
}
/* end CONST Values */

if(user::check_sent_data([post_keys::GET_TYPE]) && check::check(false)) {
    $db = new db(database_list::LIVE_MYSQL_1);
    $echo = new echo_values();
    $sessions = new get();

    variable::clear_all_data($_POST);

    switch (user::post(post_keys::GET_TYPE)){
        case get_types::DEVICES:
            (new devices($db, $sessions, $echo));
            break;
        case get_types::TYPES:
            (new types($db, $sessions, $echo));
            break;
    }

    $echo->return();
}
/* end Functions */