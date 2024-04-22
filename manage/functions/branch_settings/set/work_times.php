<?php
namespace manage\functions\branch_settings\set;

use config\db;
use config\table_helper\branch_work_times as tbl;
use config\sessions;
use matrix_library\php\operations\user;
use sameparts\php\ajax\echo_values;

class post_keys{ CONST DAY = "day";}


class work_times{
    function __construct(db $db, sessions $sessions, echo_values &$echo){
        if ($sessions->get->BRANCH_ID > 0 && user::check_sent_data([post_keys::DAY])) {
            if (count(user::post(post_keys::DAY)) < 10) $this->check($db,$sessions,$echo);
        }
    }

    private function check(db $db, sessions $sessions, echo_values &$echo): void{
        $result = $db->db_select(tbl::ID, tbl::TABLE_NAME,
            where: $db->where->equals([tbl::BRANCH_ID => $sessions->get->BRANCH_ID])
        );

        if (count($result->rows) > 0){
            $this->update($db,$sessions,$echo);
        }else {
            $this->insert($db,$sessions,$echo);
        }
    }

    private function update(db $db, sessions $sessions, echo_values &$echo): void{
        $days = user::post(post_keys::DAY);
        $i = 1;

        foreach ($days as $day){
           $echo->custom_data["day"][$i] = $db->db_update(
                tbl::TABLE_NAME,
                array(
                    tbl::START_TIME => $day[0],
                    tbl::STOP_TIME => $day[1],
                    tbl::ACTIVE => $day[2],
                ), where: $db->where->equals([tbl::BRANCH_ID => $sessions->get->BRANCH_ID,tbl::DAY_ID => $i])
            );
            $i++;
        }
    }
    private function insert(db $db, sessions $sessions, echo_values &$echo): void{
        $days = user::post(post_keys::DAY);
        $i = 1;

        foreach ($days as $day){
            $echo->custom_data["day"][$i] = $db->db_insert(
                tbl::TABLE_NAME,
                array(
                    tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                    tbl::DAY_ID => $i,
                    tbl::START_TIME => $day[0],
                    tbl::STOP_TIME => $day[1],
                    tbl::ACTIVE => $day[2],
                )
            );
            $i++;
        }
    }


}
