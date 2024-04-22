<?php
namespace manage\functions\list_device\get;

use config\db;
use config\sessions;
use sameparts\php\ajax\echo_values;
use config\table_helper\device_types as tbl;

class types {
    public function __construct(db $db, sessions $sessions, echo_values &$echo) {
        $echo->custom_data = $this->get($db, $sessions);
    }

    private function get(db $db, sessions $sessions) : array{
        return $db->db_select(
            array(
                tbl::ID,
                $db->as_name(tbl::NAME.$sessions->get->LANGUAGE_TAG, "name")
            ),
            tbl::TABLE_NAME
        )->rows;
    }
}