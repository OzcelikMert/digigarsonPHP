<?php
namespace config\table_helper;

class product_option_group_types {
    const TABLE_NAME = "product_option_group_types",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        NAME = self::TABLE_NAME.".name_";
}