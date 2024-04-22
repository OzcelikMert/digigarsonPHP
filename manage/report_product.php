<?php
namespace manage;
require "../matrix_library/php/auto_loader.php";

use matrix_library\php\page\creator;

class report_product extends creator {
    private string $v;

    public function __construct() {
        $this->v = "?v=".date("YmdHis");
        $this->page_title = "Report Product";
        parent::__construct();
    }

    protected function main(): void {

    }

    protected function page_body(): string {
         return static::set_include(array(
             "./views/report_product/report_form.php",
             "./views/report_product/result_table.php"
         ));
    }

    protected  function custom_links(): string {
        return '
            <link rel="stylesheet" href="./assets/styles/report_product.css'.$this->v.'">
        ';
    }

    protected function custom_scripts(): string {
        return '
            <script src="./assets/scripts/report_product.js'.$this->v.'"></script>
        ';

    }
}

$report_product = new report_product();

?>

