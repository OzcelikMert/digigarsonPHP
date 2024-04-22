<?php
namespace pos\functions\index;
require "../../../matrix_library/php/auto_loader.php";

use config\database_list;
use config\db;
use config\type_tables_values\device_types;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
//use pos\sameparts\functions\sessions\get;
use config\sessions;
use sameparts\php\ajax\echo_values;
use sameparts\php\db_query\branch_users;
use config\table_helper\branch_users as tbl;
use config\table_helper\branch_devices as tbl2;

class post_keys { const PASSWORD = "password", TOKEN = "token", SECURITY_CODE = "security_code",TYPE = "type"; }
class get_types { const SECURITY_CODE = 0x0001, TOKEN = 0x0002, PASSWORD = 0x0003,UN_TOKEN = 0X0004; }

if(user::check_sent_data([post_keys::TYPE])) {
    variable::clear_all_data($_POST);

    $echo = new echo_values();
    $sessions = new sessions();
    $db = new db(database_list::LIVE_MYSQL_1);

    $result = array();
    $result["login"] = null;
    $result["info"] = null;
    $type = user::post(post_keys::TYPE);

    switch ($type){
        case get_types::SECURITY_CODE:
                $result = branch_users::get_branch_devices($db, device_types::COMPUTER, $db->where->equals([
                    tbl2::SECURITY_CODE => user::post(post_keys::SECURITY_CODE),
                    tbl2::IS_CONNECT => 0
                ]));

                if (count($result->rows) > 0){
                    $db->db_update(tbl2::TABLE_NAME, [tbl2::IS_CONNECT => 1, tbl2::TOKEN => user::post(post_keys::TOKEN)],
                        where: $db->where->equals([tbl2::ID => $result->rows[0]["id"]])
                    );
                    check_token($db,$sessions,$echo);
                }else $echo->status = false;
            break;
        case get_types::TOKEN:
             check_token($db,$sessions,$echo);
            break;
        case get_types::PASSWORD:
            if ($sessions->get->BRANCH_ID > 0 &&  $sessions->get->TOKEN){
                $result["login"] =  branch_users::get($db, $sessions->get->BRANCH_ID, user::post(post_keys::PASSWORD), custom_where: $db->where->equals([tbl::ACTIVE => 1]))->rows;
                if (count($result["login"]) > 0){
                    $result["info"] = branch_users::get_branch_info($db, $sessions->get->BRANCH_ID)->rows;
                    if(count($result["login"]) > 0){
                        $result["login"][0]["status"] = true;
                        $sessions->get->TOKEN        = true;
                        $sessions->get->USER_ID      = $result["login"][0]["id"];
                        $sessions->get->CURRENCY     = $result["info"][0]["type"];
                        $sessions->get->LANGUAGE_ID  = $result["info"][0]["language_id"];
                        $sessions->get->LANGUAGE_TAG = $result["info"][0]["seo_url"];
                        $sessions->get->USER_NAME    = $result["login"][0]["name"];
                        $sessions->get->BRANCH_NAME  = $result["info"][0]["name"];
                        $sessions->get->BRANCH_MAIN_ID = $result["info"][0]["main_id"];
                        $permissions = json_decode($result["login"][0]["permissions"], true);
                        foreach ($permissions as $value){
                            $sessions->get->PERMISSION[$value] = true;
                        }
                        $sessions->set->INTEGRATIONS($db);

                        $sessions->create();
                    } else $echo->status = false;
                }else $echo->status = false;
            }
            break;
        case get_types::UN_TOKEN:
            $db->db_delete(
                tbl2::TABLE_NAME,
                where: $db->where->equals([tbl2::BRANCH_ID => $sessions->get->BRANCH_ID, tbl2::TOKEN => user::post(post_keys::TOKEN)])
            );
        break;
    }
    $echo->custom_data = [];
    $echo->return();
}

function check_token(db $db,sessions $sessions,echo_values &$echo): results{
    $result = branch_users::get_branch_devices($db, device_types::COMPUTER, $db->where->equals([tbl2::TOKEN => user::post(post_keys::TOKEN), tbl2::IS_CONNECT => 1]));
    if (count($result->rows) > 0){
        $sessions->get->TOKEN = true;
        $sessions->get->BRANCH_ID = $result->rows[0]["branch_id"];
        $sessions->get->CALLER_ID_ACTIVE = (bool)$result->rows[0]["caller_id_active"];
        $sessions->create();
    }else {
        $echo->status = false;
        session_destroy();
    }
    return $result;
}

/* end Functions */