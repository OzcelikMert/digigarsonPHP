<?php
namespace waiter_terminal\sameparts\functions\sessions;

use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;

if(!user::check_session_start()) {
    session_start();
}

class check extends keys{
    public static function check(bool $page_change = true) : bool{
        if(
            !user::session(keys::USER_ID) ||
            !user::session(keys::BRANCH_ID) ||
            !user::session(keys::TOKEN)
        ){
            if($page_change) header("Location: index.php");
            else return false;
        }

        return true;
    }
}