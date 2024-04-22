let settings_table = (function () {
    let default_ajax_path = `${settings.paths.primary.PHP}settings_table/`;
    let set_types = {
        SORT_TABLE_SECTIONS: 0x0001
    };

    function settings_table(){ initialize(); }

    function initialize(){
        table_sections.initialize();
    }

    let table_sections = {
        id_list: {
            MODAL_SORT: "#modal_table_sections_sort",
            FORM_SORT: "#form_table_sections_sort"
        },
        class_list: {
            LIST: ".e_table_sections",
            LIST_SORT: ".e_table_sections_sort"
        },
        get: function () {
            let self = this;

            function create_element() {
                let element = ``;

                main.data_list.SECTIONS.forEach(section => {
                    if(section.branch_id == 0) return;
                    let section_type = array_list.find(main.data_list.SECTION_TYPES, section.section_id, "id");
                    let is_active = (section.is_active == 1) ? `${language.data.ACTIVE}` : `${language.data.CLOSED}`;
                    element += `
                        <tr>
                            <td>${section_type.name}</td>
                            <td>${is_active}</td>
                        </tr>
                    `;
                });

                return element;
            }

            $(self.class_list.LIST).html(create_element());
        },
        initialize: function () {
            let self = this;

            function set_events(){
                $(self.id_list.MODAL_SORT).on("show.bs.modal", function () {

                    function create_element(){
                        let element = ``;

                        main.data_list.SECTIONS.forEach(section => {
                            if(section.branch_id == 0) return;
                            let section_type = array_list.find(main.data_list.SECTION_TYPES, section.section_id, "id");
                            element += `
                                <li section-id="${section.id}">${section_type.name}</li>
                            `;
                        });

                        return element;
                    }

                    $(self.class_list.LIST_SORT).html(create_element());
                });

                $(self.id_list.FORM_SORT).submit(function (e) {
                    e.preventDefault();

                    let data = Array();

                    Array.from($(`${self.class_list.LIST_SORT} li`)).forEach(element => {
                        element = $(element);
                        data.push(element.attr("section-id"));
                    });

                    set(
                        set_types.SORT_TABLE_SECTIONS,
                        {"table_sections": data},
                        function (data) {
                            data = JSON.parse(data);
                            console.log(data);
                            if(data.status){
                                helper_sweet_alert.success(language.data.PROCESS_SUCCESS_TITLE, language.data.PROCESS_SUCCESS);
                            }
                            main.get_table_related_things(main.get_type_for_table_related_things.SECTIONS);
                            self.get();
                            $(self.id_list.MODAL_SORT).modal("hide");
                        }
                    );
                });
            }

            set_events();
            self.get();
            $(self.class_list.LIST_SORT).sortable();
        }
    }

    function set (set_type, data, success_function = null, sweet_alert = true){
        if (sweet_alert) helper_sweet_alert.wait(language.data.PROCESS_PROGRESS_TITLE, language.data.PROCESS_WAIT_CONTENT);;
        if (set_type !== null)  data["set_type"] = set_type;
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

    function get(get_type, data, success_function = null, async = false, sweet_alert = false){
        data["get_type"] = get_type;
        console.log(data);
        if (sweet_alert)  helper_sweet_alert.wait(language.data.PROCESS_PROGRESS_TITLE, language.data.PROCESS_WAIT_CONTENT);
        $.ajax({
            url: `${default_ajax_path}get.php`,
            type: "POST",
            data: data,
            async: async,
            success: function (data) {
                console.log(data);
                success_function(data);
                helper_sweet_alert.close();
            },error: helper_sweet_alert.close(), timeout: settings.ajax_timeouts.NORMAL
        });
    }

    return settings_table;
})();

$(function () {
    let _settings_table = new settings_table();
});

