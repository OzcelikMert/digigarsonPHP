<?php

namespace sameparts\php\printer;

use config\db;
use config\sessions;
use config\type_tables_values\print_types;
use matrix_library\php\db_helpers\results;
use order_app\sameparts\functions\sessions\get as mobile_get;
use config\table_helper\print_invoices as tbl;


class printer_values{
    public array $products = array();
    private mixed $table_id = "";
    private mixed $order_no = "";
    private int $order_id = 0;
    private int $branch_id = 0;
    private string $user_name = "";
    private db $db;
    private int $print_type;


    public function __construct(
        db $db,
        sessions $sessions = null,
        mobile_get $mobile_sessions = null,
        $table_id,
        $order_id,
        $order_no,
        $print_type = print_types::KITCHEN
    ){
        $this->db = $db;
        $this->table_id = $table_id;
        $this->order_id = $order_id;
        $this->order_no = $order_no;
        $this->print_type = $print_type;
        $this->branch_id = (int)($sessions != null) ? $sessions->get->BRANCH_ID : $mobile_sessions->SELECT_BRANCH_ID;
        $this->user_name = (int)($sessions != null) ? "(yetkili) ".$sessions->get->USER_NAME : "(müşteri) ".$mobile_sessions->NAME;

    }

    public function create(): results{
        $invoice = array(
            "order_id" => $this->order_id,
            "type" => $this->print_type,
            "products" => $this->products,
            "orders" => [["table_id" => $this->table_id, "no" => $this->order_no]],
            "user_name" => $this->user_name
        );

       return $this->db->db_insert(
            tbl::TABLE_NAME,array(
                tbl::BRANCH_ID => $this->branch_id,
                tbl::DATA => json_encode($invoice,JSON_UNESCAPED_UNICODE)
            )
        );
    }

}
