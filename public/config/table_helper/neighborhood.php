<?php
namespace config\table_helper;

class neighborhood {
    const TABLE_NAME = "neighborhood",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        DISTRICT_ID = self::TABLE_NAME.".district_id",
        NEIGHBORHOOD = self::TABLE_NAME.".neighborhood",
        POST_CODE = self::TABLE_NAME.".post_code";
}