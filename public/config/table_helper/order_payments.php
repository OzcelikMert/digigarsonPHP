<?php
namespace config\table_helper;

class order_payments {
    const TABLE_NAME = "order_payments",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        BRANCH_ID = self::TABLE_NAME.".branch_id",
        ORDER_ID = self::TABLE_NAME.".order_id",
        TYPE = self::TABLE_NAME.".type",
        STATUS = self::TABLE_NAME.".status",
        PRICE = self::TABLE_NAME.".price",
        DATE = self::TABLE_NAME.".date",
        ACCOUNT_TYPE = self::TABLE_NAME.".account_type",
        ACCOUNT_ID = self::TABLE_NAME.".account_id",
        SAFE_ID = self::TABLE_NAME.".safe_id",
        COMMENT = self::TABLE_NAME.".comment",
        IS_DELETE = self::TABLE_NAME.".is_delete";
}