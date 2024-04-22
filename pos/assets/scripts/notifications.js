let notifications = (function () {
    let default_ajax_path = `${settings.paths.primary.PHP_SAME_PARTS}notifications/`;
    let variable_list = {
        OLD: [],
        NEW: [],
    }
    function notifications() { initialize() }
    function initialize(){ notifications.main.initialize() }

    notifications.main = {
        id_list: {
            SECTIONS: "#notification_area"
        },
        class_list: {
            NOTIFICATION_READ: ".e_notification_read",
        },

        reload_data: function (){
            let self = this;
            main.data_list.NOTIFICATIONS.forEach(function (item){
                // if it's a new notification
                if(!variable_list.OLD.includes(item.id)){
                    variable_list.NEW.push(item);
                }
            })
            if (variable_list.NEW.length > 0){
                variable_list.NEW.forEach(function (item) {
                    self.add_pop(self.create_element(item.table_id,item.notification_id,item.id))
                    variable_list.OLD.push(item.id)
                })
                variable_list.NEW = [];
            }
        },
        get_type_name: function (notification_id){
            return array_list.find(main.data_list.NOTIFICATION_TYPES,parseInt(notification_id),"id").name;
        },
        create_element: function (table_id,notification_id,id){
            let self = this;
            let element = `${helper.get_table_and_section_with_id(table_id)} "${self.get_type_name(notification_id)}"`;
            return `<div class="e_notification_read __mtoast" notification-id="${id}"><strong>${element}</strong></div>`;
        },
        add_pop: function (html = ""){
            let self = this;
            $(self.id_list.SECTIONS).append(html);
        },
        set_read: function (id=0){
            set(id,function () {})
        },
        initialize: function () {
            let self = this;
            function set_events (){
                $(document).on("click",`${self.id_list.SECTIONS} .e_notification_read`,function (){

                    let notification_id = parseInt($(this).attr("notification-id"));
                    self.set_read(notification_id);
                    $(this).remove();
                });
            }
            set_events();
        }
    }

    function set(id, success_function){
        $.ajax({
            url: `${default_ajax_path}set.php`,
            type: "POST",
            data: {"id":id},
            success: function (data) {
                console.log(data);
                success_function(data);
            }, timeout: settings.ajax_timeouts.NORMAL
        });
    }
    function get(get_type, data, success_function){
        data["get_type"] = get_type;
        $.ajax({
            url: `${default_ajax_path}get.php`,
            type: "POST",
            data: data,
            success: function (data) {
                console.log(data);
                success_function(data);
            }, timeout: settings.ajax_timeouts.NORMAL
        });
    }

    return notifications;
})();

$(function () {
    let _notifications = new notifications();
});