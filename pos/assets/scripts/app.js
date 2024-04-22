let _index = null;
helper.logger = true;
function get_token(){
    ipc.invoke('function', "get_token").then((result) => {
        g_token = result;
        $("#device_token_input").val(g_token);
        _index = new page_index();
    }
)}

$(document).ready( function () {
   app().then(r => console.log("app active"));
})
if (window.module) module = window.module;
const ipc = require('electron').ipcRenderer;

let enable_print_manager = (typeof app.print_manager !== "undefined");

app = (function() {
    let listener_types = {
        /* -------- Windows ------------ */
        WINDOW_MIN: "window_min",
        WINDOW_MAX: "window_max",
        QUIT:"quit",
        ZOOM: "zoom",
        /* -------- Printer ------------ */
        PRINT: "print",
        MULTI_PRINT: "multi_print",
        VIEW_INVOICE: "view_invoice",
        GET_PRINTERS: "get_printers",
        GET_PRINTER_SETTINGS: "get_printer_settings",
        SAVE_PRINTER_SETTINGS: "save_printer_settings",
        /* -------- Settings ------------ */
        GET_APP_SETTINGS: "get_app_settings",
        SAVE_APP_SETTINGS: "save_app_settings",

        SEND_MAIN_DATA: "main_data",

        READ_INTEGRATION_CLA3000: "read_integration_cla3000",
        SET_INTEGRATION_CLA3000: "set_integration_cla3000",
    }
    let listener_channels = {
        FUNCTION: "function",
        APP: "app",
        CHANGE_URL: "change_url"
    }

    app.ready = {
        printer_list : false,
        printer_settings : false,
        app_settings : false,
    }
    //data and settings
    app.printer = null;
    app.printer_list = null;
    app.settings = null;

    //classes
    app.app_settings = null;
    app.printer_settings = null;
    app.integration_cla3000 = null;

    async function app(){
        await app_window.initialize();
        if (location.pathname !== "/pos/index.php") {
            await app.app_settings.initialize();
            await  app.printer_settings.initialize();
        }
    }
    app.printer_settings = {
        get_printers: async function (){
            await ipc.invoke(listener_channels.FUNCTION,listener_types.GET_PRINTERS).then((result) => {
                app.printer_list = result;
                app.ready.printer_list = true;
                initialize_main()
            })
        },
        get_printer_settings: async function (){
            await  ipc.invoke(listener_channels.FUNCTION,listener_types.GET_PRINTER_SETTINGS).then(async function (result) {
                app.printer = result;
                await helper.log( app.printer," app.printer")
                await main.send_main_data();
                app.ready.printer_settings = true;
                initialize_main()
            })
        },
        set_printer_settings: async function (){
            ipc.invoke(listener_channels.FUNCTION,listener_types.SAVE_PRINTER_SETTINGS, app.printer).then((result) => {
                helper.log("Result: " + result )
            })
        },
        create_printer_settings: function (){
            app.printer = {
                settings: {
                    caller_id: false,
                    payyed_print: false,
                    cancel_invoice: false,
                    cancel_invoice_printer: "",
                    title: "",
                },
                safe: {
                    type: "default",
                    name: "",
                    printer_name: "",
                },
                groups: []
            }
        },
        add_group: function (group_name,printer_name){
            let index = array_list.index_of(app.printer.groups,group_name,"name");
            if (index === -1){
                app.printer.groups.push({type: "kitchen", name: group_name, printer_name: printer_name, categories: Array()})
            }else {
                helper.log("Already Printer Group !")
            }
        },
        del_group: function (index){
            app.printer.groups.splice(index,1)
        },
        edit_group: function (index,group_name,printer_name){
            app.printer.groups[index]["name"] = group_name;
            app.printer.groups[index]["printer_name"] = printer_name;
        },
        edit_safe: function (branch_name= "",printer_name = ""){
            app.printer.safe = { type: "default", name: branch_name, printer_name:printer_name }
        },
        print_invoice: function(printer_name,print_data){
            ipc.invoke(listener_channels.APP,listener_types.PRINT,printer_name,print_data).then((result) => {
                helper.log(result);
            })
        },
        print_multi_invoice: function(print_data){
            ipc.invoke(listener_channels.APP,listener_types.MULTI_PRINT,print_data).then((result) => {
                helper.log(print_data,"print_data")
                helper.log(result);
            })
        },
        view_invoice: function(printer_name,print_data){
            ipc.invoke(listener_channels.APP,listener_types.VIEW_INVOICE,printer_name,print_data).then((result) => {
                helper.log(result);
            })
        },
        check_printer: async function (){
            let self = this;
            helper.log("get_printers")
             self.get_printers();
            helper.log("get_printer_settings")
              self.get_printer_settings().then(async function () {
                  if (app.printer == null){
                      await self.create_printer_settings()
                      await self.set_printer_settings()
                      await self.get_printer_settings()
                      helper.log("PRINTER_SETTINGS: set printer")
                  }else {
                      helper.log("PRINTER_SETTINGS: get printer")
                  }
              })
        },
        initialize: async function (){
           await this.check_printer();
        }
    }
    app.app_settings  = {
        get: function () {
            let self = this;
            ipc.invoke('function', listener_types.GET_APP_SETTINGS).then(async function(result) {
                let status = true;
                helper.log(result,"Result")
                if (result == null) {
                    self.create();
                    self.set();
                } else {
                    app.settings = result;
                }

                if (typeof  app.settings.orders.payment_invoice_user == "undefined") {
                    app.settings.orders.payment_invoice_user = false;
                    status = false;
                }
                if (typeof app.settings.orders.payment_invoice_show_quantity == "undefined") {
                    app.settings.orders.payment_invoice_show_quantity = true;
                    status = false;
                }
                if (typeof app.settings.notifications == "undefined") {
                    app.settings.notifications = {}
                    status = false;
                }
                if(typeof app.settings.notifications.is_enable == "undefined") {
                    app.settings.notifications.is_enable = true;
                    status = false;
                }

                if (!status) app.app_settings.set();

                main.data_list.BARCODE_SYSTEM = app.settings.orders.barcode_system

                if (!enable_print_manager) {
                    main.data_list.TRIGGER_PRODUCT_EDIT = app.settings.orders.trigger_product_edit
                    main.data_list.PAYMENT_INVOICE_USER = app.settings.orders.payment_invoice_user
                }
                helper.log("GET: APP_SETTINGS")
                app.ready.app_settings = true;
                initialize_main()
            })
        },
        set: function (){
            ipc.invoke('function',listener_types.SAVE_APP_SETTINGS, app.settings).then((result) => {
                helper.log("SET: APP_SETTINGS")
            })
        },
        send_main_data: function (data = {},new_data = false) {
            data.new = new_data;
            ipc.invoke('function',listener_types.SEND_MAIN_DATA, data).then((result) => {
                helper.log("send_main_data_list")
            })
        },
        create: function (){
            app.settings = {
                orders: {
                    trigger_product_edit: false,
                    payment_invoice_user: false,
                    payment_invoice_show_quantity: true,
                    barcode_system: false
                },
                notifications: {
                    is_enable: true
                }
            }
        },
        initialize: async function (){
            let self = this;
            await self.get();
        }
    }
    app.integration_cla3000 = {
        get: async function(){
           await ipc.invoke(listener_channels.FUNCTION,listener_types.READ_INTEGRATION_CLA3000).then( async function (result) {
               main.data_list.BARCODE_PRODUCTS = result;
               helper.log("Ürünler Çekildi")
            })
        },
        set: function(){
            ipc.invoke(listener_channels.FUNCTION,listener_types.SET_INTEGRATION_CLA3000,main.data_list.BARCODE_PRODUCTS).then((result) => {
                helper.log(result);
            })
        },
        initialize(){
            let self = this;
            self.get();
        }
    }
    let app_window = {
        id_list: {
            WINDOW_ZOOM_FACTOR:"window_zoom_factor"
        },
        window_type: {
            QUIT: "app_quit",
            MINIMIZE:"app_minimize",
            RELOAD:"app_page_reload",
            ZOOM: "zoom",
        },
        set: function (type,value = 1){
            // let self = this;
            switch (type){
                case listener_types.WINDOW_MIN:
                    ipc.invoke(listener_channels.FUNCTION,listener_types.WINDOW_MIN).then((result) => {helper.log(result)})
                    break;
                case listener_types.WINDOW_MAX:
                    ipc.invoke(listener_channels.FUNCTION,listener_types.WINDOW_MAX).then((result) => {helper.log(result)})
                    break;
                case listener_types.QUIT:
                    ipc.invoke(listener_channels.FUNCTION,listener_types.QUIT).then((result) => {helper.log(result)})
                    break;
                case listener_types.ZOOM:
                    ipc.invoke(listener_channels.FUNCTION,listener_types.ZOOM,value).then((result) => {helper.log(result)})
                    break;

            }
        },
        change: function (patch){
            ipc.send(listener_channels.CHANGE_URL,patch);
        },
        initialize: async function (){
            let self = this;
            function set_events(){
                $(document).on("click","[app-function]",function (){
                    let element = $(this);
                    let type = element.attr("app-function");
                    helper.log(type)
                    switch (type){
                        case self.window_type.QUIT: self.set(listener_types.QUIT);break;
                        case self.window_type.MINIMIZE: self.set(listener_types.WINDOW_MIN);break;
                        case self.window_type.RELOAD:location.reload();break;
                        case self.window_type.ZOOM:
                            let zoom = parseFloat($(self.id_list.WINDOW_ZOOM_FACTOR).attr("zoom")/ 100);
                            self.set(listener_types.ZOOM,zoom);
                            break;
                    }
                });
                $(document).ready(function (){
                    self.change(location.pathname)
                })
            }
            set_events();
        }
    };

    function initialize_main(){
       /* if (app.printer_list && app.printer_settings && app.app_settings){
            let _main = new main();
        }*/
    }

    return app;
})();

if (typeof module === 'object') {window.module = module; module = undefined;}
