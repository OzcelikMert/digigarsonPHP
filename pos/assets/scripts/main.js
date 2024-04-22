$(document).ready( function () {
    let _main = new main();
})
let main = (function () {
    let page_name = "";
    main.data_list = {
        ADDRESS: {CITY:[]},
        BRANCH_ID: 0,
        CURRENCY: "",
        PERMISSION: Array(),
        PRODUCTS: Array(),
        PRODUCT_CATEGORIES: Array(),
        PRODUCT_QUANTITY_TYPES: Array(),
        PRODUCT_OPTIONS: Array(),
        PRODUCT_OPTIONS_ITEMS: Array(),
        TABLES: Array(),
        SECTIONS: Array(),
        SECTION_TYPES: Array(),
        ORDERS: Array(),
        ORDER_PRODUCTS: Array(),
        ORDER_PRODUCT_OPTIONS: Array(),
        ORDER_TYPES: Array(),
        ORDER_STATUS_TYPES: Array(),
        BRANCH_PAYMENT_TYPES: Array(),
        PAYMENT_TYPES: Array(),
        CATERING_OWNERS: Array(),
        CATERING_QUESTIONS: Array(),
        CATERINGS: Array(),
        PAYMENTS: Array(),
        PAYMENT_STATUS_TYPES: Array(),
        TRUST_ACCOUNTS: Array(),
        TRUST_ACCOUNT_PAYMENTS: Array(),
        CALLER_ID_ACTIVE: false,
        TRIGGER_PRODUCT_EDIT: false,
        PAYMENT_INVOICE_USER: true,
        COURIERS: Array(),
        BARCODE_PRODUCTS: Array(),
        PRODUCTS_INTEGRATE: Array(),
        PRODUCT_OPTIONS_INTEGRATE: Array(),
        ORDERS_INTEGRATE: Array(),
        NOTIFICATION_TYPES: Array(),
        NOTIFICATIONS: Array(),
        PAYMENT_TYPES_INTEGRATE: Array()
    };
    main.set_type_for_data_list = {
        PRODUCT_RELATED_THINGS: 0x0001,
        TABLE_RELATED_THINGS: 0x0002,
        ORDER_RELATED_THINGS: 0x0003,
        PAYMENT_TYPES_RELATED_THINGS: 0x0004,
        CATERING_RELATED_THINGS: 0x0005,
        PAYMENTS_RELATED_THINGS: 0x0006,
        BRANCH_TRUST_ACCOUNTS: 0x0007,
        INTEGRATE_RELATED_THINGS: 0x0008,
        NOTIFICATION_TYPES_RELATED_THINGS: 0x0009,
    }
    main.get_type_for_product_related_things = {
        ALL: 0x0001,
        PRODUCT: 0x0002,
        CATEGORIES: 0x0003,
        QUANTITY_TYPES: 0x0004,
        OPTIONS: 0x0005,
        PRODUCT_LINKED_OPTIONS: 0x0006,
    }
    main.get_type_for_table_related_things = {
        ALL: 0x0001,
        TABLES: 0x0002,
        SECTIONS: 0x0003,
        SECTION_TYPES: 0x0004
    }
    main.get_type_for_order_related_things = {
        ALL: 0x0001,
        ORDERS: 0x0002,
        ORDER_PRODUCTS: 0x0003,
        ORDER_TYPES: 0x0004,
        ORDER_STATUS_TYPES: 0x0005,
        ORDER_PRODUCTS_NOT_PRINTED: 0x0006,
        ORDER_PRODUCTS_PRINTED: 0x0007,
        ALL_WITH_ORDER_PRODUCTS_NOT_PRINTED: 0x0008,
        ALL_WITH_ORDER_PRODUCTS_PRINTED: 0x0009,
        ORDER_AND_ORDER_PRODUCTS: 0x0010,
        ORDER_PRODUCT_OPTIONS: 0x0011
    }
    main.get_type_for_payment_types_related_things = {
        ALL: 0x0001,
        BRANCH_PAYMENT_TYPES: 0x0002,
        PAYMENT_TYPES: 0x0003
    }
    main.get_type_for_catering_related_things = {
        ALL: 0x0001,
        CATERING_OWNERS: 0x0002,
        CATERING_QUESTIONS: 0x0003,
        CATERINGS: 0x0004
    }
    main.get_type_for_payments_related_things = {
        ALL: 0x0001,
        PAYMENTS: 0x0002,
        STATUS_TYPES: 0x0003
    }
    main.get_type_for_branch_trust_accounts_related_things = {
        ALL: 0x0001,
        ACCOUNTS: 0x0002,
        PAYMENTS: 0x0003
    }
    main.get_type_for_integrate_related_things = {
        ALL: 0x0001,
        PRODUCTS: 0x0002,
        OPTIONS: 0x0003,
        ORDERS: 0x0004,
        PAYMENT_TYPES: 0x0005
    }
    main.get_type_for_notification_related_things = {
        ALL: 0x0001,
        TYPES: 0x0002,
        NOTIFICATIONS: 0x0003,
    }
    function main() { initialize(); }

    main.get_payment_types_related_things = function(get_type = main.get_type_for_payment_types_related_things.ALL, async = false){
        $.ajax({
            url: `${settings.paths.primary.PHP_SAME_PARTS}values/get_payment_types_related_things.php`,
            type: "POST",
            data: {page_name: page_name, get_type: get_type},
            async: async,
            success: function (data) {
                data = JSON.parse(data);
                console.log(data);
                set_data_list(data, main.set_type_for_data_list.PAYMENT_TYPES_RELATED_THINGS)
            },timeout: settings.ajax_timeouts.NORMAL
        });
    }

    main.get_product_related_things = function(get_type = main.get_type_for_product_related_things.ALL, async = false){
        console.log(page_name);
        $.ajax({
            url: `${settings.paths.primary.PHP_SAME_PARTS}values/get_product_related_things.php`,
            type: "POST",
            data: {page_name: page_name, get_type: get_type},
            async: async,
            success: function (data) {
                data = JSON.parse(data);
                console.log(data);
                set_data_list(data, main.set_type_for_data_list.PRODUCT_RELATED_THINGS)
            },timeout: settings.ajax_timeouts.NORMAL
        });
    }

    main.get_table_related_things = function(get_type = main.get_type_for_table_related_things.ALL, async = false){
        $.ajax({
            url: `${settings.paths.primary.PHP_SAME_PARTS}values/get_table_related_things.php`,
            type: "POST",
            data: {page_name: page_name, get_type: get_type},
            async: async,
            success: function (data) {
                data = JSON.parse(data);
                console.log(data);
                set_data_list(data, main.set_type_for_data_list.TABLE_RELATED_THINGS)
            },timeout: settings.ajax_timeouts.NORMAL
        });
    }

    main.get_order_related_things = function(get_type = main.get_type_for_order_related_things.ALL, async = false){
        $.ajax({
            url: `${settings.paths.primary.PHP_SAME_PARTS}values/get_order_related_things.php`,
            type: "POST",
            data: {page_name: page_name, get_type: get_type},
            async: async,
            success: function (data) {
                data = JSON.parse(data);
                console.log(data);
                set_data_list(data, main.set_type_for_data_list.ORDER_RELATED_THINGS)
            },timeout: settings.ajax_timeouts.NORMAL
        });
    }

    main.get_catering_related_things = function(get_type = main.get_type_for_catering_related_things.ALL, async = false){
        $.ajax({
            url: `${settings.paths.primary.PHP_SAME_PARTS}values/get_catering_related_things.php`,
            type: "POST",
            data: {page_name: page_name, get_type: get_type},
            async: async,
            success: function (data) {
                data = JSON.parse(data);
                console.log(data);
                set_data_list(data, main.set_type_for_data_list.CATERING_RELATED_THINGS)
            },timeout: settings.ajax_timeouts.NORMAL
        });
    }

    main.get_payments_related_things = function(get_type = main.get_type_for_payments_related_things.ALL, async = false){
        $.ajax({
            url: `${settings.paths.primary.PHP_SAME_PARTS}values/get_payments_related_things.php`,
            type: "POST",
            data: {page_name: page_name, get_type: get_type},
            async: async,
            success: function (data) {
                data = JSON.parse(data);
                console.log(data);
                set_data_list(data, main.set_type_for_data_list.PAYMENTS_RELATED_THINGS)
            },timeout: settings.ajax_timeouts.NORMAL
        });
    }

    main.get_trust_accounts_related_things = function(get_type = main.get_type_for_branch_trust_accounts_related_things.ALL, async = false){
        $.ajax({
            url: `${settings.paths.primary.PHP_SAME_PARTS}values/get_branch_trust_accounts_related_things.php`,
            type: "POST",
            data: {page_name: page_name, get_type: get_type},
            async: async,
            success: function (data) {
                data = JSON.parse(data);
                console.log(data);
                set_data_list(data, main.set_type_for_data_list.BRANCH_TRUST_ACCOUNTS)
            },timeout: settings.ajax_timeouts.NORMAL
        });
    }

    main.get_integrate_related_things = function(get_type = main.get_type_for_integrate_related_things.ALL, async = false){
        $.ajax({
            url: `${settings.paths.primary.PHP_SAME_PARTS}values/get_integrations_related_things.php`,
            type: "POST",
            data: {page_name: page_name, get_type: get_type},
            async: async,
            success: function (data) {
                data = JSON.parse(data);
                console.log(data);
                set_data_list(data, main.set_type_for_data_list.INTEGRATE_RELATED_THINGS)
            },timeout: settings.ajax_timeouts.NORMAL
        });
    }

    main.get_notification_related_things = function(get_type = main.get_type_for_notification_related_things.ALL, async = false){
        $.ajax({
            url: `${settings.paths.primary.PHP_SAME_PARTS}values/get_notifications_related_things.php`,
            type: "POST",
            data: {get_type: get_type},
            async: async,
            success: function (data) {
                data = JSON.parse(data);
                // console.log(data);
                notifications.main.reload_data();
                set_data_list(data, main.set_type_for_data_list.NOTIFICATION_TYPES_RELATED_THINGS)
            },timeout: settings.ajax_timeouts.NORMAL
        });
    }

    function set_data_list(data, set_type){
        switch (set_type){
            case main.set_type_for_data_list.PRODUCT_RELATED_THINGS:
                if(variable.isset(()=> data.rows.products.rows)) main.data_list.PRODUCTS = data.rows.products.rows;
                if(variable.isset(()=> data.rows.categories.rows)) main.data_list.PRODUCT_CATEGORIES = data.rows.categories.rows;
                if(variable.isset(()=> data.rows.quantity_types.rows)) main.data_list.PRODUCT_QUANTITY_TYPES = data.rows.quantity_types.rows;
                if(variable.isset(()=> data.rows.options.rows)) main.data_list.PRODUCT_OPTIONS = data.rows.options.rows;
                if(variable.isset(()=> data.rows.option_items.rows)) main.data_list.PRODUCT_OPTIONS_ITEMS = data.rows.option_items.rows;
                if(variable.isset(()=> data.rows.option_types.rows)) main.data_list.OPTION_TYPES = data.rows.option_types.rows;
                if(variable.isset(()=> data.rows.linked_options.rows)) main.data_list.PRODUCT_LINKED_OPTIONS = data.rows.linked_options.rows;
                main.data_list.BRANCH_ID = data.rows.branch_id;
                main.data_list.CURRENCY = data.rows.currency;
                main.data_list.PERMISSION = data.rows.permissions;
                main.data_list.CALLER_ID_ACTIVE = data.rows.caller_id_active;
                break;
            case main.set_type_for_data_list.TABLE_RELATED_THINGS:
                if(variable.isset(()=> data.rows.tables.rows)) main.data_list.TABLES = data.rows.tables.rows;
                if(variable.isset(()=> data.rows.sections.rows)) main.data_list.SECTIONS = data.rows.sections.rows;
                if(variable.isset(()=> data.rows.section_types.rows)) main.data_list.SECTION_TYPES = data.rows.section_types.rows;
                break;
            case main.set_type_for_data_list.ORDER_RELATED_THINGS:
                if(variable.isset(()=> data.rows.orders.rows)) main.data_list.ORDERS = data.rows.orders.rows;
                if(variable.isset(()=> data.rows.order_products.rows)) main.data_list.ORDER_PRODUCTS = data.rows.order_products.rows;
                if(variable.isset(()=> data.rows.order_product_options.rows)) main.data_list.ORDER_PRODUCT_OPTIONS = data.rows.order_product_options.rows;
                if(variable.isset(()=> data.rows.order_types.rows)) main.data_list.ORDER_TYPES = data.rows.order_types.rows;
                if(variable.isset(()=> data.rows.order_status_types.rows)) main.data_list.ORDER_STATUS_TYPES = data.rows.order_status_types.rows;
                break;
            case main.set_type_for_data_list.PAYMENT_TYPES_RELATED_THINGS:
                if(variable.isset(()=> data.rows.branch_payment_types.rows)) main.data_list.BRANCH_PAYMENT_TYPES = data.rows.branch_payment_types.rows;
                if(variable.isset(()=> data.rows.payment_types.rows)) main.data_list.PAYMENT_TYPES = data.rows.payment_types.rows;
                break;
            case main.set_type_for_data_list.CATERING_RELATED_THINGS:
                if(variable.isset(()=> data.rows.catering_owners.rows)) main.data_list.CATERING_OWNERS = data.rows.catering_owners.rows;
                if(variable.isset(()=> data.rows.catering_questions.rows)) main.data_list.CATERING_QUESTIONS = data.rows.catering_questions.rows;
                if(variable.isset(()=> data.rows.caterings.rows)) main.data_list.CATERINGS = data.rows.caterings.rows;
                break;
            case main.set_type_for_data_list.PAYMENTS_RELATED_THINGS:
                if(variable.isset(()=> data.rows.order_payments.rows)) main.data_list.PAYMENTS = data.rows.order_payments.rows;
                if(variable.isset(()=> data.rows.order_payment_status_types.rows)) main.data_list.PAYMENT_STATUS_TYPES = data.rows.order_payment_status_types.rows;
                break;
            case main.set_type_for_data_list.BRANCH_TRUST_ACCOUNTS:
                if(variable.isset(()=> data.rows.accounts.rows)) main.data_list.TRUST_ACCOUNTS = data.rows.accounts.rows;
                if(variable.isset(()=> data.rows.payments.rows)) main.data_list.TRUST_ACCOUNT_PAYMENTS = data.rows.payments.rows;
                break;
            case main.set_type_for_data_list.INTEGRATE_RELATED_THINGS:
                if(variable.isset(()=> data.rows.products.rows)) main.data_list.PRODUCTS_INTEGRATE = data.rows.products.rows;
                if(variable.isset(()=> data.rows.options.rows)) main.data_list.PRODUCT_OPTIONS_INTEGRATE = data.rows.options.rows;
                if(variable.isset(()=> data.rows.orders.rows)) main.data_list.ORDERS_INTEGRATE = data.rows.orders.rows;
                if(variable.isset(()=> data.rows.payment_types.rows)) main.data_list.PAYMENT_TYPES_INTEGRATE = data.rows.payment_types.rows;
                break;
            case main.set_type_for_data_list.NOTIFICATION_TYPES_RELATED_THINGS:
                if(variable.isset(()=> data.rows.notification_types.rows)) main.data_list.NOTIFICATION_TYPES = data.rows.notification_types.rows;
                if(variable.isset(()=> data.rows.notifications.rows)) main.data_list.NOTIFICATIONS = data.rows.notifications.rows;
                break;

        }
    }

    function element_location_pop(type = 0){
        if (type === 0){
            $(".pop_page_container").fadeToggle();
        } else {
            $(".pop_page_container").fadeOut();
        }
    }

    function set_events(){
        $(".e_location_pop_button").click(function() {
            element_location_pop();
        });

        $(".pop_page_container").click(function() {
            element_location_pop("hide");
        });

        $('form').on('reset', function() {
            $("input[type='hidden']", $(this)).each(function() {
                let $t = $(this);
                $t.val($t.data('default-value'));
            });
        });
    }

    function initialize(){
        page_name = server.get_page_name();
        main.get_product_related_things();
        main.get_table_related_things();
        main.get_order_related_things();
        main.get_payment_types_related_things();
        main.get_catering_related_things();
        main.get_payments_related_things();
        main.get_trust_accounts_related_things();
        main.get_notification_related_things();
        main.get_integrate_related_things();
        set_events();
        start_timers();
    }

    main.send_main_data = function (){
        app.app_settings.send_main_data({
            ORDERS: main.data_list.ORDERS,
            ORDER_TYPES:    main.data_list.ORDER_TYPES,
            ORDER_PRODUCTS: main.data_list.ORDER_PRODUCTS,
            ORDER_PRODUCT_OPTIONS:  main.data_list.ORDER_STATUS_TYPES,
            ORDER_STATUS_TYPES: main.data_list.ORDER_STATUS_TYPES,

            PAYMENTS: main.data_list.PAYMENTS,
            PAYMENT_INVOICE_USER: main.data_list.PAYMENT_INVOICE_USER,
            PAYMENT_STATUS_TYPES: main.data_list.PAYMENT_STATUS_TYPES,
            PAYMENT_TYPES: main.data_list.PAYMENT_TYPES,
            TRUST_ACCOUNTS: main.data_list.TRUST_ACCOUNTS,

            PRODUCTS:   main.data_list.PRODUCTS,
            PRODUCT_CATEGORIES: main.data_list.PRODUCT_CATEGORIES,
            PRODUCT_LINKED_OPTIONS: main.data_list.PRODUCT_LINKED_OPTIONS,
            PRODUCT_OPTIONS:    main.data_list.PRODUCT_OPTIONS,
            PRODUCT_OPTIONS_ITEMS:main.data_list.PRODUCT_OPTIONS_ITEMS,
            PRODUCT_QUANTITY_TYPES:main.data_list.PRODUCT_QUANTITY_TYPES,
            OPTION_TYPES: main.data_list.OPTION_TYPES,

            SECTIONS: main.data_list.SECTIONS,
            SECTION_TYPES: main.data_list.SECTION_TYPES,
            TABLES: main.data_list.TABLES,
        },true)
    }

    let timers = {order: null, notification: null};
    function start_timers(){
        timers.order = setInterval(function (){
            main.get_order_related_things();
            app.app_settings.send_main_data({
                ORDER_PRODUCTS: main.data_list.ORDER_PRODUCTS,
                ORDER_PRODUCT_OPTIONS:  main.data_list.ORDER_STATUS_TYPES,
            });
        },settings.ajax_timeouts.NORMAL)
        setTimeout(function () {
            helper.log("start notification timer")
            timers.notification = setInterval(function (){
                if (app.settings.notifications.is_enable){
                    main.get_notification_related_things(main.get_type_for_notification_related_things.NOTIFICATIONS,true)
                } else clearInterval(timers.notification)
            },settings.ajax_timeouts.FAST)
        },5000)

    }

    return main;
})();

