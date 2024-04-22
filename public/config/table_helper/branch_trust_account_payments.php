<?php
namespace config\table_helper;

class branch_trust_account_payments {
    const TABLE_NAME = "branch_trust_account_payments",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        BRANCH_ID = self::TABLE_NAME.".branch_id",
        TRUST_ACCOUNT_ID = self::TABLE_NAME.".trust_account_id",
        PAYMENT_ID = self::TABLE_NAME.".payment_id",
        DISCOUNT = self::TABLE_NAME.".discount",
        COMMENT = self::TABLE_NAME.".comment",
        IS_DELETE = self::TABLE_NAME.".is_delete";
}