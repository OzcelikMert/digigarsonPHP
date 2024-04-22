<?php
namespace sameparts\php\db_query;

use config\db;

abstract class helper {
    protected static function check_and(string $string,$disable= false) : string{
        return (strlen($string) > 0 && !$disable) ? " and " : "";
    }

    protected static function check_where(db $db, array $parameters) : string{
        $values = array();
        foreach($parameters as $key => $value){
            if(!is_null($value)) { $values[$key] = $value; }
        }
        return $db->where->equals($values);
    }

    protected static function check_limit(array $limit) : string{
        return ($limit != [0, 0]) ? $limit[0].",".$limit[1] : "";
    }
}