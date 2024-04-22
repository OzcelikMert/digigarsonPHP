<?php
namespace _superadmin\functions\table_edit;

use _superadmin\functions\table_edit\get\branch;
use _superadmin\sameparts\functions\sessions\check;
use config\database_list;
use config\db;
use matrix_library\php\operations\method_types;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use _superadmin\sameparts\functions\sessions\get;
use _superadmin\functions\table_edit\get\table_section;
use _superadmin\functions\table_edit\get\getbranchlist;
use sameparts\php\ajax\echo_values;

require "../../../matrix_library/php/auto_loader.php";

class post_keys{
    const GET_TYPE = "get_type";
}

class get_types {
    const
        TABLE_SECTION  = 0x0001,
        GET_BRANCH_LIST = 0x0002,
        BRANCH = 0x0003;
}

if(user::check_sent_data([post_keys::GET_TYPE]) && check::check(false)){
    $db = new db(database_list::LIVE_MYSQL_1);
    $echo = new echo_values();
    $sessions = new get();

    $echo->custom_data["POST"] = $_POST;
    variable::clear_all_data($_POST);
    switch (user::post(post_keys::GET_TYPE))
    {
        case get_types::TABLE_SECTION:
            (new table_section($db, $sessions, $echo));
            break;
        case get_types::GET_BRANCH_LIST:
            (new getbranchlist($db, $sessions, $echo));
            break;
        case get_types::BRANCH:
            (new branch($db, $sessions, $echo));
            break;
    }
    $echo->return();
}