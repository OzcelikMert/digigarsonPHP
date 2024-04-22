<?php
namespace sameparts\php\db_query;

use config\db;
use config\table_helper\branch_tables as tbl;
use config\table_helper\branch_sections as tbl2;
use config\table_helper\table_section_types as tbl3;
use matrix_library\php\db_helpers\results;

class branch_tables extends helper {
    public static function get(
        db $db,
        int $branch_id = null,
        int $id = null,
        int $section_id = null,
        string $custom_where = "",
        string $group_by = "",
        string $order_by = "",
        array $limit = [0, 0],
        bool $check_and_disabled = false,
    ) : results{
        return $db->db_select(
            array(
                tbl::BRANCH_ID,
                tbl::ID,
                tbl::CREATE_DATE,
                tbl::IS_LOCK,
                tbl::SECTION_ID,
                tbl::TABLE_NO,
                tbl::TABLE_TYPE,
                tbl::TABLE_SHAPE_TYPE
            ),
            tbl::TABLE_NAME,
            "",
            parent::check_where($db, array(
                tbl::BRANCH_ID => [$branch_id, 0],
                tbl::ID => $id,
                tbl::SECTION_ID => $section_id,
                tbl::IS_DELETE  => 0
            )).parent::check_and($custom_where,$check_and_disabled)." {$custom_where}",
            $group_by,
            $order_by,
            parent::check_limit($limit)
        );
    }

    public static function get_sections(
        db $db,
        int $branch_id,
        int $id = null,
        int $section_id = null,
        string $custom_where = "",
        string $group_by = "",
        string $order_by = "",
        array $limit = [0, 0]
    ) : results{
        return $db->db_select(
            array(
                tbl2::ID,
                tbl2::BRANCH_ID,
                tbl2::SECTION_ID,
                tbl2::IS_ACTIVE
            ),
            tbl2::TABLE_NAME,
            "",
            parent::check_where($db, array(
                tbl2::BRANCH_ID => [$branch_id, 0],
                tbl2::ID => $id,
                tbl2::SECTION_ID => $section_id
            )).parent::check_and($custom_where)." {$custom_where}",
            $group_by,
            $order_by,
            parent::check_limit($limit)
        );
    }

    public static function get_section_types(
        db $db,
        string $language,
        int $id = null,
        string $custom_where = "",
        string $group_by = "",
        string $order_by = "",
        array $limit = [0, 0]
    ) : results{
        return $db->db_select(
            array(
                tbl3::ID,
                $db->as_name(tbl3::NAME.$language, "name")
            ),
            tbl3::TABLE_NAME,
            "",
            parent::check_where($db, array(tbl3::ID => $id)).parent::check_and($custom_where)." {$custom_where}",
            $group_by,
            $order_by,
            parent::check_limit($limit)
        );
    }
}