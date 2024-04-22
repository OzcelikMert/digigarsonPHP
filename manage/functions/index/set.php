<?php
namespace manage\functions\index;
require "../../../matrix_library/php/auto_loader.php";

use config\database_list;
use config\db;
use config\table_helper\branch_info;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use config\sessions;
use sameparts\php\ajax\echo_values;
use sameparts\php\db_query\branch_users;
use config\table_helper\branch_manage_users as tbl;

class post_keys { const EMAIL_OR_PHONE = "email_phone", PASSWORD = "password"; }

if(user::check_sent_data([post_keys::EMAIL_OR_PHONE, post_keys::PASSWORD])) {

    $echo = new echo_values();
    $db = new db(database_list::LIVE_MYSQL_1);
    $sessions = new sessions();
    variable::clear_all_data($_POST);

    $user = branch_users::get_manage_users($db, user::post(post_keys::EMAIL_OR_PHONE), user::post(post_keys::PASSWORD), custom_where: $db->where->equals([tbl::ACTIVE => 1]), limit: [0, 1])->rows;
    if(count($user) > 0){
        $info = branch_users::get_branch_info($db, $user[0]["branch_id"])->rows;
        if(count($info) > 0){
            $sessions->get->USER_ID = $user[0]["id"];
            $sessions->get->USER_NAME = $user[0]["name"];
            $sessions->get->BRANCH_ID = $info[0]["id"];
            $sessions->get->BRANCH_ID_MAIN = $info[0]["id"];
            $sessions->get->BRANCH_MAIN_ID = $info[0]["main_id"];
            $sessions->get->LANGUAGE_ID = $user[0]["language_id"];
            $sessions->get->LANGUAGE_TAG = $user[0]["seo_url"];
            $sessions->get->CURRENCY = $info[0]["type"];
            $sessions->get->BRANCH_NAME = $info[0]["name"];
            $sessions->get->IS_MAIN = $info[0]["is_main"];
            $sessions->get->PERMISSION = $user[0]["permissions"];
            $data = get_branches($db, $sessions);
            $sessions->get->BRANCHES = $data["id_list"];
            $sessions->get->BRANCHES_NAMES = $data["rows"];
            $sessions->set->INTEGRATIONS($db);
            $sessions->create();
        }else $echo->status = false;
    }else{
        $echo->status = false;
    }

    $echo->return();
}

function get_branches(db $db, sessions $sessions): array{
    $id_list = array();
    $where = ($sessions->get->BRANCH_ID_MAIN == 3 && $sessions->get->PERMISSION == "*") ? "" : db::AND." (".$db->where->like([branch_info::MAIN_ID => $sessions->get->BRANCH_ID_MAIN])." or ".branch_info::ID. " = ".$sessions->get->BRANCH_ID_MAIN.")";
    $values = $db->db_select(
        [   branch_info::NAME,
            branch_info::ID
        ],
        branch_info::TABLE_NAME,
        where: $db->where->equals([
            branch_info::ACTIVE => 1
        ]).$where
    )->rows;

    foreach ($values as $value){
        array_push($id_list, $value["id"]);
    }
    array_push($id_list, $sessions->get->BRANCH_ID_MAIN);

    return array(
        "id_list" => $id_list,
        "rows" => $values
    );
}

/* end Functions */