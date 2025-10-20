<?php

namespace pos\functions\orders\set;

use config\db;
use config\sessions;
use config\settings;
use config\table_helper\order_products as tbl12;
use config\table_helper\orders as tbl10;
use config\table_helper\products as tbl13;
use config\table_helper\order_product_options as tbl14;
use config\table_helper\branch_callers as tbl16;
use config\table_helper\integrate_orders as tbl17;
use config\table_helper\integrate_order_payments as tbl18;
use config\table_helper\integrate_customers as tbl19;

use config\type_tables_values\account_types;
use config\type_tables_values\branch_caller_status_types;
use config\type_tables_values\integrate_types;
use config\type_tables_values\order_product_types;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\array_list;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use sameparts\php\ajax\echo_values;
use sameparts\php\db_query\integrate;
use sameparts\php\db_query\orders;
use sameparts\php\db_query\products;
use sameparts\php\helper\date;
use sameparts\php\printer\printer_values;
use config\type_tables_values\print_types;

class post_keys_insert
{
    const PRODUCTS = "products",
        TABLE_ID = "table_id",
        ORDER_ID = "order_id",
        DISCOUNT = "discount",
        TYPE = "type",
        STATUS = "status",
        TOTAL_PRICE = "total_price",
        NO = "no",
        CALLER_ID = "caller_id",
        ADDRESS_ID = "address_id",
        CUSTOMER_ID = "customer_id",
        ORDER_ID_INTEGRATE = "order_id_integrate",
        TYPE_INTEGRATE = "type_integrate",
        ADDRESS_INTEGRATE = "address_integrate",
        CUSTOMER_ID_INTEGRATE = "customer_id_integrate",
        CUSTOMER_NAME_INTEGRATE = "customer_name_integrate",
        INTEGRATE_PAYMENT_ID = "integrate_payment_id",
        INTEGRATE_ORDER_ID = "integrate_order_id",
        INTEGRATE_CUSTOMER_ID = "integrate_customer_id",
        ACCOUNT_TYPE = "account_type";
}

class products_keys
{
    const ID = "id",
        QUANTITY = "quantity",
        PRICE = "price",
        VAT = "vat",
        DISCOUNT = "discount",
        QTY = "qty",
        COMMENT = "comment",
        OPTIONS = "options",
        TYPE = "type";
}

class options_keys
{
    const ID = "id",
        OPTION_ID = "option_id",
        OPTION_ITEM_ID = "option_item_id",
        ORDER_ID = "order_id",
        ORDER_PRODUCT_ID = "order_product_id",
        PRICE = "price",
        QTY = "qty";
}


class insert
{
    private printer_values $invoice;

    public function __construct(db $db, sessions $sessions, echo_values &$echo)
    {
        user::post(post_keys_insert::NO, 0);
        $this->check_values($db, $sessions, $echo);
        if ($echo->status) {
            if (user::post(post_keys_insert::CUSTOMER_ID) > 0) {
                user::post(post_keys_insert::ACCOUNT_TYPE, account_types::CUSTOMER);
            } else {
                user::post(post_keys_insert::ACCOUNT_TYPE, account_types::WAITER);
                user::post(post_keys_insert::CUSTOMER_ID, $sessions->get->USER_ID);
            }

            if (user::post(post_keys_insert::ORDER_ID) == 0) {
                user::post(post_keys_insert::ORDER_ID, $this->add_order($db, $sessions)->insert_id);
            } else {
                $this->update_order($db, $sessions);
            }

            if (user::post(post_keys_insert::ORDER_ID_INTEGRATE) > 0) {
                switch ((int)user::post(post_keys_insert::TYPE_INTEGRATE)) {
                    case integrate_types::YEMEK_SEPETI:
                        user::post(post_keys_insert::ACCOUNT_TYPE, account_types::YEMEK_SEPETI);
                        break;
                }

                $integrate_customer = integrate::get_customers($db, user::post(post_keys_insert::TYPE_INTEGRATE), custom_where: $db->where->equals([tbl19::ID_INTEGRATE => user::post(post_keys_insert::CUSTOMER_ID_INTEGRATE)]))->rows;
                user::post(
                    post_keys_insert::INTEGRATE_CUSTOMER_ID,
                    ((count($integrate_customer) > 0)
                        ? $integrate_customer[0]["id"]
                        : $this->add_integrate_customer($db, $sessions)->insert_id)
                );
                user::post(post_keys_insert::CUSTOMER_ID, user::post(post_keys_insert::INTEGRATE_CUSTOMER_ID));
                user::post(post_keys_insert::INTEGRATE_ORDER_ID, $this->add_integrate_order($db, $sessions)->insert_id);
                $echo->custom_data["payment"] = (array)$this->add_integrate_order_payment($db, $sessions);
            }

            $invoice_products = $this->add_order_products($db, $sessions);
            if (count($invoice_products) > 0) {
                $this->invoice = new printer_values(
                    db: $db,
                    sessions: $sessions,
                    table_id: user::post(post_keys_insert::TABLE_ID),
                    order_id: user::post(post_keys_insert::ORDER_ID),
                    order_no: (user::post(post_keys_insert::NO) > 0) ? user::post(post_keys_insert::NO) : $this->get_order_no($db, $sessions, $echo),
                    print_type: print_types::KITCHEN,
                );
                $this->invoice->products = $invoice_products;
                $this->invoice->create(); //create printer json and insert mysql
            }
            if (user::post(post_keys_insert::CALLER_ID) > 0) {
                $this->update_caller_status($db, $sessions);
            }
        }
        $echo->custom_data["POST"] = $_POST;
    }

    /* Functions */
    private function update_order(db $db, sessions $sessions): void
    {
        $db->db_update(
            tbl10::TABLE_NAME,
            array(
                tbl10::IS_PRINT => 0
            ),
            where: $db->where->equals([
                tbl10::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl10::ID => user::post(post_keys_insert::ORDER_ID)
            ])
        );
    }

    private function add_order(db $db, sessions $sessions): results
    {
        user::post(post_keys_insert::NO, orders::get_order_last_no($db, $sessions->get->BRANCH_ID));

        return $db->db_insert(
            tbl10::TABLE_NAME,
            array(
                tbl10::BRANCH_ID            => $sessions->get->BRANCH_ID,
                tbl10::TABLE_ID             => user::post(post_keys_insert::TABLE_ID),
                tbl10::DATE_START           => date::get(),
                tbl10::DISCOUNT             => user::post(post_keys_insert::DISCOUNT),
                tbl10::TYPE                 => user::post(post_keys_insert::TYPE),
                tbl10::STATUS               => user::post(post_keys_insert::STATUS),
                tbl10::NO                   => user::post(post_keys_insert::NO),
                tbl10::ADDRESS_ID           => (int)user::post(post_keys_insert::ADDRESS_ID),
                tbl10::CONFIRMED_ACCOUNT_ID => $sessions->get->USER_ID
            )
        );
    }

    private function add_order_products(db $db, sessions $sessions)
    {
        $time = date::get(date::date_type_simples()::HOUR_MINUTE);
        $total_price = 0;
        $index = -1;
        $invoice_products = array();
        foreach (user::post(post_keys_insert::PRODUCTS) as $value) {
            $index++;
            $total_price += (float)$value[products_keys::PRICE];

            $insert_id = $db->db_insert(
                tbl12::TABLE_NAME,
                array(
                    tbl12::BRANCH_ID     => $sessions->get->BRANCH_ID,
                    tbl12::PRODUCT_ID    => $value[products_keys::ID],
                    tbl12::ORDER_ID      => user::post(post_keys_insert::ORDER_ID),
                    tbl12::ACCOUNT_ID    => user::post(post_keys_insert::CUSTOMER_ID),
                    tbl12::ACCOUNT_TYPE  => user::post(post_keys_insert::ACCOUNT_TYPE),
                    tbl12::DISCOUNT      => $value[products_keys::DISCOUNT],
                    tbl12::PRICE         => $value[products_keys::PRICE],
                    tbl12::VAT           => $value[products_keys::VAT],
                    tbl12::QUANTITY      => $value[products_keys::QUANTITY],
                    tbl12::QTY           => $value[products_keys::QTY],
                    tbl12::PRINT         => $value[products_keys::TYPE] == order_product_types::DISCOUNT ? 1 : 0,
                    tbl12::TIME          => $time,
                    tbl12::COMMENT       => $value[products_keys::COMMENT],
                    tbl12::TYPE          => $value[products_keys::TYPE]
                )
            )->insert_id;

            if ($value[products_keys::TYPE] == order_product_types::DISCOUNT) {
                continue;
            }

            $invoice_products[$index] = array();

            $invoice_products[$index] = array(
                "product_id"    => (int)$value[products_keys::ID],
                "order_id"      => (int)user::post(post_keys_insert::ORDER_ID),
                "price"         => (float)$value[products_keys::PRICE],
                "quantity"      => (float)$value[products_keys::QUANTITY],
                "qty"           => (int)$value[products_keys::QTY],
                "comment"       => $value[products_keys::COMMENT],
            );

            $invoice_products[$index]["options"] = array();

            if (isset($value[products_keys::OPTIONS]) && count($value[products_keys::OPTIONS]) > 0) {
                $insert_data = array();
                foreach ($value[products_keys::OPTIONS] as $option) {
                    array_push(
                        $insert_data,
                        array(
                            tbl14::BRANCH_ID => $sessions->get->BRANCH_ID,
                            tbl14::ORDER_PRODUCT_ID => $insert_id,
                            tbl14::OPTION_ID => $option[options_keys::OPTION_ID],
                            tbl14::OPTION_ITEM_ID => $option[options_keys::OPTION_ITEM_ID],
                            tbl14::PRICE => $option[options_keys::PRICE],
                            tbl14::QTY => $value[options_keys::QTY]
                        )
                    );

                    array_push(
                        $invoice_products[$index]["options"],
                        array(
                            "option_id"      => (int)$option[options_keys::OPTION_ID],
                            "option_item_id" => (int)$option[options_keys::OPTION_ITEM_ID],
                            "price"          => (int)$option[options_keys::PRICE],
                            "qty"            => (int)$value[options_keys::QTY]
                        )
                    );
                }

                $db->db_insert(tbl14::TABLE_NAME, $insert_data);
                continue;
            }
        }

        user::post(post_keys_insert::TOTAL_PRICE, $total_price);

        return $invoice_products;
    }

    private function update_caller_status(db $db, sessions $sessions): results
    {
        return $db->db_update(
            tbl16::TABLE_NAME,
            array(
                tbl16::STATUS => branch_caller_status_types::CONFIRMED
            ),
            where: $db->where->equals([
                tbl16::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl16::ID        => user::post(post_keys_insert::CALLER_ID)
            ])
        );
    }

    private function add_integrate_order(db $db, sessions $sessions): results
    {
        return $db->db_insert(
            tbl17::TABLE_NAME,
            array(
                tbl17::ORDER_ID => user::post(post_keys_insert::ORDER_ID),
                tbl17::ORDER_ID_INTEGRATE => user::post(post_keys_insert::ORDER_ID_INTEGRATE),
                tbl17::TYPE => user::post(post_keys_insert::TYPE_INTEGRATE),
                tbl17::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl17::ADDRESS   => user::post(post_keys_insert::ADDRESS_INTEGRATE),
                tbl17::INTEGRATE_CUSTOMER_ID => user::post(post_keys_insert::INTEGRATE_CUSTOMER_ID)
            )
        );
    }

    private function add_integrate_order_payment(db $db, sessions $sessions): results
    {
        return $db->db_insert(
            tbl18::TABLE_NAME,
            array(
                tbl18::INTEGRATE_ORDER_ID => user::post(post_keys_insert::INTEGRATE_ORDER_ID),
                tbl18::INTEGRATE_TYPE_ID => user::post(post_keys_insert::INTEGRATE_PAYMENT_ID),
                tbl18::TYPE => user::post(post_keys_insert::TYPE_INTEGRATE),
                tbl18::BRANCH_ID => $sessions->get->BRANCH_ID
            )
        );
    }

    private function add_integrate_customer(db $db, sessions $sessions): results
    {
        return $db->db_insert(
            tbl19::TABLE_NAME,
            array(
                tbl19::NAME => user::post(post_keys_insert::CUSTOMER_NAME_INTEGRATE),
                tbl19::ID_INTEGRATE => user::post(post_keys_insert::CUSTOMER_ID_INTEGRATE),
                tbl19::TYPE => user::post(post_keys_insert::TYPE_INTEGRATE),
            )
        );
    }

    private function get_order_no(db $db, sessions $sessions, echo_values $echo)
    {
        $values = $db->db_select(tbl10::NO, tbl10::TABLE_NAME, where: $db->where->equals([
            tbl10::BRANCH_ID => $sessions->get->BRANCH_ID,
            tbl10::ID => user::post(post_keys_insert::ORDER_ID)
        ]));
        return $values->rows[0]["no"];
    }

    private function check_values(db $db, sessions $sessions, echo_values &$echo)
    {
        if (variable::is_empty(
            user::post(post_keys_insert::PRODUCTS),
            user::post(post_keys_insert::ORDER_ID),
            user::post(post_keys_insert::TABLE_ID),
            user::post(post_keys_insert::DISCOUNT),
            user::post(post_keys_insert::STATUS),
            user::post(post_keys_insert::TYPE)
        )) {
            $echo->error_code = settings::error_codes()::EMPTY_VALUE;
        }

        if ($echo->error_code == settings::error_codes()::SUCCESS) {
            $id = array();
            foreach (user::post(post_keys_insert::PRODUCTS) as $value) {
                if ($value[products_keys::TYPE] == order_product_types::DISCOUNT) continue;
                if (array_list::index_of($id, $value[products_keys::ID]) < 0) array_push($id, $value[products_keys::ID]);
            }

            if (count($id) > 0) {
                if (count(products::get(
                    $db,
                    $sessions->get->LANGUAGE_TAG,
                    $sessions->get->BRANCH_ID,
                    custom_where: $db->where->equals([tbl13::ID => $id]),
                    limit: [0, count($id)]
                )->rows) != count($id)) $echo->error_code = settings::error_codes()::INCORRECT_DATA;
            }
        }

        if ($echo->error_code != settings::error_codes()::SUCCESS) $echo->status = false;
    }
}
