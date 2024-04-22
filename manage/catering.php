<?php
namespace manage;
require "../matrix_library/php/auto_loader.php";

use matrix_library\php\operations\user;
use matrix_library\php\page\creator;


class catering extends creator {
    private string $v;

    public function __construct() {
        $this->v = "?v=".date("YmdHis");
        $this->page_title = "Catering";
        parent::__construct();
    }
    protected function main(): void {

    }
    protected function page_body(): string {
         return static::set_include(array(
             "./views/catering/top.php",
             "./views/catering/list_owners.php",
             "./views/catering/list_questions.php",
             "./views/catering/modals.php"
         ));

    }
    protected  function custom_links(): string {
        return '
            <link rel="stylesheet" href="./assets/styles/catering.css'.$this->v.'">
        ';
    }
    protected function custom_scripts(): string {
        return '
            <script src="./assets/scripts/catering.js'.$this->v.'"></script>    
        ';

    }
}

$catering = new catering();

