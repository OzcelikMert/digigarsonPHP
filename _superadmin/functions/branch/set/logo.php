<?php


namespace _superadmin\functions\branch\set;

use _superadmin\sameparts\functions\sessions\get;
use config\settings;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\server;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use sameparts\php\ajax\echo_values;
use config\db;

class post_keys {
    const
        IMAGE = "image",
        ID = "branch_id";
}
class logo{

    public function __construct(db $db, get $sessions, echo_values &$echo) {
        $this->check_values($db, $sessions, $echo);
        if($echo->status) $this->createLogo($db, $sessions, $echo);
    }

    private function createLogo(db $db, get $sessions, &$echo){
        if (!server::upload_image(
            user::files(post_keys::IMAGE)["tmp_name"],
            settings::paths()->image->BRANCH_LOGO(user::post(post_keys::ID), false),
            "logo.webp"
        )) {
            $echo->error_code = settings::error_codes()::UPLOAD_ERROR;
            $echo->status = false;
        }
    }


    function check_values(db $db, get $sessions, echo_values &$echo){
        if(variable::is_empty(
            user::files(post_keys::IMAGE),
            user::post(post_keys::ID)
        )){
            $echo->error_code = settings::error_codes()::EMPTY_VALUE;
        }

        if($echo->error_code != settings::error_codes()::SUCCESS) $echo->status = false;
    }
}