<?php
namespace config\table_helper;

class integrate_orders {
    const TABLE_NAME = "integrate_orders",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        BRANCH_ID = self::TABLE_NAME.".branch_id",
        TYPE = self::TABLE_NAME.".type",
        ORDER_ID = self::TABLE_NAME.".order_id",
        ORDER_ID_INTEGRATE = self::TABLE_NAME.".order_id_integrate",
        SAFE_ID = self::TABLE_NAME.".safe_id",
        ADDRESS = self::TABLE_NAME.".address",
        INTEGRATE_CUSTOMER_ID = self::TABLE_NAME.".integrate_customer_id";
}