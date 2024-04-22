<?php
namespace _superadmin\functions\product\get;

use _superadmin\functions\product\get\post_keys;
use _superadmin\sameparts\functions\sessions\get;
use config\db;
use config\table_helper\products as tbl;
use config\table_helper\product_categories as tbl2;
use matrix_library\php\operations\user;
use sameparts\php\ajax\echo_values;


class branch_id{
    public function __construct(db $db, get $sessions, echo_values &$echo)
    {
        $echo->rows = $db->db_select(
            array(
                tbl::ID,
                $db->as_name(tbl::NAME."tr", "product_name"),
                $db->as_name(tbl2::NAME."tr", "category_name"),
                tbl::CATEGORY_ID
            ),
            tbl::TABLE_NAME,
            $db->join->inner(array(
                tbl2::TABLE_NAME => [tbl::CATEGORY_ID => tbl2::ID]
            )),
            where: $db->where->equals([
                tbl::BRANCH_ID =>  $_POST["branch_id"]
            ])
        )->rows;
    }


}