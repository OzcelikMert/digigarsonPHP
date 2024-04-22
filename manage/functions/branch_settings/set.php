<?php
namespace manage\functions\branch_settings;
require "../../../matrix_library/php/auto_loader.php";

use config\database_list;
use config\db;
use manage\functions\branch_settings\set\change_name;
use manage\functions\branch_settings\set\edit_address;
use manage\functions\branch_settings\set\payment_types;
use manage\functions\branch_settings\set\surveys;
use manage\functions\branch_settings\set\work_times;
use manage\functions\branch_settings\set\long_address;
use manage\functions\branch_settings\set\notification_services;
use manage\functions\branch_settings\set\qr_security;
use manage\functions\branch_settings\set\takeaway_other_settings;
use config\sessions\check;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use config\sessions;
use sameparts\php\ajax\echo_values;

/* CONST Values */

class set_type {
    const CHANGE_NAME=1,
        WORKING_TIMES= 2,
        GET_ADDRESS = 3,
        TAKEAWAY_ADDRESS= 4,
        PAYMENT_METHOD= 5,
        LONG_ADDRESS=6,
        MIN_MONEY_AND_TIME=7,
        QR_CODE_SECURITY=8,
        SERVICE_NOTIFICATION=9,
        SURVEYS=10,
        INTEGRATIONS = 11;

}

class post_keys {
    const SET_TYPE = "set_type";
}
/* end CONST Values */

if(user::check_sent_data([post_keys::SET_TYPE]) && check::check(false)) {
    $echo = new echo_values();

    $db = new db(database_list::LIVE_MYSQL_1);
    $sessions = new sessions();
    variable::clear_all_data($_POST);

    switch (user::post(post_keys::SET_TYPE)){
        case set_type::TAKEAWAY_ADDRESS:        (new edit_address($db,$sessions, $echo)); break;
        case set_type::PAYMENT_METHOD:          (new payment_types($db,$sessions, $echo)); break;
        case set_type::CHANGE_NAME:             (new change_name($db,$sessions, $echo)); break;
        case set_type::WORKING_TIMES:           (new work_times($db,$sessions, $echo)); break;
        case set_type::LONG_ADDRESS:            (new long_address($db,$sessions, $echo)); break;
        case set_type::MIN_MONEY_AND_TIME:      (new takeaway_other_settings($db,$sessions, $echo)); break;
        case set_type::QR_CODE_SECURITY:        (new qr_security($db,$sessions, $echo)); break;
        case set_type::SERVICE_NOTIFICATION:    (new notification_services($db,$sessions, $echo)); break;
        case set_type::SURVEYS:                 (new surveys($db,$sessions, $echo)); break;
    }

    $echo->return();
}

/* end Functions */