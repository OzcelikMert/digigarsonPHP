let user_list = (function () {
    let default_ajax_path = `${settings.paths.primary.PHP}list_user/`;
    let set_types = {
        USER_CONTROL: 0x0001,
    };
    let get_types = {
        USERS: 0x0001,
        PERMISSIONS: 0x0002
    };
    let id_list = {
        CLOSE_SAFE_COMMENT: "#close_safe_comment"
    }
    let class_list = {
        NAVIGATION_BUTTON: ".e_navigation_btn",
        SAFE_CLOSE: ".e_safe_close"
    }

    function user_list(){ initialize(); }

    function initialize(){
        list.initialize();
    }

    let list = {
        class_list: {
            LIST: ".e_users",
            SEARCH_USER: ".e_search_user",
            ACCOUNT_BTN: ".e_account_btn",
            PERMISSIONS: ".e_permissions",
            SEARCH_PERMISSION: ".e_search_permission"
        },
        id_list: {
            FORM: "#form_account",
            MODAL: "#modal_new_account"
        },
        function_types:{
            user_control: {
                INSERT: 0x0001,
                DELETE: 0x0002
            }
        },
        variable_list: {
            SELECTED_ACCOUNT_ID: 0,
            ACCOUNTS: Array(),
            PERMISSIONS: Array()
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

                self.variable_list.ACCOUNTS.forEach(account => {
                    if(search.length > 0) {
                        let search_key = (search_type === self.search_types.ID) ? "id" : "name";
                        if(!String(account[search_key]).match(new RegExp(search, "gi"))) {
                            return;
                        }
                    }

                    element += `
                        <tr account-id="${account.id}">
                            <td>${account.id}</td>
                            <td>${account.name}</td>
                            <td>${((account.active == 1) ? "<font color='lime'>"+language.data.ACTIVE+"</font>" : "<font color='red'>"+language.data.NOT_ACTIVE+"</font>")}</td>
                            <td class="text-center">
                                <button function="edit" class="e_account_btn btn btn-warning"><i class="fa fa-pencil-alt"></i></button>
                            </td>
                            <td class="text-center">
                                <button function="delete" class="e_account_btn btn btn-danger"><i class="fa fa-trash-alt"></i></button>
                            </td>
                        </tr>
                    `;
                });

                return element;
            }

            if(self.variable_list.ACCOUNTS.length < 1) {
                get(
                    get_types.USERS,
                    {},
                    function (data) {
                        data = JSON.parse(data);
                        console.log(data);
                        if (data.status) {
                            if (data.custom_data.length > 0) {
                                self.variable_list.ACCOUNTS = Array();
                                data.custom_data.forEach(user => {
                                    self.variable_list.ACCOUNTS.push({
                                        "id": user.id,
                                        "name": user.name,
                                        "permissions": JSON.parse(user.permissions),
                                        "active": user.active
                                    });
                                });
                            }
                        }
                    }
                );
            }

            $(self.class_list.LIST).html(create_element());
        },
        get_permissions: function (search = "") {
            let self = this;

            function create_element(){
                let element = ``;

                self.variable_list.PERMISSIONS.forEach(permission => {
                    if(search.length > 0) {
                        if(!String(permission["name"]).match(new RegExp(search, "gi"))) {
                            return;
                        }
                    }

                    element += `<label> <input class="checkbox-lg" type="checkbox" name="permissions[]" value="${permission.id}"> ${permission.name}</label>`;
                });

                return element;
            }

            if(self.variable_list.PERMISSIONS.length < 1) {
                get(
                    get_types.PERMISSIONS,
                    {},
                    function (data) {
                        data = JSON.parse(data);
                        console.log(data);
                        if (data.status) {
                            if (data.custom_data.length > 0) {
                                self.variable_list.PERMISSIONS = data.custom_data;
                            }
                        }
                    }
                );
            }

            $(self.class_list.PERMISSIONS).html(create_element());
            $(self.id_list.FORM).autofill(array_list.find(self.variable_list.ACCOUNTS, self.variable_list.SELECTED_ACCOUNT_ID, "id"));
        },
        initialize: function (){
            let self = this;

            function set_events(){
                $(self.id_list.FORM).submit(function (e) {
                    e.preventDefault();
                    set(
                        set_types.USER_CONTROL,
                        Object.assign($(this).serializeObject(), {
                            "id": self.variable_list.SELECTED_ACCOUNT_ID,
                            "function_type": self.function_types.user_control.INSERT
                        }),
                        function (data) {
                            data = JSON.parse(data);
                            if(data.status){
                                self.variable_list.ACCOUNTS = Array();
                                self.get();
                                helper_sweet_alert.success(language.data.PROCESS_SUCCESS_TITLE, language.data.PROCESS_SUCCESS);
                                $(self.id_list.MODAL).modal("hide");
                            }else{
                                switch(parseInt(data.error_code)){
                                    case settings.error_codes.REGISTERED_VALUE: helper_sweet_alert.error(language.data.REGISTERED_USER, language.data.ENTER_OTHER_PASS); break;
                                    case settings.error_codes.EMPTY_VALUE: helper_sweet_alert.error(language.data.INCORRECT_ENTRY, language.data.REQURIED_LOGIN_INFO); break;
                                    case settings.error_codes.NO_PERM: helper_sweet_alert.error(language.data.PERMS_DENIED, "Zaten firmanıza tamamlanmış olan maksimum kullanıcı sınırındasınız. Lütfen daha fazlası için Digigarson pazarlama ekibine ulaşın.!"); break;
                                }
                            }
                        }
                    );
                });

                $(self.class_list.SEARCH_USER).on("keyup change", function () {
                    let function_name = $(this).attr("function");

                    let search_type = (function_name === "id") ? self.search_types.ID : self.search_types.NAME;

                    self.get(search_type, $(this).val());
                });

                $(self.class_list.SEARCH_PERMISSION).on("keyup change", function () {self.get_permissions($(this).val());});

                $(document).on("click", self.class_list.ACCOUNT_BTN, function () {
                    let element = $(this);

                    let function_name = element.attr("function");
                    self.variable_list.SELECTED_ACCOUNT_ID = parseInt(element.closest("[account-id]").attr("account-id"));
                    $(self.id_list.FORM).trigger("reset");

                    switch (function_name) {
                        case "edit":
                            $(self.id_list.FORM).autofill(array_list.find(self.variable_list.ACCOUNTS, self.variable_list.SELECTED_ACCOUNT_ID, "id"));
                            $(`${self.id_list.FORM} input[name='password']`).closest("div").hide();
                            $(self.id_list.MODAL).modal();
                            break;
                        case "delete":
                            Swal.fire({
                                icon: "question",
                                title: language.data.DELETE_PROCESS_TITLE,
                                html: `<b>'${array_list.find(self.variable_list.ACCOUNTS, self.variable_list.SELECTED_ACCOUNT_ID, "id").name}'</b> ${language.data.DELETE_COMPANY_HTML}`,
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
                                        set_types.USER_CONTROL,
                                        {
                                            "id": self.variable_list.SELECTED_ACCOUNT_ID,
                                            "function_type": self.function_types.user_control.DELETE
                                        },
                                        function (data) {
                                            data = JSON.parse(data);
                                            if(data.status){
                                                let index = array_list.index_of(self.variable_list.ACCOUNTS, self.variable_list.SELECTED_ACCOUNT_ID, "id");
                                                delete self.variable_list.ACCOUNTS[index];
                                                self.get();
                                                $(self.id_list.MODAL).modal("hide");
                                                helper_sweet_alert.success(language.data.PROCESS_SUCCESS_TITLE, language.data.PROCESS_SUCCESS);
                                                self.variable_list.SELECTED_ACCOUNT_ID = 0;
                                            }
                                        }
                                    );
                                }
                            });
                            break;
                        case "add":
                            self.variable_list.SELECTED_ACCOUNT_ID = 0;
                            $(`${self.id_list.FORM} input[name='password']`).closest("div").show();
                            $(self.id_list.MODAL).modal();
                            break;
                    }
                });
            }

            set_events();
            self.get();
            self.get_permissions();
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

    return user_list;
})();

$(function () {
    let _user_list = new user_list();
});
