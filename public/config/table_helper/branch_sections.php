<?php
namespace config\table_helper;

class branch_sections {
    const TABLE_NAME = "branch_sections",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        BRANCH_ID = self::TABLE_NAME.".branch_id",
        SECTION_ID = self::TABLE_NAME.".section_id",
        RANK = self::TABLE_NAME.".rank",
        IS_ACTIVE = self::TABLE_NAME.".is_active";
}