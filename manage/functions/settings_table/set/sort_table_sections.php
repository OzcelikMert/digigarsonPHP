<?php
namespace manage\functions\settings_table\set;

use config\db;
use config\settings;
use config\table_helper\branch_sections as tbl;
use config\sessions;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use sameparts\php\ajax\echo_values;

class post_keys{
    const TABLE_SECTIONS = "table_sections";
}

class sort_table_sections{
    function __construct(db $db,sessions $sessions,echo_values &$echo){
        $this->check_values($db, $sessions, $echo);
        if($echo->status){
            $this->set($db, $sessions, $echo);
        }
    }

    private function set(db $db, sessions $sessions, echo_values &$echo): void{
        $index = 1;
        foreach (user::post(post_keys::TABLE_SECTIONS) as $table_section){
            $db->db_update(
                tbl::TABLE_NAME,
                array(
                    tbl::RANK => $index
                ),
                where: $db->where->equals([
                    tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                    tbl::ID        => $table_section
                ])
            );
            $index++;
        }
    }

    private function check_values(db $db, sessions $sessions, echo_values &$echo){
        if(variable::is_empty(
            user::post(post_keys::TABLE_SECTIONS)
        )){
            $echo->error_code = settings::error_codes()::EMPTY_VALUE;
        }

        if($echo->error_code != settings::error_codes()::SUCCESS) $echo->status = false;
    }
}
