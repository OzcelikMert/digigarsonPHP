<?php
require "../../../../matrix_library/php/auto_loader.php";

use config\db;
use config\sessions\check;
use config\sessions\keys;
use config\sessions;
use sameparts\php\db_query\address as address_service;
use matrix_library\php\operations\user;
use matrix_library\php\operations\method_types;
use matrix_library\php\operations\clear_types;
use matrix_library\php\operations\variable;
use sameparts\php\ajax\echo_values;
use config\table_helper\branch_users as tbl;
use config\table_helper\branch_info as tbl3;
use config\table_helper\branch_manage_users as tbl5;

/* CONST Values */
class post_keys {
    const PAGE_NAME = "page_name",
        GET_TYPE = "get_type";
}
/* end Const Values */

if(
    check::check(false) &&
    user::check_sent_data(
        array(
            post_keys::PAGE_NAME,
            post_keys::GET_TYPE
        )
    )
){
    $db = new db(config\database_list::LIVE_MYSQL_1);
    $sessions = new sessions();
    $echo = new echo_values();

    set_echo_values($db, $sessions, $echo);

    $echo->rows["branch_id_main"] = $sessions->get->BRANCH_ID_MAIN;

    $echo->return();
}

function set_echo_values(db $db, sessions $sessions, echo_values &$echo) : void{
    $type = variable::clear_method(post_keys::GET_TYPE, clear_types::INT);

    if(($type == get_types::ALL || $type ==  get_types::BRANCH_ID)) $echo->rows["branch_id"] = $sessions->get->BRANCH_ID;
    if(($type == get_types::ALL || $type ==  get_types::CURRENCY)) $echo->rows["currency"] = $sessions->get->CURRENCY;
    if(($type == get_types::ALL || $type ==  get_types::GET_CITIES)) $echo->rows["city"] = (new address_service())->get_city()->rows;

    if(($type == get_types::ALL || $type ==  get_types::TAKEAWAY_ADDRESS)) {
       //$result = $db->db_select(array(tbl2::ID, tbl2::NEIGHBORHOOD_ID), tbl2::TABLE_NAME, where: $db->where->equals([tbl2::BRANCH_ID => $sessions->get->BRANCH_ID]))->rows;
       $result = sameparts\php\db_query\branch_info::takeaway_accepted_neighborhoods($db,$sessions->get->BRANCH_ID)->rows;
       $address = new address_service();
       $neighborhood = array();

       foreach ($result as $item){
           array_push($neighborhood,$item["neighborhood_id"]);
       }
       $echo->rows["takeaway_address"] = $result;
       $echo->rows["takeaway_address_names"] = $address->get_neighborhood_names($neighborhood)->rows;

    }


    if(($type == get_types::ALL || $type ==  get_types::BRANCH_USERS)) $echo->rows["branch_users"] = array_merge(
        $db->db_select(
            array(
                tbl::ID,
                $db->as_name(\config\type_tables_values\account_types::WAITER, "account_type"),
                tbl::NAME,
                tbl::ACTIVE,
                tbl::PERMISSIONS
            ),
            tbl::TABLE_NAME,
            where: $db->where->equals([tbl::BRANCH_ID => [$sessions->get->BRANCH_ID, $sessions->get->BRANCH_MAIN_ID]])
        )->rows,
        $db->db_select(
            array(
                tbl5::ID,
                $db->as_name(\config\type_tables_values\account_types::MANAGE, "account_type"),
                tbl5::NAME,
                tbl5::ACTIVE,
                tbl5::PERMISSIONS
            ),
            tbl5::TABLE_NAME,
            where: $db->where->equals([tbl5::BRANCH_ID => [$sessions->get->BRANCH_ID, $sessions->get->BRANCH_MAIN_ID]])
        )->rows
    );

    if(($type == get_types::ALL || $type ==  get_types::BRANCH_INFO)) $echo->rows["branch_info"] = $db->db_select(
        array(tbl3::ALL), tbl3::TABLE_NAME,
        where: $db->where->equals([tbl3::ID => $sessions->get->BRANCH_ID])
    )->rows;

    if(($type == get_types::ALL || $type ==  get_types::WORK_TIMES)) $echo->rows["work_times"] = sameparts\php\db_query\branch_info::branch_work_times($db,$sessions->get->BRANCH_ID)->rows;

    if(($type == get_types::ALL || $type ==  get_types::BRANCHES)) $echo->rows["branches"] = $sessions->get->BRANCHES_NAMES;

}

class get_types {
    const ALL = 0x0001,
        CURRENCY = 0x0002,
        BRANCH_ID = 0x0003,
        BRANCH_USERS = 0x0004,
        GET_CITIES = 0x0005,
        BRANCH_INFO = 0x0006,
        WORK_TIMES = 0x0007,
        TAKEAWAY_ADDRESS = 0x0008,
        BRANCHES = 0x0009;
}
