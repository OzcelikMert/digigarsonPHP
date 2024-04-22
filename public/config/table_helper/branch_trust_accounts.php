<?php
namespace config\table_helper;

class branch_trust_accounts {
    const TABLE_NAME = "branch_trust_accounts",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        BRANCH_ID = self::TABLE_NAME.".branch_id",
        NAME = self::TABLE_NAME.".name",
        ADDRESS = self::TABLE_NAME.".address",
        PHONE = self::TABLE_NAME.".phone",
        DISCOUNT = self::TABLE_NAME.".discount",
        TAX_NO = self::TABLE_NAME.".tax_no",
        TAX_ADMINISTRATION = self::TABLE_NAME.".tax_administration",
        IS_DELETE = self::TABLE_NAME.".is_delete";
}