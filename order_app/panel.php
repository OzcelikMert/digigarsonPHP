<?php
namespace order_app;
require "../matrix_library/php/auto_loader.php";

use config\database_list;
use config\db;
use matrix_library\php\operations\user;
use matrix_library\php\page\creator;
use order_app\functions\panel\set\register;
use order_app\sameparts\functions\sessions\get;
use order_app\sameparts\functions\sessions\keys;
use sameparts\php\ajax\echo_values;

class panel extends creator {
    private string $v;
    private echo_values $echo;
    public function __construct() {
        $this->v = "?v=".date("YmdHis");
        $this->page_title = "Panel";
        parent::__construct();
    }

    protected function main(): void {
        $db = new db(database_list::LIVE_MYSQL_1);
        $this->echo = new echo_values();
        $sessions = new get();

        if (user::session(keys::LANG_TAG) == null || user::session(keys::LANG_TAG) == "") {
            $sessions->LANGUAGE_TAG = "tr";
            $sessions->create();
        }

        $register = new register($db,$sessions,$this->echo,true);
        $register->check_login($db,$sessions,$this->echo);
    }


    protected function page_body(): string {
        $set_include = static::set_include(array(
            "./views/panel/pages/other.php",
            "./views/panel/pages/address/address_list.php",
            "./views/panel/pages/address/edit_address.php",
            //"./views/panel/pages/qrcode_scan.php",
            "./views/panel/pages/basket.php",
            //"./views/panel/pages/search.php",
            "./views/panel/pages/company_details.php",
            "./views/panel/pages/product_list.php",
            "./views/panel/pages/product_details.php",
            "./views/panel/pages/order_confirm.php",
            "./views/panel/pages/order_history.php",
            "./views/panel/pages/preloader.php",

            "./views/panel/pages/user/form.php"
        ));
        $main_page_include = static::set_include(array(
           // "./views/panel/main_page/top_slider.php",
           // "./views/panel/main_page/address_bar.php",
           // "./views/panel/main_page/slider-categories.php",
           // "./views/panel/main_page/companies.php",
           // "./views/panel/main_page/products.php",
        ));

        return "<div id='page'>
                <main class='container main-panel'> $main_page_include </main>
                <pop_pages>$set_include</pop_pages>
                <debug></debug>
                </div>";
    }

    protected  function custom_links(): string {
        return '
            <link rel="stylesheet" href="./assets/styles/index.css'.$this->v.'">
            <link rel="stylesheet" href="./assets/styles/panel.css'.$this->v.'">
            <link rel="stylesheet" href="./assets/styles/panel-2.css'.$this->v.'">
        ';
    }

    protected function custom_scripts(): string {
        return '
            <script src="./assets/scripts/index.js'.$this->v.'"></script>
            <script src="./assets/scripts/panel.js'.$this->v.'"></script>
            <script src="./assets/scripts/main.js'.$this->v.'"></script>
            <script src="./assets/scripts/pop_page.js'.$this->v.'"></script>
            <script src="./assets/scripts/window.js'.$this->v.'"></script>
        ';
    }
    
}
$panel = new panel();




