<?php
namespace config\table_helper;

class branch_safe {
    const TABLE_NAME = "branch_safe",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        BRANCH_ID = self::TABLE_NAME.".branch_id",
        DATE_START = self::TABLE_NAME.".date_start",
        DATE_END = self::TABLE_NAME.".date_end",
        ACCOUNT_ID = self::TABLE_NAME.".account_id",
        COMMENT = self::TABLE_NAME.".comment";
}