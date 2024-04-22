<?php
namespace manage\functions\catering;
require "../../../matrix_library/php/auto_loader.php";

use config\database_list;
use config\db;
use manage\functions\catering\set\question;
use manage\functions\catering\set\owner;
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
    const QUESTION = 0x0001,
        OWNER = 0x0002;
}
/* end CONST Values */

if(user::check_sent_data([post_keys::SET_TYPE]) && check::check(false)) {
    $db = new db(database_list::LIVE_MYSQL_1);
    $echo = new echo_values();
    $sessions = new sessions();

    variable::clear_all_data($_POST);

    switch (user::post(post_keys::SET_TYPE)){
        case set_types::OWNER:
            (new owner($db, $sessions, $echo));
            break;
        case set_types::QUESTION:
            (new question($db, $sessions, $echo));
            break;
    }

    $echo->return();
}
/* end Functions */