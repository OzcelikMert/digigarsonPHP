<?php
namespace _superadmin\functions\product;
require "../../../matrix_library/php/auto_loader.php";

use _superadmin\functions\product\set\copy;
use _superadmin\sameparts\functions\sessions\check;
use config\database_list;
use config\db;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use _superadmin\sameparts\functions\sessions\get;
use sameparts\php\ajax\echo_values;

/* CONST Values */
class post_keys {
    const SET_TYPE = "set_type";
}

class set_types {
   const COPY = 0x0001;
}
if(user::check_sent_data([post_keys::SET_TYPE]) && check::check(false)) {
    $db = new db(database_list::LIVE_MYSQL_1);
    $echo = new echo_values();
    $sessions = new get();

    variable::clear_all_data($_POST);
    switch (user::post(post_keys::SET_TYPE))
    {
        case set_types::COPY:
            (new copy($db, $sessions, $echo));
            break;
    }
    $echo->return();
}
/* end Functions */