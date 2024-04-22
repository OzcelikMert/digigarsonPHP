<?php
namespace config\table_helper;

class super_admin_user {

    const TABLE_NAME = "super_admin_user",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        NAME = self::TABLE_NAME.".name",
        PASSWORD = self::TABLE_NAME.".password",
        LAST_LOGIN = self::TABLE_NAME.".last_login";
}