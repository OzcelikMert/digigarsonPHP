<?php
namespace config\table_helper;

class branch_manage_users {
    const TABLE_NAME = "branch_manage_users",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        BRANCH_ID = self::TABLE_NAME.".branch_id",
        NAME = self::TABLE_NAME.".name",
        PHONE = self::TABLE_NAME.".phone",
        EMAIL = self::TABLE_NAME.".email",
        PASSWORD = self::TABLE_NAME.".password",
        PERMISSIONS = self::TABLE_NAME.".permissions",
        LANGUAGE_ID = self::TABLE_NAME.".language_id",
        ACTIVE = self::TABLE_NAME.".active";
}