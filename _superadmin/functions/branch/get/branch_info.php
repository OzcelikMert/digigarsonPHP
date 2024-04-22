<?php

namespace _superadmin\functions\branch\get;

use _superadmin\sameparts\functions\sessions\get;
use config\db;
use config\table_helper\branch_info as tbl;
use matrix_library\php\operations\user;
use matrix_library\php\db_helpers\results;

class post_keys{
    const FUNCTION_TYPE = "function_type";
}

class function_type{
    const GET_BRANCH = 1,
    GET_MAIN_BRANCH = 2;
}

class branch_info {
    public function __construct(db $db, get $sessions, &$echo)
    {
         $echo->rows = match ((int)user::post(post_keys::FUNCTION_TYPE)) {
             function_type::GET_BRANCH => $this->get_branch($db, $sessions, $echo)->rows,
             function_type::GET_MAIN_BRANCH => $this->get_main_branch($db, $sessions, $echo)->rows,
         };
    }

    private function get_branch(db $db, get $sessions, &$echo): results{
        return $db->db_select(
            array(
                tbl::ID,
                tbl::NAME,
                tbl::LICENSE_TYPE_ID,
                tbl::LICENSE_TIME_ID,
                tbl::POS_APP_LIMIT,
                tbl::WAITER_APP_LIMIT
            ),
            tbl::TABLE_NAME,
            where: $db->where->equals([tbl::ACTIVE => true]),
            order_by: $db->order_by(tbl::ID, db::DESC),
        );
    }
    private function get_main_branch(db $db, get $sessions, &$echo): results {
        return $db->db_select(
            array(
                tbl::ID,
                tbl::NAME,
                tbl::LICENSE_TYPE_ID,
                tbl::LICENSE_TIME_ID,
                tbl::POS_APP_LIMIT,
                tbl::WAITER_APP_LIMIT
            ),
            tbl::TABLE_NAME,
            where: $db->where->equals([tbl::ACTIVE => true, tbl::IS_MAIN => 1]),
            order_by: $db->order_by(tbl::ID, db::DESC),
        );
    }
}