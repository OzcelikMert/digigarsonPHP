<?php
namespace config\table_helper;

class branch_devices {
    const TABLE_NAME = "branch_devices",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        BRANCH_ID = self::TABLE_NAME.".branch_id",
        NAME = self::TABLE_NAME.".name",
        TYPE = self::TABLE_NAME.".type",
        TOKEN = self::TABLE_NAME.".token",
        SECURITY_CODE = self::TABLE_NAME.".security_code",
        IS_PRINT = self::TABLE_NAME.".is_print",
        IS_CONNECT = self::TABLE_NAME.".is_connect",
        CALLER_ID_ACTIVE = self::TABLE_NAME.".caller_id_active";
}