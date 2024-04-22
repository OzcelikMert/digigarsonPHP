<?php
namespace config\table_helper;

class salesman_info{
    const TABLE_NAME = "salesman_info",
    ALL = self::TABLE_NAME.".*",
    ID = self::TABLE_NAME.".id",
    NAME = self::TABLE_NAME.".name",
    IS_ADMIN = self::TABLE_NAME.".is_admin";
}