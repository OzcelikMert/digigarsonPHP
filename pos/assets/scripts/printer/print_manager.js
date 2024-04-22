let print_manager = null;
if (window.module) module = window.module;

const electron = window.require('electron');
const ipcRenderer  = electron.ipcRenderer;
let main = {};

document.addEventListener("DOMContentLoaded", function(event) {
    new print_manager();
});

print_manager =  (function() {
    function print_manager(){
        print_manager.manager.initialize();
        invoice.auto_print()
    }
    let channels = {
        FUNCTIONS: "functions",
        MAIN_DATA : "main_data",
        PRINT_LOG : "print_log",
        PRINT_DATA_LOG : "print_data_log",
    }
    print_manager.manager = {
        class_list: {
            PRINT_LOG: ".e_print_log",
            PRINT_DATA_LOG: ".e_print_data_log",
        },
        add_print_ajax_data_count: function (length){
            let self = this;
            $(self.class_list.PRINT_DATA_LOG).prepend(
                `<tr> 
                    <td>${self.get_time()}</td> 
                    <td>${length} Row</td> 
                    <td>${language.data.RETRIEV_DATA}</td>
               </tr>`
            );
        },
        add_print_data_log: function (data){
            let self = this;
            $(self.class_list.PRINT_DATA_LOG).prepend(
                `<tr> 
                    <td>${self.get_time()}</td> 
                    <td>${data.length}</td> 
                    <td>Üretim Fişi</td>
               </tr>`
            );
        },
        initialize: function () {
            this.set_events();
        },
        get_time: function (){
            let date = new Date;
            return `${date.getHours()}:${date.getMinutes()}:${(date.getSeconds() < 10) ? "0"+date.getSeconds() : date.getSeconds() }`;
        },
        set_events: function (){
            let self = this;
            ipcRenderer.on(channels.MAIN_DATA, function (event, data) {
                if (data.new) main.data_list = data;
                else for (let item in data) main.data_list[item] = data[item];
                console.log(`${self.get_time()} ${(data.new) ? "GET: DATA" : "UPDATE: DATA" }`)
            });
            ipcRenderer.on(channels.FUNCTIONS, function (event, data) {
                switch (data.type){
                    case "settings":
                        app.printer_settings.get_printer_settings();
                        app.app_settings.get();
                        break;
                }
            });
            ipcRenderer.on(channels.PRINT_LOG, function (event, data) {
                $(self.class_list.PRINT_LOG).prepend(
                    `<tr printer-name="${data.printer}" patch="${data.patch}" html='${data.html}' height="${data.height}"> 
                        <td>${self.get_time()}</td> 
                        <td>${data.table} </td> 
                         <td>${data.group} </td> 
                        <td>${data.printer}</td> 
                        <td>${(data.status.pdf && data.status.print) ?  "<span style='background: green'>"+language.data.SUCCESS+"</span>" : "<span style='background: darkred'>"+language.data.ERROR+"</span>"} </td>
                        <td><button class="btn w-100 e_re_print">${language.data.PRINT}</button></td>
                    </tr>`
                );
            });
            $(document).on("click",".e_re_print",function () {
                let element = $(this).closest("tr");
                let data = {
                    type: "re_print",
                    patch: element.attr("patch"),
                    html: element.attr("html"),
                }
                let printer_name = element.attr("printer-name")
                invoice.setPrint(data,printer_name)
            })
        },
    }
    return print_manager;
})();

if (typeof module === 'object') {window.module = module; module = undefined;}
