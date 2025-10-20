<?php

namespace waiter_terminal\functions\dashboard\set;

use config\db;
use config\settings;
use config\table_helper\print_invoices as tbl;
use config\table_helper\orders as tbl2;
use config\type_tables_values\device_types;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use sameparts\php\ajax\echo_values;
use waiter_terminal\sameparts\functions\sessions\get;

class post_keys
{
    const TABLE_ID = "table_id",
        ORDER_ID = "order_id";
}

class print_invoice
{
    public function __construct(db $db, get $sessions, echo_values &$echo)
    {
        $this->check_values($db, $sessions, $echo);
        if ($echo->status) {
            $echo->rows = (array)$this->set($db, $sessions);
        }
    }

    function set(db $db, get $sessions): results
    {
        $printInvoiceData = $db->db_insert(
            tbl::TABLE_NAME,
            array(
                tbl::DATA => json_encode(array(
                    "type" => 1,
                    "table_id" => user::post(post_keys::TABLE_ID),
                    "order_id" => user::post(post_keys::ORDER_ID)
                ), JSON_UNESCAPED_UNICODE),
                tbl::BRANCH_ID => $sessions->BRANCH_ID
            )
        );

        if ($printInvoiceData->status) {
            $db->db_update(
                tbl2::TABLE_NAME,
                array(tbl2::IS_PRINT => 1),
                where: $db->where->equals([tbl2::BRANCH_ID => $sessions->BRANCH_ID, tbl2::IS_PRINT => 0])
            );
        }

        return $printInvoiceData;
    }

    private function check_values(db $db, get $sessions, echo_values &$echo)
    {
        if (variable::is_empty(
            user::post(post_keys::TABLE_ID)
        )) {
            $echo->error_code = settings::error_codes()::EMPTY_VALUE;
        }

        if ($echo->error_code != settings::error_codes()::SUCCESS) $echo->status = false;
    }
}
