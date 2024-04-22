<?php
namespace config\table_helper;

class table_section_types {
    const TABLE_NAME = "table_section_types",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        ACTIVE = self::TABLE_NAME.".active",
        NAME = self::TABLE_NAME.".name_";
}