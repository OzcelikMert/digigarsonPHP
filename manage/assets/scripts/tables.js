let page_tables = (function() {
    let default_ajax_path = `${settings.paths.primary.PHP}tables/`;

    function page_tables(){ initialize(); }

    function initialize(){
        list.initialize();
    }

    let list = {
        id_list: {

        },
        class_list: {

        },
        get: function () {
            main.data_list.TABLES
        },
        initialize: function () {
            let self = this;

            function set_events(){

            }

            set_events();
        }
    };

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

    return page_tables;
})();

$(function () {
    let _page_tables = new page_tables();
});