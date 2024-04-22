<?php
namespace order_app\functions\panel\set;

use config\db;
use manage\functions\report_safe\get\trust;
use matrix_library\php\operations\user;
use order_app\sameparts\functions\sessions\get;
use sameparts\php\ajax\echo_values;
use config\table_helper\notifications as tbl;
use config\table_helper\send_notification as tbl2;
use sameparts\php\helper\date;


class post_keys {
    const ID = "id";
}


class notification
{

    function __construct(db $db, get $sessions,echo_values &$echo){
       // $this->check_values($db,$echo,$sessions);

      // if ($echo->status) {
         $echo->custom_data["send_notifications"] = $db->db_insert(tbl2::TABLE_NAME,array(
                tbl2::BRANCH_ID => $sessions->SELECT_BRANCH_ID,
                tbl2::USER_ID => $sessions->USER_ID,
                tbl2::TABLE_ID => $sessions->SELECT_BRANCH_TABLE_ID,
                tbl2::NOTIFICATION_ID => user::post(post_keys::ID),
                tbl2::IP => user::get_ip_address(),
                tbl2::DATE_TIME => date(date::date_type_simples()::HYPHEN_DATE_TIME),
            ));
      //  }
    }

    function check_values(db $db,echo_values &$echo,get $sessions){
        $echo->status = false;
        if ($sessions->SELECT_BRANCH_ID > 0 && $sessions->USER_ID > 0 && (int)user::post(post_keys::ID) > 0){
           $result =  $db->db_select("*",tbl::TABLE_NAME,where: $db->where->equals([tbl::ID => user::post(post_keys::ID)]));
           if (count($result->rows) > 0){
               $echo->status  = true;
           }
        }
    }

}