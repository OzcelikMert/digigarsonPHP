<?php
namespace waiter_terminal\functions\index;
require "../../../matrix_library/php/auto_loader.php";

use config\database_list;
use config\db;
use config\settings;
use config\type_tables_values\device_types;
use config\table_helper\branch_devices as tbl;
use config\table_helper\branch_users as tbl2;
use config\table_helper\branch_info as tbl3;
use config\table_helper\currency_types as tbl4;
use config\table_helper\language_types as tbl5;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use sameparts\php\db_query\branch_users;
use waiter_terminal\sameparts\functions\sessions\get;
use sameparts\php\ajax\echo_values;

class post_keys { const ID = "id", PASSWORD = "password", TOKEN = "token", SECURITY_CODE = "security_code"; }

if(user::check_sent_data([
    post_keys::SECURITY_CODE
])) {
    $db = new db(database_list::LIVE_MYSQL_1);
    $echo = new echo_values();
    $sessions = new get();

    variable::clear_all_data($_POST);

    check_values($echo);

    if($echo->status){
        $where = (user::post(post_keys::ID) > 0)
            ? [tbl::IS_CONNECT => [0, 1]]
            : [tbl::IS_CONNECT => 0];
        $device = branch_users::get_branch_devices(
            $db,
            device_types::WAITER_TERMINAL,
            $db->where->equals(array_merge([
                tbl::SECURITY_CODE => user::post(post_keys::SECURITY_CODE),
            ], $where)),
            limit: [0, 1]
        )->rows;
        if(count($device) > 0){
            $where = (user::post(post_keys::ID) > 0)
                ? [tbl2::ID => user::post(post_keys::ID)]
                : [tbl2::PASSWORD => user::post(post_keys::PASSWORD)];
            $user =  branch_users::get(
                $db,
                $device[0]["branch_id"],
                null,
                custom_where: $db->where->equals(array_merge([
                    tbl2::ACTIVE => 1
                ], $where)),
                limit: [0, 1]
            )->rows;
            if(count($user) > 0){
                $branch = branch_users::get_branch_info($db, $device[0]["branch_id"], limit: [0, 1])->rows;
                if(count($branch) > 0){
                    $sessions->TOKEN = true;
                    $sessions->BRANCH_ID = $branch[0]["id"];
                    $sessions->LANGUAGE_TAG = $branch[0]["seo_url"];
                    $sessions->CURRENCY = $branch[0]["type"];
                    $sessions->BRANCH_NAME = $branch[0]["name"];
                    $sessions->USER_ID = $user[0]["id"];
                    $sessions->USER_NAME = $user[0]["name"];
                    $sessions->DEVICE_ID = $device[0]["id"];
                    $permissions = json_decode($user[0]["permissions"], true);
                    foreach ($permissions as $value){
                        $sessions->PERMISSION[$value] = true;
                    }
                    $sessions->create();
                    $echo->rows["user_id"] = $sessions->USER_ID;
                    update($db);
                }else {
                    $echo->status = false;
                    $echo->error_code = settings::error_codes()::WRONG_VALUE;
                }
            }else{
                $echo->status = false;
                $echo->error_code = settings::error_codes()::WRONG_VALUE;
            }
        }else{
            $echo->status = false;
            $echo->error_code = settings::error_codes()::WRONG_VALUE;
        }
    }

    $echo->return();
}

function update(db $db) : results{
    return $db->db_update(
        tbl::TABLE_NAME,
        array(
            tbl::IS_CONNECT => 1,
            tbl::TOKEN => user::post(post_keys::TOKEN)
        ),
        where: $db->where->equals([
            tbl::SECURITY_CODE => user::post(post_keys::SECURITY_CODE),
            tbl::TYPE => device_types::WAITER_TERMINAL,
            tbl::IS_CONNECT => 0,
        ])
    );
}

function check_values(echo_values &$echo){
    if(variable::is_empty(
        user::post(post_keys::SECURITY_CODE)
    )){
        $echo->error_code = settings::error_codes()::EMPTY_VALUE;
    }

    if(user::post(post_keys::ID) < 1 && empty(user::post(post_keys::PASSWORD))){
        $echo->error_code = settings::error_codes()::EMPTY_VALUE;
    }

    if($echo->error_code != settings::error_codes()::SUCCESS) $echo->status = false;
}

?>
