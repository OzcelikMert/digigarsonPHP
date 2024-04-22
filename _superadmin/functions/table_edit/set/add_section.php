<?php


namespace _superadmin\functions\table_edit\set;

use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\user;
use _superadmin\sameparts\functions\sessions\get;
use config\db;
use config\table_helper\table_section_types as tbl;
use sameparts\php\ajax\echo_values;

class post_keys{
    const
        NAME = "name";
}

class add_section
{
    public function __construct(db $db, get $sessions, echo_values &$echo)
    {
        $echo->rows = (array)($this->insert($db, $sessions, $echo));
    }
    private function insert(db $db, get $sessions, &$echo) :results{
        return $db->db_insert(tbl::TABLE_NAME,
            [
                tbl::NAME."tr" => user::post(post_keys::NAME)
            ],
        );
        }
}