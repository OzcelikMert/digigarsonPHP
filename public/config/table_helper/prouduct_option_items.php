<?php
namespace config\table_helper;

class prouduct_option_items {
    const TABLE_NAME = "prouduct_option_items",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        OPTION_ID = self::TABLE_NAME.".option_id",
        BRANCH_ID = self::TABLE_NAME.".branch_id",
        IS_DEFAULT = self::TABLE_NAME.".is_default",
        PRICE = self::TABLE_NAME.".price",
        QUANTITY = self::TABLE_NAME.".quantity",
        DATE = self::TABLE_NAME.".date",
        NAME = self::TABLE_NAME.".name_";
}