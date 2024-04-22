let _page_address;
$(function(){
    let _index = new page_index();
    $("#page_basket [function=send]").hide()
});


let page_index = (function() {
    let default_ajax_path = `${settings.paths.primary.PHP}panel/`;
    function page_index(){
        initialize();
    }
    function initialize(){
        check_login.initialize();
    }
    let check_login = {
        id_list: {
            REGISTER_FORM: "#register_form",
            VERIFY_FORM: "#verify_code",
        },
        set_type: {
            REGISTER: 1,
            LOGIN: 2,
            VERIFY: 3
        },
        set: function(form_data){
            let self = this;
            helper_sweet_alert.wait(language.data.PROCESS_PROGRESS_TITLE, language.data.PROCESS_WAIT_CONTENT);
            $.ajax({
                url: `${default_ajax_path}set.php`,
                type: "POST",
                data: form_data,
                success: function (data) {
                    console.log(data);
                    data = JSON.parse(data);
                    console.log(data);
                    switch (form_data.type){
                        case self.set_type.LOGIN:
                            if(data.status === true) {
                                helper_sweet_alert.success(language.data.LOGIN_SUCCESS, "");
                                pages.close("login_and_register");
                                pages.close("basket");
                                main.data_list.USER.INFO = data.rows;
                                let _page_address = new page_address();
                                page_panel.notification.crete_elements();
                            }
                            break;
                        case self.set_type.REGISTER:
                            if(data.status === true) {
                                toggle_form("verify");
                            }else helper_sweet_alert.error(language.data.UNSUCCESS, language.data.ENTER_PHONE_NUM);
                            break;
                        case self.set_type.VERIFY:
                            if(data.status === true) {
                                helper_sweet_alert.success(language.data.SUCCESS, language.data.SMS_VERIFIED);
                                pages.close("login_and_register");
                                pages.close("basket");
                                main.data_list.USER.INFO = data.rows;
                                let _page_address = new page_address();
                               page_panel.notification.crete_elements();
                            }else helper_sweet_alert.error(language.data.UNSUCCESS, language.data.SMS_WRONG);
                            break;
                    }
                },error: helper_sweet_alert.close(), timeout: settings.ajax_timeouts.NORMAL
            });
            function toggle_form(name){
                $("#register").fadeToggle();
                $("#verify_code_area").fadeToggle();
                let total = 3 * 60;
                switch (name){
                    case "register":
                        $("#login_and_register .title").html(language.data.REGISTER);
                        break;
                    case "verify":
                        $("#login_and_register .title").html(language.data.SMS_VERIFI);

                        let timer = setInterval(function (e){
                            $("#login_and_register .e_verify_count_down").html(total + language.data.SECOND);
                            total--;
                            if (total < 0){
                                clearInterval(timer);
                            }
                        },1000)
                        break;
                }
            }
        },
        register: function(data){
            let self = this;
            self.set(data);
        },
        login: function (){
            let self = this;
            let data = {};
            data["set_type"] = self.set_type.REGISTER;
            data["type"] = self.set_type.LOGIN;
            self.set(data);
        },
        initialize: function (){
            let self = this;

            function set_events(){
                $(self.id_list.REGISTER_FORM).on("submit", function(e) {
                    let data = $(self.id_list.REGISTER_FORM).serializeObject();
                    data["set_type"] = self.set_type.REGISTER;
                    data["type"] = self.set_type.REGISTER;
                    self.register(data);
                    e.preventDefault()
                });

                $(self.id_list.VERIFY_FORM).on("submit", function(e) {
                    let data = $(self.id_list.VERIFY_FORM).serializeObject();
                    data["set_type"] = self.set_type.REGISTER;
                    data["type"] = self.set_type.VERIFY;
                    self.register(data);
                    e.preventDefault()
                });


            }
            //self.login();
            set_events();
        }
    };
    return page_index;
})();


