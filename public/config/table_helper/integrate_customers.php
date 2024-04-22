<?php
namespace config\table_helper;

class integrate_customers {
    const TABLE_NAME = "integrate_customers",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        TYPE = self::TABLE_NAME.".type",
        ID_INTEGRATE = self::TABLE_NAME.".id_integrate",
        NAME = self::TABLE_NAME.".name";
}