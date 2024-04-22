<?php
namespace sameparts\php\helper;

class variable_filters {
    public static function phone($variable) : array {
        preg_match('/(\+\d{1,2}\s)?\(?\d{3}\)?[\s.-]?\d{3}[\s.-]?\d{4}$/i', $variable, $phone);
        return $phone;
    }
}