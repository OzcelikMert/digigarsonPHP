<?php
namespace config;
use matrix_library\php\db_helpers\mysql;
use mysqli;

class database_list {
    const LIVE_MYSQL_1 = 0x0001,
        BACKUP_MYSQL_1 = 0x0002,
        TURKEY_ADDRESS = 0x0003;
}

/**
 * Makes database connection.
 */
class db extends mysql {
    /**
     * Selected value from database list.
     * @var int
     */
    private int $database_type;

    public function __construct(int $database_type) {
        date_default_timezone_set('Asia/Istanbul');
        parent::__construct();
        $this->database_type = $database_type;
        $this->initialize();
    }

    /**
     * Runs the database connection.
     */
    protected function initialize() : void {
        $connect = $this->connection_database_type();
        $connect->query("SET NAMES 'utf8'");
        $connect->query("SET CHARACTER SET utf8");
        $connect->query("SET SESSION collation_connection = 'utf8_unicode_ci'");
        $this->connect = $connect;
    }

    /**
     * Call function if u want to see which one u choose database type.
     * @return int
     */
    function get_db_type() : int{
        return $this->database_type;
    }

    /**
     * Database connections are made here.
     * @return mysqli
     */
    private function connection_database_type(): mysqli {
        return match ($this->database_type) {
            database_list::LIVE_MYSQL_1 => mysqli_connect("45.144.154.89:3306", "test_pos_app", 'S6zKyjCQlIobBCVEly0XCG7tD5zyfT!T!7!dhYw5m7Euf@X@OcgGqen^8!m0H%9A', "test_pos_app"),
            database_list::TURKEY_ADDRESS => mysqli_connect("45.144.154.89:3306", "turkey_address", "ew37O716u&E1E3tzFIfu3gI^51gfeHtJ", "turkey_address"),
            database_list::BACKUP_MYSQL_1 => mysqli_connect("45.144.154.89:3306", "test_pos_app_backup", 'Q3A*Gt6OapTv#Vz916#udfQNmcaJL7JPVIH5s4rPPtTj*YypO^^lE2IAd99E8UJk', "test_pos_app_backup"),
        };
    }
}
