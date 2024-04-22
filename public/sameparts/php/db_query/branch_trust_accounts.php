<?php
namespace sameparts\php\db_query;

use config\db;
use config\table_helper\branch_trust_accounts as tbl;
use config\table_helper\branch_trust_account_payments as tbl2;
use matrix_library\php\db_helpers\results;

class branch_trust_accounts extends helper {
    public static function get(
        db $db,
        int $branch_id = null,
        int $id = null,
        string $custom_where = "",
        string $group_by = "",
        string $order_by = "",
        int $is_delete = 0,
        array $limit = [0, 0],
        bool $check_and_disabled = false,
    ) : results{
        return $db->db_select(
            array(
                tbl::BRANCH_ID,
                tbl::ID,
                tbl::NAME,
                tbl::PHONE,
                tbl::ADDRESS,
                tbl::DISCOUNT,
                tbl::TAX_NO,
                tbl::TAX_ADMINISTRATION
            ),
            tbl::TABLE_NAME,
            "",
            parent::check_where($db, array(
                tbl::BRANCH_ID => $branch_id,
                tbl::ID => $id,
                tbl::IS_DELETE => $is_delete
            )).parent::check_and($custom_where,$check_and_disabled)." {$custom_where}",
            $group_by,
            $order_by,
            parent::check_limit($limit)
        );
    }

    public static function get_payments(
        db $db,
        int $branch_id = null,
        int $id = null,
        int $payment_id = null,
        int $trust_account_id = null,
        string $custom_join = "",
        string $custom_where = "",
        string $group_by = "",
        string $order_by = "",
        int $is_delete = 0,
        array $limit = [0, 0],
        bool $check_and_disabled = false,
    ) : results{
        return $db->db_select(
            array(
                tbl2::ID,
                tbl2::PAYMENT_ID,
                tbl2::TRUST_ACCOUNT_ID,
                tbl2::COMMENT,
                tbl2::DISCOUNT,
                tbl2::IS_DELETE
            ),
            tbl2::TABLE_NAME,
            $custom_join,
            parent::check_where($db, array(
                tbl2::ID => $id,
                tbl2::BRANCH_ID => $branch_id,
                tbl2::PAYMENT_ID => $payment_id,
                tbl2::TRUST_ACCOUNT_ID => $trust_account_id,
                tbl2::IS_DELETE => $is_delete
            )).parent::check_and($custom_where,$check_and_disabled)." {$custom_where}",
            $group_by,
            $order_by,
            parent::check_limit($limit)
        );
    }
}