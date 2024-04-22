<?php
namespace manage\functions\branch_settings\set;

use config\db;
use config\sessions;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\user;
use sameparts\php\ajax\echo_values;
use config\table_helper\branch_surveys as tbl;
use sameparts\php\helper\date;

class post_keys{
    CONST
        ID = "id",
        TYPE = "type",
        SURVEY_TYPE = "survey_type",
        NAME = "name",
        IS_DELETE = "is_delete";
}
class types{
    const EDIT = 2,  DEL = 3;
}

class surveys{
    function __construct(db $db, sessions $sessions, echo_values &$echo){
        $echo->custom_data["post"] = $_POST;
        if ($sessions->get->BRANCH_ID > 0) {
            $this->check($db,$sessions,$echo);
        }
    }

    private function check(db $db, sessions $sessions, echo_values &$echo): void{
        switch (user::post(post_keys::TYPE)){
            case types::DEL:
                $echo->rows = (array) $this->delete($db,$sessions,$echo);
                break;
            case types::EDIT:
                $echo->rows = (user::post(post_keys::ID) > 0)
                    ? (array)  $this->update($db,$sessions,$echo)
                    : (array)  $this->insert($db,$sessions,$echo);
                break;
        }
    }

    private function insert(db $db, sessions $sessions, echo_values $echo) : results{
        return $db->db_insert(
            tbl::TABLE_NAME,
            array(
                tbl::NAME."".$sessions->get->LANGUAGE_TAG => user::post(post_keys::NAME),
                tbl::TYPE => user::post(post_keys::SURVEY_TYPE),
                tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl::DATE_TIME => date(date::date_type_simples()::HYPHEN_DATE_TIME),
                tbl::IP => user::get_ip_address(),
            )
        );
    }

    private function update(db $db, sessions $sessions, echo_values $echo) : results{
        return  $db->db_update(
            tbl::TABLE_NAME,
            [
                tbl::NAME."".$sessions->get->LANGUAGE_TAG => user::post(post_keys::NAME),
                tbl::TYPE => user::post(post_keys::SURVEY_TYPE),
                tbl::IP => user::get_ip_address()
            ],
            where: $db->where->equals([ tbl::ID => user::post(post_keys::ID), tbl::BRANCH_ID => $sessions->get->BRANCH_ID ])
        );
    }

    private function delete(db $db, sessions $sessions, echo_values $echo) : results{
        return $db->db_update(tbl::TABLE_NAME, [tbl::IS_DELETE => 1],
            where: $db->where->equals([
                    tbl::ID => user::post(post_keys::ID),
                    tbl::BRANCH_ID => $sessions->get->BRANCH_ID
                ]
            )
        );
    }

}
