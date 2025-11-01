<?php
namespace order_app\functions\panel\set;
use config\db;
use config\settings;
use config\table_helper\orders as tbl;
use config\table_helper\order_products as tbl2;
use config\table_helper\products as tbl3;
use config\table_helper\order_product_options as tbl4;
use config\table_helper\product_option_items as tbl6;
use config\table_helper\product_linked_options as tbl7;
use config\table_helper\customer_address as tbl9;
use config\table_helper\branch_payment_types as tbl10;
use config\table_helper\branch_takeaway_address as tbl11;
use config\type_tables_values\account_types;
use config\type_tables_values\order_types;
use config\type_tables_values\print_types;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\array_list;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use order_app\sameparts\functions\sessions\get;
use order_app\sameparts\functions\sessions\keys;
use sameparts\php\ajax\echo_values;
use sameparts\php\db_query\branch_info;
use sameparts\php\db_query\orders;
use sameparts\php\db_query\products;
use sameparts\php\helper\date;
use sameparts\php\printer\printer_values;

class products_keys {
    const ID = "id",
        QUANTITY = "quantity",
        PRICE = "price",
        VAT = "vat",
        DISCOUNT = "discount",
        QTY = "qty",
        COMMENT = "comment",
        OPTIONS = "options";
}
class options_keys {
    const ID = "id",
        OPTION_ID = "option_id",
        OPTION_ITEM_ID = "option_item_id",
        ORDER_ID = "order_id",
        ORDER_PRODUCT_ID = "order_product_id",
        PRICE = "price",
        QTY = "qty";
}
class post_keys {
    const PRODUCTS = "products",
        TABLE_ID = "table_id",
        ORDER_ID = "order_id",
        ORDER_NO = "order_no",
        DISCOUNT = "discount",
        TYPE = "type",
        STATUS = "status";
}

class send_order{
    private printer_values $invoice;

    public function __construct(db $db, get $sessions, echo_values &$echo){
        if ($sessions->USER_ID > 0 &&  $echo->status) {

           //$day_number =  date('w');
           //if ($day_number == 0) { $day_number = 7; } //pazar günü 0 dönüyor
           //$work_times =  branch_info::branch_work_times($db,$sessions->SELECT_BRANCH_ID)->rows;
           //$time = $work_times[ $day_number -1];
           //$echo->custom_data["time"] = $time;

            $check_order = $this->check_order_for_customer($db, $sessions);
            $echo->custom_data["check_order"] = $check_order;
            user::post(post_keys::ORDER_NO,0);

            if (count($check_order->rows) == 0 || user::session(keys::SELECT_BRANCH_TABLE_TYPE) == order_types::TAKEAWAY){
                $order = $this->add_order($db, $sessions,$echo);
                if ($order->status){
                    $echo->custom_data["add_order"] = $order;
                    user::post(post_keys::ORDER_ID, $order->insert_id);
                }else {
                    $echo->error_code = settings::error_codes()::WRONG_VALUE;
                }
            }else {
                user::post(post_keys::ORDER_ID, $check_order->rows[0]["order_id"]);
            }

            $this->invoice = new printer_values(
                db: $db,
                mobile_sessions: $sessions,
                table_id: $sessions->SELECT_BRANCH_TABLE_ID,
                order_id: user::post(post_keys::ORDER_ID),
                order_no: (user::post(post_keys::ORDER_NO) > 0) ? user::post(post_keys::ORDER_NO) : $this->get_order_no($db,$sessions,$echo),
                print_type: print_types::KITCHEN
            );

            $this->add_order_products($db, $sessions, $echo);
        }else {
            $echo->error_code = settings::error_codes()::NOT_LOGGED_IN;
        }
    }

    private function get_order_no(db $db,get $session,echo_values $echo): int{
        return $db->db_select(
            tbl::NO,
            tbl::TABLE_NAME,
            where: $db->where->equals([
                tbl::BRANCH_ID => $session->SELECT_BRANCH_ID,
                tbl::ID => user::post(post_keys::ORDER_ID)
            ]))->rows[0]["no"];
    }

    /* Functions */
    private function add_order(db $db, get $sessions,echo_values &$echo) : results{
        $result = new results();
        $order_no = orders::get_order_last_no($db, $sessions->SELECT_BRANCH_ID);
        user::post(post_keys::ORDER_NO,$order_no);
        switch (user::session(keys::SELECT_BRANCH_TABLE_TYPE)){
            case order_types::TAKEAWAY:
                $status = [
                    "address" => false,
                    "payment" => false,
                ];
                $status["address"] = $this->check_takeaway_address_for_user($db,$sessions,$echo);
                $status["payment"] = $this->check_takeaway_payment_method($db,$sessions,$echo);
                $echo->rows["status"] = $status;
                if ($status["address"] && $status["payment"]){
                    return $db->db_insert(
                        tbl::TABLE_NAME,
                        array(
                            tbl::BRANCH_ID => $sessions->SELECT_BRANCH_ID,
                            tbl::TABLE_ID => order_types::TAKEAWAY,
                            tbl::DATE_START => date::get(),
                            tbl::DISCOUNT => 0,
                            tbl::TYPE => order_types::TAKEAWAY,
                            tbl::STATUS => 0,
                            tbl::NO => orders::get_order_last_no($db, $sessions->SELECT_BRANCH_ID),
                            tbl::ADDRESS_ID => user::post("takeaway")["user_address_id"],
                            tbl::COMMENT => user::post("takeaway")["note"],
                            tbl::IS_CONFIRM => 0,
                        )
                    );
                }else {
                    $result->status = false;
                }
                break;
            case order_types::TABLE:
                $result = $db->db_insert(
                    tbl::TABLE_NAME,
                    array(
                        tbl::BRANCH_ID => $sessions->SELECT_BRANCH_ID,
                        tbl::TABLE_ID =>  $sessions->SELECT_BRANCH_TABLE_ID,
                        tbl::DATE_START => date::get(),
                        tbl::DISCOUNT => 0,
                        tbl::TYPE => 1,
                        tbl::STATUS => 0,
                        tbl::NO => $order_no
                    )
                );
                break;
        }
        return $result;
    }
    private function add_order_products(db $db, get $sessions, echo_values $echo): void{
        $data = array();
        $time = date::get(date::date_type_simples()::HOUR_MINUTE);
        $result = null;
        $index = -1;
        foreach (user::post("order") as $value) {
            if ($echo->status) {
                $product = $this->check_product($db, $sessions->SELECT_BRANCH_ID, $value["product"][products_keys::ID])->rows;
                $total_price = 0;
                $index++;
                if (count($product) > 0) {
                    $echo->custom_data["product"] = $product[0];
                    $product = $product[0];
                    $insert_data = array(
                        tbl2::BRANCH_ID => $sessions->SELECT_BRANCH_ID,
                        tbl2::ACCOUNT_ID => $sessions->USER_ID,
                        tbl2::ACCOUNT_TYPE => account_types::CUSTOMER,
                        tbl2::ORDER_ID =>  user::post(post_keys::ORDER_ID),
                        tbl2::PRODUCT_ID => $product["id"],
                        tbl2::PRICE => $product["price"],
                        tbl2::VAT => $product["vat"],
                        tbl2::QTY => $value["product"]["amount"],
                        tbl2::TIME => $time,
                        tbl2::QUANTITY => 1,
                        tbl2::DISCOUNT => 0,
                        tbl2::PRINT => 0,
                        tbl2::COMMENT => ""
                    );

                    $this->invoice->products[$index] = array(
                        "product_id"    => (int)$product["id"],
                        "order_id"      => (int)user::post(post_keys::ORDER_ID),
                        "price"         => (int)$product["price"],
                        "quantity"      => 1,
                        "qty"           => (int)$value["product"]["amount"],
                        "comment"       => "",
                        "category_id"   => (int)($product["category_id"] ?? 0),
                        "is_print"      => 0
                    );

                    $total_price += $product["price"];
                    $option_count = null;
                    $option_values = array();

                    $this->invoice->products[$index]["options"] = array();
                    if (isset($value["options"]) && count($value["options"]) > 0) {
                        $insert_options_data = array();
                        foreach ($value["options"] as $option) {
                            if ($echo->status && isset($option["items"] )) {
                                foreach ($option["items"] as $item) {
                                    array_push($option_values, $item["id"]);
                                }
                                $option_count = $db->db_select(tbl6::ALL, tbl6::TABLE_NAME, where: $db->where->equals([tbl6::ID => $option_values]))->rows;

                                if (count($option_count) == count($option_values)) {
                                    foreach ($option["items"] as $item) {
                                        array_push($insert_options_data, array(
                                            tbl4::BRANCH_ID => $sessions->SELECT_BRANCH_ID,
                                            tbl4::ORDER_PRODUCT_ID => 0,
                                            tbl4::OPTION_ID => $option["id"],
                                            tbl4::OPTION_ITEM_ID => $item["id"],
                                            tbl4::PRICE => (float)$item["price"] * (int)$value["product"]["amount"],
                                            tbl4::QTY => $value["product"]["amount"]
                                        ));

                                        array_push(
                                            $this->invoice->products[$index]["options"],
                                            array(
                                                "option_id"      => (int)$option["id"],
                                                "option_item_id" => (int)$item["id"],
                                                "price"          => (int)$item["price"] * (int)$value["product"]["amount"],
                                                "qty"            => (int)$value["product"]["amount"]
                                            )
                                        );

                                        $total_price += $item["price"];
                                    }
                                } else {
                                    $echo->status = false;
                                }

                            }
                        }

                        if ($echo->status) {
                            $insert_data[tbl2::PRICE] = (float)$total_price * (int)$insert_data[tbl2::QTY];
                            $insert_id = $db->db_insert(tbl2::TABLE_NAME, $insert_data)->insert_id;
                            foreach ($insert_options_data as $key => $ins) {
                                $insert_options_data[$key][tbl4::ORDER_PRODUCT_ID] = $insert_id;
                            }
                            $db->db_insert(tbl4::TABLE_NAME, $insert_options_data);
                        }
                        continue;
                    }

                    $insert_data[tbl2::PRICE] = (float)$total_price * (int)$insert_data[tbl2::QTY];
                    array_push($data, $insert_data);
                } else {
                    $echo->status = false;
                    $echo->error_code = settings::error_codes()::INCORRECT_DATA;
                }
            }

        }

        if ($echo->status) {
            $result = $db->db_insert(tbl2::TABLE_NAME, $data);
            $this->invoice->create();
        }else {
            $result = new results();
            $result->status = false;
        }
    }
    private function check_product(db $db, $branch_id,$product_id): results{
        return $db->db_select(
            tbl3::ALL,tbl3::TABLE_NAME,
            where: $db->where->equals(
                [tbl3::BRANCH_ID => $branch_id,tbl3::ID => $product_id, tbl3::DELETE => 0]
        ));
    }
    private function check_order_for_customer(db $db, get $session): results{
        return $db->db_select(
            tbl2::ORDER_ID,tbl2::TABLE_NAME,
            $db->join->left([tbl::TABLE_NAME =>[tbl::ID => tbl2::ORDER_ID]]),
            where: $db->where->equals(
                [
                    tbl::BRANCH_ID => $session->SELECT_BRANCH_ID,
                    tbl::TABLE_ID => $session->SELECT_BRANCH_TABLE_ID,
                    tbl::DATE_END => "",
                    tbl2::ACCOUNT_TYPE => account_types::CUSTOMER,
                    tbl2::ACCOUNT_ID => $session->USER_ID
                ]
            )
        );
    }
    private function check_takeaway_address_for_user(db $db, get $session,echo_values &$echo): bool{
        $result = $db->db_select(
            [tbl9::NEIGHBORHOOD],
            tbl9::TABLE_NAME,
            where: $db->where->equals([
                tbl9::ID => user::post("takeaway")["user_address_id"]
            ])
        );
       // $echo->custom_data["check_takeaway_address_for_user"] = $result;

        if(count($result->rows) == 1){
            user::post("user_address",$result->rows[0]["neighborhood"]);
           return $this->check_takeaway_branch_check_address($db,$session,$echo);
        }

        return false;
    }
    private function check_takeaway_payment_method(db $db, get $session,echo_values &$echo): bool{
        if (user::post("takeaway")["payment_id"] == 1 || user::post("takeaway")["payment_id"] == 2){
            return true;
        }else {
            $result = $db->db_select(
                [tbl10::ACTIVE_TAKE_AWAY],
                tbl10::TABLE_NAME,
                where: $db->where->equals([
                tbl10::BRANCH_ID => $session->SELECT_BRANCH_ID,
                tbl10::TYPE_ID => user::post("takeaway")["payment_id"],
                tbl10::ACTIVE_TAKE_AWAY => 1])
            );
           // $echo->custom_data["check_takeaway_payment_method"] = $result;
            return (count($result->rows) == 1);
        }
    }
    private function check_takeaway_branch_check_address(db $db, get $session,echo_values &$echo): bool{
            $result = $db->db_select(
                [tbl11::NEIGHBORHOOD_ID],
                tbl11::TABLE_NAME,
                where: $db->where->equals([
                tbl11::BRANCH_ID => $session->SELECT_BRANCH_ID,
                tbl11::NEIGHBORHOOD_ID =>  user::post("user_address")
                ])
            );
           // $echo->custom_data["check_takeaway_branch_check_address"] = $result;
            return (count($result->rows) == 1);
    }

    /*Daha sonra kullanılabilir*/
    private function check_values(db $db, get $sessions, echo_values &$echo){
        if(variable::is_empty(
            user::post(post_keys::PRODUCTS),
            user::post(post_keys::ORDER_ID),
            user::post(post_keys::TABLE_ID),
            user::post(post_keys::DISCOUNT),
            user::post(post_keys::STATUS),
            user::post(post_keys::TYPE)
        )){
            $echo->error_code = settings::error_codes()::EMPTY_VALUE;
        }

        if($echo->error_code == settings::error_codes()::SUCCESS){
            $id = array();
            foreach (user::post(post_keys::PRODUCTS) as $value){
                if(array_list::index_of($id, $value[products_keys::ID]) < 0) array_push($id, $value[products_keys::ID]);
            }
            if(count(products::get(
                    $db,
                    $sessions->LANGUAGE_TAG,
                    $sessions->SELECT_BRANCH_ID,
                    custom_where: $db->where->equals([tbl3::ID => $id]),
                    limit: [0, count($id)]
                )->rows) != count($id)) $echo->error_code = settings::error_codes()::INCORRECT_DATA;
        }

        if($echo->error_code != settings::error_codes()::SUCCESS) $echo->status = false;
    }
    private function check_product_linked_option(db $db, $branch_id,$product_id,$option_id): results{
        return $db->db_select(
            tbl7::ALL,tbl7::TABLE_NAME,
            where: $db->where->equals(
            [tbl7::BRANCH_ID => $branch_id,tbl7::PRODUCT_ID => $product_id,tbl7::OPTION_ID => $option_id]
        ));
    }
    private function check_product_option_items(db $db, $branch_id,$option_id,$item_id): results{
        return $db->db_select(
            tbl6::ALL,tbl6::TABLE_NAME,
            where: $db->where->equals(
            [tbl6::BRANCH_ID => $branch_id,tbl6::OPTION_ID => $option_id,tbl6::ID => $item_id, tbl6::IS_DELETED => 0]
        ));
    }

}