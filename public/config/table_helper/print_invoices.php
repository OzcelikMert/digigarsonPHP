<?php
namespace config\table_helper;
        class print_invoices extends same_columns {
                const TABLE_NAME = "print_invoices",
                ID = self::TABLE_NAME.".id",
                BRANCH_ID = self::TABLE_NAME.".branch_id",
                DATA = self::TABLE_NAME.".data",
                IS_PRINT = self::TABLE_NAME.".is_print";
}