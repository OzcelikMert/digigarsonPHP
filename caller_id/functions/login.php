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
use config\table_helper\branch_info as tbl2;


class post_keys { const SECURITY_CODE = "security_code", TOKEN = "token"; }

if(user::check_sent_data([
    post_keys::SECURITY_CODE,
    post_keys::TOKEN
])) {
    $db = new db(database_list::LIVE_MYSQL_1);
    variable::clear_all_data($_POST);
    user::post(post_keys::TOKEN, hash("sha256", user::post(post_keys::TOKEN)));

    $result = $db->db_select(
        array(
            tbl::TOKEN,
            $db->as_name(tbl::NAME, "device_name"),
            $db->as_name(tbl2::NAME, "branch_name")
        ),
        tbl::TABLE_NAME,
        $db->join->inner([
            tbl2::TABLE_NAME => [tbl2::ID => tbl::BRANCH_ID]
        ]),
        $db->where->equals([
            tbl::SECURITY_CODE => user::post(post_keys::SECURITY_CODE),
            tbl::TYPE          => device_types::CALLER_ID,
            [tbl::IS_CONNECT    => 0, tbl::TOKEN => user::post(post_keys::TOKEN)]
        ]),
        limit: $db->limit([0, 1])
    );

    $echo = array(
        "status"        => false,
        "device_name"   => "",
        "branch_name"   => ""
    );

    if($result->status && count($result->rows) > 0){
        $db->db_update(
            tbl::TABLE_NAME,
            array(
                tbl::TOKEN      => user::post(post_keys::TOKEN),
                tbl::IS_CONNECT => 1
            ),
            where: $db->where->equals([
                tbl::SECURITY_CODE => user::post(post_keys::SECURITY_CODE),
                tbl::TYPE          => device_types::CALLER_ID,
                tbl::IS_CONNECT    => 0
            ])
        );
        $echo["status"] = true;
        $echo["device_name"] = $result->rows[0]["device_name"];
        $echo["branch_name"] = $result->rows[0]["branch_name"];
    }

    echo json_encode($echo);
}

/* end Functions */