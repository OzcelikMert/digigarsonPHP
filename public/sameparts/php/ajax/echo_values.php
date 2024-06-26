<?php
namespace sameparts\php\ajax;

use config\settings;

class echo_values {
    public array $custom_data = array();
    public array $rows = array();
    public bool $status = true;
    public int $error_code;
    public string $message = "";

    function __construct() {
        $this->error_code = settings::error_codes()::SUCCESS;
    }

    /**
     * Echoes by converting data to json
     * @param int $json_flag
     */
    public function return(int $json_flag = JSON_NUMERIC_CHECK) : void{
        echo json_encode(
            array(
                "custom_data" => $this->custom_data,
                "status" => $this->status,
                "error_code" => $this->error_code,
                "message" => $this->message,
                "rows" => $this->rows
            )
        , $json_flag);
    }

    /**
     * Auto fills for this class variables
     * @param array $data
     * @param bool $auto_return
     * @param int $json_flag
     */
    public function autofill(
        array $data,
        bool $auto_return = false,
        int $json_flag = JSON_NUMERIC_CHECK
    ) : void {
        foreach ($data as $key => $value){
            switch ($key){
                case "status": $this->status = $value; break;
                case "message": $this->message = $value; break;
                case "error_code": $this->error_code = $value; break;
                case "rows": $this->rows; break;
                default: $this->custom_data[$key] = $value; break;
            }
        }
        if($auto_return) $this->return($json_flag);
    }
}