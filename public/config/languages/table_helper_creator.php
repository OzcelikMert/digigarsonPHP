<?php


namespace config\languages;
require "../../../matrix_library/php/auto_loader.php";
use config\db;
use config\database_list;

class table_helper_creator{
    private db $db;

    public function __construct() {
      //  header('Content-Disposition: attachment; filename="sample.php"');
        //header('Content-Type: text/plain');
        $this->initialize();
    }

    public function initialize() : void{
       // $this->create_table_helpers();
      $this->db = new db(database_list::LIVE_MYSQL_1);

       $this->create_table_helpers();
       if(isset($_GET["table_name"])){
            $this->create_page_table_helper($_GET["table_name"]);
       }
    }

    private function create_table_helpers(): void{
        $values = array();

        $values["data"] = $this->db->db_select(
            "table_name",
            "information_schema.tables",
            "",
            "table_schema = 'pos_app'"
        )->rows;


        foreach ($values["data"] as $v){
           echo ("<a href='?table_name=".$v["table_name"]."'>".$v["table_name"]."</a><br>");
           // $this->create_page_table_helper($v["table_name"]);
        }


    }

    private function create_page_table_helper($table_name){
        $is_lang_name = true;
        $is_lang_comment = true;

        $colums = $this->db->db_select(
            "COLUMN_NAME",
            "INFORMATION_SCHEMA.COLUMNS",
            "",
            "TABLE_SCHEMA = 'pos_app' AND TABLE_NAME = '$table_name'"
        )->rows;

        $value["top"] = "<?php\nnamespace config\\table_helper;\n";
        $value["const"] = $this->tab()."const TABLE_NAME = \"$table_name\",\n";
        foreach ($colums as $c){

            if (substr($c["COLUMN_NAME"],0,5) == "name_"){
                if ($is_lang_name == false) {continue;}
                $value["const"] .= $this->tab(3)."NAME = self::TABLE_NAME.\".name_\",\n";
                $is_lang_name = false;
            }
            else if (substr($c["COLUMN_NAME"],0,8) == "comment_"){
                if ($is_lang_comment == false) {continue;}
                $value["const"] .= $this->tab(3)."COMMENT = self::TABLE_NAME.\".comment_\",\n";
                $is_lang_comment = false;
            }
            else {
                $c = $c["COLUMN_NAME"];
                $value["const"].= $this->tab(3).strtoupper($c)." = self::TABLE_NAME.\".$c\",\n";
            }

        }
        $const = substr($value["const"],0,-1);

        $value["class"] =
            $value["top"].$this->tab().
            "class $table_name extends same_columns {\n".
            $this->tab().$const;

       // echo ($value["top"].$value["class"]);
        $this->file_create_php($table_name,$value["class"]);
    }


    private function tab($count = 1){
        $tabs = "";
        for ($i=0;$i<=$count;$i++){
            $tabs .="    ";
        }
        return $tabs;
    }

    private function file_create_php($name,$data) : void{
        $create_file = fopen("../table_helper/".$name.".php", "wr") or die("Unable to open file!");
        $data = substr($data,0,-2);
        fwrite($create_file, $data."\";\n}");
        fclose($create_file);
        echo  "YES";
    }

}
$file_create = new table_helper_creator();

// SELECT table_name FROM information_schema.tables WHERE table_schema = 'pos_app'  -> get tables name
// SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'pos_app' AND TABLE_NAME = 'order_products' -> COLUM NAMES


