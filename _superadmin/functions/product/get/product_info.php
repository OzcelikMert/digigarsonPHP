<?php
namespace _superadmin\functions\product\get;

use _superadmin\sameparts\functions\sessions\get;
use config\db;
use config\table_helper\products as tbl;

class product_info{
    public function __construct(db $db, get $sessions, &$echo)
    {
     $echo->rows = $db->db_select(
         array(
             tbl::ID,
             tbl::NAME."tr"
         ),
         tbl::TABLE_NAME,
     )->rows;
    }
}