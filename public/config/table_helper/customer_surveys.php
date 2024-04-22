<?php
namespace config\table_helper;
    class customer_surveys extends same_columns {
    const
        TABLE_NAME = "customer_surveys",
        ID = self::TABLE_NAME.".id",
        BRANCH_ID = self::TABLE_NAME.".branch_id",
        USER_ID = self::TABLE_NAME.".user_id",
        DATE_TIME = self::TABLE_NAME.".date_time",
        IP = self::TABLE_NAME.".ip",
        TYPE = self::TABLE_NAME.".type",
        VALUE = self::TABLE_NAME.".value";
}