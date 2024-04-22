<?php
namespace config\table_helper;

class branch_tables {
    const TABLE_NAME = "branch_tables",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        BRANCH_ID = self::TABLE_NAME.".branch_id",
        TABLE_NO = self::TABLE_NAME.".no",
        NO = self::TABLE_NAME.".no",
        SECTION_ID = self::TABLE_NAME.".section_id",
        CREATE_DATE = self::TABLE_NAME.".create_date",
        TABLE_TYPE = self::TABLE_NAME.".type",
        TYPE = self::TABLE_NAME.".type",
        TABLE_SHAPE_TYPE = self::TABLE_NAME.".shape_type",
        IS_LOCK = self::TABLE_NAME.".is_lock",
        URL = self::TABLE_NAME.".url",
        IS_DELETE = self::TABLE_NAME.".is_delete";
}