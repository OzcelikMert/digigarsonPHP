<?php
namespace config\table_helper;

class catering_owners {
    const TABLE_NAME = "catering_owners",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        BRANCH_ID = self::TABLE_NAME.".branch_id",
        NAME = self::TABLE_NAME.".name",
        IS_DELETE = self::TABLE_NAME.".is_delete";
}