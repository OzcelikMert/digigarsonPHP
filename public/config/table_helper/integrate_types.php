<?php
namespace config\table_helper;

class integrate_types {
    const TABLE_NAME = "integrate_types",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        NAME = self::TABLE_NAME.".name";
}