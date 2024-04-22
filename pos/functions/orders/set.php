<?php
namespace pos\functions\orders;
require "../../../matrix_library/php/auto_loader.php";

use config\database_list;
use config\db;
use config\sessions;
use config\sessions\check;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use pos\functions\orders\set\cancel_and_catering;
use pos\functions\orders\set\change_price;
use pos\functions\orders\set\order_combining;
use pos\functions\orders\set\payment;
use pos\functions\orders\set\print_invoice;
use pos\functions\orders\set\separate_product;
use pos\functions\orders\set\update_confirm;
use pos\functions\orders\set\update_confirm_account_id;
use pos\functions\orders\set\update_is_print;
use sameparts\php\ajax\echo_values;
use pos\functions\orders\set\insert;
use pos\functions\orders\set\table_move;

/* CONST Values */
class post_keys {
    const SET_TYPE = "set_type";
}

class set_types {
    const INSERT = 0x0001,
        TABLE_MOVE = 0x0002,
        ORDER_COMBINING = 0x0003,
        PAYMENT = 0x0004,
        CANCEL_AND_CATERING = 0x0005,
        CHANGE_PRICE = 0x0006,
        SEPARATE_PRODUCT = 0x0007,
        PAYMENT_TRUST_ACCOUNT = 0x0008,
        PRINT_INVOICE = 0x0009,
        UPDATE_CONFIRM = 0x0010,
        UPDATE_CONFIRM_ACCOUNT_ID = 0x0011,
        UPDATE_IS_PRINT = 0x0012;
}
/* end CONST Values */

if(user::check_sent_data([post_keys::SET_TYPE]) && (check::check(false) || \waiter_terminal\sameparts\functions\sessions\check::check(false))) {
    $db = new db(database_list::LIVE_MYSQL_1);
    $echo = new echo_values();
    $sessions = new sessions();

    variable::clear_all_data($_POST);

    switch (user::post(post_keys::SET_TYPE)){
        case set_types::INSERT:
            (new insert($db, $sessions, $echo));
            break;
        case set_types::TABLE_MOVE:
            (new table_move($db, $sessions, $echo));
            break;
        case set_types::ORDER_COMBINING:
            (new order_combining($db, $sessions, $echo));
            break;
        case set_types::PAYMENT:
        case set_types::PAYMENT_TRUST_ACCOUNT:
            (new payment($db, $sessions, $echo));
            break;
        case set_types::CANCEL_AND_CATERING:
            (new cancel_and_catering($db, $sessions, $echo));
            break;
        case set_types::CHANGE_PRICE:
            (new change_price($db, $sessions, $echo));
            break;
        case set_types::SEPARATE_PRODUCT:
            (new separate_product($db, $sessions, $echo));
            break;
        case set_types::PRINT_INVOICE:
            (new print_invoice($db, $sessions, $echo));
            break;
        case set_types::UPDATE_CONFIRM:
            (new update_confirm($db, $sessions, $echo));
            break;
        case set_types::UPDATE_CONFIRM_ACCOUNT_ID:
            (new update_confirm_account_id($db, $sessions, $echo));
            break;
        case set_types::UPDATE_IS_PRINT:
            (new update_is_print($db, $sessions, $echo));
            break;
    }

    $echo->return();
}
/* end Functions */