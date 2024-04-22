<?php
namespace _superadmin\functions\branch;


use _superadmin\functions\branch\get\delete_orders;
use _superadmin\sameparts\functions\sessions\check;
use config\database_list;
use config\db;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use _superadmin\sameparts\functions\sessions\get;
use sameparts\php\ajax\echo_values;
use _superadmin\functions\branch\get\branch_info;
use _superadmin\functions\branch\get\branch_id;
use _superadmin\functions\branch\get\details;

require "../../../matrix_library/php/auto_loader.php";

class post_keys{
    const GET_TYPE = "get_type";
}

class get_types {
    const
    BRANCH_INFO = 0x0001,
    GET_DETAILS = 0x0002,
    DELETE_ORDERS = 0x0003;
}

$echo = new echo_values();
$echo->custom_data["post"] = $_POST;
if(user::check_sent_data([post_keys::GET_TYPE]) && check::check(false)) {

    $db = new db(database_list::LIVE_MYSQL_1);
    $sessions = new get();

    variable::clear_all_data($_POST);

    switch(user::post(post_keys::GET_TYPE)){
        case get_types::BRANCH_INFO:
            (new branch_info($db, $sessions, $echo));
            break;
        case get_types::GET_DETAILS:
            (new details($db, $sessions, $echo));
            break;
        case get_types::DELETE_ORDERS:
            (new delete_orders($db, $sessions, $echo));
            break;

    }

}
$echo->return();
