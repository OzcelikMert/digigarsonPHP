<?php
namespace manage\functions\branch_settings\set;

use config\db;
use config\table_helper\branch_takeaway_address as tbl;
use config\sessions;
use matrix_library\php\operations\user;
use sameparts\php\ajax\echo_values;



class post_keys{ CONST ADD = "ADD", DEL = "DEL"; }

class edit_address{
    private array $add;
    private array $del;
    function __construct(db $db, sessions $sessions, echo_values &$echo){
        if ($sessions->get->BRANCH_ID > 0) {
            $this->add = (array) user::post(post_keys::ADD);
            $this->del = (array) user::post(post_keys::DEL);

            if (isset($this->add) && is_array($this->add) && count($this->add) > 0)  {
                $this->add($db,$sessions,$echo);
            }
            if (isset($this->del) &&  is_array($this->del) && count($this->del) > 0)  {
                $this->del($db,$sessions,$echo);
            }

        }
    }

    private function add(db $db, sessions $sessions, echo_values &$echo): void{
       $array = array();
       foreach ($this->add as $value){
           array_push($array, array("BRANCH_ID" => $sessions->get->BRANCH_ID, tbl::NEIGHBORHOOD_ID => $value));
       }
        $db->db_insert(tbl::TABLE_NAME, $array);
    }
    private function del(db $db, sessions $sessions, echo_values &$echo): void{
        $array = array();
        foreach ($this->del as $value){
            array_push($array,$value);
        }
         $db->db_delete(tbl::TABLE_NAME,
             where: $db->where->equals([
            tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
            tbl::NEIGHBORHOOD_ID => $array
        ]));

    }

}
