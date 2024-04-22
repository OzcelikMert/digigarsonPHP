<?php
namespace manage\functions\list_device\get;

use config\db;
use config\sessions;
use sameparts\php\ajax\echo_values;
use config\table_helper\branch_devices as tbl;
use config\table_helper\device_types as tbl2;

class devices {
    public function __construct(db $db, sessions $sessions, echo_values &$echo) {
        $echo->custom_data = $this->get($db, $sessions);
    }

    private function get(db $db, sessions $sessions) : array{
        return $db->db_select(
            array(
                tbl::ID,
                tbl::NAME,
                tbl::TYPE,
                $db->as_name(tbl2::NAME.$sessions->get->LANGUAGE_TAG, "type_name"),
                tbl::TOKEN,
                tbl::SECURITY_CODE,
                tbl::IS_CONNECT,
                tbl::CALLER_ID_ACTIVE
            ),
            tbl::TABLE_NAME,
            joins: $db->join->inner([
                tbl2::TABLE_NAME => [tbl2::ID => tbl::TYPE]
            ]),
            where: $db->where->equals([
                tbl::BRANCH_ID => $sessions->get->BRANCH_ID
            ]),
            order_by: $db->order_by(tbl::ID, db::DESC)
        )->rows;
    }
}