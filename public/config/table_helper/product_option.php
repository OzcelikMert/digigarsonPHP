<?php
namespace config\table_helper;

class product_option {
    const TABLE_NAME = "product_option",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        BRANCH_ID = self::TABLE_NAME.".branch_id",
        SEARCH_NAME = self::TABLE_NAME.".search_name",
        SELECTION_LIMIT = self::TABLE_NAME.".selection_limit",
        TYPE = self::TABLE_NAME.".type",
        NAME = self::TABLE_NAME.".name_",
        DATE = self::TABLE_NAME.".date",
        IS_DELETED = self::TABLE_NAME.".is_deleted";
}