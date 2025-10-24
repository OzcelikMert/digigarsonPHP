<?php

namespace pos\functions\orders\set;

use config\db;
use config\sessions;
use config\table_helper\print_invoices as tbl;
use matrix_library\php\operations\user;
use sameparts\php\ajax\echo_values;
use config\type_tables_values\print_types;
use matrix_library\php\operations\array_list;

class post_keys_print_invoice
{
    const CATEGORIES = "categories";
}

class data_keys {}

class print_invoice
{
    public function __construct(db $db, sessions $sessions, echo_values &$echo)
    {
        $echo->rows = $this->get($db, $sessions);
    }

    private function get(db $db, sessions $sessions): array
    {
        $result = $db->db_select(
            [tbl::ID, tbl::DATA],
            tbl::TABLE_NAME,
            where: $db->where->equals(
                [
                    tbl::BRANCH_ID => $sessions->get->BRANCH_ID,
                    tbl::IS_PRINT => 0
                ]
            )
        )->rows;

        $categories = user::post(post_keys_print_invoice::CATEGORIES);
        foreach ($result as $row) {
            $data = json_decode($row["data"], true);
            $is_print_all = true;
            $has_printable_data = true;
            if ($data["type"] == print_types::KITCHEN) {
                $has_printable_data = false;
                $products = &$data["products"];
                foreach ($categories as $category) {
                    foreach ($products as &$product) {
                        if ($product["category_id"] == $category) {
                            if ((int)$product["is_print"] == 0) {
                                $product["is_print"] = 1;
                                $has_printable_data = true;
                            }
                        }
                    }
                }
                if (!array_list::array_all($products, function ($product) {
                    return (int)$product["is_print"] == 1;
                })) {
                    $is_print_all = false;
                }
            }
            if ($has_printable_data) {
                $db->db_update(
                    tbl::TABLE_NAME,
                    array(tbl::DATA => json_encode($data, JSON_UNESCAPED_UNICODE), tbl::IS_PRINT => $is_print_all ? 1 : 0),
                    where: $db->where->equals([tbl::BRANCH_ID => $sessions->get->BRANCH_ID, tbl::ID => $row["id"]])
                );
            }
        }

        return $result;
    }
}
