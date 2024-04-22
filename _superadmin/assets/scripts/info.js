let info = (function(){
    let default_ajax_path = `${settings.paths.primary.PHP}branch/`;

    /* Get & Set Types*/
    let set_types = {};
    let get_types = {
        GET_DETAILS: 0x0002,
    };
    let function_types = {
        GET_BRANCH : 0x0001,
        GET_BRANCH_USER: 0x0002,
        GET_MANAGE_USER: 0x0003,
    };

    /* Special Variables */
    let branch_id, active_statu, select_id;
    let params = new URLSearchParams(new URL(window.location.href).search);
    branch_id = parseInt(params.get("branch_id"));
   /* branch_id = params.get('branch_id'); */

    /* F main */
    function info(){ initialize(); }
    /* F init*/
    function initialize(){
        /* init functions here...*/
        get_info.initialize();
    }
    /* Get & Set Objects*/

    get_info = {
        class_list : {},
        id_list: {
            BRANCH : "#branch-id",
            USER: "#branch_user",
            MANAGERS: "#manage_user_info"
        },
        variable_list: {
            DATA: Array(),
        },
        get_branch: function(){
            let self = this;
            let branch_info = Array();

            function create_element(){
                let elements = '';
                if(branch_info.id == null){
                    $("body").html('<h2 class="text-center">Şirket Bulunamadı</h2>');
                }else{
                    elements += `
                    <tr>
                        <td>${branch_info.id}</td>
                        <td>${branch_info.name}</td>
                        <td>${branch_info.main_id}</td>
                        <td>
                            <button type="button" class="btn btn-dark" function="branch_edit">
                                <i class="fas fa-cogs"></i>
                            </button>
                        </td>
                    </tr>
                `;
                }

                return elements;
            }

            get(
                get_types.GET_DETAILS,
                {"function_type": function_types.GET_BRANCH, "branch_id": branch_id},
                function (data) {
                    branch_info = data.rows[0];
                    $(self.id_list.BRANCH).html(create_element());
                }
            );
        },
        get_users: function () {
            let self = this, users = Array();

            function create_element(){
                let elements = '';
                users.forEach(user => {
                    elements += `
                        <tr>
                            <td>${user.id}</td>
                            <td>${user.name}</td>
                            <td>
                                <button type="button" class="btn btn-dark" function="branch_user_edit">
                                    <i class="fas fa-cogs"></i>
                                    </button>
                                <button type="button" class="btn btn-danger" function="branch_user_delete">
                                    <i class="fas fa-trash-alt"></i>                        
                                    </button>
                            </td>
                        </tr>
                    `;
                });

                return elements;
            }

            get(get_types.GET_DETAILS,
                {function_type: function_types.GET_BRANCH_USER, branch_id: branch_id},
                function (data) {
                    users = data.rows;
                    $(self.id_list.USER).html(create_element());
                }
            );
        },
        get_managers: function () {
            let self = this, managers = Array();
            let data = {function_type: function_types.GET_MANAGE_USER, branch_id: branch_id};

            function create_element() {
                let elements = '';

                managers.forEach(data => {
                    elements += `
                       <tr>
                           <td>${data.id}</td>
                           <td>${data.name}</td>
                           <td>
                                <button type="button" class="btn btn-dark" function="manage_user_edit">
                                    <i class="fas fa-cogs"></i>
                                </button>
                                <button type="button" class="btn btn-danger" function="manage_user_delete">
                                    <i class="fas fa-trash-alt"></i>                        
                                </button>
                           </td>
                       </tr>
                    `;
                });

                return elements;
            }

            get(get_types.GET_DETAILS, data, function (data) {
                managers = data.rows;
                $(self.id_list.MANAGERS).html(create_element());
            })
        },
        initialize: function (){
            let self = this;
            function set_events() {
                $(document).on("click", "button", function (){
                    let functions = $(this).attr("function");
                    switch (functions){
                        case "delete_orders_list":
                            let element = '';
                            let date_value = $(self.class_list.ORDERS_DATE).val();
                            let data = {
                                "function_type" : function_types.DELETE_ORDER_LIST,
                                "date" : date_value,
                                "branch_id" : branch_id,
                            }

                            get(get_types.DELETE_ORDERS, data, function (data) {
                                console.log(data);
                            });

                            return false;
                            break;
                    }
                });
            }

            /* Start Functions */
            set_events();
            self.get_branch();
            self.get_users();
            self.get_managers()
        }

    }

    /*
    @Functions basic
        @F Get Data
        @F Set Data
    */
    function get(get_type, data, succes_function = null){
        if(get_type !== null) data["get_type"] = get_type;

        $.ajax({
            url: `${default_ajax_path}get.php?branch_id=${branch_id}`,
            type: "POST",
            data: data,
            success: function (data){
                data = JSON.parse(data);
                if(succes_function !== null){
                    succes_function(data);
                }
            },
            timeout : settings.ajax_timeouts.NORMAL,

        });

    }
    function set(set_type, data, success_function = null){
        helper_sweet_alert.wait('transaction in progress', 'Your transaction is in progress, please wait...');
        if(set_type !== null) data["set_type"] = set_type;
        $.ajax({
            url: `${default_ajax_path}set.php`,
            type: 'POST',
            data: data,
            success: function(data){
                data = JSON.parse(data);
                if(success_function !== null){
                    success_function(data);
                }
            },
            error: helper_sweet_alert.close(),
            timeout : settings.ajax_timeouts.SLOW,
        });
    }

    return info;
})();

$(function(){
    let _info = new info();
});