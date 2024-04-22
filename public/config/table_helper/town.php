<?php
namespace config\table_helper;

class town {
    const TABLE_NAME = "town",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        CITY_ID = self::TABLE_NAME.".city_id",
        TOWN = self::TABLE_NAME.".town";
}