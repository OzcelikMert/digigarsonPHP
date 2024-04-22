<?php
namespace pos\sameparts\functions\samepart;
require "../../../../matrix_library/php/auto_loader.php";

use config\db;
use config\sessions;
use config\settings;
use config\database_list;
use config\table_helper\send_notification as tbl2;
use matrix_library\php\db_helpers\results;
use sameparts\php\ajax\echo_values;
use matrix_library\php\operations\user;
use sameparts\php\db_query\notification;
use matrix_library\php\operations\variable;


class post_keys{const ID = "id";}

$echo = new echo_values();
$sessions = new sessions();

if(user::post(post_keys::ID) && $sessions->get->BRANCH_ID > 0) {
    variable::clear_all_data($_POST);
    $db = new db(database_list::LIVE_MYSQL_1);
    $echo->custom_data["result"] = set_read(
        $db,
        $sessions->get->BRANCH_ID,
        $sessions->get->USER_ID,
        (int)user::post(post_keys::ID)
    );
} else{
    $echo->status = false;
    $echo->error_code = settings::error_codes()::INCORRECT_DATA;
}
$echo->return();


function set_read(db $db, int $branch_id, int $user_id,int $id, int $is_read = 1) : results{
    return $db->db_update(
        tbl2::TABLE_NAME,
        [tbl2::IS_READ => $is_read, tbl2::USER_ID => $user_id,],
        where: $db->where->equals([
            tbl2::BRANCH_ID => $branch_id,
            tbl2::ID => $id,
        ])
    );
}