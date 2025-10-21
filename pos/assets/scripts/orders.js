let orders = (function () {
  let default_ajax_path = `${settings.paths.primary.PHP}orders/`;
  let default_ajax_path_finance = `${settings.paths.primary.PHP}finance/`;
  let set_types = {
    INSERT: 0x0001,
    TABLE_MOVE: 0x0002,
    ORDER_COMBINING: 0x0003,
    PAYMENT: 0x0004,
    CANCEL_AND_CATERING: 0x0005,
    CHANGE_PRICE: 0x0006,
    SEPARATE_PRODUCT: 0x0007,
    PAYMENT_TRUST_ACCOUNT: 0x0008,
    UPDATE_CONFIRM: 0x0010,
    UPDATE_CONFIRM_ACCOUNT_ID: 0x0011,
    UPDATE_IS_PRINT: 0x0012,
  };
  let variable_list = {
    NOW_DATE_TIME: null,
  };

  orders.check_caller = function () {
    if (typeof caller_id !== "undefined") {
      if (caller_id.variable_list.CALLER_ID !== 0) {
        if (caller_id.variable_list.SELECTED_ORDER_TYPE === caller_id.order_types.TAKE_AWAY) {
          $(`${table_list.class_list.TABLE}[table-takeaway-manual]`).trigger("click");
        }
      }
    }
  };
  function orders() {
    initialize();
  }
  function initialize() {
    main.get_table_related_things(main.get_type_for_table_related_things.ALL);
    main.get_order_related_things(main.get_type_for_order_related_things.ORDER_PRODUCTS);
    table_list.initialize();
    table_detail.initialize();
    sections.initialize();
    orders.check_caller();
    integrations.initialize();
  }

  function print_payment_invoice() {
    if (table_detail.variable_list.SELECTED_TABLE_ID === helper.db.branch_tables.SAFE) {
      let data = table_detail.get_order_product_data(
        table_detail.order_product_get_types.UNCONFIRMED
      );
      invoice.payment_receipt(data);
    } else if (table_detail.variable_list.SELECTED_TABLE_ID === helper.db.branch_tables.TAKE_AWAY) {
      invoice.payment_receipt_takeaway(
        table_detail.variable_list.SELECTED_ORDER_ID,
        table_detail.variable_list.SELECT_ADDRESS_STRING
      );
    } else {
      invoice.payment_receipt(null, table_detail.variable_list.SELECTED_TABLE_ID);
    }
    set(
      set_types.UPDATE_IS_PRINT,
      { order_id: table_detail.variable_list.SELECTED_ORDER_ID },
      function (data) {
        data = JSON.parse(data);
        main.get_order_related_things(main.get_type_for_order_related_things.ORDERS);
      }
    );
  }

  let table_list = {
    id_list: {
      TABLES: "#tables",
      TABLE_GROUP: "#table_group",
      MODAL_CALLER_CHOOSE: "#modal_caller_choose",
    },
    class_list: {
      TABLE: ".e_table",
      TABLE_MOVE: ".e_table_move",
      LAST_TIME: ".e_last_time",
      LAST_PRICE: ".e_last_price",
      TOTAL_PRICE: ".e_total_price",
      MODAL_CALLER_CHOOSE_BTN: ".e_modal_caller_choose_btn",
    },
    get_types: {
      TABLE: 0x0001,
      TABLE_MOVE: 0x0002,
    },
    variable_list: {
      SELECTED_GET_TYPE: 0x0001,
    },
    settings: {
      order_type: {
        0: "empty",
        1: "fill",
        2: "fill takeaway",
        5: "reserved",
        6: "fill yemek-sepeti",
      },
      type: ["", "", "round", "w2", "h2"],
    },
    get: function () {
      let self = this;

      function create_element() {
        let element = ``;
        variable_list.NOW_DATE_TIME = new Date(
          variable.date_format(new Date(), "yyyy-mm-dd HH:MM:00")
        );
        main.data_list.SECTIONS.forEach((section) => {
          if (
            section.is_active === 0 ||
            (sections.SELECTED_SECTION > 0 && sections.SELECTED_SECTION !== section.id)
          )
            return;
          let section_type = array_list.find(
            main.data_list.SECTION_TYPES,
            section.section_id,
            "id"
          );
          let tables = array_list.find_multi(main.data_list.TABLES, section.id, "section_id");
          tables.forEach((table) => {
            if (
              table.id !== helper.db.branch_tables.TAKE_AWAY &&
              table.id !== helper.db.branch_tables.YEMEK_SEPETI &&
              table.branch_id === 0
            )
              return;
            if (
              table.id === helper.db.branch_tables.TAKE_AWAY &&
              typeof main.data_list.PERMISSION[10] === "undefined"
            )
              return;
            let selected = "";
            if (
              self.variable_list.SELECTED_GET_TYPE === self.get_types.TABLE_MOVE &&
              table_detail.variable_list.SELECTED_TABLE_ID === table.id
            )
              selected = "selected";

            let bg = "";
            let order_type = 0;
            let payed_total = 0;
            let price = 0;
            let total = 0.0;
            let last_total = 0.0;
            let time = "";
            let last_time = "";
            let last_date = "";
            let table_no = table.no;
            let last_order_id = 0;

            let orders = array_list.find_multi(main.data_list.ORDERS, table.id, "table_id");

            function get_element() {
              total -= payed_total;
              if (last_time !== "") {
                let last_date_time = `${last_date} ${last_time}:00`;
                let date_time_diff = variable.diff_minutes(
                  new Date(last_date_time),
                  variable_list.NOW_DATE_TIME
                );
                let hour = Math.floor(date_time_diff / 60);
                let minute = date_time_diff - hour * 60;
                time = `${hour >= 10 ? hour : `0${hour}`}:${minute >= 10 ? minute : `0${minute}`}`;
                if (bg == "") {
                  if (minute > 20 && hour < 1) {
                    bg = "busy";
                  } else if (minute > 30 && hour < 1) {
                    bg = "busy-2";
                  } else if (minute > 40 || hour > 0) {
                    bg = "busy-3";
                  }
                }
              }
              element += `
                                <div table-type="${
                                  table.type
                                }" order-type="${order_type}" table-id="${
                table.id
              }" last-order-id="${last_order_id}" class="${
                self.variable_list.SELECTED_GET_TYPE === self.get_types.TABLE ||
                table_detail.variable_list.SELECTED_TABLE_ID === table.id
                  ? `e_table`
                  : `e_table_move`
              } order-table order-table-xl ${self.settings.type[table.shape_type]} p-0 ${selected}">
                                    <div class="in ${
                                      self.settings.order_type[order_type]
                                    } ${bg}">             
                                        <div class="tc-1 w-100 text-center fw-6 ">${table_no}</div>
                                        <div class="tc-2 w-100 size-type-lg fw-6 text-center">${
                                          section_type.name
                                        }</div>
                                        <div class="tc-3 w-100 size-type-lg fw-6 text-center">
                                           <p class="e_last_time mx-0 mb-0 d-block float-left">${time}</p>
                                           <p class="e_last_price mx-0 mb-0 d-block float-left">${
                                             last_total.toFixed(2) + main.data_list.CURRENCY
                                           }</p>
                                        </div>
                                        <div class="e_total_price tc-4 w-100 size-type-lg fw-6 text-center">${
                                          total.toFixed(2) + main.data_list.CURRENCY
                                        }</div>              
                                    </div>
                                </div>
                            `;
            }

            orders.forEach((order) => {
              if (order.confirmed_account_id == 0) bg = "order-unconfirmed";
              if (order.is_print == 1) bg = "order-printed";
              switch (table.id) {
                case helper.db.branch_tables.TAKE_AWAY:
                  table_no = order.no;
                  break;
                case helper.db.branch_tables.YEMEK_SEPETI:
                  table_no = array_list.find(
                    main.data_list.ORDERS_INTEGRATE,
                    order.id,
                    "order_id"
                  ).order_id_integrate;
                  break;
              }
              last_order_id = order.id;
              order_type = order.type;
              let order_products = array_list.find_multi(
                main.data_list.ORDER_PRODUCTS,
                order.id,
                "order_id"
              );
              order_products.forEach((order_product) => {
                price =
                  order_product.status === helper.db.order_product_status_types.CATERING
                    ? 0
                    : parseFloat(order_product.price);
                if (last_time <= order_product.time) {
                  if (last_time !== order_product.time) last_total = 0.0;
                  last_time = order_product.time;
                  last_date = order.date_start.slice(0, 10);
                  last_total += price;
                }
                total += price;
              });

              let payments = array_list.find_multi(main.data_list.PAYMENTS, order.id, "order_id");
              payments.forEach((payment) => {
                payed_total += parseFloat(payment.price);
              });

              if (
                table.id === helper.db.branch_tables.TAKE_AWAY ||
                table.id === helper.db.branch_tables.YEMEK_SEPETI
              ) {
                get_element();
                total = 0;
                payed_total = 0;
              }
            });

            if (
              table.id !== helper.db.branch_tables.TAKE_AWAY &&
              table.id !== helper.db.branch_tables.YEMEK_SEPETI
            ) {
              get_element();
            }
          });
        });

        return element;
      }

      if (sections.SELECTED_SECTION > 0) {
        $(integrations.yemek_sepeti.class_list.TABLE_GROUP).html("");
      }
      $(self.id_list.TABLE_GROUP).html(create_element());
    },
    initialize: function () {
      let self = this;
      function set_events() {
        $(self.id_list.MODAL_CALLER_CHOOSE).on("show.bs.modal", function () {
          if (typeof caller_id !== "undefined") caller_id.variable_list.CHECK = false;
        });

        $(self.id_list.MODAL_CALLER_CHOOSE).on("hide.bs.modal", function () {
          if (typeof caller_id !== "undefined") caller_id.variable_list.CHECK = true;
        });

        $(self.class_list.MODAL_CALLER_CHOOSE_BTN).on("click", function () {
          let function_name = $(this).attr("function");
          $(self.id_list.MODAL_CALLER_CHOOSE).modal("hide");

          switch (function_name) {
            case "search":
              if (typeof caller_id !== "undefined")
                caller_id._check(
                  $(`${self.id_list.MODAL_CALLER_CHOOSE} input[name='phone']`).val()
                );
              break;
            case "new":
              if (typeof caller_id !== "undefined") caller_id.modal_new_customer.get();
              break;
          }
        });

        $(document).on("click", `${self.class_list.TABLE}`, function () {
          let type = parseInt($(this).attr("table-type"));
          table_detail.variable_list.SELECTED_TABLE_ID = parseInt($(this).attr("table-id"));
          table_detail.variable_list.SELECTED_ORDER_ID = parseInt(
            $(this).attr("last-order-id") ?? 0
          );
          table_detail.variable_list.IS_SELECT_ORDER =
            table_detail.variable_list.SELECTED_ORDER_ID > 0;
          table_detail.variable_list.SELECTED_TABLE_TYPE =
            table_detail.variable_list.table_types.DEFAULT;
          table_detail.variable_list.ORDER_STATUS_TYPES = main.data_list.ORDER_STATUS_TYPES;

          $(table_detail.class_list.TABLE_TITLE_INFO).html("");
          $(`${table_detail.class_list.ORDER_BTN}[table-integrate]`).hide();
          $(`${table_detail.class_list.ORDER_BTN}[table]`).show();
          helper.log(table_detail.variable_list.SELECTED_ORDER_ID, "CHECK ORDER ID");
          switch (type) {
            case helper.db.branch_table_types.TABLE:
            case helper.db.branch_table_types.PERSON_SALE:
            case helper.db.branch_table_types.OTHER_SALE:
              let orders = array_list.find_multi(
                main.data_list.ORDERS,
                table_detail.variable_list.SELECTED_TABLE_ID,
                "table_id"
              );
              table_detail.variable_list.SELECTED_ORDER_ID =
                orders.length > 0 ? orders[orders.length - 1].id : 0;
              table_detail.variable_list.SELECTED_ORDER_TYPE = helper.db.order_types.TABLE;
              break;
            case helper.db.branch_table_types.SAFE:
              $(`${table_detail.class_list.ORDER_BTN}:not([table-safe])`).hide();
              table_detail.variable_list.SELECTED_ORDER_ID = 0;
              table_detail.variable_list.SELECTED_ORDER_TYPE = helper.db.order_types.SAFE;
              break;
            case helper.db.branch_table_types.TAKEAWAY:
              if (table_detail.variable_list.SELECTED_ORDER_ID > 0) {
                $(`${table_detail.class_list.ORDER_BTN}:not([table-take-away-confirmed])`).hide();
              } else {
                if (caller_id.variable_list.CALLER_ID === 0) {
                  $(self.id_list.MODAL_CALLER_CHOOSE).modal("show");
                  return;
                }
                $(`${table_detail.class_list.ORDER_BTN}:not([table-take-away])`).hide();
                $(table_detail.class_list.TABLE_TITLE_INFO).html(
                  `${caller_id.variable_list.DATA.user[0].name} (${caller_id.variable_list.DATA.user[0].phone})`
                );
                table_detail.variable_list.SELECTED_ORDER_ID = 0;
              }
              table_detail.variable_list.SELECTED_ORDER_TYPE = helper.db.order_types.TAKEAWAY;
              break;
            case helper.db.branch_table_types.YEMEK_SEPETI:
              table_detail.variable_list.ORDER_STATUS_TYPES =
                integrated_companies.yemek_sepeti.orders.variable_list.order_state.DATA;
              $(`${table_detail.class_list.ORDER_BTN}[table-integrate]`).show();
              $(`${table_detail.class_list.ORDER_BTN}:not([table-integrate])`).hide();
              integrations.variable_list.SELECTED_INTEGRATE_TYPE =
                helper.db.integrate_types.YEMEK_SEPETI;
              table_detail.variable_list.SELECTED_ORDER_TYPE = helper.db.order_types.YEMEK_SEPETI;
              break;
          }

          table_detail.get();
          table_detail.get_product();
          $(self.id_list.TABLES).hide();
          $(table_detail.id_list.TABLE_DETAILS).show();
          navbar.is_enable();
        });

        $(document).on("click", self.class_list.TABLE_MOVE, function () {
          let id = parseInt($(this).attr("table-id"));
          let old_table = array_list.find(
            main.data_list.TABLES,
            table_detail.variable_list.SELECTED_TABLE_ID,
            "id"
          );
          let old_section = array_list.find(
            main.data_list.SECTION_TYPES,
            array_list.find(main.data_list.SECTIONS, old_table.section_id, "id").section_id,
            "id"
          );
          let new_table = array_list.find(main.data_list.TABLES, id, "id");
          let new_section = array_list.find(
            main.data_list.SECTION_TYPES,
            array_list.find(main.data_list.SECTIONS, new_table.section_id, "id").section_id,
            "id"
          );
          Swal.fire({
            title: "Masa Taşıma",
            text: `'${old_section.name + " " + old_table.no}' isimli masayı '${
              new_section.name + " " + new_table.no
            }' isimli masaya taşımak istediğinizden emin misiniz?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Evet",
            cancelButtonText: "Hayır",
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
          }).then((result) => {
            if (result.value) {
              set(
                set_types.TABLE_MOVE,
                {
                  table_id: table_detail.variable_list.SELECTED_TABLE_ID,
                  table_move_id: id,
                  order_id: table_detail.variable_list.IS_SELECT_ORDER
                    ? table_detail.variable_list.SELECTED_ORDER_ID
                    : 0,
                },
                function (data) {
                  data = JSON.parse(data);
                  if (data.status) {
                    $(navbar.class_list.BACK_MOVE_TABLE).trigger("click");
                    main.get_order_related_things(
                      main.get_type_for_order_related_things.ORDER_AND_ORDER_PRODUCTS
                    );
                    helper_sweet_alert.success("İşlem Başarılı", "Masa başarı ile taşındı.");
                    self.get();
                    table_detail.back_detail(table_detail.back_detail_types.TABLE);
                  }
                }
              );
            }
          });
        });

        $(navbar.class_list.BACK_MOVE_TABLE).on("click", function () {
          $(
            `${self.id_list.TABLE_GROUP} ${self.class_list.TABLE}[table-id=${table_detail.variable_list.SELECTED_TABLE_ID}]`
          ).trigger("click");
        });
      }
      self.get();
      set_events();
      setInterval(function () {
        self.get();
      }, settings.ajax_timeouts.NORMAL);
    },
  };

  let sections = {
    id_list: {
      SECTIONS: "#selection-buttons",
    },
    SELECTED_SECTION: 0,
    get: function () {
      let self = this;

      function create_element() {
        let element = `<button class="btn btn-lg btn-s3 br size-type-lg m-2" section-id="0">HEPSI</button>`;

        main.data_list.SECTIONS.forEach((section) => {
          if (section.section_id !== 26 && section.branch_id === 0) return;
          // if(section.section_id === 26 && typeof main.data_list.PERMISSION[10] === "undefined") return;
          let bg = "btn-s3";
          // if(section.section_id === 26) bg = "btn-s2";
          if (section.section_id !== 26) {
            let section_type = array_list.find(
              main.data_list.SECTION_TYPES,
              section.section_id,
              "id"
            );
            element += `<button class="btn btn-lg ${bg} br size-type-lg m-2" section-id="${section.id}">${section_type.name}</button>`;
          }
        });

        return element;
      }

      $(self.id_list.SECTIONS).html(create_element());
    },
    initialize: function () {
      let self = this;

      function set_events() {
        $(document).on("click", `${self.id_list.SECTIONS} button`, function () {
          self.SELECTED_SECTION = parseInt($(this).attr("section-id"));
          table_list.get();
        });
      }

      self.get();
      set_events();
    },
  };

  let table_detail = {
    id_list: {
      TABLE_DETAILS: "#table_details",
      PRODUCT_LIST: "#product_list",
      ORDER_LIST: "#order_list",
      TABLE_ORDER_LIST: "#order_list_table",
      BACK_DETAIL: "#back_order_detail",
      MODAL_PRODUCT_DETAILS: "#modal_select_category",
      MODAL_PAYMENT_TYPES: "#modal_payment_types_fast",
      MODAL_DELETE_PRODUCT: "#modal_delete_product",
      MODAL_CATERING_PRODUCT: "#modal_catering_product",
      MODAL_CHANGE_PRICE: "#modal_change_price",
      MODAL_SEPARATE_PRODUCT: "#modal_separate_product",
      MODAL_TRUST_ACCOUNT: "#modal_trust_account",
      MODAL_NEW_TRUST_ACCOUNT: "#modal_new_trust_account",
      MODAL_DISCOUNT: "#modal_discount",
      MODAL_PAYMENT: "#modal_payment",
      MODAL_BARCODE_SYSTEM: "#modal_barcode_system",
      MODAL_ORDER_STATUS_TYPES: "#modal_order_status_types",
      FORM_MODAL_DELETE: "#modal_delete_form",
      FORM_MODAL_CATERING: "#modal_catering_form",
      FORM_MODAL_CHANGE_PRICE: "#modal_change_price_form",
      FORM_PRODUCT_OPTION: "#product_option_form",
      FORM_MODAL_SEPARATE_PRODUCT: "#modal_separate_product_form",
      FORM_MODAL_NEW_TRUST_ACCOUNT: "#form_trust_account",
      FORM_MODAL_DISCOUNT: "#modal_discount_form",
      SEARCH_PRODUCT: "#search_product",
      SEARCH_CATEGORY: "#search_category",
    },
    class_list: {
      ORDER_BTN: ".e_order_btn",
      ORDER_BODY: ".e_order_body",
      ORDER_TITLE: ".e_order_title",
      TABLE_TITLE: ".e_table_title",
      TABLE_TITLE_INFO: ".e_table_title_info",
      TABLE_PRICE_TOTAL: ".e_table_price_total",
      TABLE_HIDE_COLUMN: ".e_order_table_hide_column",
      PRODUCT: ".e_product",
      PRODUCT_SELECT: ".e_product_select",
      PRODUCT_ORDER_CONFIRMED: ".e_product_order_confirmed",
      PRODUCT_ORDER_UNCONFIRMED: ".e_product_order_unconfirmed",
      ORDER_TITLE_UNCONFIRMED: ".e_order_title_unconfirmed",
      PRODUCT_ORDER: ".e_product_order",
      BTN_QTY: ".e_btn_qty",
      BTN_QTY_SHOW: ".e_btn_qty_show",
      PRODUCT_DETAILS: ".e_product_details",
      QUANTITY: ".e_quantity",
      OPTIONS: ".e_options",
      OPTIONS_SAVE_BTN: ".e_options_save",
      PAYMENT_TYPES: ".e_payment_types_fast",
      PAYMENT_TYPES_CUSTOM: ".e_payment_types_fast_custom",
      PAYMENT_BTN: ".e_payment_fast_btn",
      DELETE_PRODUCTS: ".e_delete_products",
      CATERING_PRODUCTS: ".e_catering_products",
      CHANGE_PRICE_PRODUCTS: ".e_change_price_products",
      CHANGE_PRICE: ".e_change_price",
      SEPARATE_PRODUCT_PRODUCTS: ".e_separate_product_products",
      TRUST_ACCOUNTS: ".e_trust_accounts",
      SEARCH_TRUST_ACCOUNT: ".e_search_trust_account",
      NEW_TRUST_ACCOUNT: ".e_new_trust_account",
      PAYMENT_MODAL_PAYMENT_TYPES_CUSTOM: ".e_modal_payment_payment_types_custom",
      PAYMENT_MODAL_PAYMENT_PRODUCTS: ".e_modal_payment_products",
      PAYMENT_MODAL_PAYMENT_PRODUCTS_SELECTED: ".e_modal_payment_products_selected",
      PAYMENT_MODAL_PAYMENT_PAYMENTS: ".e_modal_payment_payments",
      PAYMENT_MODAL_PAYMENT_BTN: ".e_payment_btn",
      PAYMENT_MODAL_PRODUCT: ".e_modal_payment_product",
      PAYMENT_MODAL_PAYMENT_PRODUCT_SELECTED: ".e_modal_payment_product_selected",
      PAYMENT_MODAL_CALCULATOR: ".e_payment_calculator",
      PAYMENT_MODAL_CHANGE_PRICE: ".e_change_price",
      TAKE_AWAY_INFO: ".e_take_away_info",
      TAKE_AWAY_CANCEL_COMMENT: ".e_take_away_cancel_comment",
      CATEGORIES_LIST: ".e_categories_list",
      CATEGORY_BTN_FOR_PRODUCT: ".e_category_btn_for_product",
      BARCODE_INPUT: ".e_barcode_input",
      BARCODE_DETAILS: ".e_barcode_details",
      ORDER_STATUS_TYPES: ".e_order_status_types",
      BUTTON_ORDER_STATUS_TYPE: ".e_order_status_type_btn",
    },
    variable_list: {
      SELECTED_CATEGORY: 0,
      SELECTED_TABLE_ID: 0,
      SELECTED_ORDER_TYPE: 0,
      SELECTED_ORDER_ID: 0,
      SELECTED_PRODUCT_ID: 0,
      SELECTED_TRUST_ACCOUNT_ID: 0,
      SELECTED_PAYMENT_MODE: 0,
      IS_CUSTOM_PAYMENT_PRICE: false,
      IMAGE_ENABLE: true,
      IS_SELECT_ORDER: false,
      SELECT_ADDRESS_STRING: "",
      SEARCH_PRODUCT: "",
      SEARCH_CATEGORY: "",
      SELECTED_TABLE_TYPE: 0,
      ORDER_STATUS_TYPES: Array(),
      table_types: {
        DEFAULT: 1,
        INTEGRATION: 2,
      },
    },
    order_product_get_types: {
      ALL: 0x0001,
      CONFIRMED: 0x0002,
      UNCONFIRMED: 0x0003,
    },
    back_detail_types: {
      TABLE: 0x0001,
      TABLE_MOVE: 0x0002,
    },
    function_types: {
      DELETE: 0x0001,
      CATERING: 0x0002,
    },
    change_price_types: {
      NORMAL: 1,
      SAFE: 2,
      TAKE_AWAY: 3,
      COME_TAKE: 4,
      PERSONAL: 5,
      OTHER: 6,
      CUSTOM: 7,
    },
    search_types: {
      ID: 1,
      NAME: 2,
    },
    discount_types: {
      PRICE: 1,
      PERCENT: 2,
    },
    payment_modes: {
      FAST: 0x0001,
      NORMAL: 0x0002,
    },
    get_payment_get_types: {
      ALL: 0x0001,
      PAYMENTS: 0x0002,
      PRODUCTS: 0x0003,
      PAYMENT_TYPES: 0x0004,
      TOTALS: 0x0005,
    },
    get: function () {
      let self = this;

      let table = array_list.find(
        main.data_list.TABLES,
        table_detail.variable_list.SELECTED_TABLE_ID,
        "id"
      );
      let section = array_list.find(
        main.data_list.SECTION_TYPES,
        array_list.find(main.data_list.SECTIONS, table.section_id, "id").section_id,
        "id"
      );
      let table_total = 0.0;

      function create_element() {
        let element = ``;

        let orders = array_list.find_multi(
          main.data_list.ORDERS,
          self.variable_list.SELECTED_TABLE_ID === helper.db.branch_tables.TAKE_AWAY ||
            self.variable_list.SELECTED_TABLE_ID === helper.db.branch_tables.YEMEK_SEPETI
            ? self.variable_list.SELECTED_ORDER_ID
            : self.variable_list.SELECTED_TABLE_ID,
          self.variable_list.SELECTED_TABLE_ID === helper.db.branch_tables.TAKE_AWAY ||
            self.variable_list.SELECTED_TABLE_ID === helper.db.branch_tables.YEMEK_SEPETI
            ? "id"
            : "table_id"
        );

        if (
          self.variable_list.SELECTED_TABLE_ID === helper.db.branch_tables.TAKE_AWAY ||
          self.variable_list.SELECTED_TABLE_ID === helper.db.branch_tables.YEMEK_SEPETI
        ) {
          if (orders.length > 0) {
            switch (self.variable_list.SELECTED_TABLE_ID) {
              case helper.db.branch_tables.TAKE_AWAY:
                if (orders[0].is_confirm === 0) {
                  $(".e_takeaway_confirm_area").show();
                  $(".e_order_btn[function]").hide();
                } else {
                  $(".e_takeaway_confirm_area").hide();
                }

                let value = _page_address.get_user_address(orders[0].address_id).rows[0];
                if (typeof value !== "undefined") {
                  self.variable_list.SELECT_ADDRESS_STRING = `${value.neighborhood_name} ${value.street} | 
                               No:${value.home_number} Kat: ${value.floor} Daire: ${value.apartment_number}
                               ${value.district_name}/${value.city_name}
                               <br><b>Telefon:</b> ${value.phone} 
                               <br><b>Adres detayı</b>: ${value.address_description}`;
                } else {
                  self.variable_list.SELECT_ADDRESS_STRING = `ADRES BİLGİSİ ALINAMADI !`;
                }
                break;
              case helper.db.branch_tables.YEMEK_SEPETI:
                $(".e_takeaway_confirm_area").hide();
                let order_integrate = array_list.find(
                  main.data_list.ORDERS_INTEGRATE,
                  orders[0].id,
                  "order_id"
                );
                if (typeof order_integrate !== "undefined") {
                  self.variable_list.SELECT_ADDRESS_STRING = order_integrate.address;
                } else {
                  self.variable_list.SELECT_ADDRESS_STRING = `ADRES BİLGİSİ ALINAMADI !`;
                }
                break;
            }

            $(`${self.class_list.TAKE_AWAY_INFO} [function="address"]`).html(
              self.variable_list.SELECT_ADDRESS_STRING
            );
            $(`${self.class_list.TAKE_AWAY_INFO}`).show();
          } else {
            $(`${self.class_list.TAKE_AWAY_INFO}`).hide();
          }
        } else {
          $(`${self.class_list.TAKE_AWAY_INFO}`).hide();
        }

        orders.forEach((order) => {
          if (order.confirmed_account_id == 0)
            set(set_types.UPDATE_CONFIRM_ACCOUNT_ID, { order_id: order.id }, function (data) {
              data = JSON.parse(data);
              order.confirmed_account_id = data.rows.user_id;
            });
          let order_products = array_list.find_multi(
            main.data_list.ORDER_PRODUCTS,
            order.id,
            "order_id"
          );
          let order_product_elements = ``;
          let total = 0.0;
          if (order_products.length > 0) {
            order_products.forEach((order_product) => {
              let product = {};
              if (order_product.type == helper.db.order_product_types.DISCOUNT) {
                product = {
                  name: "Iskonto",
                };
              } else {
                product = array_list.find(main.data_list.PRODUCTS, order_product.product_id, "id");
              }
              let order_product_options = array_list.find_multi(
                main.data_list.ORDER_PRODUCT_OPTIONS,
                order_product.id,
                "order_product_id"
              );
              let price = parseFloat(order_product.price);
              let catering_class = "";
              let option_icon = "";
              if (
                order_product_options.length > 0 ||
                order_product.comment !== "" ||
                order_product.quantity != 1
              ) {
                option_icon = "<i class='fa fa-bars'></i>";
              }
              let isCatering =
                order_product.status === helper.db.order_product_status_types.CATERING;
              if (isCatering) {
                price = 0;
                catering_class = "bg-c5";
              }
              order_product_elements += `
                                <tr class="e_product_order e_product_order_confirmed ${catering_class}"
                                is-catering="${isCatering}"
                                order-product-id="${order_product.id}" 
                                product-id="${order_product.product_id}" 
                                product-name="${product.name}" 
                                quantity="${order_product.quantity}" 
                                price="${price}" 
                                vat="${order_product.vat}"
                                discount="${order_product.discount}" 
                                qty="${parseInt(order_product.qty)}" 
                                comment="${order_product.comment}" 
                                type="${order_product.type}" 
                                options='${JSON.stringify(order_product_options)}'>
                                    <td class="e_order_table_hide_column" style="display: none;"></td>
                                    <td>${parseInt(order_product.qty)}</td>
                                    <td function="name">${option_icon} ${product.name}</td>
                                    <td>${
                                      isCatering
                                        ? language.data.CATERING
                                        : price.toFixed(2) + main.data_list.CURRENCY
                                    }</td> 
                                </tr>
                            `;
              total += price;
            });

            let payments = array_list.find_multi(main.data_list.PAYMENTS, order.id, "order_id");

            let payed_total = 0;
            payments.forEach((payment) => {
              payed_total += parseFloat(payment.price);
            });

            table_total += total - payed_total;

            element += `
                            <tbody class="e_order_body" order-id="${order.id}" price="${
              total - payed_total
            }" style="display: contents;">
                                <tr class="e_order_title ${
                                  self.variable_list.SELECTED_ORDER_ID === order.id
                                    ? `selected`
                                    : ``
                                }" style="background: #0d4197">
                                    <td class="e_order_table_hide_column" style="display: none;"></td>
                                    <td colspan="3">
                                        <span>${order_products[0].account_type}: ${
              order_products[0].account_name
            } </span> 
                                        <span>No: ${order.no}</span> <span>Toplam: ${
              payed_total > 0 ? `<small><del class="mr-2">${total.toFixed(2)}</del></small>` : ``
            }<span function="price">${(total - payed_total).toFixed(2)}</span>${
              main.data_list.CURRENCY
            }</span>
                                    </td>
                                </tr>
                                ${order_product_elements}
                            </tbody>
                        `;
          }
        });

        return element;
      }

      $(self.id_list.ORDER_LIST).html(create_element());
      $(`${self.id_list.TABLE_DETAILS} ${self.class_list.TABLE_TITLE}`).html(
        `${section.name.toUpperCase()} ${table.no === 0 ? "" : table.no}`
      );
      $(
        `${self.id_list.TABLE_DETAILS} ${self.class_list.TABLE_PRICE_TOTAL} [function='price']`
      ).html(`${table_total.toFixed(2)}`);
      $(
        `${self.id_list.TABLE_DETAILS} ${self.class_list.TABLE_PRICE_TOTAL} [function='currency']`
      ).html(`${main.data_list.CURRENCY}`);
      $(`
            ${self.class_list.ORDER_BTN}[function='delete_product_cancel'],
            ${self.class_list.ORDER_BTN}[function='catering_product_cancel'],
            ${self.class_list.ORDER_BTN}[function='separate_product_cancel'],
            ${self.class_list.ORDER_BTN}[function='change_price_cancel']
            `).trigger("click");
      if ($(self.class_list.PRODUCT_ORDER_CONFIRMED).length < 1) {
        self.variable_list.SELECTED_ORDER_ID = 0;
      }
    },
    get_categories: function () {
      let self = this;

      function create_element() {
        let element =
          self.variable_list.SEARCH_CATEGORY.length === 0
            ? ` 
                        <div class="col-6 buttons category-btn"><button class="btn-s4 br" category-id="0">${language.data.FAVORITES}</button></div>
                        <div class="col-6 buttons category-btn"><button class="btn-s4 br" category-id="-1"><lang>${language.data.CAMPAIGNS}</lang></button></div>
                    `
            : ``;
        main.data_list.PRODUCT_CATEGORIES.forEach((category) => {
          if (category.main_id != 0) return;
          if (
            self.variable_list.SEARCH_CATEGORY.length > 0 &&
            !String(category.name.toLocaleLowerCase("tr")).match(
              new RegExp(self.variable_list.SEARCH_CATEGORY.toLocaleLowerCase("tr"), "gi")
            )
          )
            return;
          element += `<div class="col-6 buttons category-btn"><button class="btn-s4 br" category-id="${category.id}">${category.name}</button></div>`;
        });

        return element;
      }

      $(self.class_list.CATEGORIES_LIST).html(create_element());
    },
    get_product: function () {
      let self = this;

      function create_element() {
        let elements = {
          product: ``,
          category: ``,
        };

        if (self.variable_list.SELECTED_CATEGORY > 0) {
          let get_main = array_list.find(
            main.data_list.PRODUCT_CATEGORIES,
            self.variable_list.SELECTED_CATEGORY,
            "id"
          );
          let get_sub = array_list.find_multi(
            main.data_list.PRODUCT_CATEGORIES,
            self.variable_list.SELECTED_CATEGORY,
            "main_id"
          );

          if (get_main.main_id != 0) {
            elements.category += `
                            <div class="e_category_btn_for_product buttons category-btn" style="min-height: 100px;">
                                <button class="btn-s5 br h-100" category-id="${get_main.main_id}" style="font-size: 65px;"><i class="fa fa-arrow-left"></i><lang>BACK</lang></button>
                            </div>
                        `;
          }

          get_sub.forEach((category) => {
            if (
              self.variable_list.SEARCH_PRODUCT.length > 0 &&
              !String(category.name.toLocaleLowerCase("tr")).match(
                new RegExp(self.variable_list.SEARCH_PRODUCT.toLocaleLowerCase("tr"), "gi")
              )
            )
              return;
            elements.category += `
                            <div class="e_category_btn_for_product buttons category-btn" style="min-height: 100px;">
                                <button class="btn-s4 br h-100" category-id="${category.id}">${category.name}</button>
                            </div>
                        `;
          });
        }

        main.data_list.PRODUCTS.forEach((product) => {
          if (product.is_delete === 0) {
            if (self.variable_list.SEARCH_PRODUCT.length > 0) {
              if (
                !String(product.name.toLocaleLowerCase("tr")).match(
                  new RegExp(self.variable_list.SEARCH_PRODUCT.toLocaleLowerCase("tr"), "gi")
                )
              )
                return;
            } else {
              if (
                self.variable_list.SELECTED_CATEGORY > 0 &&
                self.variable_list.SELECTED_CATEGORY !== product.category_id
              ) {
                return;
              } else if (self.variable_list.SELECTED_CATEGORY === 0 && product.favorite === 0) {
                return;
              } else if (self.variable_list.SELECTED_CATEGORY === -1) {
                return;
              }
            }

            let price = product.price;
            if (self.variable_list.SELECTED_ORDER_TYPE === helper.db.order_types.SAFE)
              price = product.price_safe;
            else if (self.variable_list.SELECTED_ORDER_TYPE === helper.db.order_types.TAKEAWAY)
              price = product.price_take_away;
            elements.product += `
                           <div class="e_product p-1 product" product-id="${product.id}">
                                <div class="e_product_select text-center edit" function="edit"><i class="mdi mdi-pencil"></i></div>
                                <div class="e_product_select product-in btn-s6 p-1 pt-0 pb-1 br text-center" function="add">
                                    ${
                                      self.variable_list.IMAGE_ENABLE === true
                                        ? `<div class="image"> 
                                                <img draggable="false" class="br" src="${
                                                  server.is_valid_url(product.image)
                                                    ? product.image
                                                    : settings.paths.image.PRODUCT(
                                                        main.data_list.BRANCH_ID
                                                      ) + product.image
                                                }" alt="${product.name}" /> 
                                           </div>`
                                        : ""
                                    }
                                    <div class="title fw-6"> <p class="text-c6 bg-none p-0 m-0 btn-title">${
                                      product.name
                                    }</p> </div>
                                    <div class="price fw-6 text-c3"> ${
                                      price.toFixed(2) + main.data_list.CURRENCY
                                    } </div>
                                </div>
                           </div>
                        `;
          }
        });

        return `${elements.category} ${elements.product}`;
      }

      $(self.id_list.PRODUCT_LIST).html(create_element());
    },
    get_order_product_data: function (get_type) {
      let self = this;
      let caller_data = {};
      if (typeof caller_id !== "undefined") {
        caller_data = {
          caller_id: caller_id.variable_list.CALLER_ID,
          address_id: caller_id.variable_list.SELECTED_ADDRESS_ID,
          customer_id: caller_id.variable_list.CUSTOMER_ID,
        };
      }
      let data = Object.assign(
        {
          products: Array(),
          table_id: self.variable_list.SELECTED_TABLE_ID,
          order_id: self.variable_list.SELECTED_ORDER_ID,
          orders: [
            {
              table_id: self.variable_list.SELECTED_TABLE_ID,
              no: "",
            },
          ],
          discount: 0,
          type: self.variable_list.SELECTED_ORDER_TYPE,
          status: helper.db.order_status_types.GETTING_READY,
        },
        caller_data
      );

      switch (get_type) {
        case self.order_product_get_types.ALL:
          break;
        case self.order_product_get_types.CONFIRMED:
          break;
        case self.order_product_get_types.UNCONFIRMED:
          let elements = Array.from($(self.class_list.PRODUCT_ORDER_UNCONFIRMED));

          elements.forEach((element) => {
            element = $(element);
            data.products.push({
              id: parseInt(element.attr("product-id")),
              quantity: parseFloat(element.attr("quantity")),
              price: parseFloat(element.attr("price")),
              vat: parseFloat(element.attr("vat")),
              discount: parseFloat(element.attr("discount")),
              qty: parseInt(element.attr("qty")),
              comment: element.attr("comment"),
              type: helper.db.order_product_types.PRODUCT,
              options: JSON.parse(element.attr("options")),
            });
          });
          break;
      }
      return data;
    },
    get_payment_types: function () {
      let self = this;

      function create_element() {
        let element = ``;

        main.data_list.BRANCH_PAYMENT_TYPES.forEach((branch_payment_type) => {
          let payment_type = array_list.find(
            main.data_list.PAYMENT_TYPES,
            branch_payment_type.type_id,
            "id"
          );
          element += `
                        <button class="e_payment_fast_btn col-4 btn btn-dark" type-id="${payment_type.id}">${payment_type.name}</button>
                    `;
        });

        return element;
      }

      $(self.class_list.PAYMENT_TYPES_CUSTOM).html(create_element());
    },
    get_order_status_types: function () {
      let self = this;

      function create_element() {
        let element = ``;

        self.variable_list.ORDER_STATUS_TYPES.forEach((data) => {
          element += `
                        <button class="e_order_status_type_btn col-md-12 btn btn-dark mt-3" type-id="${data.id}">${data.name}</button>
                    `;
        });

        return element;
      }

      $(self.class_list.ORDER_STATUS_TYPES).html(create_element());
    },
    get_payment: function (get_type) {
      let self = this;

      function create_element_payments() {
        let element = ``;

        let payments = array_list.find_multi(
          main.data_list.PAYMENTS,
          self.variable_list.SELECTED_ORDER_ID,
          "order_id"
        );
        payments.forEach((payment) => {
          let payment_type = array_list.find(main.data_list.PAYMENT_TYPES, payment.type, "id");
          element += `
                        <tr>
                            <td>${payment_type.name}</td>
                            <td>${payment.price + main.data_list.CURRENCY}</td>
                        </tr>
                    `;
        });

        return element;
      }

      function create_element_products() {
        let element = ``;

        let selected_products = Array.from(
          $(self.class_list.PAYMENT_MODAL_PAYMENT_PRODUCT_SELECTED)
        );
        let orders = array_list.find_multi(
          main.data_list.ORDERS,
          self.variable_list.SELECTED_ORDER_ID,
          "id"
        );
        orders.forEach((order) => {
          let order_products = array_list.find_multi(
            main.data_list.ORDER_PRODUCTS,
            order.id,
            "order_id"
          );
          if (order_products.length > 0) {
            order_products.forEach((order_product) => {
              let is_available = false;
              selected_products.forEach((selected_product) => {
                if (is_available) return;
                selected_product = $(selected_product);
                if (selected_product.attr("order-product-id") == order_product.id)
                  is_available = true;
              });
              if (is_available) return;

              let product = {};
              if (order_product.type == helper.db.order_product_types.DISCOUNT) {
                product = {
                  name: "Iskonto",
                };
              } else {
                product = array_list.find(main.data_list.PRODUCTS, order_product.product_id, "id");
              }
              let order_product_options = array_list.find_multi(
                main.data_list.ORDER_PRODUCT_OPTIONS,
                order_product.id,
                "order_product_id"
              );
              let price = parseFloat(order_product.price);
              let option_icon = "";
              if (
                order_product_options.length > 0 ||
                order_product.comment !== "" ||
                order_product.quantity != 1
              ) {
                option_icon = "<i class='fa fa-bars'></i>";
              }
              if (order_product.status === helper.db.order_product_status_types.CATERING) {
                price = 0;
              }
              element += `
                                <tr class="e_modal_payment_product" 
                                order-product-id="${order_product.id}" 
                                product-id="${order_product.product_id}" 
                                product-name="${product.name}" 
                                quantity="${order_product.quantity}" 
                                price="${price}" 
                                vat="${order_product.vat}"
                                discount="${order_product.discount}" 
                                qty="${parseInt(order_product.qty)}" 
                                comment="${order_product.comment}" 
                                type="${order_product.type}" 
                                options='${JSON.stringify(order_product_options)}'>
                                    <td>${parseInt(order_product.qty)}</td>
                                    <td function="name">${option_icon} ${product.name}</td>
                                    <td>${price.toFixed(2) + main.data_list.CURRENCY}</td> 
                                </tr>
                            `;
            });
          }
        });

        return element;
      }

      function create_element_payment_types() {
        let element = ``;

        main.data_list.BRANCH_PAYMENT_TYPES.forEach((branch_payment_type) => {
          let payment_type = array_list.find(
            main.data_list.PAYMENT_TYPES,
            branch_payment_type.type_id,
            "id"
          );
          element += `
                        <button class="e_payment_btn btn btn-dark w-100 p-4 mt-2" type-id="${payment_type.id}">${payment_type.name}</button>
                    `;
        });

        return element;
      }

      switch (get_type) {
        case self.get_payment_get_types.PAYMENTS:
          $(self.class_list.PAYMENT_MODAL_PAYMENT_PAYMENTS).html(create_element_payments());
          break;
        case self.get_payment_get_types.PRODUCTS:
          $(self.class_list.PAYMENT_MODAL_PAYMENT_PRODUCTS).html(create_element_products());
          break;
        case self.get_payment_get_types.PAYMENT_TYPES:
          $(self.class_list.PAYMENT_MODAL_PAYMENT_TYPES_CUSTOM).html(
            create_element_payment_types()
          );
          break;
        case self.get_payment_get_types.ALL:
          self.get_payment(self.get_payment_get_types.PAYMENT_TYPES);
          self.get_payment(self.get_payment_get_types.PAYMENTS);
          self.get_payment(self.get_payment_get_types.PRODUCTS);
          self.get_payment(self.get_payment_get_types.TOTALS);
          break;
        case self.get_payment_get_types.TOTALS:
          $(`
                        ${self.id_list.MODAL_PAYMENT} [function='total_price'] [function='price'], 
                        ${self.id_list.MODAL_PAYMENT} [function='payment_price'] [function='price']
                    `).html(
            $(
              `${self.id_list.ORDER_LIST} ${self.class_list.ORDER_BODY}[order-id=${self.variable_list.SELECTED_ORDER_ID}] [function='price']`
            ).html()
          );
          $(`
                        ${self.id_list.MODAL_PAYMENT} [function='total_price'] [function='currency'], 
                        ${self.id_list.MODAL_PAYMENT} [function='payment_price'] [function='currency']
                    `).html(main.data_list.CURRENCY);
          break;
      }
    },
    get_trust_accounts: function (search_type = 0, search = "") {
      let self = this;

      function create_element() {
        let element = ``;

        main.data_list.TRUST_ACCOUNTS.forEach((account) => {
          if (search.length > 0) {
            let search_key = search_type === self.search_types.ID ? "id" : "name";
            if (!String(account[search_key]).match(new RegExp(search, "gi"))) {
              return;
            }
          }
          element += `
                        <tr account-id="${account.id}">
                            <td>${account.id}</td>
                            <td>${account.name}</td>
                            <td>${account.discount}</td>
                        </tr>
                    `;
        });

        return element;
      }

      $(self.class_list.TRUST_ACCOUNTS).html(create_element());
    },
    back_detail: function (type = this.back_detail_types.TABLE) {
      let self = this;

      if (type === self.back_detail_types.TABLE_MOVE) {
        $(navbar.class_list.SECTION_RIGHT).hide();
        $(navbar.class_list.BACK_MOVE_TABLE).show();
        table_list.variable_list.SELECTED_GET_TYPE = table_list.get_types.TABLE_MOVE;
      } else if (type === self.back_detail_types.TABLE) {
        $(navbar.class_list.SECTION_RIGHT).show();
        $(navbar.class_list.BACK_MOVE_TABLE).hide();
        table_list.variable_list.SELECTED_GET_TYPE = table_list.get_types.TABLE;
        self.variable_list.SELECTED_ORDER_ID = 0;
        self.variable_list.SELECTED_TABLE_ID = 0;
        self.variable_list.SELECTED_TABLE_TYPE = 0;
        if (typeof caller_id !== "undefined") {
          caller_id.variable_list.DATA = Array();
          caller_id.variable_list.CALLER_ID = 0;
          caller_id.variable_list.CUSTOMER_ID = 0;
          caller_id.variable_list.SELECTED_ORDER_TYPE = 0;
          caller_id.variable_list.SELECTED_ADDRESS_ID = 0;
        }
      }

      table_list.get();
      $(table_list.id_list.TABLES).show();
      $(self.id_list.TABLE_DETAILS).hide();
      navbar.is_enable();
    },
    order_combining: function () {
      let self = this;

      let order = array_list.find(
        main.data_list.ORDERS,
        self.variable_list.SELECTED_ORDER_ID,
        "id"
      );

      Swal.fire({
        title: language.data.ADDITON_COMBINATION,
        text: `${language.data.ALL_ADDITION}'${order.no}'${language.data.ADDITION_QUESTION}`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: language.data.ACCEPT,
        cancelButtonText: language.data.DECLINE,
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false,
      }).then((result) => {
        if (result.value) {
          set(
            set_types.ORDER_COMBINING,
            {
              table_id: self.variable_list.SELECTED_TABLE_ID,
              order_id: self.variable_list.SELECTED_ORDER_ID,
            },
            function (data) {
              data = JSON.parse(data);
              if (data.status) {
                main.get_order_related_things(
                  main.get_type_for_order_related_things.ORDER_AND_ORDER_PRODUCTS
                );
                helper_sweet_alert.success(
                  language.data.PROCESS_SUCCESS_TITLE,
                  language.data.ADDITION_UPDATED
                );
                self.get();
              }
            }
          );
        }
      });
    },
    order_create_element: function (id, options) {
      let self = this;
      let product = array_list.find(main.data_list.PRODUCTS, id, "id");
      let column_price = "price";
      let column_vat = "vat";
      switch (self.variable_list.SELECTED_ORDER_TYPE) {
        case helper.db.order_types.SAFE:
          column_price += "_safe";
          column_vat += "_safe";
          break;
        case helper.db.order_types.TAKEAWAY:
          column_price += "_take_away";
          column_vat += "_take_away";
          break;
      }
      let qty = $(self.class_list.BTN_QTY_SHOW).html();
      qty = qty === "-" ? "1" : qty;
      qty = parseInt(qty);
      let price = parseFloat(product[column_price]) * qty + parseFloat(options.price);
      price = price * parseFloat(options.quantity);
      let price_element = $(
        `${self.class_list.ORDER_TITLE}[order-id='${self.variable_list.SELECTED_ORDER_ID}'] [function='price']`
      );
      let price_total_element = $(`${self.class_list.TABLE_PRICE_TOTAL} [function='price']`);

      price_element.html((parseFloat(price_element.html()) + price).toFixed(2));
      price_total_element.html((parseFloat(price_total_element.html()) + price).toFixed(2));

      let option_icon = "";
      if (options.options.length > 0 || options.comment !== "" || options.quantity != 1) {
        option_icon = "<i class='fa fa-bars'></i>";
      }
      $(self.id_list.SEARCH_PRODUCT).val("").trigger("change");
      return `
                ${
                  $(self.class_list.PRODUCT_ORDER_UNCONFIRMED).length < 1 &&
                  self.variable_list.SELECTED_ORDER_ID === 0
                    ? `
                    <tr class="e_order_title e_order_title_unconfirmed selected" order-id="0" style="background: #0d4197">
                        <td class="e_order_table_hide_column" style="display: none;"></td>
                        <td colspan="3"><span>Yeni Sipariş</span> 
                            <span>Toplam: <span function="price">${price.toFixed(2)}</span>${
                        main.data_list.CURRENCY
                      }</span>
                        </td>
                    </tr>
                `
                    : ``
                }
                <tr class="e_product_order e_product_order_unconfirmed unconfirmed" 
                    row="${$(self.class_list.PRODUCT_ORDER_UNCONFIRMED).length + 1}"
                    product-id="${id}" 
                    quantity="${options.quantity}" 
                    price="${price}" 
                    vat="${product[column_vat]}"
                    comment="${options.comment}"
                    discount="0" 
                    qty="${qty}" 
                    options='${JSON.stringify(options.options)}'>
                    <td class="e_order_table_hide_column" style="display: none;"></td>
                    <td>
                        <button function='delete' class="btn btn-danger btn-xs"><i class="fa fa-trash-alt"></i></button> ${qty}
                    </td>
                    <td function="name">${option_icon} ${product.name}</td>
                    <td><span function="price" class="text-c5">${price.toFixed(2)}</span>${
        main.data_list.CURRENCY
      }</td> 
                </tr>
            `;
    },
    order_list_scroll_down: function () {
      let self = this;
      $(".order-table-in .list").scrollTop(99999);
      $(`${self.class_list.BTN_QTY}[function='clear']`).trigger("click");
    },
    add_order_product: function (product = { quantity: 1, qty: 1 }) {
      let self = this;
      function get_data() {
        return {
          id: product.id,
          quantity: product.quantity,
          comment: "",
          options: Array(),
          price: 0,
          qty: product.qty,
        };
      }

      let selected_order_id =
        self.variable_list.SELECTED_ORDER_ID > 0
          ? ` ${self.class_list.ORDER_BODY + `[order-id=${self.variable_list.SELECTED_ORDER_ID}]`}`
          : "";

      $(self.id_list.ORDER_LIST + selected_order_id).append(
        self.order_create_element(self.variable_list.SELECTED_PRODUCT_ID, get_data())
      );
      self.order_list_scroll_down();
    },
    initialize: function () {
      let self = this;

      function set_events() {
        $(self.id_list.SEARCH_PRODUCT).on("keyup change", function () {
          self.variable_list.SEARCH_PRODUCT = $(this).val();
          self.get_product();
        });

        $(self.id_list.SEARCH_CATEGORY).on("keyup change", function () {
          self.variable_list.SEARCH_CATEGORY = $(this).val();
          self.get_categories();
        });

        //Category Click Btn
        $(document).on(
          "click",
          `${self.class_list.CATEGORIES_LIST} button, ${self.class_list.CATEGORY_BTN_FOR_PRODUCT} button`,
          function () {
            let element = $(this);
            self.variable_list.SELECTED_CATEGORY = parseInt(element.attr("category-id"));
            self.get_product();
          }
        );

        $(document).on(
          "click",
          `${self.id_list.PRODUCT_LIST} ${self.class_list.PRODUCT} ${self.class_list.PRODUCT_SELECT}`,
          function () {
            let element = $(this);
            let function_name = element.attr("function");
            element = element.closest(self.class_list.PRODUCT);

            let id = parseInt(element.attr("product-id"));

            switch (function_name) {
              case "add":
                if (
                  app.customize_settings.triggerProductOptionModal &&
                  (typeof array_list.find(
                    main.data_list.PRODUCT_LINKED_OPTIONS,
                    id,
                    "product_id"
                  ) !== "undefined" ||
                    array_list.find(main.data_list.PRODUCTS, id, "id").quantity_id != 1)
                ) {
                  $(this).prev().trigger("click");
                  return;
                }
                function get_data() {
                  let data = {
                    quantity: 1,
                    comment: "",
                    options: Array(),
                    price: 0.0,
                  };

                  let qty = $(self.class_list.BTN_QTY_SHOW).html();
                  qty = qty === "-" ? "1" : qty;
                  qty = parseInt(qty);

                  let linked_options = array_list.find_multi(
                    main.data_list.PRODUCT_LINKED_OPTIONS,
                    id,
                    "product_id"
                  );
                  linked_options.forEach((linked_option) => {
                    let options = array_list.find_multi(
                      main.data_list.PRODUCT_OPTIONS,
                      linked_option.option_id,
                      "id"
                    );
                    options.forEach((option) => {
                      let option_id = option.id;
                      let option_items = array_list.find_multi(
                        main.data_list.PRODUCT_OPTIONS_ITEMS,
                        option_id,
                        "option_id"
                      );
                      option_items = array_list.find_multi(option_items, 1, "is_default");
                      option_items.forEach((option_item) => {
                        let price = parseFloat(option_item.price) * qty;
                        let item_id = option_item.id;

                        data.options.push({
                          id: 0,
                          option_id: option_id,
                          option_item_id: item_id,
                          order_id: self.variable_list.SELECTED_ORDER_ID,
                          order_product_id: 0,
                          price: price,
                          qty: qty,
                        });

                        data.price += price;
                      });
                    });
                  });

                  return data;
                }

                let selected_order_id =
                  self.variable_list.SELECTED_ORDER_ID > 0
                    ? ` ${
                        self.class_list.ORDER_BODY +
                        `[order-id=${self.variable_list.SELECTED_ORDER_ID}]`
                      }`
                    : "";

                $(self.id_list.ORDER_LIST + selected_order_id).append(
                  self.order_create_element(id, get_data())
                );
                self.order_list_scroll_down();
                break;
              case "edit":
                self.variable_list.SELECTED_PRODUCT_ID = id;
                $(self.id_list.MODAL_PRODUCT_DETAILS).modal();
                break;
            }
          }
        );

        $(self.id_list.MODAL_PRODUCT_DETAILS).on("show.bs.modal", function () {
          function create_element() {
            let elements = {
              options: ``,
              quantity: ``,
            };

            let linked_options = array_list.find_multi(
              main.data_list.PRODUCT_LINKED_OPTIONS,
              self.variable_list.SELECTED_PRODUCT_ID,
              "product_id"
            );
            linked_options.forEach((linked_option) => {
              let options = array_list.find_multi(
                main.data_list.PRODUCT_OPTIONS,
                linked_option.option_id,
                "id"
              );
              options.forEach((option) => {
                let option_items = array_list.find_multi(
                  main.data_list.PRODUCT_OPTIONS_ITEMS,
                  option.id,
                  "option_id"
                );
                elements.options += `
                                    <div class="option" option-type="${
                                      option.type
                                    }" option-limit="${linked_option.max_count}" option-id="${
                  option.id
                }">
                                        <p class="mb-1 pt-1">${option.name} ${
                  linked_option.max_count > 0
                    ? `<b>Limit: <span class="e_option_count">${linked_option.max_count}</span></b>`
                    : ``
                }</p>
                                `;
                option_items.forEach((option_item) => {
                  elements.options += `
                                        <button type="button" class="btn bg-c1 my-2 ${
                                          option_item.is_default == 1 ? `selected` : ``
                                        }" item-id="${option_item.id}" price="${
                    option_item.price
                  }">${option_item.name} ${
                    option_item.price != 0
                      ? `[` + (option_item.price > 0 ? `+` : ``) + `${option_item.price}]`
                      : ``
                  }</button>
                                    `;
                });
                elements.options += `
                                    </div>
                                `;
              });
            });

            let product = array_list.find(
              main.data_list.PRODUCTS,
              self.variable_list.SELECTED_PRODUCT_ID,
              "id"
            );
            let quantity = array_list.find(
              main.data_list.PRODUCT_QUANTITY_TYPES,
              product.quantity_id,
              "id"
            );

            elements.quantity =
              quantity.id > 1
                ? `
                            <h3 class="mb-1">${quantity.name}</h3>
                            <input type="number" value="1" name="quantity" min="0.00" step="0.01" placeholder="0.00" class="form-input mb-2" required>
                            <div class="quantity-btn pb-2">
                                <button type="button" class="btn bg-c1 w-100" quantity="0.5">0.5</button>
                                <button type="button" class="btn bg-c1 w-100" quantity="1">1</button>
                                <button type="button" class="btn bg-c1 w-100" quantity="1.5">1.5</button>
                                <button type="button" class="btn bg-c1 w-100" quantity="2">2</button>
                            </div>
                        `
                : ``;

            return elements;
          }

          $(self.id_list.FORM_PRODUCT_OPTION).trigger("reset");
          let elements = create_element();
          $(self.class_list.QUANTITY).html(elements.quantity);
          $(self.class_list.OPTIONS).html(elements.options);
        });

        $(document).on("click", `${self.class_list.QUANTITY} button`, function () {
          let quantity = parseFloat($(this).attr("quantity"));
          $(`${self.class_list.QUANTITY} input[name='quantity']`).val(quantity);
        });

        $(document).on("click", `${self.class_list.OPTIONS} button`, function () {
          let element = $(this);
          let closest = element.closest("[option-type]");
          let element_selected = closest.children("button.selected");
          let type = parseInt(closest.attr("option-type"));
          let option_id = parseInt(closest.attr("option-id"));
          let limit = parseInt(closest.attr("option-limit"));
          limit = limit === 0 ? 9999 : limit;
          let select_id = parseInt(element.attr("item-id"));
          let price = parseFloat(element.attr("price"));
          let selected = true;

          switch (type) {
            case helper.db.product_option_group_types.SINGLE_SELECT:
              element_selected.removeClass("selected");
              break;

            case helper.db.product_option_group_types.MULTI_SELECT:
              selected = element.hasClass("selected") || element_selected.length < limit;
              break;
          }

          if (selected) {
            element.hasClass("selected")
              ? element.removeClass("selected")
              : element.addClass("selected");
          }
        });

        $(document).on("submit", self.id_list.FORM_PRODUCT_OPTION, function (e) {
          e.preventDefault();
          let element = $(this);
          function get_data() {
            let data = {
              quantity: 1,
              comment: "",
              options: Array(),
              price: 0.0,
            };
            data = Object.assign(data, element.serializeObject());

            let qty = $(self.class_list.BTN_QTY_SHOW).html();
            qty = qty === "-" ? "1" : qty;
            qty = parseInt(qty);

            let option_items = Array.from($(`${self.class_list.OPTIONS} button.selected`));
            option_items.forEach((option_item) => {
              option_item = $(option_item);
              let option = option_item.closest("[option-type]");
              let option_id = parseInt(option.attr("option-id"));
              let price = parseFloat(option_item.attr("price")) * qty;
              let item_id = parseInt(option_item.attr("item-id"));

              data.options.push({
                id: 0,
                option_id: option_id,
                option_item_id: item_id,
                order_id: self.variable_list.SELECTED_ORDER_ID,
                order_product_id: 0,
                price: price,
                qty: qty,
              });

              data.price += price;
            });

            return data;
          }

          let selected_order_id =
            self.variable_list.SELECTED_ORDER_ID > 0
              ? ` ${
                  self.class_list.ORDER_BODY + `[order-id=${self.variable_list.SELECTED_ORDER_ID}]`
                }`
              : "";

          $(self.id_list.ORDER_LIST + selected_order_id).append(
            self.order_create_element(self.variable_list.SELECTED_PRODUCT_ID, get_data())
          );
          self.order_list_scroll_down();
          $(self.id_list.MODAL_PRODUCT_DETAILS).modal("toggle");
          return false;
        });

        $(document).on("click", self.id_list.BACK_DETAIL, function () {
          self.back_detail();
        });

        $(document).on("click", self.class_list.BTN_QTY, function () {
          let element = $(this);
          let element_show = $(self.class_list.BTN_QTY_SHOW);
          let function_name = element.attr("function");

          let qty = element_show.html();
          qty = qty === "-" ? "" : qty;

          switch (function_name) {
            case "qty":
              qty = qty + parseInt(element.html()).toString();
              break;
            case "clear":
              qty = "-";
              break;
          }

          qty = qty < 1 ? 1 : qty;
          element_show.html(qty);
        });

        $(document).on(
          "click",
          `${self.class_list.PRODUCT_ORDER_UNCONFIRMED} td button[function='delete']`,
          function (e) {
            let element = $(this);
            let element_product = element.closest(self.class_list.PRODUCT_ORDER);
            let element_total_price = $(`${self.class_list.TABLE_PRICE_TOTAL} [function='price']`);
            let price = parseFloat(element_product.attr("price"));
            let total_price = parseFloat(element_total_price.html());
            element_total_price.html((total_price - price).toFixed(2));
            element_product.remove();
            if (
              self.variable_list.SELECTED_ORDER_ID === 0 &&
              $(`${self.class_list.PRODUCT_ORDER_UNCONFIRMED}`).length < 1
            ) {
              $(`tr${self.class_list.ORDER_TITLE_UNCONFIRMED}`).remove();
            }
            e.stopPropagation();
          }
        );

        $(self.class_list.ORDER_BTN).on("click", function () {
          let function_name = $(this).attr("function");
          switch (function_name) {
            case "insert":
              if ($(self.class_list.PRODUCT_ORDER_UNCONFIRMED).length > 0) {
                set(
                  set_types.INSERT,
                  self.get_order_product_data(self.order_product_get_types.UNCONFIRMED),
                  function (data) {
                    data = JSON.parse(data);
                    if (data.status) {
                      main.get_order_related_things(
                        main.get_type_for_order_related_things.ORDER_AND_ORDER_PRODUCTS
                      );
                      helper_sweet_alert.success(
                        language.data.PROCESS_SUCCESS_TITLE,
                        language.data.PRODUCT_SENT_SUCCESS
                      );
                      self.back_detail(self.back_detail_types.TABLE);
                    }
                  }
                );
              }
              break;
            case "new_order":
              self.get();
              self.variable_list.SELECTED_ORDER_ID = 0;
              $(`${self.class_list.ORDER_TITLE}`).removeClass("selected");
              break;
            case "move_table":
              if ($(self.class_list.PRODUCT_ORDER_CONFIRMED).length > 0) {
                if (
                  self.variable_list.SELECTED_ORDER_ID !== 0 &&
                  array_list.find_multi(
                    main.data_list.PAYMENTS,
                    self.variable_list.SELECTED_ORDER_ID,
                    "order_id"
                  ).length > 0
                )
                  return;
                self.back_detail(self.back_detail_types.TABLE_MOVE);
              }
              break;
            case "order_combining":
              if (self.variable_list.SELECTED_ORDER_ID !== 0) self.order_combining();
              break;
            case "fast_payment":
              if (
                self.variable_list.SELECTED_ORDER_TYPE === helper.db.order_types.SAFE ||
                self.variable_list.SELECTED_ORDER_TYPE === helper.db.order_types.TAKEAWAY ||
                ($(self.class_list.PRODUCT_ORDER_UNCONFIRMED).length < 1 &&
                  $(self.class_list.PRODUCT_ORDER_CONFIRMED).length > 0)
              ) {
                $(`${self.id_list.MODAL_PAYMENT_TYPES} [function='price']`).html(
                  (self.variable_list.SELECTED_ORDER_ID === 0
                    ? $(`${self.class_list.TABLE_PRICE_TOTAL} [function='price']`).html()
                    : $(
                        `${self.id_list.ORDER_LIST} ${self.class_list.ORDER_BODY}[order-id=${self.variable_list.SELECTED_ORDER_ID}] [function='price']`
                      ).html()) + main.data_list.CURRENCY
                );
                self.variable_list.SELECTED_PAYMENT_MODE = self.payment_modes.FAST;
                $(self.id_list.MODAL_PAYMENT_TYPES).modal();
              }
              break;
            case "delete_product":
            case "catering_product":
            case "change_price":
            case "separate_product":
              let modal_id,
                discount = `:not([type=${helper.db.order_product_types.DISCOUNT}])`,
                catering = `:not([is-catering=true])`,
                order_unconfirmed = `:not(${self.class_list.PRODUCT_ORDER_UNCONFIRMED})`,
                separate_product_one_qty = ``;

              switch (function_name) {
                case "delete_product":
                  modal_id = self.id_list.MODAL_DELETE_PRODUCT;
                  discount = "";
                  break;
                case "catering_product":
                  modal_id = self.id_list.MODAL_CATERING_PRODUCT;
                  break;
                case "change_price":
                  modal_id = self.id_list.MODAL_CHANGE_PRICE;
                  break;
                case "separate_product":
                  modal_id = self.id_list.MODAL_SEPARATE_PRODUCT;
                  separate_product_one_qty = `:not(${self.class_list.PRODUCT_ORDER_CONFIRMED}[qty='1'])`;
                  break;
              }

              if (
                self.variable_list.SELECTED_ORDER_TYPE !== helper.db.order_types.SAFE &&
                self.variable_list.SELECTED_ORDER_TYPE !== helper.db.order_types.TAKEAWAY &&
                self.variable_list.SELECTED_ORDER_ID < 1
              )
                return;
              let element_cancel_btn = $(
                `${self.class_list.ORDER_BTN}[function='${function_name}_cancel']`
              );
              if (
                self.variable_list.SELECTED_ORDER_TYPE !== helper.db.order_types.SAFE &&
                self.variable_list.SELECTED_ORDER_TYPE !== helper.db.order_types.TAKEAWAY &&
                element_cancel_btn.css("display") === "none"
              ) {
                let selected_order_id = ` ${
                  self.class_list.ORDER_BODY + `[order-id=${self.variable_list.SELECTED_ORDER_ID}]`
                }`;
                $(
                  `${self.id_list.ORDER_LIST + selected_order_id} ${
                    self.class_list.PRODUCT_ORDER +
                    separate_product_one_qty +
                    order_unconfirmed +
                    discount +
                    catering
                  } ${self.class_list.TABLE_HIDE_COLUMN}`
                ).html(`
                                    <div function="${function_name}" >
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input">
                                            <label class="custom-control-label"></label>
                                        </div>
                                    </div>                                    
                                `);
                $(self.class_list.TABLE_HIDE_COLUMN).show();
                $(`${self.class_list.ORDER_BTN}:not([disable-group='${function_name}'])`).addClass(
                  "disable"
                );
                element_cancel_btn.show();
              } else {
                $(
                  `${self.class_list.PRODUCT_ORDER_UNCONFIRMED} td div[function='${function_name}'] div input[type='checkbox']:checked`
                )
                  .closest(self.class_list.PRODUCT_ORDER)
                  .remove();
                if (
                  self.variable_list.SELECTED_ORDER_TYPE === helper.db.order_types.SAFE ||
                  self.variable_list.SELECTED_ORDER_TYPE === helper.db.order_types.TAKEAWAY ||
                  $(
                    `${self.class_list.PRODUCT_ORDER} td div[function='${function_name}'] div input[type='checkbox']:checked`
                  ).length > 0
                ) {
                  $(modal_id).modal();
                }
              }
              break;
            case "delete_product_cancel":
            case "catering_product_cancel":
            case "change_price_cancel":
            case "separate_product_cancel":
              $(this).hide();
              $(self.class_list.TABLE_HIDE_COLUMN).hide();
              $(
                `${self.id_list.ORDER_LIST} ${self.class_list.PRODUCT_ORDER} ${self.class_list.TABLE_HIDE_COLUMN}`
              ).html("");
              $(`${self.class_list.ORDER_BTN}`).removeClass("disable");
              break;
            case "discount":
              if (
                $(self.class_list.PRODUCT_ORDER_CONFIRMED).length > 0 &&
                self.variable_list.SELECTED_ORDER_ID > 0
              ) {
                $(self.id_list.FORM_MODAL_DISCOUNT).trigger("reset");
                $(`${self.id_list.FORM_MODAL_DISCOUNT} [name='type'][value='1']`)
                  .trigger("click")
                  .change();
                $(self.id_list.MODAL_DISCOUNT).modal();
              }
              break;
            case "payment":
              if (
                $(self.class_list.PRODUCT_ORDER_UNCONFIRMED).length < 1 &&
                $(self.class_list.PRODUCT_ORDER_CONFIRMED).length > 0 &&
                self.variable_list.SELECTED_ORDER_ID > 0
              ) {
                self.variable_list.SELECTED_PAYMENT_MODE = self.payment_modes.NORMAL;
                $(self.id_list.MODAL_PAYMENT).modal();
              }
              break;
            case "print_safe":
              print_payment_invoice();
              break;
            case "read_barcode":
              $(self.id_list.MODAL_BARCODE_SYSTEM).modal();
              $(
                `${self.id_list.MODAL_BARCODE_SYSTEM} input${self.class_list.BARCODE_DETAILS}`
              ).focus();
              break;
            case "change_status":
              self.get_order_status_types();
              $(self.id_list.MODAL_ORDER_STATUS_TYPES).modal("show");
              break;
          }
        });

        $(document).on("click", self.class_list.PRODUCT_ORDER, function () {
          let element = $(this);
          let element_delete_checkbox = element
            .children("td")
            .children(
              "div[function='delete_product'],[function='catering_product'],[function='change_price'],[function='separate_product']"
            )
            .children("div")
            .children("input[type='checkbox'],input[type='radio']");
          if (element_delete_checkbox.length > 0) {
            element_delete_checkbox
              .prop("checked", !element_delete_checkbox.prop("checked"))
              .change();
          } else {
            let order_product_options = element.attr("options");
            let product_id = parseInt(element.attr("product-id"));
            let product = array_list.find(main.data_list.PRODUCTS, product_id, "id");
            let comment = element.attr("comment");
            let product_name = element.attr("product-name");
            let quantity = parseFloat(element.attr("quantity"));
            order_product_options = JSON.parse(order_product_options);
            if (comment === "" && quantity === 1 && order_product_options.length < 1) return false;

            function create_element() {
              let element = ``;

              if (quantity !== 1) {
                let product_quantity = array_list.find(
                  main.data_list.PRODUCT_QUANTITY_TYPES,
                  product.quantity_id,
                  "id"
                );
                element += `
                                <legend class="w-100 text-center">${quantity} ${product_quantity.name}</legend>
                            `;
              }

              if (comment !== "") {
                element += `
                                <fieldset>
                                    <legend>Açıklama</legend>
                                    <ul>
                                        <li>${comment}</li>
                                    </ul>
                                </fieldset>
                            `;
              }

              if (order_product_options.length > 0) {
                element += `
                                <fieldset>
                                    <legend>Opsiyon</legend>
                                    <ul>
                            `;
                order_product_options.forEach((order_product_option) => {
                  let option = array_list.find(
                    main.data_list.PRODUCT_OPTIONS,
                    order_product_option.option_id,
                    "id"
                  );
                  let option_item = array_list.find(
                    main.data_list.PRODUCT_OPTIONS_ITEMS,
                    order_product_option.option_item_id,
                    "id"
                  );
                  element += `
                                    <li>[${option.name}]-> ${order_product_option.qty}x ${
                    option_item.name
                  } ${
                    order_product_option.price != 0
                      ? `[${order_product_option.price > 0 ? `+` : ``}${
                          order_product_option.price + main.data_list.CURRENCY
                        }]`
                      : ``
                  }</li>
                                `;
                });
                element += `
                                    </ul>
                                </fieldset>
                            `;
              }

              return element;
            }

            $.confirm({
              title: product_name,
              backgroundDismiss: false,
              content: create_element(),
              type: "red",
              typeAnimated: true,
              buttons: {
                okay: {
                  text: "Tamam",
                  action: function () {},
                },
              },
            });
          }
        });

        $(
          `${self.id_list.MODAL_DELETE_PRODUCT},${self.id_list.MODAL_CATERING_PRODUCT},${self.id_list.MODAL_CHANGE_PRICE},${self.id_list.MODAL_SEPARATE_PRODUCT}`
        ).on("show.bs.modal", function () {
          let id = "#" + $(this).attr("id");
          let attr_value,
            form_id,
            products_id,
            readonly_qty = "",
            readonly_price = "";

          switch (id) {
            case self.id_list.MODAL_DELETE_PRODUCT:
              attr_value = "delete_product";
              form_id = self.id_list.FORM_MODAL_DELETE;
              products_id = self.class_list.DELETE_PRODUCTS;
              readonly_price = "readonly";
              break;
            case self.id_list.MODAL_CATERING_PRODUCT:
              attr_value = "catering_product";
              form_id = self.id_list.FORM_MODAL_CATERING;
              products_id = self.class_list.CATERING_PRODUCTS;
              $(`${self.id_list.FORM_MODAL_CATERING} select[name='owner']`).html(
                helper.get_select_options(main.data_list.CATERING_OWNERS, "id", "name")
              );
              $(`${self.id_list.FORM_MODAL_CATERING} select[name='question']`).html(
                helper.get_select_options(main.data_list.CATERING_QUESTIONS, "id", "comment")
              );
              readonly_price = "readonly";
              break;
            case self.id_list.MODAL_CHANGE_PRICE:
              attr_value = "change_price";
              form_id = self.id_list.FORM_MODAL_CHANGE_PRICE;
              products_id = self.class_list.CHANGE_PRICE_PRODUCTS;
              readonly_qty = "readonly";
              break;
            case self.id_list.MODAL_SEPARATE_PRODUCT:
              attr_value = "separate_product";
              form_id = self.id_list.FORM_MODAL_SEPARATE_PRODUCT;
              products_id = self.class_list.SEPARATE_PRODUCT_PRODUCTS;
              readonly_qty = "readonly";
              break;
          }

          function create_element() {
            let elements = ``;

            let class_order_confirmed =
              self.variable_list.SELECTED_ORDER_TYPE === helper.db.order_types.SAFE ||
              self.variable_list.SELECTED_ORDER_TYPE === helper.db.order_types.TAKEAWAY
                ? self.class_list.PRODUCT_ORDER_UNCONFIRMED
                : `${self.class_list.PRODUCT_ORDER_CONFIRMED} td div[function='${attr_value}'] div input[type='checkbox']:checked`;
            let products = Array.from($(class_order_confirmed));
            products.forEach((product) => {
              product = $(product).closest(self.class_list.PRODUCT_ORDER);
              elements += `
                                <tr row="${product.attr("row")}" product-id="${product.attr(
                "product-id"
              )}" order-product-id="${product.attr("order-product-id")}" options ='${product.attr(
                "options"
              )}' >
                                    <td function="qty" old-qty="${product.attr("qty")}">
                                        <input class="form-input" type="number" name="qty" value="${product.attr(
                                          "qty"
                                        )}" max="${product.attr(
                "qty"
              )}" min="1" ${readonly_qty} required>
                                    </td>
                                    <td>
                                        ${product.children("td[function='name']").html()}
                                    </td>
                                    ${
                                      attr_value !== "separate_product"
                                        ? `<td function="price" old-price="${product.attr(
                                            "price"
                                          )}">
                                        <input class="form-input" type="number" name="price" step="0.01" min="0" max="9999999999" value="${product.attr(
                                          "price"
                                        )}" ${readonly_price} required>
                                   </td>`
                                        : `<td function="qty_separate">
                                        <input class="form-input" type="number" name="qty_separate" min="1"  max="${
                                          product.attr("qty") - 1
                                        }" value="${product.attr("qty") - 1}" required>
                                    </td>
                                    <td function="piece_separate">
                                        <input class="form-input" type="number" name="piece_separate" min="1"  max="${
                                          product.attr("qty") - 1
                                        }" value="1" required>
                                    </td>
                                    `
                                    }
                                </tr>
                            `;
            });

            return elements;
          }

          $(form_id).trigger("reset");
          $(products_id).html(create_element());
        });

        $(document).on(
          "change keyup",
          `${self.class_list.DELETE_PRODUCTS} tr td[function='qty'] input[name='qty'],
                    ${self.class_list.CATERING_PRODUCTS} tr td[function='qty'] input[name='qty']`,
          function () {
            let element = $(this).closest("[order-product-id]");
            let old_qty = parseInt(element.children("td[function='qty']").attr("old-qty"));
            let old_price = parseFloat(element.children("td[function='price']").attr("old-price"));
            let new_qty = parseInt(
              element.children("td[function='qty']").children("input[name='qty']").val()
            );
            let single_price = old_price / old_qty;
            let new_price = single_price * new_qty;
            element.children("td[function='price']").children("input[name='price']").val(new_price);

            let options = JSON.parse(element.attr("options"));
            options.forEach((option) => {
              option.price = (option.price / option.qty) * new_qty;
              option.qty = new_qty;
            });
            element.attr("options", JSON.stringify(options));
          }
        );

        $(
          `${self.id_list.FORM_MODAL_DELETE},${self.id_list.FORM_MODAL_CATERING},${self.id_list.FORM_MODAL_CHANGE_PRICE},${self.id_list.FORM_MODAL_SEPARATE_PRODUCT}`
        ).submit(function (e) {
          e.preventDefault();
          let id = "#" + $(this).attr("id");
          let attr_value,
            modal_id,
            products_id,
            function_type,
            data = {},
            set_type = set_types.CANCEL_AND_CATERING;

          switch (id) {
            case self.id_list.FORM_MODAL_DELETE:
              attr_value = "delete_product";
              products_id = self.class_list.DELETE_PRODUCTS;
              modal_id = self.id_list.MODAL_DELETE_PRODUCT;
              function_type = self.function_types.DELETE;
              data = {
                comment: $(`${self.id_list.FORM_MODAL_DELETE} input[name='comment']`).val(),
              };
              break;
            case self.id_list.FORM_MODAL_CATERING:
              attr_value = "catering_product";
              products_id = self.class_list.CATERING_PRODUCTS;
              modal_id = self.id_list.MODAL_CATERING_PRODUCT;
              function_type = self.function_types.CATERING;
              data = {
                owner: $(`${self.id_list.FORM_MODAL_CATERING} select[name='owner']`).val(),
                question: $(`${self.id_list.FORM_MODAL_CATERING} select[name='question']`).val(),
              };
              break;
            case self.id_list.FORM_MODAL_CHANGE_PRICE:
              attr_value = "change_price";
              products_id = self.class_list.CHANGE_PRICE_PRODUCTS;
              modal_id = self.id_list.MODAL_CHANGE_PRICE;
              function_type = parseInt(
                $(`${self.id_list.FORM_MODAL_CHANGE_PRICE} button[type=submit]:focus`).attr(
                  "change-id"
                )
              );
              set_type = set_types.CHANGE_PRICE;
              break;
            case self.id_list.FORM_MODAL_SEPARATE_PRODUCT:
              attr_value = "separate_products";
              products_id = self.class_list.SEPARATE_PRODUCT_PRODUCTS;
              modal_id = self.id_list.MODAL_SEPARATE_PRODUCT;
              function_type = 0;
              set_type = set_types.SEPARATE_PRODUCT;
              break;
          }

          function get_data() {
            let order = array_list.find(
              main.data_list.ORDERS,
              self.variable_list.SELECTED_ORDER_ID,
              "id"
            );
            data = Object.assign(data, {
              order_id: self.variable_list.SELECTED_ORDER_ID,
              function_type: function_type,
              products: Array(),
              orders: [
                {
                  table_id: self.variable_list.SELECTED_TABLE_ID,
                  no: typeof order === "undefined" ? "" : order.no,
                },
              ],
            });

            let products = Array.from($(`${products_id} tr`));
            products.forEach((product) => {
              product = $(product);
              data.products.push({
                id: product.attr("order-product-id"),
                product_id: product.attr("product-id"),
                options: product.attr("options"),
                row: product.attr("row"),
                qty: product.children("td[function='qty']").children("input[name='qty']").val(),
                price: product
                  .children("td[function='price']")
                  .children("input[name='price']")
                  .val(),
                qty_separate: product
                  .children("td[function='qty_separate']")
                  .children("input[name='qty_separate']")
                  .val(),
                piece_separate: product
                  .children("td[function='piece_separate']")
                  .children("input[name='piece_separate']")
                  .val(),
              });
            });

            return data;
          }

          if (
            self.variable_list.SELECTED_ORDER_TYPE === helper.db.order_types.SAFE ||
            self.variable_list.SELECTED_ORDER_TYPE === helper.db.order_types.TAKEAWAY
          ) {
            let column_price = "",
              column_vat = "";
            data = get_data();
            switch (data.function_type) {
              case self.change_price_types.NORMAL:
                column_price = "price";
                column_vat = "vat";
                break;
              case self.change_price_types.SAFE:
                column_price = "price_safe";
                column_vat = "vat_safe";
                break;
              case self.change_price_types.TAKE_AWAY:
                column_price = "price_take_away";
                column_vat = "vat_take_away";
                break;
              case self.change_price_types.COME_TAKE:
                column_price = "price_come_take";
                column_vat = "vat_come_take";
                break;
              case self.change_price_types.PERSONAL:
                column_price = "price_personal";
                column_vat = "vat_personal";
                break;
              case self.change_price_types.OTHER:
                column_price = "price_other";
                column_vat = "vat_other";
                break;
            }

            data.products.forEach((product) => {
              let element_product = $(`${self.class_list.PRODUCT_ORDER}[row='${product.row}']`);
              let options = JSON.parse(element_product.attr("options"));
              let find_product = array_list.find(
                main.data_list.PRODUCTS,
                parseInt(element_product.attr("product-id")),
                "id"
              );
              let price = parseFloat(find_product[column_price]);
              let vat = parseFloat(find_product[column_vat]);

              if (data.function_type === self.change_price_types.CUSTOM) {
                price = parseFloat(product.price);
                vat = parseFloat(element_product.attr("vat"));
              } else {
                options.forEach((option) => {
                  price += parseFloat(option.price);
                });
              }

              element_product.attr("price", price);
              element_product.attr("vat", vat);
              element_product
                .children("td")
                .children("span[function='price']")
                .html(price.toFixed(2));
            });
            helper_sweet_alert.success(
              language.data.PROCESS_SUCCESS_TITLE,
              language.data.PROCESS_SUCCESS
            );
            $(modal_id).modal("toggle");
          } else {
            set(set_type, get_data(), function (data) {
              data = JSON.parse(data);
              main.get_order_related_things(
                main.get_type_for_order_related_things.ORDER_AND_ORDER_PRODUCTS
              );
              $(`${self.class_list.ORDER_BTN}[function='${attr_value}_product_cancel']`).trigger(
                "click"
              );
              $(modal_id).modal("toggle");
              helper_sweet_alert.success(
                language.data.PROCESS_SUCCESS_TITLE,
                language.data.PROCESS_SUCCESS
              );
              self.get();
            });
          }
          return false;
        });

        $(document).on("click", self.class_list.ORDER_TITLE, function () {
          let element = $(this);
          let id = parseInt(element.closest(self.class_list.ORDER_BODY).attr("order-id"));

          if (
            self.variable_list.SELECTED_ORDER_ID === id &&
            self.variable_list.SELECTED_ORDER_ID !== 0
          ) {
            self.variable_list.SELECTED_ORDER_ID = 0;

            self.get();
            return;
          }

          if ($(self.class_list.PRODUCT_ORDER_UNCONFIRMED).length > 1) {
            Swal.fire({
              title: language.data.UNSAVED_ORDER,
              text: language.data.UNSAVED_QUESTION,
              icon: "warning",
              showCancelButton: true,
              confirmButtonColor: "#3085d6",
              cancelButtonColor: "#d33",
              confirmButtonText: language.data.ACCEPT,
              cancelButtonText: language.data.DECLINE,
              allowOutsideClick: false,
              allowEscapeKey: false,
              allowEnterKey: false,
            }).then((result) => {
              if (result.value) {
                self.variable_list.SELECTED_ORDER_ID = id;
                self.variable_list.IS_SELECT_ORDER = true;
                self.get();
              }
            });
          } else {
            $(`${self.class_list.ORDER_TITLE}`).removeClass("selected");
            element.addClass("selected");
            self.variable_list.SELECTED_ORDER_ID = id;
            self.variable_list.IS_SELECT_ORDER = true;
          }
        });

        $(document).on(
          "click",
          `${self.class_list.PAYMENT_TYPES} ${self.class_list.PAYMENT_BTN}, ${self.class_list.PAYMENT_MODAL_PAYMENT_BTN}`,
          function () {
            let id = parseInt($(this).attr("type-id"));

            if (id === 6 && self.variable_list.SELECTED_TRUST_ACCOUNT_ID === 0) {
              $(self.id_list.MODAL_TRUST_ACCOUNT).modal();
              return false;
            }

            function get_data() {
              let data = {
                orders: Array(),
                products_normal_payment: Array(),
                table_id: self.variable_list.SELECTED_TABLE_ID,
              };

              switch (self.variable_list.SELECTED_PAYMENT_MODE) {
                case self.payment_modes.FAST:
                  let attr_order_id =
                    self.variable_list.SELECTED_ORDER_ID > 0
                      ? `[order-id=${self.variable_list.SELECTED_ORDER_ID}]`
                      : ``;
                  let orders = Array.from($(`${self.class_list.ORDER_BODY}${attr_order_id}`));

                  orders.forEach((order) => {
                    order = $(order);
                    data.orders.push({
                      id: order.attr("order-id"),
                      price: order.attr("price"),
                    });
                  });
                  break;
                case self.payment_modes.NORMAL:
                  let products = Array.from(
                    $(self.class_list.PAYMENT_MODAL_PAYMENT_PRODUCT_SELECTED)
                  );
                  products.forEach((product) => {
                    product = $(product);
                    data.products_normal_payment.push({
                      id: product.attr("order-product-id"),
                    });
                  });

                  data.orders.push({
                    id: self.variable_list.SELECTED_ORDER_ID,
                    price: parseFloat(
                      $(
                        `${self.id_list.MODAL_PAYMENT} [function='payment_price'] [function='price']`
                      ).html()
                    ),
                  });
                  break;
              }

              return data;
            }

            let data =
              self.variable_list.SELECTED_ORDER_TYPE === helper.db.order_types.SAFE ||
              self.variable_list.SELECTED_ORDER_TYPE === helper.db.order_types.TAKEAWAY
                ? self.get_order_product_data(self.order_product_get_types.UNCONFIRMED)
                : get_data();
            data = Object.assign(data, {
              order_type: self.variable_list.SELECTED_ORDER_TYPE,
              payment_type: id,
              trust_account_id: self.variable_list.SELECTED_TRUST_ACCOUNT_ID,
            });

            switch (self.variable_list.SELECTED_PAYMENT_MODE) {
              case self.payment_modes.FAST:
                if (app.printer.settings.printPaymentInvoiceAfterPayment) {
                  print_payment_invoice();
                }
                set(set_types.PAYMENT, data, function () {
                  main.get_order_related_things(
                    main.get_type_for_order_related_things.ORDER_AND_ORDER_PRODUCTS
                  );
                  helper_sweet_alert.success(
                    language.data.PROCESS_SUCCESS_TITLE,
                    language.data.PAID_SUCCESS_TEXT
                  );
                  self.variable_list.SELECTED_ORDER_ID = 0;
                  self.variable_list.SELECTED_TRUST_ACCOUNT_ID = 0;
                  self.get();
                  if ($(self.class_list.PRODUCT_ORDER_CONFIRMED).length < 1)
                    self.back_detail(self.back_detail_types.TABLE);
                  $(self.id_list.MODAL_PAYMENT_TYPES).modal("toggle");
                });
                break;
              case self.payment_modes.NORMAL:
                Swal.fire({
                  icon: "question",
                  title: language.data.PAYMENT_PROCESS,
                  html: `<b>'${array_list.find(main.data_list.PAYMENT_TYPES, id, "id").name}'</b> ${
                    language.data.PAYMENT_QUESTION
                  }?`,
                  allowEscapeKey: false,
                  allowOutsideClick: false,
                  showCancelButton: true,
                  confirmButtonText: language.data.ACCEPT,
                  cancelButtonText: language.data.DECLINE,
                  confirmButtonClass: "btn btn-success btn-lg mr-3 mt-5",
                  cancelButtonClass: "btn btn-danger btn-lg ml-3 mt-5",
                  buttonsStyling: false,
                }).then((result) => {
                  if (result.value) {
                    let total_price = parseFloat(
                      $(
                        `${self.id_list.MODAL_PAYMENT} [function='total_price'] [function='price']`
                      ).html()
                    );
                    let payment_price = parseFloat(
                      $(
                        `${self.id_list.MODAL_PAYMENT} [function='payment_price'] [function='price']`
                      ).html()
                    );

                    if (total_price <= payment_price) {
                      if (app.printer.settings.printPaymentInvoiceAfterPayment) {
                        print_payment_invoice();
                      }
                    }

                    set(set_types.PAYMENT, data, function (data) {
                      data = JSON.parse(data);
                      main.get_order_related_things(
                        main.get_type_for_order_related_things.ORDER_AND_ORDER_PRODUCTS
                      );
                      main.get_payments_related_things(
                        main.get_type_for_payments_related_things.PAYMENTS
                      );
                      helper_sweet_alert.success(
                        language.data.PROCESS_SUCCESS_TITLE,
                        language.data.PAID_SUCCESS_TEXT
                      );
                      self.variable_list.SELECTED_ORDER_ID =
                        typeof data.custom_data.new_order_id !== "undefined"
                          ? parseInt(data.custom_data.new_order_id)
                          : self.variable_list.SELECTED_ORDER_ID;
                      self.variable_list.SELECTED_TRUST_ACCOUNT_ID = 0;
                      self.get();
                      if (total_price <= payment_price) {
                        $(self.id_list.MODAL_PAYMENT).modal("hide");
                        if ($(self.class_list.PRODUCT_ORDER_CONFIRMED).length < 1) {
                          self.back_detail(self.back_detail_types.TABLE);
                          return;
                        }
                      } else {
                        $(self.class_list.PAYMENT_MODAL_PAYMENT_PRODUCTS_SELECTED).html("");
                        self.variable_list.IS_CUSTOM_PAYMENT_PRICE = false;
                      }
                      self.get_payment(self.get_payment_get_types.ALL);
                      self.variable_list.SELECTED_ORDER_ID =
                        $(
                          `${self.class_list.ORDER_BODY}[order-id='${self.variable_list.SELECTED_ORDER_ID}']`
                        ).length < 1
                          ? 0
                          : self.variable_list.SELECTED_ORDER_ID;
                    });
                  }
                });
                break;
            }
          }
        );

        $(self.id_list.MODAL_TRUST_ACCOUNT).on("show.bs.modal", self.get_trust_accounts());

        $(self.class_list.SEARCH_TRUST_ACCOUNT).on("keyup change", function () {
          let function_name = $(this).attr("function");

          let search_type = function_name === "id" ? self.search_types.ID : self.search_types.NAME;

          self.get_trust_accounts(search_type, $(this).val());
        });

        $(document).on("click", `${self.class_list.TRUST_ACCOUNTS} tr[account-id]`, function () {
          self.variable_list.SELECTED_TRUST_ACCOUNT_ID = parseInt($(this).attr("account-id"));
          Swal.fire({
            icon: "question",
            title: language.data.CREDIT_TRANSACTION,
            html: `<b>'${
              array_list.find(
                main.data_list.TRUST_ACCOUNTS,
                self.variable_list.SELECTED_TRUST_ACCOUNT_ID,
                "id"
              ).name
            }'</b>${language.data.ACCOUNT_CREDIT_QUESTION}`,
            allowEscapeKey: false,
            allowOutsideClick: false,
            showCancelButton: true,
            confirmButtonText: language.data.ACCEPT,
            cancelButtonText: language.data.DECLINE,
            confirmButtonClass: "btn btn-success btn-lg mr-3 mt-5",
            cancelButtonClass: "btn btn-danger btn-lg ml-3 mt-5",
            buttonsStyling: false,
          }).then((result) => {
            if (result.value) {
              $(self.id_list.MODAL_TRUST_ACCOUNT).modal("hide");
              $(
                `${self.class_list.PAYMENT_TYPES} ${self.class_list.PAYMENT_BTN}[type-id='6']`
              ).trigger("click");
            }
          });
        });

        $(self.class_list.NEW_TRUST_ACCOUNT).on("click", function () {
          $(self.id_list.MODAL_NEW_TRUST_ACCOUNT).modal();
        });

        $(self.id_list.FORM_MODAL_NEW_TRUST_ACCOUNT).submit(function (e) {
          e.preventDefault();
          let data = Object.assign($(this).serializeObject(), {
            id: 0,
            function_type: 0x0001,
            set_type: 0x0001,
          });
          helper_sweet_alert.wait(
            language.data.PROCESS_PROGRESS_TITLE,
            language.data.PROCESS_WAIT_CONTENT
          );
          $.ajax({
            url: `${default_ajax_path_finance}set.php`,
            type: "POST",
            data: data,
            success: function (data) {
              data = JSON.parse(data);
              if (data.status) {
                main.get_trust_accounts_related_things(
                  main.get_type_for_branch_trust_accounts_related_things.ACCOUNTS
                );
                self.get_trust_accounts();
                $(self.id_list.MODAL_NEW_TRUST_ACCOUNT).modal("hide");
                $(this).trigger("reset");
                helper_sweet_alert.success(
                  language.data.PROCESS_WAIT_CONTENT,
                  language.data.PROCESS_SUCCESS
                );
              }
            },
            error: helper_sweet_alert.close(),
            timeout: settings.ajax_timeouts.NORMAL,
          });
        });

        $(self.id_list.FORM_MODAL_DISCOUNT).submit(function (e) {
          e.preventDefault();
          let form_data = $(this).serializeObject();

          function get_data() {
            let data = {
              products: Array(),
              table_id: self.variable_list.SELECTED_TABLE_ID,
              order_id: self.variable_list.SELECTED_ORDER_ID,
              no: array_list.find(main.data_list.ORDERS, self.variable_list.SELECTED_ORDER_ID, "id")
                .no,
              discount: 0,
              type: self.variable_list.SELECTED_ORDER_TYPE,
              status: helper.db.order_status_types.GETTING_READY,
            };

            let price = $(
              `${self.id_list.ORDER_LIST} ${self.class_list.ORDER_BODY}[order-id=${self.variable_list.SELECTED_ORDER_ID}] [function='price']`
            ).html();
            price = parseFloat(price);

            let discount =
              form_data.type == self.discount_types.PRICE
                ? form_data.price
                : (price / 100) * form_data.price;
            discount = discount * -1;

            data.products.push({
              id: 0,
              quantity: 1,
              price: discount,
              vat: 0,
              discount: 0,
              qty: 1,
              comment: form_data.comment,
              type: helper.db.order_product_types.DISCOUNT,
              options: Array(),
            });

            return data;
          }

          set(set_types.INSERT, get_data(), function (data) {
            data = JSON.parse(data);
            if (data.status) {
              main.get_order_related_things(
                main.get_type_for_order_related_things.ORDER_AND_ORDER_PRODUCTS
              );
              helper_sweet_alert.success(
                language.data.PROCESS_WAIT_CONTENT,
                language.data.ADDED_DISCOUNT
              );
              self.get();
              self.get_payment(self.get_payment_get_types.ALL);
              $(self.id_list.MODAL_DISCOUNT).modal("hide");
            }
          });
        });

        $(self.id_list.MODAL_PAYMENT).on("show.bs.modal", function () {
          $(self.class_list.PAYMENT_MODAL_PAYMENT_PRODUCTS_SELECTED).html("");
          $(self.class_list.PAYMENT_MODAL_CHANGE_PRICE).hide();
          self.variable_list.IS_CUSTOM_PAYMENT_PRICE = false;
          self.get_payment(self.get_payment_get_types.ALL);
        });

        $(document).on("click", self.class_list.PAYMENT_MODAL_PRODUCT, function () {
          if ($(self.class_list.PAYMENT_MODAL_PAYMENT_PAYMENTS).html().length > 0) return;
          let element = $(this);

          function create_element() {
            let option_icon = "";
            if (
              JSON.parse(element.attr("options")).length > 0 ||
              element.attr("comment") !== "" ||
              element.attr("quantity") != 1
            ) {
              option_icon = "<i class='fa fa-bars'></i>";
            }
            return `
                                <tr class="e_modal_payment_product_selected" 
                                order-product-id="${element.attr("order-product-id")}" 
                                product-id="${element.attr("product-id")}" 
                                product-name="${element.attr("product-name")}" 
                                quantity="${element.attr("quantity")}" 
                                price="${element.attr("price")}" 
                                vat="${element.attr("vat")}"
                                discount="${element.attr("discount")}" 
                                qty="${parseInt(element.attr("qty"))}" 
                                comment="${element.attr("comment")}" 
                                type="${element.attr("type")}" 
                                options='${element.attr("options")}'>
                                    <td>${parseInt(element.attr("qty"))}</td>
                                    <td function="name">${option_icon} ${element.attr("product-name")}</td>
                                    <td>${element.attr("price") + main.data_list.CURRENCY}</td> 
                                </tr>
                        `;
          }

          $(this).remove();
          let element_total_price = $(
            `${self.id_list.MODAL_PAYMENT} [function='total_price'] [function='price']`
          );
          let price =
            $(self.class_list.PAYMENT_MODAL_PAYMENT_PRODUCT_SELECTED).length > 0
              ? parseFloat(element_total_price.html()) + parseFloat(element.attr("price"))
              : parseFloat(element.attr("price"));
          element_total_price.html(price.toFixed(2));
          $(`${self.id_list.MODAL_PAYMENT} [function='payment_price'] [function='price']`).html(
            price.toFixed(2)
          );
          $(self.class_list.PAYMENT_MODAL_PAYMENT_PRODUCTS_SELECTED).append(create_element());
        });

        $(document).on(
          "click",
          self.class_list.PAYMENT_MODAL_PAYMENT_PRODUCT_SELECTED,
          function () {
            let element = $(this);
            let element_total_price = $(
              `${self.id_list.MODAL_PAYMENT} [function='total_price'] [function='price']`
            );
            let price =
              $(self.class_list.PAYMENT_MODAL_PAYMENT_PRODUCT_SELECTED).length > 0
                ? parseFloat(element_total_price.html()) - parseFloat(element.attr("price"))
                : 0;
            element_total_price.html(price.toFixed(2));
            $(`${self.id_list.MODAL_PAYMENT} [function='payment_price'] [function='price']`).html(
              price.toFixed(2)
            );
            $(this).remove();

            self.get_payment(
              price === 0 ? self.get_payment_get_types.ALL : self.get_payment_get_types.PRODUCTS
            );
          }
        );

        $(`${self.class_list.PAYMENT_MODAL_CALCULATOR} button`).on("click", async function () {
          let value = parseInt($(this).attr("value"));
          let element = $(
            `${self.id_list.MODAL_PAYMENT} [function='payment_price'] [function='price']`
          );
          let price_total = parseFloat(
            $(`${self.id_list.MODAL_PAYMENT} [function='total_price'] [function='price']`).html()
          );
          let element_html = self.variable_list.IS_CUSTOM_PAYMENT_PRICE ? element.html() : "";
          switch (value) {
            case -1:
              element_html = element_html.slice(0, -1);
              break;
            case -2:
              if (element_html.split(".").length < 2) element_html += ".";
              break;
            case -3:
              element_html = "";
              break;
            case -4:
              const { value: inputValue } = await Swal.fire({
                title: "Bölünecek Kişi Sayısını Seçin",
                input: "number",
                showCloseButton: true,
                showCancelButton: true,
                confirmButtonText: language.data.APPROVE,
                cancelButtonText: language.data.CANCEL,
              });
              if (inputValue > 1) {
                element_html = price_total / inputValue;
              }
              break;
            case -5:
              element_html = price_total;
              break;
            case -6:
              element_html = $(this).text();
              break;
            case -7:
              let value_round = price_total % 1;
              console.log(value_round);
              if (value_round !== 0) {
                $(`${self.id_list.FORM_MODAL_DISCOUNT} input[name="comment"]`).val(
                  "Yuvarlama Yapıldı!"
                );
                $(`${self.id_list.FORM_MODAL_DISCOUNT} input[name="price"]`).val(value_round);
                $(`${self.id_list.FORM_MODAL_DISCOUNT} input[name="type"][value="1"]`).prop(
                  "checked",
                  true
                );
                $(`${self.id_list.FORM_MODAL_DISCOUNT}`).trigger("submit");
              }
              return;
              break;
            case -8:
              $(`${self.class_list.ORDER_BTN}[function='discount']`).trigger("click");
              return;
              break;
            case -9:
              element_html = element_html + parseInt("0") + parseInt("0");
              break;
            default:
              element_html += value;
              element_html = parseFloat(element_html);
              break;
          }
          if (Number.isNaN(parseFloat(element_html))) element_html = 0;
          let change_price = price_total - parseFloat(element_html);
          if (change_price < 0) {
            $(self.class_list.PAYMENT_MODAL_CHANGE_PRICE).show();
            $(`${self.id_list.MODAL_PAYMENT} [function='change_price'] [function='price']`).html(
              (change_price * -1).toFixed(2)
            );
          } else {
            $(self.class_list.PAYMENT_MODAL_CHANGE_PRICE).hide();
          }
          self.variable_list.IS_CUSTOM_PAYMENT_PRICE = true;
          element.html(element_html);
        });

        $(document).on("click", `button.e_takeaway_confirm`, function () {
          let element = $(this);
          let confirm = parseInt(element.attr("confirm"));
          let message = confirm === 1 ? language.data.ORDER_CONFIRM : "Sipariş İptal Edilsinmi ?";
          let input =
            confirm === 1
              ? ""
              : `<input type="text" class="form-input e_take_away_cancel_comment" placeholder="İptal Nedeni">`;

          Swal.fire({
            html: `<h3>${message}</h3><br>${input}`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Evet",
            cancelButtonText: "Hayır",
          }).then((result) => {
            if (result.value) {
              if (
                self.variable_list.SELECTED_TABLE_TYPE === self.variable_list.table_types.DEFAULT
              ) {
                set(
                  set_types.UPDATE_CONFIRM,
                  {
                    order_id: self.variable_list.SELECTED_ORDER_ID,
                    confirm: confirm,
                  },
                  function () {
                    main.data_list.ORDERS.forEach(function (item) {
                      if (item.order_id === self.variable_list.SELECTED_ORDER_ID) {
                        item.is_confirm = confirm;
                        return item;
                      }
                    });
                    if (confirm === 1) {
                      $(`[function=separate_product]`).show();
                      $(`[function=delete_product]`).show();
                      $(`[function=fast_payment]`).show();
                      $(`[function=payment]`).show();
                      $(`[function=print_safe]`).show();
                      $(`.e_takeaway_confirm_area`).hide();
                    } else {
                      $("#back_order_detail").trigger("click");
                      $(`[last-order-id=${table_detail.variable_list.SELECTED_ORDER_ID}]`).remove();
                    }
                  }
                );
              } else {
                let reason = "";

                if (confirm !== 1) {
                  reason = variable.clear($(self.class_list.TAKE_AWAY_CANCEL_COMMENT).val());
                }

                switch (integrations.variable_list.SELECTED_INTEGRATE_TYPE) {
                  case helper.db.integrate_types.YEMEK_SEPETI:
                    if (confirm === 1) {
                      let is_all_matching = true;
                      let data_order =
                        integrations.yemek_sepeti.variable_list.DATA[
                          integrations.yemek_sepeti.variable_list.SELECTED_DATA_INDEX
                        ];

                      let data_product = Array();
                      data_order.product.forEach((product) => {
                        if (!is_all_matching) return;

                        let options = product["option"];
                        product = product["@attributes"];

                        let product_integrated = array_list.find(
                          main.data_list.PRODUCTS_INTEGRATE,
                          product.id,
                          "product_id_integrated"
                        );
                        if (typeof product_integrated === "undefined") {
                          is_all_matching = false;
                          return;
                        }
                        let product_current = array_list.find(
                          main.data_list.PRODUCTS,
                          product_integrated.product_id,
                          "id"
                        );

                        let data_options = Array();
                        options.forEach((option) => {
                          if (!is_all_matching) return;
                          option = option["@attributes"];
                          let option_integrated = array_list.find(
                            main.data_list.PRODUCT_OPTIONS_INTEGRATE,
                            option.Id,
                            "option_id_integrated"
                          );
                          if (typeof option_integrated === "undefined") {
                            is_all_matching = false;
                            return;
                          }
                          let option_current = array_list.find(
                            main.data_list.PRODUCT_OPTIONS_ITEMS,
                            option_integrated.option_id,
                            "id"
                          );
                          data_options.push({
                            option_id: option_current.option_id,
                            option_item_id: option_current.id,
                            price: option_current.price,
                            qty: product.Quantity,
                          });
                        });

                        data_product.push({
                          id: product_current.id,
                          quantity: 1,
                          price: product.ListPrice,
                          vat: product_current.vat,
                          discount: 0,
                          qty: product.Quantity,
                          comment: "",
                          options: data_options,
                          type: helper.db.order_product_types.PRODUCT,
                        });
                      });

                      if (!is_all_matching) {
                        helper_sweet_alert.error(
                          "İşlem Onaylanamadı!",
                          "Lütfen tüm ürün ve opsiyonları eşleştirdiğinizden emin olunuz!"
                        );
                        return false;
                      }

                      data_order = data_order["@attributes"];
                      let integrate_payment_id = array_list.find(
                        main.data_list.PAYMENT_TYPES_INTEGRATE,
                        data_order.PaymentMethodId,
                        "type_id_integrate"
                      );
                      integrate_payment_id =
                        typeof integrate_payment_id === "undefined"
                          ? `-${data_order.PaymentMethodId}`
                          : integrate_payment_id.id;

                      let data_insert = {
                        products: data_product,
                        table_id: self.variable_list.SELECTED_TABLE_ID,
                        order_id: 0,
                        order_id_integrate: self.variable_list.SELECTED_ORDER_ID,
                        discount: 0,
                        type: helper.db.order_types.YEMEK_SEPETI,
                        type_integrate: integrations.variable_list.SELECTED_INTEGRATE_TYPE,
                        status: helper.db.order_status_types.GETTING_READY,
                        address_integrate:
                          `Adres: ${data_order.Address} - ${data_order.AddressDescription} ${data_order.City}/${data_order.Region}` +
                          ` | Telefon Numarası: ${data_order.CustomerPhone}` +
                          `${
                            data_order.CustomerPhone2 !== ""
                              ? ` | Telefon Numarası - 2: ${data_order.CustomerPhone2}`
                              : ``
                          }` +
                          `${
                            data_order.OrderNote !== ""
                              ? ` | Sipariş Notu: ${data_order.OrderNote}`
                              : ``
                          }` +
                          ` | Ödeme Notu: ${data_order.PaymentNote}`,
                        customer_id_integrate: data_order.CustomerId,
                        customer_name_integrate: data_order.CustomerName,
                        integrate_payment_id: integrate_payment_id,
                      };

                      helper.log(data_insert, "data_insert");

                      set(set_types.INSERT, data_insert, function (data) {
                        data = JSON.parse(data);
                        console.log(data);
                        main.get_order_related_things(
                          main.get_type_for_order_related_things.ORDER_AND_ORDER_PRODUCTS
                        );
                        main.get_integrate_related_things(
                          main.get_type_for_integrate_related_things.ORDERS
                        );
                      });
                    }

                    integrated_companies.yemek_sepeti.orders.set_message_successful(
                      integrations.yemek_sepeti.variable_list.SELECTED_MESSAGE_ID,
                      function (data) {
                        if (data.rows.status) {
                          let state =
                            confirm === 1
                              ? integrated_companies.yemek_sepeti.orders.variable_list.order_state
                                  .enums.ACCEPTED
                              : integrated_companies.yemek_sepeti.orders.variable_list.order_state
                                  .enums.CANCELLED;

                          integrated_companies.yemek_sepeti.orders.set_status(
                            table_detail.variable_list.SELECTED_ORDER_ID,
                            state,
                            reason,
                            function (data) {}
                          );
                        }
                      }
                    );
                    break;
                }

                self.back_detail(self.back_detail_types.TABLE);
                $(
                  `${integrations.class_list.TABLE}[integrate-type="${integrations.variable_list.SELECTED_INTEGRATE_TYPE}"][order-id="${self.variable_list.SELECTED_ORDER_ID}"]`
                ).remove();
              }
            }
          });
        });

        //barcode
        $(document).on(
          "keypress",
          `${self.id_list.MODAL_BARCODE_SYSTEM} ${self.class_list.BARCODE_INPUT}`,
          function (e) {
            const kg = 1000;
            const keycode = {
              ENTER: 13,
            };
            if (e.which === keycode.ENTER) {
              let html = "";
              let numbers = $(
                `${self.id_list.MODAL_BARCODE_SYSTEM} ${self.class_list.BARCODE_INPUT}`
              ).val();

              if (numbers.length === 13) {
                let product = {
                  code: numbers.substring(2, 7),
                  quantity: numbers.substring(7, 12),
                  quantity_type: numbers.substring(0, 2),
                };
                let p = array_list.find(main.data_list.PRODUCTS, parseInt(product.code), "code");
                let quantity = (parseInt(product.quantity) / kg).toFixed(3);

                if (typeof p == "undefined") {
                  $(`${self.id_list.MODAL_BARCODE_SYSTEM} ${self.class_list.BARCODE_INPUT}`).val(
                    ""
                  );
                  $(`${self.id_list.MODAL_BARCODE_SYSTEM} .e_message`)
                    .addClass("alert-danger")
                    .addClass("alert-success")
                    .html(
                      "<h2 style='color: red' class='p-0 m-0 text-center'>ÜRÜN BULUNAMADI !</h2>"
                    );
                  return false;
                }

                html = `<li style="color: white;font-size: 20px">
                                    Ürün Adı: ${p.name} 
                                    KG: ${(parseInt(product.quantity) / kg).toFixed(3)}, 
                                    Fiyat: ${(p.price * quantity).toFixed(2)}  </li>`;
                self.variable_list.SELECTED_PRODUCT_ID = p.id;

                self.add_order_product({
                  quantity: quantity,
                  price: p.price,
                  qty: 1,
                });
              } else {
                $(`${self.id_list.MODAL_BARCODE_SYSTEM} .e_message`)
                  .addClass("alert-danger")
                  .addClass("alert-success")
                  .html(
                    "<h2 style='color: red' class='p-0 m-0 text-center'>ÜRÜN BULUNAMADI !</h2>"
                  );
                return false;
              }
              $(`${self.id_list.MODAL_BARCODE_SYSTEM} ${self.class_list.BARCODE_DETAILS}`).append(
                html
              );
              $(`${self.id_list.MODAL_BARCODE_SYSTEM} .e_message`)
                .removeClass("alert-danger")
                .addClass("alert-success")
                .html("<h2 class='m-0 p-0 text-center' style='color: #0c0c0c'>Ürün Eklendi</h2>");
              $(`${self.id_list.MODAL_BARCODE_SYSTEM} ${self.class_list.BARCODE_INPUT}`).val("");
            }
          }
        );

        $(self.id_list.MODAL_BARCODE_SYSTEM).on("show.bs.modal", function () {
          $(`${self.id_list.MODAL_BARCODE_SYSTEM} ${self.class_list.BARCODE_DETAILS}`).html("");
          setTimeout(function () {
            $(`input${self.class_list.BARCODE_INPUT}`).focus();
          }, 500);
        });

        $(document).on("click", self.class_list.BUTTON_ORDER_STATUS_TYPE, function () {
          let type = $(this).attr("type-id");

          let confirm = true;
          let type_name = array_list.find(self.variable_list.ORDER_STATUS_TYPES, type, "id").name;

          switch (integrations.variable_list.SELECTED_INTEGRATE_TYPE) {
            case helper.db.integrate_types.YEMEK_SEPETI:
              if (
                type ===
                  integrated_companies.yemek_sepeti.orders.variable_list.order_state.enums
                    .CANCELLED ||
                type ===
                  integrated_companies.yemek_sepeti.orders.variable_list.order_state.enums.REJECTED
              )
                confirm = false;
              break;
          }

          let order_id = array_list.find(
            array_list.find_multi(
              main.data_list.ORDERS_INTEGRATE,
              integrations.variable_list.SELECTED_INTEGRATE_TYPE,
              "type"
            ),
            table_detail.variable_list.SELECTED_ORDER_ID,
            "order_id"
          ).order_id_integrate;

          let input = confirm
            ? ""
            : `<input type="text" class="form-input e_take_away_cancel_comment" placeholder="İptal Nedeni">`;

          Swal.fire({
            title: "Durum Değiştirme",
            html: `Siparişin durumunu <b>'${type_name}'</b> olarak değiştirmek istediğinizden emin misiniz?<br>${input}`,
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Evet",
            cancelButtonText: "Hayır",
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
          }).then((result) => {
            if (result.value) {
              let reason = "";

              if (!confirm) {
                reason = variable.clear($(self.class_list.TAKE_AWAY_CANCEL_COMMENT).val());
              }

              switch (integrations.variable_list.SELECTED_INTEGRATE_TYPE) {
                case helper.db.integrate_types.YEMEK_SEPETI:
                  integrated_companies.yemek_sepeti.orders.set_status(
                    order_id,
                    type,
                    reason,
                    function (data) {
                      helper_sweet_alert.success(
                        "İşlem Başarılı",
                        `Sipariş  durumu <b>'${type_name}'</b> olarak güncellenmiştir.`
                      );
                    }
                  );
                  break;
              }
            }
          });
        });
      }
      self.get_categories();
      self.get_payment_types();
      $(self.id_list.PRODUCT_LIST).addClass("gtc-3");
      set_events();

      /*if (typeof app.customize_settings !== "undefined") {
        if (app.customize_settings.enableBarcodeSystem) {
          $("[function=read_barcode]").show();
        } else {
          $("[function=read_barcode]").attr("function", "0");
        }
      }*/
    },
  };

  let integrations = {
    id_list: {
      TABLE_GROUP: "#table_group_integrations",
    },
    class_list: {
      TABLE: ".e_table_integrations",
      PRODUCTS_INTEGRATE: ".e_product_order_integration",
    },
    variable_list: {
      SELECTED_INTEGRATE_TYPE: 0,
    },
    yemek_sepeti: {
      class_list: {
        TABLE_GROUP: ".e_table_group_yemek_sepeti",
      },
      variable_list: {
        DATA: Array(),
        INTERVAL: null,
        SELECTED_MESSAGE_ID: "",
        SELECTED_DATA_INDEX: 0,
      },
      start: function () {
        let self = this;
        self.variable_list.INTERVAL = setInterval(() => {
          self.get_orders();
        }, settings.ajax_timeouts.NORMAL);
      },
      stop: function () {
        let self = this;
        clearInterval(self.variable_list.INTERVAL);
      },
      get_orders: function () {
        let self = this;
        integrated_companies.yemek_sepeti.orders.get((data) => {
          self.variable_list.DATA = typeof data.rows.order !== "undefined" ? data.rows.order : [];
          self.get_elements_table();
        }, true);
      },
      get_order_products: function () {
        let self = this;

        let element = ``;

        let element_html = $(
          `${integrations.id_list.TABLE_GROUP} ${self.class_list.TABLE_GROUP} [order-id="${table_detail.variable_list.SELECTED_ORDER_ID}"]`
        );
        let index = parseInt(element_html.attr("index"));
        console.log(element_html, index, table_detail.variable_list.SELECTED_ORDER_ID);
        let data_order = self.variable_list.DATA[index]["@attributes"];
        let data_product = self.variable_list.DATA[index].product;
        data_product.forEach((product) => {
          let custom_class = "";
          let option_icon = "";
          let options = "[]";
          if (typeof product.option !== "undefined" && product.option.length > 0) {
            option_icon = "<i class='fa fa-bars'></i>";
            options = JSON.stringify(product.option);
          }
          product = product["@attributes"];
          let product_integrated = array_list.find(
            main.data_list.PRODUCTS_INTEGRATE,
            product.id,
            "product_id_integrated"
          );
          if (typeof product_integrated === "undefined")
            custom_class = "order-product-integrate-not-matching";
          element += `
                    <tr class="e_product_order_integration ${custom_class}"
                    options='${options}'
                    product-name="${product.Name}">
                        <td class="e_order_table_hide_column" style="display: none;"></td>
                        <td>${parseInt(product.Quantity)}</td>
                        <td function="name">${option_icon} ${product.Name}</td>
                        <td>${product.ListPrice.toFixed(2) + main.data_list.CURRENCY}</td> 
                    </tr>
                    `;
        });

        return `
                    <tbody class="e_order_body" style="display: contents;">
                        <tr class="e_order_title selected" style="background: #0d4197">
                            <td colspan="3">
                                <span>Yemek Sepeti: ${data_order.CustomerName} </span> 
                                <span>No: ${
                                  data_order.Id
                                }</span> <span>Toplam: <span>${data_order.OrderTotal.toFixed(
          2
        )}</span>${main.data_list.CURRENCY}</span>
                            </td>
                        </tr>
                        ${element}
                    </tbody>
                `;
      },
      get_elements_table: function () {
        let self = this;

        if (sections.SELECTED_SECTION > 0) return;

        function create_element() {
          let element = ``;

          self.variable_list.DATA.forEach((data, index) => {
            data = data["@attributes"];
            let last_date_time = `${data.DeliveryTime.replace(".", "-")}:00`;
            let date_time_diff = variable.diff_minutes(
              new Date(last_date_time),
              variable_list.NOW_DATE_TIME
            );
            let hour = Math.floor(date_time_diff / 60);
            let minute = date_time_diff - hour * 60;
            let time = `${hour >= 10 ? hour : `0${hour}`}:${minute >= 10 ? minute : `0${minute}`}`;
            element += `
                        <div 
                        integrate-type="1" 
                        order-id="${data.Id}" 
                        index="${index}" 
                        message-id="${data.MessageId}"
                        table-id="${helper.db.branch_tables.YEMEK_SEPETI}"
                        class="e_table_integrations order-table order-table-xl ${
                          table_list.settings.type[0]
                        } p-0">
                            <div class="in ${
                              table_list.settings.order_type[1]
                            } yemek-sepeti order-unconfirmed">             
                                <div class="tc-1 w-100 text-center fw-6">${data.Id}</div>
                                <div class="tc-2 w-100 size-type-lg fw-6 text-center">Yemek Sepeti</div>
                                <div class="tc-3 w-100 size-type-lg fw-6 text-center">
                                   <p class="e_last_time mx-0 mb-0 d-block float-left">${time}</p>
                                   <p class="e_last_price mx-0 mb-0 d-block float-left">${
                                     data.OrderTotal.toFixed(2) + main.data_list.CURRENCY
                                   }</p>
                                </div>
                                <div class="e_total_price tc-4 w-100 size-type-lg fw-6 text-center">${
                                  data.OrderTotal.toFixed(2) + main.data_list.CURRENCY
                                }</div>              
                            </div>
                        </div>
                    `;
          });

          return element;
        }

        $(`${integrations.id_list.TABLE_GROUP} ${self.class_list.TABLE_GROUP}`).html(
          create_element()
        );
      },
    },
    initialize: function () {
      let self = this;

      function set_events() {
        $(document).on("click", self.class_list.TABLE, function () {
          let element = $(this);
          let type = parseInt(element.attr("integrate-type"));
          self.variable_list.SELECTED_INTEGRATE_TYPE = type;
          table_detail.variable_list.SELECTED_ORDER_ID = element.attr("order-id");
          let index = parseInt(element.attr("index"));
          table_detail.variable_list.SELECTED_TABLE_TYPE =
            table_detail.variable_list.table_types.INTEGRATION;
          let table_id = element.attr("table-id");
          table_detail.variable_list.SELECTED_TABLE_ID = table_id;

          let elements = {
            order_products: "",
            section_name: "",
            table_no: "",
            title_info: "",
            address: "",
            table_total: 0,
          };
          switch (type) {
            case helper.db.integrate_types.YEMEK_SEPETI:
              let data = self.yemek_sepeti.variable_list.DATA[index]["@attributes"];
              self.yemek_sepeti.variable_list.SELECTED_MESSAGE_ID = element.attr("message-id");
              self.yemek_sepeti.variable_list.SELECTED_DATA_INDEX = index;
              elements.title_info = `${data.CustomerName} (${data.CustomerPhone})`;
              elements.order_products = self.yemek_sepeti.get_order_products();
              elements.table_no = data.Id;
              elements.section_name = "Yemek Sepeti";
              elements.table_total = data.OrderTotal;
              elements.address = `
                            ${data.Address}-${data.AddressDescription} ${data.City}/${data.Region}
                            <br>${data.OrderNote}
                            <br>${data.PaymentNote}
                            `;
              break;
          }

          $(table_detail.id_list.ORDER_LIST).html(elements.order_products);
          $(`${table_detail.id_list.TABLE_DETAILS} ${table_detail.class_list.TABLE_TITLE}`).html(
            `${elements.section_name.toUpperCase()} ${elements.table_no}`
          );
          $(
            `${table_detail.id_list.TABLE_DETAILS} ${table_detail.class_list.TABLE_PRICE_TOTAL} [function='price']`
          ).html(`${elements.table_total.toFixed(2)}`);
          $(
            `${table_detail.id_list.TABLE_DETAILS} ${table_detail.class_list.TABLE_PRICE_TOTAL} [function='currency']`
          ).html(`${main.data_list.CURRENCY}`);
          $(table_detail.class_list.TABLE_TITLE_INFO).html(elements.title_info);
          $(`${table_detail.class_list.TAKE_AWAY_INFO} [function="address"]`).html(
            elements.address
          );
          $(`
                        ${table_detail.class_list.ORDER_BTN}[function='delete_product_cancel'],
                        ${table_detail.class_list.ORDER_BTN}[function='catering_product_cancel'],
                        ${table_detail.class_list.ORDER_BTN}[function='separate_product_cancel'],
                        ${table_detail.class_list.ORDER_BTN}[function='change_price_cancel']
                    `).trigger("click");
          $(".e_takeaway_confirm_area").show();
          $(".e_order_btn[function]").hide();
          $(`${table_detail.class_list.ORDER_BTN}:not([table-take-away])`).hide();
          $(`${table_detail.class_list.TAKE_AWAY_INFO}`).show();
          table_detail.get_product();
          $(table_list.id_list.TABLES).hide();
          $(table_detail.id_list.TABLE_DETAILS).show();
          navbar.is_enable();
        });

        $(document).on("click", self.class_list.PRODUCTS_INTEGRATE, function () {
          let element_html = $(this);
          let options = JSON.parse(element_html.attr("options"));
          let name = element_html.attr("product_name");

          function create_element() {
            let element = `<ul>`;

            options.forEach((data) => {
              let not_matching = "";
              data = data["@attributes"];
              let option_integrated = array_list.find(
                main.data_list.PRODUCT_OPTIONS_INTEGRATE,
                data.Id,
                "option_id_integrated"
              );
              if (typeof option_integrated === "undefined")
                not_matching = `<b class="order-product-option-integrate-not-matching">(Eşleştirilmemiş Opsiyon!)</b>`;
              element += `<li>${data.Name} ${not_matching}</li>`;
            });

            return `${element} </ul>`;
          }

          if (options.length > 0) {
            $.confirm({
              title: name,
              backgroundDismiss: false,
              content: create_element(),
              type: "red",
              typeAnimated: true,
              buttons: {
                okay: {
                  text: "Tamam",
                  action: function () {},
                },
              },
            });
          }
        });
      }

      set_events();
      if (integrated_companies.yemek_sepeti !== null) {
        self.yemek_sepeti.get_orders();
        self.yemek_sepeti.start();
      }
    },
  };

  function set(set_type, data, success_function) {
    helper_sweet_alert.wait(
      language.data.PROCESS_PROGRESS_TITLE,
      language.data.PROCESS_WAIT_CONTENT
    );
    data["set_type"] = set_type;
    $.ajax({
      url: `${default_ajax_path}set.php`,
      type: "POST",
      data: data,
      success: function (data) {
        console.log(data);
        success_function(data);
      },
      error: helper_sweet_alert.close(),
      timeout: settings.ajax_timeouts.NORMAL,
    });
  }

  return orders;
})();

$(function () {
  let _orders = new orders();
});
