<?php
namespace config\table_helper;

class customer_users {
    const TABLE_NAME = "customer_users",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        NAME = self::TABLE_NAME.".name",
        CREATE_DATE = self::TABLE_NAME.".create_date",
        EMAIL = self::TABLE_NAME.".email",
        EMAIL_CONFIRM = self::TABLE_NAME.".email_confirm",
        PHONE = self::TABLE_NAME.".phone",
        PHONE_CONFIRM_CODE = self::TABLE_NAME.".phone_confirm_code",
        ADDRESS_ID = self::TABLE_NAME.".address_id",
        LANGUAGE_ID = self::TABLE_NAME.".language_id";
}