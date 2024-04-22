<?php
namespace manage\functions\settings_integration\set;

use config\db;
use config\settings;
use config\table_helper\integrate_products as tbl;
use config\table_helper\integrate_product_options as tbl2;
use config\sessions;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use sameparts\php\ajax\echo_values;
use sameparts\php\db_query\integrate;

class post_keys{
    const TYPE = "type",
        PRODUCTS = "products",
        LIST_TYPE = "list_type";
}

class list_types {
    const PRODUCT = 1,
        OPTION = 2;
}

class products_keys {
    const PRODUCT_ID = "product_id",
        PRODUCT_INTEGRATE_ID = "product_integrate_id";
}

class product {
    function __construct(db $db,sessions $sessions,echo_values &$echo){
        $this->check_values($db, $sessions, $echo);
        if($echo->status){
            $this->set($db, $sessions, $echo);
        }
    }

    private function set(db $db,sessions $sessions, echo_values &$echo): void{
        $id = array();
        $data = array();
        $table_name = (user::post(post_keys::LIST_TYPE) == list_types::PRODUCT)
            ? tbl::TABLE_NAME
            : tbl2::TABLE_NAME;
        $update_where = (user::post(post_keys::LIST_TYPE) == list_types::PRODUCT)
            ? $db->where->equals([
                tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl::PRODUCT_ID_INTEGRATED => $id
            ])
            : $db->where->equals([
                tbl2::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl2::OPTION_ID_INTEGRATED => $id
            ]);

        foreach (user::post(post_keys::PRODUCTS) as $product){
            array_push($id, $product[products_keys::PRODUCT_INTEGRATE_ID]);

            if(user::post(post_keys::LIST_TYPE) == list_types::PRODUCT) array_push($data, array(
                tbl::PRODUCT_ID            => $product[products_keys::PRODUCT_ID],
                tbl::TYPE                  => user::post(post_keys::TYPE),
                tbl::BRANCH_ID             => $sessions->get->BRANCH_ID,
                tbl::PRODUCT_ID_INTEGRATED => $product[products_keys::PRODUCT_INTEGRATE_ID]
            ));
            else array_push($data, array(
                tbl2::OPTION_ID             => $product[products_keys::PRODUCT_ID],
                tbl2::TYPE                  => user::post(post_keys::TYPE),
                tbl2::BRANCH_ID             => $sessions->get->BRANCH_ID,
                tbl2::OPTION_ID_INTEGRATED  => $product[products_keys::PRODUCT_INTEGRATE_ID]
            ));
        }

        if(count($id) > 0) $db->db_delete(
            $table_name,
            where: $update_where
        );

        if(count($data) > 0) $db->db_insert(
            $table_name,
            $data
        );
    }

    private function check_values(db $db, sessions $sessions, echo_values &$echo){
        if(variable::is_empty(
            user::post(post_keys::TYPE),
            user::post(post_keys::PRODUCTS),
            user::post(post_keys::LIST_TYPE)
        )){
            $echo->error_code = settings::error_codes()::EMPTY_VALUE;
        }

        if($echo->error_code == settings::error_codes()::SUCCESS){
            if(count(integrate::get_types(
                    $db,
                    user::post(post_keys::TYPE)
                )->rows) < 1){
                $echo->error_code = settings::error_codes()::INCORRECT_DATA;
            }
        }

        if($echo->error_code != settings::error_codes()::SUCCESS) $echo->status = false;
    }
}
