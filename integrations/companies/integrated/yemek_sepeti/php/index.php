<?php
namespace integrations\companies\integrated\yemek_sepeti\php;
require "../../../../../matrix_library/php/auto_loader.php";

use integrations\companies\integrated\yemek_sepeti\php\config\service;
use integrations\companies\integrated\yemek_sepeti\php\config\service_list;
use integrations\companies\integrated\yemek_sepeti\php\helper\service_1\helper;
use matrix_library\php\operations\method_types;
use matrix_library\php\operations\server;
use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use mysql_xdevapi\Exception;
use sameparts\php\ajax\echo_values;
use SoapFault;
session_start();
class post_keys {
    const USER_NAME = "user_name",
        PASSWORD = "password";
}
$result = array();
if(user::check_sent_data([post_keys::USER_NAME, post_keys::PASSWORD], method_types::GET)) {
    variable::clear_all_data($_GET);

    $service = new service(service_list::SERVICE_1, user::get(post_keys::USER_NAME), user::get(post_keys::PASSWORD));

    $functions = $service->get_functions();

    echo "<hr> \n <h1>Functions</h1>";
    print_r($functions);
    echo "\n <hr>";

    $result["messages"] = $service->helper_1->get->messages()->rows;

    echo "<hr> \n <h1>Other</h1>";
    print_r($result["messages"]);
    echo "\n <hr>";

    $result["payment_types"] = $service->helper_1->get->payments_types()->rows;
}
?>
<script>
    console.log(<?=json_encode($result)?>)
    console.log(<?=json_encode($_SERVER)?>)
    console.log(<?=json_encode(server::get_url_folders())?>)
</script>