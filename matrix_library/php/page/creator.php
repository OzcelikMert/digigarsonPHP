<?php
namespace matrix_library\php\page;

abstract class creator extends elements{
    protected function __construct() {
        parent::__construct();
        $this->main();
        self::page_launch();
    }

    /**
     * Calling all set functions for html page.<br>
     * @return void
     */
    private function page_launch() : void{
        parent::definer();
        parent::page_skeleton();
    }
    
    /**
     * You can write extra codes in this
     * @return void
     */
    protected function main() : void { }
}