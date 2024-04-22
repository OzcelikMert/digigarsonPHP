<?php
namespace pos\functions\products\set;

use config\db;
use config\sessions;
use config\settings;
use config\type_tables_values\product_option_group_types as option_types;
use config\table_helper\product_option as tbl;
use config\table_helper\product_option_items as tbl2;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use sameparts\php\ajax\echo_values;
use sameparts\php\helper\date;

class post_keys {
    const SETTINGS = "settings",
        ADD_ITEMS = "add_items",
        REMOVE_ITEMS = "delete_items",
        UPDATE_ITEMS = "update_items",
        INSERT_ID = "insert_id";

}
class settings_keys {
    const LIMIT = "limit",
        NAME = "name",
        SEARCH_NAME = "search_name",
        TYPE = "type",
        OPTION_ID = "option_id";
}
class items_keys {
    const
        ID = "id",
        NAME = "name",
        PRICE = "price",
        QUANTITY = "quantity",
        ACTIVE = "active";
}



class insert_option
{
    public function __construct(db $db, sessions $sessions, echo_values &$echo){
        $this->check_and_save($db,$sessions,$echo);
    }

    public function check_and_save(db $db,sessions $sessions,echo_values &$echo){
        $settings = user::post(post_keys::SETTINGS);
        $add_items = user::post(post_keys::ADD_ITEMS);
        $remove_items = user::post(post_keys::REMOVE_ITEMS);
        $update_items = user::post(post_keys::UPDATE_ITEMS);

        if (!(variable::is_empty(
            $settings[settings_keys::OPTION_ID],
            $settings[settings_keys::NAME],
            $settings[settings_keys::SEARCH_NAME],
            $settings[settings_keys::TYPE],
            $settings[settings_keys::LIMIT]))) {

            $selected = 0;

            if ($settings[settings_keys::OPTION_ID] == 0 && $echo->status){
                $echo->custom_data["add_option"] = (array)$this->add_option($db,$sessions);
                $echo->error_code = settings::error_codes()::SUCCESS;
            }else {
                $echo->custom_data["update_option"] = (array)$this->update($db,$sessions);
                $echo->custom_data["update_option_type"] = (array)$this->update_options_type($db,$sessions);
                $echo->error_code = settings::error_codes()::REGISTERED_VALUE;
            }

            if(is_array($add_items) && count($add_items) > 0 ) {
                foreach ($add_items as $item)  $this->check_items($item,$settings[settings_keys::TYPE],$selected,$echo);
               if ($echo->status){
                   if ($settings[settings_keys::OPTION_ID] == 0){
                       $echo->custom_data["add_option_items"] = (array)$this->add_option_items($db,$sessions);
                   }else if ($settings[settings_keys::OPTION_ID] > 0){
                       $echo->custom_data["add_option_items"] = (array)$this->add_option_items($db,$sessions,$settings[settings_keys::OPTION_ID]);
                   }
               }
            }

            if(is_array($update_items) && count($update_items) > 0 && $settings[settings_keys::OPTION_ID] > 0) {
                foreach ($update_items as $item) $this->check_items($item, $settings[settings_keys::TYPE], $selected, $echo);
                if ($echo->status){
                    $echo->custom_data["update_option_items"] = (array)$this->update_options($db,$sessions);
                }
            }

            if (is_array($remove_items) && count($remove_items) > 0 && $settings[settings_keys::OPTION_ID] > 0) {
                if ($echo->status){
                    $echo->custom_data["delete_option_items"] = (array)$this->remove_option_item($db,$sessions);
                }
            }

        }else { $echo->error_code = settings::error_codes()::EMPTY_VALUE; }
        if($echo->error_code != settings::error_codes()::SUCCESS && $echo->error_code != settings::error_codes()::REGISTERED_VALUE ) $echo->status = false;
    }

    private function check_items(&$item, $type, &$selected, echo_values &$echo){

        if (!(variable::is_empty(
            $item[items_keys::ID],
            $item[items_keys::NAME],
            $item[items_keys::PRICE],
            $item[items_keys::QUANTITY],
            $item[items_keys::ACTIVE])))
        {

            $selected += ($item[items_keys::ACTIVE] == 1) ? 1 : 0;
            switch ($type){
                case option_types::MATERIALS:
                    $item[items_keys::PRICE] = 0;
                    $item[items_keys::QUANTITY] = 0;
                    $item[items_keys::ACTIVE] = 0;
                    break;
                case option_types::SINGLE_SELECT:
                    $item[items_keys::QUANTITY] = 0;
                    if (!($selected <= 1)) $echo->error_code = settings::error_codes()::INCORRECT_DATA;
                    break;
                case option_types::MULTI_SELECT:
                    $item[items_keys::QUANTITY] = 0;
                    break;
                case option_types::QUANTITY:
                    if ($item[items_keys::QUANTITY] <= 0) $echo->error_code = settings::error_codes()::INCORRECT_DATA;
                    break;
            }
        }
        if($echo->error_code != settings::error_codes()::SUCCESS && $echo->error_code != settings::error_codes()::REGISTERED_VALUE ) {$echo->status = false; }
    }
    private function add_option(db $db, sessions $sessions) : results{
        $settings = user::post(post_keys::SETTINGS);

        $result = $db->db_insert(
            tbl::TABLE_NAME,
            array(
                tbl::BRANCH_ID  => $sessions->get->BRANCH_ID,
                tbl::DATE => date::get(),
                tbl::NAME.$sessions->get->LANGUAGE_TAG => $settings[settings_keys::NAME],
                tbl::SEARCH_NAME   => $settings[settings_keys::SEARCH_NAME],
                tbl::TYPE   => $settings[settings_keys::TYPE],
                tbl::SELECTION_LIMIT   => $settings[settings_keys::LIMIT]
            )
        );

        user::post(post_keys::INSERT_ID, $result->insert_id);
        return $result;
    }
    private function add_option_items(db $db, sessions $sessions,$option_id = 0) : results{
        $data = array();
        $add_items = user::post(post_keys::ADD_ITEMS);
        $option_id = ($option_id == 0) ? user::post(post_keys::INSERT_ID) : $option_id;
        foreach ($add_items as $value){
            array_push(
                $data,
                array(
                    tbl2::NAME.$sessions->get->LANGUAGE_TAG => $value[items_keys::NAME],
                    tbl2::DATE       => date::get(),
                    tbl2::BRANCH_ID  => $sessions->get->BRANCH_ID,
                    tbl2::OPTION_ID  => $option_id,
                    tbl2::IS_DEFAULT => $value[items_keys::ACTIVE],
                    tbl2::PRICE      => (float)$value[items_keys::PRICE],
                    tbl2::QUANTITY   => $value[items_keys::QUANTITY],
                )
            );
        }
        return $db->db_insert(tbl2::TABLE_NAME, $data);
    }
    private function update(db $db, sessions $sessions) : results{
        $settings = user::post(post_keys::SETTINGS);
        return $db->db_update(
            tbl::TABLE_NAME,
            array(
                tbl::NAME.$sessions->get->LANGUAGE_TAG => $settings[settings_keys::NAME],
                tbl::SEARCH_NAME   => $settings[settings_keys::SEARCH_NAME],
                tbl::TYPE   => $settings[settings_keys::TYPE],
                tbl::SELECTION_LIMIT   => $settings[settings_keys::LIMIT]
            ),
            where: $db->where->equals(array(
                tbl::BRANCH_ID => $sessions->get->BRANCH_ID, tbl::ID => $settings[settings_keys::OPTION_ID]
            ))
        );
    }

    private function update_options_type(db $db, sessions $sessions): results{
        $settings = user::post(post_keys::SETTINGS);
        $data = array();

        switch ($settings[settings_keys::TYPE]){
            case option_types::MATERIALS:
                $data =  array(tbl2::PRICE => 0, tbl2::QUANTITY => 0, tbl2::IS_DEFAULT => 0);
               break;
            case option_types::MULTI_SELECT: case option_types::SINGLE_SELECT:
                $data =  array(tbl2::QUANTITY => 0);
                break;
        }

        return $db->db_update(
             tbl2::TABLE_NAME,
             $data,
             where: $db->where->equals(array(
                 tbl2::BRANCH_ID => $sessions->get->BRANCH_ID,
                 tbl2::OPTION_ID => $settings[settings_keys::OPTION_ID]
         )));

    }


    private function update_options(db $db, sessions $sessions) : array{
        $update_items = user::post(post_keys::UPDATE_ITEMS);
        $return = array();

        foreach ($update_items as $item){
           $data = null;
           $data = $db->db_update(
                tbl2::TABLE_NAME,
                array(
                    tbl2::NAME.$sessions->get->LANGUAGE_TAG => $item[items_keys::NAME],
                    tbl2::PRICE => $item[items_keys::PRICE],
                    tbl2::QUANTITY => $item[items_keys::QUANTITY],
                    tbl2::IS_DEFAULT => $item[items_keys::ACTIVE],
                ),
                where: $db->where->equals(array(
                tbl2::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl2::ID => $item[items_keys::ID]
                ))
            );
            array_push($return,$data);
        }
        return $return;
    }
    private function remove_option_item(db $db, sessions $sessions) : array{
        $remove_items = user::post(post_keys::REMOVE_ITEMS);
        $return = array();

        foreach ($remove_items as $item){
            $data = null;
            $data = $db->db_update(
                tbl2::TABLE_NAME,
                array(tbl2::IS_DELETED => 1),
                where: $db->where->equals(array(
                tbl2::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl2::ID => $item[items_keys::ID]
            )));
            array_push($return,$data);
        }
        return $return;
    }
}
