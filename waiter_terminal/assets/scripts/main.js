let main = (function () {
    let page_name = "";
    main.data_list = {
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
        APP_TABLE_SECTIONS: Array(),
        ORDERS: Array(),
        ORDER_PRODUCTS: Array(),
        ORDER_PRODUCT_OPTIONS: Array(),
        ORDER_TYPES: Array(),
        ORDER_STATUS_TYPES: Array(),
        CATERING_OWNERS: Array(),
        CATERING_QUESTIONS: Array(),
        CATERINGS: Array(),
        PAYMENTS: Array(),
        PAYMENT_STATUS_TYPES: Array(),
    };
    main.set_type_for_data_list = {
        PRODUCT_RELATED_THINGS: 0x0001,
        TABLE_RELATED_THINGS: 0x0002,
        ORDER_RELATED_THINGS: 0x0003,
        CATERING_RELATED_THINGS: 0x0004,
        PAYMENTS_RELATED_THINGS: 0x0005,
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

    function main() { initialize(); }

    main.get_product_related_things = function(get_type = main.get_type_for_product_related_things.ALL, async = false){
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
            case main.set_type_for_data_list.CATERING_RELATED_THINGS:
                if(variable.isset(()=> data.rows.catering_owners.rows)) main.data_list.CATERING_OWNERS = data.rows.catering_owners.rows;
                if(variable.isset(()=> data.rows.catering_questions.rows)) main.data_list.CATERING_QUESTIONS = data.rows.catering_questions.rows;
                if(variable.isset(()=> data.rows.caterings.rows)) main.data_list.CATERINGS = data.rows.caterings.rows;
                break;
            case main.set_type_for_data_list.PAYMENTS_RELATED_THINGS:
                if(variable.isset(()=> data.rows.order_payments.rows)) main.data_list.PAYMENTS = data.rows.order_payments.rows;
                if(variable.isset(()=> data.rows.order_payment_status_types.rows)) main.data_list.PAYMENT_STATUS_TYPES = data.rows.order_payment_status_types.rows;
                break;
        }
    }

    function initialize(){
        page_name = server.get_page_name();
        main.get_product_related_things();
        main.get_table_related_things();
        main.get_order_related_things();
        main.get_catering_related_things();
        main.get_payments_related_things();
        let application_table_sections = JSON.parse(application.db.table_sections.get());
        application_table_sections.forEach(sections => {
            main.data_list.APP_TABLE_SECTIONS.push(sections.section_id);
        });
        console.log("TABLE SECTIONS", main.data_list.APP_TABLE_SECTIONS);
        get_order_timer();
    }
    let order_timer = null;
    function get_order_timer(){
        order_timer = setInterval(function (){main.get_order_related_things();},settings.ajax_timeouts.NORMAL)
    }

    return main;
})();

$(function () {
    let _main = new main();
});
