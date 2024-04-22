<?php
namespace config\table_helper;

class integrate_products {
    const TABLE_NAME = "integrate_products",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        BRANCH_ID = self::TABLE_NAME.".branch_id",
        TYPE = self::TABLE_NAME.".type",
        PRODUCT_ID_INTEGRATED = self::TABLE_NAME.".product_id_integrated",
        PRODUCT_ID = self::TABLE_NAME.".product_id";
}