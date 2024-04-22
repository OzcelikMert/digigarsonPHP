let page_index = (function() {
    let default_ajax_path = `${settings.paths.primary.PHP}index/`;

    function page_index(){ initialize(); }

    function initialize(){
        check_login.initialize();
    }

    let check_login = {
        id_list: {
            FORM: "#login_form",
        },
        initialize: function (){
            let self = this;

            function set_events(){
                $(self.id_list.FORM).submit(function(e) {
                    e.preventDefault();
                    let data = $(this).serializeObject();
                    let data_recaptcha = array_list.find_multi($(this).serializeArray(), "g-recaptcha-response", "name");
                    console.log(data, data_recaptcha)
                    if(
                        (typeof data.email_phone !== "undefined" && !variable.is_empty(data.email_phone)) &&
                        (typeof data.password !== "undefined" && !variable.is_empty(data.password)) /* &&
                        (data_recaptcha.length > 0 && data_recaptcha[0].value !== "")*/
                    ){
                        $.ajax({
                            url: `${default_ajax_path}set.php`,
                            type: "POST",
                            data: data,
                            success: function (data) {
                                console.log(data);
                                data = JSON.parse(data);
                                console.log(data);
                                if(data.status){
                                    helper_sweet_alert.success(language.data.LOGIN_SUCCESS, language.data.REDIRECTED);
                                    setTimeout(() => {window.location.href = "dashboard.php";}, 300)
                                }
                            }, timeout: settings.ajax_timeouts.NORMAL
                        });
                    }else{
                        helper_sweet_alert.error(language.data.WRONG__LOGIN, language.data.REQURIED_LOGIN_INFO);
                    }
                });
            }
            set_events();
        }
    };

    return page_index;
})();

$(function () {
    let _index = new page_index();
});