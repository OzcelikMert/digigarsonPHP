<?php
namespace pos;
require "../matrix_library/php/auto_loader.php";

use matrix_library\php\page\creator;

class finance extends creator {
    private string $v;

    public function __construct() {
        $this->v = "?v=".date("YmdHis");
        $this->page_title = "Finance";
        parent::__construct();
    }
    protected function main(): void {
        session_start();
    }
    protected function page_body(): string {
        return '<div class="container-fluid nav-mt-show">'
            .static::set_include( array(
                "./views/finance/navigation_buttons.php",
                "./views/finance/safe.php",
                "./views/finance/invoices.php",
                "./views/finance/trusts.php",
                "./views/finance/cost.php",
                "./views/finance/modals.php"
        )).
            '</div>';

    }
    protected  function custom_links(): string {
        return '
            <link rel="stylesheet" href="./assets/styles/finance.css'.($this->v).'"/>
        ';
    }
    protected function custom_scripts(): string {
        return '
            <script src="./assets/scripts/finance.js'.$this->v.'"></script>
            <script src="./assets/scripts/printer/invoice_type/z_report.js'.$this->v.'"></script>
        ';
    }
}

$finance = new finance();
