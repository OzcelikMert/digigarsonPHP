<?php
namespace manage\sameparts\functions\navbar\set;

use config\db;
use config\sessions;
use config\settings;
use config\table_helper\branch_info as tbl;
use config\table_helper\currency_types as tbl3;
use config\table_helper\language_types as tbl4;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use sameparts\php\ajax\echo_values;

class post_keys {
    const ID = "id";
}

class company {
    public function __construct(db $db, sessions $sessions, echo_values &$echo) {
        $this->check_values($db, $sessions, $echo);
        if($echo->status){
            $echo->rows = $this->get($db, $sessions);
            if(count($echo->rows) < 1){
                $echo->status = false;
                $echo->error_code = settings::error_codes()::WRONG_VALUE;
                return;
            }

            $sessions->get->BRANCH_ID = $echo->rows[0]["id"];
            $sessions->get->BRANCH_MAIN_ID = $echo->rows[0]["main_id"];
            $sessions->get->BRANCH_NAME = $echo->rows[0]["name"];
            $sessions->get->CURRENCY = $echo->rows[0]["type"];
            $sessions->get->LANGUAGE_TAG = $echo->rows[0]["seo_url"];
            $sessions->get->LANGUAGE_ID = $echo->rows[0]["language_id"];
            $sessions->create();
        }
    }

    /* Functions */
    private function get(db $db, sessions $sessions) : array{
        $where = ($sessions->get->BRANCH_ID_MAIN == 3 && $sessions->get->PERMISSION == "*")
            ? []
            : [
                tbl::MAIN_ID => $sessions->get->BRANCH_ID_MAIN
            ];
        if(user::post(post_keys::ID) == 0){
            user::post(post_keys::ID, $sessions->get->BRANCH_ID_MAIN);
            $where = [];
        }

        return $db->db_select(
            array(
                tbl::ID,
                tbl::MAIN_ID,
                tbl::NAME,
                tbl::LANGUAGE_ID,
                tbl3::TYPE,
                tbl4::SEO_URL
            ),
            tbl::TABLE_NAME,
            $db->join->inner( array(
                tbl3::TABLE_NAME => [ tbl::CURRENCY_ID => tbl3::ID ],
                tbl4::TABLE_NAME => [ tbl::LANGUAGE_ID => tbl4::ID ],
            )),
            $db->where->equals(array_merge([
                tbl::ID => user::post(post_keys::ID)
            ], $where))
        )->rows;
    }

    private function check_values(db $db, sessions $sessions, echo_values &$echo){
        if(variable::is_empty(
            user::post(post_keys::ID)
        )){
            $echo->error_code = settings::error_codes()::EMPTY_VALUE;
        }

        if($echo->error_code != settings::error_codes()::SUCCESS) $echo->status = false;
    }
}