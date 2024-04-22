<?php
namespace manage\functions\report_product\get;

use config\db;
use config\type_tables_values\account_types;
use config\type_tables_values\order_payment_status_types;
use config\type_tables_values\order_product_types;
use config\type_tables_values\order_products_status_types;
use config\sessions;
use matrix_library\php\operations\array_list;
use matrix_library\php\operations\user;
use sameparts\php\ajax\echo_values;
use config\table_helper\order_payments as tbl;
use config\table_helper\payment_types as tbl2;
use config\table_helper\order_products as tbl3;
use config\table_helper\products as tbl4;
use config\table_helper\customer_users as tbl5;
use config\table_helper\orders as tbl6;
use config\table_helper\branch_users as tbl7;
use config\table_helper\branch_tables as tbl8;
use config\table_helper\branch_sections as tbl9;
use config\table_helper\table_section_types as tbl10;
use config\table_helper\product_categories as tbl11;
use config\table_helper\branch_safe as tbl12;
use config\table_helper\account_types as tbl13;
use config\table_helper\integrate_customers as tbl14;
use sameparts\php\helper\date;

class post_keys {
    const REPORT_TYPE = "type",
        DATE_START = "date_start",
        DATE_END = "date_end",
        BRANCH_ID = "branch_id",
        SAFE_ID = "safe_id";
}

class report_types {
    const TOTAL_PRICE = 1,
        PAYMENT_TYPES = 2,
        SALES_PRODUCT = 3,
        SALES_CUSTOMER = 4,
        SALES_WAITER = 5,
        SALES_TABLE = 6,
        SALES_PRODUCT_BY_TABLE = 7,
        RUSH_HOURS = 8,
        QUESTIONS_POINT = 9,
        QUESTIONS_TEXT = 10,
        ORDERS_CANCEL = 11,
        ORDERS_CATERING = 12,
        ORDERS_TAKE_AWAY = 13,
        SALES_PRODUCT_CATEGORY = 14,
        SALES_PRODUCT_PRODUCT = 15;
}

class report {
    public function __construct(db $db, db $db_backup, sessions $sessions, echo_values &$echo) {
        $safe_id = array();
        if(user::post(post_keys::DATE_END) == date::get(date::date_type_simples()::HYPHEN_DATE)) array_push($safe_id, 0);
        user::post("branch_id",($sessions->get->IS_MAIN > 0 && $sessions->get->BRANCH_ID == $sessions->get->BRANCH_ID_MAIN) ? $sessions->get->BRANCHES : $sessions->get->BRANCH_ID);
        user::post(post_keys::DATE_START,user::post(post_keys::DATE_START)." 00:00:00");
        user::post(post_keys::DATE_END,user::post(post_keys::DATE_END)." 23:59:59");

        foreach ($this->get_safe_id($db) as $value){
            array_push($safe_id, $value["id"]);
        }
        user::post(post_keys::SAFE_ID, $safe_id);

        $echo->rows = match ((int)user::post(post_keys::REPORT_TYPE)) {
            report_types::TOTAL_PRICE => $this->total_price($db, $db_backup, $sessions),
            report_types::PAYMENT_TYPES => $this->payment_types($db, $db_backup, $sessions),
            report_types::SALES_PRODUCT => $this->sales_product($db, $db_backup, $sessions),
            report_types::SALES_CUSTOMER => $this->sales_customer($db, $db_backup, $sessions),
            report_types::SALES_WAITER => $this->sales_waiter($db, $db_backup, $sessions),
            report_types::SALES_TABLE => $this->sales_table($db, $db_backup, $sessions),
            report_types::RUSH_HOURS => $this->rush_hours($db, $db_backup, $sessions),
            report_types::ORDERS_CANCEL => $this->orders_cancel($db, $db_backup, $sessions),
            report_types::ORDERS_CATERING => $this->orders_catering($db, $db_backup, $sessions),
            report_types::SALES_PRODUCT_CATEGORY => $this->sales_product_category($db, $db_backup, $sessions),
            report_types::SALES_PRODUCT_PRODUCT => $this->sales_product_waiter($db, $db_backup, $sessions),
        };
    }

    private function get_safe_id(db $db) : array{
        return $db->db_select(
            tbl12::ID,
            tbl12::TABLE_NAME,
            where: $db->where->equals([
                tbl12::BRANCH_ID => user::post(post_keys::BRANCH_ID)
            ])." ".db::AND." ".$db->where->between([tbl12::DATE_START => [user::post(post_keys::DATE_START), user::post(post_keys::DATE_END)]])
        )->rows;
    }

    private function total_price(db $db, db $db_backup, sessions $sessions) : array{
        $where = $db->where->equals([
                tbl::BRANCH_ID => user::post(post_keys::BRANCH_ID),
                tbl::SAFE_ID => user::post(post_keys::SAFE_ID),
                tbl::STATUS => [
                    order_payment_status_types::PAID,
                ],
                tbl::IS_DELETE => 0
            ]);

        return array(
            "new" => $db->db_select(
                array(
                    tbl::BRANCH_ID,
                    $db->as_name($db->if_null($db->sum(tbl::PRICE), 0), "total")
                ),
                tbl::TABLE_NAME,
                where: $where,
                group_by: tbl::BRANCH_ID,
                order_by: tbl::BRANCH_ID
            )->rows,

            "old" => $db_backup->db_select(
                array(
                    tbl::BRANCH_ID,
                    $db->as_name($db->if_null($db->sum(tbl::PRICE), 0), "total")
                ),
                tbl::TABLE_NAME,
                where: $where,
                group_by: tbl::BRANCH_ID,
                order_by: tbl::BRANCH_ID
            )->rows
        );
    }

    private function payment_types(db $db, db $db_backup, sessions $sessions) : array{
        $where = $db->where->equals([
                tbl::BRANCH_ID => user::post(post_keys::BRANCH_ID),
                tbl::SAFE_ID => user::post(post_keys::SAFE_ID),
                tbl::STATUS => [
                    order_payment_status_types::PAID,
                    order_payment_status_types::COST
                ],
                tbl::IS_DELETE => 0
            ]);
        return array(
            "new" => $db->db_select(
                array(
                    tbl::BRANCH_ID,
                    tbl::TYPE,
                    $db->as_name($db->if_null($db->sum(tbl::PRICE), 0), "total")
                ),
                tbl::TABLE_NAME,
                where: $where,
                group_by: tbl::BRANCH_ID.",".tbl::TYPE,
                order_by: $db->order_by("total", db::DESC)
            )->rows,

            "old" => $db_backup->db_select(
                array(
                    tbl::BRANCH_ID,
                    tbl::TYPE,
                    $db->as_name($db->if_null($db->sum(tbl::PRICE),0), "total")
                ),
                tbl::TABLE_NAME,
                where: $where,
                group_by: $db->group_by([tbl::BRANCH_ID,tbl::TYPE]),
                order_by: $db->order_by("total", db::DESC)
            )->rows,

            "payment_types" => $db->db_select(
                array(
                    tbl2::ID,
                    $db->as_name(tbl2::NAME.$sessions->get->LANGUAGE_TAG, "name")
                ),
                tbl2::TABLE_NAME
            )->rows
        );
    }

    private function sales_product(db $db, db $db_backup, sessions $sessions) : array{
        $id = array();

        $where = $db->where->equals([
                tbl3::BRANCH_ID => user::post(post_keys::BRANCH_ID),
                tbl6::SAFE_ID => user::post(post_keys::SAFE_ID),
                tbl3::STATUS => [
                    order_products_status_types::ACTIVE
                ]
            ]);
        $join = $db->join->inner([tbl6::TABLE_NAME => [tbl6::ID => tbl3::ORDER_ID]]);


        $new = $db->db_select(
            array(
                tbl3::BRANCH_ID,
                tbl3::PRODUCT_ID,
                $db->as_name($db->if_null($db->sum(tbl3::QTY), 0), "total_qty"),
                $db->as_name($db->if_null($db->sum(tbl3::QUANTITY), 0), "total_quantity"),
                $db->as_name($db->if_null($db->sum(tbl3::PRICE), 0), "total")
            ),
            tbl3::TABLE_NAME,
            joins: $join,
            where: $where,
            group_by: $db->group_by([tbl3::BRANCH_ID,tbl3::PRODUCT_ID]),
            order_by: $db->order_by("total", db::DESC)
        )->rows;

        $old = $db_backup->db_select(
            array(
                tbl3::BRANCH_ID,
                tbl3::PRODUCT_ID,
                $db->as_name($db->if_null($db->sum(tbl3::QTY), 0), "total_qty"),
                $db->as_name($db->if_null($db->sum(tbl3::QUANTITY), 0), "total_quantity"),
                $db->as_name($db->if_null($db->sum(tbl3::PRICE), 0), "total")
            ),
            tbl3::TABLE_NAME,
            joins: $join,
            where: $where,
            group_by: $db->group_by([tbl3::BRANCH_ID,tbl3::PRODUCT_ID]),
            order_by: $db->order_by([tbl3::BRANCH_ID,"total"], db::DESC)
        )->rows;

        foreach ($new as $data){
            if(array_list::index_of($id, $data["product_id"]) < 0) array_push($id, $data["product_id"]);
        }

        foreach ($old as $data){
            if(array_list::index_of($id, $data["product_id"]) < 0) array_push($id, $data["product_id"]);
        }

        return array(
            "new" => $new,

            "old" => $old,

            "products" => $db->db_select(
                array(
                    tbl4::BRANCH_ID,
                    tbl4::ID,
                    tbl4::QUANTITY_ID,
                    tbl4::CODE,
                    $db->as_name(tbl4::NAME.$sessions->get->LANGUAGE_TAG, "name")
                ),
                tbl4::TABLE_NAME,
                where: $db->where->equals([
                    tbl4::ID => $id
                ])
            )->rows,
        );
    }

    private function sales_product_category(db $db, db $db_backup, sessions $sessions) : array{
        $id = array();

        $where = $db->where->equals([
                tbl3::BRANCH_ID => user::post(post_keys::BRANCH_ID),
                tbl6::SAFE_ID => user::post(post_keys::SAFE_ID),
                tbl3::STATUS => [
                    order_products_status_types::ACTIVE
                ]
            ]);
        $join = $db->join->inner([
            tbl6::TABLE_NAME => [tbl6::ID => tbl3::ORDER_ID]
        ]);


        $new = $db->db_select(
            array(
                tbl3::BRANCH_ID,
                tbl3::PRODUCT_ID,
                $db->as_name($db->if_null($db->sum(tbl3::PRICE), 0), "total")
            ),
            tbl3::TABLE_NAME,
            joins: $join,
            where: $where,
            group_by: $db->group_by([tbl3::BRANCH_ID, tbl3::PRODUCT_ID]),
            order_by: $db->order_by("total", db::DESC)
        )->rows;

        $old = $db_backup->db_select(
            array(
                tbl3::BRANCH_ID,
                tbl3::PRODUCT_ID,
                $db->as_name($db->if_null($db->sum(tbl3::PRICE), 0), "total")
            ),
            tbl3::TABLE_NAME,
            joins: $join,
            where: $where,
            group_by: $db->group_by([tbl3::BRANCH_ID, tbl3::PRODUCT_ID]),
            order_by: $db->order_by([tbl3::BRANCH_ID,"total"], db::DESC)
        )->rows;

        foreach ($new as $data){
            if(array_list::index_of($id, $data["product_id"]) < 0) array_push($id, $data["product_id"]);
        }

        foreach ($old as $data){
            if(array_list::index_of($id, $data["product_id"]) < 0) array_push($id, $data["product_id"]);
        }

        return array(
            "new" => $new,

            "old" => $old,

            "products" => $db->db_select(
                array(
                    tbl4::BRANCH_ID,
                    tbl4::ID,
                    tbl4::CATEGORY_ID
                ),
                tbl4::TABLE_NAME,
                where: $db->where->equals([
                    tbl4::ID => $id
                ])
            )->rows,

            "categories" => $db->db_select(
                array(
                    tbl11::BRANCH_ID,
                    tbl11::ID,
                    $db->as_name(tbl11::NAME.$sessions->get->LANGUAGE_TAG, "name")
                ),
                tbl11::TABLE_NAME,
                where: $db->where->equals([
                    tbl11::BRANCH_ID => user::post(post_keys::BRANCH_ID)
                ])
            )->rows
        );
    }

    private function sales_product_waiter(db $db, db $db_backup, sessions $sessions) : array{
        $id = array(
            "product" => array(),
            "waiter" => array()
        );

        $where = $db->where->equals([
            tbl3::BRANCH_ID => user::post(post_keys::BRANCH_ID),
            tbl3::ACCOUNT_TYPE => account_types::WAITER,
            tbl3::STATUS => order_products_status_types::ACTIVE,
            tbl3::TYPE => order_product_types::PRODUCT,
            tbl6::SAFE_ID => user::post(post_keys::SAFE_ID)
        ]);
        $join = $db->join->inner([tbl6::TABLE_NAME => [tbl6::ID => tbl3::ORDER_ID]]);

        $new = $db->db_select(
            array(
                tbl3::BRANCH_ID,
                tbl3::ACCOUNT_ID,
                tbl3::PRODUCT_ID,
                $db->as_name($db->if_null($db->sum(tbl3::QTY), 0), "total_qty"),
                $db->as_name($db->if_null($db->sum(tbl3::QUANTITY), 0), "total_quantity"),
                $db->as_name($db->if_null($db->sum(tbl3::PRICE), 0), "total")
            ),
            tbl3::TABLE_NAME,
            joins: $join,
            where: $where,
            group_by: $db->group_by([tbl3::BRANCH_ID, tbl3::PRODUCT_ID, tbl3::ACCOUNT_ID]),
            order_by: $db->order_by([tbl3::BRANCH_ID,"total"], db::DESC)
        )->rows;

        $old = $db_backup->db_select(
            array(
                tbl3::BRANCH_ID,
                tbl3::ACCOUNT_ID,
                tbl3::PRODUCT_ID,
                $db->as_name($db->if_null($db->sum(tbl3::QTY), 0), "total_qty"),
                $db->as_name($db->if_null($db->sum(tbl3::QUANTITY), 0), "total_quantity"),
                $db->as_name($db->if_null($db->sum(tbl3::PRICE), 0), "total")
            ),
            tbl3::TABLE_NAME,
            joins: $join,
            where: $where,
            group_by: $db->group_by([tbl3::BRANCH_ID, tbl3::PRODUCT_ID, tbl3::ACCOUNT_ID]),
            order_by: $db->order_by([tbl3::BRANCH_ID,"total"], db::DESC)
        )->rows;

        foreach ($new as $data){
            if(array_list::index_of($id["waiter"], $data["account_id"]) < 0) array_push($id["waiter"], $data["account_id"]);
            if(array_list::index_of($id["product"], $data["product_id"]) < 0) array_push($id["product"], $data["product_id"]);
        }

        foreach ($old as $data){
            if(array_list::index_of($id["waiter"], $data["account_id"]) < 0) array_push($id["waiter"], $data["account_id"]);
            if(array_list::index_of($id["product"], $data["product_id"]) < 0) array_push($id["product"], $data["product_id"]);
        }

        return array(
            "new" => $new,

            "old" => $old,

            "accounts" => $db->db_select(
                array(
                    tbl7::BRANCH_ID,
                    tbl7::ID,
                    tbl7::NAME
                ),
                tbl7::TABLE_NAME,
                where: $db->where->equals([
                tbl7::ID => $id["waiter"]
            ]),
                order_by: tbl7::BRANCH_ID
            )->rows,

            "products" => $db->db_select(
                array(
                    tbl4::BRANCH_ID,
                    tbl4::ID,
                    tbl4::QUANTITY_ID,
                    tbl4::CODE,
                    $db->as_name(tbl4::NAME.$sessions->get->LANGUAGE_TAG, "name")
                ),
                tbl4::TABLE_NAME,
                where: $db->where->equals([
                    tbl4::ID => $id["product"]
                ])
            )->rows
        );
    }

    private function sales_customer(db $db, db $db_backup, sessions $sessions) : array{
        $id = array(
            account_types::CUSTOMER => array(),
            account_types::YEMEK_SEPETI => array()
        );
        $type = array();

        $where = $db->where->equals([
                tbl3::BRANCH_ID => user::post(post_keys::BRANCH_ID),
                tbl3::ACCOUNT_TYPE => [account_types::CUSTOMER, account_types::YEMEK_SEPETI],
                tbl3::STATUS => order_products_status_types::ACTIVE,
                tbl6::SAFE_ID => user::post(post_keys::SAFE_ID)
            ]);
        $join = $db->join->inner([tbl6::TABLE_NAME => [tbl6::ID => tbl3::ORDER_ID]]);

        $new = $db->db_select(
            array(
                tbl3::ACCOUNT_TYPE,
                tbl3::BRANCH_ID,
                tbl3::ACCOUNT_ID,
                $db->as_name($db->if_null($db->sum(tbl3::PRICE), 0), "total")
            ),
            tbl3::TABLE_NAME,
            joins: $join,
            where: $where,
            group_by: $db->group_by([tbl3::BRANCH_ID,tbl3::ACCOUNT_ID]),
            order_by: $db->order_by([tbl3::BRANCH_ID,"total"], db::DESC)
        )->rows;

        $old = $db_backup->db_select(
            array(
                tbl3::ACCOUNT_TYPE,
                tbl3::BRANCH_ID,
                tbl3::ACCOUNT_ID,
                $db->as_name($db->if_null($db->sum(tbl3::PRICE),0), "total")
            ),
            tbl3::TABLE_NAME,
            joins: $join,
            where: $where,
            group_by: $db->group_by([tbl3::BRANCH_ID,tbl3::ACCOUNT_ID]),
            order_by: $db->order_by([tbl3::BRANCH_ID,"total"], db::DESC)
        )->rows;

        foreach ($new as $data){
            if(array_list::index_of($id[$data["account_type"]], $data["account_id"]) < 0) array_push($id[$data["account_type"]], $data["account_id"]);
            if(array_list::index_of($type, $data["account_type"]) < 0) array_push($type, $data["account_type"]);
        }

        foreach ($old as $data){
            if(array_list::index_of($id[$data["account_type"]], $data["account_id"]) < 0) array_push($id[$data["account_type"]], $data["account_id"]);
            if(array_list::index_of($type, $data["account_type"]) < 0) array_push($type, $data["account_type"]);
        }

        return array(
            "new" => $new,

            "old" => $old,

            "accounts" => $db->db_select(
                array(
                    tbl5::ID,
                    tbl5::NAME
                ),
                tbl5::TABLE_NAME,
                where: $db->where->equals([
                    tbl5::ID => $id[account_types::CUSTOMER]
                ])
            )->rows,

            "accounts_yemek_sepeti" => $db->db_select(
                array(
                    tbl14::ID,
                    tbl14::NAME
                ),
                tbl14::TABLE_NAME,
                where: $db->where->equals([
                    tbl14::ID => $id[account_types::YEMEK_SEPETI]
                ])
            )->rows,

            "types" => (array)$db->db_select(
                array(
                    tbl13::ID,
                    $db->as_name(tbl13::NAME.$sessions->get->LANGUAGE_TAG, "name")
                ),
                tbl13::TABLE_NAME,
                where: $db->where->equals([
                    tbl13::ID => $type
                ])
            )->rows
        );
    }

    private function sales_waiter(db $db, db $db_backup, sessions $sessions) : array{
        $id = array();

        $where = $db->where->equals([
                tbl3::BRANCH_ID => user::post(post_keys::BRANCH_ID),
                tbl3::ACCOUNT_TYPE => account_types::WAITER,
                tbl3::STATUS => order_products_status_types::ACTIVE,
                tbl6::SAFE_ID => user::post(post_keys::SAFE_ID)
            ]);
        $join = $db->join->inner([tbl6::TABLE_NAME => [tbl6::ID => tbl3::ORDER_ID]]);

        $new = $db->db_select(
            array(
                tbl3::BRANCH_ID,
                tbl3::ACCOUNT_ID,
                $db->as_name($db->if_null($db->sum(tbl3::PRICE), 0), "total")
            ),
            tbl3::TABLE_NAME,
            joins: $join,
            where: $where,
            group_by: $db->group_by([tbl3::BRANCH_ID, tbl3::ACCOUNT_ID]),
            order_by: $db->order_by([tbl3::BRANCH_ID,"total"], db::DESC)
        )->rows;

        $old = $db_backup->db_select(
            array(
                tbl3::BRANCH_ID,
                tbl3::ACCOUNT_ID,
                $db->as_name($db->if_null($db->sum(tbl3::PRICE), 0), "total")
            ),
            tbl3::TABLE_NAME,
            joins: $join,
            where: $where,
            group_by: $db->group_by([tbl3::BRANCH_ID, tbl3::ACCOUNT_ID]),
            order_by: $db->order_by([tbl3::BRANCH_ID,"total"], db::DESC)
        )->rows;

        foreach ($new as $data){
            if(array_list::index_of($id, $data["account_id"]) < 0) array_push($id, $data["account_id"]);
        }

        foreach ($old as $data){
            if(array_list::index_of($id, $data["account_id"]) < 0) array_push($id, $data["account_id"]);
        }

        return array(
            "new" => $new,

            "old" => $old,

            "accounts" => $db->db_select(
                array(
                    tbl7::BRANCH_ID,
                    tbl7::ID,
                    tbl7::NAME
                ),
                tbl7::TABLE_NAME,
                where: $db->where->equals([
                    tbl7::ID => $id
                ]),
                order_by: tbl7::BRANCH_ID
            )->rows
        );
    }

    private function sales_table(db $db, db $db_backup, sessions $sessions) : array{
        $id = array();

        $where = $db->where->equals([
                tbl::BRANCH_ID => user::post(post_keys::BRANCH_ID),
                tbl::STATUS => [
                    order_payment_status_types::PAID,
                    order_payment_status_types::COST
                ],
                tbl::SAFE_ID => user::post(post_keys::SAFE_ID),
            ]);
        $join = $db->join->inner([
            tbl6::TABLE_NAME => [tbl6::ID => tbl::ORDER_ID]
        ]);


        $new = $db->db_select(
            array(
                tbl6::BRANCH_ID,
                tbl6::TABLE_ID,
                $db->as_name($db->if_null($db->sum(tbl::PRICE), 0), "total")
            ),
            tbl::TABLE_NAME,
            joins: $join,
            where: $where,
            group_by: $db->group_by([tbl::BRANCH_ID, tbl6::TABLE_ID]),
            order_by: $db->order_by([tbl::BRANCH_ID,"total"], db::DESC)
        )->rows;

        $old = $db_backup->db_select(
            array(
                tbl6::BRANCH_ID,
                tbl6::TABLE_ID,
                $db->as_name($db->if_null($db->sum(tbl::PRICE),0), "total")
            ),
            tbl::TABLE_NAME,
            joins: $join,
            where: $where,
            group_by: $db->group_by([tbl::BRANCH_ID, tbl6::TABLE_ID]),
            order_by: $db->order_by([tbl::BRANCH_ID,"total"], db::DESC)
        )->rows;

        foreach ($new as $data){
            if(array_list::index_of($id, $data["table_id"]) < 0) array_push($id, $data["table_id"]);
        }

        foreach ($old as $data){
            if(array_list::index_of($id, $data["table_id"]) < 0) array_push($id, $data["table_id"]);
        }

        return array(
            "new" => $new,
            "old" => $old,
            "tables" => $db->db_select(
                array(
                    tbl8::BRANCH_ID,
                    tbl8::ID,
                    tbl8::NO,
                    $db->as_name(tbl10::NAME.$sessions->get->LANGUAGE_TAG, "section_name")
                ),
                tbl8::TABLE_NAME,
                joins: $db->join->inner([
                    tbl9::TABLE_NAME => [tbl9::ID => tbl8::SECTION_ID],
                    tbl10::TABLE_NAME => [tbl10::ID => tbl9::SECTION_ID]
                ]),
                where: $db->where->equals([
                    tbl8::ID => $id
                ]),
                order_by: tbl8::BRANCH_ID
            )->rows,
            "sql" => $db_backup->db_select(
                array(
                    tbl6::BRANCH_ID,
                    tbl6::TABLE_ID,
                    $db->as_name($db->if_null($db->sum(tbl::PRICE),0), "total")
                ),
                tbl::TABLE_NAME,
                joins: $join,
                where: $where,
                group_by: $db->group_by([tbl::BRANCH_ID, tbl6::TABLE_ID]),
                order_by: $db->order_by([tbl::BRANCH_ID,"total"], db::DESC),
                just_show_sql: true
            )->sql
        );
    }

    private function rush_hours(db $db, db $db_backup, sessions $sessions) : array{
        $where = $db->where->equals([
                tbl3::BRANCH_ID => user::post(post_keys::BRANCH_ID),
                tbl3::STATUS => order_products_status_types::ACTIVE,
                tbl6::SAFE_ID => user::post(post_keys::SAFE_ID),
            ]);
        $join = $db->join->inner([
            tbl6::TABLE_NAME => [tbl6::ID => tbl3::ORDER_ID]
        ]);

        return array(
            "new" => $db->db_select(
                array(
                    tbl3::BRANCH_ID,
                    $db->as_name($db->concat($db->substring(tbl3::TIME, 1, 3), "'00'"), "time"),
                    $db->as_name($db->if_null($db->sum(tbl3::PRICE), 0), "total")
                ),
                tbl3::TABLE_NAME,
                joins: $join,
                where: $where,
                group_by: $db->group_by([tbl3::BRANCH_ID,$db->substring(tbl3::TIME, 1, 2)]),
                order_by: $db->order_by([tbl3::BRANCH_ID,"total"], db::DESC)
            )->rows,

            "old" => $db_backup->db_select(
                array(
                    tbl3::BRANCH_ID,
                    $db->as_name($db->concat($db->substring(tbl3::TIME, 1, 3), "'00'"), "time"),
                    $db->as_name($db->if_null($db->sum(tbl3::PRICE), 0), "total")
                ),
                tbl3::TABLE_NAME,
                joins: $join,
                where: $where,
                group_by: $db->group_by([tbl3::BRANCH_ID,$db->substring(tbl3::TIME, 1, 2)]),
                order_by: $db->order_by([tbl3::BRANCH_ID,"total"], db::DESC)
            )->rows
        );
    }

    private function orders_cancel(db $db, db $db_backup, sessions $sessions) : array{
        $id = array();

        $where = $db->where->equals([
                tbl3::BRANCH_ID => user::post(post_keys::BRANCH_ID),
                tbl3::STATUS => order_products_status_types::CANCEL,
                tbl6::SAFE_ID => user::post(post_keys::SAFE_ID),
            ]);
        $join = $db->join->inner([tbl6::TABLE_NAME => [tbl6::ID => tbl3::ORDER_ID]]);


        $new = $db->db_select(
            array(
                tbl3::BRANCH_ID,
                tbl3::PRODUCT_ID,
                $db->as_name($db->if_null($db->sum(tbl3::QTY), 0), "total_qty"),
                $db->as_name($db->if_null($db->sum(tbl3::QUANTITY), 0), "total_quantity"),
                $db->as_name($db->if_null($db->sum(tbl3::PRICE), 0), "total")
            ),
            tbl3::TABLE_NAME,
            joins: $join,
            where: $where,
            group_by: $db->group_by([tbl3::BRANCH_ID,tbl3::PRODUCT_ID]),
            order_by: $db->order_by([tbl3::BRANCH_ID,"total"], db::DESC)
        )->rows;

        $old = $db_backup->db_select(
            array(
                tbl3::BRANCH_ID,
                tbl3::PRODUCT_ID,
                $db->as_name($db->if_null($db->sum(tbl3::QTY), 0), "total_qty"),
                $db->as_name($db->if_null($db->sum(tbl3::QUANTITY), 0), "total_quantity"),
                $db->as_name($db->if_null($db->sum(tbl3::PRICE), 0), "total")
            ),
            tbl3::TABLE_NAME,
            joins: $join,
            where: $where,
            group_by: $db->group_by([tbl3::BRANCH_ID,tbl3::PRODUCT_ID]),
            order_by: $db->order_by([tbl3::BRANCH_ID,"total"], db::DESC)
        )->rows;

        foreach ($new as $data){
            if(array_list::index_of($id, $data["product_id"]) < 0) array_push($id, $data["product_id"]);
        }

        foreach ($old as $data){
            if(array_list::index_of($id, $data["product_id"]) < 0) array_push($id, $data["product_id"]);
        }

        return array(
            "new" => $new,

            "old" => $old,

            "products" => $db->db_select(
                array(
                    tbl4::BRANCH_ID,
                    tbl4::ID,
                    tbl4::QUANTITY_ID,
                    $db->as_name(tbl4::NAME.$sessions->get->LANGUAGE_TAG, "name")
                ),
                tbl4::TABLE_NAME,
                where: $db->where->equals([
                    tbl4::ID => $id
                ]),
                order_by: tbl4::BRANCH_ID
            )->rows
        );
    }

    private function orders_catering(db $db, db $db_backup, sessions $sessions) : array{
        $id = array();

        $where = $db->where->equals([
                tbl3::BRANCH_ID => user::post(post_keys::BRANCH_ID),
                tbl3::STATUS => order_products_status_types::CATERING,
                tbl6::SAFE_ID => user::post(post_keys::SAFE_ID),
            ]);
        $join = $db->join->inner([tbl6::TABLE_NAME => [tbl6::ID => tbl3::ORDER_ID]]);


        $new = $db->db_select(
            array(
                tbl3::BRANCH_ID,
                tbl3::PRODUCT_ID,
                $db->as_name($db->if_null($db->sum(tbl3::QTY), 0), "total_qty"),
                $db->as_name($db->if_null($db->sum(tbl3::QUANTITY), 0), "total_quantity"),
                $db->as_name($db->if_null($db->sum(tbl3::PRICE), 0), "total")
            ),
            tbl3::TABLE_NAME,
            joins: $join,
            where: $where,
            group_by: $db->group_by([tbl3::BRANCH_ID,tbl3::PRODUCT_ID]),
            order_by: $db->order_by([tbl3::BRANCH_ID,"total"], db::DESC)
        )->rows;

        $old = $db_backup->db_select(
            array(
                tbl3::BRANCH_ID,
                tbl3::PRODUCT_ID,
                $db->as_name($db->if_null($db->sum(tbl3::QTY), 0), "total_qty"),
                $db->as_name($db->if_null($db->sum(tbl3::QUANTITY), 0), "total_quantity"),
                $db->as_name($db->if_null($db->sum(tbl3::PRICE), 0), "total")
            ),
            tbl3::TABLE_NAME,
            joins: $join,
            where: $where,
            group_by: $db->group_by([tbl3::BRANCH_ID,tbl3::PRODUCT_ID]),
            order_by: $db->order_by([tbl3::BRANCH_ID,"total"], db::DESC)
        )->rows;

        foreach ($new as $data){
            if(array_list::index_of($id, $data["product_id"]) < 0) array_push($id, $data["product_id"]);
        }

        foreach ($old as $data){
            if(array_list::index_of($id, $data["product_id"]) < 0) array_push($id, $data["product_id"]);
        }

        return array(
            "new" => $new,

            "old" => $old,

            "products" => $db->db_select(
                array(
                    tbl4::BRANCH_ID,
                    tbl4::ID,
                    tbl4::QUANTITY_ID,
                    $db->as_name(tbl4::NAME.$sessions->get->LANGUAGE_TAG, "name")
                ),
                tbl4::TABLE_NAME,
                where: $db->where->equals([
                    tbl4::ID => $id
                ]),
                order_by: tbl4::BRANCH_ID
            )->rows
        );
    }

    private function orders_take_away(db $db, sessions $sessions) : array{}
    private function questions_point(db $db, sessions $sessions) : array{}
    private function questions_text(db $db, sessions $sessions) : array{}
    private function sales_product_by_table(db $db, sessions $sessions) : array{}
}
