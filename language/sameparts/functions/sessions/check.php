<?php
namespace language\sameparts\functions\sessions;
session_start();

use matrix_library\php\operations\user;

class check extends keys{
    public static function check(){
        if(
            !user::session(keys::USER_ID)
        ){
            header("Location: login.php");
        }
    }
}

check::check();