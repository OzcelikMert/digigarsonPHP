<?php
namespace config\table_helper;
        class send_notification extends same_columns {
                const TABLE_NAME = "send_notification",
                ID = self::TABLE_NAME.".id",
                TABLE_ID = self::TABLE_NAME.".table_id",
                BRANCH_ID = self::TABLE_NAME.".branch_id",
                NOTIFICATION_ID = self::TABLE_NAME.".notification_id",
                USER_ID = self::TABLE_NAME.".user_id",
                TYPE = self::TABLE_NAME.".type",
                IP = self::TABLE_NAME.".ip",
                DATE_TIME = self::TABLE_NAME.".date_time",
                IS_READ =  self::TABLE_NAME.".is_read";
}