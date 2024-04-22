<?php
namespace waiter_terminal\sameparts\functions\sessions;

use matrix_library\php\operations\user;

class get extends keys{
    public int $BRANCH_ID = 0,
        $USER_ID = 0,
        $DEVICE_ID = 0;
    public string $LANGUAGE_TAG = "",
        $CURRENCY = "",
        $BRANCH_NAME = "",
        $USER_NAME = "";
    public bool $TOKEN = false;
    public mixed $PERMISSION;


    public function __construct(){
        if(!user::check_session_start()) session_start();
        $this->BRANCH_ID = user::session(keys::BRANCH_ID);
        $this->LANGUAGE_TAG = user::session(keys::LANGUAGE_TAG);
        $this->CURRENCY = user::session(keys::CURRENCY);
        $this->USER_ID = user::session(keys::USER_ID);
        $this->TOKEN = user::session(keys::TOKEN);
        $this->BRANCH_NAME = user::session(keys::BRANCH_NAME);
        $this->USER_NAME = user::session(keys::USER_NAME);
        $this->PERMISSION = user::session(keys::PERMISSION);
        $this->DEVICE_ID = user::session(keys::DEVICE_ID);
    }

    public function create() : void{
        user::session_creator(array(
            keys::BRANCH_ID => $this->BRANCH_ID,
            keys::LANGUAGE_TAG => $this->LANGUAGE_TAG,
            keys::CURRENCY => $this->CURRENCY,
            keys::USER_ID => $this->USER_ID,
            keys::TOKEN => $this->TOKEN,
            keys::BRANCH_NAME => $this->BRANCH_NAME,
            keys::USER_NAME => $this->USER_NAME,
            keys::PERMISSION => $this->PERMISSION,
            keys::DEVICE_ID => $this->DEVICE_ID
        ));
    }
}