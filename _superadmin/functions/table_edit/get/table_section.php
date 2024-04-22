<?php
namespace _superadmin\functions\table_edit\get;

use _superadmin\sameparts\functions\sessions\get;
use config\db;
use sameparts\php\ajax\echo_values;
use config\table_helper\table_section_types as tbl;
class table_section
{
    public function __construct(db $db, get $sessions, &$echo)
    {
        $echo->rows = (array)$db->db_select(
            array(
                tbl::ID,
                $db->as_name(tbl::NAME."tr", "name")
            ),
            tbl::TABLE_NAME,
            order_by: $db->order_by("name", db::ASC)
        )->rows;
    }
}