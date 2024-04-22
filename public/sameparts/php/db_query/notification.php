<?php
namespace sameparts\php\db_query;

use config\db;
use config\table_helper\notifications as tbl;
use config\table_helper\send_notification as tbl2;
use matrix_library\php\db_helpers\results;

class notification extends helper {

    public static function get(db $db, int $branch_id, int $is_delete = 0) : results{
        return $db->db_select(
            array(
                tbl::ID,
                tbl::NAME,
                tbl::COMMENT,
                tbl::ACTIVE,
            ),
            tbl::TABLE_NAME,
            where: $db->where->equals([
                tbl::BRANCH_ID => $branch_id,
                tbl::IS_DELETE => $is_delete,
            ]));
    }
    public static function get_send(db $db, int $branch_id, int $is_delete = 0) : results{
        return $db->db_select(
            array(
                tbl2::ID,
                tbl2::NOTIFICATION_ID,
                tbl2::TABLE_ID,
            ),
            tbl2::TABLE_NAME,
            where: $db->where->equals([
                tbl2::BRANCH_ID => $branch_id,
                tbl2::IS_READ => $is_delete,
            ]));
    }


}

