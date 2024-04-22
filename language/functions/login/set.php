<?php
namespace language\functions\index;
require "../../../matrix_library/php/auto_loader.php";

use config\database_list;
use config\db;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use language\sameparts\functions\sessions\get;
use sameparts\php\ajax\echo_values;

class post_keys { const PASSWORD = "password", USER_NAME = "user_name"; }


if(user::check_sent_data([post_keys::USER_NAME, post_keys::PASSWORD])) {
    $echo = new echo_values();
    variable::clear_all_data($_POST);
    $sessions = new get();

    if (user::post(post_keys::USER_NAME) == "admin" && user::post(post_keys::PASSWORD) == "A*8517.cD"){
        $sessions->USER_ID = 1;
        $sessions->create();
    }else {
        $echo->status = false;
    }

    $echo->return();

}

/* end Functions */