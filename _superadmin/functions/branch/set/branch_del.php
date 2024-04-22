<?php

namespace _superadmin\functions\branch\set;

use _superadmin\sameparts\functions\sessions\get;
use config\db;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\user;
use sameparts\php\ajax\echo_values;
use config\table_helper\branch_info as tbl;

class post_keys { const ID = "branch_id";}

class branch_del
{
    public function __construct(db $db, get $sessions, echo_values &$echo){
        $echo->rows = (array)$this->update($db, $sessions, $echo);
    }

    private function update(db $db, get $sessions, echo_values $echo): results {
        return $db->db_update(
            tbl::TABLE_NAME,
            [
                tbl::ACTIVE => false,
                tbl::QR_ACTIVE => false,
            ],
            where: $db->where->equals([
            tbl::ID => user::post(post_keys::ID),
            ]),
        );
    }

}