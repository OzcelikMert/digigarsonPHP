let page_index = (function() {
    let default_ajax_path = `${settings.paths.primary.PHP}index/`;
    let id_list = {
        VERSION_INFO: "#version_info",
        ERROR: "#login_error",
        FORM: "#login_form"
    };
    function page_index(){ initialize(); }

    function get_version_info(){
        let version = application.application.get_version();
        $(id_list.VERSION_INFO).html("v"+version);
    }

    function initialize(){

        function set_events(){
            $(id_list.FORM).submit(function(e) {
                e.preventDefault();
                let _data = $(this).serializeObject();

                set(
                    _data,
                    function (data) {
                        data = JSON.parse(data);
                        console.log(data);
                        if(data.status){
                            application.db.info.set(_data.security_code);
                            application.db.accounts.set(data.rows.user_id, true);
                            window.location.href = "dashboard.php";
                        }else{
                            $(id_list.ERROR).html(language.data.REQURIED_MESSAGE);
                        }
                    }
                );
            });
        }

        set_events();
        get_version_info();
        console.log("GET");
        let info = JSON.parse(application.db.info.get());
        console.log("GET");
        console.log(application.db.info.get());
        if(info.length > 0){
            $(id_list.FORM).autofill(info[0]);
        }
        let accounts = JSON.parse(application.db.accounts.get(0));
        console.log(accounts);
        if(accounts.length > 0){
            set(
                Object.assign($(id_list.FORM).serializeObject(), {"id": accounts[0].user_id}),
                function (data) {
                    data = JSON.parse(data);
                    if(data.status){
                        window.location = "dashboard.php";
                    }
                }
            );
        }
    }

    function set (data, success_function){
        helper_sweet_alert.wait(language.data.PROCESS_PROGRESS_TITLE, language.data.PROCESS_WAIT_CONTENT);
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

    return page_index;
})();

$(function () {
    let _page_index = new page_index();
})