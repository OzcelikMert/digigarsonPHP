<?php
namespace pos\functions\products\set;

use config\db;
use config\sessions;
use config\settings;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use config\table_helper\products as tbl;
use sameparts\php\ajax\echo_values;


class post_keys {
      CONST  PRODUCTS = "products";
}

class products_keys {
    const ID = "id";
}

class translate_product{
    function __construct(db $db, sessions $sessions, echo_values &$echo){
        $this->check_values($db, $sessions, $echo);
        if($echo->status){
            $echo->custom_data = (array)$this->set($db, $sessions);
        }
    }

    function set(db $db, sessions $sessions): array{
        $values = array();

        foreach(user::post(post_keys::PRODUCTS) as $product){
            $update = array();
            foreach ($product["translates"] as $key => $value){
                $update[$key] = $value;
            }

            array_push($values, (array)$db->db_update(
                tbl::TABLE_NAME,
                $update,
                where: $db->where->like([
                    tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                    tbl::ID => $product[products_keys::ID]
                ])
            ));
        }

        return $values;
    }

    function check_values(db $db, sessions $sessions, echo_values &$echo){
        if(variable::is_empty(user::post(post_keys::PRODUCTS)))
            $echo->error_code = settings::error_codes()::EMPTY_VALUE;

        if($echo->error_code != settings::error_codes()::SUCCESS) $echo->status = false;
    }
}