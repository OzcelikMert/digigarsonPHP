let device_list = (function () {
    let default_ajax_path = `${settings.paths.primary.PHP}list_device/`;
    let set_types = {
        DEVICE_CONTROL: 0x0001,
    };
    let get_types = {
        DEVICES: 0x0001,
        TYPES: 0x0002
    };

    function device_list(){ initialize(); }

    function initialize(){
        list.initialize();
    }

    let list = {
        class_list: {
            LIST: ".e_devices",
            SEARCH_DEVICE: ".e_search_device",
            DEVICE_BTN: ".e_device_btn",
            TYPES: ".e_types"
        },
        id_list: {
            FORM: "#form_device",
            MODAL: "#modal_new_device"
        },
        function_types:{
            device_control: {
                INSERT: 0x0001,
                DELETE: 0x0002
            }
        },
        variable_list: {
            SELECTED_DEVICE_ID: 0,
            DEVICES: Array(),
            TYPES: Array()
        },
        search_types: {
            ID: 1,
            NAME: 2
        },
        data_types: {

        },
        get: function (search_type = 0, search = "") {
            let self = this;

            function create_element(){
                let element = ``;

                self.variable_list.DEVICES.forEach(device => {
                    if(search.length > 0) {
                        let search_key = (search_type === self.search_types.ID) ? "id" : "name";
                        if(!String(device[search_key]).match(new RegExp(search, "gi"))) {
                            return;
                        }
                    }

                    element += `
                        <tr device-id="${device.id}">
                            <td>${device.id}</td>
                            <td>${device.name}</td>
                            <td>${device.type_name}</td>
                            <td>${device.security_code}</td>
                            <td>${((device.is_connect == 1) ? `<font color='lime'>${language.data.CONNECTED}</font>` : `<font color='red'>${language.data.NOT_CONNECTED}</font>`)}</td>
                            <td>${((device.caller_id_active == 1) ? `<font color='lime'>${language.data.ACTIVE}</font>` : `<font color='red'>${language.data.NOT_ACTIVE}</font>`)}</td>
                            <td class="text-center">
                                <button function="edit" class="e_device_btn btn btn-warning"><i class="fa fa-pencil-alt"></i></button>
                            </td>
                            <td class="text-center">
                                <button function="delete" class="e_device_btn btn btn-danger"><i class="fa fa-trash-alt"></i></button>
                            </td>
                        </tr>
                    `;
                });

                return element;
            }

            if(self.variable_list.DEVICES.length < 1) {
                get(
                    get_types.DEVICES,
                    {},
                    function (data) {
                        data = JSON.parse(data);
                        console.log(data);
                        if (data.status) {
                            if (data.custom_data.length > 0) {
                                self.variable_list.DEVICES = data.custom_data;
                            }
                        }
                    }
                );
            }

            $(self.class_list.LIST).html(create_element());
        },
        get_device_types: function () {
            let self = this;

            if(self.variable_list.TYPES.length < 1) {
                get(
                    get_types.TYPES,
                    {},
                    function (data) {
                        data = JSON.parse(data);
                        console.log(data);
                        if (data.status) {
                            if (data.custom_data.length > 0) {
                                self.variable_list.TYPES = data.custom_data;
                            }
                        }
                    }
                );
            }

            $(self.class_list.TYPES).html(helper.get_select_options(self.variable_list.TYPES, "id", "name"));
        },
        initialize: function (){
            let self = this;

            function set_events(){
                $(self.id_list.FORM).submit(function (e) {
                    e.preventDefault();
                    set(
                        set_types.DEVICE_CONTROL,
                        Object.assign($(this).serializeObject(), {
                            "id": self.variable_list.SELECTED_DEVICE_ID,
                            "function_type": self.function_types.device_control.INSERT
                        }),
                        function (data) {
                            data = JSON.parse(data);
                            if(data.status){
                                self.variable_list.DEVICES = Array();
                                self.get();
                                helper_sweet_alert.success(language.data.PROCESS_SUCCESS_TITLE, language.data.PROCESS_SUCCESS);
                                $(self.id_list.MODAL).modal("hide");
                            }else{
                                switch(parseInt(data.error_code)){
                                    case settings.error_codes.REGISTERED_VALUE: helper_sweet_alert.error(language.data.REGISTERED_DEVICE, language.data.ENTER_NOTHER_SECURITY_CODE); break;
                                    case settings.error_codes.EMPTY_VALUE: helper_sweet_alert.error(language.data.INCORRECT_ENTRY, language.data.REQURIED_MESSAGE); break;
                                    //TODO TRANSLATE
                                    case settings.error_codes.NO_PERM: helper_sweet_alert.error(language.data.PERMS_DENIED, "Zaten firmanıza tamamlanmış olan maksimum kullanıcı sınırındasınız. Lütfen daha fazlası için Digigarson pazarlama ekibine ulaşın.!"); break;
                                }
                            }
                        }
                    );
                });

                $(self.class_list.SEARCH_DEVICE).on("keyup change", function () {
                    let function_name = $(this).attr("function");

                    let search_type = (function_name === "id") ? self.search_types.ID : self.search_types.NAME;

                    self.get(search_type, $(this).val());
                });

                $(document).on("click", self.class_list.DEVICE_BTN, function () {
                    let element = $(this);

                    let function_name = element.attr("function");
                    self.variable_list.SELECTED_DEVICE_ID = parseInt(element.closest("[device-id]").attr("device-id"));
                    $(self.id_list.FORM).trigger("reset");

                    switch (function_name) {
                        case "edit":
                            $(self.id_list.FORM).autofill(array_list.find(self.variable_list.DEVICES, self.variable_list.SELECTED_DEVICE_ID, "id"));
                            $(`${self.id_list.FORM} input[name="is_connect"]`).prop("disabled", false);
                            $(self.id_list.MODAL).modal();
                            break;
                        case "delete":
                            Swal.fire({
                                icon: "question",
                                title: language.data.DELETE_PROCESS_TITLE,
                                html: `<b>'${array_list.find(self.variable_list.DEVICES, self.variable_list.SELECTED_DEVICE_ID, "id").name}'</b> ${language.data.DELETE_DEVICE_HTML}`,
                                allowEscapeKey: false,
                                allowOutsideClick: false,
                                showCancelButton: true,
                                confirmButtonText: language.data.DELETE,
                                cancelButtonText: language.data.CANCEL,
                                confirmButtonClass: 'btn btn-success btn-lg mr-3 mt-5',
                                cancelButtonClass: 'btn btn-danger btn-lg ml-3 mt-5',
                                buttonsStyling: false
                            }).then((result) => {
                                if (result.value) {
                                    set(
                                        set_types.DEVICE_CONTROL,
                                        {
                                            "id": self.variable_list.SELECTED_DEVICE_ID,
                                            "function_type": self.function_types.device_control.DELETE
                                        },
                                        function (data) {
                                            data = JSON.parse(data);
                                            if(data.status){
                                                let index = array_list.index_of(self.variable_list.DEVICES, self.variable_list.SELECTED_DEVICE_ID, "id");
                                                delete self.variable_list.DEVICES[index];
                                                self.get();
                                                $(self.id_list.MODAL).modal("hide");
                                                helper_sweet_alert.success(language.data.PROCESS_SUCCESS_TITLE, language.data.PROCESS_SUCCESS);
                                                self.variable_list.SELECTED_DEVICE_ID = 0;
                                            }
                                        }
                                    );
                                }
                            });
                            break;
                        case "add":
                            self.variable_list.SELECTED_DEVICE_ID = 0;
                            $(`${self.id_list.FORM} input[name="is_connect"]`).prop("disabled", true);
                            $(self.id_list.MODAL).modal();
                            break;
                    }
                });
            }

            set_events();
            self.get();
            self.get_device_types();
        }
    }

    function set (set_type, data, success_function){
        helper_sweet_alert.wait(language.data.PROCESS_PROGRESS_TITLE, language.data.PROCESS_WAIT_CONTENT);
        data["set_type"] = set_type;
        $.ajax({
            url: `${default_ajax_path}set.php`,
            type: "POST",
            data: data,
            success: function (data) {
                console.log(data);
                success_function(data);
            },error: helper_sweet_alert.close(), timeout: settings.ajax_timeouts.NORMAL
        });
    }

    function get(get_type, data, success_function){
        data["get_type"] = get_type;
        $.ajax({
            url: `${default_ajax_path}get.php`,
            type: "POST",
            data: data,
            async: false,
            success: function (data) {
                console.log(data);
                success_function(data);
            }, timeout: settings.ajax_timeouts.NORMAL
        });
    }

    return device_list;
})();

$(function () {
    let _device_list = new device_list();
});
