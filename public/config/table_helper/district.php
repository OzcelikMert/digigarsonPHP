<?php
namespace config\table_helper;

class district {
    const TABLE_NAME = "district",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        TOWN_ID = self::TABLE_NAME.".town_id",
        DISTRICT = self::TABLE_NAME.".district";
}