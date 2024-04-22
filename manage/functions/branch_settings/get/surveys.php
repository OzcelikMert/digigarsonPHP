<?php
namespace manage\functions\branch_settings\get;

use config\db;
use config\settings;
use config\sessions;
use matrix_library\php\operations\user;
use sameparts\php\ajax\echo_values;
use sameparts\php\db_query\surveys as db_query_surveys;

class post_keys{ const TYPE = "type"; }

class types {
    const BRANCH = 1; //BRANCH_NOTIFICATION
}

class surveys{
    function __construct(db $db, sessions $sessions, echo_values &$echo){
        switch (user::post(post_keys::TYPE)) {
            case types::BRANCH:
                $echo->rows = db_query_surveys::get_questions($db,$sessions->get->BRANCH_ID,$sessions->get->LANGUAGE_TAG)->rows;
                break;
            default:
                $echo->message = user::post(post_keys::TYPE);
                $echo->status = false;
                $echo->error_code = settings::error_codes()::NOT_FOUND;
                break;
        }
    }
}
