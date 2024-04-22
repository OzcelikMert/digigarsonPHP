<?php
namespace config\sessions;

use config\db;
use config\sessions;
use config\sessions\integrations\results;
use config\type_tables_values\integrate_types;
use sameparts\php\db_query\integrate;

class set{
    private sessions $sessions;

    public function __construct(sessions $sessions) {
        $this->sessions = $sessions;
    }

    public function INTEGRATION(string $key, results $results) {
        $this->sessions->get->INTEGRATION[$key] = serialize($results);
    }

    public function INTEGRATIONS(db $db){
        $integrations = integrate::get_users($db, $this->sessions->get->BRANCH_ID, show_password: true)->rows;
        if(count($integrations) > 0){
            foreach ($integrations as $integration){
                $key = match((int)$integration["type"]){
                    integrate_types::YEMEK_SEPETI => sessions::INTEGRATION_KEYS()::YEMEK_SEPETI,
                    integrate_types::GETIR => sessions::INTEGRATION_KEYS()::GETIR
                };
                $this->INTEGRATION($key, (new results($integration["user_name"], $integration["password"], $integration["is_active"])));
            }
        }else{
            $this->INTEGRATION(sessions::INTEGRATION_KEYS()::YEMEK_SEPETI, (new results("", "", false)));
            $this->INTEGRATION(sessions::INTEGRATION_KEYS()::GETIR, (new results("", "", false)));
        }
    }
}