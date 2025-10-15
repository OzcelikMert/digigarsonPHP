
let page_products = (function() {
    let default_ajax_path = `${settings.paths.primary.PHP}products/`;
    let get_types = {
        TRANSLATE_PRODUCT: 0x0001,
        TRANSLATE_CATEGORY: 0x0002
    };
    let set_types = {
        TRANSLATE_PRODUCT: 0x0007,
        TRANSLATE_CATEGORY: 0x0008
    };

    function page_products(){ initialize(); }

    function initialize(){
        page.initialize();
        modal_category.initialize();
        modal_select_category.initialize();
        modal_product.initialize();
        modal_edit_options.initialize();
        modal_get_options.initialize();
        modal_translate_product.initialize();
        modal_translate_category.initialize();
        let barcode_system_interval = setInterval(function (){
            if (typeof app.customize_settings !== "undefined"){
                if (app.customize_settings.enableBarcodeSystem) modal_scales_products.initialize();
                clearInterval(barcode_system_interval)
            }
        },500)

    }

    let modal_select_category = {
        id_list: {
            CATEGORIES: "#select_categories",
            MODAL: "#modal_select_category",
        },
        class_list: {
            BUTTON_SELECT_CATEGORY: ".e_btn_select_category"
        },
        SELECTED_CATEGORY: 0,
        get: function(){
            let self = this;

            function create_element(){
                let element = ``;

                main.data_list.PRODUCT_CATEGORIES.forEach(category => {
                    element += `
                        <div class="col-md-3 modal-category-select-box">
                            <a href="javascript:void(0)" class="e_btn_select_category" category-id="${category.id}" data-dismiss="modal">
                                <span class="${(self.SELECTED_CATEGORY === category.id) ? "selected" : ""}">${category.name}</span>
                            </a>
                        </div>
                    `;
                });

                return element;
            }

            $(self.id_list.CATEGORIES).html(create_element());
        },
        initialize: function (){
            let self = this;

            function set_events(){
                $(self.id_list.MODAL).on("show.bs.modal", function () {
                    self.get();
                });

                $(document).on("click", self.class_list.BUTTON_SELECT_CATEGORY, function () {
                    let element = $(this);
                    let id = parseInt(element.attr("category-id"));

                    self.SELECTED_CATEGORY = (self.SELECTED_CATEGORY === id) ? 0 : id;
                    page.get();
                });
            }

            set_events();
        }
    };

    let modal_edit_options = {
        id_list: {
            FORM_OPTION: "#option_form",
            MODAL: "#modal_set_options",
            OPTION_TYPE_SELECT : "#option_type",
            NEW_OPTION_BTN: "#new_option_btn"
        },
        class_list: {
            CREATE_OPTION_BTN: ".e_create_option_btn",
            ITEM_DEL_BTN :  ".e_option_item_del",
            ITEM_ADD_BTN :  ".e_option_add_item",
            OPTION_INFO:    ".e_option_info",
            AREA_ITEMS:     ".e_option_items_area",
            AREA_CREATE:    ".e_option_create_area",
            AREA_LIST:      ".e_option_list_area",
            AREA_CHAR:      ".e_option_char_area",
            AREA_LIMIT:     ".e_option_limit_area",
        },
        option_type: {
            MATERIALS: 1,
            SINGE_SELECT: 2,
            MULTI_SELECT: 3,
            SINGE_PRODUCT_SELECT: 4,
            QUANTITY_SELECT: 5,
        },
        set_type: {
            INSERT_OPTION : 0x0005,
            DELETE_OPTION : 0x0006,
        },
        edit_type: {
            NONE: 0,
            ADD : 1,
            UPDATE : 2,
            DELETE : 3,
        },
        SELECTED_CATEGORY: 0,
        cols: Array(),
        set: function(set_type, data){
            let self = this;
            helper_sweet_alert.wait(language.data.PROCESS_PROGRESS_TITLE, language.data.PROCESS_WAIT_CONTENT);
            data.set_type = set_type;
            console.log(data);
            $.ajax({
                url: `${default_ajax_path}set.php`,
                type: "POST",
                data: data,
                success: function (data) {
                   data = JSON.parse(data);
                    console.log(data);
                    if(data.status){
                        main.get_product_related_things(main.get_type_for_product_related_things.OPTIONS);
                        modal_get_options.get();
                    }

                    switch (data.error_code){
                        case settings.error_codes.EMPTY_VALUE:
                            helper_sweet_alert.error(language.data.SAVE_ERROR_TITLE, language.data.REQURIED_MESSAGE);
                            break;
                        case settings.error_codes.WRONG_VALUE:
                            helper_sweet_alert.error(language.data.SAVE_ERROR_TITLE, language.data.INVALID_OPERATAION_MESSAGE);
                            break;
                        case settings.error_codes.REGISTERED_VALUE:
                            $(self.id_list.MODAL).modal("toggle");
                            modal_get_options.is_open(1);
                            helper_sweet_alert.success(language.data.UPDATE_SUCCESS_TITLE, language.data.UPDATE_SUCCESS_MESSAGE);
                            break;
                        case settings.error_codes.SUCCESS:
                            helper_sweet_alert.success(language.data.PROCESS_SUCCESS_TITLE, language.data.CATEGORY_ADD_MESSAGE);
                            break;
                    }


                },error: helper_sweet_alert.close(), timeout: settings.ajax_timeouts.NORMAL
            });
        },
        edit: function(id){
            let self = this;

            $(`${self.class_list.OPTION_INFO} input[name=option_list]`).attr("disabled"," ").val(" ");
            $(`${self.class_list.OPTION_INFO} select[name=char]`).attr("disabled"," ");
            $(self.class_list.AREA_CREATE).css("display","none");
            $(self.class_list.AREA_LIST).css("display","none");
            $(self.class_list.AREA_CHAR).css("display","none");
            $(self.class_list.AREA_LIMIT).removeClass("col-md-6").removeClass("pr-1").addClass("col-md-12");

            let option_items = "";
            let values = array_list.find(main.data_list.PRODUCT_OPTIONS,id,"id");
            $(self.id_list.MODAL).modal();

            Array.from($(`${self.id_list.FORM_OPTION} ${self.class_list.OPTION_INFO} .item input[name],${self.id_list.FORM_OPTION} ${self.class_list.OPTION_INFO} .item select[name]`)).forEach(function(e){

                let input_type = $(e).attr("name");
                switch(input_type){
                 case "option_id" :  $(e).val(values.id);break;
                 case "name" :       $(e).val(values.name);break;
                 case "search_name": $(e).val(values.search_name);break;
                 case "limit" :      $(e).val(values.selection_limit);break;
                 case "type":        $(`${self.id_list.OPTION_TYPE_SELECT} option[value=${values.type}]`).attr('selected','selected');break;
                 case "option_list":
                     array_list.find_multi(main.data_list.PRODUCT_OPTIONS_ITEMS,id,"option_id").forEach(function (item){
                         option_items += self.create_input(item.name,values.type,self.edit_type.NONE,item.id,item.price,item.quantity,item.is_default)
                     });
                     $(self.class_list.AREA_ITEMS).html(self.create_title(self.cols) + option_items);
                     break;
                }
            });
        },
        create_input: function (name,option_type = 0,edit_type = 1,item_id= 0,price=0,quantity = 0,active=false){
            let self = this;
            let select_box = "";
            let col_size = Array();
            active = (active) ? `checked="checked"` : "";

            col_size = ["col-2", "col-6", "col-2", "", "col-2"]
            if(self.option_type.QUANTITY_SELECT === option_type){col_size = ["col-2", "col-5", "col-2", "col-2", "col-1"]}

            if (self.option_type.MULTI_SELECT === option_type){
                select_box = `<div class="${col_size[4]} pl-0 xl-select-box"> <input name="active" type="checkbox" class="form-input" ${active}></div>`;
            }else if (self.option_type.SINGE_SELECT === option_type || self.option_type.QUANTITY_SELECT === option_type){
                select_box = `<div class="${col_size[4]} pl-0 xl-select-box"> <input name="active" type="radio" class="form-input" ${active}></div>`;
            }
            if (self.option_type.MATERIALS === option_type) col_size = ["col-2", "col-10", "", "", "",];

            self.cols = col_size;
            return `
                    <div item-id="${item_id}" edit-type="${edit_type}" class="item col-12 row m-0 mt-2">
                       ${( self.cols[0] !== "") ? `<div class="${col_size[0]} pl-1 pr-1"><button class="e_option_item_del btn btn-danger" type="button">Sil</button></div>` : "" }
                       ${( self.cols[1] !== "") ? `<div class="${col_size[1]} pl-0 pr-1"> <input  name="name" type="text" class="e_name form-input" required="" value="${name}"></div>` : "" }
                       ${( self.cols[2] !== "") ? `<div class="${col_size[2]} pl-0 pr-1"> <input  name="price" type="number" class="e_price form-input" required="" value="${price}" step="0.01"></div>` : "" }
                       ${( self.cols[3] !== "") ? `<div class="${col_size[3]} pl-0 pr-1"> <input  name="quantity" type="number" class="e_name form-input" required="" value="${quantity}" step="0.01"></div>` : "" }
                       ${( self.cols[4] !== "") ? `${select_box}` : "" }
                    </div>
                `;
        },
        create_title: function(cols=Array()){
            return `
                <div class="col-12 row mt-2 m-0">
                    ${(cols[0] !== "") ? `<div class="${cols[0]}"><label class="col-form-label mt-2 pl-1 pr-1"><lang>Sil</lang></label></div>` : "" }
                    ${(cols[1] !== "") ? `<div class="${cols[1]} pl-1 pr-1"><label class="col-form-label mt-2"><lang>İsim</lang></label></div>` : "" }
                    ${(cols[2] !== "") ? `<div class="${cols[2]} pl-1 pr-1"><label class="col-form-label mt-2"><lang>Fiyat</lang></label> </div>` : "" }
                    ${(cols[3] !== "") ? `<div class="${cols[3]} pl-1 pr-1"><label class="col-form-label mt-2"><lang>Miktar</lang></label></div>` : "" }
                    ${(cols[4] !== "") ? `<div class="${cols[4]} pl-1 pr-1"><label class="col-form-label mt-2"><lang>Vars.</lang></label></div>` : "" }
                </div>`;
        },
        delete_option: function (id){
            let self = this;
            self.set(self.set_type.DELETE_OPTION,{"id": id});

        },
        initialize: function (){
            let self = this;
            function set_events(){
                //Create Options Elements
                $(document).on("click", self.class_list.CREATE_OPTION_BTN, function () {
                    let values = Array();
                    let html = "";

                    // values => char limit name option_list search_name type
                    Array.from($(`${self.class_list.OPTION_INFO} [name]`)).forEach(function(e){
                        e = $(e); values[e.attr("name")] = e.val();
                    })
                    let options = (values.option_list).split(values.char);
                    options.forEach(function (e){
                        console.log("-")
                        html += self.create_input(e,parseInt(values.type),self.edit_type.ADD);
                    });
                    $(self.class_list.AREA_ITEMS).html(self.create_title(self.cols) + html);
                });
                //Del Option Items
                $(document).on("click",`button${self.class_list.ITEM_DEL_BTN}`,function (){
                    let element = $(this).closest(".item");
                    let id = parseInt(element.attr("item-id"));
                    if (id === 0){
                        element.remove();
                    }else {
                        element.html("");
                        element.removeClass("mt-2");
                        element.attr("edit-type",self.edit_type.DELETE)
                    }

                });
                /// -------------------- ADD Items --------------------------------------///
                $(document).on("click",`button${self.class_list.ITEM_ADD_BTN}`,function (){
                    let type = parseInt($("#option_type").val());
                    $(self.class_list.AREA_ITEMS).append(self.create_input("",type));
                });
                /// -------------------- CHANGE --------------------------------------///
                $(document).on("change",`${self.class_list.AREA_ITEMS} input[name]`,function (){
                    let element = $(this).closest(".item");
                    if (parseInt(element.attr("edit-type")) === self.edit_type.ADD) return;
                    element.attr("edit-type",self.edit_type.UPDATE)
                });
                /// ------------------- New Option Btn ---------------------------------///
                $(document).on("click",self.id_list.NEW_OPTION_BTN,function (){
                    //clear input values
                    Array.from($(".e_option_info input[name]")).forEach(function (e){ $(e).val(""); });
                    //new item id = 0
                    $(".e_option_info input[name=option_id]").val(0)
                    //selected firt child
                    $(`${self.id_list.OPTION_TYPE_SELECT} option:first-child`).attr('selected','selected');
                    //clear option item area
                    $(self.class_list.AREA_ITEMS).html("");
                    $(`${self.class_list.OPTION_INFO} input[name=option_list]`).removeAttr("disabled").val("");
                    $(`${self.class_list.OPTION_INFO} select[name=char]`).removeAttr("disabled");
                    $(self.class_list.AREA_CREATE).css("display","unset");
                    $(self.class_list.AREA_LIST).css("display","unset");
                    $(self.class_list.AREA_CHAR).css("display","unset");
                    $(self.class_list.AREA_LIMIT).addClass("col-md-6").addClass("pr-1").removeClass("col-md-12");

                })
                /// -------------------- SUBMIT --------------------------------------///
                $(self.id_list.FORM_OPTION).submit(function(e){
                    e.preventDefault();

                    let element = Array.from($(`${self.class_list.AREA_ITEMS} .item`));
                    let values = {};
                        values.settings = {};
                        values.add_items = {};
                        values.delete_items = {};
                        values.update_items = {};
                    let items = {};
                    let children = null;
                    let index = 0;
                    let name = null;
                    let s_element = null;
                    let id = null;
                    let edit_type = 0;

                    // values => char limit name option_list search_name type
                    Array.from($(`${self.class_list.OPTION_INFO} [name]`)).forEach(function(e){
                        e = $(e); values.settings[e.attr("name")] = e.val();
                    })

                    element.forEach(function (e){
                        s_element = $(e);
                        id = parseInt(s_element.attr("item-id"));
                        children = Array.from($(e).children().children("input"));
                        edit_type = parseInt(s_element.attr("edit-type"));

                        switch (edit_type){
                            case self.edit_type.DELETE:
                                values.delete_items[index] = {id:id};
                                break;
                            case self.edit_type.ADD:
                              values.add_items[index] = {id:id,name: null, price: 0, quantity: 0, active: 0}
                                children.forEach(function (c){
                                    name = $(c).attr("name");
                                    values.add_items[index][name] = (name === "active") ? ParseIntToStringBool($(c).is(':checked')) :  $(c).val();
                                });
                                break;
                            case self.edit_type.UPDATE:
                                values.update_items[index] = {id:id,name: null, price: 0, quantity: 0, active: 0}
                                children.forEach(function (c){
                                    name = $(c).attr("name");
                                    values.update_items[index][name] = (name === "active") ? ParseIntToStringBool($(c).is(':checked')) :  $(c).val();
                                });
                                break;
                        }
                        index++;
                    })
                    self.set(self.set_type.INSERT_OPTION,values);
                    $(self.class_list.AREA_ITEMS).html("");
                });
            }
            function ParseIntToStringBool(bool) {
                // true, "true", "on", "yes" -> (1 / 0)
                let value = 0;
                if (bool === true || bool === "true" || bool === "on" || bool === "yes") {
                    value = 1
                }
                return value;
            }
            set_events();
        }
    };

    let modal_get_options = {
        id_list: {
            FORM_OPTION: "#option_form",
            OPEN_MODAL_BTN: "#option_list_btn",
            MODAL: "#modal_get_options",
        },
        class_list: {
            OPTION_LIST: ".e_option_list",
            OPTION_TOOLS : ".e_option_tools"
        },
        function_types: {
            FUNCTION: "function",
            EDIT: "edit",
            DELETE: "del",
        },
        get: function(){
            let self = this;
            $(self.class_list.OPTION_LIST).html(create_element());

            function create_element(){
                let values = "";
                let options_items = "";
                main.data_list.PRODUCT_OPTIONS.forEach(function (v){
                    let btns = `
                        <button class="btn btn-s1 m-0" style="height: 45px;" function="edit"><lang>EDIT</lang></button>
                        <button class="btn btn-s3 m-0" style="height: 45px;" function="del"><lang>DELETE</lang></button>`;

                    values +=
                        `<tr option_id="${v.id}">
                            <td>${v.id}</td>
                            <td>${v.search_name}</td>
                            <td>${v.name}</td>
                            <td>${array_list.find(main.data_list.OPTION_TYPES,v.type,"id").name}</td>
                            <td>${v.selection_limit}</td>
                            <td class="e_option_tools m-0 p-0" style="width: 1%; white-space: nowrap;">${btns}</td>
                        </tr>`;
                    options_items = "";
                });

                return values;
            }
        },
        is_open: function (is_open = 1){
            (is_open === 1) ? $(this.id_list.MODAL).modal("show") : $(this.id_list.MODAL).modal("hide");
        },
        initialize: function (){
            let self = this;
            function set_events(){
                $(self.id_list.MODAL).on("show.bs.modal", function () {
                    self.get();
                });
                $(self.id_list.MODAL).on("hide.bs.modal", function () {
                   $( self.id_list.MODAL).attr("linked","0");
                });

                $(document).on("click", self.id_list.OPEN_MODAL_BTN, function (){
                    $(self.class_list.OPTION_TOOLS).show();
                });

                $(document).on("click", `${self.class_list.OPTION_LIST} button[function]`, function () {
                    let element = $(this);
                    let id = parseInt(element.closest("tr").attr("option_id"));

                    if ( element.attr(self.function_types.FUNCTION) === self.function_types.EDIT ){
                       $(self.id_list.MODAL).modal("toggle");
                       modal_edit_options.edit(id)
                    }else if ( element.attr(self.function_types.FUNCTION) === self.function_types.DELETE  ){
                        Swal.fire({
                            icon: "question",
                            title: language.data.DELETE_PROCESS_TITLE,
                            html: language.data.DELETE_PROCESS_TEXT,
                            allowEscapeKey: false,
                            allowOutsideClick: false,
                            showCancelButton: true,
                            confirmButtonText: language.data.DELETE,
                            cancelButtonText: "İptal",
                            confirmButtonClass: 'btn btn-success btn-lg mr-3 mt-5',
                            cancelButtonClass: 'btn btn-danger btn-lg ml-3 mt-5',
                            buttonsStyling: false
                        }).then((result) => {
                            if (result.value) {
                                modal_edit_options.delete_option(id)
                            }
                        });
                    }

                });
                
            }
            set_events();
        }
    };

    let modal_category = {
        id_list: {
            MODAL: "#modal_set_category",
            FORM: "#form_set_category",
            TABLE: "#categories_table",
            MAIN_CATEGORIES_OPTION: "#main_categories"
        },
        class_list: {BTN_OPERATION: ".e_category_operation_btn"},
        name_list: {
            SELECT_MAIN_CATEGORY: "main_category",
            INPUT_ID: "id",
            INPUT_CATEGORY_ACTIVE: "category_active",
            INPUT_NAME: "name",
            INPUT_ACTIVE_START_TIME: "active_start_time",
            INPUT_END_START_TIME: "end_start_time"
        },
        element_list: {
            get self() { return modal_category; },
            get SELECT_MAIN_CATEGORY() { return helper.get_form_element_with_name(this.self.id_list.FORM, this.self.name_list.SELECT_MAIN_CATEGORY, helper.element_types.SELECT); },
            get INPUT_ID() { return helper.get_form_element_with_name(this.self.id_list.FORM, this.self.name_list.INPUT_ID); },
            get INPUT_CATEGORY_ACTIVE() { return helper.get_form_element_with_name(this.self.id_list.FORM, this.self.name_list.INPUT_CATEGORY_ACTIVE); },
            get INPUT_NAME() { return helper.get_form_element_with_name(this.self.id_list.FORM, this.self.name_list.INPUT_NAME); },
            get INPUT_ACTIVE_START_TIME() { return helper.get_form_element_with_name(this.self.id_list.FORM, this.self.name_list.INPUT_ACTIVE_START_TIME); },
            get INPUT_END_START_TIME() { return helper.get_form_element_with_name(this.self.id_list.FORM, this.self.name_list.INPUT_END_START_TIME); }
        },
        tree_table: null,
        SELECTED_MAIN_CATEGORY: 0,
        set: function(){
            let self = this;
            let form_data = $(self.id_list.FORM).serialize();
            $.ajax({
                url: `${default_ajax_path}set_category.php`,
                type: "POST",
                data: form_data,
                success: function (data) {
                    data = JSON.parse(data);
                    console.log(data);
                    switch (data.error_code){
                        case settings.error_codes.EMPTY_VALUE:
                            helper_sweet_alert.error(language.data.SAVE_ERROR_TITLE, language.data.SAVE_ERROR_TITLE);
                            break;
                        case settings.error_codes.WRONG_VALUE:
                            helper_sweet_alert.error(language.data.SAVE_ERROR_TITLE, language.data.CATEGORY_ERROR_TEXT);
                            break;
                        case settings.error_codes.NOT_FOUND:
                            helper_sweet_alert.error(language.data.SAVE_ERROR_TITLE, language.data.CATEGORY_SAVE_ERROR_TEXT);
                            break;
                        case settings.error_codes.REGISTERED_VALUE:
                            helper_sweet_alert.success(language.data.UPDATE_SUCCESS_TITLE, language.data.CATEGORY_UPDATE_MESSAGE);
                            break;
                        case settings.error_codes.SUCCESS:
                            helper_sweet_alert.success(language.data.PROCESS_SUCCESS_TITLE, language.data.CATEGORY_ADD_MESSAGE);
                            break;
                    }
                    if(!data.status) return;

                    main.get_product_related_things(main.get_type_for_product_related_things.CATEGORIES);
                    self.initialize.tree_table();
                    self.SELECTED_MAIN_CATEGORY = self.element_list.SELECT_MAIN_CATEGORY.val();
                    if(self.element_list.INPUT_ID.val() > 0){
                        $(self.id_list.FORM).trigger("reset");
                    }
                    modal_product.element_list.SELECT_CATEGORY.html(helper.get_select_options(main.data_list.PRODUCT_CATEGORIES, "id", "name"));
                },timeout: settings.ajax_timeouts.NORMAL
            });
        },
        initialize: function () {
            let self = this;

            function set_events(){
                $(self.id_list.MODAL).on("show.bs.modal", function () {
                    if(!$(self.id_list.TABLE).hasClass("dataTable")){
                        self.initialize.tree_table();
                    }
                });

                $(self.id_list.FORM).submit(function(e) {
                    e.preventDefault();
                    let function_value = parseInt($(`${self.id_list.FORM} button[type="submit"]:focus`).attr("value"));

                    function set(){
                        $(`${self.id_list.FORM} input[name=function]`).val(function_value);
                        self.set();
                    }

                    switch (function_value) {
                        case 1:
                            set();
                            break;
                        case 2:
                            Swal.fire({
                                icon: "question",
                                title: language.data.DELETE_CATEGORY,
                                html: `<b style="color: black">'${array_list.find(main.data_list.PRODUCT_CATEGORIES, parseInt($(`${self.id_list.FORM} input[name="id"]`).val()), "id").name}'</b> isimli kategoriyi silmek istediğinizden emin misiniz?`,
                                allowEscapeKey: false,
                                allowOutsideClick: false,
                                showCancelButton: true,
                                confirmButtonText: language.data.DELETE,
                                cancelButtonText: language.data.CANCEL,
                                confirmButtonClass: 'btn btn-success btn-lg mr-3 mt-5',
                                cancelButtonClass: 'btn btn-danger btn-lg ml-3 mt-5',
                                buttonsStyling: false
                            }).then((result) => {
                                if (result.value) {
                                    set();
                                }
                            });
                            break;
                    }
                });

                $(document).on('click', `${self.id_list.TABLE} tbody tr td:not(.tt-details-control)`, function () {
                    let data = array_list.find(main.data_list.PRODUCT_CATEGORIES, (self.tree_table.DataTable().row(this).data()).id, "id");
                    let element = $(`${self.id_list.TABLE} tbody`);

                    if(element.children(`#${data.id}`).hasClass("bg-info")){
                        $(self.id_list.FORM).trigger("reset");
                        element.children(`#${data.id}`).removeClass("bg-info");
                    }else{
                        $(self.id_list.FORM).autofill(data);
                        element.children("tr").removeClass("bg-info");
                        element.children(`#${data.id}`).addClass("bg-info");
                    }
                });
            }

            self.initialize.tree_table = function(){
                function get_tree_table_data(){
                    let data = Array();

                    main.data_list.PRODUCT_CATEGORIES.forEach(category => {
                        data.push({
                            "id": category.id,
                            "category_name": category.name,
                            "display_range": `${category.start_time}-${category.end_time}`,
                            "active": `
                                <div style="font-size: 1.2em; text-align: center; font-weight: bold;">${
                                    (`${(category.active_table) ? "<span class='text-success'><i class='mdi mdi-check'></i></span>" : "<span class='text-danger'><i class='mdi mdi-close'></i></span>"}`) +
                                    (`${(category.active_safe) ? "<span class='text-success'><i class='mdi mdi-check'></i></span>" : "<span class='text-danger'><i class='mdi mdi-close'></i></span>"}`) +
                                    (`${(category.active_take_away) ? "<span class='text-success'><i class='mdi mdi-check'></i></span>" : "<span class='text-danger'><i class='mdi mdi-close'></i></span>"}`) +
                                    (`${(category.active_come_take) ? "<span class='text-success'><i class='mdi mdi-check'></i></span>" : "<span class='text-danger'><i class='mdi mdi-close'></i></span>"}`)
                                }</div>
                            `,
                            "rank": category.rank,
                            "tt_key": category.id,
                            "tt_parent": ((array_list.find_multi(main.data_list.PRODUCT_CATEGORIES, category.main_id, "id") > 0) ? category.main_id : 0)
                        });
                    });

                    $('[data-toggle="tooltip"]').tooltip();

                    return data;
                }

                self.tree_table = $(self.id_list.TABLE).treeTable({
                    "data": get_tree_table_data(),
                    "scrollY": '50vh',
                    "scrollCollapse": true,
                    "paging": false,
                    "collapsed": true,
                    "order": [1, "asc"],
                    "columns": [
                        { data:"id", title: "Id" },
                        { data:"category_name", title: language.data.CATEGORY_NAME },
                        { data:"display_range",title: language.data.DISPLAY_RANGE },
                        { data:"rank", title: language.data.RANK },
                        { data:"active",title: language.data.ACTIVE }
                    ],
                    "language": {
                        "search":language.data.SEARCH_FAST
                    }
                });

                $(".dataTables_scrollHeadInner").css("width", "100%");
                $(".dataTable ").css("width","100%");
                $(self.id_list.MAIN_CATEGORIES_OPTION).html(helper.get_select_options(array_list.sort(main.data_list.PRODUCT_CATEGORIES, "name"), "id", "name"));
                self.element_list.SELECT_MAIN_CATEGORY.val(self.SELECTED_MAIN_CATEGORY).change();
            }

            set_events();
        },
    };

    let modal_product = {
        id_list: {
            FORM: "#form_set_product",
            MODAL: "#modal_set_product",
            IMAGE: "#product_edit_image",
            IMAGE_MODAL: "#modal_set_product_image",
            IMAGE_CROP_AREA: "#product_image_crop_area",
            INPUT_FILE_IMAGE: "#product_file_image",
            INPUT_HIDDEN_IMAGE: "#product_image"
        },
        class_list: {
            BUTTON_CROP: ".e_btn_crop",
            BUTTON_TOOLS: ".e_btn_tools",
            LINKED_OPTION_AREA: ".e_product_linked_option_area",
            LINKED_OPTION: ".e_linked_option"
        },
        name_list: {
            SELECT_OPTION: "option",
            SELECT_CATEGORY: "category_id",
            SELECT_QUANTITY: "quantity_id",
            ID: "id"
        },
        element_list: {
            get self() { return modal_product; },
            get SELECT_QUANTITY() { return helper.get_form_element_with_name(this.self.id_list.FORM, this.self.name_list.SELECT_QUANTITY, helper.element_types.SELECT) },
            get SELECT_CATEGORY() { return helper.get_form_element_with_name(this.self.id_list.FORM, this.self.name_list.SELECT_CATEGORY, helper.element_types.SELECT) },
            get INPUT_ID() { return helper.get_form_element_with_name(this.self.id_list.FORM, this.self.name_list.ID) },
        },
        option_edit_type: {
            GET: 0,
            ADD : 1,
            UPDATE : 2,
            DELETE : 3,
            LIKED_SET : 7,
        },
        set: function (){
            let self = this;
            helper_sweet_alert.wait(language.data.REGISTRATION, language.data.PRODUCT_SAVE_TEXT);
            let form_data = new FormData($(self.id_list.FORM)[0]);
            let image = $(self.id_list.IMAGE).attr("src");

            if(variable.is_base64(image)) {
                image = variable.base64_to_blob(image);
            }

            form_data.append("image", image);
            form_data.append("set_type", settings.set_types.INSERT);
            form_data.append("linked_options", settings.set_types.INSERT);
            form_data.append("options", JSON.stringify(self.save_options()));
            
            $.ajax({
                url: `${default_ajax_path}set.php`,
                type: "POST",
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                success: function (data) {
                    console.log(data);
                    data = JSON.parse(data);
                    console.log(data);
                    main.get_product_related_things(main.get_type_for_product_related_things.PRODUCT);
                    page.get();
                    helper_sweet_alert.success(language.data.SUCCESS, language.data.PRODUCT_ADD_MESSAGE);
                },error: helper_sweet_alert.close(), timeout: settings.ajax_timeouts.NORMAL
            });
        },
        clear_form: function (){
            let self = this;
            $(self.id_list.FORM)[0].reset();
            $(modal_product.id_list.IMAGE).attr("src", $(modal_product.id_list.IMAGE).attr("main-src"));
            $(self.class_list.LINKED_OPTION_AREA).html("");
        },
        set_form: function (data){
            let self = this;
            let linked_option_area = "";
            $(modal_product.id_list.IMAGE).attr("src", (server.is_valid_url(data.image)) ? data.image : settings.paths.image.PRODUCT(main.data_list.BRANCH_ID) + data.image);
            $(self.id_list.FORM).autofill(data);

           array_list.find_multi(main.data_list.PRODUCT_LINKED_OPTIONS,data.id,"product_id").forEach(function(e){
               linked_option_area += self.linked_option(e.option_id,self.option_edit_type.GET,e.id)
            })

            $(self.class_list.LINKED_OPTION_AREA).html(linked_option_area);

           let category = array_list.find(main.data_list.PRODUCT_CATEGORIES, data.category_id, "id");
           if(typeof category.name !== "undefined"){
                if(category.product_id == data.id){
                    $(`${self.id_list.FORM} input[name='default_category_image']`).prop("checked", 1).change();
                }
           }
        },
        save_options: function (){
            let self = this;
            let values = {};
                values.add = {};
                values.update = {};
                values.delete = {};
            let index = 0;
           Array.from($(`${self.class_list.LINKED_OPTION_AREA} tr${self.class_list.LINKED_OPTION}`)).forEach(function (element){
               element = $(element);
                let id = parseInt(element.attr("item-id"));
                let option_id = parseInt(element.attr("option-id"));
                let edit_type = parseInt(element.attr("edit-type"));
                let limit = parseInt(element.children("td").children("input.e_option_limit").val());

               switch (edit_type){
                   case self.option_edit_type.ADD:
                        values.add[index] = {id:id,option_id:option_id,limit:limit}
                       break;
                   case self.option_edit_type.UPDATE:
                       values.update[index] = {id:id,limit:limit}
                       break;
                   case self.option_edit_type.DELETE:
                       values.delete[index] = {id:id}
                       break;
               }
            index++;
           })
            console.log(values);
            return values;
        },
        linked_option: function (option_id,type,item_id=0){
            let self = this;
            let html = "";
            let limit;
            let option = array_list.find(main.data_list.PRODUCT_OPTIONS,option_id,"id");
            if (option !== "undefined"){
                if (item_id !== 0) limit = array_list.find(main.data_list.PRODUCT_LINKED_OPTIONS,item_id,"id").max_count

                switch (type){
                    case self.option_edit_type.GET: return create_option_element(option,type,item_id,limit);
                    case self.option_edit_type.ADD:
                    case self.option_edit_type.DELETE:
                    case self.option_edit_type.UPDATE:
                        html = create_option_element(option,type, item_id,option.selection_limit)

                }
            }

            $(self.class_list.LINKED_OPTION_AREA).append(html);

            function create_option_element(option,edit_type= self.option_edit_type.ADD, item_id = 0, limit = 0){
                return `<tr class="e_linked_option" edit-type="${edit_type}" option-id="${option_id}" item-id="${item_id}">
                       <td>${option.name}</td> 
                       <td class="m-0 p-1"><input class="form-input e_option_limit p-0 text-center"  style="font-size: 23px" type="number"  min="0" max="25" value="${limit}"></td> 
                       <td class="p-1 pt-2 text-center" function="del"><i class="fa fa-trash" style="font-size: 32px;"></i></td>
                </tr>`;
            }
        },
        initialize: function () {
            let self = this;

            function set_events(){
                $(self.id_list.FORM).on("submit", function(e) {
                    e.preventDefault(); // Cancel the submit
                    self.set();
                });

                $(self.id_list.MODAL).on("show.bs.modal", function () {
                    self.element_list.SELECT_QUANTITY.html(helper.get_select_options(main.data_list.PRODUCT_QUANTITY_TYPES, "id", "name"));
                    self.element_list.SELECT_CATEGORY.html(helper.get_select_options(main.data_list.PRODUCT_CATEGORIES, "id", "name"));
                });

                $(self.id_list.IMAGE).on("click", function() {
                    $(self.id_list.IMAGE_MODAL).modal("show");
                });

                $(self.class_list.BUTTON_TOOLS).on("click", function() {
                    let element = $(this);
                    let function_name = element.attr("function");

                    switch (function_name){
                        case "product":
                            self.clear_form();
                            break;
                    }
                });

                $("#product_linked_btn").on("click", function() {
                    modal_get_options.is_open(1);
                    $(modal_get_options.class_list.OPTION_TOOLS).hide();
                    $(`${modal_get_options.id_list.MODAL} .modal-title`).html(language.data.PRODUCT_ADD_OPTION_TITLE);
                    $(modal_get_options.id_list.MODAL).attr("linked","1");

                    $(document).on("click",`${modal_get_options.id_list.MODAL} ${modal_get_options.class_list.OPTION_LIST} tr`,function (){
                        let tr_element = $(this);
                        let name = tr_element.children("td:nth-child(2)").html();
                        let id = parseInt(tr_element.attr("option_id"));
                        if ($(modal_get_options.id_list.MODAL).attr("linked") === "1"){
                            Swal.fire({
                                icon: "question",
                                title: "Ürüne Opsiyon Eklensinmi ?",
                                html: `<b style="color: black">'${name}'</b> ${language.data.PRODUCT_ADD_OPTION_TEXT}`,
                                allowEscapeKey: false,
                                allowOutsideClick: false,
                                showCancelButton: true,
                                confirmButtonText: language.data.ADD,
                                cancelButtonText: "İptal",
                                confirmButtonClass: 'btn btn-success btn-lg mr-3 mt-5',
                                cancelButtonClass: 'btn btn-danger btn-lg ml-3 mt-5',
                                buttonsStyling: false
                            }).then((result) => {
                                if (result.value) {
                                    $(modal_get_options.id_list.MODAL).attr("linked","0");
                                    $(modal_get_options.is_open(0))
                                    self.linked_option(id,self.option_edit_type.ADD);
                                }
                            });
                        }

                    })
                });

                $(document).on("click",`${self.id_list.MODAL} ${self.class_list.LINKED_OPTION_AREA} td[function=del]`,function (){
                   let element = $(this);
                   let closest = element.closest("tr");
                   let edit_type = parseInt(closest.attr("edit-type"));

                    switch (edit_type){
                        case self.option_edit_type.ADD:
                                closest.remove();
                            break;
                        case self.option_edit_type.GET:
                        case self.option_edit_type.UPDATE:
                        case self.option_edit_type.DELETE:
                            closest.attr("edit-type",self.option_edit_type.DELETE).html("");
                            break;
                    }
                });

                $(document).on("change",`${self.id_list.MODAL} ${self.class_list.LINKED_OPTION_AREA} td input`,function (){
                    let element = $(this);
                    let closest = element.closest("tr");
                    let edit_type = parseInt(closest.attr("edit-type"));
                    if (edit_type === self.option_edit_type.GET){
                        $(this).closest("tr").attr("edit-type",self.option_edit_type.UPDATE)
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
                    self.class_list.BUTTON_CROP,
                    self.id_list.IMAGE,
                    "",
                    self.id_list.IMAGE_CROP_AREA
                );
            }

            set_events();
            initialize_crop();
            $(modal_product.id_list.IMAGE).attr("src", settings.paths.image.BRANCH_LOGO(main.data_list.BRANCH_ID)).attr("main-src", settings.paths.image.BRANCH_LOGO(main.data_list.BRANCH_ID));
        }
    };

    let modal_translate_product = {
        id_list: {
            MODAL: "#modal_translate_product",
            FORM: "#form_modal_translate_product"
        },
        class_list: {
            PRODUCTS: ".e_translate_products",
            PAGINATION: ".e_translate_product_pagination"
        },
        variable_list: {
            DATA: Array(),
            PER_COUNT: 10,
            INDEX: 1
        },
        get: function () {
            let self = this;

            function create_element() {
                let elements = {
                    table: "",
                    pagination: helper.get_pagination_elements(self.variable_list.DATA.length, self.variable_list.PER_COUNT, self.variable_list.INDEX)
                };

                let start = (self.variable_list.PER_COUNT * (self.variable_list.INDEX - 1));
                let end = start + self.variable_list.PER_COUNT;
                let languages = self.variable_list.DATA.slice(start, end);
                let index = 0;
                languages.forEach(language => {
                    elements.table += `
                        <tr>
                        <input type="hidden" name="products[${index}][id]" value="${language.id}">
                    `;

                    for (const [key, data] of Object.entries(helper.db.language_columns)) {
                        let key_name = `name_${data}`;
                        let key_comment = `comment_${data}`;
                        if(typeof language[key_name] !== "undefined" && typeof language[key_comment] !== "undefined"){
                            let name = language[key_name];
                            name = (name === null) ? "" : name;
                            let comment = language[key_comment];
                            comment = (comment === null) ? "" : comment;

                            elements.table += `
                                <td type="${data}">
                                    <input name="products[${index}][translates][${key_name}]" type="text" class="form-input" value="${name}" placeholder="İsim">
                                    <input name="products[${index}][translates][${key_comment}]" type="text" class="form-input" value="${comment}" placeholder="Açıklama">
                                </td>
                            `;
                        }
                    }

                    elements.table += `
                        </tr>
                    `;
                    index++;
                });

                return elements;
            }

            if(self.variable_list.DATA.length < 1){
                get(
                    get_types.TRANSLATE_PRODUCT,
                    {},
                    function (data) {
                        data = JSON.parse(data);
                        console.log(data);
                        if(data.status) self.variable_list.DATA = data.rows;
                    }
                );
            }

            let elements = create_element();
            $(self.class_list.PRODUCTS).html(elements.table);
            $(self.class_list.PAGINATION).html(elements.pagination);
        },
        initialize: function () {
            let self = this;

            function set_events(){
                $(self.id_list.MODAL).on("show.bs.modal", function () {
                   self.get();
                });

                $(document).on("click", `${self.class_list.PAGINATION} ul li a`, function () {
                    self.variable_list.INDEX = parseInt($(this).attr("index"));
                    self.get();
                });

                $(self.id_list.FORM).submit(function (e) {
                    e.preventDefault();
                    console.log($(this).serializeObject());
                    set(
                        set_types.TRANSLATE_PRODUCT,
                        $(this).serializeObject(),
                        function (data) {
                            data = JSON.parse(data);
                            console.log(data);
                            if(data.status){
                                self.variable_list.DATA = Array();
                                self.get();
                                helper_sweet_alert.success(language.data.PROCESS_SUCCESS_TITLE, language.data.SUCCESS_PRODUCT_LANG);
                            }
                        }
                    );
                });
            }

            set_events();
        }
    }

    let modal_translate_category = {
        id_list: {
            MODAL: "#modal_translate_category",
            FORM: "#form_modal_translate_category"
        },
        class_list: {
            PRODUCTS: ".e_translate_categories",
            PAGINATION: ".e_translate_category_pagination"
        },
        variable_list: {
            DATA: Array(),
            PER_COUNT: 10,
            INDEX: 1
        },
        get: function () {
            let self = this;

            function create_element() {
                let elements = {
                    table: "",
                    pagination: helper.get_pagination_elements(self.variable_list.DATA.length, self.variable_list.PER_COUNT, self.variable_list.INDEX)
                };

                let start = (self.variable_list.PER_COUNT * (self.variable_list.INDEX - 1));
                let end = start + self.variable_list.PER_COUNT;
                let languages = self.variable_list.DATA.slice(start, end);
                let index = 0;
                languages.forEach(language => {
                    elements.table += `
                        <tr>
                        <input type="hidden" name="categories[${index}][id]" value="${language.id}">
                    `;

                    for (const [key, data] of Object.entries(helper.db.language_columns)) {
                        let key_name = `name_${data}`;
                        if(typeof language[key_name] !== "undefined"){
                            let name = language[key_name];
                            name = (name === null) ? "" : name;

                            elements.table += `
                                <td type="${data}">
                                    <input name="categories[${index}][translates][${key_name}]" type="text" class="form-input" value="${name}" placeholder="İsim">
                                </td>
                            `;
                        }
                    }

                    elements.table += `
                        </tr>
                    `;
                    index++;
                });

                return elements;
            }

            if(self.variable_list.DATA.length < 1){
                get(
                    get_types.TRANSLATE_CATEGORY,
                    {},
                    function (data) {
                        data = JSON.parse(data);
                        console.log(data);
                        if(data.status) self.variable_list.DATA = data.rows;
                    }
                );
            }

            let elements = create_element();
            $(self.class_list.PRODUCTS).html(elements.table);
            $(self.class_list.PAGINATION).html(elements.pagination);
        },
        initialize: function () {
            let self = this;

            function set_events(){
                $(self.id_list.MODAL).on("show.bs.modal", function () {
                    self.get();
                });

                $(document).on("click", `${self.class_list.PAGINATION} ul li a`, function () {
                    self.variable_list.INDEX = parseInt($(this).attr("index"));
                    self.get();
                });

                $(self.id_list.FORM).submit(function (e) {
                    e.preventDefault();
                    console.log($(this).serializeObject());
                    set(
                        set_types.TRANSLATE_CATEGORY,
                        $(this).serializeObject(),
                        function (data) {
                            data = JSON.parse(data);
                            console.log(data);
                            if(data.status){
                                self.variable_list.DATA = Array();
                                self.get();
                                helper_sweet_alert.success(language.data.PROCESS_SUCCESS_TITLE, language.data.SUCCESS_PRODUCT_LANG);
                            }
                        }
                    );
                });
            }

            set_events();
        }
    }

    let modal_scales_products = {
        id_list : {
            MODAL_SCALES_PRODUCTS: "#modal_scales_products",
            SCALES_PRODUCTS_AREA: "#scales_products_area",
            SCALES_ADD_PRODUCTS_AREA: "#scales_add_products_area",
        },
        class_list : {
            GET_PRODUCTS_SCALES: ".e_get_products_scales",
            GET_ALL_PRODUCTS_SCALES: ".e_get_all_products_scales",
        },
        variable_list: {
            products:  {add: [], del: [], saved: [],data: []},
            buttons : {
                ADD: `<button class="btn btn-s1 w-100" function="add" type='button'>${language.data.ADD}</button>`,
                DEL: `<button class="btn btn-s3 w-100" function="del" type='button'>${language.data.DELETE}</button>`
            },
        },
        get_all_products: function (){
            let self = this;
            let value = "";
            main.data_list.PRODUCTS.forEach(function (product) {
                if(helper.db.quantity_types.PIECE === product.quantity_id || product.is_delete === 1 || typeof product.code !== "number" ) return;
                let btn = (self.variable_list.products.saved.indexOf(product.id) === -1) ? self.variable_list.buttons.ADD: self.variable_list.buttons.DEL
                value += helper.create_table_columns(
                    {
                        attr: {
                            "product-id":product.id,
                            "code": product.code
                        }
                    },
                    [{class:"p-1",html:btn},product.name,product.code.toString(),`${product.price_safe.toString()}₺`]
                )
            })
            $(self.class_list.GET_ALL_PRODUCTS_SCALES).html(value);
        },
        get_saved_products: async function (){
            let self = this;
            await app.integration_cla3000.get();
            main.data_list.BARCODE_PRODUCTS.forEach(function (i) {
                let p = array_list.find(main.data_list.PRODUCTS,parseInt(i.item_code),"code")
                self.variable_list.products.saved.push(p.id)
            })
        },
        create_saved_elements: function (){
            let self = this;

            let value = "";
            self.variable_list.products.saved.forEach(function (id) {
                let p = array_list.find(main.data_list.PRODUCTS,id,"id")
                console.log(p);
                let code = (main.data_list.BARCODE_PRODUCTS.length > 0 && typeof main.data_list.BARCODE_PRODUCTS[0].item_code === "string") ? `${p.code}` : p.code;
                let bp = array_list.find(main.data_list.BARCODE_PRODUCTS,code,"item_code")
                console.log(bp)
                let input_val = (typeof bp !== "undefined") ? bp.plu_no : 0;
                value += helper.create_table_columns(
                    {
                        attr: {
                            "product-id":p.id,
                            "code": p.code,
                            "name": p.name,
                            "price": p.price_safe
                        }
                    },
                    [{class:"p-1",html:`<input type='text' class="form-input w-100" name='plu_no' value='${input_val}'>`},p.name,`${p.code}`,`${p.price_safe}₺`]
                )
            })
            $(self.class_list.GET_PRODUCTS_SCALES).html(value);
        },
        initialize: function () {
            let self = this;
            function set_events(){
                $(document).on("click",`${self.id_list.MODAL_SCALES_PRODUCTS} button[function]`, function (){
                    let button = $(this)
                    let type = button.attr("function");
                    let element = $(this).closest("tr");
                    let product_id = parseInt(element.attr("product-id"));
                    let add_index = 0, del_index = 0;

                    console.log("TYPE: " + type)
                    switch (type){
                        case "add":
                                add_index = self.variable_list.products.add.indexOf(product_id)
                                del_index = self.variable_list.products.del.indexOf(product_id)
                                if (del_index > -1){
                                    self.variable_list.products.del.splice(add_index,1);
                                }else if (add_index === -1){
                                    self.variable_list.products.add.push(product_id)
                                }
                                button.replaceWith(self.variable_list.buttons.DEL)
                            break;
                        case "del":
                                add_index = self.variable_list.products.add.indexOf(product_id)
                                del_index = self.variable_list.products.del.indexOf(product_id)
                                console.log(add_index,del_index)
                               if (add_index > -1){
                                    self.variable_list.products.add.splice(add_index,1);
                               }else if (del_index === -1){
                                    self.variable_list.products.del.push(product_id)
                               }
                            button.replaceWith(self.variable_list.buttons.ADD)
                            break;
                        case "save":
                            self.variable_list.products.saved = helper.array_remove_duplicates(self.variable_list.products.saved.concat(self.variable_list.products.add))
                            self.variable_list.products.del.forEach(function (product){
                               let index = self.variable_list.products.saved.indexOf(product);
                               if (index !== -1) self.variable_list.products.saved.splice(index,1);
                            })
                            helper_sweet_alert.success(language.data.WAS_RECORDED);
                            self.variable_list.products.add = [];
                            self.variable_list.products.del = [];
                            console.log(self.variable_list.products.saved)
                            break;
                        case "save_all":
                            //{plu_no:0,item_code:0,name:"",price:0}
                            let data = [];
                           Array.from( $(`.e_get_products_scales tr`)).forEach(function (i) {
                               let tr = $(i)
                               let plu_no = tr.children("td").children("input").val()

                                data.push({
                                    plu_no:plu_no,
                                    item_code: tr.attr("code"),
                                    name: tr.attr("name"),
                                    price: tr.attr("price")
                                })
                           })
                            main.data_list.BARCODE_PRODUCTS = data;
                            app.integration_cla3000.set(data)
                            helper_sweet_alert.success(language.data.WAS_RECORDED);
                            //self.variable_list.products.saved = []
                            //self.get_saved_products();
                            break;
                    }
                    console.log(self.variable_list.products)
                });
                $(document).on("click","#scales_get_saved_btn",function (){
                    self.create_saved_elements();
                })
                $(document).on("show.bs.modal","#modal_scales_products",function (){
                    self.create_saved_elements();
                })
                $(document).on("click","#scales_get_all_saved_btn",function (){
                    self.get_all_products();
                })
            }


            self.get_all_products();
            self.get_saved_products();
            set_events();
            $(".e_scales_products_open_btn").show();
        }
    }

    let page = {
        id_list: {
            LIST: "#product_list",
            PRODUCT: "#product",
            SEARCH: "#search"
        },
        class_list: {
            PRODUCT_OPERATIONS: ".e_product_operations"
        },
        WANTED_PRODUCT: "",
        get: function () {
            let self = this;

            function create_element(){
                let element = ``;

                main.data_list.PRODUCTS.forEach(product => {
                    let category = array_list.find(main.data_list.PRODUCT_CATEGORIES,  product.category_id, "id");

                    if(typeof category === "undefined"){
                        category = {
                            id: 0,
                            name: `<font color="red"><lang>DELETED</lang></font>`
                        }
                    }
                    if(product.is_delete === 0) {
                        if(modal_select_category.SELECTED_CATEGORY > 0 && modal_select_category.SELECTED_CATEGORY !== category.id) return;
                        if(self.WANTED_PRODUCT.length > 0 && !String(product.name).match(new RegExp(self.WANTED_PRODUCT, "gi"))) return;
                        element += `
                            <div class="products text-black-50 shadow-sm bg-card" id="product" product-id="${product.id}">
                                <div class="img">
                                    <img draggable="false" src="${(server.is_valid_url(product.image)) ? product.image : settings.paths.image.PRODUCT(main.data_list.BRANCH_ID) + product.image}"  alt="${product.name}">
                                </div>
                            
                                <div class="details">
                                    <div class="column-xl text-body"><p>${product.name}</p></div>
                                    <div class="column-sm"><p><b><lang>${language.data.CATEGORY}</lang>:</b> ${(variable.isset(()=> category)) ? category.name : ""}</p></div>
                                    <div class="column-sm"><p><b><lang>${language.data.STOCK_CODE}</lang>:</b> ${product.code}</p></div>
                                </div>
                            
                                <div class="right-buttons">
                                    <div class="box-square wpx-125 text-black-50 text-center-y-100px">
                                        <span class="size-type-xxl">${product.price + main.data_list.CURRENCY}</span>
                                    </div>
                                    <div class="box-square wpx-75">
                                        <i class="e_product_operations fa fa-cogs text-c4" function="edit" data-toggle="modal" data-target="#modal_set_product"></i>
                                    </div>
                                    <div class="box-square wpx-75 btn-delete-product">
                                        <i class="e_product_operations fa fa-trash text-c3" function="delete"></i>
                                    </div>
                                    <div class="box-square wpx-75">
                                        <i class="e_product_operations ${(product.favorite) ? "fa" : "far" } fa-star text-c2" function="favorite"></i>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                });

                return element;
            }

            $(self.id_list.LIST).html(create_element());
        },
        set_favorite: function (id){
            helper_sweet_alert.wait(language.data.FAVORITE_TITLE, language.data.PRODUCT_ADD_FAVORITE_TEXT);
            $.ajax({
                url: `${default_ajax_path}set.php`,
                type: "POST",
                data: {id: id, set_type: settings.set_types.UPDATE},
                success: function (data) {
                    data = JSON.parse(data);
                    if(data.status){
                        main.data_list.PRODUCTS[array_list.index_of(main.data_list.PRODUCTS, id, "id")].favorite = !main.data_list.PRODUCTS[array_list.index_of(main.data_list.PRODUCTS, id, "id")].favorite;
                    }
                },error: helper_sweet_alert.close(), timeout: settings.ajax_timeouts.NORMAL
            });
        },
        delete: function (id) {
            helper_sweet_alert.wait(language.data.DELETE_PRODUCT, language.data.DELETE_PRODUCT_TEXT);
            $.ajax({
                url: `${default_ajax_path}set.php`,
                type: "POST",
                data: {id: id, set_type: settings.set_types.DELETE},
                success: function (data) {
                    data = JSON.parse(data);
                    if(data.status){
                        if(data.error_code === settings.error_codes.SUCCESS){
                            main.get_product_related_things(main.get_type_for_product_related_things.PRODUCT);
                            helper_sweet_alert.success(language.data.PROCESS_SUCCESS_TITLE, `<b>'${product.name}'</b> ${language.data.PRODUCT_DELETE_TEXT}`);
                        }
                    }
                },error: helper_sweet_alert.close(), timeout: settings.ajax_timeouts.NORMAL
            });
        },
        initialize: function () {
            let self = this;

            function set_events(){
                $(document).on("click", self.class_list.PRODUCT_OPERATIONS, function() {
                    let element = $(this);
                    let main_element = element.closest(self.id_list.PRODUCT);
                    let function_name = element.attr("function");
                    let id = parseInt(main_element.attr("product-id"));
                    let product = array_list.find(main.data_list.PRODUCTS, id, "id");

                    switch (function_name) {
                        case "edit":
                            modal_product.clear_form();
                            modal_product.set_form(product);
                            break;
                        case "delete":
                            Swal.fire({
                                icon: "question",
                                title: language.data.DELETE_PROCESS_TITLE,
                                html: `<b>'${product.name}'</b> ${language.data.DELETE_PRODUCT_QUESTION}`,
                                allowEscapeKey: false,
                                allowOutsideClick: false,
                                showCancelButton: true,
                                confirmButtonText: language.data.DELETE,
                                cancelButtonText: language.data.CANCEL,
                                confirmButtonClass: 'btn btn-success btn-lg mr-3 mt-5',
                                cancelButtonClass: 'btn btn-danger btn-lg ml-3 mt-5',
                                buttonsStyling: false
                            }).then((result) => {
                                if (result.value) {
                                    self.delete(id);
                                    main_element.remove();
                                }
                            });
                            break;
                        case "favorite":
                            self.set_favorite(id);
                            if(element.hasClass("fa")){
                                element.removeClass("fa").addClass("far");
                            }else{
                                element.removeClass("far").addClass("fa");
                            }
                            break;
                    }
                });

                $(self.id_list.SEARCH).on("keyup", function (){
                    let element = $(this);

                    self.WANTED_PRODUCT = element.val();
                    self.get();
                });
            }

            self.get();
            set_events();
        }
    };

    function set(set_type, data, success_function){
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

    return page_products;
})();

$(function () {
    let _products = new page_products();
});



