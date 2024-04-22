<?php
namespace config\table_helper;

class license_time_types {
    const TABLE_NAME = "license_time_types",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        NAME = self::TABLE_NAME.".name_";
}