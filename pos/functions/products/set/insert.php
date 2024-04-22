<?php
namespace pos\functions\products\set;

use config\db;
use config\sessions;
use config\settings;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\clear_types;
use matrix_library\php\operations\server;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use config\table_helper\products as tbl;
use config\table_helper\product_linked_options as tbl2;
use config\table_helper\product_categories as tbl3;
use sameparts\php\ajax\echo_values;
use sameparts\php\db_query\products;
use sameparts\php\helper\date;

class post_keys {
    const SET_TYPE = "set_type",
        ID = "id",
        ACTIVE_QR = "active_mobile",
        ACTIVE_TAKE_AWAY = "active_take_away",
        ACTIVE_COME_TAKE = "active_come_take",
        ACTIVE_POS = "active_pos",
        ACTIVE_START_TIME = "start_time",
        ACTIVE_END_TIME = "end_time",
        IMAGE = "image",
        DEFAULT_CATEGORY_IMAGE = "default_category_image",
        NAME = "name",
        STOCK_ID = "code",
        COMMENT = "comment",
        CATEGORY = "category_id",
        PRICE = "price",
        PRICE_SAFE = "price_safe",
        PRICE_TAKE_AWAY = "price_take_away",
        PRICE_PERSONAL = "price_personal",
        PRICE_COME_TAKE = "price_come_take",
        PRICE_OTHER = "price_other",
        QUANTITY = "quantity_id",
        VAT = "vat",
        VAT_SAFE = "vat_safe",
        VAT_TAKE_AWAY = "vat_take_away",
        VAT_PERSONAL = "vat_personal",
        VAT_COME_TAKE = "vat_come_take",
        VAT_OTHER = "vat_other",
        OPTIONS = "options",
        RANK = "rank";

}
class option_keys{
    const ADD = "add",
    UPDATE = "update",
    DELETE = "delete",
    /* in items names */
    OPTION_ID = "option_id",
    ID = "id",
    LIMIT = "limit";
}

class insert{
    function __construct(db $db, sessions $sessions, echo_values &$echo){
        if(user::check_sent_data([
            post_keys::ACTIVE_START_TIME,
            post_keys::ACTIVE_END_TIME,
            post_keys::NAME,
            post_keys::CATEGORY,
            post_keys::PRICE,
            post_keys::VAT,
            post_keys::QUANTITY])) {

            $this->check_values_insert($db, $sessions, $echo);
            if ($echo->status) {

                $image_name = settings::paths()->image->BRANCH_LOGO($sessions->get->BRANCH_ID);
                if (user::files(post_keys::IMAGE)) {
                    $image_name = date::get(date\date_type_simples::UNIFIED_DATE_TIME).".webp";

                    if (!server::upload_image(
                        user::files(post_keys::IMAGE)["tmp_name"],
                        settings::paths()->image->PRODUCT($sessions->get->BRANCH_ID),
                        $image_name
                    )) {
                        $echo->error_code = settings::error_codes()::UPLOAD_ERROR;
                        $echo->status = false;
                    }

                } else if (!variable::is_empty(user::post(post_keys::IMAGE))) {
                    $image_name = user::post(post_keys::IMAGE);
                }

                $options = null;
                if(user::check_sent_data([post_keys::OPTIONS]))
                    $options = user::post(post_keys::OPTIONS);

                if ($echo->status) {
                    user::post(post_keys::IMAGE, $image_name);
                    if (user::post(post_keys::ID)) {
                        $echo->custom_data = (array)$this->update($db, $sessions);
                        if ($options != null){
                            if (is_array($options[option_keys::ADD])    && count($options[option_keys::ADD]) > 0)    {$echo->custom_data["linked_insert"] = $this->insert_linked_option($db,$sessions); }
                            if (is_array($options[option_keys::UPDATE]) && count($options[option_keys::UPDATE]) > 0) { $echo->custom_data["linked_update"] = $this->update_linked_option($db,$sessions); }
                            if (is_array($options[option_keys::DELETE]) && count($options[option_keys::DELETE]) > 0) { $echo->custom_data["linked_delete"] = $this->delete_linked_option($db, $sessions);}
                        }
                    } else {
                        $result = $this->insert($db, $sessions);
                        $echo->custom_data["insert"] = (array) $result;

                        if ($options != null && is_array($options[option_keys::ADD]) && count($options[option_keys::ADD]) > 0){
                            user::post(post_keys::ID,$result->insert_id);
                            $echo->custom_data["linked_insert"] = $this->insert_linked_option($db,$sessions);
                        }
                    }
                    $this->update_category($db, $sessions);
                } // status end

            }
        }
        $echo->custom_data["options"] =  user::post(post_keys::OPTIONS);

    }
    function check_values_insert(db $db, sessions $sessions, echo_values &$echo){
        if(variable::is_empty(
            user::post(post_keys::NAME),
            user::post(post_keys::PRICE),
            user::post(post_keys::VAT),
            user::post(post_keys::QUANTITY),
            user::post(post_keys::CATEGORY)
        )){
            $echo->error_code = settings::error_codes()::EMPTY_VALUE;
        }

        if($echo->error_code == settings::error_codes()::SUCCESS){
            if(count(products::get_categories(
                    $db,
                    $sessions->get->LANGUAGE_TAG,
                    $sessions->get->BRANCH_ID,
                    user::post(post_keys::CATEGORY),
                    limit: [0, 1]
                )->rows) < 1) $echo->error_code = settings::error_codes()::INCORRECT_DATA;
        }

        if($echo->error_code == settings::error_codes()::SUCCESS){
            if(count(products::get_quantity_types(
                    $db,
                    $sessions->get->LANGUAGE_TAG,
                    user::post(post_keys::QUANTITY),
                    limit: [0, 1]
                )->rows) < 1) $echo->error_code = settings::error_codes()::INCORRECT_DATA;
        }

        if($echo->error_code == settings::error_codes()::SUCCESS){
            if(is_string(user::post(post_keys::STOCK_ID)) && strlen(user::post(post_keys::STOCK_ID)) > 0){
                $results = $this->check_stock_id($db, $sessions);
                if(count($results->rows) > 0){
                    if(!(user::post(post_keys::ID) || $results->rows[0][post_keys::ID] != user::post(post_keys::ID)))
                        $echo->error_code = settings::error_codes()::REGISTERED_VALUE;
                }
            }
        }

        if($echo->error_code != settings::error_codes()::SUCCESS) $echo->status = false;
        else{
            // PRICE
            if(variable::is_empty(user::post(post_keys::PRICE_PERSONAL))) user::post(post_keys::PRICE_PERSONAL, user::post(post_keys::PRICE));
            if(variable::is_empty(user::post(post_keys::PRICE_SAFE))) user::post(post_keys::PRICE_SAFE, user::post(post_keys::PRICE));
            if(variable::is_empty(user::post(post_keys::PRICE_TAKE_AWAY))) user::post(post_keys::PRICE_TAKE_AWAY, user::post(post_keys::PRICE));
            if(variable::is_empty(user::post(post_keys::PRICE_COME_TAKE))) user::post(post_keys::PRICE_COME_TAKE, user::post(post_keys::PRICE));
            if(variable::is_empty(user::post(post_keys::PRICE_OTHER))) user::post(post_keys::PRICE_OTHER, user::post(post_keys::PRICE));
            // VAT
            if(variable::is_empty(user::post(post_keys::VAT_PERSONAL))) user::post(post_keys::VAT_PERSONAL, user::post(post_keys::VAT));
            if(variable::is_empty(user::post(post_keys::VAT_SAFE))) user::post(post_keys::VAT_SAFE, user::post(post_keys::VAT));
            if(variable::is_empty(user::post(post_keys::VAT_TAKE_AWAY))) user::post(post_keys::VAT_TAKE_AWAY, user::post(post_keys::VAT));
            if(variable::is_empty(user::post(post_keys::VAT_COME_TAKE))) user::post(post_keys::VAT_COME_TAKE, user::post(post_keys::VAT));
            if(variable::is_empty(user::post(post_keys::VAT_OTHER))) user::post(post_keys::VAT_OTHER, user::post(post_keys::VAT));
        }

    }
    function insert(db $db, sessions $sessions) : results{
        $values = $db->db_insert(
            tbl::TABLE_NAME,
            array(
                tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl::CREATE_DATE => date::get(),
                tbl::NAME.$sessions->get->LANGUAGE_TAG => user::post(post_keys::NAME),
                tbl::COMMENT.$sessions->get->LANGUAGE_TAG => user::post(post_keys::COMMENT),
                tbl::IMAGE            => user::post(post_keys::IMAGE),
                tbl::ACTIVE_COME_TAKE => (int)user::post(post_keys::ACTIVE_COME_TAKE),
                tbl::ACTIVE_MOBILE    => (int)user::post(post_keys::ACTIVE_QR),
                tbl::ACTIVE_POS       => (int)user::post(post_keys::ACTIVE_POS),
                tbl::ACTIVE_TAKE_AWAY => (int)user::post(post_keys::ACTIVE_TAKE_AWAY),
                tbl::CATEGORY_ID      => user::post(post_keys::CATEGORY),
                tbl::CODE             => variable::clear(user::post(post_keys::STOCK_ID), clear_types::SEO_URL),
                tbl::START_TIME       => date::get(date\date_type_simples::HOUR_MINUTE, strtotime(user::post(post_keys::ACTIVE_START_TIME))),
                tbl::END_TIME         => date::get(date\date_type_simples::HOUR_MINUTE, strtotime(user::post(post_keys::ACTIVE_END_TIME))),
                tbl::PRICE            => user::post(post_keys::PRICE),
                tbl::PRICE_PERSONAL   => user::post(post_keys::PRICE_PERSONAL),
                tbl::PRICE_SAFE       => user::post(post_keys::PRICE_SAFE),
                tbl::PRICE_TAKE_AWAY  => user::post(post_keys::PRICE_TAKE_AWAY),
                tbl::PRICE_COME_TAKE  => user::post(post_keys::PRICE_COME_TAKE),
                tbl::PRICE_OTHER      => user::post(post_keys::PRICE_OTHER),
                tbl::VAT              => user::post(post_keys::VAT),
                tbl::VAT_SAFE         => user::post(post_keys::VAT_SAFE),
                tbl::VAT_COME_TAKE    => user::post(post_keys::VAT_COME_TAKE),
                tbl::VAT_TAKE_AWAY    => user::post(post_keys::VAT_TAKE_AWAY),
                tbl::VAT_PERSONAL     => user::post(post_keys::VAT_PERSONAL),
                tbl::VAT_OTHER        => user::post(post_keys::VAT_OTHER),
                tbl::QUANTITY_ID      => user::post(post_keys::QUANTITY),
                tbl::RANK             => (int)user::post(post_keys::RANK)
            )
        );
        user::post(post_keys::ID, $values->insert_id);
        return $values;
    }
    function update(db $db, sessions $sessions) : results{
        return $db->db_update(
            tbl::TABLE_NAME,
            array(
                tbl::NAME.$sessions->get->LANGUAGE_TAG => user::post(post_keys::NAME),
                tbl::COMMENT.$sessions->get->LANGUAGE_TAG => user::post(post_keys::COMMENT),
                tbl::IMAGE            => user::post(post_keys::IMAGE),
                tbl::ACTIVE_COME_TAKE => (int)user::post(post_keys::ACTIVE_COME_TAKE),
                tbl::ACTIVE_MOBILE    => (int)user::post(post_keys::ACTIVE_QR),
                tbl::ACTIVE_POS       => (int)user::post(post_keys::ACTIVE_POS),
                tbl::ACTIVE_TAKE_AWAY => (int)user::post(post_keys::ACTIVE_TAKE_AWAY),
                tbl::CATEGORY_ID      => user::post(post_keys::CATEGORY),
                tbl::CODE             => user::post(post_keys::STOCK_ID),
                tbl::START_TIME       => date::get(date\date_type_simples::HOUR_MINUTE, strtotime(user::post(post_keys::ACTIVE_START_TIME))),
                tbl::END_TIME         => date::get(date\date_type_simples::HOUR_MINUTE, strtotime(user::post(post_keys::ACTIVE_END_TIME))),
                tbl::PRICE            => user::post(post_keys::PRICE),
                tbl::PRICE_PERSONAL   => user::post(post_keys::PRICE_PERSONAL),
                tbl::PRICE_SAFE       => user::post(post_keys::PRICE_SAFE),
                tbl::PRICE_TAKE_AWAY  => user::post(post_keys::PRICE_TAKE_AWAY),
                tbl::PRICE_COME_TAKE  => user::post(post_keys::PRICE_COME_TAKE),
                tbl::PRICE_OTHER      => user::post(post_keys::PRICE_OTHER),
                tbl::VAT              => user::post(post_keys::VAT),
                tbl::VAT_SAFE         => user::post(post_keys::VAT_SAFE),
                tbl::VAT_COME_TAKE    => user::post(post_keys::VAT_COME_TAKE),
                tbl::VAT_TAKE_AWAY    => user::post(post_keys::VAT_TAKE_AWAY),
                tbl::VAT_PERSONAL     => user::post(post_keys::VAT_PERSONAL),
                tbl::VAT_OTHER        => user::post(post_keys::VAT_OTHER),
                tbl::QUANTITY_ID      => user::post(post_keys::QUANTITY),
                tbl::RANK             => (int)user::post(post_keys::RANK)
            ),
            where: $db->where->like([
            tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
            tbl::ID => (int)user::post(post_keys::ID)
        ])
        );
    }
    function update_category(db $db, sessions $sessions) : results{
        return $db->db_update(
            tbl3::TABLE_NAME,
            array(
                tbl3::PRODUCT_ID => (user::post(post_keys::DEFAULT_CATEGORY_IMAGE)) ? user::post(post_keys::ID) : 0
            ),
            where: $db->where->equals([
                tbl3::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl3::ID => (int)user::post(post_keys::CATEGORY)
            ])
        );
    }
    function check_stock_id(db $db, sessions $sessions) : results{
        return products::get(
            $db,
            $sessions->get->LANGUAGE_TAG,
            $sessions->get->BRANCH_ID,
            custom_where: $db->where->like(
            [
                tbl::CODE => user::post(post_keys::STOCK_ID)
            ]
        ),
            limit: [0, 1]
        );
    }


    //linked functions
    function insert_linked_option(db $db, sessions $sessions): results{
        $options = user::post(post_keys::OPTIONS);
        $data = array();
        foreach ($options[option_keys::ADD] as $item) {
            if (! variable::is_empty($item[option_keys::OPTION_ID], $item["limit"])){
                array_push($data, array(
                    tbl2::BRANCH_ID => $sessions->get->BRANCH_ID,
                    tbl2::PRODUCT_ID => user::post(post_keys::ID),
                    tbl2::OPTION_ID => $item[option_keys::OPTION_ID],
                    tbl2::DATE => date::get(),
                    tbl2::MAX_COUNT => $item[option_keys::LIMIT],
                ));
            }
        }
        return $db->db_insert(tbl2::TABLE_NAME, $data);
    }
    function update_linked_option(db $db, sessions $sessions): results{
        $options = user::post(post_keys::OPTIONS);
        $data = new results();

        foreach ($options[option_keys::UPDATE] as $item) {
            if (!variable::is_empty($item[option_keys::LIMIT], $item[option_keys::ID])) {
                array_push($data->rows,
                    $db->db_update(
                        tbl2::TABLE_NAME,
                        array(tbl2::MAX_COUNT => $item[option_keys::LIMIT]),
                        where: $db->where->equals(
                        array(
                            tbl2::BRANCH_ID => $sessions->get->BRANCH_ID,
                            tbl2::ID => $item[option_keys::ID]
                        ))
                    )
                );
            }
        }
        return $data;
    }
    function delete_linked_option(db $db, sessions $sessions): results{
        $options = user::post(post_keys::OPTIONS);
        $data = array();
        foreach ($options[option_keys::DELETE] as $item) {
            if (!variable::is_empty($item[option_keys::ID])) {
                array_push($data,$item[option_keys::ID]);
            }
        }
        return $db->db_delete(tbl2::TABLE_NAME,
            where: $db->where->equals(array(
                tbl2::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl2::ID => $data
            ))
        );
    }

}