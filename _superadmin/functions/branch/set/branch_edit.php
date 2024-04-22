<?php
namespace _superadmin\functions\branch\set;

use _superadmin\sameparts\functions\sessions\get;
use config\db;
use config\table_helper\branch_info as tbl;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\user;
class post_keys {
    const
    ID = "id",
    NAME = "name",
    LICENSE_TIME_ID = "license_time_id",
    LICENSE_TYPE_ID = "license_type_id",
    POS_APP_LIMIT = "pos_app_limit",
    WAITER_APP_LIMIT = "waiter_app_limit",
    IS_MAIN = "is_main",
    MAIN_ID = "main_id";
}


class branch_edit
{
    public function __construct(db $db, get $sessions, &$echo) {
        $echo->rows =(array)$this->update($db, $sessions, $echo)->rows;
    }

    private function update(db $db, get $sessions, &$echo) :results{
        return $db->db_update(
            tbl::TABLE_NAME,
            array(
                tbl::NAME => user::post(post_keys::NAME),
                tbl::LICENSE_TYPE_ID => user::post(post_keys::LICENSE_TYPE_ID),
                tbl::POS_APP_LIMIT => user::post(post_keys::POS_APP_LIMIT),
                tbl::WAITER_APP_LIMIT => user::post(post_keys::WAITER_APP_LIMIT),
                tbl::LICENSE_TIME_ID => user::post(post_keys::LICENSE_TIME_ID),
                tbl::IS_MAIN => user::post(post_keys::IS_MAIN),
                tbl::MAIN_ID => user::post(post_keys::MAIN_ID),
            ),
            where: $db->where->equals([
            tbl::ID => user::post(post_keys::ID)
        ])
        );
    }
}

