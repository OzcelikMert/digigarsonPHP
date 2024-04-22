<?php
namespace config\table_helper;

class permission_types {
    const TABLE_NAME = "permission_types",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        GROUP_ID = self::TABLE_NAME.".group_id",
        NAME = self::TABLE_NAME.".name_";
}