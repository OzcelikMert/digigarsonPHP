<?php
namespace _superadmin\functions\branch;
require '../../../matrix_library/php/auto_loader.php';

use _superadmin\functions\branch\set\branch_del;
use _superadmin\functions\branch\set\branch_edit;
use _superadmin\functions\branch\set\delete_orders;
use _superadmin\functions\branch\set\logo;
use _superadmin\functions\branch\set\user_edit;
use _superadmin\functions\branch\set\branch;
use _superadmin\functions\branch\set\branch_add;
use _superadmin\sameparts\functions\sessions\check;
use _superadmin\sameparts\functions\sessions\get;
use config\database_list;
use config\db;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use sameparts\php\ajax\echo_values;

class post_keys{
    const SET_TYPE = "set_type";
}

class set_types {
    const  BRANCH = 0x0001,
        BRANCH_ADD = 0x0002,
        USER_EDIT = 0x0003,
        BRANCH_EDIT = 0x0004,
        DELETE_ORDERS = 0x0005,
        BRANCH_DELETE = 0x0006,
        LOGO = 0x0007;
}

if(user::check_sent_data([post_keys::SET_TYPE]) && check::check(false)){

    $db = new db(database_list::LIVE_MYSQL_1);
    $echo = new echo_values();
    $sessions = new get();

    $echo->custom_data["post"] = $_POST;

    variable::clear_all_data($_POST);

    switch (user::post(post_keys::SET_TYPE)){
        case set_types::BRANCH_ADD:
            (new branch_add($db, $sessions, $echo));
            break;
        case set_types::USER_EDIT:
            (new user_edit($db, $sessions, $echo));
            break;
        case set_types::BRANCH_EDIT:
            (new branch_edit($db, $sessions, $echo));
            break;
        case set_types::DELETE_ORDERS:
            (new delete_orders($db, $sessions, $echo));
            break;
        case set_types::BRANCH_DELETE:
            (new branch_del($db, $sessions, $echo));
            break;
        case set_types::LOGO:
            (new logo($db, $sessions, $echo));
            break;
    }

    $echo->return();

}