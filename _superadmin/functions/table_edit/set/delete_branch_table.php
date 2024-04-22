<?php
namespace _superadmin\functions\table_edit\set;

use config\db;
use _superadmin\sameparts\functions\sessions\get;
use matrix_library\php\operations\user;
use config\table_helper\branch_tables as tbl;
use sameparts\php\ajax\echo_values;
class post_keys{
    const DEL_ID = "del_id",
        BRANCH_ID = "branch_id";
}
class delete_branch_table
{
        public function __construct(db $db, get $sessions, &$echo){
            $echo->rows = $db->db_update(
                tbl::TABLE_NAME,
                array(
                    tbl::IS_DELETE => 1
                ),
                where: $db->where->equals([
                tbl::BRANCH_ID => user::post(post_keys::BRANCH_ID),
                tbl::ID => user::post(post_keys::DEL_ID)
            ]))->rows;
        }
}