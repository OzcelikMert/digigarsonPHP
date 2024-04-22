<?php
namespace pos\functions\products\get;

use config\db;
use config\sessions;
use matrix_library\php\db_helpers\results;
use config\table_helper\products as tbl;
use sameparts\php\ajax\echo_values;
use sameparts\php\helper\language_codes;

class translate_product{
    function __construct(db $db, sessions $sessions, echo_values &$echo){
        $echo->rows = (array)$this->get($db, $sessions)->rows;
    }

    function get(db $db, sessions $sessions) : results{
        return $db->db_select(
            array(
                tbl::ID,

                tbl::NAME.language_codes::TURKISH,
                tbl::NAME.language_codes::ARABIC,
                tbl::NAME.language_codes::CHINESE,
                tbl::NAME.language_codes::DUTCH,
                tbl::NAME.language_codes::ENGLISH,
                tbl::NAME.language_codes::FRENCH,
                tbl::NAME.language_codes::SPANISH,
                tbl::NAME.language_codes::RUSSIAN,
                tbl::NAME.language_codes::ROMANIAN,
                tbl::NAME.language_codes::PORTUGUESE,
                tbl::NAME.language_codes::ITALIAN,
                tbl::NAME.language_codes::GERMAN,

                tbl::COMMENT.language_codes::TURKISH,
                tbl::COMMENT.language_codes::ARABIC,
                tbl::COMMENT.language_codes::CHINESE,
                tbl::COMMENT.language_codes::DUTCH,
                tbl::COMMENT.language_codes::ENGLISH,
                tbl::COMMENT.language_codes::FRENCH,
                tbl::COMMENT.language_codes::SPANISH,
                tbl::COMMENT.language_codes::RUSSIAN,
                tbl::COMMENT.language_codes::ROMANIAN,
                tbl::COMMENT.language_codes::PORTUGUESE,
                tbl::COMMENT.language_codes::ITALIAN,
                tbl::COMMENT.language_codes::GERMAN
            ),
            tbl::TABLE_NAME,
            where: $db->where->equals([
                tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl::DELETE => 0
            ]),
            order_by: $db->order_by(tbl::NAME.$sessions->get->LANGUAGE_TAG, db::ASC)
        );
    }

}