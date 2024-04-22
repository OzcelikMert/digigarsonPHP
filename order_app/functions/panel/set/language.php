<?php
namespace order_app\functions\panel\set;
use config\db;
use matrix_library\php\operations\user;
use order_app\sameparts\functions\sessions\get;
use order_app\sameparts\functions\sessions\keys;
use sameparts\php\ajax\echo_values;

class post_keys {
    const LANGUAGE = "language";
}

class language{

    public function __construct(db $db, get $sessions, echo_values &$echo){
        $this->check();
    }
    function check(){
        $language = match (user::post(post_keys::LANGUAGE)){
            "en" => "en",
            "de" => "de",
            "fr" => "fr",
            "ru" => "ru",
            "ar" => "ar",
            "sp" => "sp",
            "nl" => "nl",
            "it" => "it",
            "ro" => "ro",
            "pt" => "pt",
            "zh" => "zh",
            default => "tr",
        };
        user::session(keys::LANG_TAG,$language);
    }


}