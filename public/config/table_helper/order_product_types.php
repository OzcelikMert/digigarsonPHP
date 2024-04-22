<?php
namespace config\table_helper;

class order_product_types {
    const TABLE_NAME = "order_product_types",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        NAME = self::TABLE_NAME.".name_";
}