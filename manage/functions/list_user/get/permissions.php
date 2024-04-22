<?php
namespace manage\functions\list_user\get;

use config\db;
use config\sessions;
use sameparts\php\ajax\echo_values;
use config\table_helper\branch_user_permission_types as tbl;
use sameparts\php\db_query\branch_users;

class permissions {
    public function __construct(db $db, sessions $sessions, echo_values &$echo) {
        $echo->custom_data = $this->get($db, $sessions);
    }

    private function get(db $db, sessions $sessions) : array{
        return branch_users::get_permissions(
            $db,
            $sessions->get->LANGUAGE_TAG,
            order_by: $db->order_by(tbl::ID, db::DESC)
        )->rows;
    }
}