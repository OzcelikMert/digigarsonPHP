<?php
namespace integrations\companies\integrated\yemek_sepeti\php\functions\get;

use config\sessions;
use integrations\companies\integrated\yemek_sepeti\php\config\service;
use sameparts\php\ajax\echo_values;

class orders{
    function __construct(service $service, sessions $sessions, echo_values &$echo){
        $echo->rows = $this->get($service, $sessions);
    }

    private function get(service $service, sessions $sessions) : array{
        return $service->helper_1->get->messages()->rows;
    }
}
