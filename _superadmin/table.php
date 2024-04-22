<?php
namespace _superadmin;
use matrix_library\php\page\creator;
use sameparts\php\helper\date;

require "../matrix_library/php/auto_loader.php";

class table extends creator{

    private string $v;

    public function __construct()
    {
        $this->v = "?v=".date(date::date_type_simples()::HYPHEN_DATE_TIME);
        $this->page_title = "Masa Düzenle";
        parent::__construct();
    }

    protected function main() : void{}

    protected function page_body(): string
    {
        return static::set_include(array(
            "./view/table/index.php",
            "./view/table/modals.php"
        ));
    }

    protected function custom_links(): string
    {
        return '<link rel="stylesheet" href="./assets/styles/index.css'.$this->v.'">';
    }

    protected function custom_scripts(): string
    {
        return '<script src="./assets/scripts/table.js'.$this->v.'"></script>';
    }
}

$_table = new table();