<?php
namespace config\table_helper;

class branch_user_permission_types {
    const TABLE_NAME = "branch_user_permission_types",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        NAME = self::TABLE_NAME.".name_";
}