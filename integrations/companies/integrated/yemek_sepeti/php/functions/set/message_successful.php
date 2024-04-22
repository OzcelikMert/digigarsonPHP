<?php
namespace integrations\companies\integrated\yemek_sepeti\php\functions\set;

use config\sessions;
use integrations\companies\integrated\yemek_sepeti\php\config\service;
use matrix_library\php\operations\user;
use sameparts\php\ajax\echo_values;

class post_keys {
    const MESSAGE_ID = "message_id";
}

class message_successful{
    function __construct(service $service, sessions $sessions, echo_values &$echo){
        $echo->rows = $this->set($service, $sessions);
    }

    private function set(service $service, sessions $sessions) : array{
        return (array)$service->helper_1->set->message_successful((string)user::post(post_keys::MESSAGE_ID));
    }
}
