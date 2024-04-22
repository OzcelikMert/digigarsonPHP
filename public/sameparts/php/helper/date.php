<?php
namespace sameparts\php\helper;

use sameparts\php\helper\date\date_type_simples;
use sameparts\php\helper\date\date_types;

class date {
    public static function date_types() : date_types{ return (new date_types()); }

    public static function date_type_simples() : date_type_simples{ return (new date_type_simples()); }

    public static function get(string $date_type = "", int $timestamp = 0) : string {
        $date_type = (empty($date_type)) ? self::date_type_simples()::HYPHEN_DATE_TIME : $date_type;
        return ($timestamp > 0) ? date($date_type, $timestamp) : date($date_type);
    }
}