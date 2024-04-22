<?php
namespace config\table_helper;

class product_categories {
    const TABLE_NAME = "product_categories",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        BRANCH_ID = self::TABLE_NAME.".branch_id",
        MAIN_ID = self::TABLE_NAME.".main_id",
        RANK = self::TABLE_NAME.".rank",
        NAME = self::TABLE_NAME.".name_",
        START_TIME = self::TABLE_NAME.".start_time",
        END_TIME = self::TABLE_NAME.".end_time",
        ACTIVE = self::TABLE_NAME.".active_table",
        ACTIVE_TAKE_AWAY = self::TABLE_NAME.".active_take_away",
        ACTIVE_SAFE = self::TABLE_NAME.".active_safe",
        ACTIVE_COME_TAKE = self::TABLE_NAME.".active_come_take",
        PRODUCT_ID = self::TABLE_NAME.".product_id",
        IS_DELETE = self::TABLE_NAME.".is_delete";
}