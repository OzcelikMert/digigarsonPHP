<?php
namespace language;
require "../matrix_library/php/auto_loader.php";

use matrix_library\php\page\creator;

class index extends creator {
    private string $v;

    public function __construct() {
        $this->v = "?v=".date("YmdHis");
        $this->page_title = "Index";
        parent::__construct();
    }

    protected function main(): void {}

    protected function page_body(): string {
        return static::set_include(array(
            "./views/index/form.php"
        ));

    }
    protected  function custom_links(): string {
        return '
            <link rel="stylesheet" href="./assets/styles/index.css'.$this->v.'">
        ';
    }
    protected function custom_scripts(): string {
        return '
            <script src="./assets/scripts/index.js'.$this->v.'"></script>    
        ';

    }
}

$index = new index();

?>

