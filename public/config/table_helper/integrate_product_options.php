<?php
namespace config\table_helper;

class integrate_product_options {
    const TABLE_NAME = "integrate_product_options",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        BRANCH_ID = self::TABLE_NAME.".branch_id",
        TYPE = self::TABLE_NAME.".type",
        OPTION_ID_INTEGRATED = self::TABLE_NAME.".option_id_integrated",
        OPTION_ID = self::TABLE_NAME.".option_id";
}