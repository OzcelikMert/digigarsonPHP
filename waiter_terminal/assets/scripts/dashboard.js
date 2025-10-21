let page_dashboard = (function () {
  let default_ajax_path = `${settings.paths.primary.PHP}dashboard/`;
  let default_ajax_path_pos = `../pos/${settings.paths.primary.PHP}orders/`;
  let variable_list = {
    NOTIFICATION: null,
  };
  let id_list = {
    HEADER: "#header",
    RESTART_PAGE: "#restart_page",
  };
  let set_types = {
    INSERT: 0x0001,
    TABLE_MOVE: 0x0002,
    SIGN_OUT: 0x0101,
    UPDATE_CONFIRM_ACCOUNT_ID: 0x0011,
    PRINT_INVOICE: 0x0102,
  };

  function page_dashboard() {
    initialize();
  }

  function initialize() {
    page_main.initialize();
    page_table_detail.initialize();
    page_move_table.initialize();
    page_basket.initialize();
    variable_list.NOTIFICATION = setInterval(() => {
      get_new_orders();
      get_new_requests();
      page_main.get_tables();
      console.log("NOTIFICATION interval");
    }, settings.ajax_timeouts.NORMAL);
  }

  function get_new_orders() {
    main.data_list.ORDERS.forEach((order) => {
      console.log(JSON.stringify(order));
      if (order.confirmed_account_id != 0) return;
      let table = array_list.find(main.data_list.TABLES, parseInt(order.table_id), "id");
      if (typeof table === "undefined") return;
      console.log(JSON.stringify(table));
      if (
        typeof array_list.find(main.data_list.APP_TABLE_SECTIONS, table.section_id) !== "undefined"
      )
        return;
      let section = array_list.find(main.data_list.SECTIONS, table.section_id, "id");
      let section_type = array_list.find(main.data_list.SECTION_TYPES, section.section_id, "id");
      let notification = {
        title: language.data.NEW_ORDER,
        message: `${language.data.TABLE} ${section_type.name}-${table.no} ${language.data.HAVE_ORDER}!`,
      };
      console.log(JSON.stringify(notification));
      application.notifications.show_new_order(JSON.stringify(notification));
      Swal.fire({
        icon: "warning",
        title: notification.title,
        html: notification.message,
        allowEscapeKey: false,
        allowOutsideClick: false,
        showCancelButton: false,
        timer: 2500,
        timerProgressBar: true,
        confirmButtonText: language.data.OK,
        confirmButtonClass: "btn btn-success btn-lg mr-3 mt-5",
        cancelButtonClass: "btn btn-danger btn-lg ml-3 mt-5",
        buttonsStyling: false,
      }).then((result) => {
        if (result.value) {
          set_pos(set_types.UPDATE_CONFIRM_ACCOUNT_ID, { order_id: order.id }, function (data) {
            data = JSON.parse(data);
            console.log(data);
            console.log(data.rows.user_id);
            order.confirmed_account_id = data.rows.user_id;
          });
        }
      });
    });
  }

  function get_new_requests() {}

  function print_invoice() {
    Swal.fire({
      icon: "question",
      title: language.data.ORDER_PRINT,
      html: language.data.PRINT_INVOICE_HTML,
      allowEscapeKey: false,
      allowOutsideClick: false,
      showCancelButton: true,
      confirmButtonText: language.data.ACCEPT,
      cancelButtonText: language.data.CANCEL,
      confirmButtonClass: "btn btn-success btn-lg mr-3 mt-5",
      cancelButtonClass: "btn btn-danger btn-lg ml-3 mt-5",
      buttonsStyling: false,
    }).then((result) => {
      if (result.value) {
        set(
          set_types.PRINT_INVOICE,
          {
            table_id: page_table_detail.variable_list.SELECTED_TABLE_ID,
            order_id: page_table_detail.variable_list.SELECTED_ORDER_ID,
          },
          function (data) {
            data = JSON.parse(data);
            console.log(data);
            if (data.status) {
              helper_sweet_alert.success(
                language.data.ORDER_PRINT,
                language.data.RECEIPT_SENT_PRINTER
              );
            }
          }
        );
      }
    });
  }

  let page_main = {
    id_list: {
      PAGE: "#main",
      SWIPER: "#main-swiper",
      SWIPER_PAGINATION: "#main-swiper-pagination",
      SWIPER_SCROLLBAR: "#main-swiper-scrollbar",
    },
    class_list: {
      TABLE_SECTIONS: ".e_table_sections",
      TABLES: ".e_tables",
      TABLE: ".e_table",
      BUTTON_SETTINGS: ".e_setting_btn",
      BUTTON_SECTION: ".e_section_btn",
      BOTTOM_ICONS: ".e_bottom_icons",
      BUTTON_TABLE: ".e_main_table_btn",
      SECTIONS_FOR_SETTINGS: ".e_table_sections_for_settings",
    },
    variable_list: {
      SWIPER: null,
      SELECTED_SECTION_ID: 0,
    },
    set_swiper: function () {
      let self = this;
      self.variable_list.SWIPER = new Swiper(self.id_list.SWIPER, {
        speed: 250,
        slidesPerView: "auto",
        allowTouchMove: true,
        debugger: false,
        pagination: {
          el: self.id_list.SWIPER_PAGINATION,
          clickable: true,
          renderBullet: function (index, className) {
            let icon = "";
            let firstDisabled = "";
            switch (index) {
              case 0:
                icon = "home";
                break;
              case 1:
                icon = "table-of-contents";
                firstDisabled = "disabled-element";
                break;
              case 2:
                icon = "cogs";
                break;
              default:
                break;
            }

            return `<div class="e_bottom_icons bottom-icons ${className} ${firstDisabled}"><i class="mdi mdi-${icon}"></i></div>`;
          },
        },
        scrollbar: {
          el: self.id_list.SWIPER_SCROLLBAR,
          hide: false,
        },
      });
    },
    get_table_sections: function () {
      let self = this;

      function create_element() {
        let element = `
                    <div class="col-6 p-2">
                        <div class="e_section_btn section-name section-btn" section-id="-1">${language.data.ALL}</div>
                    </div>
                `;

        console.log(main.data_list.APP_TABLE_SECTIONS);
        main.data_list.SECTIONS.forEach((section) => {
          if (section.branch_id === 0) return;
          if (typeof array_list.find(main.data_list.APP_TABLE_SECTIONS, section.id) !== "undefined")
            return;

          let section_type = array_list.find(
            main.data_list.SECTION_TYPES,
            section.section_id,
            "id"
          );
          element += `
                        <div class="col-6 p-2 ">
                            <div class="e_section_btn section-name section-btn" section-id="${section.id}">${section_type.name}</div>
                        </div>
                    `;
        });

        return element;
      }

      $(self.class_list.TABLE_SECTIONS).html(create_element());
    },
    get_table_sections_for_application: function () {
      let self = this;

      function create_element() {
        let element = `
                    <div class="text-dark">
                        <div class="col-12 text-center mb-3">
                            <b>${language.data.DNW_APPEAR_MARK_TABLE}</b>
                        </div>  
                `;

        main.data_list.SECTIONS.forEach((section) => {
          if (section.branch_id === 0) return;
          let checked = "checked";
          if (typeof array_list.find(main.data_list.APP_TABLE_SECTIONS, section.id) === "undefined")
            checked = "";

          let section_type = array_list.find(
            main.data_list.SECTION_TYPES,
            section.section_id,
            "id"
          );
          element += `
                        <div class="col-12 mt-2" section-id="${section.id}">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" id="table_section_${section.id}_settings" class="e_table_sections_for_settings custom-control-input table-sections-for-settings" ${checked}/>
                                <label class="custom-control-label" for="table_section_${section.id}_settings">${section_type.name}</label>
                            </div>
                        </div>
                    `;
        });

        return `
                        ${element} 
                    </div>
`;
      }

      $.confirm({
        title: language.data.TABLE_SECTIONS,
        backgroundDismiss: false,
        content: create_element(),
        type: "red",
        typeAnimated: true,
        buttons: {
          okay: {
            text: language.data.OK,
            action: function () {
              let table_sections = Array();
              $(self.class_list.SECTIONS_FOR_SETTINGS).each(function () {
                if ($(this).prop("checked")) {
                  table_sections.push(parseInt($(this).closest(`[section-id]`).attr("section-id")));
                }
              });
              console.log(JSON.stringify(table_sections));
              application.db.table_sections.set(JSON.stringify(table_sections));
              main.data_list.APP_TABLE_SECTIONS = table_sections;
              self.get_table_sections();
            },
          },
          cancel: {
            text: language.data.CANCEL,
          },
        },
      });
    },
    get_tables: function () {
      let self = this;

      function create_element() {
        let element = ``;

        let sections =
          self.variable_list.SELECTED_SECTION_ID > 0
            ? array_list.find_multi(
                main.data_list.SECTIONS,
                self.variable_list.SELECTED_SECTION_ID,
                "id"
              )
            : main.data_list.SECTIONS;

        sections.forEach((section) => {
          let section_type = array_list.find(
            main.data_list.SECTION_TYPES,
            section.section_id,
            "id"
          );
          let tables = array_list.find_multi(main.data_list.TABLES, section.id, "section_id");
          tables.forEach((table) => {
            if (table.branch_id === 0) return;
            let orders = array_list.find_multi(main.data_list.ORDERS, table.id, "table_id");
            let time = "";
            let last_time = "";
            let last_date = "";
            let bg = "";
            let isFill = false;
            let function_buttons = "hidden-and-events-none";
            if (orders.length > 0) {
              function_buttons = "";
              isFill = true;
              orders.forEach((order) => {
                let order_products = array_list.find_multi(
                  main.data_list.ORDER_PRODUCTS,
                  order.id,
                  "order_id"
                );
                order_products.forEach((order_product) => {
                  if (last_time <= order_product.time) {
                    last_time = order_product.time;
                    last_date = order.date_start.slice(0, 10);
                  }
                });
                bg = order.is_print == 1 ? "order-printed" : "";
              });

              if (last_time !== "") {
                let last_date_time = `${last_date} ${last_time}:00`;
                console.log(last_date_time, variable_list.NOW_DATE_TIME);

                let date_time_diff = variable.diff_minutes(
                  new Date(last_date_time),
                  new Date(variable.date_format(new Date(), "yyyy-mm-dd HH:MM:00"))
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
            }

            element += `
                            <div class="e_table col-12 mt-1 p-4 order-table ${
                              isFill ? "fill" : ""
                            } ${bg} " table-id="${table.id}">
                                <div class="row">
                                    <div class="col-5 text-left">${section_type.name}-${
              table.no
            }</div>
                                    <div class="col-2 pl-1 pr-0">
                                        <button type="button" class="e_main_table_btn btn btn-outline-danger btn-sm ${function_buttons} main-page-table-btn" function="print"">${
              language.data.PRINT
            }</button>
                                    </div>
                                    <div class="col-2 text-center pl-1 pr-0">
                                        <button type="button" class="e_main_table_btn btn btn-outline-light btn-sm ${function_buttons} main-page-table-btn" function="move">${
              language.data.MOVE
            }</button>
                                    </div>
                                    <div class="col-3 pl-1 pr-1">
                                        <button  type="button" class="e_main_table_btn btn  btn-table-open btn-sm main-page-table-btn" function="show">${
                                          language.data.OPEN_TABLE
                                        }</button>
                                    </div>
                                </div>
                            </div>
                        `;
          });
        });

        return element;
      }

      $(self.class_list.TABLES).html(create_element());
    },
    get: function () {
      let self = this;

      self.get_tables();
      $(self.id_list.PAGE).show(0);
      $(id_list.HEADER).show(0);
      self.SWIPER.update();
    },
    initialize: function () {
      let self = this;

      function set_events() {
        $(document).on("click", self.class_list.BUTTON_SECTION, function () {
          self.variable_list.SELECTED_SECTION_ID = parseInt($(this).attr("section-id"));
          $(self.class_list.BOTTOM_ICONS).removeClass("disabled-element");
          self.get_tables();
          self.variable_list.SWIPER.slideNext(250, true);
        });

        $(document).on("click", self.class_list.BUTTON_TABLE, function () {
          let element = $(this);
          let function_name = element.attr("function");

          page_table_detail.variable_list.SELECTED_TABLE_ID = parseInt(
            element.closest(self.class_list.TABLE).attr("table-id")
          );
          page_table_detail.variable_list.SELECTED_ORDER_ID = 0;
          switch (function_name) {
            case "print":
              print_invoice();
              break;
            case "move":
              page_move_table.get();
              break;
            case "show":
              page_table_detail.get();
              break;
          }
        });

        $(document).on("click", self.class_list.BUTTON_SETTINGS, function () {
          let element = $(this);
          let function_name = element.attr("function");

          switch (function_name) {
            case "select_table_sections":
              self.get_table_sections_for_application();
              break;
            case "select_tables":
              break;
            case "exit":
              set(set_types.SIGN_OUT, {}, function (data) {
                data = JSON.parse(data);
                application.db.accounts.set(0, false);
                location.reload();
              });
              break;
          }
        });

        $(document).on("click", id_list.RESTART_PAGE, function () {
          $.confirm({
            icon: "mdi mdi-help",
            title: `<b class="text-dark">${language.data.REFRESH}</b>`,
            backgroundDismiss: false,
            content: `<p class="text-dark">${language.data.REFRESH_QUESTION}</p>`,
            type: "red",
            typeAnimated: true,
            buttons: {
              okay: {
                text: language.data.ACCEPT,
                action: function () {
                  window.location.reload();
                },
              },
              cancel: {
                text: language.data.CANCEL,
              },
            },
          });
        });
      }

      self.set_swiper();
      self.get_table_sections();
      set_events();
    },
  };

  let page_table_detail = {
    id_list: {
      PAGE: "#table_detail",
      SWIPER: "#table-swiper",
      SWIPER_PAGINATION: "#table-swiper-pagination",
      SWIPER_SCROLLBAR: "#table-swiper-scrollbar",
      FORM_PRODUCT_OPTION: "#product_option_form",
      BASKET_COUNT: "#table_basket_count",
      SEARCH_PRODUCT: "#search_product",
      SEARCH_CATEGORY: "#search_category",
    },
    class_list: {
      INFO: ".e_table_info",
      TOTAL_PRICE: ".e_table_total",
      ORDERS: ".e_orders",
      PRODUCT_CATEGORIES: ".e_product_categories",
      BUTTON_PRODUCT_CATEGORY: ".e_product_category_btn",
      PRODUCTS: ".e_products",
      BOTTOM_ICONS: ".e_bottom_icons_table",
      BUTTON_PRODUCT: ".e_product_btn",
      QUANTITY: ".e_quantity",
      OPTIONS: ".e_options",
      BUTTON_PRODUCT_PIECE: ".e_product_piece_btn",
      PRODUCT_PIECE: ".e_product_piece",
      BUTTON_PAGE: ".e_table_page_btn",
      BUTTON_ORDER: ".e_table_page_order_btn",
      BUTTON_ORDER_PRODUCT: ".e_table_page_order_product_btn",
      BUTTON_ADD_NEW_ORDER: ".e_add_new_order",
    },
    variable_list: {
      SWIPER: null,
      PAGE: null,
      SELECTED_TABLE_ID: 0,
      SELECTED_ORDER_ID: 0,
      SELECTED_PRODUCT_CATEGORY_ID: 0,
      SELECTED_PRODUCT_ID: 0,
      TOAST: null,
      SEARCH_PRODUCT: "",
      SEARCH_CATEGORY: "",
    },
    set_toast: function () {
      let self = this;

      self.variable_list.TOAST = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true,
        onOpen: (toast) => {
          toast.addEventListener("mouseenter", Swal.stopTimer);
          toast.addEventListener("mouseleave", Swal.resumeTimer);
        },
      });
    },
    set_page: function () {
      let self = this;
      self.variable_list.PAGE = new custom_pop_up(
        self.id_list.PAGE,
        "animate__slideInRight",
        "animate__slideOutLeft"
      );
    },
    set_swiper: function () {
      let self = this;
      self.variable_list.SWIPER = new Swiper(self.id_list.SWIPER, {
        speed: 250,
        slidesPerView: "auto",
        allowTouchMove: true,
        debugger: false,
        pagination: {
          el: self.id_list.SWIPER_PAGINATION,
          clickable: true,
          renderBullet: function (index, className) {
            let icon = "";
            let firstDisabled = "";
            switch (index) {
              case 0:
                icon = "room-service";
                break;
              case 1:
                icon = "tag";
                break;
              case 2:
                icon = "food";
                firstDisabled = "disabled-element table-products-icon";
                break;
              default:
                break;
            }

            return `<div class="e_bottom_icons_table bottom-icons table-bottom-icons ${className} ${firstDisabled}"><i class="mdi mdi-${icon}"></i></div>`;
          },
        },
        scrollbar: {
          el: self.id_list.SWIPER_SCROLLBAR,
          hide: false,
        },
      });
    },
    set_order_print: function () {
      Swal.fire({
        icon: "success",
        title: language.data.ORDER_PRINT,
        html: language.data.SUCCESS_RECEIPT_PRINTED,
        allowEscapeKey: true,
        allowOutsideClick: false,
        showCancelButton: false,
        confirmButtonText: language.data.OK,
        confirmButtonClass: "btn btn-success btn-lg mr-3 mt-5",
        buttonsStyling: false,
      });
    },
    get_orders: function () {
      let self = this;
      let table_total = 0.0;

      function create_element() {
        let element = ``;

        let orders = array_list.find_multi(
          main.data_list.ORDERS,
          self.variable_list.SELECTED_TABLE_ID,
          "table_id"
        );

        orders.forEach((order) => {
          let total = 0.0;
          let products_element = ``;

          let order_products = array_list.find_multi(
            main.data_list.ORDER_PRODUCTS,
            order.id,
            "order_id"
          );

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
            let option_icon = "hidden-and-events-none";
            if (
              order_product_options.length > 0 ||
              order_product.comment !== "" ||
              order_product.quantity != 1
            ) {
              option_icon = "";
            }
            if (order_product.status === helper.db.order_product_status_types.CATERING) {
              price = 0;
              catering_class = "disabled-element";
            }

            total += price;

            products_element += `
                            <div class="col-12 order-info-products pb-1 ${catering_class}" order-product-id="${
              order_product.id
            }">
                                <div class="row">
                                    <div class="col-2 p-0 pl-1 text-left">
                                        <!--button class="e_table_page_order_product_btn btn btn-success btn-sm w-100 table-page-order-product-btn" function="move">Taşı</button-->
                                    </div>
                                    <div class="col-1 p-0 pl-2 text-left">${
                                      order_product.qty
                                    }x</div>
                                    <div class="col-1 p-0 text-left order-info-product-option-icon">
                                        <i class="e_table_page_order_product_btn mdi mdi-format-list-checkbox ${option_icon} table-page-order-product-btn" function="show"></i>
                                    </div>
                                    <div class="col-8 p-0 pr-1 text-right">
                                        <font class="float-left" id="order_product_${
                                          order_product.id
                                        }_info">${product.name}</font>${
              price.toFixed(2) + main.data_list.CURRENCY
            }
                                    </div>
                                </div>
                            </div>
                        `;
          });

          let payments = array_list.find_multi(main.data_list.PAYMENTS, order.id, "order_id");

          let payed_total = 0;
          payments.forEach((payment) => {
            payed_total += parseFloat(payment.price);
          });

          table_total += total - payed_total;

          element += `
                        <div class="e_order col-12 order" order-id="${order.id}">
                            <div class="row" id="order_${
                              order.id
                            }" data-toggle="collapse" data-target="#order_${
            order.id
          }_values" aria-expanded="true" aria-controls="order_${order.id}_values">
                                <div class="col-3 pl-1 pr-0" id="order_id_${order.id}_info">No: ${
            order.id
          }</div>
                                <div class="col-3 pl-1 pr-0">${
                                  payed_total > 0
                                    ? `<small><del class="mr-2">${total.toFixed(2)}</del></small>`
                                    : ``
                                }${
            (total - payed_total).toFixed(2) + main.data_list.CURRENCY
          }</span></div>
                                <div class="col-2 pl-1 pr-0">
                                    <button class="e_table_page_order_btn btn btn-danger btn-sm w-100 table-page-order-btn" function="print"><i class="fa fa-print"></i> ${
                                      language.data.PRINT
                                    }</button>
                                </div>
                                <div class="col-2 pl-1 pr-1">
                                    <button class="e_table_page_order_btn btn btn-success btn-sm w-100 table-page-order-btn" function="move"><i class="fa fa-arrow-left"></i> ${
                                      language.data.MOVE
                                    }</button>
                                </div>
                                <div class="col-2 pl-1 pr-1">
                                    <button class="e_add_new_order btn btn-primary btn-sm w-100 add_new_order" function="stated"><i class="mdi mdi-plus"></i>${
                                      language.data.ADD
                                    }</button>
                                </div>
                            </div>
                            <div id="order_${
                              order.id
                            }_values" class="collapse row mt-2 order-info" aria-labelledby="order_${
            order.id
          }" data-parent='#orders'>${products_element}</div>
                        </div>
                    `;
        });

        if (element === "") {
          element = `
                        <div class="free-order">
                            ${language.data.NO_CONFIRMED_ORDER}
                            <i class="mdi mdi-emoticon-sad-outline"></i>
                        </div>
                    `;
        }

        return element;
      }

      $(self.class_list.ORDERS).html(create_element());
      $(self.class_list.TOTAL_PRICE).html(`${table_total.toFixed(2) + main.data_list.CURRENCY}`);
    },
    get_order_product_options: function () {
      let self = this;

      let order_product = array_list.find(
        main.data_list.ORDER_PRODUCTS,
        self.variable_list.SELECTED_PRODUCT_ID,
        "id"
      );
      let product = array_list.find(main.data_list.PRODUCTS, order_product.product_id, "id");

      function create_element() {
        let element = `
                    <div class="container text-dark">
                        <div class="row" id="order_product_options">
                `;

        let order_product_options = array_list.find_multi(
          main.data_list.ORDER_PRODUCT_OPTIONS,
          order_product.id,
          "order_product_id"
        );

        if (order_product.quantity !== 1) {
          let product_quantity = array_list.find(
            main.data_list.PRODUCT_QUANTITY_TYPES,
            product.quantity_id,
            "id"
          );
          element += `<div class="col-12 order-item text-center"><b>${order_product.quantity} ${product_quantity.name}</b></div>`;
        }

        if (order_product.comment !== "") {
          element += `<div class="col-12 order-item">${language.data.DESCRIPTION}: <b>${order_product.comment}</b></div>`;
        }

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
                        <div class="col-12 basket-item">
                            <div class="basket-item">
                                <font class="float-left mr-2">[${option.name}]-> ${
            order_product_option.qty
          }x ${option_item.name}</font> ${
            order_product_option.price != 0
              ? `[${order_product_option.price > 0 ? `+` : ``}${
                  order_product_option.price + main.data_list.CURRENCY
                }]`
              : ``
          }
                            </div>
                        </div>
                    `;
        });

        return element;
      }

      $.confirm({
        title: product.name,
        backgroundDismiss: false,
        content: create_element(),
        type: "red",
        typeAnimated: true,
        buttons: {
          okay: {
            text: language.data.OK,
          },
        },
      });
    },
    get_product_categories: function () {
      let self = this;

      function create_element() {
        let element =
          self.variable_list.SEARCH_CATEGORY.length === 0
            ? `
                        <div class="col-6 mt-2" category-id="0">
                            <button type='button' class="e_product_category_btn btn product-category-btn w-100">${language.data.FAVORITES}</button>
                        </div>
                        <div class="col-6 mt-2" category-id="-1">
                            <button type='button' class="e_product_category_btn btn product-category-btn w-100">${language.data.CAMPAIGNS}</button>
                        </div>
                    `
            : ``;

        main.data_list.PRODUCT_CATEGORIES.forEach((product_category) => {
          if (
            self.variable_list.SEARCH_CATEGORY.length > 0 &&
            !String(product_category.name.toLocaleLowerCase("tr")).match(
              new RegExp(self.variable_list.SEARCH_CATEGORY.toLocaleLowerCase("tr"), "gi")
            )
          )
            return;
          if (
            typeof array_list.find(main.data_list.PRODUCTS, product_category.id, "category_id") ===
            "undefined"
          )
            return;
          element += `
                        <div class="col-6 mt-2" category-id="${product_category.id}">
                            <button type='button' class="e_product_category_btn btn product-category-btn w-100">${product_category.name}</button>
                        </div>
                    `;
        });

        return element;
      }

      $(self.class_list.PRODUCT_CATEGORIES).html(create_element());
    },
    get_products: function () {
      let self = this;

      function create_element() {
        let element = ``;

        let products =
          self.variable_list.SEARCH_PRODUCT.length > 0
            ? main.data_list.PRODUCTS
            : self.variable_list.SELECTED_PRODUCT_CATEGORY_ID === 0
            ? array_list.find_multi(main.data_list.PRODUCTS, 1, "favorite")
            : array_list.find_multi(
                main.data_list.PRODUCTS,
                self.variable_list.SELECTED_PRODUCT_CATEGORY_ID,
                "category_id"
              );

        products.forEach((product) => {
          if (product.is_delete == 1) return;
          if (
            self.variable_list.SEARCH_PRODUCT.length > 0 &&
            !String(product.name.toLocaleLowerCase("tr")).match(
              new RegExp(self.variable_list.SEARCH_PRODUCT.toLocaleLowerCase("tr"), "gi")
            )
          )
            return;
          element += `
                        <div class="e_product_btn col-12 mt-2 product-bar product-btn" product-id="${product.id}">
                            <div class="row">
                                <div class="col-1 pl-1 pr-0">
                                    <i class="mdi mdi-food-fork-drink"></i>
                                </div>
                                <div class="col-9 pl-1 pr-0 product-name">
                                    ${product.name}
                                </div>
                                <div class="col-2 pl-1 pr-1 text-right">
                                    <font id="product_${product.id}_price">${product.price}</font>${main.data_list.CURRENCY}
                                </div>
                            </div>
                        </div>
                    `;
        });

        return element;
      }

      $(self.class_list.PRODUCTS).html(create_element());
    },
    get_product_details: function () {
      let self = this;

      let product = array_list.find(
        main.data_list.PRODUCTS,
        self.variable_list.SELECTED_PRODUCT_ID,
        "id"
      );

      function create_element() {
        let element = ``;

        let quantity = array_list.find(
          main.data_list.PRODUCT_QUANTITY_TYPES,
          product.quantity_id,
          "id"
        );

        element +=
          quantity.id > 1
            ? `
                    <div class="col-12 mt-3 e_quantity">
                        <h3 class="mb-1">${quantity.name}</h3>
                        <div class='form-group'>
                            <input type="number" value="1" name="quantity" min="0.00" step="0.01" placeholder="0.00" class="form-control w-100" required>
                        </div>
                        <div class="quantity-btn pb-2 row">
                            <button type="button" class="btn btn-outline-warning w-100 col-3" quantity="0.5">0.5</button>
                            <button type="button" class="btn btn-outline-warning w-100 col-3" quantity="1">1</button>
                            <button type="button" class="btn btn-outline-warning w-100 col-3" quantity="1.5">1.5</button>
                            <button type="button" class="btn btn-outline-warning w-100 col-3" quantity="2">2</button>
                        </div>
                    </div>
                `
            : ``;

        let linked_options = array_list.find_multi(
          main.data_list.PRODUCT_LINKED_OPTIONS,
          self.variable_list.SELECTED_PRODUCT_ID,
          "product_id"
        );

        element += `<div class="e_options col-12">`;

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
            element += `
                            <div class="option col-12 mt-3" option-type="${
                              option.type
                            }" option-limit="${linked_option.max_count}" option-id="${option.id}">
                                <p class="mb-1 pt-1">${option.name} ${
              linked_option.max_count > 0
                ? `<b>Limit: <span class="e_option_count">${linked_option.max_count}</span></b>`
                : ``
            }</p>
                                <div class="row">
                        `;

            option_items.forEach((option_item) => {
              element += `
                                <button type="button" class="btn btn-outline-primary ${
                                  option_item.is_default == 1 ? `selected` : ``
                                } mt-2 col-md-4" item-id="${option_item.id}" price="${
                option_item.price
              }">${option_item.name} ${
                option_item.price != 0
                  ? `[` +
                    (option_item.price > 0 ? `+` : ``) +
                    `${option_item.price + main.data_list.CURRENCY}]`
                  : ``
              }</button>
                            `;
            });

            element += `
                                </div>
                            </div>
                        `;
          });
        });

        element += `</div>`;

        return `
                    <div class='container'>
                        <div class='row'>
                            <div class='col-12'>
                                <form id="product_option_form">
                                    <div class='row'>
                                        <div class='col-4'>
                                            <button type="button" class='e_product_piece_btn btn btn-danger' function="minus">
                                                <i class='mdi mdi-minus'></i>
                                            </button>
                                        </div>
                                        <input type="number" class="e_product_piece col-4 table-basket-product-piece border-0 text-center" name="qty" readonly value="1">
                                        <div class='col-4'>
                                            <button type="button" class='e_product_piece_btn btn btn-success' function="plus">
                                                <i class='mdi mdi-plus'></i>
                                            </button>
                                        </div>
                                        <div class='col-12 mt-5'>
                                            <div class='row'>
                                                <div class='col-12'>
                                                    <div class='form-group'>
                                                        <input type='text' class='form-control w-100' name="comment" placeholder='${language.data.DESCRIPTION}' maxlength='70' onclick=''>
                                                    </div>
                                                </div>
                                                ${element}
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div> 
                `;
      }

      Swal.fire({
        title: product.name,
        html: create_element(),
        allowEscapeKey: false,
        allowOutsideClick: false,
        showCancelButton: true,
        confirmButtonText: "Onayla",
        cancelButtonText: "İptal",
        confirmButtonClass: "btn btn-success btn-lg mr-3 mt-5",
        cancelButtonClass: "btn btn-danger btn-lg ml-3 mt-5",
        buttonsStyling: false,
      }).then((result) => {
        if (result.value) {
          $(self.id_list.FORM_PRODUCT_OPTION).trigger("submit");
        }
      });
    },
    get: function () {
      let self = this;

      let table = array_list.find(
        main.data_list.TABLES,
        self.variable_list.SELECTED_TABLE_ID,
        "id"
      );
      let section = array_list.find(main.data_list.SECTIONS, table.section_id, "id");
      let section_type = array_list.find(main.data_list.SECTION_TYPES, section.section_id, "id");

      self.get_orders();
      self.get_product_categories();

      $(self.class_list.INFO).html(`${section_type.name}-${table.no}`);
      self.variable_list.PAGE.open();
      self.variable_list.SWIPER.slideTo(0, 1, false);
      self.variable_list.SWIPER.update();
      $(page_main.id_list.PAGE).delay(500).hide(0);
      $(id_list.HEADER).delay(500).hide(0);
    },
    close: function () {
      let self = this;

      function _close() {
        self.variable_list.SELECTED_ORDER_ID = 0;
        self.variable_list.SELECTED_TABLE_ID = 0;
        self.variable_list.SELECTED_PRODUCT_ID = 0;
        self.variable_list.PAGE.close();
        page_main.get();
      }

      if (page_basket.variable_list.DATA.length > 0) {
        Swal.fire({
          icon: "warning",
          title: language.data.BASKET_FULL,
          //TODO TRANSLATE
          html: "Sepet içerisinde adisyona eklenmemiş ürünler mevcuttur. Masa içerisinden çıkmak istediğinizden emin misiniz?",
          allowEscapeKey: false,
          allowOutsideClick: false,
          showCancelButton: true,
          confirmButtonText: language.data.ACCEPT,
          cancelButtonText: language.data.CANCEL,
          confirmButtonClass: "btn btn-success btn-lg mr-3 mt-5",
          cancelButtonClass: "btn btn-danger btn-lg ml-3 mt-5",
          buttonsStyling: false,
        }).then((result) => {
          if (result.value) {
            page_basket.variable_list.DATA = Array();
            $(self.id_list.BASKET_COUNT).html(page_basket.variable_list.DATA.length);
            _close();
          }
        });
      } else {
        _close();
      }
    },
    initialize: function () {
      let self = this;

      function set_events() {
        $(self.id_list.SEARCH_PRODUCT).on("keyup change", function () {
          self.variable_list.SEARCH_PRODUCT = $(this).val();
          self.get_products();
        });

        $(self.id_list.SEARCH_CATEGORY).on("keyup change", function () {
          self.variable_list.SEARCH_CATEGORY = $(this).val();
          self.get_product_categories();
        });

        $(document).on("click", self.class_list.BUTTON_PRODUCT_CATEGORY, function () {
          self.variable_list.SELECTED_PRODUCT_CATEGORY_ID = parseInt(
            $(this).closest(`[category-id]`).attr("category-id")
          );
          $(self.class_list.BOTTOM_ICONS).removeClass("disabled-element");
          self.get_products();
          self.variable_list.SWIPER.slideNext(250, true);
        });

        $(document).on("click", self.class_list.BUTTON_PRODUCT, function () {
          self.variable_list.SELECTED_PRODUCT_ID = parseInt($(this).attr("product-id"));
          console.log(self.variable_list.SELECTED_PRODUCT_ID);
          self.get_product_details();
        });

        $(document).on("click", `${self.class_list.QUANTITY} button`, function () {
          let quantity = parseFloat($(this).attr("quantity"));
          $(`${self.class_list.QUANTITY} input[name='quantity']`).val(quantity);
        });

        $(document).on("click", `${self.class_list.OPTIONS} button`, function () {
          let element = $(this);
          let closest = element.closest("[option-type]");
          let element_selected = closest.children("div").children("button.selected");
          let type = parseInt(closest.attr("option-type"));
          let option_id = parseInt(closest.attr("option-id"));
          let limit = parseInt(closest.attr("option-limit"));
          let select_id = parseInt(element.attr("item-id"));
          let price = parseFloat(element.attr("price"));
          console.log(type, option_id, limit, select_id, price);
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

        $(document).on("click", self.class_list.BUTTON_PRODUCT_PIECE, function () {
          let function_name = $(this).attr("function");

          let piece = 0;
          switch (function_name) {
            case "plus":
              piece = 1;
              break;
            case "minus":
              piece = -1;
              break;
          }

          let product_piece = parseInt($(self.class_list.PRODUCT_PIECE).val());
          product_piece += piece;
          if (product_piece < 1) {
            product_piece = 1;
          }
          $(self.class_list.PRODUCT_PIECE).val(product_piece);
        });

        $(document).on("submit", self.id_list.FORM_PRODUCT_OPTION, function (e) {
          e.preventDefault();
          let element = $(this);
          let product = array_list.find(
            main.data_list.PRODUCTS,
            self.variable_list.SELECTED_PRODUCT_ID,
            "id"
          );

          function get_data() {
            let data = {
              id: product.id,
              quantity: 1,
              vat: product.vat,
              discount: 0,
              qty: 1,
              comment: "",
              options: Array(),
              price: product.price,
            };
            data = Object.assign(data, element.serializeObject());

            let qty = data.qty;
            data.price *= qty;
            data.price *= data.quantity;

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

          page_basket.variable_list.DATA.push(get_data());
          console.log(page_basket.variable_list.DATA);
          $(self.id_list.BASKET_COUNT).html(page_basket.variable_list.DATA.length);
          $(self.id_list.SEARCH_PRODUCT).val("").trigger("change");
          self.variable_list.TOAST.fire({
            icon: "success",
            title: language.data.ADD_SUCCESSFUL,
            text: `${product.name} ${language.data.PRODUCT_ADD_TO_CART}.`,
          });
        });

        $(document).on("click", self.class_list.BUTTON_PAGE, function () {
          let function_name = $(this).attr("function");

          switch (function_name) {
            case "back":
              self.close();
              break;
            case "show":
              page_basket.open();
              break;
          }
        });

        $(document).on("click", self.class_list.BUTTON_ORDER, function (e) {
          let function_name = $(this).attr("function");

          self.variable_list.SELECTED_ORDER_ID = parseInt(
            $(this).closest(`[order-id]`).attr("order-id")
          );

          switch (function_name) {
            case "print":
              print_invoice();
              break;
            case "move":
              page_move_table.get();
              break;
          }

          e.stopPropagation();
        });

        $(document).on("click", self.class_list.BUTTON_ORDER_PRODUCT, function (e) {
          let function_name = $(this).attr("function");

          self.variable_list.SELECTED_PRODUCT_ID = parseInt(
            $(this).closest(`[order-product-id]`).attr("order-product-id")
          );

          switch (function_name) {
            case "move":
            /* let Order = Element.attr("order");
                            let Customer = Element.attr("customer");
                            let OrderSerial = Element.attr("order-serial");
                            setMoveTableValuesForOrderItem(Order, Customer, OrderSerial);
                            break;*/
            case "show":
              self.get_order_product_options();
              break;
          }

          e.stopPropagation();
        });

        $(document).on("click", self.class_list.BUTTON_ADD_NEW_ORDER, function (e) {
          let function_name = $(this).attr("function");

          switch (function_name) {
            case "new":
              self.variable_list.SELECTED_ORDER_ID = 0;
              break;
            case "stated":
              self.variable_list.SELECTED_ORDER_ID = parseInt(
                $(this).closest(`[order-id]`).attr("order-id")
              );
              break;
          }

          self.variable_list.SWIPER.slideNext(250, true);
          e.stopPropagation();
        });
      }

      self.set_toast();
      self.set_swiper();
      self.set_page();
      set_events();
    },
  };

  let page_move_table = {
    id_list: {
      PAGE: "#move_table",
      TABLES: "#move_tables",
    },
    class_list: {
      BUTTON_TABLE: ".e_move_table_btn",
      BUTTON_PAGE: ".e_move_page_btn",
    },
    variable_list: {
      PAGE: null,
    },
    set_page: function () {
      let self = this;
      self.variable_list.PAGE = new custom_pop_up(
        self.id_list.PAGE,
        "animate__slideInUp",
        "animate__slideOutDown"
      );
    },
    close: function () {
      let self = this;

      self.variable_list.PAGE.close();
    },
    get: function () {
      let self = this;

      function create_element() {
        let element = ``;

        let tables =
          page_main.variable_list.SELECTED_SECTION_ID > 0
            ? array_list.find_multi(
                main.data_list.TABLES,
                page_main.variable_list.SELECTED_SECTION_ID,
                "section_id"
              )
            : main.data_list.TABLES;

        tables.forEach((table) => {
          if (
            table.branch_id === 0 ||
            table.id === page_table_detail.variable_list.SELECTED_TABLE_ID
          )
            return;
          let section = array_list.find(main.data_list.SECTIONS, table.section_id, "id");
          let section_type = array_list.find(
            main.data_list.SECTION_TYPES,
            section.section_id,
            "id"
          );
          let orders = array_list.find_multi(main.data_list.ORDERS, table.id, "table_id");

          let table_status = "";
          if (orders.length > 0) {
            table_status = "fill";
          }

          element += `
                        <div class="e_table col-12 mt-1 p-3 order-table ${table_status} border-bottom" table-id="${table.id}">
                            <div class="row">
                                <div class="col-10 text-left">${section_type.name}-${table.no}</div>
                                <div class="col-2 text-center pl-1 pr-0">
                                    <button type="button" class="e_move_table_btn btn btn-outline-success btn-sm main-page-table-btn" function="move">Taşı</button>
                                </div>
                            </div>
                        </div>
                    `;
        });

        return element;
      }

      $(self.id_list.TABLES).html(create_element());
      self.variable_list.PAGE.open();
    },
    initialize: function () {
      let self = this;

      function set_events() {
        $(document).on("click", self.class_list.BUTTON_TABLE, function () {
          let id = parseInt($(this).closest(`[table-id]`).attr("table-id"));

          let table_new = array_list.find(main.data_list.TABLES, id, "id");
          let section_type_new = array_list.find(
            main.data_list.SECTION_TYPES,
            array_list.find(main.data_list.SECTIONS, table_new.section_id, "id").section_id,
            "id"
          );

          let table_old = array_list.find(
            main.data_list.TABLES,
            page_table_detail.variable_list.SELECTED_TABLE_ID,
            "id"
          );
          let section_type_old = array_list.find(
            main.data_list.SECTION_TYPES,
            array_list.find(main.data_list.SECTIONS, table_old.section_id, "id").section_id,
            "id"
          );

          Swal.fire({
            icon: "question",
            title: language.data.TABLE_MOVE,
            //TODO Translate
            html: `<b>'${section_type_old.name}-${table_old.no}'</b> içinde bulunan ürünleri veya ürünü <b>'${section_type_new.name}-${table_new.no}'</b> ${language.data.QUESTION_TABLE_CHANGE}`,
            allowEscapeKey: false,
            allowOutsideClick: false,
            showCancelButton: true,
            confirmButtonText: language.data.ACCEPT,
            cancelButtonText: language.data.CANCEL,
            confirmButtonClass: "btn btn-success btn-lg mr-3 mt-5",
            cancelButtonClass: "btn btn-danger btn-lg ml-3 mt-5",
            buttonsStyling: false,
          }).then((result) => {
            if (result.value) {
              set_pos(
                set_types.TABLE_MOVE,
                {
                  table_id: table_old.id,
                  table_move_id: table_new.id,
                  order_id:
                    page_table_detail.variable_list.SELECTED_ORDER_ID > 0
                      ? page_table_detail.variable_list.SELECTED_ORDER_ID
                      : 0,
                },
                function (data) {
                  data = JSON.parse(data);
                  if (data.status) {
                    main.get_order_related_things(
                      main.get_type_for_order_related_things.ORDER_AND_ORDER_PRODUCTS
                    );
                    helper_sweet_alert.success(
                      language.data.PROCESS_SUCCESS_TITLE,
                      language.data.TABLE_MOVED
                    );
                    self.close();
                    page_main.get_tables();
                    page_table_detail.get_orders();
                  }
                }
              );
            }
          });
        });

        $(document).on("click", self.class_list.BUTTON_PAGE, function () {
          let function_name = $(this).attr("function");

          switch (function_name) {
            case "back":
              self.close();
              break;
          }
        });
      }

      self.set_page();
      set_events();
    },
  };

  let page_basket = {
    id_list: {
      PAGE: "#basket",
      TOTAL: "#basket-total-price",
      ORDER_PRODUCT_QTY: "#basket_product_piece",
    },
    class_list: {
      ITEMS: ".e_basket_items",
      CONFIRM: ".e_basket_confirm",
      BUTTON_PAGE: ".e_basket_page_btn",
      BUTTON_ORDER_PRODUCT: ".e_basket_page_order_product_btn",
      BUTTON_ORDER_PRODUCT_QTY: ".e_basket_page_order_product_qty_btn",
      BUTTON_ORDER_PRODUCT_OPTION: ".e_basket_page_order_product_option_btn",
    },
    variable_list: {
      PAGE: null,
      DATA: Array(),
      SELECTED_ORDER_PRODUCT_INDEX: 0,
      CONFIRM_BOX: null,
    },
    set_page: function () {
      let self = this;
      self.variable_list.PAGE = new custom_pop_up(
        self.id_list.PAGE,
        "animate__slideInUp",
        "animate__slideOutDown"
      );
    },
    get_order_product_qty: function () {
      let self = this;

      function create_element() {
        return `
                    <div class="container text-dark">
                        <div class="row">
                            <div class="col-4 text-left">
                                <button class="e_basket_page_order_product_qty_btn btn btn-danger basket-page-order-product-piece-btn" function="minus">
                                    <i class='mdi mdi-minus'></i>
                                </button>
                            </div>
                            <div class="col-4 table-basket-product-piece text-center" id="basket_product_piece">${
                              self.variable_list.DATA[
                                self.variable_list.SELECTED_ORDER_PRODUCT_INDEX
                              ].qty
                            }</div>
                            <div class="col-4 text-right">
                                <button class="e_basket_page_order_product_qty_btn btn btn-success basket-page-order-product-piece-btn" function="plus">
                                    <i class="mdi mdi-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
      }

      $.confirm({
        title: array_list.find(
          main.data_list.PRODUCTS,
          self.variable_list.DATA[self.variable_list.SELECTED_ORDER_PRODUCT_INDEX].id,
          "id"
        ).name,
        backgroundDismiss: false,
        content: create_element(),
        type: "red",
        typeAnimated: true,
        buttons: {
          okay: {
            text: language.data.OK,
            action: function () {
              self.get();
            },
          },
        },
      });
    },
    get_order_product_options: function () {
      let self = this;

      let order_product = self.variable_list.DATA[self.variable_list.SELECTED_ORDER_PRODUCT_INDEX];
      let product = array_list.find(main.data_list.PRODUCTS, order_product.id, "id");

      function create_element() {
        let element = `
                    <div class="container text-dark">
                        <div class="row" id="basket_product_options">
                `;

        let order_product_options = order_product.options;

        if (order_product.quantity != 1) {
          let product_quantity = array_list.find(
            main.data_list.PRODUCT_QUANTITY_TYPES,
            product.quantity_id,
            "id"
          );
          element += `<div class="col-12 order-item text-center mt-2 mb-2"><b>${order_product.quantity} ${product_quantity.name}</b></div>`;
        }

        if (order_product.comment != "") {
          element += `<div class="col-12 order-item mt-2 mb-2">${language.data.DESCRIPTION}: <b>${order_product.comment}</b></div>`;
        }

        order_product_options.forEach((order_product_option, index) => {
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
                        <div class="col-12 mb-2 basket-item" option-index="${index}">
                            <div class="row">
                                <div class="col-2 p-0 pl-1 text-left">
                                    <button class="e_basket_page_order_product_option_btn btn btn-danger basket-page-order-product-option-btn" function="delete">${
                                      language.data.DELETE
                                    }</button>
                                </div>
                                <div class="col-7 p-0 pr-1 text-left">
                                    <div class="basket-item">
                                        <font><small>[${option.name}]-></small> ${
            order_product_option.qty
          }x ${option_item.name}</font>  
                                        ${
                                          order_product_option.price != 0
                                            ? `[${order_product_option.price > 0 ? `+` : ``}${
                                                order_product_option.price + main.data_list.CURRENCY
                                              }]`
                                            : ``
                                        }
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
        });

        return element;
      }

      self.variable_list.CONFIRM_BOX = $.confirm({
        title: product.name,
        backgroundDismiss: false,
        content: create_element(),
        type: "red",
        typeAnimated: true,
        buttons: {
          okay: {
            text: "Tamam",
            action: function () {
              self.get();
            },
          },
        },
      });
    },
    confirm: function () {
      let self = this;

      function get_data() {
        let data = {
          products: Array(),
          table_id: page_table_detail.variable_list.SELECTED_TABLE_ID,
          order_id: page_table_detail.variable_list.SELECTED_ORDER_ID,
          orders: [
            {
              table_id: page_table_detail.variable_list.SELECTED_TABLE_ID,
              no: "",
            },
          ],
          discount: 0,
          type: 1,
          status: helper.db.order_status_types.GETTING_READY,
        };

        self.variable_list.DATA.forEach((item) => {
          data.products.push({
            id: parseInt(item.id),
            quantity: parseFloat(item.quantity),
            price: parseFloat(item.price),
            vat: parseFloat(item.vat),
            discount: parseFloat(item.discount),
            qty: parseInt(item.qty),
            comment: item.comment,
            type: helper.db.order_product_types.PRODUCT,
            options: item.options,
          });
        });

        return data;
      }

      console.log(get_data());
      console.log(self.variable_list.DATA);

      set_pos(set_types.INSERT, get_data(), function (data) {
        data = JSON.parse(data);
        console.log(data);
        if (data.status) {
          main.get_order_related_things(
            main.get_type_for_order_related_things.ORDER_AND_ORDER_PRODUCTS
          );
          helper_sweet_alert.success(
            language.data.PROCESS_SUCCESS_TITLE,
            language.data.PRODUCT_SENT_SUCCESS
          );
          self.variable_list.DATA = Array();
          self.get();
          page_table_detail.get_orders();
        }
      });
    },
    open: function () {
      let self = this;

      self.get();
      self.variable_list.PAGE.open();
    },
    close: function () {
      let self = this;

      $(page_table_detail.id_list.BASKET_COUNT).html(self.variable_list.DATA.length);
      self.variable_list.PAGE.close();
    },
    get: function () {
      let self = this;
      let total = 0.0;

      function create_element() {
        let element = ``;

        self.variable_list.DATA.forEach((item, index) => {
          let option_icon =
            item.options.length < 1 && item.comment.length < 1 && item.quantity == 1
              ? "hidden-and-events-none"
              : "";
          total += item.price;
          let product = array_list.find(main.data_list.PRODUCTS, item.id, "id");
          element += `
                        <div class="col-12 mb-2 basket-item" index="${index}">
                            <div class="row">
                                <div class="col-1 p-0 pl-1 text-left">
                                    <button class="e_basket_page_order_product_btn btn btn-danger btn-sm basket-page-order-product-btn" function="delete">${
                                      language.data.DELETE
                                    }</button>
                                </div>
                                <div class="e_basket_page_order_product_btn col-1 p-0 pl-2 text-left basket-page-order-product-btn" function="show_count">${
                                  item.qty
                                }x</div>
                                <div class='col-1 p-0 text-left basket-item-option-icon'>
                                    <i class="e_basket_page_order_product_btn mdi mdi-format-list-checkbox ${option_icon} basket-page-order-product-btn" function="show_options"></i>
                                </div>
                                <div class="col-9 p-0 pr-1 text-right">
                                    <div class="basket-item">
                                        <font class="float-left">${product.name}</font> ${
            item.price.toFixed(2) + main.data_list.CURRENCY
          }
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
        });

        if (element === "") {
          element = `
                        <div class="free-order">
                            ${language.data.U_CART_EMPTY}
                            <i class="mdi mdi-alert-circle"></i>
                        </div>
                    `;
        }

        return element;
      }

      $(self.class_list.ITEMS).html(create_element());
      $(self.id_list.TOTAL).html(total.toFixed(2) + main.data_list.CURRENCY);
      if (self.variable_list.DATA.length > 0) $(self.class_list.CONFIRM).show();
      else $(self.class_list.CONFIRM).hide();
    },
    initialize: function () {
      let self = this;

      function set_events() {
        $(document).on("click", self.class_list.BUTTON_PAGE, function () {
          let function_name = $(this).attr("function");

          switch (function_name) {
            case "confirm":
              Swal.fire({
                icon: "question",
                title: language.data.CONFIRM_CART,
                html: language.data.DO_U_CONFIRM_CART,
                allowEscapeKey: false,
                allowOutsideClick: false,
                showCancelButton: true,
                confirmButtonText: language.data.APPROVE,
                cancelButtonText: language.data.CANCEL,
                confirmButtonClass: "btn btn-success btn-lg mr-3 mt-5",
                cancelButtonClass: "btn btn-danger btn-lg ml-3 mt-5",
                buttonsStyling: false,
              }).then((result) => {
                if (result.value) {
                  self.confirm();
                }
              });
              break;
            case "back":
              self.close();
              break;
          }
        });

        $(document).on("click", self.class_list.BUTTON_ORDER_PRODUCT, function () {
          let function_name = $(this).attr("function");

          self.variable_list.SELECTED_ORDER_PRODUCT_INDEX = parseInt(
            $(this).closest(`[index]`).attr("index")
          );

          switch (function_name) {
            case "delete":
              $.confirm({
                icon: "mdi mdi-help",
                title: `<b class="text-dark">${language.data.DELETE_PROCESS_TITLE}</b>`,
                backgroundDismiss: false,
                content: `<p class="text-dark">${language.data.DELETE_BASKET_PRODUCT_QUESTION}</p>`,
                type: "red",
                typeAnimated: true,
                buttons: {
                  okay: {
                    text: language.data.ACCEPT,
                    action: function () {
                      self.variable_list.DATA.splice(
                        self.variable_list.SELECTED_ORDER_PRODUCT_INDEX,
                        1
                      );
                      self.get();
                    },
                  },
                  cancel: {
                    text: language.data.CANCEL,
                  },
                },
              });
              break;
            case "show_count":
              self.get_order_product_qty();
              break;
            case "show_options":
              self.get_order_product_options();
              break;
          }
        });

        $(document).on("click", self.class_list.BUTTON_ORDER_PRODUCT_QTY, function () {
          let function_name = $(this).attr("function");
          let item = self.variable_list.DATA[self.variable_list.SELECTED_ORDER_PRODUCT_INDEX];

          let piece = 0;
          switch (function_name) {
            case "plus":
              piece = 1;
              break;
            case "minus":
              piece = -1;
              break;
          }

          let product_piece = parseInt(item.qty);
          product_piece += piece;
          if (product_piece < 1) {
            product_piece = 1;
          }

          if (item.qty != product_piece) {
            let price = item.price / item.qty;
            price *= product_piece;
            self.variable_list.DATA[self.variable_list.SELECTED_ORDER_PRODUCT_INDEX].qty =
              product_piece;
            self.variable_list.DATA[self.variable_list.SELECTED_ORDER_PRODUCT_INDEX].price = price;
          }
          $(self.id_list.ORDER_PRODUCT_QTY).html(product_piece);
        });

        $(document).on("click", self.class_list.BUTTON_ORDER_PRODUCT_OPTION, function () {
          let function_name = $(this).attr("function");
          let index = $(this).closest(`[option-index]`).attr("option-index");
          console.log(index);
          switch (function_name) {
            case "delete":
              console.log(
                self.variable_list.DATA[self.variable_list.SELECTED_ORDER_PRODUCT_INDEX].options[
                  index
                ]
              );
              self.variable_list.DATA[self.variable_list.SELECTED_ORDER_PRODUCT_INDEX].price -=
                self.variable_list.DATA[self.variable_list.SELECTED_ORDER_PRODUCT_INDEX].options[
                  index
                ].price;
              self.variable_list.DATA[
                self.variable_list.SELECTED_ORDER_PRODUCT_INDEX
              ].options.splice(index, 1);
              self.variable_list.CONFIRM_BOX.close();
              self.get_order_product_options();
              self.get();
              break;
          }
        });
      }

      self.set_page();
      set_events();
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

  function set_pos(set_type, data, success_function) {
    helper_sweet_alert.wait(
      language.data.PROCESS_PROGRESS_TITLE,
      language.data.PROCESS_WAIT_CONTENT
    );
    data["set_type"] = set_type;
    $.ajax({
      url: `${default_ajax_path_pos}set.php`,
      type: "POST",
      data: data,
      async: false,
      success: function (data) {
        console.log(data);
        success_function(data);
      },
      error: helper_sweet_alert.close(),
      timeout: settings.ajax_timeouts.NORMAL,
    });
  }

  function get(get_type, data, success_function) {
    data["get_type"] = get_type;
    $.ajax({
      url: `${default_ajax_path}get.php`,
      type: "POST",
      data: data,
      async: false,
      success: function (data) {
        console.log(data);
        success_function(data);
      },
      timeout: settings.ajax_timeouts.NORMAL,
    });
  }

  return page_dashboard;
})();

$(function () {
  let _dashboard = new page_dashboard();
});
