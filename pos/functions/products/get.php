<?php
namespace functions\products;
require "../../../matrix_library/php/auto_loader.php";

use config\database_list;
use config\db;
use config\sessions;
use config\sessions\check;
use config\settings\application_names;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use pos\functions\products\get\translate_category;
use pos\functions\products\get\translate_product;
use sameparts\php\ajax\echo_values;


/* CONST Values */
class post_keys {
    const GET_TYPE = "get_type";
}

class get_types {
    const TRANSLATE_PRODUCT = 0x0001, TRANSLATE_CATEGORY = 0x0002;
}


if (user::check_sent_data([post_keys::GET_TYPE]) && (check::check(false) || check::check(false, application_names::MANAGE))) {
    $db = new db(database_list::LIVE_MYSQL_1);
    $echo = new echo_values();
    $sessions = new sessions();

    variable::clear_all_data($_POST);

    switch (user::post(post_keys::GET_TYPE)) {
        case get_types::TRANSLATE_PRODUCT:
            new translate_product($db, $sessions, $echo);
            break;
        case get_types::TRANSLATE_CATEGORY:
            new translate_category($db, $sessions, $echo);
            break;
    }

    $echo->return();
}
