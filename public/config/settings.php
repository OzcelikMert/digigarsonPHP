<?php
namespace config;

use config\settings\application_names;
use config\settings\error_codes;
use config\settings\paths;
use config\settings\set_types;

class settings {
    public static function error_codes() : error_codes {
        return (new error_codes());
    }

    public static function paths() : paths {
        return (new paths());
    }

    public static function set_types() : set_types{
        return (new set_types());
    }

    public static function application_names() : application_names {
        return (new application_names());
    }
}