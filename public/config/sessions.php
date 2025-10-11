<?php
namespace config;

use config\sessions\get;
use config\sessions\keys;
use config\sessions\set;
use matrix_library\php\operations\server;
use matrix_library\php\operations\user;

class sessions extends keys{
    private string $application_name;
    public set $set;
    public get $get;

    public function __construct(){
        if(!user::check_session_start()) session_start();

        $this->application_name = server::get_url_folders()[0];
        $this->set = new set($this);
        $this->get = new get($this);

        $key = match($this->application_name) {
            settings::application_names()::MANAGE => static::USER_ID_MANAGE,
            default => static::USER_ID
        };

        $this->get->BRANCH_ID = user::session(keys::BRANCH_ID);
        $this->get->LANGUAGE_ID = user::session(keys::BRANCH_ID);
        $this->get->LANGUAGE_TAG = user::session(keys::LANGUAGE_TAG);
        $this->get->CURRENCY = user::session(keys::CURRENCY);
        $this->get->USER_ID = user::session($key);
        $this->get->USER_NAME = user::session(keys::USER_NAME);
        $this->get->BRANCH_NAME = user::session(keys::BRANCH_NAME);
        $this->get->BRANCH_ID_MAIN = user::session(keys::BRANCH_ID_MAIN);
        $this->get->IS_MAIN = user::session(keys::IS_MAIN);
        $this->get->BRANCHES = user::session(keys::BRANCHES);
        $this->get->PERMISSION = user::session(keys::PERMISSION);
        $this->get->BRANCHES_NAMES = user::session(keys::BRANCHES_NAMES);
        $this->get->BRANCH_MAIN_ID = user::session(keys::BRANCH_MAIN_ID);
        $this->get->INTEGRATION    = user::session(keys::INTEGRATIONS);
        $this->get->CALLER_ID_ACTIVE = user::session(keys::CALLER_ID_ACTIVE);
        $this->get->TOKEN = user::session(keys::TOKEN);
        $this->get->USER_NAME = user::session(keys::USER_NAME);
    }

    public function create() : void{
        $data = array();
        $data = match ($this->application_name){
            settings::application_names()::POS => array(
                keys::BRANCH_ID => $this->get->BRANCH_ID,
                keys::LANGUAGE_ID => $this->get->LANGUAGE_ID,
                keys::LANGUAGE_TAG => $this->get->LANGUAGE_TAG,
                keys::CURRENCY => $this->get->CURRENCY,
                keys::USER_ID => $this->get->USER_ID,
                keys::TOKEN => $this->get->TOKEN,
                keys::BRANCH_NAME => $this->get->BRANCH_NAME,
                keys::USER_NAME => $this->get->USER_NAME,
                keys::PERMISSION => $this->get->PERMISSION,
                keys::CALLER_ID_ACTIVE => $this->get->CALLER_ID_ACTIVE,
                keys::BRANCH_MAIN_ID => $this->get->BRANCH_MAIN_ID,
                keys::INTEGRATIONS => $this->get->INTEGRATION
            ),
            settings::application_names()::MANAGE => array(
                keys::BRANCH_ID => $this->get->BRANCH_ID,
                keys::LANGUAGE_ID => $this->get->LANGUAGE_ID,
                keys::LANGUAGE_TAG => $this->get->LANGUAGE_TAG,
                keys::CURRENCY => $this->get->CURRENCY,
                keys::USER_ID_MANAGE => $this->get->USER_ID,
                keys::USER_NAME => $this->get->USER_NAME,
                keys::BRANCH_NAME => $this->get->BRANCH_NAME,
                keys::BRANCH_ID_MAIN => $this->get->BRANCH_ID_MAIN,
                keys::BRANCHES => $this->get->BRANCHES,
                keys::BRANCHES_NAMES => $this->get->BRANCHES_NAMES,
                keys::IS_MAIN => $this->get->IS_MAIN,
                keys::PERMISSION => $this->get->PERMISSION,
                keys::BRANCH_MAIN_ID => $this->get->BRANCH_MAIN_ID,
                keys::INTEGRATIONS => $this->get->INTEGRATION
            )
        };

        user::session_creator($data);
    }
}