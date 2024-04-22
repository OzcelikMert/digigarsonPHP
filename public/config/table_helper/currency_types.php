<?php
namespace config\table_helper;

class currency_types {
    const TABLE_NAME = "currency_types",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        TYPE = self::TABLE_NAME.".type",
        NAME = self::TABLE_NAME.".name_";
}