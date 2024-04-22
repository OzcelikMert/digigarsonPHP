<?php
namespace integrations\companies\integrated\yemek_sepeti\php\functions\set;

use config\sessions;
use integrations\companies\integrated\yemek_sepeti\php\config\service;
use matrix_library\php\operations\user;
use sameparts\php\ajax\echo_values;

class post_keys {
    const ORDER_ID = "order_id", ORDER_STATE = "order_state", REASON = "reason";
}

class update_order{
    function __construct(service $service, sessions $sessions, echo_values &$echo){
        $echo->rows = $this->set($service, $sessions);
        $echo->custom_data = $_POST;
    }

    private function set(service $service, sessions $sessions) : array{
        return (array)$service->helper_1->set->update_order(
            user::post(post_keys::ORDER_ID),
            user::post(post_keys::ORDER_STATE),
            user::post(post_keys::REASON)
        );
    }
}
