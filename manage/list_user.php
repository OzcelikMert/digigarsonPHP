<?php
namespace manage;
require "../matrix_library/php/auto_loader.php";

use matrix_library\php\operations\user;
use matrix_library\php\page\creator;


class list_user extends creator {
    private string $v;

    public function __construct() {
        $this->v = "?v=".date("YmdHis");
        $this->page_title = "User List";
        parent::__construct();
    }
    protected function main(): void {

    }
    protected function page_body(): string {
         return static::set_include(array(
             "./views/list_user/top.php",
             "./views/list_user/list.php",
             "./views/list_user/modals.php"
         ));

    }
    protected  function custom_links(): string {
        return '
            <link rel="stylesheet" href="./assets/styles/list_user.css'.$this->v.'">
        ';
    }
    protected function custom_scripts(): string {
        return '
            <script src="./assets/scripts/list_user.js'.$this->v.'"></script>    
        ';

    }
}

$user_list = new list_user();

