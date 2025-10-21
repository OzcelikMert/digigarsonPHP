let page_settings = ( function (){
    let default_ajax_path = `${settings.paths.primary.PHP}settings/`;
    let set_types = {
        CHANGE_PASSWORD: 0x0001
    }

    function page_settings(){ initialize(); }

    function initialize(){
        change_password.initialize();
        printer_settings.initialize();
        modal_customize.initialize();
    }
    function set(set_type, form_data, success_function){
        helper_sweet_alert.wait(language.data.PROCESS_PROGRESS_TITLE, language.data.PROCESS_WAIT_CONTENT);

        form_data.append("set_type", set_type);
        $.ajax({
            url: `${default_ajax_path}set.php`,
            type: "POST",
            data: form_data,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                success_function(data);
            },error: helper_sweet_alert.close(), timeout: settings.ajax_timeouts.NORMAL
        });
    }

    let change_password = {
        id_list: {
            FORM: "#form_change_password"
        },
        name_list: {
            PASSWORD: "password",
            NEW_PASSWORD: "new_password",
            RETRY_NEW_PASSWORD: "re_new_password"
        },
        set: function () {
            let self = this;

            let password = $(`${self.id_list.FORM} [name='${self.name_list.PASSWORD}']`).val();
            let new_password = $(`${self.id_list.FORM} [name='${self.name_list.NEW_PASSWORD}']`).val();
            let re_new_password = $(`${self.id_list.FORM} [name='${self.name_list.RETRY_NEW_PASSWORD}']`).val();

            function check(){
                if(
                    (variable.is_empty(password) || variable.is_empty(new_password) || variable.is_empty(re_new_password)) ||
                    (new_password !== re_new_password)
                ){
                    return false;
                }

                return true;
            }

            if(check()){
                let form_data = new FormData($(self.id_list.FORM)[0]);
                set(
                    set_types.CHANGE_PASSWORD,
                    form_data,
                    function (data) {
                        data = JSON.parse(data);
                        console.log(data);
                        switch (data.error_code) {
                            case settings.error_codes.WRONG_VALUE:
                                helper_sweet_alert.error(language.data.NT_CHANGE_PASS, language.data.ERROR_PASS);
                                break;
                            case settings.error_codes.SUCCESS:
                                helper_sweet_alert.success(language.data.PASS_WAS_CHANGED, language.data.PASS_WAS_CHANGED);
                                break;
                        }
                    }
                );
            }else{
                helper_sweet_alert.error(language.data.NT_CHANGE_PASS, language.data.REQURIED_MESSAGE);
            }
        },

        get_categories: function (){
            let html = "";
            main.data_list.PRODUCT_CATEGORIES.forEach(function (e){
              html += `<tr class="e_category_group" category-id="${e.id}">
                        <td>${e.name}</td>
                        <td><button type="button" class="btn w-100 btn-s1 e_del_group"><lang>SAVE</lang></button></td>
                       </tr>`;
          })
            $("#printer_settings_product_categories").html(html)
        },
        get_printer_groups: function (){
            let html = "";
            app.printer.groups.forEach(function (e){
                html += `<tr class="e_printer_group" group-name="${e.name}" printer-name="${e.printerName}"><td>${e.name}</td><td><button type="button" class="btn w-100 btn-s3 e_del_group"><lang>DELETE</lang></land></button></td></tr>`;
            })
            $("#form_printer_settings .e_printer_groups").html(html)
        },
        initialize: function () {
            let self = this;
            function set_events(){
                $(self.id_list.FORM).on("submit", function () {
                    self.set();
                    return false;
                });
            }
            set_events();
        }
    }
    let printer_settings = {
        id_list: {
            FORM: "#form_printer_settings",
            MODAL: "#modal_printer_settings",
        },
        name_list: {

        },
        group_btn : {
            ADD: `<button type="button" class="btn w-100 btn-s3 e_operation_group" function="del">${language.data.DELETE}</button>`,
            DEL: `<button type="button" class="btn w-100 btn-s1 e_operation_group" function="add">${language.data.ADD}></button>`
        },
        get_categories: function (index){
            let self = this;
            let selected = "";
            let unselected = "";

            main.data_list.PRODUCT_CATEGORIES.forEach(function (e){
                 (app.printer.groups[index].categories.includes(e.id))
                     ?  selected += create_element(e.id,e.name,self.group_btn.ADD)
                     :  unselected += create_element(e.id,e.name,self.group_btn.DEL);

            })
            $("#printer_settings_product_categories").html( selected + unselected)

            function create_element(id,name,btn){
                return ` <tr class="e_category_group" category-id="${id}">
                        <td>${name}</td>
                        <td>${btn}</td>
                </tr>`;
            }
        },
        get_printer_groups: function (){
            let html = "";
            app.printer.groups.forEach(function (e){
                html += `<tr group-name="${e.name}" printer-name="${e.printerName}" >
                            <td class="e_printer_group" function="edit">${e.name}</td>
                            <td class="e_printer_group" function="del"><button type="button" class="btn w-100 btn-s3" >${language.data.DELETE}</button></td>
                       </tr>`;
            })
            $("#form_printer_settings .e_printer_groups").html(html)
        },
        initialize: function () {
            let self = this;
            let selected_group = false;
            let index = -1;
            function set_events(){
                $(self.id_list.MODAL).on('shown.bs.modal', function (e) {
                    let options = `<option value=''>${language.data.MAKE_CHOICE}</option>`;

                    app.printer_list.forEach(function (e){
                        options += `<option value="${e.name}">${e.name}</option>`
                    })
                    $(`${self.id_list.FORM} [name=printer]`).html(options)
                    $(`${self.id_list.FORM} [name=safe_printer]`).html(options)
                    $(`${self.id_list.FORM} [name=safe_printer] option[value='${app.printer.safePrinterName}']`).attr('selected','selected');
                    $(`${self.id_list.FORM} [name=title]`).val(app.printer.title);
                    $(`${self.id_list.FORM} input[name="payment_invoice_user"]`).prop("checked",  app.printer.settings.showUserName);
                    $(`${self.id_list.FORM} input[name="payment_invoice_show_quantity"]`).prop("checked",  app.printer.settings.showQuantityName);
                    $(`${self.id_list.FORM} input[name="get_payment_invoice_after_payment"]`).prop("checked",  app.printer.settings.printPaymentInvoiceAfterPayment);

                    self.get_printer_groups();
                })
                $(`${self.id_list.FORM} .e_new_group_btn`).on("click",function (){
                    let group_name =   $(`${self.id_list.FORM} [name=printer_group_name]`).val().toUpperCase()
                    let printer_name = $(`${self.id_list.FORM} [name=printer]`).val();
                    if (group_name !== "" && printer_name !== ""){
                        app.printer_settings.add_group(group_name,printer_name)
                        self.get_printer_groups();
                    }else {
                        alert(language.data.NO_PRINTER_OR_GROUP)
                    }
                })

                $(document).on("click",`${self.id_list.FORM} .e_printer_group`,function (){
                    let type = $(this).attr("function");
                    let element = $(this).closest("tr")
                    switch (type){
                        case "edit":
                            selected_group = true;
                            let group_name = element.attr("group-name")
                            index = array_list.index_of(app.printer.groups,group_name,"name")
                            self.get_categories(index);
                            let printer_name = app.printer.groups[index].printerName;
                            console.log(printer_name, app.printer.groups[index]);
                            
                            $(`${self.id_list.FORM} .e_printer_groups tr.selected`).removeClass("selected")
                            element.addClass("selected");
                            $("#form_printer_settings [name=printer] option").attr("selected",null);
                            $("#form_printer_settings [name=printer]").find(`option[value="${printer_name}"]`).attr('selected','selected');
                            break;
                        case "del":
                            let name = element.closest("tr").attr("group-name")
                            app.printer_settings.del_group(array_list.index_of(app.printer.groups,name,"name"))
                            element.remove();
                            $("#printer_settings_product_categories").html("")
                            selected_group = false;
                            index = -1;
                            break;
                    }
                })
                $(document).on("click",`${self.id_list.FORM} input[name=printer_group_name]`,function (){
                    $("#form_printer_settings .e_printer_group.selected").removeClass("selected")
                    selected_group = false;
                })
                $(document).on("click",`${self.id_list.FORM} .e_operation_group`,function (){
                   let element = $(this);
                   let type = element.attr("function");
                   let id = parseInt(element.closest("tr").attr("category-id"))

                   switch (type){
                       case "add":
                           app.printer.groups[index].categories.push(id)
                           element.replaceWith(self.group_btn.ADD)
                           break;
                       case "del":
                           app.printer.groups[index].categories.splice(app.printer.groups[index].categories.indexOf(id),1);
                           element.replaceWith(self.group_btn.DEL)
                           break;
                   }
                })
               /*
                $(document).on("click",`${self.id_list.FORM} .e_safe_printer [function=set]`,function (){
                    app.printer_settings.edit_safe(
                        $(`${self.id_list.FORM} [name=title]`).val(), //title
                        $(`${self.id_list.FORM} [name=safe_printer]`).val() //printer name
                    )
                    helper_sweet_alert.success("Ayarlarlandı","Tamamen Kaydetmek için Yukarıdaki Kaydet'e Basmayı Unutmayın")
                })
                */
                $(document).on("change",`${self.id_list.FORM} select[name=printer]`,function (){
                    if (selected_group){
                        $("#form_printer_settings [name=printer] option").attr("selected",null)
                        let element =  $(`${self.id_list.FORM} .e_printer_groups tr.selected`);
                        let printer_name = $(`${self.id_list.FORM} [name=printer]`).val()
                        element.attr("printer-name",printer_name)
                        let group_name = element.attr("group-name")
                        let index = array_list.index_of(app.printer.groups,group_name,"name")
                        app.printer_settings.edit_group(index,group_name,printer_name)
                    }
                })
                $(document).on("click",`${self.id_list.FORM} .e_save_printer_settings`,function (){
                    app.printer.settings.showUserName = $(`${self.id_list.FORM} input[name="payment_invoice_user"]`).prop("checked");
                    app.printer.settings.showQuantityName = $(`${self.id_list.FORM} input[name="payment_invoice_show_quantity"]`).prop("checked");
                    app.printer.settings.printPaymentInvoiceAfterPayment = $(`${self.id_list.FORM} input[name="get_payment_invoice_after_payment"]`).prop("checked");
                    app.printer.title = $(`${self.id_list.FORM} [name=title]`).val();
                    app.printer.safePrinterName = $(`${self.id_list.FORM} [name=safe_printer]`).val();
                    app.printer_settings.set_printer_settings().then(()=> helper_sweet_alert.success(language.data.SUCCESS_SAVED));
                })

            }
            set_events();
        }
    }
    let modal_customize = {
        id_list: {
            FORM: "#modal_customize_form",
            MODAL: "#modal_customize"
        },
        initialize(){
            let self = this;

            function set_events(){
                $(document).on('shown.bs.modal',self.id_list.MODAL,function (){
                    $(`${self.id_list.FORM} input[name="active_trigger_product_edit"]`).prop("checked", app.customize_settings.triggerProductOptionModal);
                    $(`${self.id_list.FORM} input[name="barcode_system"]`).prop("checked",  app.customize_settings.enableBarcodeSystem);
                    $(`${self.id_list.FORM} input[name="notifications"]`).prop("checked",  app.customize_settings.enableNotifications);
                })
                $(`${self.id_list.FORM} input[name="active_trigger_product_edit"]`).on("change", function () {
                    if(!$(this).prop("checked")){
                        Swal.fire({
                            icon: "warning",
                            title: language.data.INFORMATION,
                            html: `${language.data.CUSTOMIZE_INFORMATION_ABOUT_OFF_OPTION_TRIGGER}`,
                            showCancelButton: false,
                            confirmButtonText: language.data.OK,
                            confirmButtonClass: 'btn btn-danger btn-lg mr-3 mt-5',
                            cancelButtonClass: 'btn btn-primary btn-lg ml-3 mt-5',
                            buttonsStyling: false
                        });
                    }
                });
                $(self.id_list.FORM).submit(function (e) {
                    e.preventDefault();

                    app.customize_settings.triggerProductOptionModal = $(`${self.id_list.FORM} input[name="active_trigger_product_edit"]`).prop("checked");
                    app.customize_settings.enableBarcodeSystem = $(`${self.id_list.FORM} input[name="barcode_system"]`).prop("checked");
                    app.customize_settings.enableNotifications = $(`${self.id_list.FORM} input[name="notifications"]`).prop("checked");

                    app.app_settings.set();
                    helper_sweet_alert.success( language.data.PROCESS_SUCCESS_TITLE,language.data.INFORMATION_CONTENT);
                });

                $(document).on('click',`${self.id_list.MODAL} table tr[for]`,function (){
                    let element = $(this);
                    let name = element.attr("for");
                    if (name !== undefined && typeof name == "string") {
                        let input = $(`${self.id_list.MODAL} input[name=${name}]`);
                        input.each(function () { this.checked = !this.checked; });
                    }
                })
            }
            set_events()
        },
    }

    return page_settings;
})();

$(function () {
    let _settings = new page_settings();
});