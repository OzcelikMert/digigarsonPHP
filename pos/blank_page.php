<?php
namespace pos;
require "../matrix_library/php/auto_loader.php";

use matrix_library\php\operations\user;
use matrix_library\php\page\creator;
use pos\sameparts\functions\sessions\keys;

class blank_page extends creator {
    private string $v;

    public function __construct() {
        $this->v = "?v=".date("YmdHis");
        $this->page_title = "Blank Page";
        parent::__construct();
    }
    protected function main(): void {
        /*user::session_creator(array(
            keys::BRANCH_ID => 3,
            keys::LANGUAGE_ID => 1,
            keys::LANGUAGE_TAG => "tr"
        ));*/
    }
    protected function page_body(): string {
        // return static::set_include( array("./views/example/example.php") );
        return "";
    }
    protected  function custom_links(): string {
        return '
            <link rel="stylesheet" href="./assets/styles/products.css'.($this->v).'"/>
            <link rel="stylesheet" href="./assets/styles/theme/theme_dark.css'.($this->v).'"/>
        ';
    }
    protected function custom_scripts(): string {
        return '
            <script src="./assets/scripts/example.js'.$this->v.'"></script>
        ';
    }
}

$blank_page= new blank_page();
