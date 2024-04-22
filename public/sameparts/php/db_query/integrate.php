<?php
namespace sameparts\php\db_query;

use config\db;
use config\table_helper\integrate_users as tbl;
use config\table_helper\integrate_types as tbl2;
use config\table_helper\integrate_products as tbl3;
use config\table_helper\integrate_product_options as tbl4;
use config\table_helper\integrate_orders as tbl5;
use config\table_helper\integrate_payment_types as tbl6;
use config\table_helper\integrate_customers as tbl7;

use matrix_library\php\db_helpers\results;

class integrate extends helper {

    public static function get_users(
        db $db,
        int $branch_id,
        int $type = null,
        string $joins = "",
        string $custom_where = "",
        string $group_by = "",
        string $order_by = "",
        array $limit = [0, 0],
        bool $show_password = false
    ) : results{
        $columns_extra = array(
            tbl::PASSWORD
        );
        $columns = array(
            tbl::ID,
            tbl::USER_NAME,
            tbl::IS_ACTIVE,
            tbl::TYPE,
            tbl::BRANCH_ID
        );
        if($show_password) $columns = array_merge($columns, $columns_extra);
        return $db->db_select(
            $columns,
            tbl::TABLE_NAME,
            $joins,
            parent::check_where($db, array(
                tbl::BRANCH_ID => $branch_id,
                tbl::TYPE => $type,
            )).parent::check_and($custom_where)." {$custom_where}",
            $group_by,
            $order_by,
            parent::check_limit($limit)
        );
    }

    public static function get_types(
        db $db,
        int $id = null,
        string $custom_where = "",
        string $order_by = "",
        array $limit = [0, 0]
    ) : results{
        return $db->db_select(
            array(
                tbl2::ID,
                tbl2::NAME
            ),
            tbl2::TABLE_NAME,
            "",
            parent::check_where($db, array(
                tbl2::ID => $id,
            )).parent::check_and($custom_where)." {$custom_where}",
            "",
            $order_by,
            parent::check_limit($limit)
        );
    }

    public static function get_products(
        db $db,
        int $branch_id,
        int $type = null,
        string $joins = "",
        string $custom_where = "",
        string $group_by = "",
        string $order_by = "",
        array $limit = [0, 0]
    ) : results{
        return $db->db_select(
            array(
                tbl3::ID,
                tbl3::BRANCH_ID,
                tbl3::TYPE,
                tbl3::PRODUCT_ID,
                tbl3::PRODUCT_ID_INTEGRATED
            ),
            tbl3::TABLE_NAME,
            $joins,
            parent::check_where($db, array(
                tbl3::BRANCH_ID => $branch_id,
                tbl3::TYPE => $type,
            )).parent::check_and($custom_where)." {$custom_where}",
            $group_by,
            $order_by,
            parent::check_limit($limit)
        );
    }

    public static function get_product_options(
        db $db,
        int $branch_id,
        int $type = null,
        string $joins = "",
        string $custom_where = "",
        string $group_by = "",
        string $order_by = "",
        array $limit = [0, 0]
    ) : results{
        return $db->db_select(
            array(
                tbl4::ID,
                tbl4::BRANCH_ID,
                tbl4::TYPE,
                tbl4::OPTION_ID,
                tbl4::OPTION_ID_INTEGRATED
            ),
            tbl4::TABLE_NAME,
            $joins,
            parent::check_where($db, array(
                tbl4::BRANCH_ID => $branch_id,
                tbl4::TYPE => $type,
            )).parent::check_and($custom_where)." {$custom_where}",
            $group_by,
            $order_by,
            parent::check_limit($limit)
        );
    }

    public static function get_orders(
        db $db,
        int $branch_id,
        int $type = null,
        string $joins = "",
        string $custom_where = "",
        string $group_by = "",
        string $order_by = "",
        array $limit = [0, 0]
    ) : results{
        return $db->db_select(
            array(
                tbl5::ID,
                tbl5::BRANCH_ID,
                tbl5::TYPE,
                tbl5::ORDER_ID,
                tbl5::ORDER_ID_INTEGRATE,
                tbl5::SAFE_ID,
                tbl5::INTEGRATE_CUSTOMER_ID,
                tbl5::ADDRESS
            ),
            tbl5::TABLE_NAME,
            $joins,
            parent::check_where($db, array(
                tbl5::BRANCH_ID => $branch_id,
                tbl5::TYPE => $type,
            )).parent::check_and($custom_where)." {$custom_where}",
            $group_by,
            $order_by,
            parent::check_limit($limit)
        );
    }

    public static function get_payment_types(
        db $db,
        int $type = null,
        string $joins = "",
        string $custom_where = "",
        string $group_by = "",
        string $order_by = "",
        array $limit = [0, 0]
    ) : results{
        return $db->db_select(
            array(
                tbl6::ID,
                tbl6::TYPE,
                tbl6::TYPE_ID,
                tbl6::TYPE_ID_INTEGRATE
            ),
            tbl6::TABLE_NAME,
            $joins,
            parent::check_where($db, array(
                tbl6::TYPE => $type,
            )).parent::check_and($custom_where)." {$custom_where}",
            $group_by,
            $order_by,
            parent::check_limit($limit)
        );
    }

    public static function get_customers(
        db $db,
        int $type = null,
        string $joins = "",
        string $custom_where = "",
        string $group_by = "",
        string $order_by = "",
        array $limit = [0, 0]
    ) : results{
        return $db->db_select(
            array(
                tbl7::ID,
                tbl7::TYPE,
                tbl7::NAME,
                tbl7::ID_INTEGRATE
            ),
            tbl7::TABLE_NAME,
            $joins,
            parent::check_where($db, array(
                tbl7::TYPE => $type,
            )).parent::check_and($custom_where)." {$custom_where}",
            $group_by,
            $order_by,
            parent::check_limit($limit)
        );
    }
}
