let branch_info = (function () {
    let default_ajax_path = `${settings.paths.primary.PHP}branch/`;
    let branchId = 0;
    let selected_user_id = "";
    let get_types = {
        BRANCH_INFO : 1,
        GET_DETAILS : 2,
        DELETE_ORDERS: 3
    };
    let active_status = 0;
    let set_types = {
        USER_EDIT : 0x0003,
        BRANCH_EDIT : 0x0004,
        DELETE_ORDERS : 0x0005,
        BRANCH_DELETE : 0x0006,
        LOGO : 0x0007,
    }
    let function_type = {
        //Get data start
        GET_BRANCH  : 0x0001,
        GET_MAIN_BRANCH: 0x0002,
        GET_BRANCH_USER : 0x0002,
        GET_BRANCH_MANAGE_USER : 0x0003,
        //Operations function_type Start
        MANAGE_USER_DELETE : 0x0004,
        BRANCH_USER_DELETE : 0x0005,
        BRANCH_USER_EDIT: 0x0006,
        DELETE_ORDER_LIST: 0x0007,
        ORDERS_ALL_DELETE : 0x0008,
        ORDERS_SELECTED_DELETE : 0x0009,
        BRANCH_DELETE : 0x0011,
        BRANCH_EDIT : 0x0012,
        CREATE_LOGO : 0x0013
    }
    let global_data = {
        MANAGE_USER_ACCOUNT : {},
        BRANCH_USER_ACCOUNT: {},
        BRANCH_INFO : {},
        DATA: {},
        ORDERS_SELECTED_LIST : [],
        ORDERS_ALL_LIST : {},
        ORDER_ID : 0

    }
    /* Branch Id GET*/
    let params = new URLSearchParams(new URL(window.location.href).search)
    branchId = params.get('branch_id');

    function notification(){ initialize(); }
    function initialize(){
        get_branch_info.initialize();
        set_branch_info.initialize();
    }

    let get_branch_info = {
        variable_list: {
            ID : "",
            MESSAGE_ID : "",
            MAIN_BRANCH_SELECT: "#main_select_branch",
            IS_MAIN_CONTROL : "#is_main_control",
            SELECT_MAIN_BRANCH: "#main_select_branch",
            SELECT_BRANCH_BOX : "#select_branch_box",
            BRANCH_FORM : "#edit_branch_form",
            MAIN_SELECT : "#main_select_branch",
        },
        class_list: {
            ORDERS_DATE: ".orders_date",
        },
        data_types : {
            ADMIN_DATA : [],
            MESSAGE_DATA : Array(),

        },
        get_branch : function (){
            let self = this;
            get(get_types.GET_DETAILS, {"function_type": function_type.GET_BRANCH, "branch_id": branchId}, function (data) {
                let element = ``;
                global_data.BRANCH_INFO = data.rows;
                if (data.rows <= 1){
                    $(".content").html("<div class='text-center'><h4>Şirket Bulunamadı<br></h4><a href='index.php'>GeriDön</a></div>");
                }
                data.rows.forEach(function (e) {
                    element += `
                        <tr data-id="${e.id}" >
                            <td>${e.id}</td>
                            <td id="branch_name">${e.name}</td>
                            <td>
                                <button type="button" class="btn btn-dark" function="branch_edit">
                                    <i class="fas fa-cogs"></i>
                                </button>
                           </td>        
                        </tr>`;
                });
                $("#branch-id").html(element);
            })
            $(self.variable_list.MAIN_SELECT).select2();
            /* Get Main Branch*/
            function create_element(data) {
                let elements = '';
                elements += `<option value="0">Ana Şube Yok</option>`;

                data.forEach(item => {
                    elements += `<option value="${item.id}">${item.name}</option>`;
                });

                return elements;
            }
            get(get_types.BRANCH_INFO, {"function_type": function_type.GET_MAIN_BRANCH}, function (data) {
                $(self.variable_list.MAIN_BRANCH_SELECT).html(create_element(data.rows));
            });
        },
        get_manage_user : function (){
            get(get_types.GET_DETAILS, {function_type: function_type.GET_BRANCH_MANAGE_USER, branch_id: branchId}, function (data) {
                global_data.MANAGE_USER_ACCOUNT = data;
                let element = ` `;
                data.rows.forEach(function (e) {
                    element += `
                        <tr data-id="${e.id}" class="border-top border-dark">
                            <td>${e.id}</td>
                            <td class="_name">${e.name}</td>
                            <td class="text-right">
                                <button type="button" class="btn btn-dark" function="manage_user_edit">
                                    <i class="fas fa-cogs"></i>
                                </button>
                                <button type="button" class="btn btn-danger" function="manage_user_delete">
                                    <i class="fas fa-trash-alt"></i>                        
                                </button>
                            </td>
                        </tr>`;
                });
                $("#manage_user_info").html(element);
            })
        },
        get_branch_user : function(){
            get(get_types.GET_DETAILS, {function_type: function_type.GET_BRANCH_USER, branch_id: branchId}, function (data) {
                global_data.BRANCH_USER_ACCOUNT = data;
                let element = ` `;
                data.rows.forEach(function (e) {
                    element += `
                <tr class="" data-id="${e.id}" class="border-top-1 border-primary">
                    <td>${e.id}</td>
                    <td>${e.name}</td>
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
                $("#branch_user").html(element);
            });
        },
        initialize: function (){
            let self = this;
            function set_events(){
                $(document).on("click", "button", function () {
                   let functions = $(this).attr("function");
                   switch (functions){
                       case "delete_orders_list":
                           let element = ``;
                           let date_value = $(self.class_list.ORDERS_DATE).val();
                           let data = { "function_type" : function_type.DELETE_ORDER_LIST, "date" : date_value, "branch_id" : branchId };
                           get(get_types.DELETE_ORDERS, data, function (data){
                               global_data.ORDERS_ALL_LIST = data.rows;
                               data.rows.forEach(function (e) {
                                   element += `
                            <tr class="" data-id="${e.id}">
                                 <td>${e.id}</td>
                                <td>${e.date_start}</td>
                                <td>
                                    <button class="btn btn-warning" function="select_orders_list" is_add="0">
                                        ekle/kaldır
                                    </button>               
                                 </td>
                            </tr>`;
                               })
                               $("#delete_products").html(element);
                           })
                           return false;
                           break;
                       case "branch_edit":
                           let main_branch_box = global_data.BRANCH_INFO[0];
                           $(self.variable_list.BRANCH_FORM).autofill(array_list.find(global_data.BRANCH_INFO, parseInt(branchId), "id"));

                           if(main_branch_box.is_main == 0){
                               $(self.variable_list.SELECT_BRANCH_BOX).css("display", "block");
                           }else{
                               $(self.variable_list.SELECT_BRANCH_BOX).css("display", "none");
                           }
                       break;
                   }
                });

                $(document).on("change", self.variable_list.IS_MAIN_CONTROL, function () {
                    let select_val = $(this).val();
                    if(select_val == 0){
                        $(self.variable_list.SELECT_BRANCH_BOX).css("display", "block");
                    }else{
                        $(self.variable_list.SELECT_BRANCH_BOX).css("display", "none");
                    }
                });
            }

            set_events();
            this.get_branch();
            this.get_manage_user();
            this.get_branch_user();
        },
    }
    let set_branch_info = {
        id_list : {
            MANAGE_USER : "#manage_user",
            BRANCH_USER : "#branch_user_form",
            BRANCH : "#branch_form",
            USER_ID : "#user_id",
            CONFIRM_DELETE : "#confirm_delete",
            DESCRIPTION_NAME : "#description_name",
            BRANCH_NAME : "#branch_name",
            BRANCH_FORM : "#edit_branch_form",
            SHOW_PASS : "#show-passw",
            TYPE : "#type",
            DESC: "#desc",
            BRANCH_IMAGE: "#branch_img",
            IMAGE: "#product_edit_image",
            INPUT_FILE_IMAGE: "#product_file_image",
            IMAGE_CROP_AREA: "#product_image_crop_area",
        },
        modal_id :{
            MANAGE_USER_EDIT : "#manage_user_edit",
            BRANCH_USER_EDIT: "#branch_user_edit",
            BRANCH_EDIT: "#modal_edit_branch",
        },
        class_list : {
            //status active button
            ACTIVE_BTN: ".e_active",
            NOT_ACTIVE_BTN: ".e_not_active",
            //logo crop button
            CROP_BTN : ".e_btn_crop",
            LOGO_SAVE : ".e_btn_save",
        },
        initialize : function () {
            let self = this;
            function set_events() {
                $(document).on("click", "button", function () {
                    let value = $(this).attr("function");
                    let confirm_text = $("input[name=check]").val();
                    let type = $("input[name=type]").val();
                    let data = global_data.DATA;
                    switch (value) {
                        case "manage_user_edit":
                            selected_user_id = $(this).closest("tr").attr("data-id");
                            $(self.modal_id.MANAGE_USER_EDIT).modal();
                            $(self.id_list.MANAGE_USER).autofill(array_list.find(global_data.MANAGE_USER_ACCOUNT.rows, parseInt(selected_user_id), "id"));
                            break;
                        case "branch_user_edit":
                           selected_user_id = $(this).closest("tr").attr("data-id");
                           var user_data = array_list.find(global_data.BRANCH_USER_ACCOUNT.rows, parseInt(selected_user_id), "id")
                           $(self.modal_id.BRANCH_USER_EDIT).modal();
                           if(user_data.active === 0){
                               $(self.class_list.ACTIVE_BTN).show();
                               $(self.class_list.NOT_ACTIVE_BTN).hide();
                           }else {
                               $(self.class_list.ACTIVE_BTN).hide();
                               $(self.class_list.NOT_ACTIVE_BTN).show();
                           }
                           $(self.id_list.BRANCH_USER).autofill(user_data);
                            break;
                        case "branch_edit":
                           $(self.modal_id.BRANCH_EDIT).modal();
                           break;
                        case "add_manage_user":
                           selected_user_id = "" ;
                           $(self.id_list.MANAGE_USER).trigger("reset");
                           $(self.modal_id.MANAGE_USER_EDIT).modal();
                           break;
                        case "manage_user_delete":
                           $(self.id_list.USER_ID).val($(this).closest("tr").attr("data-id"));
                           $(self.id_list.DESCRIPTION_NAME).html($(this).closest("tr").children("._name").text());
                           $(self.id_list.DESC).show();
                           $(self.id_list.TYPE).val(1);
                           $(self.id_list.CONFIRM_DELETE).modal();
                           break;
                        case "branch_user_delete":
                           $(self.id_list.USER_ID).val($(this).closest("tr").attr("data-id"));
                           $(self.id_list.DESCRIPTION_NAME).html($(this).closest("tr").children("._name").text());
                           $(self.id_list.DESC).show();
                           $(self.id_list.TYPE).val(2);
                           $(self.id_list.CONFIRM_DELETE).modal();
                           break;
                        case "branch_delete":
                           let branch_name = $("#branch_name").html();
                           $(self.id_list.DESCRIPTION_NAME).html(branch_name);
                           $(self.id_list.TYPE).val(3);
                           $(self.id_list.DESC).show();
                           $(self.id_list.CONFIRM_DELETE).modal();
                               break;
                        case "select_orders_list":
                            let check = $(this).attr("class");
                            let id = $(this).closest("tr").attr("data-id");
                            if(check == "btn btn-warning"){
                                $(this).attr("class", "btn btn-danger");
                                global_data.ORDERS_SELECTED_LIST.push(array_list.find(global_data.ORDERS_ALL_LIST,parseInt(id), "id"));
                            }
                            else{
                                $(this).attr("class", "btn btn-warning");
                                let Removed_item = array_list.find(global_data.ORDERS_ALL_LIST,parseInt(id), "id");
                                global_data.ORDERS_SELECTED_LIST.splice(global_data.ORDERS_SELECTED_LIST.findIndex(a => a.id === Removed_item.id) , 1)
                            }
                            break;
                        case "orders_selected_delete":
                            $(self.id_list.DESCRIPTION_NAME).html("Seçilen Verileri Silmek İçin 'onayla' yazıp Gönderin");
                            $(self.id_list.DESC).hide();
                            $(self.id_list.TYPE).val(4);
                            $(self.id_list.CONFIRM_DELETE).modal();
                            return false;
                            break;
                        case "orders_all_delete":
                            $(self.id_list.DESCRIPTION_NAME).html("Tüm Verileri Silmek İçin 'onayla' yazıp Gönderin");
                            $(self.id_list.DESC).hide();
                            $(self.id_list.TYPE).val(5);
                            $(self.id_list.CONFIRM_DELETE).modal();
                            return false;
                            break;
                        case "branch_img":
                            $(self.id_list.BRANCH_IMAGE).modal();
                            let image = $(self.id_list.IMAGE).attr("src");
                            break;
                        case "check_input":
                                /*  delete values
                                    manage users : 1
                                    branch users : 2
                                    branch : 3
                                    ORDERS_SELECTED_DELETE : 4,
                                    ORDERS_ALL_DELETE : 5
                                    */
                                    if(confirm_text !== "onayla"){
                                        helper_sweet_alert.error("Ooops", "Onay Kodu Hatalı");
                                        window.location.href = "index.php"
                                    }
                                    else {
                                        switch (type) {
                                            case "1":
                                                data["id"] =  $(self.id_list.USER_ID).val();
                                                data["function_type"] = function_type.MANAGE_USER_DELETE;
                                                set(set_types.USER_EDIT, data, function (data) {
                                                    helper_sweet_alert.success("Başarılı", "Silme İşlemi Tamamlandı");
                                                    get_branch_info.get_manage_user();
                                                });
                                                break;
                                            case "2":
                                                data["id"] =  $(self.id_list.USER_ID).val();
                                                data["function_type"] = function_type.BRANCH_USER_DELETE;
                                                data["branch_id"] = branchId;
                                                set(set_types.USER_EDIT, data, function (data) {
                                                    helper_sweet_alert.success("Başarılı", "Silme İşlemi Tamamlandı");
                                                    get_branch_info.get_branch_user();
                                                });
                                                break;
                                            case "3":
                                                data["branch_id"] = branchId
                                                set(set_types.BRANCH_DELETE, data, function(){});
                                                setTimeout(() =>   window.location.href = "index.php" , 500)
                                                break;
                                            case "4":
                                                let orders = {};
                                                orders["function_type"] = function_type.ORDERS_SELECTED_DELETE;
                                                orders["orders"] = global_data.ORDERS_SELECTED_LIST;

                                                set(set_types.DELETE_ORDERS, data, function(){});
                                                break;
                                            case "5":
                                                data["function_type"] = function_type.ORDERS_ALL_DELETE;
                                                data["orders"] = global_data.ORDERS_ALL_LIST;
                                                set(set_types.DELETE_ORDERS, data, function(){})
                                                break;
                                        }
                                    }
                                    break;
                        case "image_saved":
                            let form_data = new FormData();
                            let logo = $(self.id_list.IMAGE).attr("src");
                            if(variable.is_base64(logo)) {
                                logo = variable.base64_to_blob(logo);
                            }
                            console.log(logo);
                            form_data.append("image", logo);
                            form_data.append("set_type", set_types.LOGO);
                            form_data.append("branch_id", branchId);
                            form_data.append("function_type", function_type.CREATE_LOGO);
                            console.log(form_data);
                            $.ajax({
                                url: `${default_ajax_path}set.php`,
                                type: "POST",
                                cache: false,
                                contentType: false,
                                processData: false,
                                data: form_data,
                                success: function (data) {
                                    console.log(data);
                                    helper_sweet_alert.success("Başarılı", "Logo <b>eklendi</b>!");
                                },error: helper_sweet_alert.close(), timeout: settings.ajax_timeouts.NORMAL
                            });
                            break;
                    }
                });
                $(document).on("click", "a", function () {
                    let value = $(this).attr("function");
                    switch (value) {
                        case "active":
                            $(self.class_list.ACTIVE_BTN).hide();
                            $(self.class_list.NOT_ACTIVE_BTN).show();
                            active_status = 1;
                            break;
                        case "not_active":
                            $(self.class_list.ACTIVE_BTN).show();
                            $(self.class_list.NOT_ACTIVE_BTN).hide();
                            active_status = 0;
                            break;
                    }
                });
                $(document).on("submit", self.id_list.MANAGE_USER, function () {
                    let data = $("#manage_user").serializeObject();
                    data["id"] = selected_user_id;
                    data["branch_id"] = branchId;
                    set(set_types.USER_EDIT, data,function (data) {
                        get_branch_info.get_manage_user();
                    });
                        return false;
                });
                //user
                $(document).on("submit", self.id_list.BRANCH_USER, function () {
                    let data = $("#branch_user_form").serializeObject();
                        data["id"] = selected_user_id,
                        data["branch_id"] = branchId,
                        data["function_type"] = function_type.BRANCH_USER_EDIT,
                        data["active"] = active_status
                    set(set_types.USER_EDIT, data, function (response) {
                        if (response.status){
                            get_branch_info.get_branch_user();
                        }
                    });
                    return false;
                });
                //
                $(document).on("submit",self.id_list.BRANCH_FORM, function (e) {
                    let serialize_form = $(this).serializeObject();

                    if(serialize_form.is_main == 1){
                        serialize_form.main_id = 0;
                    }

                    set(set_types.BRANCH_EDIT, serialize_form, function(){});
                    return false;
                });
                //Password Eye on/of
                $(document).on("click", self.id_list.SHOW_PASS, function () {
                    let type = $("input[name=password]").attr("type")
                    if(type == "text"){
                    $("input[name=password]").attr("type", "password");
                    $(".eye").attr("class", "fas fa-eye-slash eye");
                    }
                    else {
                        $("input[name=password]").attr("type", "text");
                        $(".eye").attr("class", "fas fa-eye eye");
                    }
                });
            }
            function initialize_crop() {
                crop.initialize(
                    640,
                    360,
                    640,
                    360,
                    self.id_list.INPUT_FILE_IMAGE,
                    self.class_list.CROP_BTN,
                    self.id_list.IMAGE,
                    "",
                    self.id_list.IMAGE_CROP_AREA
                );
            }
            set_events();
            initialize_crop();
            let random = Math.floor(Math.random() * 999);
            $(self.id_list.IMAGE).attr("src", settings.paths.image.BRANCH_LOGO(branchId)+"?v=0."+random);

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
                data = JSON.parse(data);
                if (success_function !== null){
                    success_function(data);
                }
            },error: helper_sweet_alert.close(),timeout: settings.ajax_timeouts.NORMAL
        });
    }
    function get(get_type, data, success_function = null){
        if (get_type !== null)  data["get_type"] = get_type;
        $(".loader-box").show();
        if(branchId){
          $.ajax({
                url: `${default_ajax_path}get.php?branch_id=${branchId}`,
                type: "POST",
                data: data,
                success: function (data) {
                    data = JSON.parse(data);
                    if (success_function !== null){
                        success_function(data);
                    }
                }, timeout: settings.ajax_timeouts.VERY_SLOW
            });
        }
    }
    return notification;
})();

$(function () {
    let _branch_info = new branch_info();
});

