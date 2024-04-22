<?php
namespace manage\functions\branch_settings\set;

use config\db;
use config\table_helper\branch_payment_types as tbl;
use config\sessions;
use matrix_library\php\operations\user;
use sameparts\php\ajax\echo_values;

class post_keys{ CONST PAYMENT = "payment", TAKEAWAY = "takeaway"; }
class array_keys{ CONST ADD = "ADD", DEL = "DEL"; }


class payment_types{
    private array $payment;
    private array $takeaway;
    function __construct(db $db, sessions $sessions, echo_values &$echo){
        $echo->custom_data["post"] = $_POST;
        if ($sessions->get->BRANCH_ID > 0) {
            $this->payment = (array) user::post(post_keys::PAYMENT);
            $this->takeaway = (array) user::post(post_keys::TAKEAWAY);

            if (isset($this->payment) && is_array($this->payment) && count($this->payment) > 0)  {
                $this->add($db,$sessions,$echo);
            }
            if (isset($this->takeaway) &&  is_array($this->takeaway) && count($this->takeaway) > 0)  {
                $this->del($db,$sessions,$echo);
            }

        }
    }

    private function add(db $db, sessions $sessions, echo_values &$echo): void{
        $array = array();
        $payment_methods = (array_key_exists(array_keys::ADD,$this->payment)) ? $this->payment[array_keys::ADD]   : null;
        $enable_takeaway = (array_key_exists(array_keys::ADD,$this->takeaway)) ? $this->takeaway[array_keys::ADD] : null;

        if ($payment_methods != null){
            foreach ($payment_methods as $value){
                array_push($array, array("BRANCH_ID" => $sessions->get->BRANCH_ID, tbl::ACTIVE => 1, tbl::TYPE_ID => $value, tbl::ACTIVE_TAKE_AWAY => 0));
            }
            $db->db_insert(tbl::TABLE_NAME, $array);
        }

        if ($enable_takeaway != null){
            foreach ($enable_takeaway as $value){ array_push($array, $value); }
           $echo->custom_data["update"] = (array) $db->db_update(tbl::TABLE_NAME, [tbl::ACTIVE_TAKE_AWAY => 1], where: $db->where->equals([
                tbl::BRANCH_ID => $sessions->get->BRANCH_ID, tbl::TYPE_ID => $array
            ]));
        }
    }

    private function del(db $db, sessions $sessions, echo_values &$echo): void{
        $array = array();
        $payment_methods = (array_key_exists(array_keys::DEL,$this->payment)) ? $this->payment[array_keys::DEL]   : null;
        $disable_takeaway = (array_key_exists(array_keys::DEL,$this->takeaway)) ? $this->takeaway[array_keys::DEL] : null;

        $arr = [];
        if ($payment_methods != null){
            foreach ($payment_methods as $value){
              array_push($arr,$db->db_delete(tbl::TABLE_NAME, where: $db->where->equals([
                  tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                  tbl::TYPE_ID => $value
              ])));
            }
        }

        if ($disable_takeaway != null){
            foreach ($disable_takeaway as $value){ array_push($array, $value); }
            $echo->custom_data["update"] = (array) $db->db_update(tbl::TABLE_NAME, [tbl::ACTIVE_TAKE_AWAY => 0], where: $db->where->equals([
                tbl::BRANCH_ID => $sessions->get->BRANCH_ID, tbl::TYPE_ID => $array
            ]));
        }
    }
}
