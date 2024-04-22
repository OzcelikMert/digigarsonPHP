<?php
namespace config\table_helper;
class customer_address {
    const TABLE_NAME = "customer_address",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        USER_ID = self::TABLE_NAME.".user_id",
        PHONE = self::TABLE_NAME.".phone",
        ADDRESS_TYPE = self::TABLE_NAME.".address_type",
        TITLE = self::TABLE_NAME.".title",
        CITY = self::TABLE_NAME.".city",
        TOWN = self::TABLE_NAME.".town",
        DISTRICT = self::TABLE_NAME.".district",
        NEIGHBORHOOD = self::TABLE_NAME.".neighborhood",
        STREET = self::TABLE_NAME.".street",
        ADDRESS_DESCRIPTION = self::TABLE_NAME.".address_description",
        APARTMENT_NUMBER = self::TABLE_NAME.".apartment_number",
        HOME_NUMBER = self::TABLE_NAME.".home_number",
        FLOOR = self::TABLE_NAME.".floor",
        TYPE = self::TABLE_NAME.".type";
}