<?php
namespace manage;
require "../matrix_library/php/auto_loader.php";

use matrix_library\php\page\creator;

class report_safe extends creator {
    private string $v;

    public function __construct() {
        $this->v = "?v=".date("YmdHis");
        $this->page_title = "Report Safe";
        parent::__construct();
    }

    protected function main(): void {

    }

    protected function page_body(): string {
         return static::set_include(array(
             "./views/report_safe/navigation_buttons.php",
             "./views/report_safe/safe.php",
             "./views/report_safe/safe_list.php",
             "./views/report_safe/invoices.php",
             "./views/report_safe/trusts.php",
             "./views/report_safe/cost.php",
             "./views/report_safe/modals.php"
         ));

    }

    protected  function custom_links(): string {
        return '
            <link rel="stylesheet" href="./assets/styles/report_safe.css'.$this->v.'">
        ';
    }

    protected function custom_scripts(): string {
        return '
            <script src="./assets/scripts/report_safe.js'.$this->v.'"></script>
        ';

    }
}

$report_safe = new report_safe();

?>

