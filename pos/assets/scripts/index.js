let g_token = null;
$(document).ready(function (){
    get_token(); 
})

let page_index = (function() {
    let default_ajax_path = `${settings.paths.primary.PHP}index/`;

    function page_index(){ initialize(); }

    function initialize(){
        check_login.initialize();
    }

    let check_login = {
        self : this,
        id_list: {
            FORM: "#branch_login",
        },
        class_list: {
            UN_TOKEN: ".e_token_logout"
        },
        element_type: {
            VERIFY_CODE_INPUT: `input[name=security_code]`,
            PASSWORD_INPUT:    `input[name=password]`,
        },
        login_types: {
            VERIFY_CODE: 0x0001,
            TOKEN: 0x0002,
            PASSWORD: 0x0003,
            UN_TOKEN: 0x0004,
        },
        variable_list: {
          next_type: 2
        },
        set: function(return_function = function (){},un_token = false){
            let self = this;
            let form_data = [];
            form_data = new FormData($(self.id_list.FORM)[0]);

            if (un_token){
                form_data.append("type", self.login_types.UN_TOKEN);
            }else {
                form_data.append("type", self.variable_list.next_type);
            }

            console.log(form_data)
            $.ajax({
                url: `${default_ajax_path}set.php`,
                type: "POST",
                cache: false, contentType: false, processData: false,
                data: form_data,
                success: function (data) {
                    data = JSON.parse(data);
                    if(un_token) location.reload();
                    return_function(data);
                },error: helper_sweet_alert.close(), timeout: settings.ajax_timeouts.NORMAL
            });
        },

        check : function (data){
            let self = this;

            switch (self.variable_list.next_type){
                case self.login_types.TOKEN:
                case self.login_types.VERIFY_CODE:
                    if (data.status){ // allow token
                        $(self.element_type.PASSWORD_INPUT).show().val("");
                        $(self.element_type.VERIFY_CODE_INPUT).hide().val("");
                        self.variable_list.next_type = self.login_types.PASSWORD;
                        $(self.class_list.UN_TOKEN).show();
                    }else {
                        $(self.element_type.VERIFY_CODE_INPUT).show().val("");
                        $(self.element_type.PASSWORD_INPUT).hide().val("");
                        self.variable_list.next_type = self.login_types.VERIFY_CODE;
                    }
                break;
                case self.login_types.PASSWORD:
                    if (data.status === true) {
                        helper_sweet_alert.success(language.data.LOGIN_SUCCESS, language.data.REDIRECTED);
                        setTimeout(() => {window.location.href = "orders.php";}, 300)
                    } else helper_sweet_alert.error(language.data.LOGIN_FAILED, PASSWORD_CHECK);
                break;
            }


        },
        initialize: function (){
            let self = this;

            function set_events(){
                $(document).ready(function (){
                    setTimeout(function (){
                        self.variable_list.next_type = self.login_types.TOKEN;
                        self.set(function (data){self.check(data)})
                    },500)
                })

                $(self.id_list.FORM).on("submit", function() {
                    self.set(function (data){self.check(data)});
                    return false;
                });

                $(`${self.class_list.UN_TOKEN}`).on("click", function() {
                    Swal.fire({
                        icon: "question",
                        title: language.data.REGISTERED_DEVICE,
                        html: language.data.DELETE_RECORD_QUESTION,
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
                            self.set(function (data){},true);
                        }
                    });
                    return false;
                });

            }
            set_events();
        }
    };



    return page_index;
})();


