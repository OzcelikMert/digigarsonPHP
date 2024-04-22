<?php


namespace _superadmin\functions\branch\get;


use _superadmin\sameparts\functions\sessions\get;
use config\db;
use config\table_helper\branch_info as tbl1;
use config\table_helper\branch_manage_users as tbl2;
use config\table_helper\branch_users as tbl3;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\user;

class post_keys{
    const
    FUNCTION_TYPE = "function_type",
    BRANCH_ID = "branch_id";
}

class function_type{
    const GET_BRANCH  = 1,
    GET_BRANCH_USER = 2,
    GET_MANAGE_USER = 3;
}
class details
{
 public function __construct(db $db, get $sessions, &$echo) {
     $echo->rows = match ((int)user::post(post_keys::FUNCTION_TYPE)) {
         function_type::GET_BRANCH => $this->get_branch($db, $sessions, $echo)->rows,
         function_type::GET_BRANCH_USER => $this->get_branch_user($db, $sessions, $echo)->rows,
         function_type::GET_MANAGE_USER => $this->get_manage_user($db, $sessions, $echo)->rows,
     };
   }

    private function get_branch(db $db, get $sessions, &$echo ): results{
    return  $db->db_select(
         array(
         tbl1::NAME,
             tbl1::ID,
             tbl1::CREATE_DATE,
             tbl1::WAITER_APP_LIMIT,
             tbl1::POS_APP_LIMIT,
             tbl1::LICENSE_TIME_ID,
             tbl1::LICENSE_TYPE_ID,
             tbl1::ADDRESS,
             tbl1::IS_MAIN,
             tbl1::MAIN_ID,
         ),
         tbl1::TABLE_NAME,
        where: $db->where->equals([tbl1::ID => user::post(post_keys::BRANCH_ID)])
     );
    }

    private function get_main_branch(db $db, get $sessions, &$echo): results{
        return  $db->db_select(
            array(
                tbl1::NAME,
                tbl1::ID,
                tbl1::CREATE_DATE,
                tbl1::WAITER_APP_LIMIT,
                tbl1::POS_APP_LIMIT,
                tbl1::LICENSE_TIME_ID,
                tbl1::LICENSE_TYPE_ID,
                tbl1::ADDRESS,
                tbl1::IS_MAIN,
                tbl1::MAIN_ID,
            ),
            tbl1::TABLE_NAME,
            where: $db->where->equals([tbl1::IS_MAIN => 1])
        );
    }

    private function get_manage_user(db $db, get $sessions, &$echo ): results{
        return $db->db_select(
            array(
                tbl2::ID,
                tbl2::NAME,
                tbl2::EMAIL,
                tbl2::PASSWORD,
                tbl2::PHONE
            ),
            tbl2::TABLE_NAME,
            where: $db->where->equals([tbl2::BRANCH_ID =>user::post(post_keys::BRANCH_ID)])
        );
    }
    private function get_branch_user(db $db, get $sessions, &$echo): results{
        return $db->db_select(
            array(
                tbl3::ID,
                tbl3::NAME,
                tbl3::PASSWORD,
                tbl3::ACTIVE
            ),
            tbl3::TABLE_NAME,
            where: $db->where->equals([
                tbl3::BRANCH_ID =>user::post(post_keys::BRANCH_ID),
                tbl3::IS_DELETE => false
            ])
        );
    }

}