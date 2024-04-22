<?php
namespace config\table_helper;

class permission_group_types {
    const TABLE_NAME = "permission_group_types",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        NAME = self::TABLE_NAME.".name_";
}