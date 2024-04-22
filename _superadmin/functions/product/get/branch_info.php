<?php
namespace _superadmin\functions\product\get;

use _superadmin\sameparts\functions\sessions\get;
use config\db;
use config\table_helper\branch_info as tbl;

class branch_info{
    public function __construct(db $db, get $sessions, &$echo)
    {
        $echo->rows = $db->db_select(
            array(
                tbl::ID,
                tbl::NAME
            ),
            tbl::TABLE_NAME,
            where: $db->where->equals([tbl::ACTIVE => true]),
            order_by: $db->order_by(tbl::ID, db::DESC),
        )->rows;
    }
}