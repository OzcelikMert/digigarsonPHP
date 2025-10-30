let main = (function () {
  let page_name = "";
  main.data_list = {
    BRANCH_ID: 0,
    BRANCH_ID_MAIN: 0,
    CURRENCY: "",
    PRODUCTS: Array(),
    PRODUCT_CATEGORIES: Array(),
    PRODUCT_QUANTITY_TYPES: Array(),
    PRODUCT_OPTIONS: Array(),
    PRODUCT_OPTIONS_ITEMS: Array(),
    BRANCH_USERS: Array(),
    BRANCH_INFO: Array(),
    ORDERS: Array(),
    ORDER_PRODUCTS: Array(),
    ORDER_PRODUCT_OPTIONS: Array(),
    ORDER_TYPES: Array(),
    ORDER_STATUS_TYPES: Array(),
    TABLES: Array(),
    SECTIONS: Array(),
    SECTION_TYPES: Array(),
    PAYMENTS: Array(),
    PAYMENT_TYPES: Array(),
    BRANCH_PAYMENT_TYPES: Array(),
    BRANCH_CALLERS: Array(),
    BRANCH_WORK_TIMES: Array(),
    BRANCHES: Array(),
    ADDRESS: { CITY: Array(), TAKEAWAY: [], TAKEAWAY_NAMES: [] },
    SURVEY_TYPES: Array(),
    INTEGRATION_USERS: Array(),
    INTEGRATION_TYPES: Array(),
  };
  main.set_type_for_data_list = {
    BRANCH_RELATED_THINGS: 0x0001,
    TABLE_RELATED_THINGS: 0x0002,
    PAYMENT_TYPES_RELATED_THINGS: 0x0003,
    PRODUCT_RELATED_THINGS: 0x0004,
    ORDER_RELATED_THINGS: 0x0005,
    INTEGRATIONS_RELATED_THINGS: 0x0006,
    SURVEY_RELATED_THINGS: 0x0007,
  };
  main.get_type_for_product_related_things = {
    ALL: 0x0001,
    PRODUCT: 0x0002,
    CATEGORIES: 0x0003,
    QUANTITY_TYPES: 0x0004,
    OPTIONS: 0x0005,
    PRODUCT_LINKED_OPTIONS: 0x0006,
  };
  main.get_type_for_branch_related_things = {
    ALL: 0x0001,
    CURRENCY: 0x0002,
    BRANCH_ID: 0x0003,
    BRANCH_USERS: 0x0004,
    GET_CITIES: 0x0005,
    BRANCH_INFO: 0x0006,
    WORK_TIMES: 0x0007,
    TAKEAWAY_ADDRESS: 0x0008,
  };
  main.get_type_for_table_related_things = {
    ALL: 0x0001,
    TABLES: 0x0002,
    SECTIONS: 0x0003,
    SECTION_TYPES: 0x0004,
  };
  main.get_type_for_payment_types_related_things = {
    ALL: 0x0001,
    PAYMENT_TYPES: 0x0002,
    BRANCH_PAYMENT_TYPES: 0x0003,
  };
  main.get_type_for_order_related_things = {
    ALL: 0x0001,
    ORDERS: 0x0002,
  };
  main.get_type_for_integrate_related_things = {
    ALL: 0x0001,
    USERS: 0x0002,
    TYPES: 0x0003,
  };
  main.get_type_survey_related_things = {
    ALL: 0x0001,
    TYPE: 0x0002,
    CUSTOMER: 0x0003,
  };
  main.get_type_for_payments_related_things = {
    ALL: 0x0001,
    PAYMENTS: 0x0002,
    STATUS_TYPES: 0x0003,
  };

  function main() {
    initialize();
  }

  main.get_product_related_things = function (
    get_type = main.get_type_for_product_related_things.ALL,
    async = false
  ) {
    $.ajax({
      url: `${settings.paths.primary.PHP_SAME_PARTS}values/get_product_related_things.php`,
      type: "POST",
      data: { page_name: page_name, get_type: get_type },
      async: async,
      success: function (data) {
        // console.log(data);
        data = JSON.parse(data);
        console.log(data);
        set_data_list(data, main.set_type_for_data_list.PRODUCT_RELATED_THINGS);
      },
      timeout: settings.ajax_timeouts.NORMAL,
    });
  };

  main.get_branch_related_things = function (
    get_type = main.get_type_for_branch_related_things.ALL,
    async = false
  ) {
    $.ajax({
      url: `${settings.paths.primary.PHP_SAME_PARTS}values/get_branch_related_things.php`,
      type: "POST",
      data: { page_name: page_name, get_type: get_type },
      async: async,
      success: function (data) {
        //console.log(data);
        data = JSON.parse(data);
        //console.log(data);
        set_data_list(data, main.set_type_for_data_list.BRANCH_RELATED_THINGS);
      },
      timeout: settings.ajax_timeouts.NORMAL,
    });
  };

  main.get_table_related_things = function (
    get_type = main.get_type_for_table_related_things.ALL,
    async = false
  ) {
    $.ajax({
      url: `${settings.paths.primary.PHP_SAME_PARTS}values/get_table_related_things.php`,
      type: "POST",
      data: { page_name: page_name, get_type: get_type },
      async: async,
      success: function (data) {
        data = JSON.parse(data);
        //console.log(data);
        set_data_list(data, main.set_type_for_data_list.TABLE_RELATED_THINGS);
      },
      timeout: settings.ajax_timeouts.NORMAL,
    });
  };

  main.get_payment_types_related_things = function (
    get_type = main.get_type_for_payment_types_related_things.ALL,
    async = false
  ) {
    $.ajax({
      url: `${settings.paths.primary.PHP_SAME_PARTS}values/get_payment_types_related_things.php`,
      type: "POST",
      data: { page_name: page_name, get_type: get_type },
      async: async,
      success: function (data) {
        data = JSON.parse(data);
        //console.log(data);
        set_data_list(data, main.set_type_for_data_list.PAYMENT_TYPES_RELATED_THINGS);
      },
      timeout: settings.ajax_timeouts.NORMAL,
    });
  };

  main.get_payments_related_things = function (
    get_type = main.get_type_for_payments_related_things.ALL,
    async = false
  ) {
    $.ajax({
      url: `${settings.paths.primary.PHP_SAME_PARTS}values/get_payments_related_things.php`,
      type: "POST",
      data: { page_name: page_name, get_type: get_type },
      async: async,
      success: function (data) {
        data = JSON.parse(data);
        console.log(data);
        set_data_list(data, main.set_type_for_data_list.PAYMENTS_RELATED_THINGS);
      },
      timeout: settings.ajax_timeouts.NORMAL,
    });
  };

  main.get_order_related_things = function (
    get_type = main.get_type_for_order_related_things.ALL,
    safe_id = 0,
    async = false
  ) {
    $.ajax({
      url: `${settings.paths.primary.PHP_SAME_PARTS}values/get_order_related_things.php`,
      type: "POST",
      data: { page_name: page_name, get_type: get_type, safe_id: safe_id },
      async: async,
      success: function (data) {
        //console.log(data);
        data = JSON.parse(data);
        //console.log(data);
        set_data_list(data, main.set_type_for_data_list.ORDER_RELATED_THINGS);
      },
      timeout: settings.ajax_timeouts.NORMAL,
    });
  };

  main.get_integrate_related_things = function (
    get_type = main.get_type_for_integrate_related_things.ALL,
    safe_id = 0,
    async = false
  ) {
    $.ajax({
      url: `${settings.paths.primary.PHP_SAME_PARTS}values/get_integrations_related_things.php`,
      type: "POST",
      data: { page_name: page_name, get_type: get_type, safe_id: safe_id },
      async: async,
      success: function (data) {
        //console.log(data);
        data = JSON.parse(data);
        //console.log(data);
        set_data_list(data, main.set_type_for_data_list.INTEGRATIONS_RELATED_THINGS);
      },
      timeout: settings.ajax_timeouts.NORMAL,
    });
  };

  main.get_survey_related_things = function (
    get_type = main.get_type_survey_related_things.ALL,
    safe_id = 0,
    async = false
  ) {
    $.ajax({
      url: `${settings.paths.primary.PHP_SAME_PARTS}values/get_survey_related_things.php`,
      type: "POST",
      data: { page_name: page_name, get_type: get_type, safe_id: safe_id },
      async: async,
      success: function (data) {
        //console.log(data);
        data = JSON.parse(data);
        console.log(data);
        set_data_list(data, main.set_type_for_data_list.SURVEY_RELATED_THINGS);
      },
      timeout: settings.ajax_timeouts.NORMAL,
    });
  };

  function set_data_list(data, set_type) {
    switch (set_type) {
      case main.set_type_for_data_list.PRODUCT_RELATED_THINGS:
        if (variable.isset(() => data.rows.products.rows))
          main.data_list.PRODUCTS = data.rows.products.rows;
        if (variable.isset(() => data.rows.categories.rows))
          main.data_list.PRODUCT_CATEGORIES = data.rows.categories.rows;
        if (variable.isset(() => data.rows.quantity_types.rows))
          main.data_list.PRODUCT_QUANTITY_TYPES = data.rows.quantity_types.rows;
        if (variable.isset(() => data.rows.options.rows))
          main.data_list.PRODUCT_OPTIONS = data.rows.options.rows;
        if (variable.isset(() => data.rows.option_items.rows))
          main.data_list.PRODUCT_OPTIONS_ITEMS = data.rows.option_items.rows;
        if (variable.isset(() => data.rows.option_types.rows))
          main.data_list.OPTION_TYPES = data.rows.option_types.rows;
        if (variable.isset(() => data.rows.linked_options.rows))
          main.data_list.PRODUCT_LINKED_OPTIONS = data.rows.linked_options.rows;
        break;
      case main.set_type_for_data_list.BRANCH_RELATED_THINGS:
        if (variable.isset(() => data.rows.branch_id))
          main.data_list.BRANCH_ID = data.rows.branch_id;
        if (variable.isset(() => data.rows.currency)) main.data_list.CURRENCY = data.rows.currency;
        if (variable.isset(() => data.rows.city)) main.data_list.ADDRESS.CITY = data.rows.city;
        if (variable.isset(() => data.rows.takeaway_address))
          main.data_list.ADDRESS.TAKEAWAY = data.rows.takeaway_address;
        if (variable.isset(() => data.rows.takeaway_address_names))
          main.data_list.ADDRESS.TAKEAWAY_NAMES = data.rows.takeaway_address_names;
        if (variable.isset(() => data.rows.branch_info))
          main.data_list.BRANCH_INFO = data.rows.branch_info[0];
        if (variable.isset(() => data.rows.branch_users))
          main.data_list.BRANCH_USERS = data.rows.branch_users;
        if (variable.isset(() => data.rows.work_times))
          main.data_list.BRANCH_WORK_TIMES = data.rows.work_times;
        if (variable.isset(() => data.rows.branches)) main.data_list.BRANCHES = data.rows.branches;
        main.data_list.BRANCH_ID_MAIN = data.rows.branch_id_main;
        break;
      case main.set_type_for_data_list.TABLE_RELATED_THINGS:
        if (variable.isset(() => data.rows.tables.rows))
          main.data_list.TABLES = data.rows.tables.rows;
        if (variable.isset(() => data.rows.sections.rows))
          main.data_list.SECTIONS = data.rows.sections.rows;
        if (variable.isset(() => data.rows.section_types.rows))
          main.data_list.SECTION_TYPES = data.rows.section_types.rows;
        break;
      case main.set_type_for_data_list.PAYMENTS_RELATED_THINGS:
        if (variable.isset(() => data.rows.order_payments.rows))
          main.data_list.PAYMENTS = data.rows.order_payments.rows;
        if (variable.isset(() => data.rows.order_payment_status_types.rows))
          main.data_list.PAYMENT_STATUS_TYPES = data.rows.order_payment_status_types.rows;
        break;
      case main.set_type_for_data_list.PAYMENT_TYPES_RELATED_THINGS:
        if (variable.isset(() => data.rows.branch_payment_types.rows))
          main.data_list.BRANCH_PAYMENT_TYPES = data.rows.branch_payment_types.rows;
        if (variable.isset(() => data.rows.payment_types.rows))
          main.data_list.PAYMENT_TYPES = data.rows.payment_types.rows;
        break;
      case main.set_type_for_data_list.ORDER_RELATED_THINGS:
        if (variable.isset(() => data.rows.orders.rows))
          main.data_list.ORDERS = data.rows.orders.rows;
        if (variable.isset(() => data.rows.order_products.rows))
          main.data_list.ORDER_PRODUCTS = data.rows.order_products.rows;
        if (variable.isset(() => data.rows.order_product_options.rows))
          main.data_list.ORDER_PRODUCT_OPTIONS = data.rows.order_product_options.rows;
        if (variable.isset(() => data.rows.order_types.rows))
          main.data_list.ORDER_TYPES = data.rows.order_types.rows;
        if (variable.isset(() => data.rows.order_status_types.rows))
          main.data_list.ORDER_STATUS_TYPES = data.rows.order_status_types.rows;
        break;
      case main.set_type_for_data_list.INTEGRATIONS_RELATED_THINGS:
        if (variable.isset(() => data.rows.users.rows))
          main.data_list.INTEGRATION_USERS = data.rows.users.rows;
        if (variable.isset(() => data.rows.types.rows))
          main.data_list.INTEGRATION_TYPES = data.rows.types.rows;
        break;
      /* case main.set_type_for_data_list.SURVEY_RELATED_THINGS:
                if(variable.isset(()=> data.rows.survey_types.rows)) main.data_list.SURVEY_TYPES = data.rows.survey_types.rows;
                break;*/
    }
  }

  function initialize() {
    let self = this;

    function set_events() {}

    page_name = server.get_page_name();
    main.get_product_related_things();
    main.get_branch_related_things();
    main.get_table_related_things();
    main.get_payment_types_related_things();
    main.get_payments_related_things();
    main.get_order_related_things();
    main.get_integrate_related_things();
    main.get_survey_related_things();
    set_events();
    update_top_bar();
  }

  return main;
})();

function update_top_bar() {
  $(".e_company_title").html(
    array_list.find(main.data_list.BRANCHES, main.data_list.BRANCH_ID, "id").name
  );
}
$(function () {
  let _main = new main();
});
