<?php
namespace _superadmin\functions\login\set;

use config\db;
use config\table_helper\super_admin_user as tbl;
use _superadmin\sameparts\functions\sessions\get;
use matrix_library\php\operations\user;
use sameparts\php\ajax\echo_values;

class post_keys{const NAME = "name",PASSWORD = "password";}

class login_check
{
    public function __construct(db $db, get $sessions, echo_values &$echo) {
        $echo->custom_data["POST"] = $_POST;
        if (user::check_sent_data([post_keys::NAME, post_keys::PASSWORD])) {
            $query = $db->db_select(
                tbl::ID,
                tbl::TABLE_NAME,
                where: $db->where->equals([
                    tbl::NAME => user::post(post_keys::NAME),
                    tbl::PASSWORD => user::post(post_keys::PASSWORD)
                ]),
            );

            $echo->rows = $query->rows;

            if (count($query->rows) > 0) {
                $sessions = new get();
                $sessions->USER_ID = $query->rows[0]["id"];
                $sessions->create();
            }
        }
    }
}