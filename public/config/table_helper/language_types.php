<?php
namespace config\table_helper;

class language_types {
    const TABLE_NAME = "language_types",
        ALL = self::TABLE_NAME.".*",
        ID = self::TABLE_NAME.".id",
        SEO_URL = self::TABLE_NAME.".seo_url",
        NAME = self::TABLE_NAME.".name_";
}