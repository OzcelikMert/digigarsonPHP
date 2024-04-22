<?php
namespace manage;
require "../matrix_library/php/auto_loader.php";

use matrix_library\php\operations\user;
use matrix_library\php\page\creator;


class list_device extends creator {
    private string $v;

    public function __construct() {
        $this->v = "?v=".date("YmdHis");
        $this->page_title = "Device List";
        parent::__construct();
    }

    protected function main(): void {

    }

    protected function page_body(): string {
         return static::set_include(array(
             "./views/list_device/top.php",
             "./views/list_device/list.php",
             "./views/list_device/modals.php"
         ));

    }
    protected  function custom_links(): string {
        return '
            <link rel="stylesheet" href="./assets/styles/list_device.css'.$this->v.'">
        ';
    }
    protected function custom_scripts(): string {
        return '
            <script src="./assets/scripts/list_device.js'.$this->v.'"></script>    
        ';

    }
}

$device_list = new list_device();

