<?php
namespace sameparts\php\db_query;
use config\db;
use config\table_helper\product_linked_options as tbl7;
use config\table_helper\products as tbl;
use config\table_helper\quantity_types as tbl2;
use config\table_helper\product_categories as tbl3;
use config\table_helper\product_option as tbl4;
use config\table_helper\product_option_items as tbl5;
use config\table_helper\product_option_group_types as tbl6;
use config\type_tables_values\order_types;
use matrix_library\php\db_helpers\results;

/* Tables
    * tbl1 => products
    * tbl2 => quantity_types
    * tbl3 => product_categories
    * tbl4 => product_option
    * tbl5 => product_option_items
    * tbl6 => product_option_group_types
*/
class products extends helper {
    public static function get(
        db $db,
        string $language,
        int $branch_id,
        int $id = null,
        int $category_id = null,
        bool $is_mobile = false,
        order_types|int $order_type = order_types::TABLE,
        string $custom_where = "",
        string $order_by = "",
        array $limit = [0, 0]
    ) : results{
        $array = [];

        if ($is_mobile){
            $price = match ($order_type) {
                order_types::TABLE => tbl::PRICE,
                order_types::TAKEAWAY => $db->as_name(tbl::PRICE_TAKE_AWAY, "price"),
                order_types::COME_TAKE => $db->as_name(tbl::PRICE_COME_TAKE, "price"),
                order_types::PERSONNEL => $db->as_name(tbl::PRICE_PERSONAL, "price"),
                order_types::OTHER => $db->as_name(tbl::PRICE_OTHER, "price"),
            };

            $active = match ($order_type) {
                order_types::TABLE, order_types::PERSONNEL, order_types::OTHER => $db->as_name(tbl::ACTIVE_POS,"active"),
                order_types::TAKEAWAY => $db->as_name(tbl::ACTIVE_TAKE_AWAY, "active"),
                order_types::COME_TAKE => $db->as_name(tbl::ACTIVE_COME_TAKE, "active"),
            };

            $array = array(
                tbl::ID,
                $db->as_name(tbl::NAME.$language, "name"),
                tbl::CATEGORY_ID,
                tbl::QUANTITY_ID,
                $price,
                $active,
                $db->as_name(tbl::COMMENT.$language, "comment"),
                tbl::RANK,
                tbl::IMAGE,
                tbl::START_TIME,
                tbl::END_TIME,
            );
        }else {
            $array = array(
                tbl::ID,
                $db->as_name(tbl::NAME.$language, "name"),
                tbl::CODE,
                tbl::CATEGORY_ID,
                tbl::QUANTITY_ID,
                tbl::PRICE,
                tbl::PRICE_SAFE,
                tbl::PRICE_PERSONAL,
                tbl::PRICE_TAKE_AWAY,
                tbl::PRICE_COME_TAKE,
                tbl::PRICE_OTHER,
                tbl::VAT,
                tbl::VAT_SAFE,
                tbl::VAT_PERSONAL,
                tbl::VAT_TAKE_AWAY,
                tbl::VAT_COME_TAKE,
                tbl::VAT_OTHER,
                tbl::ACTIVE_TAKE_AWAY,
                tbl::ACTIVE_POS,
                tbl::ACTIVE_COME_TAKE,
                tbl::ACTIVE_MOBILE,
                $db->as_name(tbl::COMMENT.$language, "comment"),
                tbl::RANK,
                tbl::IMAGE,
                tbl::FAVORITE,
                tbl::START_TIME,
                tbl::END_TIME,
                tbl::CREATE_DATE,
                tbl::DELETE,
                tbl::DELETE_IP,
                tbl::DELETE_DATE,
            );

        }

        return $db->db_select(
            $array,
            tbl::TABLE_NAME,
            "",
            parent::check_where($db, array(
                tbl::BRANCH_ID => $branch_id,
                tbl::ID => $id,
                tbl::CATEGORY_ID => $category_id
            )).parent::check_and($custom_where)." {$custom_where}",
            "",
            $order_by,
            parent::check_limit($limit)
        );
    }

    public static function get_quantity_types(
        db $db,
        string $language,
        int $id = null,
        string $custom_where = "",
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
            "",
            $order_by,
            parent::check_limit($limit)
        );
    }

    public static function get_categories(
        db $db,
        string $language,
        int $branch_id,
        int $id = null,
        string $custom_where = "",
        string $group_by = "",
        string $order_by = "",
        array $limit = [0, 0],
        bool $is_mobile = false,
        int $order_type = 0
    ) : results{

        if ($is_mobile){
            $active = match ($order_type) {
                order_types::TABLE, order_types::PERSONNEL, order_types::OTHER => $db->as_name(tbl3::ACTIVE,"active"),
                order_types::TAKEAWAY => $db->as_name(tbl3::ACTIVE_TAKE_AWAY, "active"),
                order_types::COME_TAKE => $db->as_name(tbl3::ACTIVE_COME_TAKE, "active"),
            };
            $columns = array(
                tbl3::ID,
                tbl3::BRANCH_ID,
                tbl3::MAIN_ID,
                $db->as_name(tbl3::NAME.$language, "name"),
                tbl3::RANK,
                tbl3::START_TIME,
                tbl3::END_TIME,
                $active,
                tbl3::PRODUCT_ID
            );
        }else {
            $columns = array(
                tbl3::ID,
                tbl3::BRANCH_ID,
                tbl3::MAIN_ID,
                $db->as_name(tbl3::NAME.$language, "name"),
                tbl3::RANK,
                tbl3::START_TIME,
                tbl3::END_TIME,
                tbl3::ACTIVE,
                tbl3::ACTIVE_SAFE,
                tbl3::ACTIVE_COME_TAKE,
                tbl3::ACTIVE_TAKE_AWAY,
                tbl3::PRODUCT_ID
            );
        }

        return $db->db_select(
            $columns,
            tbl3::TABLE_NAME,
            "",
            parent::check_where($db, array(
                tbl3::BRANCH_ID => $branch_id,
                tbl3::ID => $id,
                tbl3::IS_DELETE => 0
            )).parent::check_and($custom_where)." {$custom_where}",
            $group_by,
            $order_by,
            parent::check_limit($limit)
        );
    }


    public static function get_options(
        db $db,
        string $language,
        int $branch_id,
        string $custom_where = "",
        string $group_by = "",
        string $order_by = "",
        array $limit = [0, 0]
    ) : results{
        return $db->db_select(
            array(
                tbl4::ID,
                tbl4::BRANCH_ID,
                tbl4::SELECTION_LIMIT,
                tbl4::TYPE,
                tbl4::SEARCH_NAME,
                tbl4::DATE,
                tbl4::IS_DELETED,
                $db->as_name(tbl4::NAME.$language, "name")
            ),
            tbl4::TABLE_NAME,
            "",
            parent::check_where($db, array(
                tbl4::BRANCH_ID => $branch_id
            )).parent::check_and($custom_where)." {$custom_where}",
            $group_by,
            $order_by,
            parent::check_limit($limit)
        );
    }
    public static function get_options_items(
        db $db,
        string $language,
        int $branch_id,
        string $custom_where = "",
        string $group_by = "",
        string $order_by = "",
        array $limit = [0, 0]
    ) : results{
        return $db->db_select(
            array(
                tbl5::ID,
                tbl5::OPTION_ID,
                tbl5::BRANCH_ID,
                tbl5::QUANTITY,
                tbl5::PRICE,
                tbl5::IS_DEFAULT,
                tbl5::DATE,
                tbl5::IS_DELETED,
                $db->as_name(tbl5::NAME.$language, "name")
            ),
            tbl5::TABLE_NAME,
            "",
            parent::check_where($db, array(
                tbl5::BRANCH_ID => $branch_id
            )).parent::check_and($custom_where)." {$custom_where}",
            $group_by,
            $order_by,
            parent::check_limit($limit)
        );

    }

    public static function get_option_types(
        db $db,
        string $language,
        int $branch_id,
        string $custom_where = "",
        string $group_by = "",
        string $order_by = "",
        array $limit = [0, 0]
    ) : results{
        return $db->db_select(
            array(
                tbl6::ID,
                $db->as_name(tbl6::NAME.$language, "name")
            ),
            tbl6::TABLE_NAME,
            "",
            parent::check_where($db, array()).parent::check_and($custom_where)." {$custom_where}",
            $group_by,
            $order_by,
            parent::check_limit($limit)
        );
    }

    public static function get_linked_options(
        db $db,
        int $branch_id,
        string $custom_where = "",
        string $group_by = "",
        string $order_by = "",
        array $limit = [0, 0]
    ) : results{
        return $db->db_select(
            array(tbl7::ALL),
            tbl7::TABLE_NAME,
            "",
            parent::check_where($db, array(
                tbl7::BRANCH_ID => $branch_id
            )).parent::check_and($custom_where)." {$custom_where}",
            $group_by,
            $order_by,
            parent::check_limit($limit)
        );
    }

}