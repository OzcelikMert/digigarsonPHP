<?php
namespace manage;
require "../matrix_library/php/auto_loader.php";

use matrix_library\php\operations\user;
use matrix_library\php\page\creator;
use pos\sameparts\functions\sessions\keys;


class dashboard extends creator {
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
             "./views/dashboard/top_charts.php",
             "./views/dashboard/middle_charts.php",
             "./views/dashboard/last_payments.php",
             "./views/dashboard/last_orders.php"
         ));

    }
    protected  function custom_links(): string {
        return '
            <link rel="stylesheet" href="./assets/styles/index.css'.$this->v.'">
        ';
    }
    protected function custom_scripts(): string {
        return '
            <script src="./../public/assets/plugins/Chart/Chart.bundle.min.js"></script>
            <script src="./../public/assets/plugins/Chart/chart.min.js"></script>
            <script src="./../public/assets/plugins/JQuery/jquery.sparkline.js"></script>
            <script src="./../public/assets/plugins/Chart/chart-spark.js"></script>
            <script src="./assets/scripts/dashboard.js'.$this->v.'"></script>
        ';

    }
}

$dashboard = new dashboard();

?>

