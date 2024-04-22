<?php
namespace manage\functions\branch_settings\set;

use config\db;
use config\table_helper\branch_info as tbl;
use config\sessions;
use matrix_library\php\operations\user;
use sameparts\php\ajax\echo_values;

class post_keys{ CONST MIN_TIME = "min_time",MIN_TOTAL = "min_total";  }


class takeaway_other_settings{
    function __construct(db $db, sessions $sessions, echo_values &$echo){
        if ($sessions->get->BRANCH_ID > 0 && user::check_sent_data([post_keys::MIN_TIME,post_keys::MIN_TOTAL])) {
            $this->update($db,$sessions,$echo);
        }
    }

    private function update(db $db, sessions $sessions, echo_values &$echo): void{
        $db->db_update(
            tbl::TABLE_NAME,
            array(tbl::TAKE_AWAY_TIME => user::post(post_keys::MIN_TIME)),
            where: $db->where->equals([tbl::ID => $sessions->get->BRANCH_ID])
        );
        $db->db_update(
            tbl::TABLE_NAME,
            array(tbl::TAKE_AWAY_AMOUNT => user::post(post_keys::MIN_TOTAL)),
            where: $db->where->equals([tbl::ID => $sessions->get->BRANCH_ID])
        );

    }
}
