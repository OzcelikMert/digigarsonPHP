<?php
namespace config\table_helper;
class product_linked_options {
    const TABLE_NAME = "product_linked_options",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        BRANCH_ID = self::TABLE_NAME.".branch_id",
        PRODUCT_ID = self::TABLE_NAME.".product_id",
        OPTION_ID = self::TABLE_NAME.".option_id",
        DATE = self::TABLE_NAME.".date",
        MAX_COUNT = self::TABLE_NAME.".max_count";
}