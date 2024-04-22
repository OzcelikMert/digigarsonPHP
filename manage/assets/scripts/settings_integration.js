let settings_integration = (function () {
    let default_ajax_path = `${settings.paths.primary.PHP}settings_integration/`;

    let set_types = {
        ACCOUNT: 0x0001,
        PRODUCT: 0x0002
    };

    let get_types = {
        PRODUCTS: 0x0001
    }

    let areas = {
        INTEGRATION_STATUS: "integration_status_"
    }

    function settings_integration(){ initialize(); }
    function initialize(){
        page.initialize();
        yemek_sepeti.initialize();
    }

    let page = {
        id_list: {},
        class_list: {},
        fill_areas: function (){
            let self = this;

            main.data_list.INTEGRATION_USERS.forEach(data => {
                let element = $(`[area=${areas.INTEGRATION_STATUS + data.type}]`);
                if(data.is_active == 1){
                    element.html("Açık");
                }else{
                    element.html("Kapalı");
                }
            });
        },
        initialize(){
            let self = this;

            function set_events(){

            }

            self.fill_areas();
            set_events();
        }
    }
    let yemek_sepeti = {
        id_list: {
            MODAL_ACCOUNT: "#modal_account_yemek_sepeti",
            MODAL_PRODUCTS: "#modal_products_yemek_sepeti",
            FORM_ACCOUNT: "#form_account_yemek_sepeti",
            FORM_PRODUCTS: "#form_products_yemek_sepeti"
        },
        class_list: {
            SEARCH_PRODUCT: ".e_search_product",
            PRODUCT_SHOW_BTN: ".e_product_show_btn_yemek_sepeti",
            TABLE_PRODUCTS: ".e_products_table",
            TABLE_PRODUCTS_INTEGRATE: ".e_products_integrate_table",
            PRODUCTS: ".e_products",
            PRODUCTS_INTEGRATE: ".e_products_integrate",
            BUTTON_LIST: ".e_btn_list"
        },
        variable_list: {
            list_types: {
                PRODUCT: 1,
                OPTION: 2
            },
            DATA: Array(),
            SELECTED_PRODUCTS: Array(),
            SELECTED_TYPE: 0,
            SELECTED_PRODUCT_ID: "",
            LIST_TYPE: 1,
            search_values: {
                INTEGRATE: "",
                DIGIGARSON: ""
            }
        },
        get: function (){
            let self = this;
            $(`${self.class_list.FORM}[type="${self.variable_list.SELECTED_TYPE}"]`).autofill(array_list.find(main.data_list.INTEGRATION_USERS, self.variable_list.SELECTED_TYPE, "type"));
        },
        get_products: function (data_clear = true) {
            let self = this;

            function create_element(){
                let elements = {
                    "products": "",
                    "products_integrate": ""
                };

                let key = "";
                let data_type = self.variable_list.DATA[self.variable_list.SELECTED_TYPE];
                let data_integrate = Array();
                let data_integrate_product = Array();
                let data_integrated = (self.variable_list.LIST_TYPE === self.variable_list.list_types.PRODUCT)
                    ? data_type.integrated.products
                    : data_type.integrated.options;
                switch (self.variable_list.SELECTED_TYPE) {
                    case helper.db.integrate_types.YEMEK_SEPETI:
                        data_integrate = (self.variable_list.LIST_TYPE === self.variable_list.list_types.PRODUCT)
                            ? data_type.integrate.Menu.Products
                            : data_type.integrate.Menu.Options;
                        data_integrate_product = data_type.integrate.Menu.Products;
                        break;
                }

                (
                    (self.variable_list.LIST_TYPE === self.variable_list.list_types.PRODUCT)
                        ? main.data_list.PRODUCTS
                        : main.data_list.PRODUCT_OPTIONS_ITEMS
                ).forEach(data => {
                    if((self.variable_list.LIST_TYPE === self.variable_list.list_types.PRODUCT)) if(data.is_delete == 1) return;
                    else if(data.is_deleted == 1) return;

                    if(self.variable_list.search_values.DIGIGARSON.length > 0) {
                        if(!String(data.name.toLocaleLowerCase("tr")).match(new RegExp(self.variable_list.search_values.DIGIGARSON.toLocaleLowerCase("tr"), "gi"))) {
                            return;
                        }
                    }

                    let classes = "";
                    let option_name_for_option_item = "";

                    if(self.variable_list.LIST_TYPE === self.variable_list.list_types.PRODUCT) {
                        key = "product_id";
                        let data_find = array_list.find(data_integrated, data.id, key);
                        if(typeof data_find !== "undefined"){
                            classes += " product-selected-confirmed ";
                        }
                    }

                    if(self.variable_list.LIST_TYPE === self.variable_list.list_types.OPTION) {
                        let option_name = array_list.find(main.data_list.PRODUCT_OPTIONS, data.option_id, "id");
                        if(typeof option_name !== "undefined")
                            option_name_for_option_item = `(${option_name.search_name})`;
                    }

                    let selected_product = array_list.find(self.variable_list.SELECTED_PRODUCTS, data.id, "product_id");
                    if(typeof selected_product !== "undefined"){
                        if(self.variable_list.LIST_TYPE === self.variable_list.list_types.PRODUCT) classes += ` product-selected-confirmed `;
                    }

                    elements.products += `
                        <tr product-id="${data.id}" class="=${classes}">
                            <td><small class="option-info">${option_name_for_option_item}</small> ${data.name}</td>
                        </tr> 
                    `;
                });

                data_integrate.forEach(data => {
                    if(self.variable_list.search_values.INTEGRATE.length > 0) {
                        if(!String(data.Name.toLocaleLowerCase("tr")).match(new RegExp(self.variable_list.search_values.INTEGRATE.toLocaleLowerCase("tr"), "gi"))) {
                            return;
                        }
                    }

                    let classes = "";
                    let integrated_product = {
                        "name": "",
                        "id": ""
                    };
                    let product_name_for_option = "";

                    key = (self.variable_list.LIST_TYPE === self.variable_list.list_types.PRODUCT)
                        ? "product_id_integrated"
                        : "option_id_integrated";
                    let data_find = array_list.find(data_integrated, data.Id, key);
                    if(typeof data_find !== "undefined"){
                        classes += " product-integrate-selected-confirmed ";
                        let data_integrated_product = (self.variable_list.LIST_TYPE === self.variable_list.list_types.PRODUCT)
                            ? array_list.find(main.data_list.PRODUCTS, data_find.product_id, "id")
                            : array_list.find(main.data_list.PRODUCT_OPTIONS_ITEMS, data_find.option_id, "id");
                        if(typeof data_integrated_product !== "undefined") {
                            integrated_product.name = `(${data_integrated_product.name})`;
                            if(self.variable_list.LIST_TYPE === self.variable_list.list_types.PRODUCT) integrated_product.id = data_integrated_product.id;
                        }
                    }
                    if(self.variable_list.LIST_TYPE === self.variable_list.list_types.OPTION) {
                        let product_name = array_list.find(data_integrate_product, data.ProductId, "Id");
                        if(typeof product_name !== "undefined")
                            product_name_for_option = `(${product_name.Name})`;
                    }

                    let selected_product = array_list.find(self.variable_list.SELECTED_PRODUCTS, data.Id, "product_integrate_id");
                    if(typeof selected_product !== "undefined"){
                        selected_product.product_id = parseInt(selected_product.product_id);
                        classes += ` product-integrate-selected-confirmed `;
                        let data_integrated_product = (self.variable_list.LIST_TYPE === self.variable_list.list_types.PRODUCT)
                            ? array_list.find(main.data_list.PRODUCTS, selected_product.product_id, "id")
                            : array_list.find(main.data_list.PRODUCT_OPTIONS_ITEMS, selected_product.product_id, "id");
                        if(typeof data_integrated_product !== "undefined") {
                            integrated_product.name = `(${data_integrated_product.name})`;
                            if(self.variable_list.LIST_TYPE === self.variable_list.list_types.PRODUCT) integrated_product.id = data_integrated_product.id;
                        }
                    }

                    elements.products_integrate += `
                        <tr product-id="${data.Id}" class="${classes}">
                            <td><small class="option-info">${product_name_for_option}</small> ${data.Name} <small product-id="${integrated_product.id}" function="integrated_product">${integrated_product.name}</small></td>
                        </tr> 
                    `;
                });

                return elements;
            }

            if(typeof self.variable_list.DATA[self.variable_list.SELECTED_TYPE] === "undefined" || self.variable_list.DATA[self.variable_list.SELECTED_TYPE].length < 1){
                self.variable_list.DATA[self.variable_list.SELECTED_TYPE] = {
                    "integrate": Array(),
                    "integrated": Array()
                };

                integrated_companies.yemek_sepeti.products.get(function (data) {
                    self.variable_list.DATA[self.variable_list.SELECTED_TYPE].integrate = data.rows;
                    get(
                        get_types.PRODUCTS,
                        {type: self.variable_list.SELECTED_TYPE},
                        function (data) {
                            data = JSON.parse(data);
                            console.log(data);
                            self.variable_list.DATA[self.variable_list.SELECTED_TYPE].integrated = data.rows;
                        }
                    );
                });
            }

            let elements = create_element();
            $(`${self.id_list.MODAL_PRODUCTS} ${self.class_list.PRODUCTS}`).html(elements.products);
            $(`${self.id_list.MODAL_PRODUCTS} ${self.class_list.PRODUCTS_INTEGRATE}`).html(elements.products_integrate);
            if(data_clear) self.variable_list.SELECTED_PRODUCTS = Array();
        },
        initialize: function (){
            let self = this;

            function set_events(){
                $(self.class_list.SEARCH_PRODUCT).on("keyup change", function () {
                    let element = $(this);
                    let function_name = element.attr("function");

                    switch (function_name){
                        case "integrate":
                            self.variable_list.search_values.INTEGRATE = element.val();
                            break;
                        case "digigarson":
                            self.variable_list.search_values.DIGIGARSON = element.val();
                            break;
                    }

                    self.get_products(false);
                });

                $(self.id_list.FORM_ACCOUNT).submit(function (e) {
                    e.preventDefault();
                    let object = $(this).serializeObject();
                    let type = $(this).attr("type");
                    let form_data = Object.assign(object, {"type": type})
                    integrated_companies.yemek_sepeti.restaurant_list.get(object,function (data) {
                        if (data.rows > 0 || typeof data.rows.RestaurantList !== "undefined"){
                            set(
                                set_types.ACCOUNT,
                                form_data,
                                function (data2) {
                                    console.log(data2);
                                    helper_sweet_alert.success(language.data.PROCESS_SUCCESS_TITLE, language.data.REGISTERED_ACCOUNT);
                                    main.get_integrate_related_things(main.get_type_for_integrate_related_things.USERS);
                                    page.fill_areas();
                                }
                            );
                        }else{
                            helper_sweet_alert.error(language.data.LOGIN_FAILED, language.data.LOGIN_ERROR);
                        }
                    })
                });

                $(self.id_list.MODAL_ACCOUNT).on("show.bs.modal", function () {
                    self.variable_list.SELECTED_TYPE = helper.db.integrate_types.YEMEK_SEPETI;
                    self.get();
                });

                $(self.class_list.PRODUCT_SHOW_BTN).on("click", function (){
                    let is_active = false;
                    if(typeof array_list.find(main.data_list.INTEGRATION_USERS, helper.db.integrate_types.YEMEK_SEPETI, "type") !== "undefined"){
                        let user = array_list.find(main.data_list.INTEGRATION_USERS, helper.db.integrate_types.YEMEK_SEPETI, "type");
                        if(user.is_active == 1){
                            is_active = true;
                        }
                    }
                    if(is_active) $(self.id_list.MODAL_PRODUCTS).modal("show");
                    else helper_sweet_alert.error(language.data.PROCESS_SUCCESS_TITLE, "Lütfen ilk önce hesap bölümünden 'Yemek Sepeti' hesabınıza giriş yapınız ve aktif ediniz.")
                });

                $(self.id_list.MODAL_PRODUCTS).on("show.bs.modal", function () {
                    self.variable_list.SELECTED_TYPE = helper.db.integrate_types.YEMEK_SEPETI;
                    self.variable_list.LIST_TYPE = self.variable_list.list_types.PRODUCT;
                    self.get_products();
                    if(!$(self.class_list.PRODUCTS).hasClass("products-table-disabled"))
                        $(self.class_list.TABLE_PRODUCTS).addClass("products-table-disabled");
                });

                $(document).on("click", `${self.class_list.PRODUCTS_INTEGRATE} tr`, function () {
                    let element = $(this);

                    let id = element.attr("product-id").toString();
                    $(`${self.class_list.PRODUCTS_INTEGRATE} tr`).removeClass("product-integrate-selected");

                    if(self.variable_list.SELECTED_PRODUCT_ID === id || element.hasClass("product-integrate-selected-confirmed")){
                        let id_integrated = $(`${self.class_list.PRODUCTS_INTEGRATE} tr[product-id="${id}"] [function="integrated_product"]`).attr("product-id");
                        $(`${self.class_list.PRODUCTS_INTEGRATE} tr[product-id="${id}"] [function="integrated_product"]`).html(``).attr("product-id", "");
                        self.variable_list.SELECTED_PRODUCTS.forEach((data, index) => {
                            if(data.product_integrate_id === id){
                                self.variable_list.SELECTED_PRODUCTS.splice(index, 1);
                            }
                        });
                        console.log(id_integrated);
                        $(`${self.class_list.PRODUCTS} tr[product-id="${id_integrated}"]`).removeClass("product-selected-confirmed");
                        element.removeClass("product-integrate-selected-confirmed");

                        self.variable_list.SELECTED_PRODUCT_ID = "";
                        $(self.class_list.TABLE_PRODUCTS).addClass("products-table-disabled");
                    }else{
                        self.variable_list.SELECTED_PRODUCT_ID = id;
                        element.addClass("product-integrate-selected");
                        $(self.class_list.TABLE_PRODUCTS).removeClass("products-table-disabled");
                    }
                    console.log(self.variable_list.SELECTED_PRODUCTS);
                });

                $(document).on("click", `${self.class_list.PRODUCTS} tr`, function () {
                    let element = $(this);


                    let id = element.attr("product-id");

                    $(`${self.class_list.PRODUCTS_INTEGRATE} tr[product-id="${self.variable_list.SELECTED_PRODUCT_ID}"] [function="integrated_product"]`).html(`(${element.html()})`).attr("product-id", id);
                    $(`${self.class_list.PRODUCTS_INTEGRATE} tr[product-id="${self.variable_list.SELECTED_PRODUCT_ID}"]`)
                        .removeClass("product-integrate-selected")
                        .addClass("product-integrate-selected-confirmed");
                    self.variable_list.SELECTED_PRODUCTS.push({
                        "product_id": id,
                        "product_integrate_id": self.variable_list.SELECTED_PRODUCT_ID,
                    });
                    if(self.variable_list.LIST_TYPE === self.variable_list.list_types.PRODUCT) element.addClass("product-selected-confirmed");
                });

                $(self.id_list.FORM_PRODUCTS).submit(function (e) {
                    e.preventDefault();

                    if(self.variable_list.SELECTED_PRODUCTS.length > 0){
                        set(
                            set_types.PRODUCT,
                            {"type": self.variable_list.SELECTED_TYPE, "products": self.variable_list.SELECTED_PRODUCTS, "list_type": self.variable_list.LIST_TYPE},
                            function (data) {
                                data = JSON.parse(data);
                                console.log(data);
                                if(data.status){
                                    helper_sweet_alert.success(language.data.PROCESS_SUCCESS_TITLE, "Ürünler başarı ile eşleştirildi!");
                                    self.variable_list.DATA[self.variable_list.SELECTED_TYPE] = Array();
                                    $(self.id_list.MODAL_PRODUCTS).modal("hide");
                                }
                            }
                        );
                    }
                });

                $(self.class_list.BUTTON_LIST).on("click", function () {
                    let element = $(this);
                    let function_name = element.attr("function");

                    $(self.class_list.BUTTON_LIST).removeClass("btn-success").removeClass("btn-warning").addClass("btn-warning");
                    element.addClass("btn-success").removeClass("btn-warning");

                    self.variable_list.LIST_TYPE = parseInt(function_name);
                    self.get_products();
                });
            }

            set_events();
        }
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
                console.log(data);
                success_function(data);
            },error: helper_sweet_alert.close(), timeout: settings.ajax_timeouts.NORMAL
        });
    }
    function get(get_type, data, success_function = null, async = false, sweet_alert = false){
        data["get_type"] = get_type;
        console.log(data);
        if (sweet_alert) helper_sweet_alert.wait(language.data.PROCESS_PROGRESS_TITLE, language.data.PROCESS_WAIT_CONTENT);
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
    return settings_integration;
})();

$(function () {
    let _settings_integration = new settings_integration();
});