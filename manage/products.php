<?php
namespace manage;
require "../matrix_library/php/auto_loader.php";

use matrix_library\php\page\creator;

class products extends creator {
    private string $v = "";

    public function __construct() {
        $this->v = "?v=".date("YmdHis");
        $this->page_title = "Products";
        parent::__construct();
    }

    protected function main(): void {}

    protected function page_body(): string {
        return parent::set_include(array(
            "./views/products/modals.php",
            "./views/products/list.php"
        ));
    }

    protected function custom_links(): string {
        return "
            <!--link rel='stylesheet' href='../public/assets/plugins/JQuery/styles/jquery-ui.min.css'/-->
            <link rel='stylesheet' href='../public/assets/plugins/JQuery/styles/jquery.dataTables.min.css'/>
            <link rel='stylesheet' href='../public/assets/plugins/JQuery/styles/jquery.treeTable.css'/>
            <link rel='stylesheet' href='./../public/assets/plugins/Animate/animate.min.css'>
            <link rel='stylesheet' href='../public/assets/plugins/Croper/styles/croppie.css'/>
            <link rel='stylesheet' href='./assets/styles/products.css{$this->v}'/>
        ";
    }

    protected function custom_scripts(): string {
        return "
            <script src='../public/assets/plugins/JQuery/jquery-ui.min.js'></script>
            <script src='../public/assets/plugins/JQuery/jquery.dataTables.min.js'></script>
            <script src='../public/assets/plugins/JQuery/jquery.treeTable.js'></script>
            <script src='../public/assets/plugins/Croper/croppie.min.js'></script>
            <script src='../public/assets/plugins/Croper/croper.js'></script>
            <script src='./assets/scripts/products.js{$this->v}'></script>
            
        ";
    }

}
$products = new products();