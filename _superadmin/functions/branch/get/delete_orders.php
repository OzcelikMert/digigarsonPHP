<?php
namespace _superadmin\functions\branch\get;

use _superadmin\sameparts\functions\sessions\get;
use config\db;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\user;
use sameparts\php\ajax\echo_values;
use config\table_helper\orders as tbl1;

class post_keys{
    const
    DATE = "date",
        BRANCH_ID="branch_id",
    FUNCTION_TYPE = "function_type";
}
class function_type{
    const
    DELETE_ORDER_LIST= 0x0007;
}
class delete_orders
{
    public function __construct(db $db, get $sessions, echo_values &$echo){
        $echo->rows = match ((int)user::post(post_keys::FUNCTION_TYPE)) {
            function_type::DELETE_ORDER_LIST => $this->delete_orders($db, $sessions, $echo)->rows,
        };
    }
    private function delete_orders(db $db, get $sessions, echo_values &$echo) : results{
        (int)$date_start  = user::post(post_keys::DATE)." 00:00:00";
        (int)$date_end = user::post(post_keys::DATE)." 23:59:59";
        $where = $db->where->equals([tbl1::BRANCH_ID => user::post(post_keys::BRANCH_ID)])." ".db::AND." ".$db->where->between([tbl1::DATE_START => [$date_start, $date_end]]);
        return $db->db_select(
            [tbl1::ID,
                tbl1::DATE_START,
                tbl1::BRANCH_ID,
                ],
            tbl1::TABLE_NAME,
            where: $where,
        );
    }
}