let _page_address;
let caller_id_;
$(function () {
     _page_address = new page_address();
     caller_id_ = new caller_id();
})


let caller_id = (function () {
    let default_ajax_path = `${settings.paths.primary.PHP_SAME_PARTS}caller_id/`;
    let set_types = {
        CANCEL: 0x0001,
        CUSTOMER: 0x0002
    };
    let get_types = {
        LAST_CALLER: 0x0001
    };

    caller_id.order_types = {
        TAKE_AWAY: 1,
        COME_TAKE: 2
    };
    caller_id.variable_list = {
        CALLER_ID: 0,
        CUSTOMER_ID: 0,
        DATA: Array(),
        SELECTED_ADDRESS_ID: 0,
        SELECTED_ORDER_TYPE: 0,
        CHECK: true,
        SHOW_NEW_CUSTOMER: false,
        SHOW_NEW_ORDER: false
    }

    let timer_check = {
        _timer: null,
        _function: function () {
            check();
        },
        _delay: 2500
    }
    function caller_id(){ initialize(); }
    function initialize() {
        caller_id.modal_new_customer.initialize();
        caller_id.modal_new_order.initialize();
        check_url_get_methods();
        if(main.data_list.CALLER_ID_ACTIVE === true){
            timer_check._timer = setInterval(timer_check._function, timer_check._delay);
        }
    }

    caller_id._check = function (phone) {
        check(0, phone);
    }

    function check_url_get_methods(){
        let get_methods = window.location.href.split("#")[1];
        if(typeof get_methods !== "undefined"){
            try{
                let data = JSON.parse(decodeURI(get_methods));
                console.log(data);
                caller_id.variable_list.CALLER_ID = parseInt(data.caller_id);
                caller_id.variable_list.SELECTED_ADDRESS_ID = parseInt(data.address_id);
                caller_id.variable_list.SELECTED_ORDER_TYPE = parseInt(data.order_type);
                check(caller_id.variable_list.CALLER_ID);
                orders.check_caller();
            }catch (e) {
                console.log(e);
            }
            window.history.replaceState(null, null, window.location.pathname);
        }
    }
    function check(caller_id_ = 0, phone = ""){
        console.log("CALLER ID: " + caller_id_ + " PHONE: " + phone)
        if(
            !caller_id.variable_list.SHOW_NEW_CUSTOMER &&
            !caller_id.variable_list.SHOW_NEW_ORDER &&
            caller_id.variable_list.CHECK &&
            (
                caller_id.variable_list.CALLER_ID === 0 ||
                caller_id_ !== 0
            )
        ){
            get(
                get_types.LAST_CALLER,
                {
                    caller_id: caller_id_,
                    phone: phone
                },
                function (data) {
                    data = JSON.parse(data);
                    console.log(data);
                    caller_id.variable_list.DATA = data.rows;
                    if(caller_id_ === 0){
                        data.rows.user.forEach(user => {
                            if(user.customer_id == null){
                                caller_id.modal_new_customer.get();
                            } else{
                                caller_id.modal_new_order.get();
                            }
                        })
                    }
                }
            );
        }
    }

    function caller_cancel(){
        set(
            set_types.CANCEL,
            {caller_id: parseInt(caller_id.variable_list.DATA.user[0].id)},
            function (data) {
                console.log(data);
                data = JSON.parse(data);
                console.log(data);
                if(caller_id.variable_list.SHOW_NEW_CUSTOMER) $(caller_id.modal_new_customer.id_list.MODAL).modal("hide");
                if(caller_id.variable_list.SHOW_NEW_ORDER) $(caller_id.modal_new_order.id_list.MODAL).modal("hide");
            }
        );
    }

    caller_id.modal_new_customer = {
        id_list: {
            MODAL: "#modal_new_customer",
            FORM: "#modal_new_customer_form",
        },
        class_list: {},
        get: function (fill = false) {
            let self = this;

            let input_name = $(`${self.id_list.FORM} input[name="name"]`);
            let input_phone = $(`${self.id_list.FORM} input[name="phone"]`);

            $(`${self.id_list.FORM}`).trigger("reset");
            input_name.prop("readonly", false);
            input_phone.prop("readonly", false);

            if(typeof caller_id.variable_list.DATA.user !== "undefined" && caller_id.variable_list.DATA.user.length > 0){
                input_name.val(caller_id.variable_list.DATA.user[0].name).prop("readonly", fill);
                input_phone.val(caller_id.variable_list.DATA.user[0].phone).prop("readonly", fill);
            }

            $(self.id_list.MODAL).modal("show");
        },
        initialize: function () {
            let self = this;

            function set_events(){
                $(self.id_list.MODAL).on("hide.bs.modal", function () {
                    caller_id.variable_list.SHOW_NEW_CUSTOMER = false;
                });

                $(self.id_list.MODAL).on("show.bs.modal", function () {
                    caller_id.variable_list.SHOW_NEW_CUSTOMER = true;
                });

                $(self.id_list.FORM).submit(function (e) {
                    e.preventDefault();
                    let form_data = $(this).serializeObject();
                    set(
                        set_types.CUSTOMER,
                        form_data,
                        function (data) {
                            console.log(data);
                            data = JSON.parse(data);
                            console.log(data);
                            if(data.error_code == settings.error_codes.SUCCESS) {
                                $(self.id_list.MODAL).modal("hide");
                                caller_id._check(form_data.phone);
                            }else {
                                helper_sweet_alert.error(language.data.ERROR, language.data.REQURIED_MESSAGE);
                            }
                        }
                    );
                });
            }

            set_events();
        }
    };
    caller_id.modal_new_order = {
        id_list: {
            MODAL: "#modal_new_order",
            FORM: "#modal_new_order_form",
        },
        class_list: {
            CUSTOMER_ADDRESS: ".e_modal_new_order_customer_address",
            ORDER_TYPE_BUTTONS: ".e_modal_new_order_type_buttons",
            CANCEL: ".e_caller_cancel",
            NEW_ADDRESS: ".e_new_address_for_customer"
        },
        get: function () {
            let self = this;

            function create_element(){
                let elements = ``;

                let checked = "checked";
                caller_id.variable_list.DATA.address.forEach(address => {
                    elements += `
                        <div class="col-12">
                            <div class="custom-control custom-radio">
                                <input type="radio" name="address" id="customer_address_${address.id}" class="custom-control-input" value="${address.id}" ${checked}>
                                <label class="custom-control-label" for="customer_address_${address.id}">${address.title}</label>
                            </div>
                        </div>
                    `;
                    checked = "";
                });

                return elements;
            }

            caller_id.variable_list.SELECTED_ORDER_TYPE = caller_id.order_types.TAKE_AWAY;
            $(`${self.id_list.FORM} input[name="name"]`).val(caller_id.variable_list.DATA.user[0].name);
            $(`${self.id_list.FORM} input[name="phone"]`).val(caller_id.variable_list.DATA.user[0].phone);
            $(`${self.id_list.FORM} ${self.class_list.CUSTOMER_ADDRESS}`).html(create_element());
            $(self.id_list.MODAL).modal("show");
        },
        initialize: function () {
            let self = this;

            function set_events(){
                $(self.id_list.MODAL).on("hide.bs.modal", function () {
                    caller_id.variable_list.SHOW_NEW_ORDER = false;
                });

                $(self.id_list.MODAL).on("show.bs.modal", function () {
                    caller_id.variable_list.SHOW_NEW_ORDER = true;
                });

                $(`${self.id_list.MODAL} ${self.class_list.ORDER_TYPE_BUTTONS} button`).on("click", function () {
                    let function_name = $(this).attr("function");

                    let order_type_title = $(`${self.id_list.FORM} [function="order_type_title"]`);
                    switch (function_name) {
                        case "take_away":
                            order_type_title.html("Paket Servisi");
                            $(`${self.id_list.FORM} [order-type="take_away"]`).show();
                            caller_id.variable_list.SELECTED_ORDER_TYPE = caller_id.order_types.TAKE_AWAY;
                            break;
                        case "come_take":
                            order_type_title.html("Gel Al");
                            $(`${self.id_list.FORM} [order-type="take_away"]`).hide();
                            caller_id.variable_list.SELECTED_ORDER_TYPE = caller_id.order_types.COME_TAKE;
                            break;
                    }
                });

                $(self.id_list.FORM).submit(function (e) {
                    e.preventDefault();
                    let data = $(this).serializeObject();
                    caller_id.variable_list.SELECTED_ADDRESS_ID = parseInt(data.address);
                    caller_id.variable_list.CALLER_ID = parseInt(caller_id.variable_list.DATA.user[0].id);
                    caller_id.variable_list.CUSTOMER_ID = parseInt(caller_id.variable_list.DATA.user[0].customer_id);
                    if(server.get_page_name() === "orders"){
                        orders.check_caller();
                        $(self.id_list.MODAL).modal("hide");
                    }else{
                        window.location.href = `orders.php#{"caller_id": ${caller_id.variable_list.CALLER_ID}, "address_id": ${caller_id.variable_list.SELECTED_ADDRESS_ID}, "order_type": ${caller_id.variable_list.SELECTED_ORDER_TYPE}}`;
                    }
                });

                $(self.class_list.CANCEL).on("click", function () {
                   caller_cancel();
                });

                $(self.class_list.NEW_ADDRESS).on("click", function () {
                    $(self.id_list.MODAL).modal("hide");
                    caller_id.modal_new_customer.get(true);
                });
            }

            set_events();
        }
    };

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

    return caller_id;
})();


let page_address = (function() {
    let default_ajax_path = `${settings.paths.primary.PHP_SAME_PARTS}caller_id/`;

    page_address.prototype.get_user_address =  function (address_id){
        let udata = null;
        address.set({get_type:address.set_type.GET_ADDRESS,next_type:address.type.USER,address_id:address_id},function (data) {
            udata = data;
        },false)
        return udata;
    }

    function page_address(){
        address.initialize();
    }


    let address = {
        id_list: {
            MODAL: "#modal_new_customer",
        },
        set_type: {
            GET_ADDRESS: 3,
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
            SELECT_TYPE: null
        },
        set: function(form_data, success_function = function (){},sync = true){
            //let self = this;
            $.ajax({
                url: `${default_ajax_path}get.php`,
                type: "POST",
                data: form_data,
                async: sync,
                success: function (data) {
                    data = JSON.parse(data);
                    //console.log(data);
                    success_function(data);
                },error: helper_sweet_alert.close(), timeout: settings.ajax_timeouts.NORMAL
            });
        },
        create_options: function (array,value_key,display_key){
            let html = `<option value="">Seçim Yapınız</option>`;
            if (array === undefined || array === null || array.length === 0) return html;
            array.forEach(function (e){html += `<option value="${e[value_key]}">${e[display_key]}</option>`;});
            return html;
        },
        get_address_select: function (data,type){
            let self = this;
            data = (data.rows !== undefined ) ? data.rows : data;
            if (type === undefined) { type = self.variable_list.SELECT_TYPE}
            let address_data = [];
            switch (type){
                case self.type.CITY:
                    address_data = self.create_options(main.data_list.ADDRESS.CITY,"id","city")
                    break;
                case self.type.TOWN:
                    address_data = self.create_options(data,"id","town")
                    break;
                case  self.type.DISTRICT:
                    address_data = self.create_options(data,"id","district")
                    break;
                case  self.type.NEIGHBORHOOD:
                    address_data = self.create_options(data,"id","neighborhood")
                    break;
                case self.type.ALL_TYPE:
                    $(`${self.id_list.MODAL} [function=1]`).html(self.create_options(main.data_list.ADDRESS.CITY,"id","city"))
                    $(`${self.id_list.MODAL} [function=2]`).html(self.create_options(data,"id","town"))
                    $(`${self.id_list.MODAL} [function=3]`).html(self.create_options(data,"id","district"))
                    $(`${self.id_list.MODAL} [function=4]`).html(self.create_options(data,"id","neighborhood"))
                    break;
            }
            if ([1,2,3,4].includes(type)) $(`${self.id_list.MODAL} [function=${type}]`).html(address_data);

        },
        get_city: function (){
            let self = this;
            let data = {get_type: self.set_type.GET_ADDRESS, next_type: self.type.CITY}
            self.set(data, function (value){
                if(variable.isset(()=> value.rows)) main.data_list.ADDRESS.CITY = value.rows
                $(`${self.id_list.MODAL} [function=1]`).html(self.create_options(main.data_list.ADDRESS.CITY,"id","city"))
            });
        },
        operation: function(type,address_id){
            let self = this;
            let data = {}
            switch (type){
                case "edit":
                    data = {
                        set_type: self.set_type.GET_ADDRESS,
                        next_type: self.type.USER,
                        address_id: address_id
                    }
                    self.set(data,function(value){auto_fill(value)});
                    break;
            }
            function auto_fill(data){
                self.get_address_select(data.custom_data.select.town,self.type.TOWN);
                self.get_address_select(data.custom_data.select.district,self.type.DISTRICT);
                self.get_address_select(data.custom_data.select.neighborhood,self.type.NEIGHBORHOOD);
                $("#address_form").autofill(data.rows[0]);
            }
        },
        initialize: function (){
            let self = this;
            function set_events(){
                $(document).on("change","#modal_new_customer [function]",function (){
                    let element = $(this);
                    let type = parseInt(element.attr("function"));
                    let data = {};
                    data["id"] = element.val();
                    data["get_type"] = self.set_type.GET_ADDRESS;
                    data["next_type"] = 0;
                    switch (type){
                        case self.type.CITY:
                            data["type"] = self.type.CITY;
                            data["next_type"] = self.type.TOWN;
                            $("#modal_new_customer [function=3],#page_edit_address [function=4]").html(self.create_options())
                            break;
                        case  self.type.TOWN:
                            data["type"] = self.type.TOWN;
                            data["next_type"] = self.type.DISTRICT;
                            $("#modal_new_customer [function=4]").html(self.create_options())
                            break;
                        case  self.type.DISTRICT:
                            data["type"] = self.type.DISTRICT;
                            data["next_type"] = self.type.NEIGHBORHOOD;
                            break;
                    }
                    if (data["next_type"] > 0){
                        self.variable_list.SELECT_TYPE = data.next_type;
                        self.set(data,function (data){self.get_address_select(data)});
                    }
                })
            }
            set_events();
            self.get_city();
        }
    };

    return page_address;
})();