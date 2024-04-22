<?php
namespace config\sessions;

abstract class keys {
    const BRANCH_ID = "branch_id",
        BRANCH_ID_MAIN = "branch_id_main",
        BRANCH_MAIN_ID = "branch_main_id",
        LANGUAGE_ID = "language_id",
        LANGUAGE_TAG = "language_tag",
        CURRENCY = "currency",
        USER_ID_MANAGE = "manage_user_id",
        USER_ID = "user_id",
        USER_NAME = "user_name",
        BRANCH_NAME = "branch_name",
        BRANCHES = "branches",
        PERMISSION = "permission",
        BRANCHES_NAMES = "branches_names",
        IS_MAIN = "is_main",
        INTEGRATIONS = "integrations",
        TOKEN = "token",
        CALLER_ID_ACTIVE = "caller_id_active";

        public static function INTEGRATION_KEYS() : \config\sessions\integrations\keys {
            return (new \config\sessions\integrations\keys());
        }
}