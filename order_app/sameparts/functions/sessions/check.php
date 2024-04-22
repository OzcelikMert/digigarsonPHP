<?php
namespace order_app\sameparts\functions\sessions;
session_start();

use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;

class check extends keys{
    public static function check(){
        if(
            !user::session(keys::USER_ID)
        ){
            header("Location: index.php");
        }
    }
}

check::check();