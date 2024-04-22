<?php
namespace language\functions\index\set;

use config\db;
use config\settings;
use config\table_helper\translate as tbl;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\clear_types;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use language\sameparts\functions\sessions\get;
use sameparts\php\ajax\echo_values;

class post_keys {
    const KEY = "key",
        TR = "tr",
        EN = "en",
        AR = "ar",
        DE = "de",
        FR = "fr",
        IT = "it",
        NL = "nl",
        PT = "pt",
        RO = "ro",
        RU = "ru",
        SP = "sp",
        ZH = "zh";
}

class insert {
    public function __construct(db $db, get $sessions, echo_values &$echo) {
        $this->check_values($db, $sessions, $echo);
        if($echo->status){
            $echo->custom_data = (array)$this->set($db, $sessions);
        }
    }


    /* Functions */
    private function set(db $db, get $sessions) : results{
        return $db->db_insert(
            tbl::TABLE_NAME,
            array(
                tbl::CONST_NAME => user::post(post_keys::KEY),
                tbl::NAME_TR    => user::post(post_keys::TR),
                tbl::NAME_EN    => user::post(post_keys::EN),
                tbl::NAME_AR    => user::post(post_keys::AR),
                tbl::NAME_DE    => user::post(post_keys::DE),
                tbl::NAME_FR    => user::post(post_keys::FR),
                tbl::NAME_IT    => user::post(post_keys::IT),
                tbl::NAME_NL    => user::post(post_keys::NL),
                tbl::NAME_PT    => user::post(post_keys::PT),
                tbl::NAME_RO    => user::post(post_keys::RO),
                tbl::NAME_RU    => user::post(post_keys::RU),
                tbl::NAME_SP    => user::post(post_keys::SP),
                tbl::NAME_ZH    => user::post(post_keys::ZH),
            )
        );
    }

    private function check_values(db $db, get $sessions, echo_values &$echo){
        if(variable::is_empty(
            user::post(post_keys::KEY),
            user::post(post_keys::TR),
            user::post(post_keys::EN)
        )){
            $echo->error_code = settings::error_codes()::EMPTY_VALUE;
        }

        if($echo->error_code == settings::error_codes()::SUCCESS){
            user::post(
                post_keys::KEY,
                str_replace("-", "_", variable::clear(user::post(post_keys::KEY), clear_types::SEO_URL))
            );
            if(count($db->db_select(
                    tbl::ALL,
                    tbl::TABLE_NAME,
                    where: $db->where->equals([tbl::CONST_NAME => user::post(post_keys::KEY)]),
                    order_by: $db->order_by(tbl::CONST_NAME, $db::ASC),
                    limit: $db->limit([0,1]),
                )->rows) > 0) $echo->error_code = settings::error_codes()::REGISTERED_VALUE;
        }

        if($echo->error_code != settings::error_codes()::SUCCESS) $echo->status = false;
    }
}