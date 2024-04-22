<?php

namespace _superadmin\functions\branch\set;

use _superadmin\sameparts\functions\sessions\get;
use config\db;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\user;
use sameparts\php\ajax\echo_values;
use config\table_helper\branch_info as tbl;
use sameparts\php\helper\page_names\pos;

class post_keys {
    const NAME = "name",
        POS_APP_LIMIT = "pos_limit",
        WAITER_APP_LIMIT = "waiter_limit",
        LICENSE_TIME_ID = "license_time_id",
        LICENSE_TYPE_ID = "license_type_id",
        IS_MAIN = "is_main",
        MAIN_ID = "main_id";
}

class branch_add
{
    public function __construct(db $db, get $sessions, echo_values &$echo){
        $echo->rows = (array)$this->insert($db, $sessions, $echo);
    }

    private function insert(db $db, get $sessions, echo_values $echo): results {
        return $db->db_insert(
            tbl::TABLE_NAME,
            [  tbl::NAME => user::post(post_keys::NAME),
                tbl::LICENSE_TYPE_ID => user::post(post_keys::LICENSE_TYPE_ID),
                tbl::LICENSE_TIME_ID => user::post(post_keys::LICENSE_TIME_ID),
                tbl::WAITER_APP_LIMIT => user::post(post_keys::WAITER_APP_LIMIT),
                tbl::POS_APP_LIMIT => user::post(post_keys::POS_APP_LIMIT),
                tbl::IS_MAIN => user::post(post_keys::IS_MAIN),
                tbL::MAIN_ID => user::post(post_keys::MAIN_ID),
                tbl::ACTIVE => 1,
                tbl::IS_CONFIRM => 1,
                tbl::LANGUAGE_ID => 1,
                tbl::QR_ACTIVE => true,
                tbl::CREATE_DATE => date("Y-m-d")
            ]
        );
    }

}