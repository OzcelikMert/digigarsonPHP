<?php
namespace order_app\sameparts\functions\sessions;

abstract class keys {
    const TAG = "order_app_",
          VERIFY      = keys::TAG."verify",
          NAME        = keys::TAG."name",
          PHONE       = keys::TAG."phone",
          USER_ID     = keys::TAG."user_id",
          LANG_ID     = keys::TAG."lang_id",
          LANG_TAG     = keys::TAG."lang_tag",
          VERIFY_CODE = keys::TAG."verify_code",
          SELECT_BRANCH_ID = keys::TAG."select_branch_id",
          SELECT_BRANCH_TABLE_ID = keys::TAG."select_branch_table_id",
          SELECT_BRANCH_TABLE_TYPE = keys::TAG."select_branch_table_type";
}

