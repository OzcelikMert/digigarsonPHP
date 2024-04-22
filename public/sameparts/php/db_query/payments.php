<?php
namespace sameparts\php\db_query;

use config\db;
use config\table_helper\order_payments as tbl;
use config\table_helper\order_payment_status_types as tbl2;
use config\table_helper\account_types as tbl5;
use config\table_helper\branch_users as tbl6;
use config\table_helper\customer_users as tbl7;
use config\table_helper\branch_manage_users as tbl8;
use matrix_library\php\db_helpers\results;

class payments extends helper {
    public static function get(
        db $db,
        string $language,
        int $branch_id,
        int $id = null,
        int $order_id = null,
        int $type_id = null,
        int $status_id = null,
        int $safe_id = null,
        string $custom_where = "",
        string $group_by = "",
        string $order_by = "",
        array $limit = [0, 0]
    ) : results{
        return $db->db_select(
            array(
                tbl::ID,
                tbl::ORDER_ID,
                tbl::TYPE,
                tbl::STATUS,
                tbl::PRICE,
                tbl::DATE,
                $db->as_name(
                    $db->case->equals(
                        [tbl::ACCOUNT_TYPE => 3],
                        tbl8::NAME,
                        $db->case->equals([tbl::ACCOUNT_TYPE => 1], tbl7::NAME, tbl6::NAME)
                    ),
                    "account_name"
                ),
                $db->as_name(tbl5::NAME.$language, "account_type"),
                tbl::SAFE_ID,
                tbl::COMMENT
            ),
            tbl::TABLE_NAME,
            $db->join->left(array(
                tbl7::TABLE_NAME => [tbl::ACCOUNT_TYPE => 1, tbl7::ID => tbl::ACCOUNT_ID],
                tbl6::TABLE_NAME => [tbl::ACCOUNT_TYPE => 2, tbl6::ID => tbl::ACCOUNT_ID],
                tbl8::TABLE_NAME => [tbl::ACCOUNT_TYPE => 3, tbl8::ID => tbl::ACCOUNT_ID],
                tbl5::TABLE_NAME => [tbl5::ID => tbl::ACCOUNT_TYPE],
            )),
            parent::check_where($db, array(
                tbl::BRANCH_ID => $branch_id,
                tbl::ID => $id,
                tbl::ORDER_ID => $order_id,
                tbl::TYPE => $type_id,
                tbl::STATUS => $status_id,
                tbl::SAFE_ID => $safe_id,
                tbl::IS_DELETE => 0
            )).parent::check_and($custom_where)." {$custom_where}",
            $group_by,
            $order_by,
            parent::check_limit($limit)
        );
    }

    public static function get_status_types(
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