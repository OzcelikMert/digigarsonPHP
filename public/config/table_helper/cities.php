<?php
namespace config\table_helper;

class cities {
    const TABLE_NAME = "cities",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        CITY = self::TABLE_NAME.".city";
}