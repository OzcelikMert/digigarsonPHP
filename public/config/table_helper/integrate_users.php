<?php
namespace config\table_helper;

class integrate_users {
    const TABLE_NAME = "integrate_users",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        BRANCH_ID = self::TABLE_NAME.".branch_id",
        TYPE = self::TABLE_NAME.".type",
        USER_NAME = self::TABLE_NAME.".user_name",
        PASSWORD = self::TABLE_NAME.".password",
        IS_ACTIVE = self::TABLE_NAME.".is_active";
}