<?php
namespace manage;
require "../matrix_library/php/auto_loader.php";

use matrix_library\php\page\creator;

class settings_integration extends creator {
    private string $v;

    public function __construct() {
        $this->v = "?v=".date("YmdHis");
        $this->page_title = "Settings Integration";
        parent::__construct();
    }
    protected function main(): void {

    }
    protected function page_body(): string {
        return static::set_include(array(
            "./views/settings_integration/modals.php",
            "./views/settings_integration/main.php",
        ));
    }
    protected  function custom_links(): string {
        return '
            <link rel="stylesheet" href="./assets/styles/settings_integration.css'.$this->v.'">
        ';
    }
    protected function custom_scripts(): string {
        return '
            <script src="./assets/scripts/settings_integration.js'.$this->v.'"></script>
        ';
    }
}

$page = new settings_integration();

?>

