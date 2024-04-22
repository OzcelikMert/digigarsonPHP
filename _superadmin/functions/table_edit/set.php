<?php
namespace _superadmin\functions\table_edit;
require "../../../matrix_library/php/auto_loader.php";

use _superadmin\functions\table_edit\set\add_branch_table;
use _superadmin\functions\table_edit\set\add_section;
use _superadmin\sameparts\functions\sessions\check;
use config\database_list;
use _superadmin\functions\table_edit\set\delete_branch_table;
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
   const DELETE = 0x0001,
   INSERT = 0x0002,
   ADD_SECTION = 0x0003;
}
if(user::check_sent_data([post_keys::SET_TYPE]) && check::check(false)) {
    $db = new db(database_list::LIVE_MYSQL_1);
    $echo = new echo_values();
    $sessions = new get();

    variable::clear_all_data($_POST);
    switch (user::post(post_keys::SET_TYPE))
    {
        case set_types::DELETE:
            (new delete_branch_table($db, $sessions, $echo));
            break;
        case set_types::INSERT:
            (new add_branch_table($db, $sessions, $echo));
            break;
        case set_types::ADD_SECTION:
            (new add_section($db, $sessions, $echo));
            break;
    }
    $echo->return();
}
/* end Functions */