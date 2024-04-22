<?php
namespace integrations\companies\integrated\yemek_sepeti\php\config;

use integrations\companies\integrated\yemek_sepeti\php\helper\service_1\helper;
use matrix_library\php\post_services\soap_service;

class service extends soap_service {
    /**
     * Selected value from service type.
     * @var int
     */
    public int $service_type;
    /**
     * Header username
     * @var string
     */
    private string $user_name;
    /**
     * Header password
     * @var string
     */
    private string $password;

    public helper $helper_1;

    public function __construct(int $service_type, string $user_name, string $password) {
        $this->service_type = $service_type;
        $this->user_name = $user_name;
        $this->password = $password;
        parent::__construct($this->check_url());
        $this->initialize();
        $this->helper_1 = new helper($this);
    }

    protected function initialize() : void {
        $this->check_header();
    }

    private function check_url() : string {
        return match ($this->service_type) {
            service_list::SERVICE_1 => "http://messaging.yemeksepeti.com/messagingwebservice/integration.asmx?WSDL"
        };
    }

    private function check_header(){
        switch ($this->service_type){
            case service_list::SERVICE_1:
                $this->set_header(
                    "http://tempuri.org/",
                    "AuthHeader",
                    array(
                        "UserName" => $this->user_name,
                        "Password" => $this->password
                    )
                );
                break;
        }
    }
}

