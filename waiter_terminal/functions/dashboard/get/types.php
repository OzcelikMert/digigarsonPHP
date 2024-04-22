<?php
namespace manage\functions\list_device\get;

use config\db;
use manage\sameparts\functions\sessions\get;
use sameparts\php\ajax\echo_values;
use config\table_helper\device_types as tbl;

class types {
    public function __construct(db $db, get $sessions, echo_values &$echo) {
        $echo->custom_data = $this->get($db, $sessions);
    }

    private function get(db $db, get $sessions) : array{
        return $db->db_select(
            array(
                tbl::ID,
                $db->as_name(tbl::NAME.$sessions->LANGUAGE_TAG, "name")
            ),
            tbl::TABLE_NAME
        )->rows;
    }
}