<?php
namespace _superadmin\sameparts\functions\sessions;
use matrix_library\php\operations\user;

if(!user::check_session_start()) {
    session_start();
}

class check extends keys{
    public static function check(bool $page_change = true) : bool{
        if(
            !user::session(keys::USER_ID)
        ){
            if($page_change) header("Location: login.php");
            else return false;
        }

        return true;
    }
}