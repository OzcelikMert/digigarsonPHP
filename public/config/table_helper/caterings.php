<?php
namespace config\table_helper;

class caterings {
    const TABLE_NAME = "caterings",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        BRANCH_ID = self::TABLE_NAME.".branch_id",
        PRODUCT_ID = self::TABLE_NAME.".product_id",
        OWNER_ID = self::TABLE_NAME.".owner_id",
        QUESTION_ID = self::TABLE_NAME.".question_id",
        DATE = self::TABLE_NAME.".date";
}