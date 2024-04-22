<?php
namespace pos\sameparts\functions\samepart;
require "../../../../matrix_library/php/auto_loader.php";

use config\database_list;
use config\db;
use config\sessions;
use config\settings;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use sameparts\php\ajax\echo_values;
use sameparts\php\db_query\branch_users;
use config\table_helper\branch_users as tbl;

class post_keys{const PASSWORD = "password";}

$echo = new echo_values();
if(user::post(post_keys::PASSWORD)) {
    $sessions = new sessions();
    $db = new db(database_list::LIVE_MYSQL_1);

    variable::clear_all_data($_POST);
    if ($sessions->get->BRANCH_ID > 0){
        $result["login"] =  branch_users::get($db, $sessions->get->BRANCH_ID, user::post(post_keys::PASSWORD), custom_where: $db->where->equals([tbl::ACTIVE => 1]))->rows;
        if (count($result["login"]) > 0) {
            $result["info"] = branch_users::get_branch_info($db, $sessions->get->BRANCH_ID)->rows;
            $sessions->get->USER_ID = $result["login"][0]["id"];

            $permissions = json_decode($result["login"][0]["permissions"], true);
            $new_permission = array();
            foreach ($permissions as $value){
                $new_permission[$value] = true;
            }
            $sessions->get->PERMISSION = $new_permission;
            $sessions->get->USER_NAME = $result["login"][0]["name"];
            $sessions->create();
        }else{
            $echo->status= false;
            $echo->error_code = settings::error_codes()::NOT_FOUND;
        }
    }
} else{
    $echo->status= false;
    $echo->error_code = settings::error_codes()::INCORRECT_DATA;
}
$echo->return();
