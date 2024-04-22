<?php
namespace _superadmin\functions\branch\set;

use _superadmin\sameparts\functions\sessions\get;
use config\db;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\user;
use sameparts\php\ajax\echo_values;
use config\table_helper\orders as tbl1;

class post_keys{
    const
    DATE = "date",
    ORDER_ID ="id",
    BRANCH_ID="branch_id",
    DATA = "data",
    FUNCTION_TYPE = "function_type";
}
class function_type{
   const ORDERS_ALL_DELETE = 0x0008,
        ORDERS_SELECTED_DELETE = 0x0009;
}
class delete_orders
{
    public function __construct(db $db, get $sessions, echo_values &$echo){
        $echo->rows = (array)user::post(post_keys::DATA);
    }
}