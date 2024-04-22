<?php

namespace _superadmin;

use matrix_library\php\page\creator;
use sameparts\php\helper\date;
require "../matrix_library/php/auto_loader.php";

class index extends creator{

    private string $v;

    public function __construct()
    {
        $this->v = "?v=".date(date::date_type_simples()::HYPHEN_DATE_TIME);
        $this->page_title = "Super Admin";
        parent::__construct();
    }

    protected function main() : void{}

    protected function page_body(): string
    {
        return  static::set_include(array(
            "./view/index/index.php",
            "./view/index/modals.php"
        ));
    }

    protected function custom_links(): string
    {
        return '<link rel="stylesheet" href="assets/styles/index.css'.$this->v.'">';
    }

    protected function custom_scripts(): string
    {
        return '<script src="./assets/scripts/branch.js'.$this->v.'"></script>'
        ;
    }
}
$_index = new index();