<?php
namespace config\table_helper;

class integrate_payment_types {
    const TABLE_NAME = "integrate_payment_types",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        TYPE = self::TABLE_NAME.".type",
        TYPE_ID = self::TABLE_NAME.".type_id",
        TYPE_ID_INTEGRATE = self::TABLE_NAME.".type_id_integrate";
}