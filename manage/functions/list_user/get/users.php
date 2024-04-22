<?php
namespace manage\functions\list_user\get;

use config\db;
use config\sessions;
use sameparts\php\ajax\echo_values;
use config\table_helper\branch_users as tbl;

class users {
    public function __construct(db $db, sessions $sessions, echo_values &$echo) {
        $echo->custom_data = $this->get($db, $sessions);
    }

    private function get(db $db, sessions $sessions) : array{
        return $db->db_select(
            array(
                tbl::ID,
                tbl::NAME,
                tbl::ACTIVE,
                tbl::PERMISSIONS
            ),
            tbl::TABLE_NAME,
            where: $db->where->equals([
                tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl::IS_DELETE => 0
            ]),
            order_by: $db->order_by(tbl::ID, db::DESC)
        )->rows;
    }
}