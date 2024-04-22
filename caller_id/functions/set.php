<?php
namespace caller_id\functions;
header("Content-Type: application/json; charset=UTF-8");
require "../../matrix_library/php/auto_loader.php";

use config\database_list;
use config\db;
use config\type_tables_values\device_types;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use config\table_helper\branch_devices as tbl;
use config\table_helper\branch_callers as tbl2;
use sameparts\php\helper\variable_filters;

class post_keys { const SECURITY_CODE = "security_code", TOKEN = "token", PHONE = "phone"; }


if(user::check_sent_data([
    post_keys::SECURITY_CODE,
    post_keys::TOKEN,
    post_keys::PHONE
])) {
    $db = new db(database_list::LIVE_MYSQL_1);
    variable::clear_all_data($_POST);
    user::post(post_keys::TOKEN, hash("sha256", user::post(post_keys::TOKEN)));

    $result = $db->db_select(
        array(
            tbl::BRANCH_ID,
            tbl::ID
        ),
        tbl::TABLE_NAME,
        where: $db->where->equals([
            tbl::SECURITY_CODE => user::post(post_keys::SECURITY_CODE),
            tbl::TYPE          => device_types::CALLER_ID,
            [tbl::IS_CONNECT    => 0, tbl::TOKEN => user::post(post_keys::TOKEN)]
        ]),
        limit: $db->limit([0, 1])
    );

    $echo = array(
        "status" => false
    );

    if(count($result->rows) > 0){
        $phone = variable_filters::phone(user::post(post_keys::PHONE));
        user::post(post_keys::PHONE, (count($phone) > 0) ? $phone[0] : "");

        $echo["status"] = $db->db_insert(
            tbl2::TABLE_NAME,
            array(
                tbl2::BRANCH_ID => $result->rows[0]["branch_id"],
                tbl2::DEVICE_ID => $result->rows[0]["id"],
                tbl2::PHONE     => user::post(post_keys::PHONE)
            )
        )->status;
    }
    echo json_encode($echo);
}
/* end Functions */