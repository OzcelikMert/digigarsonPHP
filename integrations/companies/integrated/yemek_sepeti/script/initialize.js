integrated_companies.yemek_sepeti = new ((function () {
    let default_ajax_path = settings.paths.primary.INTEGRATED_COMPANIES("yemek_sepeti");
    let get_types = {
        ORDERS: 0x0001,
        PRODUCTS: 0x0002,
        RESTAURANT_LIST: 0x0003
    };
    let set_types = {
        MESSAGE_SUCCESSFUL: 0x0001,
        UPDATE_ORDER: 0x0002
    }

    function set(set_type, data, success_function){
        data["set_type"] = set_type;
        $.ajax({
            url: `${default_ajax_path}set.php`,
            type: "POST",
            data: data,
            success: function (data) {
            //    console.log(data);
                success_function(data);
            },timeout: settings.ajax_timeouts.NORMAL
        });
    }

    function get(get_type, data, success_function, async = false){
        data["get_type"] = get_type;
        $.ajax({
            url: `${default_ajax_path}get.php`,
            type: "POST",
            data: data,
            async: async,
            success: function (data) {
              //  console.log(data);
                success_function(data);
            }, timeout: settings.ajax_timeouts.NORMAL
        });
    }

    return function () {
        let self = this;

        self.orders = {
            variable_list: {
                DATA: Array(),
                order_state: {
                    enums: {
                        ACCEPTED: "Accepted",
                        REJECTED: "Rejected",
                        CANCELLED: "Cancelled",
                        ON_DELIVERY: "OnDelivery",
                        DELIVERED: "Delivered"
                    },
                    DATA: [
                        {"id": "Accepted", "name": "Onaylandı"},
                        {"id": "Rejected", "name": "Reddedildi"},
                        {"id": "Cancelled", "name": "İptal Edildi"},
                        {"id": "OnDelivery", "name": "Sipariş Yolda"},
                        {"id": "Delivered", "name": "Sipariş Teslim Edildi"},
                    ]
                }
            },
            get: (success_function = (data) => {}, async = false) => {
                get(
                    get_types.ORDERS,
                    {},
                    function (data) {
                        data = JSON.parse(data);
                       // console.log(data);
                        if(typeof data.rows.order === "undefined") {
                            data.rows.order = Array();
                        }

                        if(typeof data.rows.order["@attributes"] !== "undefined") {
                            data.rows.order = [data.rows.order];
                        }

                        data.rows.order.forEach(products => {
                            if(typeof products.product["@attributes"] !== "undefined") {
                                products.product = [products.product];
                            }

                            products.product.forEach(options => {
                                if(typeof options.option === "undefined"){
                                    options.option = Array();
                                }
                                if(typeof options.option["@attributes"] !== "undefined") {
                                    options.option = [options.option];
                                }
                            })
                        });

                       // console.log(data);
                        success_function(data);
                    },
                    async
                );
            },
            set_message_successful: (message_id, success_function = (data) => {}) => {
                set(
                    set_types.MESSAGE_SUCCESSFUL,
                    {message_id: message_id},
                    function (data) {
                        data = JSON.parse(data);
                      //  console.log(data);
                        success_function(data);
                    }
                );
            },
            set_status: (order_id, order_state, reason = "", success_function = (data) => {}) => {
                set(
                    set_types.UPDATE_ORDER,
                    {
                        order_id: order_id,
                        order_state: order_state,
                        reason: reason
                    },
                    function (data) {
                        data = JSON.parse(data);
                        console.log(data);
                        success_function(data);
                    }
                );
            },
            __initialize: function () {
                function set_events(){

                }

                set_events();
            }
        }

        self.products = {
            variable_list: {
                DATA: Array()
            },
            get: (success_function = (data) => {}) => {
                get(
                    get_types.PRODUCTS,
                    {},
                    function (data) {
                        data = JSON.parse(data);
                    //    console.log(data);
                        success_function(data);
                    }
                );
            },
            __initialize: function () {
                function set_events(){

                }

                set_events();
            }
        }

        self.restaurant_list = {
            variable_list: {
                DATA: Array()
            },
            get: (data = {}, success_function = (data) => {}) => {
                get(
                    get_types.RESTAURANT_LIST,
                    data,
                    function (data) {
                        data = JSON.parse(data);
                     //   console.log(data);
                        success_function(data);
                    }
                );
            },
            __initialize: function () {
                function set_events(){

                }

                set_events();
            }
        }

        function initialize(){
            self.orders.__initialize();
            self.products.__initialize();
            self.restaurant_list.__initialize();
        }initialize();
    };
})());