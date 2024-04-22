<?php
namespace manage\functions\settings_integration\get;

use config\db;
use config\settings;
use config\type_tables_values\integrate_types;
use config\sessions;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use sameparts\php\ajax\echo_values;
use sameparts\php\db_query\integrate;


class post_keys{
    const TYPE = "type";
}

class products{
    function __construct(db $db, sessions $sessions, echo_values &$echo){
        $this->check_values($db, $sessions, $echo);
        if($echo->status){
            $echo->rows = $this->get($db, $sessions);
        }
    }

    private function get(db $db, sessions $sessions) : array{
        return array(
            "products" => integrate::get_products($db, $sessions->get->BRANCH_ID, user::post(post_keys::TYPE))->rows,

            "options" => integrate::get_product_options($db, $sessions->get->BRANCH_ID, user::post(post_keys::TYPE))->rows
        );
    }

    private function check_values(db $db, sessions $sessions, echo_values &$echo){
        if(variable::is_empty(
            user::post(post_keys::TYPE)
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
