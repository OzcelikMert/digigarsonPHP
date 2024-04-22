<?php
namespace integrations\companies\integrated\yemek_sepeti\php\helper\service_1;

use integrations\companies\integrated\yemek_sepeti\php\config\service;
use integrations\companies\integrated\yemek_sepeti\php\helper\service_1\helper\get;
use integrations\companies\integrated\yemek_sepeti\php\helper\service_1\helper\set;

class helper {
    private service $service;
    public get $get;
    public set $set;

    public function __construct(service $service){
        $this->service = $service;
        $this->get = new get($this->service);
        $this->set = new set($this->service);
    }
}