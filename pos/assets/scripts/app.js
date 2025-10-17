helper.logger = true;

$(document).ready(function () {
  //app().then((r) => );
});

if (window.module) module = window.module;
const ipc = require("electron").ipcRenderer;

// let enable_print_manager = (typeof app.print_manager !== "undefined");

let app = (function () {
  app.listeners = {
    /* -------- Windows ------------ */
    WINDOW_MIN: "minimize",
    WINDOW_MAX: "maximize",
    QUIT: "exit",
    ZOOM: "zoom",
    CHANGE_URL: "changeURL",
    /* -------- Printer ------------ */
    PRINT: "print",
    MULTI_PRINT: "multiPrint",
    VIEW_INVOICE: "viewInvoice",
    GET_PRINTERS: "getPrinters",
    GET_PRINTER_SETTINGS: "getPrinterSettings",
    SAVE_PRINTER_SETTINGS: "setPrinterSettings",
    /* -------- Settings ------------ */
    GET_CUSTOMIZE_SETTINGS: "getCustomizeSettings",
    SAVE_CUSTOMIZE_SETTINGS: "setCustomizeSettings",

    READ_INTEGRATION_CLA3000: "readIntegrationCLA3000",
    SET_INTEGRATION_CLA3000: "setIntegrationCLA3000",

    /* -------- User ------------ */
    GET_TOKEN: "getToken",
    GET_USER: "getUser",
    SET_USER: "setUser",
  };

  app.printer_types = {
    kitchen: "kitchen",
    default: "default",
    safe: "safe",
  };

  app.ready = {
    printer_list: false,
    printer_settings: false,
    app_settings: false,
  };
  //data and settings
  app.printer = null;
  app.printer_list = null;
  app.customize_settings = null;
  app.token = "";

  //classes
  app.app_settings = null;
  app.printer_settings = null;
  app.integration_cla3000 = null;

  function app() {
    initialize();
  }

  async function initialize() {
    await theme_window.initialize();
    if (location.pathname !== "/pos/index.php") {
      await app.app_settings.initialize();
      await app.printer_settings.initialize();
    }
    console.log("app active");
  }

  app.printer_settings = {
    get_printers: async function () {
      await ipc.invoke(app.listeners.GET_PRINTERS).then((result) => {
        app.printer_list = result;
        app.ready.printer_list = true;
        initialize_main();
      });
    },
    get_printer_settings: async function () {
      await ipc
        .invoke(app.listeners.GET_PRINTER_SETTINGS)
        .then(async function (result) {
          app.printer = result;
          await helper.log(app.printer, " app.printer");
          app.ready.printer_settings = true;
          initialize_main();
        });
    },
    set_printer_settings: async function () {
      ipc
        .invoke(app.listeners.SAVE_PRINTER_SETTINGS, app.printer)
        .then((result) => {
          helper.log("Result: " + result);
        });
    },
    add_group: function (group_name, printer_name) {
      let index = array_list.index_of(app.printer.groups, group_name, "name");
      if (index === -1) {
        app.printer.groups.push({
          name: group_name,
          printeNamer: printer_name,
          categories: Array(),
        });
      } else {
        helper.log("Already Printer Group !");
      }
    },
    del_group: function (index) {
      app.printer.groups.splice(index, 1);
    },
    edit_group: function (index, group_name, printer_name) {
      app.printer.groups[index]["name"] = group_name;
      app.printer.groups[index]["printerName"] = printer_name;
      this.set_printer_settings();
    },
    print_invoice: function (printer_name, print_data) {
      console.log('print_invoice', printer_name, print_data);
      
      helper.log(printer_name, "print_data");
      ipc
        .invoke(app.listeners.PRINT, {printerName: printer_name, data: print_data})
        .then((result) => {
          helper.log(result);
        });
    },
    print_multi_invoice: function (print_data) {
      helper.log(print_data, "print_data");
      ipc.invoke(app.listeners.MULTI_PRINT, print_data).then((result) => {
        helper.log(result);
      });
    },
    view_invoice: function (printer_name, print_data) {
      ipc
        .invoke(app.listeners.VIEW_INVOICE, printer_name, print_data)
        .then((result) => {
          helper.log(result);
        });
    },
    check_printer: async function () {
      let self = this;
      helper.log("get_printers");
      self.get_printers();
      helper.log("get_printer_settings");
      self.get_printer_settings().then(async function () {
        if (app.printer == null) {
          await self.set_printer_settings();
          await self.get_printer_settings();
          helper.log("PRINTER_SETTINGS: set printer");
        } else {
          helper.log("PRINTER_SETTINGS: get printer");
        }
      });
    },
    initialize: async function () {
      await this.check_printer();
    },
  };
  app.app_settings = {
    get: function () {
      let self = this;
      ipc
        .invoke(app.listeners.GET_CUSTOMIZE_SETTINGS)
        .then(async function (result) {
          let status = true;
          helper.log(result, "Result");

          app.customize_settings = result;

          helper.log("GET: APP_SETTINGS");
          app.ready.app_settings = true;
          initialize_main();
        });
    },
    set: function () {
      ipc
        .invoke(app.listeners.SAVE_CUSTOMIZE_SETTINGS, app.customize_settings)
        .then((result) => {
          helper.log("SET: APP_SETTINGS");
        });
    },
    initialize: async function () {
      let self = this;
      await self.get();
    },
  };
  app.integration_cla3000 = {
    get: async function () {
      await ipc
        .invoke(app.listeners.READ_INTEGRATION_CLA3000)
        .then(async function (result) {
          main.data_list.BARCODE_PRODUCTS = result;
          helper.log("Ürünler Çekildi");
        });
    },
    set: function () {
      ipc
        .invoke(
          app.listeners.SET_INTEGRATION_CLA3000,
          main.data_list.BARCODE_PRODUCTS
        )
        .then((result) => {
          helper.log(result);
        });
    },
    initialize() {
      let self = this;
      self.get();
    },
  };
  let theme_window = {
    id_list: {
      WINDOW_ZOOM_FACTOR: "window_zoom_factor",
    },
    app_function_attr: {
      QUIT: "app_quit",
      MINIMIZE: "app_minimize",
      RELOAD: "app_page_reload",
      ZOOM: "zoom",
    },
    set: function (type, value = 1) {
      // let self = this;
      switch (type) {
        case app.listeners.WINDOW_MIN:
          ipc.invoke(app.listeners.WINDOW_MIN).then((result) => {
            helper.log(result);
          });
          break;
        case app.listeners.WINDOW_MAX:
          ipc.invoke(app.listeners.WINDOW_MAX).then((result) => {
            helper.log(result);
          });
          break;
        case app.listeners.QUIT:
          ipc.invoke(app.listeners.QUIT).then((result) => {
            helper.log(result);
          });
          break;
        case app.listeners.ZOOM:
          ipc.invoke(app.listeners.ZOOM, value).then((result) => {
            helper.log(result);
          });
          break;
      }
    },
    change: function (patch) {
      ipc.send(app.listeners.CHANGE_URL, patch);
    },
    initialize: async function () {
      let self = this;
      function set_events() {
        $(document).on("click", "[app-function]", function () {
          let element = $(this);
          let type = element.attr("app-function");
          helper.log(type);
          switch (type) {
            case self.app_function_attr.QUIT:
              self.set(app.listeners.QUIT);
              break;
            case self.app_function_attr.MINIMIZE:
              self.set(app.listeners.WINDOW_MIN);
              break;
            case self.app_function_attr.RELOAD:
              location.reload();
              break;
            case self.app_function_attr.ZOOM:
              let zoom = parseFloat(
                $(self.id_list.WINDOW_ZOOM_FACTOR).attr("zoom") / 100
              );
              self.set(app.listeners.ZOOM, zoom);
              break;
          }
        });
        $(document).ready(function () {
          self.change(location.pathname);
        });
      }
      set_events();
    },
  };

  app.users = {
    get_token: async function () {
      let result = await ipc.invoke(app.listeners.GET_TOKEN);
      console.log(result);
      app.token = result;
      return result;
    },
    get_user: function (id) {
      ipc.invoke(app.listeners.GET_USER).then((result) => {
        console.log(result);
      });
    },
    set_user: function (id = 0, isDarkMode = false) {
      ipc
        .invoke(app.listeners.GET_TOKEN, { id: 0, isDarkMode: false })
        .then((result) => {
          console.log(result);
        });
    },
    initialize: async function () {
      let self = this;

      self.get_token();
    },
  };

  function initialize_main() {
    if (app.printer_list && app.printer_settings && app.app_settings) {
      //let _main = new main();
    }
  }

  return app;
})();

$(function () {
  let _app = new app();
});

/*if (typeof module === "object") {
  window.module = module;
  module = undefined;
}*/
