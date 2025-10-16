<?php
namespace config\sessions\integrations;

use config\sessions;

class results {
    public string $user_name;
    public string $password;
    public bool $status;

    public function __construct($user_name, $password, $status = false){
        $this->user_name = $user_name;
        $this->password = $password;
        $this->status = $status;
    }
}