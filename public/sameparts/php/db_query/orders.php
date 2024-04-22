<?php
namespace sameparts\php\db_query;

use config\db;
use config\table_helper\orders as tbl;
use config\table_helper\order_products as tbl2;
use config\table_helper\order_types as tbl3;
use config\table_helper\order_status_types as tbl4;
use config\table_helper\account_types as tbl5;
use config\table_helper\branch_users as tbl6;
use config\table_helper\customer_users as tbl7;
use config\table_helper\order_payments as tbl8;
use config\table_helper\catering_owners as tbl9;
use config\table_helper\catering_questions as tbl10;
use config\table_helper\caterings as tbl11;
use config\table_helper\order_product_options as tbl12;
use config\table_helper\integrate_customers as tbl13;
use config\type_tables_values\account_types;
use matrix_library\php\db_helpers\results;

class orders extends helper {
    public static function get(
        db $db,
        int $branch_id,
        int $id = null,
        int $table_id = null,
        string $join = "",
        string $custom_where = "",
        string $group_by = "",
        string $order_by = "",
        array $limit = [0, 0]
    ) : results{
        return $db->db_select(
            array(
                tbl::ID,
                tbl::TABLE_ID,
                tbl::STATUS,
                tbl::TYPE,
                tbl::DISCOUNT,
                tbl::DATE_END,
                tbl::DATE_START,
                tbl::SAFE_ID,
                tbl::NO,
                tbl::ADDRESS_ID,
                tbl::IS_CONFIRM,
                tbl::CONFIRMED_ACCOUNT_ID,
                tbl::COURIER_ID,
                tbl::IS_PRINT
            ),
            tbl::TABLE_NAME,
            $join,
            parent::check_where($db, array(
                tbl::BRANCH_ID => $branch_id,
                tbl::ID => $id,
                tbl::TABLE_ID => $table_id
            )).parent::check_and($custom_where)." {$custom_where}",
            $group_by,
            $order_by,
            parent::check_limit($limit)
        );
    }

    public static function get_products(
        db $db,
        string $language,
        int $branch_id,
        int $id = null,
        int $product_id = null,
        int $order_id = null,
        string $custom_join = "",
        string $custom_where  = "",
        string $group_by = "",
        string $order_by = "",
        array $limit = [0, 0]
    ) : results{
        return $db->db_select(
            array(
                tbl2::ID,
                tbl2::PRODUCT_ID,
                tbl2::ORDER_ID,
                $db->as_name(
                    $db->case->equals(
                        [tbl2::ACCOUNT_TYPE => account_types::CUSTOMER],
                        tbl7::NAME,
                        $db->case->equals(
                            [tbl2::ACCOUNT_TYPE => account_types::WAITER],
                            tbl6::NAME,
                            $db->case->equals(
                                [tbl2::ACCOUNT_TYPE => account_types::YEMEK_SEPETI],
                                tbl13::NAME,
                                "NULL"
                            )
                        )
                    ),
                    "account_name"
                ),
                $db->as_name(tbl5::NAME.$language, "account_type"),
                tbl2::PRICE,
                tbl2::VAT,
                tbl2::DISCOUNT,
                tbl2::QTY,
                tbl2::QUANTITY,
                tbl2::COMMENT,
                tbl2::STATUS,
                tbl2::TYPE,
                tbl2::PRICE_CHANGED,
                tbl2::TIME,
            ),
            tbl2::TABLE_NAME,
            $db->join->left(array(
                tbl7::TABLE_NAME => [tbl2::ACCOUNT_TYPE => account_types::CUSTOMER, tbl7::ID => tbl2::ACCOUNT_ID],
                tbl6::TABLE_NAME => [tbl2::ACCOUNT_TYPE => account_types::WAITER, tbl6::ID => tbl2::ACCOUNT_ID],
                tbl13::TABLE_NAME => [tbl2::ACCOUNT_TYPE => account_types::YEMEK_SEPETI, tbl13::ID => tbl2::ACCOUNT_ID],
                tbl5::TABLE_NAME => [tbl5::ID => tbl2::ACCOUNT_TYPE],
                tbl::TABLE_NAME  => [tbl::ID => tbl2::ORDER_ID]
            )).$custom_join,
            parent::check_where($db, array(
                tbl2::BRANCH_ID => $branch_id,
                tbl2::ID => $id,
                tbl2::PRODUCT_ID => $product_id,
                tbl2::ORDER_ID => $order_id
            )).parent::check_and($custom_where)." {$custom_where}",
            $group_by,
            $order_by,
            parent::check_limit($limit)
        );
    }

    public static function get_product_options(
        db $db,
        int $branch_id,
        int $id = null,
        int $order_product_id = null,
        int $option_id = null,
        int $option_item_id = null,
        string $custom_join = "",
        string $custom_where  = "",
        string $group_by = "",
        string $order_by = "",
        array $limit = [0, 0]
    ) : results{
        return $db->db_select(
            array(
                tbl12::ID,
                tbl12::BRANCH_ID,
                tbl12::ORDER_PRODUCT_ID,
                tbl12::OPTION_ID,
                tbl12::OPTION_ITEM_ID,
                tbl12::PRICE,
                tbl12::QTY
            ),
            tbl12::TABLE_NAME,
            $custom_join,
            parent::check_where($db, array(
                tbl12::BRANCH_ID => $branch_id,
                tbl12::ID => $id,
                tbl12::ORDER_PRODUCT_ID => $order_product_id,
                tbl12::OPTION_ID => $option_id,
                tbl12::OPTION_ITEM_ID => $option_item_id
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

    public static function order_status_types(
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
                tbl4::ID,
                $db->as_name(tbl4::NAME.$language, "name")
            ),
            tbl4::TABLE_NAME,
            "",
            parent::check_where($db, array(tbl4::ID => $id)).parent::check_and($custom_where)." {$custom_where}",
            $group_by,
            $order_by,
            parent::check_limit($limit)
        );
    }

    public static function get_catering_owners(
        db $db,
        int $branch_id,
        int $id = null,
        string $custom_join = "",
        string $custom_where  = "",
        string $group_by = "",
        string $order_by = "",
        array $limit = [0, 0]
    ) : results{
        return $db->db_select(
            array(
                tbl9::ID,
                tbl9::NAME
            ),
            tbl9::TABLE_NAME,
            $custom_join,
            parent::check_where($db, array(
                tbl9::BRANCH_ID => $branch_id,
                tbl9::ID => $id,
                tbl9::IS_DELETE => 0
            )).parent::check_and($custom_where)." {$custom_where}",
            $group_by,
            $order_by,
            parent::check_limit($limit)
        );
    }

    public static function get_catering_questions(
        db $db,
        int $branch_id,
        int $id = null,
        string $custom_join = "",
        string $custom_where  = "",
        string $group_by = "",
        string $order_by = "",
        array $limit = [0, 0]
    ) : results{
        return $db->db_select(
            array(
                tbl10::ID,
                tbl10::COMMENT
            ),
            tbl10::TABLE_NAME,
            $custom_join,
            parent::check_where($db, array(
                tbl10::BRANCH_ID => $branch_id,
                tbl10::ID => $id,
                tbl10::IS_DELETE => 0
            )).parent::check_and($custom_where)." {$custom_where}",
            $group_by,
            $order_by,
            parent::check_limit($limit)
        );
    }

    public static function get_caterings(
        db $db,
        int $branch_id,
        int $id = null,
        int $order_product_id = null,
        string $custom_join = "",
        string $custom_where  = "",
        string $group_by = "",
        string $order_by = "",
        array $limit = [0, 0]
    ) : results{
        return $db->db_select(
            array(
                tbl11::ID,
                tbl11::PRODUCT_ID,
                tbl11::OWNER_ID,
                tbl11::QUESTION_ID,
                tbl11::DATE
            ),
            tbl11::TABLE_NAME,
            $custom_join,
            parent::check_where($db, array(
                tbl11::BRANCH_ID => $branch_id,
                tbl11::PRODUCT_ID => $order_product_id,
                tbl11::ID => $id
            )).parent::check_and($custom_where)." {$custom_where}",
            $group_by,
            $order_by,
            parent::check_limit($limit)
        );
    }

    public static function get_order_last_no(
        db $db,
        int $branch_id,
        bool $plus_one = true
    ) : int{
        $column_plus_one = ($plus_one) ? " + 1" : "";
        return (int)$db->db_select(
            $db->as_name($db->if_null($db->max(tbl::NO), 0).$column_plus_one, "last_no"),
            tbl::TABLE_NAME,
            "",
            parent::check_where($db, array(
                tbl::BRANCH_ID => $branch_id,
                tbl::SAFE_ID => 0
            ))
        )->rows[0]["last_no"];
    }
}