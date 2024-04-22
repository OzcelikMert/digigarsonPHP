<?php
namespace order_app\sameparts\functions\sessions;

use matrix_library\php\operations\user;

class get extends keys{
    public int $SELECT_BRANCH_ID = 0, $SELECT_BRANCH_TABLE_ID = 0,$SELECT_BRANCH_TABLE_TYPE= 0, $LANGUAGE_ID = 0, $USER_ID = 0, $LANG_ID=0,$VERIFY_CODE = 0000;
    public string $LANGUAGE_TAG = "tr",$NAME = "";
    public bool $VERIFY = false;
    public mixed $PHONE = null;



    public function __construct(){
        if(!user::check_session_start()) session_start();
        $this->LANGUAGE_TAG = user::session(keys::LANG_TAG);
        $this->LANG_ID = user::session(keys::LANG_ID);
        $this->USER_ID = user::session(keys::USER_ID);
        $this->NAME = user::session(keys::NAME);
        $this->PHONE = user::session(keys::PHONE);
        $this->VERIFY = user::session(keys::VERIFY);
        $this->VERIFY_CODE = user::session(keys::VERIFY_CODE);
        $this->SELECT_BRANCH_ID = user::session(keys::SELECT_BRANCH_ID);
        $this->SELECT_BRANCH_TABLE_ID = user::session(keys::SELECT_BRANCH_TABLE_ID);
        $this->SELECT_BRANCH_TABLE_TYPE = user::session(keys::SELECT_BRANCH_TABLE_TYPE);
    }

    public function create() : void{
        user::session_creator(array(
            keys::LANG_ID => $this->LANGUAGE_ID,
            keys::LANG_TAG => $this->LANGUAGE_TAG,
            keys::USER_ID => $this->USER_ID,
            keys::NAME => $this->NAME,
            keys::PHONE => $this->PHONE,
            keys::VERIFY => $this->VERIFY,
            keys::VERIFY_CODE => $this->VERIFY_CODE,
            keys::SELECT_BRANCH_ID => $this->SELECT_BRANCH_ID,
            keys::SELECT_BRANCH_TABLE_ID => $this->SELECT_BRANCH_TABLE_ID,
            keys::SELECT_BRANCH_TABLE_TYPE => $this->SELECT_BRANCH_TABLE_TYPE,
        ));
    }
}

