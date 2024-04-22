<?php
namespace config\table_helper;

class catering_questions {
    const TABLE_NAME = "catering_questions",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        BRANCH_ID = self::TABLE_NAME.".branch_id",
        COMMENT = self::TABLE_NAME.".comment",
        IS_DELETE = self::TABLE_NAME.".is_delete";
}