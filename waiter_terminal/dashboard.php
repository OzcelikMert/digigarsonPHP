<?php
namespace waiter_terminal;
require "../matrix_library/php/auto_loader.php";

use matrix_library\php\page\creator;

class dashboard extends creator {
    private string $v;

    public function __construct() {
        $this->v = "?v=".date("YmdHis");
        $this->page_title = "Dashboard";
        parent::__construct();
    }
    protected function main(): void { }

    protected function page_body(): string {
        return static::set_include(array(
            "./views/dashboard/main.php",
            "./views/dashboard/table_detail.php",
            "./views/dashboard/basket.php",
            "./views/dashboard/move_table.php"
        ));

    }

    protected  function custom_links(): string {
        return '
            <link rel="stylesheet" href="../public/assets/plugins/Swiper/styles/swiper.css" />
            <link rel="stylesheet" href="./assets/styles/dashboard.css'.$this->v.'">
        ';
    }

    protected function custom_scripts(): string {
        return '
            <script src="../public/assets/plugins/Swiper/swiper.js"></script>
            <script src="./assets/scripts/dashboard.js'.$this->v.'"></script>
        ';

    }
}

$dashboard = new dashboard();

?>