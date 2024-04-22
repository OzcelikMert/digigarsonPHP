<?php
namespace config\table_helper;

class order_product_options {
    const TABLE_NAME = "order_product_options",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        BRANCH_ID = self::TABLE_NAME.".branch_id",
        ORDER_PRODUCT_ID = self::TABLE_NAME.".order_product_id",
        OPTION_ID = self::TABLE_NAME.".option_id",
        OPTION_ITEM_ID = self::TABLE_NAME.".option_item_id",
        PRICE = self::TABLE_NAME.".price",
        QTY = self::TABLE_NAME.".qty";
}