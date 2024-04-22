<?php
namespace config\table_helper;

class print_types {
    const TABLE_NAME = "print_types",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        NAME = self::TABLE_NAME.".name_";
}