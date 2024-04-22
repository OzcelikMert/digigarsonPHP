<?php
namespace integrations\companies\integrated\sameparts\functions\get;

use config\sessions;
use config\type_tables_values\integrate_types;
use integrations\companies\integrated\yemek_sepeti\php\config\service_list;

class service {
    public static function get(sessions $sessions, float $type, int $service_list = 1) : mixed {
        return match ((int)$type) {
            integrate_types::YEMEK_SEPETI => new \integrations\companies\integrated\yemek_sepeti\php\config\service($service_list, $sessions->get->INTEGRATION(sessions::INTEGRATION_KEYS()::YEMEK_SEPETI)->user_name, $sessions->get->INTEGRATION(sessions::INTEGRATION_KEYS()::YEMEK_SEPETI)->password),
        };
    }
}