<?php
namespace _superadmin\functions\branch\set;

use _superadmin\sameparts\functions\sessions\get;
use config\db;
use config\table_helper\branch_manage_users as tbl;
use config\table_helper\branch_users as tbl2;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\user;
class post_keys {
    const  NAME = "name",
        PASSWORD = "password",
        PHONE = "phone",
        EMAIL = "email",
        ACTIVE = "active",
        BRANCH_ID = "branch_id",
        ID = "id",
        FUNCTION_TYPE = "function_type";
}
class function_type{
    const
        MANAGE_USER_DELETE  = 0x0004,
        BRANCH_USER_DELETE =  0x0005,
        BRANCH_USER_EDIT = 0x0006;
}

class user_edit
{
    public function __construct(db $db, get $sessions, &$echo) {
        if(user::post(post_keys::FUNCTION_TYPE)){
            $echo->rows = match ((int)user::post(post_keys::FUNCTION_TYPE)) {
                function_type::MANAGE_USER_DELETE => (array)$this->delete_manage($db, $sessions, $echo)->rows,
                function_type::BRANCH_USER_DELETE => (array)$this->delete_branch($db, $sessions, $echo)->rows,
                function_type::BRANCH_USER_EDIT => (array)$this->branch_user_edit($db, $sessions, $echo)->rows,
            };
        }
        else{
            if(!user::post(post_keys::ID))
                $echo->rows = (array)$this->insert($db, $sessions, $echo)->rows;
            else
                $echo->rows =(array)$this->update($db, $sessions, $echo)->rows;
        }
}
// manage edit S
    private function insert(db $db, get $sessions, &$echo) :results{
        return $db->db_insert(
            tbl::TABLE_NAME, array(
            tbl::NAME  => user::post(post_keys::NAME),
            tbl::PASSWORD =>  user::post(post_keys::PASSWORD),
            tbl::PHONE  => user::post(post_keys::PHONE),
            tbl::EMAIL =>  user::post(post_keys::EMAIL),
            tbl::BRANCH_ID  => user::post(post_keys::BRANCH_ID)
        ));
    }
    private function update(db $db, get $sessions, &$echo) :results{
        return $db->db_update(
            tbl::TABLE_NAME,
            array(
                tbl::NAME  => user::post(post_keys::NAME),
                tbl::PASSWORD =>  user::post(post_keys::PASSWORD),
                tbl::PHONE  => user::post(post_keys::PHONE),
                tbl::EMAIL =>  user::post(post_keys::EMAIL),
                tbl::BRANCH_ID  => user::post(post_keys::BRANCH_ID)
            ),
            where: $db->where->equals([
            tbl::ID => user::post(post_keys::ID)
        ])
        );
    }
    //manage edit F
    private function branch_user_edit(db $db, get $sessions, &$echo) :results{
        return $db->db_update(
            tbl2::TABLE_NAME,
            array(
                tbl2::NAME => user::post(post_keys::NAME),
                tbl2::ACTIVE => user::post(post_keys::ACTIVE),
                tbl2::PASSWORD => user::post(post_keys::PASSWORD)
            ),
            where: $db->where->equals([
            tbl2::ID => user::post(post_keys::ID),
            tbl2::BRANCH_ID => user::post(post_keys::BRANCH_ID),
        ])
        );
    }
    private function delete_manage(db $db, get $sessions, &$echo) : results{
        return $db->db_delete(
            tbl::TABLE_NAME,
            where: $db->where->equals([
            tbl::ID => user::post(post_keys::ID)
        ])
   );
    }
    private function delete_branch(db $db, get $sessions, &$echo) :results{
        return $db->db_update(
            tbl2::TABLE_NAME,
            array(
                tbl2::ACTIVE => false,
                tbl2::IS_DELETE => true
            ),
            where: $db->where->equals([
            tbl2::ID => user::post(post_keys::ID),
            tbl2::BRANCH_ID => user::post(post_keys::BRANCH_ID),
        ])
        );
    }
}

