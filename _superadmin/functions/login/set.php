<?php
namespace _superadmin\functions\login;
require "../../../matrix_library/php/auto_loader.php";

use config\database_list;
use config\db;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use _superadmin\sameparts\functions\sessions\get;
use sameparts\php\ajax\echo_values;
use _superadmin\functions\login\set\login_check;

class post_keys{const  SET_TYPE = "set_type";}

class set_types{
    const
        LOGIN_CHECK = 0x0001;
}
if(user::check_sent_data([post_keys::SET_TYPE])) {

    $db = new db(database_list::LIVE_MYSQL_1);
    $echo = new echo_values();
    $sessions = new get();

    variable::clear_all_data($_POST);

    switch (user::post(post_keys::SET_TYPE)){
        case set_types::LOGIN_CHECK:
            (new login_check($db, $sessions, $echo));
            break;
    }

    $echo->return();
}

/* end Functions */