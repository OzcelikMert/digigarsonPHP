<?php
namespace order_app\functions\panel\set;

use config\db;
use config\sessions;
use config\settings;
use config\table_helper\customer_users;
use config\type_tables_values\language_types;
use JetBrains\PhpStorm\ArrayShape;
use matrix_library\php\db_helpers\results;
use matrix_library\php\operations\clear_types;
use matrix_library\php\operations\cryption;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use matrix_library\php\post_services\sms\net_gsm;
use order_app\sameparts\functions\sessions\get;
use order_app\sameparts\functions\sessions\keys;
use sameparts\php\ajax\echo_values;
use config\table_helper\customer_users as tbl;

class post_keys {
    const NAME = "name",
        PHONE = "phone",
        VERIFY_CODE = "verify_code",
        TYPE = "type",
        RANDOM = "random";
}
class login_types {
    const REGISTER = 1,
        LOGIN = 2,
        VERIFY_CODE = 3;
}
class user_keys{
    const NAME = "name",
        PHONE = "phone",
        PHONE_CONFIRM_CODE = "phone_confirm_code",
        ID = "id";
}


class register{
    function __construct(db $db,get $session, echo_values &$echo,$get_class = false){
        if (!$get_class){
            $echo->custom_data["SESSION"] = $_SESSION;
            $echo->custom_data["POST"] = $_POST;

            $type = (int)user::post(post_keys::TYPE);
            if ($type == login_types::REGISTER || $type == login_types::LOGIN){
                $phone = variable::clear(user::post(post_keys::PHONE),clear_types::INT);
                $phone_length = strlen($phone);
                if ($phone_length !== 10){
                    $echo->status = false;
                    $echo->return();
                    exit;
                }
                user::post(post_keys::PHONE,$phone);
            }

            switch ($type) {
                case login_types::REGISTER:
                    if ($echo->status && user::check_sent_data([post_keys::NAME, post_keys::PHONE])) {
                        $this->register($db, $session, $echo);
                    } else $echo->error_code = settings::error_codes()::EMPTY_VALUE;
                    break;

                case login_types::LOGIN:
                    $this->check_login($db,$session,$echo);
                    break;

                case login_types::VERIFY_CODE:
                    if ($echo->status && user::check_sent_data([post_keys::VERIFY_CODE])) {
                        $this->check_verify_code($db, $echo);
                    } else $echo->error_code = settings::error_codes()::EMPTY_VALUE;
                    break;
            }
        }
    }

    function check_login($db,get $session,echo_values &$echo){
        $echo->custom_data["cokie"] = $_COOKIE;
        if (user::session(keys::USER_ID) > 0 && user::session(keys::VERIFY) == true){
            $echo->rows = [
                "name" => user::session(keys::NAME),
                "phone" => user::session(keys::PHONE),
                "user_id" => user::session(keys::USER_ID)
            ];
        } else if (isset($_COOKIE["_ut"])) {
            $echo->message = "ut";
            $echo->rows = $this->get_check_user_token($db,$session,$_COOKIE["_ut"],$echo);
        } else {
            $echo->status = false;
        }
    }

    function register(db $db, get $session, echo_values &$echo){
        $result = $this->get_user($db, $echo);
        user::post(post_keys::RANDOM, rand(1000, 9999));

        if (count($result->rows) > 0) {
            $echo->custom_data["update_user"] = $this->update_user($db, $echo);
            $echo->error_code = settings::error_codes()::REGISTERED_VALUE;

            //new net_gsm("Güvenlik Kodu: " . user::post(post_keys::RANDOM), user::post(post_keys::PHONE));
            $session->NAME  = $result->rows[0][user_keys::NAME];
            $session->PHONE = $result->rows[0][user_keys::PHONE];
            $session->USER_ID = $result->rows[0][user_keys::ID];
            $session->create();
        } else {
            $result = $this->insert_user($db, $echo);
            $echo->custom_data["insert_user"] = $result;
            $echo->status = $result->status;
            if ($result->status && $result->insert_id > 0) {
                //new net_gsm("Güvenlik Kodu: " . user::post(post_keys::RANDOM), user::post(post_keys::PHONE));
                $session->NAME  = user::post(post_keys::NAME);
                $session->PHONE = user::post(post_keys::PHONE);
                $session->USER_ID = $result->insert_id;
                $session->create();
            }
        }


    }

    function get_user(db $db, echo_values &$echo): results{
        $result = $db->db_select(
            array(tbl::ALL),
            tbl::TABLE_NAME,
            where: $db->where->equals([tbl::PHONE => user::post(post_keys::PHONE)])
        );
        if (!count($result->rows) > 0){
            $echo->status = false;
        }
        return $result;
    }

    function update_user(db $db, echo_values &$echo): results{
        $code = user::post(post_keys::RANDOM);
        return $db->db_update(tbl::TABLE_NAME,
            array(
                tbl::PHONE_CONFIRM_CODE => $code,
                tbl::NAME => user::post(post_keys::NAME)
            ),
            where: $db->where->like([tbl::PHONE => user::post(post_keys::PHONE)])
        );
    }

    function insert_user(db $db, echo_values &$echo): results{
        $code = user::post(post_keys::RANDOM);
        return $db->db_insert(tbl::TABLE_NAME,
            array(
                tbl::PHONE => user::post(post_keys::PHONE),
                tbl::NAME => user::post(post_keys::NAME),
                tbl::LANGUAGE_ID => language_types::TR,
                tbl::PHONE_CONFIRM_CODE => $code,
            ),
        );
    }

    function check_verify_code(db $db, echo_values &$echo){
        $echo->custom_data["check_verify_code"] = "start function";
        if (user::session(keys::USER_ID) > 0){
            $result = $db->db_select(
                array(tbl::ALL),
                tbl::TABLE_NAME,
                where: $db->where->equals([tbl::PHONE_CONFIRM_CODE => user::post(post_keys::VERIFY_CODE)])
            );
            $echo->custom_data["check_verify_code"] = $result;
            if (count($result->rows) > 0) {
                user::session(keys::VERIFY, true);
                user::session(keys::VERIFY_CODE, $result->rows[0][user_keys::PHONE_CONFIRM_CODE]);
                $this->create_user_token(user::session(keys::PHONE), user::session(keys::VERIFY_CODE));
                $echo->rows = [
                    "name" => user::session(keys::NAME),
                    "phone" => user::session(keys::PHONE),
                    "user_id" => user::session(keys::USER_ID)
                ];
            } else {
                $echo->status = false;
                $echo->error_code = settings::error_codes()::WRONG_VALUE;
            }
        }
    }

    //#[ArrayShape(["login" => "bool", "id" => "int|mixed", "name" => "mixed|string", "phone" => "mixed|null", "user_id" => "string", 3 => "string"])]
    function get_check_user_token(db $db, get $session, $token, echo_values &$echo) : array{
        $data = array(
            "login" => false,
            "user_id" => "",
            "name" => "",
            "user_id"
        );
        $hash = new cryption();
        $token = $hash->decryption($token);
        $echo->custom_data["token"] = $token;

        if($token["status"]){
            $phone = $token["phone"];
            $verify_code = $token["verify_code"];

            user::post(post_keys::PHONE,$phone);

            $result = $this->get_user($db,$echo);
            if ($result->status && $result->rows[0][user_keys::PHONE_CONFIRM_CODE] == $verify_code){
                $this->create_user_token($result->rows[0][user_keys::PHONE], $verify_code);

                //Sessions
                $session->NAME = $result->rows[0][user_keys::NAME];
                $session->PHONE = $result->rows[0][user_keys::PHONE];
                $session->USER_ID = $result->rows[0][user_keys::ID];
                $session->VERIFY = true;
                $session->create();

                $data = array(
                    "login" => true,
                    "id" => $session->USER_ID,
                    "name" => $session->NAME,
                    "phone" => $session->PHONE
                );
            }
        }
        return $data;
    }

    function create_user_token($phone,$verify_code){
        $hash = new cryption();
        $token = $hash->encryption($phone,$verify_code);
        setcookie("_ut", $token, time()+(365*24*60*60), "/");
    }

}