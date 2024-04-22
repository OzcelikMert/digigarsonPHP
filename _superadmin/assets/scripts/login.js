let login = (function () {
    let default_ajax_path = `${settings.paths.primary.PHP}login/`;
    let set_types = {
        LOGIN_CHECK : 0x0001,
    };
    let get_types = {};

    function notification(){ initialize(); }

    function initialize(){ set_super_admin.initialize(); }

    let set_super_admin = {
        variable_list: {
            ID : "",
            MESSAGE_ID : "",
        },
        class_list: {
            FORM_LOGIN: ".e_login-form"
        },
        data_types : {
            ADMIN_DATA : [],
            MESSAGE_DATA : Array(),
        },
        initialize: function (){
            function set_events(){
                $(set_super_admin.class_list.FORM_LOGIN).submit(function(e){
                    e.preventDefault();
                    let data = $(set_super_admin.class_list.FORM_LOGIN).serializeObject();
                    set(
                        set_types.LOGIN_CHECK,
                        data,
                        function (data) {
                            data = JSON.parse(data);
                            if(data.status && data.rows.length > 0){
                                setTimeout(()=> { window.location.href = "index.php"; },500);
                            }
                    });
                });
            }
            set_events();
        },
    }
    function set (set_type, data, success_function){
        helper_sweet_alert.wait("İşlem Sürdürülüyor", "İşleminiz yapılıyor lütfen bekleyiniz...");
        data["set_type"] = set_type;
        $.ajax({
            url: `${default_ajax_path}set.php`,
            type: "POST",
            data: data,
            success: function (data) {
                console.log(data);
                success_function(data);
            },error: helper_sweet_alert.close(),timeout: settings.ajax_timeouts.NORMAL
        });
    }
    return notification;
})();

$(function () {
    let _index = new login();
});