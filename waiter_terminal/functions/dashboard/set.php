<?php
namespace waiter_terminal\functions\dashboard;
require "../../../matrix_library/php/auto_loader.php";

use config\database_list;
use config\db;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use waiter_terminal\functions\dashboard\set\print_invoice;
use waiter_terminal\sameparts\functions\sessions\check;
use waiter_terminal\sameparts\functions\sessions\get;
use sameparts\php\ajax\echo_values;
use waiter_terminal\functions\dashboard\set\sign_out;

/* CONST Values */
class post_keys {
    const SET_TYPE = "set_type";
}

class set_types {
    const SIGN_OUT = 0x0101, PRINT_INVOICE = 0x0102;
}
/* end CONST Values */

if(user::check_sent_data([post_keys::SET_TYPE]) && check::check(false)) {
    $db = new db(database_list::LIVE_MYSQL_1);
    $echo = new echo_values();
    $sessions = new get();

    variable::clear_all_data($_POST);

    switch (user::post(post_keys::SET_TYPE)){
        case set_types::SIGN_OUT:
            (new sign_out($db, $sessions, $echo));
            break;
        case set_types::PRINT_INVOICE:
            (new print_invoice($db, $sessions, $echo));
            break;
    }

    $echo->return();
}
/* end Functions */