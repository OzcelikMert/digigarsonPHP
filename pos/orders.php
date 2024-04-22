<?php
namespace pos;
require "../matrix_library/php/auto_loader.php";

use matrix_library\php\page\creator;

class orders extends creator {
    private string $v;

    public function __construct() {
        $this->v = "?v=".date("YmdHis");
        $this->page_title = "Siparişler";
        parent::__construct();

    }
    protected function main(): void {
        session_start();
    }
    protected function page_body(): string {

        return static::set_include(array(
            "./views/orders/modals.php",
            "./views/orders/selection.php"
        ));
    }
    protected  function custom_links(): string {
        return '
            <link rel="stylesheet" href="./assets/styles/orders.css'.($this->v).'"/>
        ';
    }
    protected function custom_scripts(): string {
        return '
            <script src="./assets/scripts/orders.js'.($this->v).'"></script>
        ';
    }
}

$orders = new orders();