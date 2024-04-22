<?php
namespace config\table_helper;

class branch_work_times {
    const TABLE_NAME = "branch_work_times",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        DAY_ID = self::TABLE_NAME.".day_id",
        START_TIME = self::TABLE_NAME.".start_time",
        STOP_TIME = self::TABLE_NAME.".stop_time",
        ACTIVE = self::TABLE_NAME.".active",
        BRANCH_ID = self::TABLE_NAME.".branch_id";
}