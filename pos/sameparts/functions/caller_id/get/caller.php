<?php
namespace pos\sameparts\functions\caller_id\get;

use config\db;
use config\sessions;
use config\type_tables_values\branch_caller_status_types;
use matrix_library\php\operations\user;
use sameparts\php\ajax\echo_values;
use config\table_helper\branch_callers as tbl;
use config\table_helper\customer_users as tbl2;
use config\table_helper\customer_address as tbl3;
use sameparts\php\helper\variable_filters;

class post_keys {
    const CALLER_ID = "caller_id",
        PHONE = "phone";
}

class caller {
    public function __construct(db $db, sessions $sessions, echo_values &$echo) {
        $phone = variable_filters::phone(user::post(post_keys::PHONE));
        user::post(post_keys::PHONE, (count($phone) > 0) ? $phone[0] : "");

        $echo->custom_data["POST"] = $_POST;
        $echo->rows["user"] = (empty(user::post(post_keys::PHONE)))
            ? $this->get($db, $sessions)
            : $this->get_with_phone($db, $sessions);
        if(user::post(post_keys::CALLER_ID) == 0 && count($echo->rows["user"]) > 0 && $echo->rows["user"][0]["customer_id"] != null)
            $echo->rows["address"] = $this->get_address($db, $sessions, (int)$echo->rows["user"][0]["customer_id"]);
    }

    private function get(db $db, sessions $sessions) : array{
        $where = (user::post(post_keys::CALLER_ID) != 0) ? [tbl::ID => user::post(post_keys::CALLER_ID)] : [];
        return $db->db_select(
            array(
                tbl::ID,
                $db->as_name(tbl2::ID, "customer_id"),
                tbl2::NAME,
                tbl::PHONE
            ),
            tbl::TABLE_NAME,
            $db->join->left([
                tbl2::TABLE_NAME => [tbl2::PHONE => tbl::PHONE]
            ]),
            $db->where->equals(array_merge([
                tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                tbl::STATUS    => branch_caller_status_types::WAITING
            ], $where)),
            limit: $db->limit([0, 1])
        )->rows;
    }

    private function get_with_phone(db $db, sessions $sessions) : array{
        return $db->db_select(
            array(
                $db->as_name("-1", "id"),
                $db->as_name(tbl2::ID, "customer_id"),
                tbl2::NAME,
                tbl2::PHONE
            ),
            tbl2::TABLE_NAME,
            where: $db->where->equals([
                tbl2::PHONE => user::post(post_keys::PHONE)
            ]),
            limit: $db->limit([0, 1])
        )->rows;
    }

    private function get_address(db $db, sessions $sessions, int $user_id) : array{
        return $db->db_select(
            array(
                tbl3::ID,
                tbl3::TITLE,
                tbl3::PHONE
            ),
            tbl3::TABLE_NAME,
            where: $db->where->equals([
                tbl3::USER_ID   => $user_id
            ])
        )->rows;
    }
}