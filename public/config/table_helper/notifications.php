<?php
namespace config\table_helper;
        class notifications extends same_columns {
                const TABLE_NAME = "notifications",
                ID = self::TABLE_NAME.".id",
                BRANCH_ID = self::TABLE_NAME.".branch_id",
                NAME = self::TABLE_NAME.".name",
                COMMENT = self::TABLE_NAME.".comment",
                ACTIVE = self::TABLE_NAME.".active",
                IP = self::TABLE_NAME.".ip",
                DATE_TIME = self::TABLE_NAME.".date_time",
                IS_DELETE = self::TABLE_NAME.".is_delete";
}