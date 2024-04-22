<?php
namespace config\languages;
require "../../../matrix_library/php/auto_loader.php";
use config\db;
use config\database_list;

class creator{
    private db $db;
    private $ext;

    public function __construct() {
        $this->db = new db(database_list::LIVE_MYSQL_1);
    }

    public function initialize($ext) : void{
        $this->ext = "_".$ext;
        $this->create_languages();

    }

    private function create_languages(): void{
        $values = array();
        $values["file"] = "";

        $values["data"] = $this->db->db_select(
            "*",
            "translate",
            "",
            "",
            "",
            "const_name ASC"
        );

        foreach($values["data"]->rows  as $row){
            $values["file"] .= $this->add_const($row["const_name"],$row["name".$this->ext]);
        }

        $this->file_create_php("language$this->ext.js",$values["file"]);

    }

    private function add_const($const_name,$name): string{
        $name = ($name == null || $name == "") ? "[$const_name]" : $name;
        return "        ".strtoupper(str_replace(" ","_",$const_name)).":'$name',\n";
    }

    private function file_create_php($name,$data) : void{
        $create_file = fopen("../../assets/config/languages/".$name, "wr") or die("Unable to open file!");
        $data = substr($data,0,-2);
        $txt = "let language = {\n    data:{\n{$data}\r\n    }\n} ";
        fwrite($create_file, $txt);
        fclose($create_file);
        echo  "YES";
    }

}
$file_create = new creator();
$file_create->initialize("tr");
$file_create->initialize("en");

