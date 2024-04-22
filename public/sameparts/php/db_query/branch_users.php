<?php
namespace sameparts\php\db_query;

use config\db;
use config\table_helper\branch_users as tbl;
use config\table_helper\branch_devices as tbl2;
use config\table_helper\branch_info as tbl3;
use config\table_helper\currency_types as tbl4;
use config\table_helper\language_types as tbl5;
use config\table_helper\branch_manage_users as tbl6;
use config\table_helper\branch_user_permission_types as tbl7;
use matrix_library\php\db_helpers\results;

class branch_users extends helper {
    //get user
    public static function get(db $db, int $branch_id, mixed $password, string $custom_where = "", string $order_by = "", array $limit = [0, 0]) : results{
        return $db->db_select(
            array(
                tbl::ID,
                tbl::NAME,
                tbl::ACTIVE,
                tbl::PASSWORD,
                tbl::BRANCH_ID,
                tbl::PERMISSIONS
            ),
           tbl::TABLE_NAME,
           "",
           parent::check_where($db, array(
                tbl::BRANCH_ID => $branch_id,
                tbl::PASSWORD => $password,
                tbl::IS_DELETE => 0,
           )).parent::check_and($custom_where)." {$custom_where}",
            "",
            $order_by,
            parent::check_limit($limit)
        );
    }


    public static function get_branch_info(db $db, int $branch_id, string $custom_where = "", string $order_by = "", array $limit = [0, 0],$mobile = false) : results{
        $columns = array();
        if ($mobile) {
            $columns =  array(
                tbl3::ID,
                tbl3::NAME,
                tbl3::TYPE_ID,
                tbl3::CURRENCY_ID,
                tbl3::OPEN_TIME,
                tbl3::CLOSE_TIME,
                tbl3::ACTIVE,
                tbl3::ONLINE_PAYMENT,
                tbl3::TAKE_AWAY_TIME,
                tbl3::TAKE_AWAY_AMOUNT,
                tbl3::TAKE_AWAY_ACTIVE,
                tbl3::QR_DISCOUNT ,
                tbl3::IP,
                tbl3::IP_BLOCK,
                tbl3::ADDRESS,
                tbl3::LOGO,
                tbl3::LANGUAGE_ID,
            );
        }else {
            $columns = array(
                tbl3::ID,
                tbl3::NAME,
                tbl3::TYPE_ID,
                tbl3::CURRENCY_ID,
                tbl3::LICENSE_TIME_ID,
                tbl3::LICENSE_TYPE_ID,
                tbl3::LICENSE_DATE_END,
                tbl3::CREATE_DATE ,
                tbl3::OPEN_TIME,
                tbl3::CLOSE_TIME,
                tbl3::ACTIVE,
                tbl3::LOGIN_MESSAGE,
                tbl3::IP,
                tbl3::IP_BLOCK,
                tbl3::ONLINE_PAYMENT,
                tbl3::TAKE_AWAY_TIME,
                tbl3::TAKE_AWAY_AMOUNT,
                tbl3::TAKE_AWAY_ACTIVE,
                tbl3::QR_DISCOUNT ,
                tbl3::QR_ACTIVE   ,
                tbl3::WAITER_APP_LIMIT,
                tbl3::POS_APP_LIMIT,
                tbl3::ADDRESS,
                tbl3::LOGO,
                tbl3::LANGUAGE_ID,
                tbl4::TYPE,
                tbl5::SEO_URL,
                tbl3::MAIN_ID,
                tbl3::IS_MAIN
            );
        }

        return $db->db_select(
            $columns,
            tbl3::TABLE_NAME,
            $db->join->inner( array(
                tbl4::TABLE_NAME => [ tbl3::CURRENCY_ID => tbl4::ID ],
                tbl5::TABLE_NAME => [ tbl3::LANGUAGE_ID => tbl5::ID ],
            )),
            parent::check_where($db, array(
                tbl3::ID => $branch_id,
                tbl3::ACTIVE => 1
            ))."{$custom_where}",
            "",
            $order_by,
            parent::check_limit($limit)
        );
    }

    public static function get_branch_devices(db $db, int $type = null, string $custom_where = "", string $order_by = "", array $limit = [0, 1]) : results{
        return $db->db_select(
            array(
                tbl2::ID,
                tbl2::NAME,
                tbl2::TOKEN,
                tbl2::IS_PRINT,
                tbl2::BRANCH_ID,
                tbl2::SECURITY_CODE,
                tbl2::TYPE,
                tbl2::CALLER_ID_ACTIVE
            ),
            tbl2::TABLE_NAME,
            "",
            parent::check_where($db, array(
                tbl2::TYPE => $type
            )).parent::check_and($custom_where)." {$custom_where}",
            "",
            $order_by,
            parent::check_limit($limit)
        );
    }

    public static function get_manage_users(
        db $db,
        string $email_or_phone = null,
        string $password = null,
        int $branch_id = null,
        int $id = null,
        string $join = "",
        string $custom_where = "",
        string $group_by = "",
        string $order_by = "",
        array $limit = [0, 0]
    ) : results{
        return $db->db_select(
            array(
                tbl6::ID,
                tbl6::NAME,
                tbl6::PHONE,
                tbl6::EMAIL,
                tbl6::PASSWORD,
                tbl6::BRANCH_ID,
                tbl6::PERMISSIONS,
                tbl6::ACTIVE,
                tbl6::LANGUAGE_ID,
                tbl5::SEO_URL
            ),
            tbl6::TABLE_NAME,
            $db->join->inner( array(
                tbl5::TABLE_NAME => [ tbl6::LANGUAGE_ID => tbl5::ID ],
            )).$join,
            parent::check_where($db, array(
                [tbl6::EMAIL => $email_or_phone, tbl6::PHONE => $email_or_phone],
                tbl6::BRANCH_ID => $branch_id,
                tbl6::PASSWORD  => $password,
                tbl6::ID        => $id
            )).parent::check_and($custom_where)." {$custom_where}",
            $group_by,
            $order_by,
            parent::check_limit($limit)
        );
    }

    public static function get_permissions(db $db, string $language, string $custom_join = "", string $custom_where = "", string $custom_group_by = "", string $order_by = "", array $limit = [0, 0]) : results{
        return $db->db_select(
            array(
                tbl7::ID,
                $db->as_name(tbl7::NAME.$language, "name")
            ),
            tbl7::TABLE_NAME,
            $custom_join,
            "{$custom_where}",
            $custom_group_by,
            $order_by,
            parent::check_limit($limit)
        );
    }
}