<?php
namespace config\table_helper;
class branch_surveys extends same_columns {
    const
        TABLE_NAME = "branch_surveys",
        ID = self::TABLE_NAME.".id",
        BRANCH_ID = self::TABLE_NAME.".branch_id",
        TYPE = self::TABLE_NAME.".type",
        IP = self::TABLE_NAME.".ip",
        IS_DELETE = self::TABLE_NAME.".is_delete",
        DATE_TIME = self::TABLE_NAME.".date_time",
        NAME = self::TABLE_NAME.".name_";
}