<?php
namespace _superadmin\functions\table_edit\set;


use config\table_helper\branch_tables as tbl;
use config\table_helper\branch_sections as tbl2;
use matrix_library\php\operations\user;
use _superadmin\sameparts\functions\sessions\get;
use config\db;
use sameparts\php\helper\date;
use sameparts\php\ajax\echo_values;

class post_keys{
    const
    BRANCH_NO = "branch_no",
    TABLE_SECTION = "table_section",
    TABLE_START = "table_start",
    TABLE_END = "table_end";
}
class add_branch_table
{

        public  $data = [];
        public function __construct(db $db, get $sessions, echo_values &$echo){
            $limit = 1;
            $section = $db->db_select(tbl2::ID, tbl2::TABLE_NAME, where: $db->where->equals([tbl2::BRANCH_ID => user::post(post_keys::BRANCH_NO), tbl2::SECTION_ID => user::post(post_keys::TABLE_SECTION)]), limit: $db->limit([0, 1]));
            if(count($section->rows) < 1){
                $section_name = $db->db_insert(
                    tbl2::TABLE_NAME,
                    array(
                        tbl2::SECTION_ID => user::post(post_keys::TABLE_SECTION),
                        tbl2::BRANCH_ID  => user::post(post_keys::BRANCH_NO)
                    )
                )->insert_id;
            }else{
                $section_name = $section->rows[0]["id"];
            }
            for ($i=user::post(post_keys::TABLE_START); $i <= user::post(post_keys::TABLE_END) ; $i++) {
                $table_url = $this->dechex();
                array_push($this->data, array(
                    tbl::BRANCH_ID => user::post(post_keys::BRANCH_NO),
                    tbl::SECTION_ID => $section_name,
                    tbl::TABLE_NO => $i,
                    tbl::URL => $table_url,
                    tbl::TYPE => 1,
                    tbl::TABLE_SHAPE_TYPE => 1,
                    tbl::CREATE_DATE => date::get()
                ));
                $limit++;
            }
            if ($limit <= 50){
                $db->db_insert(tbl::TABLE_NAME, $this->data);
            }
            else {
                $echo->error_code = "1";
            }
        }
        private function dechex(){
            return dechex(rand(1,99999999)+time());
        }
}