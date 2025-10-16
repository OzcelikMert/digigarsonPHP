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
    private bool $is_qr_order = false;


    public function __construct(
        db $db,
        $table_id,
        $order_id,
        $order_no,
        $print_type = print_types::KITCHEN,
        ?sessions $sessions = null,
        ?mobile_get $mobile_sessions = null,
    ){
        $this->db = $db;
        $this->table_id = $table_id;
        $this->order_id = $order_id;
        $this->order_no = $order_no;
        $this->print_type = $print_type;
        if($sessions){
            $this->branch_id = $sessions->get->BRANCH_ID;
            $this->user_name = $sessions->get->USER_NAME;
        }
        if($mobile_sessions){
            $this->branch_id = $mobile_sessions->SELECT_BRANCH_ID;
            $this->user_name = $mobile_sessions->NAME;
            $this->is_qr_order = true;
        }
    }

    public function create(): results{
        $invoice = array(
            "order_id" => $this->order_id,
            "type" => $this->print_type,
            "products" => $this->products,
            "orders" => [["table_id" => $this->table_id, "no" => $this->order_no]],
            "user_name" => $this->user_name,
            "is_qr_order" => $this->is_qr_order
        );

       return $this->db->db_insert(
            tbl::TABLE_NAME,array(
                tbl::BRANCH_ID => $this->branch_id,
                tbl::DATA => json_encode($invoice,JSON_UNESCAPED_UNICODE)
            )
        );
    }

}
