<?php
namespace manage\functions\branch_settings\set;

use config\db;
use config\table_helper\branch_info as tbl;
use config\sessions;
use matrix_library\php\operations\user;
use sameparts\php\ajax\echo_values;

class post_keys{ CONST ADDRESS = "text"; }


class long_address  {
    function __construct(db $db, sessions $sessions, echo_values &$echo){
        if ($sessions->get->BRANCH_ID > 0) {
            $this->update($db,$sessions,$echo);
        }
    }

    private function update(db $db, sessions $sessions, echo_values &$echo): void{
        $db->db_update(
            tbl::TABLE_NAME,
            array(tbl::ADDRESS => user::post(post_keys::ADDRESS)),
            where: $db->where->equals([tbl::ID => $sessions->get->BRANCH_ID])
        );
    }
}
