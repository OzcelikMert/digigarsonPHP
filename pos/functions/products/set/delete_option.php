<?php

namespace pos\functions\products\set;

use config\db;
use config\sessions;
use config\table_helper\product_option as tbl;
use config\table_helper\product_option_items as tbl2;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use sameparts\php\ajax\echo_values;


class post_keys {
    const ID = "id";
}

class delete_option{
    public function __construct(db $db, sessions $sessions, echo_values &$echo){
        $this->check($echo);
        if ($echo->status) {
            $echo->custom_data["option"] = (array)$this->delete_option($db, $sessions);
            $echo->custom_data["items"] = (array)$this->delete_option_items($db, $sessions);
        }
    }

    function check(echo_values &$echo){
        if (variable::is_empty(user::get(post_keys::ID))){
            $echo->status = false;
        }
    }

    function delete_option(db $db, sessions $sessions): results{
        return $db->db_update(
            tbl::TABLE_NAME,
            array(tbl::IS_DELETED => 1),
            where: $db->where->like([tbl::BRANCH_ID => $sessions->get->BRANCH_ID, tbl::ID => (int)user::post(post_keys::ID)])
        );
    }
    function delete_option_items(db $db, sessions $sessions): results{
        return $db->db_update(
            tbl2::TABLE_NAME,
            array(tbl2::IS_DELETED => 1),
            where: $db->where->like([tbl2::BRANCH_ID => $sessions->get->BRANCH_ID, tbl2::OPTION_ID => (int)user::post(post_keys::ID)])
        );
    }

}