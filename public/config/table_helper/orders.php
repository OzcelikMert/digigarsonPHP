<?php
namespace config\table_helper;

class orders {
    const TABLE_NAME = "orders",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        BRANCH_ID = self::TABLE_NAME.".branch_id",
        TABLE_ID = self::TABLE_NAME.".table_id",
        NO = self::TABLE_NAME.".no",
        TYPE = self::TABLE_NAME.".type",
        STATUS = self::TABLE_NAME.".status",
        DISCOUNT = self::TABLE_NAME.".discount",
        DATE_START = self::TABLE_NAME.".date_start",
        DATE_END = self::TABLE_NAME.".date_end",
        SAFE_ID = self::TABLE_NAME.".safe_id",
        COMMENT = self::TABLE_NAME.".comment",
        ADDRESS_ID = self::TABLE_NAME.".address_id",
        IS_CONFIRM = self::TABLE_NAME.".is_confirm",
        CONFIRMED_ACCOUNT_ID = self::TABLE_NAME.".confirmed_account_id",
        COURIER_ID = self::TABLE_NAME.".courier_id",
        IS_PRINT = self::TABLE_NAME.".is_print";
}