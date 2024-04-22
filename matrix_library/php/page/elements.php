<?php
namespace matrix_library\php\page;

use matrix_library\php\operations\server;
use RuntimeException;

abstract class elements extends helper {
    /**
     * The name of the page that appear on the browser tabs.
     * Default name <b>Blank Page</b>.
     * @var string
     */
    private string $page_name = "";
    protected string $page_title = "";

    protected function __construct(){
        $this->page_name = str_replace(".php", "", basename($_SERVER['PHP_SELF']));
        $this->page_title = (empty($this->page_title)) ? ucfirst($this->page_name) : $this->page_title;
    }

    /**
     * Default structure of page. <b>(Defined head and scripts tools)</b>
     * @return void
     */
    protected function page_skeleton() : void{
        require "./tools/page_skeleton.php";
    }

    /**
     * Created for use variables of in page
     * * @return void
     */
    protected function definer() : void{
        define("page_name", $this->page_name);
        define("page_title", $this->page_title);
        define("page_body", $this->page_body());
        define("custom_links", $this->custom_links());
        define("custom_scripts", $this->custom_scripts());
    }

    /**
     * This is body elements included in web page
     * @return string
     */
    protected function page_body() : string { throw new RuntimeException("Unimplemented"); }

    /**
     * Call and create this function if u want to call custom styles files
     * @return string
     */
    protected function custom_links() : string { return ""; }

    /**
     * Call and create this function if u want to call custom scripts files
     * @return string
     */
    protected function custom_scripts() : string { return ""; }
}