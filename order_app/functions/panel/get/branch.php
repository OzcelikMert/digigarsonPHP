<?php
namespace order_app\functions\panel\get;

use config\db;
use config\settings;
use config\table_helper\order_types;
use config\type_tables_values\branch_table_types;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use order_app\sameparts\functions\sessions\get;
use order_app\sameparts\functions\sessions\keys;
use pos\sameparts\functions\get_types;
use sameparts\php\ajax\echo_values;
use config\table_helper\branch_tables as tbl;
use config\table_helper\products as tbl2;
use config\table_helper\product_option as tbl4;
use config\table_helper\product_option_items as tbl5;
use config\table_helper\branch_sections as tbl6;
use config\table_helper\table_section_types as tbl7;
use sameparts\php\db_query\address;
use sameparts\php\db_query\branch_info;
use sameparts\php\db_query\branch_payment_types;
use sameparts\php\db_query\branch_users;
use sameparts\php\db_query\notification;
use sameparts\php\db_query\products;
use sameparts\php\db_query\surveys;

class post_keys {
    const TYPE = "type",
         URL = "url";
}
class login_types {
    const GET_ALL = 1;
}

class branch{
    function __construct(db $db, get $sessions, echo_values &$echo){
        switch ((int)user::post(post_keys::TYPE)){
            case login_types::GET_ALL:
                $result = null;
                if ($echo->status){

                    $url = user::post(post_keys::URL);
                    if($url != "" && $url != null) {
                        $result = $this->get_table_values_for_url($db, $sessions, user::post(post_keys::URL));
                    }else {
                        $echo->status = false;
                    }

                    if ($echo->status && count($result->rows) > 0){
                        $branch_id      = $result->rows[0]["branch_id"];
                        $table_type     = $result->rows[0]["type"];
                        $table_no       = $result->rows[0]["no"];
                        $section        = $result->rows[0]["section_id"];
                        $section_name   = $result->rows[0]["section_name"];

                        user::session(keys::SELECT_BRANCH_ID, $result->rows[0]["branch_id"]);
                        user::session(keys::SELECT_BRANCH_TABLE_ID, $result->rows[0]["id"]);
                        user::session(keys::SELECT_BRANCH_TABLE_TYPE, $result->rows[0]["type"]);

                        $echo->rows["BRANCH"] =  branch_users::get_branch_info($db,$branch_id,mobile: true)->rows;
                        $echo->rows["BRANCH"][0]["table_no"] = $table_no;
                        $echo->rows["BRANCH"][0]["section"] = $section;
                        $echo->rows["BRANCH"][0]["section_name"] = $section_name;
                        $echo->rows["BRANCH"][0]["table_type"] = $table_type;

                        if ($table_type !== branch_table_types::TAKE_AWAY && ($echo->rows["BRANCH"][0]["ip_block"] == 1 && ($echo->rows["BRANCH"][0]["ip"] !== user::get_ip_address()))) {
                            $echo->rows["BRANCH"][0]["table_type"] = branch_table_types::DIGITAL_MENU;
                            $echo->error_code = settings::error_codes()::IP_BLOCK;
                        }

                        $table_type = ($table_type == branch_table_types::DIGITAL_MENU || $table_type == branch_table_types::TABLE_WITHOUT_SESSION) ? branch_table_types::TABLE : $table_type;

                        $echo->rows["PRODUCTS"] = products::get($db,$sessions->LANGUAGE_TAG,
                            $branch_id, is_mobile: true,
                            order_type:  $table_type,
                            custom_where: $db->where->equals([tbl2::DELETE => 0]),
                            order_by: $db->order_by(tbl2::CATEGORY_ID, DB::ASC)
                        )->rows;

                        $echo->rows["PRODUCT_LINKED_OPTIONS"] = (array) products::get_linked_options($db,$branch_id)->rows;
                        $echo->rows["OPTION_TYPES"] = (array) products::get_option_types($db,$sessions->LANGUAGE_TAG,$branch_id)->rows;
                        $echo->rows["PRODUCT_OPTIONS"] = (array)products::get_options($db,$sessions->LANGUAGE_TAG, $branch_id, $db->where->not_like([tbl4::IS_DELETED => 1]))->rows;
                        $echo->rows["PRODUCT_OPTION_ITEMS"] = (array)products::get_options_items($db, $sessions->LANGUAGE_TAG, $branch_id, $db->where->not_like([tbl5::IS_DELETED => 1]))->rows;
                        $echo->rows["CATEGORIES"] = (array) products::get_categories($db,$sessions->LANGUAGE_TAG,$branch_id, is_mobile: true, order_type: $table_type)->rows;
                        $echo->rows["BRANCH_PAYMENT_TYPES"] = branch_payment_types::get($db, $branch_id, order_by: \config\table_helper\branch_payment_types::ID." ASC")->rows;
                        $echo->rows["PAYMENT_TYPES"] = branch_payment_types::get_types($db, $sessions->LANGUAGE_TAG, order_by: \config\table_helper\payment_types::ID." ASC")->rows;
                        $echo->rows["ACCEPTED_ADDRESS"] = branch_info::takeaway_accepted_neighborhoods($db,$branch_id)->rows;
                        $echo->rows["WORK_TIMES"] = branch_info::branch_work_times($db,$branch_id)->rows;
                        $echo->rows["NOTIFICATIONS"] = notification::get($db,$branch_id)->rows;
                        //$echo->rows["SURVEYS"] = surveys::get_questions($db,$branch_id,$sessions->LANGUAGE_TAG)->rows;

                        if ($sessions->USER_ID > 0 && $sessions->VERIFY == true){
                            $echo->rows["USER_INFO"] =  array(
                                "login" => true,
                                "user_id" => $sessions->USER_ID,
                                "name" => $sessions->NAME,
                                "phone" => $sessions->PHONE
                            );
                        }

                    } else {
                        $echo->status = false;
                        $echo->error_code = settings::error_codes()::NOT_FOUND;
                    }
               } else $echo->error_code = settings::error_codes()::WRONG_VALUE; break;
        }
    }

    function get_table_values_for_url (db $db, get $sessions, $url = "", ) : results{
        $lang = ($sessions->LANGUAGE_TAG == "tr") ? "tr" : "en";
        return $db->db_select(
            array(
                tbl::BRANCH_ID,
                tbl::ID,
                tbl::CREATE_DATE,
                tbl::IS_LOCK,
                tbl::SECTION_ID,
                tbl::TABLE_NO,
                tbl::TABLE_TYPE,
                tbl::TABLE_SHAPE_TYPE,
                $db->as_name(tbl7::NAME.$lang, "section_name")
            ),tbl::TABLE_NAME,
            $db->join->inner([
                tbl6::TABLE_NAME => [tbl6::ID => tbl::SECTION_ID],
                tbl7::TABLE_NAME => [tbl7::ID => tbl6::SECTION_ID]
            ]),
            where: $db->where->equals([tbl::URL => $url]),
            limit: $db->limit([0,1])
        );
    }


}

