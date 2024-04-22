<?php
namespace config\table_helper;

class order_payment_status_types {
    const TABLE_NAME = "order_payment_status_types",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        NAME = self::TABLE_NAME.".name_";
}