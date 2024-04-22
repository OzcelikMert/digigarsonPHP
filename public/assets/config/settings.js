let settings = (function () {

    function settings() {}

    settings.ajax_timeouts = {
        VERY_FAST: 2500,
        FAST: 5000,
        NORMAL: 10000,
        SLOW: 15000,
        VERY_SLOW: 30000
    };

    settings.error_codes = {
        SUCCESS: 0x0000,
        INCORRECT_DATA: 0x0001,
        EMPTY_VALUE: 0x0002,
        WRONG_VALUE: 0x0003,
        REGISTERED_VALUE: 0x0004,
        SQL_SYNTAX: 0x0005,
        NOT_FOUND: 0x0006,
        UPLOAD_ERROR: 0x0007,
        NOT_LOGGED_IN: 0x0008,
        NO_PERM: 0x0009,
        IP_BLOCK: 0x0010,
    };

     settings.paths = {
         static: {
            url: window.location.origin,
            folder_branch: `branches`,
            folder_user: `users`,
         },
         primary: {
             SCRIPT: "./assets/scripts/",
             CSS: "./assets/styles/",
             PHP: "functions/",
             PHP_SAME_PARTS: "sameparts/functions/",
             INTEGRATED_COMPANIES: (folder_name) => { return `/integrations/companies/integrated/${folder_name}/php/functions/`; }
         },
         image: {
             PRODUCT: function (branch_id) { return `${settings.paths.static.url}/images/${settings.paths.static.folder_branch}/${branch_id}/product/`; },
             BRANCH_LOGO: function (branch_id) { return `${settings.paths.static.url}/images/${settings.paths.static.folder_branch}/${branch_id}/logo/logo.webp`; },
             BRANCH_SLIDER: function (branch_id) { return `${settings.paths.static.url}/images/${settings.paths.static.folder_branch}/${branch_id}/slider/`; },
             USER_AVATAR: function (user_id) { return `${settings.paths.static.url}/images/${settings.paths.static.folder_user}/${user_id}/avatar/`; },
         }
     }

     settings.set_types = {
         INSERT: 0x0001,
         UPDATE: 0x0002,
         DELETE: 0x0003,
         SELECT: 0x0004
     }

    return settings;
})();