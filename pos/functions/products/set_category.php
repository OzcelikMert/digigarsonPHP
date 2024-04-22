<?php
namespace functions\products;
require "../../../matrix_library/php/auto_loader.php";

use config\database_list;
use config\db;
use config\sessions;
use config\sessions\check;
use config\settings;
use config\settings\application_names;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use sameparts\php\ajax\echo_values;
use config\table_helper\product_categories as tbl;
use sameparts\php\db_query\products;
use sameparts\php\helper\date;

/* CONST Values */
class post_keys {
    const ID = "id",
        ACTIVE = "active_table",
        ACTIVE_TAKE_AWAY = "active_take_away",
        ACTIVE_SAFE = "active_safe",
        ACTIVE_COME_TAKE = "active_come_take",
        NAME = "name",
        MAIN_CATEGORY = "main_id",
        ACTIVE_START_TIME = "start_time",
        END_START_TIME = "end_time",
        RANK = "rank",
        FUNCTION = "function";
}
class function_types{
    const SAVE = 1,
          DELETE = 2;
}

/* end CONST Values */

if(user::check_sent_data(
    array(
        post_keys::NAME,
        post_keys::ACTIVE_START_TIME,
        post_keys::END_START_TIME
    )
) && (check::check(false) || check::check(false, application_names::MANAGE))){
    $db = new db(database_list::LIVE_MYSQL_1);
    $sessions = new sessions();
    variable::clear_all_data($_POST);
    $echo = new echo_values();


    check_values($db, $sessions, $echo);

    switch (user::post(post_keys::FUNCTION)){
        case function_types::SAVE:

            if($echo->status && user::post(post_keys::ID) > 0){
                $echo->autofill((array)update($db, $sessions));
            }else if($echo->status){
                $echo->autofill((array)insert($db, $sessions));
            }
            $echo->message= "ADD OR UPDATE";
            break;
        case function_types::DELETE:
            if($echo->status && user::post(post_keys::ID) > 0)
                $echo->autofill((array)delete($db, $sessions));
            $echo->message= "DELETE OR DELETE";
            break;
    }

    $echo->custom_data["post"] = $_POST;
    $echo->return();
}

/* Functions */
function insert(db $db, sessions $sessions) : results{
    return $db->db_insert(
        tbl::TABLE_NAME,
        array(
            tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
            tbl::NAME.$sessions->get->LANGUAGE_TAG => user::post(post_keys::NAME),
            tbl::MAIN_ID => user::post(post_keys::MAIN_CATEGORY),
            tbl::START_TIME => date::get(date\date_type_simples::HOUR_MINUTE, strtotime(user::post(post_keys::ACTIVE_START_TIME))),
            tbl::END_TIME => date::get(date\date_type_simples::HOUR_MINUTE, strtotime(user::post(post_keys::END_START_TIME))),
            tbl::ACTIVE => (int)user::post(post_keys::ACTIVE),
            tbl::ACTIVE_TAKE_AWAY => (int)user::post(post_keys::ACTIVE_TAKE_AWAY),
            tbl::ACTIVE_COME_TAKE => (int)user::post(post_keys::ACTIVE_COME_TAKE),
            tbl::ACTIVE_SAFE => (int)user::post(post_keys::ACTIVE_SAFE),
            tbl::RANK        => (int)user::post(post_keys::RANK)
        )
    );
}

function update(db $db, sessions $sessions) : results{
    return $db->db_update(
        tbl::TABLE_NAME,
        array(
            tbl::NAME.$sessions->get->LANGUAGE_TAG => user::post(post_keys::NAME),
            tbl::MAIN_ID => user::post(post_keys::MAIN_CATEGORY),
            tbl::START_TIME => date::get(date\date_type_simples::HOUR_MINUTE, strtotime(user::post(post_keys::ACTIVE_START_TIME))),
            tbl::END_TIME => date::get(date\date_type_simples::HOUR_MINUTE, strtotime(user::post(post_keys::END_START_TIME))),
            tbl::ACTIVE => (int)user::post(post_keys::ACTIVE),
            tbl::ACTIVE_TAKE_AWAY => (int)user::post(post_keys::ACTIVE_TAKE_AWAY),
            tbl::ACTIVE_COME_TAKE => (int)user::post(post_keys::ACTIVE_COME_TAKE),
            tbl::ACTIVE_SAFE => (int)user::post(post_keys::ACTIVE_SAFE),
            tbl::RANK        => (int)user::post(post_keys::RANK)
        ),
        where: $db->where->like([
            tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
            tbl::ID => user::post(post_keys::ID)
        ])
    );
}

function delete(db $db, sessions $sessions) : results{
    return $db->db_update(
        tbl::TABLE_NAME,
        array(
            tbl::IS_DELETE => 1
        ),
        where: $db->where->equals([
            tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
            tbl::ID => user::post(post_keys::ID)
        ])
    );
}

function check_values(db $db, sessions $sessions, echo_values &$echo){
    if(empty(user::post(post_keys::NAME))){
        $echo->error_code = settings::error_codes()::EMPTY_VALUE;
    }else if(!empty(user::post(post_keys::MAIN_CATEGORY))) {
        $values = products::get_categories(
            $db,
            $sessions->get->LANGUAGE_TAG,
            $sessions->get->BRANCH_ID,
            user::post(post_keys::MAIN_CATEGORY),
            limit: [0, 1]
        );

        if (count($values->rows) < 1) {
            $echo->error_code = settings::error_codes()::NOT_FOUND;
        }else if($values->rows[0]["id"] == user::post(post_keys::ID)) {
            $echo->error_code = settings::error_codes()::WRONG_VALUE;
        }
    }

    if($echo->error_code == settings::error_codes()::SUCCESS && user::post(post_keys::ID) > 0){
        $values = products::get_categories(
            $db,
            $sessions->get->LANGUAGE_TAG,
            $sessions->get->BRANCH_ID,
            user::post(post_keys::ID),
            limit: [0, 1]
        );

        if (count($values->rows) < 1) {
            $echo->error_code = settings::error_codes()::NOT_FOUND;
        }
    }

    if($echo->error_code != settings::error_codes()::SUCCESS) $echo->status = false;
}
/* end Functions */




