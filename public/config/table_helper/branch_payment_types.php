<?php
namespace config\table_helper;

class branch_payment_types {
    const TABLE_NAME = "branch_payment_types",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        TYPE_ID = self::TABLE_NAME.".type_id",
        BRANCH_ID = self::TABLE_NAME.".branch_id",
        RANK = self::TABLE_NAME.".rank",
        ACTIVE = self::TABLE_NAME.".active",
        ACTIVE_TAKE_AWAY = self::TABLE_NAME.".active_take_away";
}