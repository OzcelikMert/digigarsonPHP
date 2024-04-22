<?php
namespace config\table_helper;

class branch_takeaway_address {
    const TABLE_NAME = "branch_takeaway_address",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        BRANCH_ID = self::TABLE_NAME.".branch_id",
        NEIGHBORHOOD_ID = self::TABLE_NAME.".neighborhood_id";
}