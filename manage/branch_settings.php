<?php
namespace manage;
require "../matrix_library/php/auto_loader.php";

use matrix_library\php\page\creator;

class branch_settings extends creator {
    private string $v;

    public function __construct() {
        $this->v = "?v=".date("YmdHis");
        $this->page_title = "Dashboard";
        parent::__construct();
    }
    protected function main(): void {

    }
    protected function page_body(): string {
        return static::set_include(array(
            "./views/branch_settings/modals.php",
            "./views/branch_settings/main.php",
        ));
    }
    protected  function custom_links(): string {
        return '
            <link rel="stylesheet" href="./assets/styles/index.css'.$this->v.'">
        ';
    }
    protected function custom_scripts(): string {
        return '
            <script src="./assets/scripts/branch_settings.js'.$this->v.'"></script>
        ';
        return  "";

    }
}

$page = new branch_settings();

?>

