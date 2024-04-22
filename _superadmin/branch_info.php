<?php

namespace _superadmin;

use matrix_library\php\page\creator;
use sameparts\php\helper\date;
require "../matrix_library/php/auto_loader.php";

class branch_info extends creator{

    private string $v;


    public function __construct()
    {
        $this->v = "?v=".date(date::date_type_simples()::HYPHEN_DATE_TIME);
        $this->page_title = "Index";
        parent::__construct();
    }

    protected function main() : void{}

    protected function page_body(): string
    {
        return  static::set_include(array(
            "./view/branch/branch_info.php",
            "./view/branch/modals.php"
        ));
    }

    protected function custom_links(): string
    {
        return '
          <link rel="stylesheet" href="assets/styles/index.css'.$this->v.'">
          <link rel="stylesheet" href="../public/assets/plugins/Croper/styles/croppie.css"/>
          ';
    }

    protected function custom_scripts(): string
    {
        return '
            <script src="./assets/scripts/branch_info.js'.$this->v.'"></script>  
   
        ';
    }
}

$_branch_info = new branch_info();