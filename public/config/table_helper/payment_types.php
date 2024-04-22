<?php
namespace config\table_helper;

class payment_types {
    const TABLE_NAME = "payment_types",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        NAME = self::TABLE_NAME.".name_";
}