<?php
namespace _superadmin\functions\table_edit\get;
use config\db;
use config\table_helper\branch_tables as tbl;
use config\table_helper\branch_sections as tbl2;
use config\table_helper\table_section_types as tbl3;
use matrix_library\php\operations\user;
use _superadmin\sameparts\functions\sessions\get;

class post_keys{
    const BRANCH_ID = "id";
}
class getbranchlist
{
public function __construct(db $db, get $sessions, &$echo){
    $echo->rows = (array)$db->db_select(
        array(
            tbl::ID,
            tbl::TABLE_NO,
            tbl::URL,
            $db->as_name(tbl3::NAME."tr", "section_name")
        ),
        tbl::TABLE_NAME,
        joins: $db->join->left([
        tbl2::TABLE_NAME => [tbl2::ID => tbl::SECTION_ID],
        tbl3::TABLE_NAME => [tbl3::ID => tbl2::SECTION_ID]
    ]),
        where: $db->where->equals([
        tbl::BRANCH_ID => user::post(post_keys::BRANCH_ID),
        tbl::IS_DELETE => 0
    ]),
        order_by: $db->order_by(tbl::ID, db::ASC)
    )->rows;
}
}