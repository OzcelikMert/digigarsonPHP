<?php
namespace sameparts\php\db_query;

use config\db;
use config\table_helper\branch_payment_types as tbl;
use config\table_helper\payment_types as tbl2;
use matrix_library\php\db_helpers\results;

class branch_payment_types extends helper {
    public static function get(
        db $db,
        int $branch_id,
        int $id = null,
        int $type_id = null,
        string $custom_where = "",
        string $group_by = "",
        string $order_by = "",
        array $limit = [0, 0]
    ) : results{
        return $db->db_select(
            array(
                tbl::ID,
                tbl::TYPE_ID,
                tbl::RANK,
                tbl::ACTIVE,
                tbl::ACTIVE_TAKE_AWAY
            ),
            tbl::TABLE_NAME,
            "",
            parent::check_where($db, array(
                tbl::BRANCH_ID => $branch_id,
                tbl::ID => $id,
                tbl::TYPE_ID => $type_id
            )).parent::check_and($custom_where)." {$custom_where}",
            $group_by,
            $order_by,
            parent::check_limit($limit)
        );
    }

    public static function get_types(
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
                tbl2::ID,
                $db->as_name(tbl2::NAME.$language, "name")
            ),
            tbl2::TABLE_NAME,
            "",
            parent::check_where($db, array(tbl2::ID => $id)).parent::check_and($custom_where)." {$custom_where}",
            $group_by,
            $order_by,
            parent::check_limit($limit)
        );
    }
}