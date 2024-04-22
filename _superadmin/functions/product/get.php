<?php
namespace _superadmin\functions\table_edit;

use _superadmin\functions\product\get\branch_info;
use _superadmin\functions\manage_branch\get\function_type;
use _superadmin\functions\product\get\branch_id;
use _superadmin\functions\product\get\branch_products;
use _superadmin\functions\product\get\product_info;
use _superadmin\sameparts\functions\sessions\check;
use config\database_list;
use config\db;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use _superadmin\sameparts\functions\sessions\get;
use sameparts\php\ajax\echo_values;

require "../../../matrix_library/php/auto_loader.php";

class post_keys{
    const GET_TYPE = "get_type", BRANCH_ID = "branch_id";
}

class get_types {
    const PRODUCTS = 1, BRANCH_ID = 2, BRANCH = 3;
}

if(user::check_sent_data([post_keys::GET_TYPE]) && check::check(false)){

    $db = new db(database_list::LIVE_MYSQL_1);
    $echo = new echo_values();
    $sessions = new get();

    $echo->custom_data["post"] = $_POST;

    variable::clear_all_data($_POST);
    switch (user::post(post_keys::GET_TYPE))
    {
        case get_types::PRODUCTS:
            (new product_info($db, $sessions, $echo));
            break;
        case get_types::BRANCH_ID:
            (new branch_id($db, $sessions, $echo));
            break;
        case get_types::BRANCH:
            (new branch_info($db, $sessions, $echo));
            break;
    }
    $echo->return();
}