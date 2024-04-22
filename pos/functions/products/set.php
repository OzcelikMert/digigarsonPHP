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
use pos\functions\products\set\translate_category;
use pos\functions\products\set\delete;
use pos\functions\products\set\delete_option;
use pos\functions\products\set\insert_option;
use pos\functions\products\set\insert;
use pos\functions\products\set\translate_product;
use pos\functions\products\set\update;
use sameparts\php\ajax\echo_values;


/* CONST Values */
class post_keys {
    const SET_TYPE = "set_type",
        ID = "id";
}

class set_types {
    const INSERT = 0x0001,
        UPDATE = 0x0002,
        DELETE = 0x0003,
        OPTION_INSERT = 0x0005,
        OPTION_DELETE = 0x0006,
        TRANSLATE_PRODUCT = 0x0007,
        TRANSLATE_CATEGORY = 0x0008;
}


if ((user::check_sent_data([post_keys::SET_TYPE])  || user::check_sent_data([post_keys::ID])) && (check::check(false) || check::check(false, application_names::MANAGE))) {
    $db = new db(database_list::LIVE_MYSQL_1);
    $echo = new echo_values();
    $sessions = new sessions();
    
    //options get json values (form data dont support array values)
    if (isset($_POST["options"])){ user::post("options", json_decode(user::post("options"),true)); }
    variable::clear_all_data($_POST);

    switch (user::post(post_keys::SET_TYPE)) {
        case set_types::INSERT:
            new insert($db, $sessions, $echo);
            break;
        case set_types::UPDATE:
            new update($db, $sessions, $echo);
            break;
        case set_types::DELETE:
            new delete($db, $sessions, $echo);
            break;
        case set_types::OPTION_INSERT:
            new insert_option($db, $sessions, $echo);
            break;
        case set_types::OPTION_DELETE:
            new delete_option($db,$sessions,$echo);
            break;
        case set_types::TRANSLATE_PRODUCT:
            new translate_product($db,$sessions,$echo);
            break;
        case set_types::TRANSLATE_CATEGORY:
            new translate_category($db,$sessions,$echo);
            break;
    }

    $echo->return();

}
