<?php
namespace language;
require "../matrix_library/php/auto_loader.php";

use matrix_library\php\page\creator;

class login extends creator {
    private string $v;

    public function __construct() {
        $this->v = "?v=".date("YmdHis");
        $this->page_title = "Login";
        parent::__construct();
    }

    protected function main(): void {}

    protected function page_body(): string {
        return static::set_include(array(
            "./views/login/panel.php"
        ));

    }
    protected  function custom_links(): string {
        return '
            <link rel="stylesheet" href="./assets/styles/login.css'.$this->v.'">
        ';
    }
    protected function custom_scripts(): string {
        return '
            <script src="./assets/scripts/login.js'.$this->v.'"></script>    
        ';

    }
}

$login = new login();

?>

