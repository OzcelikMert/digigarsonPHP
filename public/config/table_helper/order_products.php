<?php
namespace config\table_helper;

class order_products {
    const TABLE_NAME = "order_products",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        BRANCH_ID = self::TABLE_NAME.".branch_id",
        ORDER_ID = self::TABLE_NAME.".order_id",
        ACCOUNT_ID = self::TABLE_NAME.".account_id",
        ACCOUNT_TYPE = self::TABLE_NAME.".account_type",
        PRODUCT_ID = self::TABLE_NAME.".product_id",
        PRICE = self::TABLE_NAME.".price",
        DISCOUNT = self::TABLE_NAME.".discount",
        QTY = self::TABLE_NAME.".qty",
        QUANTITY = self::TABLE_NAME.".quantity",
        PRINT = self::TABLE_NAME.".print",
        TIME = self::TABLE_NAME.".time",
        COMMENT = self::TABLE_NAME.".comment",
        STATUS = self::TABLE_NAME.".status",
        TYPE = self::TABLE_NAME.".type",
        PRICE_CHANGED = self::TABLE_NAME.".price_changed",
        VAT = self::TABLE_NAME.".vat";
}