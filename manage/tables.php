<?php
namespace manage;
require "../matrix_library/php/auto_loader.php";

use matrix_library\php\operations\user;
use matrix_library\php\page\creator;

class tables extends creator {
    private string $v;

    public function __construct() {
        $this->v = "?v=".date("YmdHis");
        $this->page_title = "Tables";
        parent::__construct();
    }
    protected function main(): void {

    }
    protected function page_body(): string {
         return static::set_include(array(
             "./views/tables/list.php"
         ));

    }
    protected  function custom_links(): string {
        return '<link rel="stylesheet" href="./assets/styles/tables.css'.$this->v.'">
        ';
    }
    protected function custom_scripts(): string {
        return '
        <script src="./assets/scripts/tables.js'.$this->v.'"></script>
        ';

    }
}

$tables = new tables();

