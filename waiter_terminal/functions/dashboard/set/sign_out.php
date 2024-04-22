<?php
namespace waiter_terminal\functions\dashboard\set;

use config\db;
use config\table_helper\branch_devices as tbl;
use config\type_tables_values\device_types;
use matrix_library\php\db_helpers\results;
use sameparts\php\ajax\echo_values;
use waiter_terminal\sameparts\functions\sessions\get;

class sign_out {
    public function __construct(db $db, get $sessions, echo_values &$echo) {
        if($_SESSION){
            $this->update($db, $sessions);
            session_destroy();
        }
    }

    function update(db $db, get $sessions) : results{
        return $db->db_update(
            tbl::TABLE_NAME,
            array(
                tbl::IS_CONNECT => 0
            ),
            where: $db->where->equals([
                tbl::BRANCH_ID => $sessions->BRANCH_ID,
                tbl::ID => $sessions->DEVICE_ID,
                tbl::TYPE => device_types::WAITER_TERMINAL,
                tbl::IS_CONNECT => 1,
            ])
        );
    }
}