<?php
namespace language\functions\index;
require "../../../matrix_library/php/auto_loader.php";

use config\database_list;
use config\db;
use language\functions\index\set\delete;
use language\functions\index\set\insert;
use language\functions\index\set\update;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use language\sameparts\functions\sessions\get;
use sameparts\php\ajax\echo_values;

/* CONST Values */
class post_keys {
    const SET_TYPE = "set_type";
}

class set_types {
    const INSERT = 0x0001,
        UPDATE = 0x0002,
        DELETE = 0x0003;
}
/* end CONST Values */

if(user::check_sent_data([post_keys::SET_TYPE])) {
    $db = new db(database_list::LIVE_MYSQL_1);
    $echo = new echo_values();
    $sessions = new get();

    variable::clear_all_data($_POST);

    switch (user::post(post_keys::SET_TYPE)){
        case set_types::INSERT:
            (new insert($db, $sessions, $echo));
            break;
        case set_types::UPDATE:
            (new update($db, $sessions, $echo));
            break;
        case set_types::DELETE:
            (new delete($db, $sessions, $echo));
            break;
    }

    $echo->return();
}
/* end Functions */