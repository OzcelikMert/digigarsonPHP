<?php
namespace pos;
require "../matrix_library/php/auto_loader.php";

use matrix_library\php\page\creator;

class settings extends creator {
    private string $v;

    public function __construct() {
        $this->v = "?v=".date("YmdHis");
        $this->page_title = "Settings";
        parent::__construct();
    }
    protected function main(): void {

    }
    protected function page_body(): string {
        $patch = "./views/settings";

        return static::set_include( array(
                "$patch/modals.php",
                "$patch/index.php"
        ));
    }
    protected  function custom_links(): string {
        return '
            <link rel="stylesheet" href="./assets/styles/settings.css'.($this->v).'"/>
        ';
    }
    protected function custom_scripts(): string {
        return '
            <script src="./assets/scripts/settings.js'.($this->v).'"></script>
        ';
    }
}

$settings = new settings();
