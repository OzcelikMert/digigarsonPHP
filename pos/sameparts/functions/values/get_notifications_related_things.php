<?php
namespace pos\sameparts\functions;
require "../../../../matrix_library/php/auto_loader.php";

use config\db;
use config\sessions;
use config\sessions\check;
use config\settings;
use config\database_list;
use manage\functions\branch_settings\set\array_keys;
use matrix_library\php\operations\user;
use sameparts\php\ajax\echo_values;
use sameparts\php\db_query\notification;

$echo = new echo_values();
$sessions = new sessions();

if(check::check(false) && user::check_sent_data([post_keys::GET_TYPE])) {
    $db = new db(database_list::LIVE_MYSQL_1);

    switch (user::post(post_keys::GET_TYPE)) {
        case get_types::ALL:
            $echo->rows["notifications"] = (array)notification::get_send($db,$sessions->get->BRANCH_ID);
            $echo->rows["notification_types"] = (array)notification::get($db,$sessions->get->BRANCH_ID);
            break;
        case get_types::TYPES:
            $echo->rows["notification_types"] = (array)notification::get($db,$sessions->get->BRANCH_ID);
            break;
        case get_types::NOTIFICATIONS:
            $echo->rows["notifications"] = (array)notification::get_send($db,$sessions->get->BRANCH_ID);
            break;

    }
} else{
    $echo->status = false;
    $echo->error_code = settings::error_codes()::NO_PERM;
}
$echo->return();

class post_keys{
    const GET_TYPE = "get_type";
}

class get_types {
    const ALL = 0x0001,
        TYPES = 0x0002,
        NOTIFICATIONS = 0x0003;
}
