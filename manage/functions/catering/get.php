<?php
namespace manage\functions\catering;
require "../../../matrix_library/php/auto_loader.php";

use config\database_list;
use config\db;
use manage\functions\catering\get\owner;
use manage\functions\catering\get\question;
use config\sessions\check;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use config\sessions;
use sameparts\php\ajax\echo_values;

/* CONST Values */
class post_keys {
    const GET_TYPE = "get_type";
}

class get_types {
    const QUESTION = 0x0001,
        OWNER = 0x0002;
}
/* end CONST Values */

if(user::check_sent_data([post_keys::GET_TYPE]) && check::check(false)) {
    $db = new db(database_list::LIVE_MYSQL_1);
    $echo = new echo_values();
    $sessions = new sessions();

    variable::clear_all_data($_POST);

    switch (user::post(post_keys::GET_TYPE)){
        case get_types::OWNER:
            (new owner($db, $sessions, $echo));
            break;
        case get_types::QUESTION:
            (new question($db, $sessions, $echo));
            break;
    }

    $echo->return();
}
/* end Functions */