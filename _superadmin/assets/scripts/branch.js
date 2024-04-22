let notification = (function () {
    let default_ajax_path = `${settings.paths.primary.PHP}branch/`;
    let selected_branch_id = 0;
    let set_types = {
        BRANCH_ADD : 0x0002,
    };
    let get_types = {
        BRANCH_INFO : 0x0001,
    };
    let function_types = {
        GET_BRANCH : 0x0001,
        GET_MAIN_BRANCH: 0x0002,
    }

    function notification(){ initialize(); }
    function initialize(){
        get_branch_info.initialize();
        set_branch_info.initialize();
    }

    let get_branch_info = {
        variable_list: {
            ID : "",
            TABLEID : "#table_section",
            MAIN_SELECT : "#main_select_branch",
        },
        data_types : {
            ADMIN_DATA : [],
            MESSAGE_DATA : Array(),
            AUTO : {},
        },
        get_branch : function(){
            get(get_types.BRANCH_INFO, {"function_type" : function_types.GET_BRANCH}, function (data) {
                let element = '';
                data.rows.forEach(function (e) {
                    element += `
                        <tr data-id="${e.id}"> 
                            <td>${e.id}</td>
                            <td>${e.name}</td>
                            <td>
                                <button class="btn btn-primary" function="branch_info">Firma İşlemleri</button>
                            </td>
                        </tr>
                    `;
                });
                $("#branch_table").html(element);
            });
        },
        initialize: function (){
            let self = this;
            function set_events() {
                $(document).on("click", "button[function=branch_info]",function () {
                 selected_branch_id =   $(this).closest("tr").attr("data-id");
                 window.location.href = `./branch_info.php?branch_id=${selected_branch_id}`;
                });
                $(self.variable_list.MAIN_SELECT).select2();
                /* Branch Search*/
                $("#branch_search").on("keydown", function() {
                    let value = $(this).val().toLowerCase();
                    $("table tbody tr").filter(function() {
                        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                    });
                });

            }

            set_events();
            this.get_branch();
        },
    }
    let set_branch_info = {
        variable_list: {
            ID: "",
            select_branch: "#is_main_control",
            form_class : "#add_branch",
            select_branch_main : "#select_branch_box",
            modal_branch_add : "#modal_branch",
            main_select_branch : "#main_select_branch",
        },
        data_types: {
            DATA : Array(),
            DELETE_DATA: Array()
        },
        initialize: function(){
            let self = this;
            function set_events() {
                /* IS MAIN CONTROL*/
                $(document).on("change", self.variable_list.select_branch, function (){
                    let select_val = $(this).val();

                    if(select_val == 0){
                        $(self.variable_list.select_branch_main).css("display", "block");
                    }else{
                        $(self.variable_list.select_branch_main).css("display", "none");
                    }
                });

                $(document).on("click", "button[function=add]", function () {
                    $(self.variable_list.modal_branch_add).modal();

                    function create_element(data){
                        let element = '';
                        element += `<option value="0" selected>Ana Şube Yok</option>`;

                        data.forEach(branch => {
                            element += `<option value="${branch.id}">${branch.name}</option>`;
                        });

                        return element;
                    }

                    get(get_types.BRANCH_INFO, {"function_type" : function_types.GET_MAIN_BRANCH}, function (data){
                        $(self.variable_list.main_select_branch).html(create_element(data.rows));

                    });

                })
                /* New Branch Add */
                $(document).on("submit", self.variable_list.form_class, function(e){
                    e.preventDefault();
                    let data = $(self.variable_list.form_class).serializeObject();

                    console.log(data);
                    set(set_types.BRANCH_ADD, data, function(data){
                        data = JSON.parse(data);
                        console.log(data)
                        if(data.status){
                            get_branch_info.get_branch();
                        }
                    });
                });
            }
            set_events();
        }
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
            },
            error: helper_sweet_alert.close(),timeout: settings.ajax_timeouts.NORMAL
        });
    }

    function get(get_type, data, success_function = null){
        if (get_type !== null)  data["get_type"] = get_type;
        $.ajax({
            url: `${default_ajax_path}get.php`,
            type: "POST",
            data: data,
            success: function (data) {
                data = JSON.parse(data);
                if (success_function !== null){
                    success_function(data);
                }
            }, timeout: settings.ajax_timeouts.NORMAL
        });
    }
    return notification;

})();

$(function () {
    let _index = new notification();
});