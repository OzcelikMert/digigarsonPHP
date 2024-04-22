<?php
namespace sameparts\php\helper;

use sameparts\php\helper\page_names\pos;
use sameparts\php\helper\page_names\manage;

class page_names {
    public static function MANAGE() : manage{
        return (new manage());
    }

    public static function POS() : pos{
        return (new pos());
    }
}