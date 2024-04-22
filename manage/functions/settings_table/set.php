<?php
namespace manage\functions\settings_table;
require "../../../matrix_library/php/auto_loader.php";

use config\database_list;
use config\db;
use manage\functions\settings_table\set\sort_table_sections;
use config\sessions\check;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use config\sessions;
use sameparts\php\ajax\echo_values;

/* CONST Values */

class set_types {
    const SORT_TABLE_SECTIONS = 0x0001;

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
        case set_types::SORT_TABLE_SECTIONS: (new sort_table_sections($db, $sessions, $echo)); break;
    }

    $echo->return();
}

/* end Functions */