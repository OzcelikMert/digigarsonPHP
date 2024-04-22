<?php
namespace config\table_helper;

class branch_mac_addresses {
    const TABLE_NAME = "branch_mac_addresses",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        BRANCH_ID = self::TABLE_NAME.".branch_id",
        ADDRESS = self::TABLE_NAME.".address",
        NAME = self::TABLE_NAME.".name";
}