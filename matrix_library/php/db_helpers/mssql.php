<?php
namespace matrix_library\php\db_helpers;
/* Database Helper MSSQL */
class mssql{
    function __construct($connect){
        $this->connect = $connect;
    }
    
    /* Variables */
    private $connect = null;
    public $show_sql = false;
    /* end Variables */

    /* Functions */
    // Get DB Type
    function getDBType(){
        return "mssql";
    }
    // Database Insert
    function dbInsert($table_name, $data){
        $values = array();
        $values["status"] = false;
        $values["message"] = "";
        try{
            $sql_insert = "";
            $sql_values = "";

            foreach ($data as $key => $value) {
                $sql_insert .= "$key,";
                $sql_values .= $this->checkAndConvertVarchar($value).",";
            }

            if(strlen($sql_insert) > 0 && strlen($sql_values) > 0){
                // Clear last character => sql_insert and sql_values
                $sql_insert = substr($sql_insert, 0, -1);
                $sql_values = substr($sql_values, 0, -1);

                $sql = "insert into $table_name($sql_insert) values ($sql_values);";
                $values = $this->checkShowSQL($values, $sql);
                if(sqlsrv_query($this->connect, $sql))
                    $values[ "status"] = true;
                else
                    $values["message"] = "MSSQL error description: ".json_encode(sqlsrv_errors());
            }
        }catch(Exception $exception){ $values["message"] = "Function error description:".$exception; }

        return $values;
    }
    // Database Select
    function dbSelect($columns, $table_name, $joins = "", $where = "", $group_by = "", $order_by = "", $limit = 0, $union_columns, $union_table_name, $union_joins = "", $union_where = "", $union_group_by = "", $union_order_by = "", $union_limit = ""){
        $values = array();
        $values["status"] = false;
        $values["message"] = "";
        $values["rows"] = array();
        try{
            $sql = "select ".(($limit > 0) ? "TOP($limit)" : "")." $columns from $table_name $joins ".$this->checkCondition("where", $where)." ".$this->checkCondition("group by", $group_by)." ".$this->checkCondition("order by", $order_by).(
                (strlen($union_columns) > 0) ? " union select ".(($limit > 0) ? "TOP($limit)" : "")." $union_columns from $union_table_name $union_joins ".$this->checkCondition("where", $union_where)." ".$this->checkCondition("group by", $union_group_by)." ".$this->checkCondition("order by", $union_order_by) : "");
            $values = $this->checkShowSQL($values, $sql);
            $query = sqlsrv_query($this->connect, $sql);
            if($query){
                $values["status"] = true;
                $count = 0;
                while($rows = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)){
                    $values["rows"][$count] = $rows;
                    $count++;
                }
            }else{
                $values["message"] = "MSSQL error description: ".json_encode(sqlsrv_errors());
            }
        }catch(Exception $exception){ $values["message"] = "Function error description:".$exception; }

        return $values;
    }
    // Database Update
    function dbUpdate($table_name, $columns, $joins = "", $where = "", $group_by = ""){
        $values = array();
        $values["status"] = false;
        $values["message"] = "";
        try{
            $sql = "update $table_name set $columns ".$this->checkCondition("from", "$joins")." ".$this->checkCondition("where", $where)." ".$this->checkCondition("group by", $group_by)."";
            $values = $this->checkShowSQL($values, $sql);
            if(sqlsrv_query($this->connect, $sql)){
                $values["status"] = true;
            }else{
                $values["message"] = "MSSQL error description: ".json_encode(sqlsrv_errors());
            }
        }catch(Exception $exception){ $values["message"] = "Function error description:".$exception; }

        return $values;
    }
    // Database Delete
    function dbDelete($table_name, $joins = "", $where  = "", $order_by  = ""){
        $values = array();
        $values["status"] = false;
        $values["message"] = "";
        try{
            $sql = "delete from $table_name $joins ".$this->checkCondition("where", $where)." ".$this->checkCondition("order by", $order_by);
            $values = $this->checkShowSQL($values, $sql);
            if(sqlsrv_query($this->connect, $sql)){
                $values["status"] = true;
            }else{
                $values["message"] = "MSSQL error description: ".json_encode(sqlsrv_errors());
            }
        }catch(Exception $exception){ $values["message"] = "Function error description:".$exception; }

        return $values;
    }
    // Check And Convert String
    private function checkAndConvertVarchar($string){
        if(is_string($string))
            $string = "'".$string."'";

        return $string;
    }
    // Check Condition
    private function checkCondition($condition, $condition_values){
        $value = "";

        $value = ($condition_values != null && strlen($condition_values) > 0) ? "$condition $condition_values" : "";
    
        return $value;
    }
    // Check Show SQL
    private function checkShowSQL($values, $sql){
        if($this->show_sql){
            $values["sql"] = $sql;
        }

        return $values;
    }
    /* end Functions */
}
/* end Database Helper MSSQL */
?>