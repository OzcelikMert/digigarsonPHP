<?php
namespace config\sessions;

use config\settings;
use matrix_library\php\operations\method_types;
use matrix_library\php\operations\server;
use matrix_library\php\operations\user;

if(!user::check_session_start()) {
    session_start();
}

class check extends keys{
    public static function check(bool $page_change = true, string $application_name = "") : bool{
        $data = array();

        $application_name = ($application_name != "") ? $application_name : server::get_url_folders()[0];

        $data = match ($application_name){
            settings::application_names()::MANAGE => array(
                keys::USER_ID_MANAGE,
                keys::BRANCH_ID
            ),
            default => array(
                keys::USER_ID,
                keys::BRANCH_ID,
                keys::TOKEN
            )
        };

        if(
            !user::check_sent_data($data, method_types::SESSION)
        ){
            if($page_change) header("Location: index.php");
            else return false;
        }

        return true;
    }
}