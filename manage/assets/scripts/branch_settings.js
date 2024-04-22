let branch_settings = (function () {
    let default_ajax_path = `${settings.paths.primary.PHP}branch_settings/`;

    let set_types = {
        CHANGE_NAME: 1,
        WORKING_TIMES: 2,
        TAKEAWAY_ADDRESS: 4,
        PAYMENT_METHOD: 5,
        LONG_ADDRESS: 6,
        MIN_MONEY_AND_TIME: 7,
        QR_CODE_SECURITY: 8,
        SERVICE_NOTIFICATION: 9,
        SURVEYS: 10
    };
    let get_types = {
        GET_ADDRESS: 1,
        SURVEYS: 2,
    }

    let areas = {
        BRANCH_NAME: "branch_name",
        BRANCH_NAME_INPUT: "branch_name_input",
        LONG_ADDRESS: "long_address",
        LONG_ADDRESS_INPUT: "long_address_input",
        WORKING_TIMES: "working_times",
        MIN_TIME_AND_TOTAL: "min_time_and_total",
        MIN_TIME_INPUT: "min_time_input",
        MIN_TOTAL_INPUT: "min_total_input",
        QR_SECURITY: "qr_security", //checkbox
        PAYMENT_TYPES: "payment_types",
        ADDRESS_LIST: "address_list"
    }

    function branch_settings(){ initialize(); }
    function initialize(){
        //get_settings.initialize();
        payment_settings.initialize();
        address.initialize();
        fast_settings.initialize();
        notification_settings.initialize();
        //survey_settings.initialize();
    }

    let fast_settings = {
        id_list: {
            MODAL_CHANGE_NAME: "#modal_settings_change_name",
            MODAL_TAKEAWAY_MONEY_AND_TIME: "#modal_settings_takeaway_min_total_and_time",
            MODAL_WORKING_TIMES: "#modal_settings_edit_working_times",
            MODAL_LONG_ADDRESS: "#modal_settings_edit_long_address",
        },
        class_list: {},
        fill_areas: function (){
            let self = this;
            $(`[area=${areas.BRANCH_NAME}]`).html(main.data_list.BRANCH_INFO.name)
            $(`[area=${areas.LONG_ADDRESS}]`).html(main.data_list.BRANCH_INFO.address)
            $(`[area=${areas.MIN_TIME_AND_TOTAL}]`).html(main.data_list.BRANCH_INFO.take_away_time+ " dk  - "+main.data_list.BRANCH_INFO.take_away_amount)
            $(`[area=${areas.MIN_TIME_INPUT}]`).val(main.data_list.BRANCH_INFO.take_away_time)
            $(`[area=${areas.BRANCH_NAME_INPUT}]`).val(main.data_list.BRANCH_INFO.name)
            $(`[area=${areas.LONG_ADDRESS_INPUT}]`).val(main.data_list.BRANCH_INFO.address)
            $(`[area=${areas.MIN_TOTAL_INPUT}]`).val(main.data_list.BRANCH_INFO.take_away_amount)
            $(`[area=${areas.QR_SECURITY}]`).prop("checked",(main.data_list.BRANCH_INFO.ip_block === 1));

            //work times
            let days = ["Pazartesi","Salı","Çarşamba","Perşembe","Cuma","Cumartesi","Pazar"];
            let value = "";
            let index = 0;
            main.data_list.BRANCH_WORK_TIMES.forEach(function (item = {start_time:null,stop_time:null,active:null}){
                value += `<li>${days[index]}: ${item.start_time} - ${item.stop_time}</li>`;
                $(`${self.id_list.MODAL_WORKING_TIMES} form input[name="day[${index}][0]"]`).val(item.start_time)
                $(`${self.id_list.MODAL_WORKING_TIMES} form input[name="day[${index}][1]"]`).val(item.stop_time)
                $(`${self.id_list.MODAL_WORKING_TIMES} form select[name="day[${index}][2]"] option[value=${item.active}]`).attr("selected",true)
                index++;
            });
            $(`[area=${areas.WORKING_TIMES}]`).html(value)

            value = "";
            main.data_list.BRANCH_PAYMENT_TYPES.forEach(function (item = {type_id: 0}){
                let payment = array_list.find(main.data_list.PAYMENT_TYPES,item.type_id,"id");
                value += payment.name + ", ";
            })
            $(`[area=${areas.PAYMENT_TYPES}]`).html(value.slice(0,-2))
            value = "";

            main.data_list.ADDRESS.TAKEAWAY_NAMES.forEach(function (item = {neighborhood: null}){
                value += `<li>${item.neighborhood}</li>`;
                $(`[area=${areas.ADDRESS_LIST}]`).html(value)

            })
        },
        initialize(){
            let self = this;
            function set_events(){
                $(document).on("submit",`${self.id_list.MODAL_TAKEAWAY_MONEY_AND_TIME} form`,function (){
                    let data = $(`${self.id_list.MODAL_TAKEAWAY_MONEY_AND_TIME} form`).serializeObject();
                    set(set_types.MIN_MONEY_AND_TIME, data,function () {
                        main.get_branch_related_things(main.get_type_for_branch_related_things.BRANCH_INFO);
                        self.fill_areas();
                        $(self.id_list.MODAL_TAKEAWAY_MONEY_AND_TIME).modal("hide");
                    });
                    return false;
                })
                $(document).on("submit",`${self.id_list.MODAL_CHANGE_NAME} form`,function (){
                    let data = $(`${self.id_list.MODAL_CHANGE_NAME} form`).serializeObject();
                   //console.log(data);
                    data.set_type = set_types.CHANGE_NAME;
                    set(null, data,function () {
                        main.get_branch_related_things(main.get_type_for_branch_related_things.BRANCH_INFO);
                        self.fill_areas();
                        $(self.id_list.MODAL_CHANGE_NAME).modal("hide");
                    });
                    return false;
                })
                $(document).on("submit",`${self.id_list.MODAL_WORKING_TIMES} form`,function (){
                    let data = $(`${self.id_list.MODAL_WORKING_TIMES} form`).serializeObject();
                    set(set_types.WORKING_TIMES, data,function () {
                        main.get_branch_related_things(main.get_type_for_branch_related_things.WORK_TIMES);
                        self.fill_areas();
                        $(self.id_list.MODAL_WORKING_TIMES).modal("hide");
                    });
                    return false;
                })
                $(document).on("submit",`${self.id_list.MODAL_LONG_ADDRESS} form`,function (){
                    let data = $(`${self.id_list.MODAL_LONG_ADDRESS} form`).serializeObject();
                    set(set_types.LONG_ADDRESS, data,function () {
                        main.get_branch_related_things(main.get_type_for_branch_related_things.BRANCH_INFO);
                        self.fill_areas();
                        $(self.id_list.MODAL_LONG_ADDRESS).modal("hide");
                    });
                    return false;
                })

                $(document).on("change",`[area=${areas.QR_SECURITY}]`,function (){
                    let data = $(`[area=${areas.QR_SECURITY}]`).prop("checked")
                    set(set_types.QR_CODE_SECURITY, {check: data},function () {
                        main.get_branch_related_things(main.get_type_for_branch_related_things.BRANCH_INFO);
                        self.fill_areas();
                        $(self.id_list.QR_SECURITY).modal("hide");

                    });
                    return false;
                })

            }
            self.fill_areas();
            set_events();
        }
    }
    let address = {
        id_list: {
            MODAL: "#modal_takeaway_address",
        },
        type: {
            CITY : 1,
            TOWN : 2,
            DISTRICT : 3,
            NEIGHBORHOOD : 4,
            USER : 5,
            ALL_TYPE: 9
        },
        variable_list: {
            SELECT_TYPE: null,
            ADDRESS: { ADD: [], DEL: []}
        },
        create_elements: function (array,value_key,display_key){
            let value = "";
            if (!(display_key === "neighborhood")) value += `<option value="">${language.data.MAKE_CHOICE}</option>`;
            if (array === undefined || array === null || array.length === 0) return value;

            array.forEach(function (key){
                let checked = ((display_key === "neighborhood") && ( array_list.find(main.data_list.ADDRESS.TAKEAWAY,key[value_key],"neighborhood_id") !== undefined )) ? "checked" : "";

                value += (display_key === "neighborhood")
                    ? `<div class="col-4"><input class="checkbox-md" ${checked}  type="checkbox" name="neighborhood_checkbox" value="${key[value_key]}" id="address_checkbox_${key[value_key]}">
                    <label class="size-type-md" for="address_checkbox_${key[value_key]}">${key[display_key]}</label></div>`
                    : `<option value="${key[value_key]}">${key[display_key]}</option>`;
            });

            return value;
        },
        get_address_select: function (data,type){
            let self = this;
            data = (data.rows !== undefined ) ? data.rows : data;
            if (type === undefined) { type = self.variable_list.SELECT_TYPE}
            switch (type){
                case  self.type.CITY:
                    $("#page_edit_address [function=1]").html(self.create_elements(main.data_list.ADDRESS.city,"id","city")); break;
                case  self.type.TOWN:
                    $("#page_edit_address [function=2]").html(self.create_elements(data,"id","town"));break;
                case  self.type.DISTRICT:
                    $("#page_edit_address [function=3]").html(self.create_elements(data,"id","district"));break;
                case  self.type.NEIGHBORHOOD:
                    $("#page_edit_address [function=4]").html(self.create_elements(data,"id","neighborhood"));break;
                case  self.type.ALL_TYPE:
                    $("#page_edit_address [function=1]").html(self.create_elements(main.data_list.ADDRESS.city,"id","city"))
                    $("#page_edit_address [function=2]").html(self.create_elements(data,"id","town"))
                    $("#page_edit_address [function=3]").html(self.create_elements(data,"id","district"))
                    $("#page_edit_address [function=4]").html(self.create_elements(data,"id","neighborhood"))
                    break;
            }
        },
        initialize: function (){
            let self = this;
            function set_events(){

                $(document).on('shown.bs.modal',"#modal_takeaway_address",function (){
                    console.log("set hi");
                    $("#page_edit_address [function=1]").html(self.create_elements(main.data_list.ADDRESS.CITY,"id","city"))
                })
                $(document).on("change","#modal_takeaway_address select[function]",function (){
                    let element = $(this);
                    let type = parseInt(element.attr("function"));
                    let data = {};
                    data["id"] = element.val();
                    data["set_type"] = get_types.GET_ADDRESS;
                    data["next_type"] = 0;
                    switch (type){
                        case self.type.CITY:
                            data["type"] = self.type.CITY;
                            data["next_type"] = self.type.TOWN;
                            // $("#page_edit_address [function=3],#page_edit_address [function=4]").html(self.create_options())
                            break;
                        case  self.type.TOWN:
                            data["type"] = self.type.TOWN;
                            data["next_type"] = self.type.DISTRICT;
                            //$("#page_edit_address [function=4]").html(self.create_options())
                            break;
                        case  self.type.DISTRICT:
                            data["type"] = self.type.DISTRICT;
                            data["next_type"] = self.type.NEIGHBORHOOD;
                            // $("#page_edit_address [function=5]").html(self.create_options())
                            break;
                    }
                    if (data["next_type"] > 0){
                        self.variable_list.SELECT_TYPE = data.next_type;
                        get(get_types.GET_ADDRESS,data,function (data){self.get_address_select(data)});
                    }
                })
                // send form data //
                $(document).on("submit","#modal_takeaway_address form",function (){

                    set(set_types.TAKEAWAY_ADDRESS,self.variable_list.ADDRESS,function (data){
                        main.get_branch_related_things(main.get_type_for_branch_related_things.TAKEAWAY_ADDRESS);
                        fast_settings.fill_areas();
                        $(self.id_list.MODAL).modal("hide");
                    });
                    return false;
                })
                $(document).on("change",'#modal_takeaway_address [function=4] input[type="checkbox"]', function() {
                    let isChecked = $(this).is(':checked');
                    let value = parseInt(this.value)
                    if(isChecked){
                        (self.variable_list.ADDRESS.DEL.indexOf(value) === -1)
                            ? self.variable_list.ADDRESS.ADD.push(value)
                            : array_list.del_index_of(self.variable_list.ADDRESS.DEL,value);
                    }else{
                        (self.variable_list.ADDRESS.ADD.indexOf(this.value) > -1)
                            ?  array_list.del_index_of(self.variable_list.ADDRESS.ADD,value)
                            :  self.variable_list.ADDRESS.DEL.push(value);
                    }
                    //console.log(self.variable_list.ADDRESS)
                });
            }
            set_events();
        }
    };
    let payment_settings = {
        id_list: {
            MODAL: "#modal_payment_settings"
        },
        class_list: {
            PAYMENT_LIST: ".e_payments_list",
        },
        attr_list: {
            OPERATION: "operation",
            FUNCTION: "function",
            TYPE_ID: "type-id",
        },
        functions: {
            ADD: 1, DEL: 2
        },
        variable_list: {
            PAYMENT: {BRANCH:[1,2],ADD: [], DEL: []},
            TAKEAWAY: {BRANCH:[],ADD: [], DEL: []},
        },
        buttons : {ADD:"",DEL:""},
        get_active_payments: function (){
            let self = this;
            let active = "";
            let deactivate = "";

            main.data_list.BRANCH_PAYMENT_TYPES.forEach(function (item = { active: 0,active_take_away: 0, "type_id": undefined }){
                let payment_type = array_list.find(main.data_list.PAYMENT_TYPES,item.type_id,"id");
                if (item.active === 1) self.variable_list.PAYMENT.BRANCH.push(item.type_id);
                let checked = (item.active_take_away) ? "checked" : "";

                active += helper.create_table_columns(
                    {attr: {"type-id": item.type_id}},
                    [self.buttons.DEL, payment_type.name,`<input type="checkbox" ${checked} class="checkbox-md">`]
                );
            })
            main.data_list.PAYMENT_TYPES.forEach(function (item){
                if (!self.variable_list.PAYMENT.BRANCH.includes(item.id)){
                    deactivate += helper.create_table_columns({attr: {"type-id": item.id}}, [self.buttons.ADD, `${item.name}`]);
                }
            })
            $(self.class_list.PAYMENT_LIST).html(active + deactivate);
        },
        initialize: function (){
            let self = this;
            function set_events(){
                $(document).on("click",`${self.id_list.MODAL} button[${self.attr_list.OPERATION}]`,function (){
                    let element = $(this);
                    let type = parseInt(element.attr(self.attr_list.OPERATION));
                    let id = parseInt(element.closest("tr").attr(self.attr_list.TYPE_ID))
                    switch (type){
                        case self.functions.ADD:
                            element.replaceWith(self.buttons.DEL);
                            self.variable_list.PAYMENT.ADD.push(id);
                            if (self.variable_list.PAYMENT.DEL.indexOf(id) > -1) {
                                self.variable_list.PAYMENT.DEL.splice(self.variable_list.PAYMENT.DEL.indexOf(id), 1)
                            }
                            break;
                        case self.functions.DEL:
                            element.replaceWith(self.buttons.ADD);
                            self.variable_list.PAYMENT.DEL.push(id);
                            if (self.variable_list.PAYMENT.ADD.indexOf(id) > -1) {
                                self.variable_list.PAYMENT.ADD.splice(self.variable_list.PAYMENT.ADD.indexOf(id), 1)
                            }
                            break;
                    }
                });
                $(document).on("change",`${self.id_list.MODAL} input[type=checkbox]`,function (){
                    let element = $(this);
                    let type = $(this).is(':checked');
                    let id = parseInt(element.closest("tr").attr(self.attr_list.TYPE_ID))
                    switch (type){
                        case true:
                            self.variable_list.TAKEAWAY.ADD.push(id);
                            if (self.variable_list.TAKEAWAY.DEL.indexOf(id) > -1) {
                                self.variable_list.TAKEAWAY.DEL.splice(self.variable_list.TAKEAWAY.DEL.indexOf(id), 1)
                            }
                            break;
                        case false:
                            self.variable_list.TAKEAWAY.DEL.push(id);
                            if (self.variable_list.TAKEAWAY.ADD.indexOf(id) > -1) {
                                self.variable_list.TAKEAWAY.ADD.splice(self.variable_list.TAKEAWAY.ADD.indexOf(id), 1)
                            }
                            break;
                    }
                    //console.log(self.variable_list.TAKEAWAY)
                });
                $(document).on("submit",`${self.id_list.MODAL}`,function (){
                    set(set_types.PAYMENT_METHOD, {payment: self.variable_list.PAYMENT, takeaway: self.variable_list.TAKEAWAY})
                    return false;
                });
            }
            self.buttons.ADD = `<button type="button" class="btn btn-s1 w-100" operation="${self.functions.ADD}">${language.data.ADD}</button>`;
            self.buttons.DEL = `<button type="button" class="btn btn-s3 w-100" operation="${self.functions.DEL}">${language.data.DELETE}</button>`;
            set_events();
            self.get_active_payments();
        },
    }
    let notification_settings = {
        id_list: {
            MODAL: "#modal_notifications",
        },
        class_list: {
            FORM: ".e_branch_notifications",
            TABLE: ".e_service_table",
            EDIT_BTN: ".e_edit_btn",
            CANCEL_BTN: ".e_cancel_btn",
        },
        functions: { GET:1, EDIT: 2, DEL: 3, CANCEL: 4 },
        get: function(){
            let self = this;
            let element = "";
            let btn = {
                EDIT : `<button type="button" class="btn btn-s2 w-100" function="${self.functions.EDIT}">${language.data.EDIT}</button>`,
                DEL : `<button type="button" class="btn btn-s3 w-100" function="${self.functions.DEL}">${language.data.DELETE}</button>`
            };

            set(set_types.SERVICE_NOTIFICATION,{type: self.functions.GET},function (data = []) {
                //console.log(data);
                data.rows.forEach(function (item) {
                    element += helper.create_table_columns({attr: {"service-id": item.id}}, [btn.EDIT,btn.DEL,item.name]);
                })
                $(self.class_list.TABLE).html(element);
            },false)
        },
        form: function (button_txt,id,name,cancel_btn= false, focus = true){
            let self = this;
            let btn_element = $(`${self.class_list.FORM} ${self.class_list.CANCEL_BTN}`);
            let name_element = $(`${self.class_list.FORM} input[name=name]`);
            $(`${self.class_list.EDIT_BTN}`).html(button_txt)
            $(`${self.class_list.FORM} input[name=id]`).val(id);
            name_element.val(name);
            (cancel_btn) ? btn_element.show() : btn_element.hide()
            if (focus) name_element.focus();
        },
        initialize: function (){
            let self = this;
            function set_events(){
                $(document).on("submit",`${self.id_list.MODAL}`,function (){
                    let data = $(self.class_list.FORM).serializeObject();
                    data.type = self.functions.EDIT;
                    set(set_types.SERVICE_NOTIFICATION, data, function (data) {
                        //console.log(data);
                        if (data.status){
                            self.get();
                            self.form(language.data.ADD, 0, "", false)
                        }
                    });
                    return false;
                });
                $(document).on("click",`${self.id_list.MODAL} button[function]`,function (){
                    let element = $(this);
                    let type = parseInt(element.attr("function"));
                    let id = parseInt(element.closest("tr").attr("service-id"))

                    switch (type){
                        case self.functions.EDIT:
                            self.form(language.data.EDIT, id, element.closest("tr").find("td:last").html(), true)
                            break;
                        case self.functions.DEL:
                            set(set_types.SERVICE_NOTIFICATION, {type: self.functions.DEL,id: id}, function (data) {
                                if (data.status) element.closest("tr").remove();
                            });
                            break;
                        case self.functions.CANCEL:
                            self.form(language.data.ADD, 0, "", false)
                            break;
                    }
                });
            }
            set_events();
            self.get();
        },
    }
    let survey_settings = {
        id_list: {
            MODAL: "#modal_surveys",
        },
        class_list: {
            FORM: ".e_surveys",
            TABLE: ".e_survey_table",
            EDIT_BTN: ".e_edit_btn",
            CANCEL_BTN: ".e_cancel_btn",
        },
        get_type: { BRANCH: 1 },
        variable_list: {
            type: 1,
        },
        functions: { GET:1, EDIT: 2, DEL: 3, CANCEL: 4 },
        get: function(){
            let self = this;
            let element = "";
            let select_element = "";
            let btn = {
                EDIT : `<button type="button" class="btn btn-s2 w-100" function="${self.functions.EDIT}">${language.data.EDIT}</button>`,
                DEL : `<button type="button" class="btn btn-s3 w-100" function="${self.functions.DEL}">${language.data.DELETE}</button>`
            };
            get(get_types.SURVEYS,{type: self.get_type.BRANCH},function (data = []) {
                data.rows.forEach(function (item) {
                    let type = array_list.find(main.data_list.SURVEY_TYPES,parseInt(item.type),"id");
                    element += helper.create_table_columns({attr: {"survey-id": item.id}}, [btn.EDIT,btn.DEL,item.name,type.name]);
                })
                $(self.class_list.TABLE).html(element);
            },false)

            main.data_list.SURVEY_TYPES.forEach(function (item) {
                select_element += `<option value="${item.id}">${item.name}</option>`;
            })
            $(`${self.class_list.FORM} select`).html(select_element);
        },
        form: function (button_txt,id,name,cancel_btn= false, focus = true){
            let self = this;
            let btn_element = $(`${self.class_list.FORM} ${self.class_list.CANCEL_BTN}`);
            let name_element = $(`${self.class_list.FORM} input[name=name]`);
            $(`${self.class_list.EDIT_BTN}`).html(button_txt)
            $(`${self.class_list.FORM} input[name=id]`).val(id);
            name_element.val(name);
            (cancel_btn) ? btn_element.show() : btn_element.hide()
            if (focus) name_element.focus();
        },
        initialize: function (){
            let self = this;
            function set_events(){
                $(document).on("submit",`${self.id_list.MODAL}`,function (){
                    let data = $(self.class_list.FORM).serializeObject();
                    data.type = 2;
                    data.survey_type = parseInt( $(`${self.class_list.FORM} select[name='type']`).val());
                    set(set_types.SURVEYS, data, function (data) {
                        if (data.status){
                            self.get();
                            self.form(language.data.ADD, 0, "", false)
                        }
                    });
                    return false;
                });
                $(document).on("click",`${self.id_list.MODAL} button[function]`,function (){
                    let element = $(this);
                    let type = parseInt(element.attr("function"));
                    let id = parseInt(element.closest("tr").attr("survey-id"))
                    switch (type){
                        case self.functions.EDIT:
                            self.form(language.data.EDIT, id, element.closest("tr").find("td:nth-child(3)").html(), true)
                            break;
                        case self.functions.DEL:
                            set(set_types.SURVEYS, {type: self.functions.DEL,id: id}, function (data) {
                                if (data.status) element.closest("tr").remove();
                            });
                            break;
                        case self.functions.CANCEL:
                            self.form(language.data.ADD, 0, "", false)
                            break;
                    }
                });
            }
            set_events();
            self.get();
        },
    }

    /*=  GLOBAL FUNCTIONS =*/
    function set (set_type, data, success_function = null, sweet_alert = true){
        if (sweet_alert) helper_sweet_alert.wait(language.data.PROCESS_PROGRESS_TITLE, language.data.PROCESS_WAIT_CONTENT);
        if (set_type !== null)  data["set_type"] = set_type;
        $.ajax({
            url: `${default_ajax_path}set.php`,
            type: "POST",
            data: data,
            success: function (data) {
                //console.log(data);
                data = JSON.parse(data);
                console.log(data);
                if (success_function !== null){
                    if (data.status) {
                        if (sweet_alert) helper_sweet_alert.success(language.data.PROCESS_SUCCESS_TITLE);
                    }
                    success_function(data);
                }
            },error: () =>{ helper_sweet_alert.close() } , timeout: settings.ajax_timeouts.NORMAL
        });
    }
    function get(get_type, data, success_function = null){
        data["get_type"] = get_type;
        console.log(data);
        $.ajax({
            url: `${default_ajax_path}get.php`,
            type: "POST",
            data: data,
            success: function (data) {
                data = JSON.parse(data);
                console.log(data);
                if (success_function !== null){
                    success_function(data);
                }
            }, timeout: settings.ajax_timeouts.NORMAL
        });
    }
    return branch_settings;
})();

$(function () {
    let _branch_settings = new branch_settings();
});

