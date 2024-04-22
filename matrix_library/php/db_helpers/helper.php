<?php
namespace matrix_library\php\db_helpers;

use JetBrains\PhpStorm\Pure;

abstract class helper extends tags{
    protected function __construct() {
        $this->where = new db_where();
        $this->case = new db_case();
        $this->join = new db_join();
    }

    /**
     * Do it <b>true</b> if u want to see sql query string
     * @var bool
     */
    public bool $show_sql = true;
    /**
     * Helps in where conditions
     * @var db_where
     */
    public db_where $where;
    /**
     * Helps in case conditions
     * @var db_case
     */
    public db_case $case;
    /**
     * Helps in join conditions
     * @var db_join
     */
    public db_join $join;

    public function as_name(string $name, string $new_name) : string {
        return "{$name} as {$new_name}";
    }

    public function count(string $column) : string{
        return "COUNT($column)";
    }

    public function length(string $column) : string{
        return "LENGTH($column)";
    }

    public function max(string $column) : string{
        return "MAX($column)";
    }

    public function min(string $column) : string{
        return "MIN($column)";
    }

    public function sum(string $column) : string{
        return "SUM($column)";
    }

    public function if_null(string $column, mixed $value) : string{
        return "IFNULL($column, $value)";
    }

    public function group_by(string | array $column) : string{
       $value = "";
        if(is_array($column)){
            foreach ($column as $item) $value .= "{$item},";
            $value = substr($value, 0, -1);
        }else{
            $value = $column;
        }
        return $value;
    }

    public function order_by(mixed $column, string $sort_type = parent::ASC) : string{
        $value = "";
        if(is_array($column)){
            foreach ($column as $item) $value .= "{$item},";
            $value = substr($value, 0, -1);
        }else{
            $value = $column;
        }
        return "{$value} {$sort_type}";
    }

    public function order_by_multi(array $strings) : string{
        $value = "";
        foreach ($strings as $string){
            $value .= $string.",";
        }
        return substr($value, 0, -1);
    }

    public function limit(array $limit) : string{
        return $limit[0].",".$limit[1];
    }

    public function substring(string $value, int $start, int $length) : string{
        return "SUBSTRING({$value}, {$start}, {$length})";
    }

    public function concat(string ...$string) : string{
        $concat = "";
        foreach ($string as $data){
            $concat .= "{$data},";
        }
        $concat = substr($concat, 0, -1);
        return "CONCAT({$concat})";
    }

    /**
     * If the entered value is a string, it converts to varchar
     * @param mixed $value
     * @return mixed
     */
    public static function convert_varchar(mixed $value) : mixed{
        return (((is_string($value) && !strpos($value, "(CASE WHEN")) || empty($value)) && !is_numeric($value)) ? "'{$value}'" : $value;
    }

    protected function check_condition(string $condition, string $condition_values) : string{
        return ($condition_values != null && strlen(trim($condition_values)) > 0) ? "$condition $condition_values" : "";
    }

    protected function check_show_sql(string &$variable, string $sql, bool $just_show_sql = true) : string{
        if($this->show_sql || $just_show_sql){
            $variable = $sql;
        }

        return $variable;
    }
}

class db_where{
    /**
     * Enter desired column values here <br>
     * Example: <b>equals(["column" => value])</b>
     * @param array $where
     * @return string
     */
    public function equals(array $where = array()) : string{
        return $this->where($where, helper::EQUALS);
    }

    /**
     * Enter desired column values here <br>
     * Example: <b>is_null(["column"])</b>
     * @param array $where
     * @return string
     */
    public function is_null(array $where = array()) : string{
        return $this->where($where, helper::IS_NULL);
    }

    /**
     * Enter desired column values here <br>
     * Example: <b>like(["column" => value])</b>
     * @param array $where
     * @return string
     */
    public function like(array $where = array()) : string{
        return $this->where($where, helper::LIKE);
    }

    /**
     * Enter unwanted column values here <br>
     * Example: <b>not_like(["column" => value])</b>
     * @param array $where
     * @return string
     */
    public function not_like(array $where = array()) : string{
        return $this->where($where, helper::NOT_LIKE);
    }

    /**
     * Enter greater than column values here <br>
     * Example: <b>greater_than(["column" => value)</b>
     * Set the <b>$equals</b> to true if the value is greater than or equal
     * @param array $where
     * @param bool $equals
     * @return string
     */
    public function greater_than(array $where = array(), bool $equals = false) : string{
        return $this->where($where, helper::GREATER_THAN.(($equals) ? helper::EQUALS : ""));
    }

    /**
     * Enter less than column values here <br>
     * Example: <b>less_than(["column", value)</b>
     * Set the <b>$equals</b> to true if the value is less than or equal
     * @param array $where
     * @param bool $equals
     * @return string
     */
    public function less_than(array $where = array(), bool $equals = false) : string{
        return $this->where($where, helper::LESS_THAN.(($equals) ? helper::EQUALS : ""));
    }

    /**
     * Enter between column values here <br>
     * Example: <b>between(["column" => [value, value2]])</b>
     * @param array $where
     * @return string
     */
    public function between(array $where = array()) : string{
        $sql = "";
        foreach ($where as $key => $value){
            $sql = " $key ".helper::BETWEEN." ".helper::convert_varchar($value[0])." ".helper::AND." ".helper::convert_varchar($value[1])." ";
        }
        return $sql;
    }

    private function where(array $where, string $condition) : string {
        $values = "";

        if(count($where) > 0) {
            $values .= " ( ";
            foreach ($where as $key2 => $value2) {
                if (is_array($value2)) {
                    $values .= " ( ";
                    foreach ($value2 as $key3 => $value3) {
                        $key3 = (is_numeric($key3)) ? $key2 : $key3;
                        $values .= ($condition == helper::IS_NULL)
                            ? " {$value3} {$condition} "
                            : " {$key3} {$condition} ".helper::convert_varchar($value3)." ";
                        $values .= helper:: OR;
                    }
                    $values = substr($values, 0, -2);
                    $values .= " ) ";
                } else {
                    $values .= ($condition == helper::IS_NULL)
                        ? " {$value2} {$condition} "
                        : " {$key2} {$condition} ".helper::convert_varchar($value2)." ";
                }
                $values .= helper::AND;
            }
            $values = substr($values, 0, -3);
            $values .= " ) ";
        }

        return $values;
    }
}

class db_case{
    /**
     * Enter desired column values here <br>
     * Example: <b>equals(["column" => value], "then value", "else value")</b>
     * @param array $when
     * @param mixed $then
     * @param mixed $else
     * @return string
     */
    public function equals(array $when, mixed $then, mixed $else = null) : string{
        return $this->case($when, $then, $else, helper::EQUALS);
    }


    /**
     * Enter desired column values here <br>
     * Example: <b>like(["column" => value], "then value", "else value")</b>
     * @param array $when
     * @param mixed $then
     * @param mixed $else
     * @return string
     */
    public function like(array $when, mixed $then, mixed $else = null) : string{
        return $this->case($when, $then, $else, helper::LIKE);
    }

    /**
     * Enter unwanted column values here <br>
     * Example: <b>not_like(["column" => value], "then value", "else value")</b>
     * @param array $when
     * @param mixed $then
     * @param mixed $else
     * @return string
     */
    public function not_like(array $when, mixed $then, mixed $else = null) : string{
        return $this->case($when, $then, $else, helper::NOT_LIKE);
    }

    /**
     * Enter greater than column values here <br>
     * Example: <b>greater_than(["column" => value], "then value", "else value")</b>
     * Set the <b>$equals</b> to true if the value is greater than or equal
     * @param array $when
     * @param mixed $then
     * @param mixed $else
     * @param bool $equals
     * @return string
     */
    public function greater_than(array $when, mixed $then, mixed $else = null, bool $equals = false) : string{
        return $this->case($when, $then, $else, helper::GREATER_THAN.(($equals) ? helper::EQUALS : ""));
    }

    /**
     * Enter less than column values here <br>
     * Example: <b>less_than(["column" => value], "then value", "else value")</b>
     * Set the <b>$equals</b> to true if the value is less than or equal
     * @param array $when
     * @param mixed $then
     * @param mixed $else
     * @param bool $equals
     * @return string
     */
    public function less_than(array $when, mixed $then, mixed $else = null, bool $equals = false) : string{
        return $this->case($when, $then, $else, helper::LESS_THAN.(($equals) ? helper::EQUALS : ""));
    }

    private function case(array $when, mixed $then, mixed $else, string $condition) : string {
        $values = "";

        $values .= " (CASE WHEN ";
        foreach ($when as $key => $value) $values .= " {$key} {$condition} {$value}";
        $values .= " THEN {$then} ";
        $values .= " ELSE {$else} ";
        $values .= " END) ";

        return $values;
    }
}

class db_join{
    /**
     * Enter desired column values here <br>
     * Example: <b>inner("TABLE_NAME" => ["column1" => "column2"])</b>
     * @param array $join
     * @return string
     */
    public function inner(array $join = array()) : string{
        return $this->join($join, helper::INNER." ".helper::JOIN);
    }

    /**
     * Enter desired column values here <br>
     * Example: <b>left("TABLE_NAME" => ["column1" => "column2"])</b>
     * @param array $join
     * @return string
     */
    public function left(array $join = array()) : string{
        return $this->join($join, helper::LEFT." ".helper::JOIN);
    }

    /**
     * Enter desired column values here <br>
     * Example: <b>right("TABLE_NAME" => ["column1" => "column2"])</b>
     * @param array $join
     * @return string
     */
    public function right(array $join = array()) : string{
        return $this->join($join, helper::RIGHT." ".helper::JOIN);
    }

    private function join(array $join, string $condition) : string {
        $values = "";

        if(count($join) > 0) {
            foreach ($join as $key => $value) {
                $values .= " {$condition} {$key} ON ";
                foreach ($value as $key2 => $value2) {
                    $values .= " {$key2} = {$value2} ".helper::AND;
                }
                $values = substr($values, 0, -3);
            }
        }

        return $values;
    }
}