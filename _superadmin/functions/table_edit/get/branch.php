<?php
namespace _superadmin\functions\table_edit\get;
use config\db;
use config\table_helper\branch_info as tbl;

use matrix_library\php\operations\user;
use _superadmin\sameparts\functions\sessions\get;


class branch
{
    public function __construct(db $db, get $sessions, &$echo){
        $echo->rows = (array)$db->db_select(
            array(
                tbl::ID,
                tbl::NAME,
            ),
            tbl::TABLE_NAME,
            where: $db->where->equals([tbl::ACTIVE => true]),
            order_by: $db->order_by(tbl::ID, db::DESC),
        )->rows;
    }
}