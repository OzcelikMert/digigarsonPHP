<?php
namespace config\table_helper;

class survey_types {
    const TABLE_NAME = "survey_types",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        NAME = self::TABLE_NAME.".name_";
}