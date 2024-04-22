<?php
namespace config\table_helper;

class test
{
    const TABLE_NAME = "test",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        NAME = self::TABLE_NAME.".name",
        PASSWORD = self::TABLE_NAME.".password",
        COMMENT = self::TABLE_NAME.".comment",
        SEX = self::TABLE_NAME.".sex",
        AGE = self::TABLE_NAME.".age";
}