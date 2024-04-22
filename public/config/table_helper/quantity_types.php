<?php
namespace config\table_helper;

class quantity_types {
    const TABLE_NAME = "quantity_types",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        NAME = self::TABLE_NAME.".name_";
}