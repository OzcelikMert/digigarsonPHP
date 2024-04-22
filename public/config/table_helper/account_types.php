<?php
namespace config\table_helper;

class account_types {
    const TABLE_NAME = "account_types",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        NAME = self::TABLE_NAME.".name_";
}