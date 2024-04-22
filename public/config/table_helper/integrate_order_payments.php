<?php
namespace config\table_helper;

class integrate_order_payments {
    const TABLE_NAME = "integrate_order_payments",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        BRANCH_ID = self::TABLE_NAME.".branch_id",
        TYPE = self::TABLE_NAME.".type",
        INTEGRATE_ORDER_ID = self::TABLE_NAME.".integrate_order_id",
        INTEGRATE_TYPE_ID = self::TABLE_NAME.".integrate_type_id",
        SAFE_ID = self::TABLE_NAME.".safe_id";
}