<?php
namespace config\table_helper;

class branch_callers {
    const TABLE_NAME = "branch_callers",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        BRANCH_ID = self::TABLE_NAME.".branch_id",
        DEVICE_ID = self::TABLE_NAME.".device_id",
        PHONE     = self::TABLE_NAME.".phone",
        STATUS    = self::TABLE_NAME.".status";
}