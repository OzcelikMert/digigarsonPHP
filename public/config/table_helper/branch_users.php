<?php
namespace config\table_helper;

class branch_users {
    const TABLE_NAME = "branch_users",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        BRANCH_ID = self::TABLE_NAME.".branch_id",
        NAME = self::TABLE_NAME.".name",
        ACTIVE = self::TABLE_NAME.".active",
        PASSWORD = self::TABLE_NAME.".password",
        PERMISSIONS = self::TABLE_NAME.".permissions",
        IS_DELETE = self::TABLE_NAME.".is_delete";
}