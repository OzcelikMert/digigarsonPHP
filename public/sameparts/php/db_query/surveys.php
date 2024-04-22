<?php
namespace sameparts\php\db_query;

use config\db;
use config\table_helper\customer_surveys as tbl;
use config\table_helper\branch_surveys as tbl2;
use config\table_helper\survey_types as tbl3;
use matrix_library\php\db_helpers\results;

class surveys extends helper {

    //GET CUSTOMER SURVEYS
    public static function get(db $db, int $branch_id) : results{
        return $db->db_select(
            array(
                tbl::ID,
                tbl::USER_ID,
                tbl::BRANCH_ID,
                tbl::DATE_TIME,
                tbl::IP,
                tbl::TYPE,
                tbl::VALUE,
            ),
            tbl::TABLE_NAME,
            where: $db->where->equals([
                tbl::BRANCH_ID => $branch_id,
            ]));
    }

    //GET BRANCH SURVEY QUESTIONS
    public static function get_questions(db $db, int $branch_id, string $language, int $is_delete = 0) : results{
        return $db->db_select(
            array(
                tbl2::ID,
                tbl2::BRANCH_ID,
                $db->as_name(tbl2::NAME."".$language,"name"),
                tbl2::IP,
                tbl2::DATE_TIME,
                tbl2::IS_DELETE,
                tbl2::TYPE,
            ),
            tbl2::TABLE_NAME,
            where: $db->where->equals([
                tbl2::BRANCH_ID => $branch_id,
                tbl2::IS_DELETE => $is_delete,
            ]));
    }

    //GET BRANCH SURVEY TYPES
    public static function get_types(db $db,$language_tag) : results{
        return $db->db_select(
            [
                tbl3::ID,
                $db->as_name(tbl3::NAME."".$language_tag, "name")
            ],
            tbl3::TABLE_NAME);
    }

}

