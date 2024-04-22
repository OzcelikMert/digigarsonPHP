<?php

namespace _superadmin\functions\branch\set;

use _superadmin\sameparts\functions\sessions\get;
use config\db;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\user;
use sameparts\php\ajax\echo_values;
use config\table_helper\branch_info as tbl;

class function_type {
    const ADD = 0x0010, DELETE = 0x0011, EDIT = 0x0012;
}

class post_keys {
    const
        NAME = "name",
        POS_APP_LIMIT = "pos_limit",
        WAITER_APP_LIMIT = "waiter_limit",
        LICENSE_TIME_ID = "license_time_id",
        LICENSE_TYPE_ID = "license_type_id",
        FUNCTION_TYPE = "function_type";
}

class branch
{
    public function __construct(db $db, get $sessions, &$echo){
        if(user::post(post_keys::FUNCTION_TYPE)){
            $echo->rows= match((int)user::post(post_keys::FUNCTION_TYPE)){
                function_type::ADD => (array)$this->add($db, $sessions, $echo),
                function_type::DELETE => (array)$this->delete($db, $sessions, $echo),
                function_type::EDIT => (array)$this->edit($db, $sessions, $echo),
            };
        }else{
            if(!user::post(post_keys::ID)){

            }
        }
    }

}