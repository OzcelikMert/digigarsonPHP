<?php
namespace _superadmin\sameparts\functions\sessions;
use matrix_library\php\operations\user;

class get extends keys{
    public int $USER_ID = 0;

    public function __construct(){
        if(!user::check_session_start()) session_start();
        $this->USER_ID = user::session(keys::USER_ID);
    }

    public function create() : void{
        user::session_creator(array(
            keys::USER_ID => $this->USER_ID,
        ));
    }
}