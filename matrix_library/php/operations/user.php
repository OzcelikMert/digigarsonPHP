<?php
namespace matrix_library\php\operations;

abstract class method_types {
    const POST = 0x0001,
    GET = 0x0002,
    SESSION = 0x0003;
}

abstract class check_types {
    const IS_SET = 0x0001,
        EMPTY = 0x0002,
        IS_NULL = 0x0003;
}

/**
 * With this class you can control users.
 */
class user {
    /**
     * Gets the user's ethernet ip.
     * @return string (HTTP_CLIENT_IP | HTTP_X_FORWARDED_FOR | HTTP_X_FORWARDED_FOR)
     */
    public static function get_ip_address() : string{
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            //ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            //ip pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * Checks if there is a session or not
     * @return bool
     */
    public static function check_session_start() : bool {
        $value = false;
        if(session_status() == PHP_SESSION_ACTIVE) {
            $value = true;
        }
        return $value;
    }

    /**
     * Checks if the incoming post value is from javascript ajax.
     * @param array $ajax_post
     * @return array
     */
    public static function check_ajax_post(array $ajax_post) : array{
        $values =  array();
        $values["status"] = false;
        $values["post_name"] = null;

        $HTTP_REFERER = str_replace("https://","",$_SERVER['HTTP_REFERER']);
        $HTTP_REFERER = str_replace("http://","",$HTTP_REFERER);
        $HTTP_REFERER = str_replace("/","",$HTTP_REFERER);

        if(($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') && (isset($_SERVER['HTTP_REFERER'])) && $HTTP_REFERER == $_SERVER['SERVER_NAME']) {

            foreach ($ajax_post as $post) {
                $values["status"] = (isset($_POST[$post])) || (isset($_GET[$post]));

                if(!$values["status"]){
                    $values["post_name"] =  $ajax_post;
                    return $values;
                }
            }

        }else {
            $values["post_name"] = "Ip is invalid";
        }

        return $values;
    }

    /**
     * Checks data directed to the page.
     * @param array $keys
     * @param int $method_type
     * @param int $check_type
     * @return bool
     */
    public static function check_sent_data(
        array $keys,
        int $method_type = method_types::POST,
        int $check_type = check_types::IS_SET
    ) : bool{
        $method = self::check_method($method_type);
        foreach($keys as $key => $value){
            if(!static::check_value($method, $value, $check_type)) return false;
        }
        return true;
    }

    /**
     * Get $GLOBALS value.
     * If u are defining value then it sets the value.
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public static function global(string $key, mixed $value = null) : mixed{
        if (!is_null($value)) $GLOBALS[$key] = $value;
        return $GLOBALS[$key] ?? false;
    }

    /**
     * Get $_POST value.
     * If u are defining value then it sets the value.
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public static function post(string $key, mixed $value = null) : mixed{
        if (!is_null($value)) $_POST[$key] = $value;
        return isset($_POST[$key]) ? $_POST[$key] : false;
    }

    /**
     * Get $_GET value
     * If u are defining value then it sets the value.
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public static function get(string $key, mixed $value = null) : mixed{
        if (!is_null($value)) $_GET[$key] = $value;
        return isset($_GET[$key]) ? $_GET[$key] : false;
    }

    /**
     * Get $_FILES value
     * @param string $key
     * @return mixed
     */
    public static function files(string $key) : mixed{
        return isset($_FILES[$key]) ? $_FILES[$key] : false;
    }

    /**
     * Get $_SESSION value
     * If u are defining value then it sets the value.
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public static function session(string $key, mixed $value = null) : mixed{
        if (!is_null($value)) $_SESSION[$key] = $value;
        return isset($_SESSION[$key]) ? $_SESSION[$key] : false;
    }

    private static function check_method(int $method_type) : array{
        $method = array();
        switch ($method_type){
            case method_types::POST:
                $method = $_POST;
                break;
            case method_types::GET:
                $method = $_GET;
                break;
            case method_types::SESSION:
                if(!self::check_session_start()) session_start();
                $method = $_SESSION;
                break;
        }
        return $method;
    }

    private static function check_value(array $method, string $key, int $check_type) : bool{
        switch ($check_type){
            case check_types::IS_SET:
                if(!isset($method[$key])) return false;
                break;
            case check_types::EMPTY:
                if(!static::check_value($method, $key, check_types::IS_SET) || empty($method[$key])) return false;
                break;
            case check_types::IS_NULL:
                if(!static::check_value($method, $key, check_types::IS_SET) || is_null($method[$key])) return false;
                break;
        }
        return true;
    }

    /**
     * Sets the sessions you want to create.
     * @param array $sessions
     */
    public static function session_creator(array $sessions) : void{
        if(!self::check_session_start()) {
            session_start();
            session_regenerate_id();
        }
        foreach($sessions as $key => $value)
            $_SESSION[$key] = $value;
    }
}