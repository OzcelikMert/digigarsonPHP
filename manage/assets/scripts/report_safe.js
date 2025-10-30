let report_safe = (function () {
    let default_ajax_path = `${settings.paths.primary.PHP}report_safe/`;
    let default_ajax_path_pos_orders = `../pos/${settings.paths.primary.PHP}orders/`;
    let set_types = {
        TRUST_ACCOUNT_INSERT: 0x0001,
        CLOSE_SAFE: 0x0002,
        COST: 0x0003,
        INVOICE_EDIT: 0x0004,
        TRUST_ACCOUNT_PAYMENT: 0x0005
    };
    let get_types = {
        SAFE_LIST: 0x0001,
        SAFE: 0x0002,
        INVOICE: 0x0003,
        TRUST: 0x0004,
        TRUST_PAYMENTS: 0x0005,
        COST: 0x0006,
        Z_REPORT: 0x0007
    };
    let id_list = {
        CLOSE_SAFE_COMMENT: "#close_safe_comment"
    }
    let class_list = {
        NAVIGATION_BUTTON: ".e_navigation_btn",
        SAFE_CLOSE: ".e_safe_close"
    }

    function report_safe(){ initialize(); }

    function initialize(){

        function set_events() {
            $(class_list.NAVIGATION_BUTTON).on("click", function () {
                let function_name = $(this).attr("function");

                if(function_name == "show_safe_report_invoice"){
                    get(
                        get_types.Z_REPORT,
                        {safe_id: safe.variable_list.SELECTED_SAFE_ID},
                        function (data){
                            data = JSON.parse(data)
                            console.log(data);
                            function get_data(data){
                                data.payments.data.forEach(data_ => {
                                    data_.name = array_list.find(data.payments.names, data_.type, "id").name;
                                });

                                data.trust_payments.data.forEach(data_ => {
                                    data_.name = array_list.find(data.trust_payments.names, data_.type, "id").name;
                                });

                                data.products.data_options.forEach(data_ => {
                                    data_.name = array_list.find(data.products.name_options, data_.option_item_id, "id").name.toString();
                                });

                                data.products.data.forEach(data_ => {
                                    let name = "";
                                    if(typeof array_list.find(data.products.names, data_.product_id, "id") === "undefined") name = "Iskonto";
                                    else name = array_list.find(data.products.names, data_.product_id, "id").name;
                                    data_.name = name.toString();
                                    data_.options = array_list.find_multi(data.products.data_options, data_.product_id, "product_id")
                                });

                                data.cancel_products.data_options.forEach(data_ => {
                                    data_.name = array_list.find(data.cancel_products.name_options, data_.option_item_id, "id").name.toString();
                                });

                                data.cancel_products.data.forEach(data_ => {
                                    let name = "";
                                    if(typeof array_list.find(data.cancel_products.names, data_.product_id, "id") === "undefined") name = "Iskonto";
                                    else name = array_list.find(data.cancel_products.names, data_.product_id, "id").name;
                                    data_.name = name.toString();
                                    data_.options = array_list.find_multi(data.cancel_products.data_options, data_.product_id, "product_id")
                                });

                                let data_info = {
                                    "safe": safe.variable_list.SELECTED_SAFE_ID,
                                    "safe_open": "",
                                    "safe_close": "",
                                }
                                if(data_info.safe > 0){
                                    let safe_find = array_list.find(safe_list.variable_list.DATA, data_info.safe, "id");
                                    data_info.safe_open = safe_find.date_start;
                                    data_info.safe_close = safe_find.date_end;
                                }
                                return {
                                    "payments": data.payments.data,
                                    "trust_payments": data.trust_payments.data,
                                    "products": data.products.data,
                                    "cancel_products": data.cancel_products.data,
                                    "costs": data.costs.data,
                                    "info": data_info
                                };
                            }
                            invoice.return_html = true;

                            helper_sweet_alert.wait(language.data.LOADING, language.data.PLS_WAIT);
                            let html = invoice.z_report(get_data(data.rows));
                            $(`${invoice_.class_list.INVOICE_SHOW_ELEMENTS} iframe`).attr("srcdoc", html);
                            $(invoice_.id_list.MODAL_INVOICE_SHOW).modal("show");
                            helper_sweet_alert.close();
                        }
                    );
                    return;
                }

                $(`${safe.id_list.SAFE},${invoice_.id_list.INVOICE},${trust.id_list.TRUST},${safe_list.id_list.SAFE_LIST},${cost.id_list.COST}`).hide();
                switch (function_name) {
                    case "safe":
                        if (safe.variable_list.DATA.length < 1)
                            safe.get();
                        $(safe.id_list.SAFE).show();
                        break;
                    case "safe_list":
                        if (safe_list.variable_list.DATA.length < 1)
                            safe_list.get();
                        $(safe_list.id_list.SAFE_LIST).show();
                        break;
                    case "invoice":
                        if(invoice_.variable_list.DATA.length < 1)
                            invoice_.get();
                        $(invoice_.id_list.INVOICE).show();
                        break;
                    case "trust":
                        if($(trust.class_list.e_trust_account_btn).length < 1)
                            trust.get();
                        $(trust.id_list.TRUST).show();
                        break;
                    case "cost": cost.get(); $(cost.id_list.COST).show(); break;
                }
            });
            
            $(class_list.SAFE_CLOSE).on("click", function () {
                Swal.fire({
                    icon: "question",
                    title: "Kasa Kapatma İşlemi",
                    html: `
                    <label for="close_safe_comment"><{language.data.DESCRIPTION}</label>
                    <input id="close_safe_comment" placeholder="Açıklama" class="form-input" type="text" />
                    ${language.data.SAFE_CLOSE_QUESTION}
                    `,
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    showCancelButton: true,
                    confirmButtonText: language.data.ACCEPT,
                    cancelButtonText: language.data.CANCEL,
                    confirmButtonClass: 'btn btn-danger btn-lg mr-3 mt-5',
                    cancelButtonClass: 'btn btn-primary btn-lg ml-3 mt-5',
                    buttonsStyling: false
                }).then((result) => {
                    if (result.value) {
                        set(
                            set_types.CLOSE_SAFE,
                            {
                                "comment": $(id_list.CLOSE_SAFE_COMMENT).val()
                            },
                            function (data) {
                                data = JSON.parse(data);
                                console.log(data);
                                if(data.status){
                                    helper_sweet_alert.success(onlanguagechange.data.PROCESS_SUCCESS_TITLE, language.data.SAFE_CLOSE_TEXT);
                                    setTimeout(function () {
                                        location.reload();
                                    }, 1500);
                                }
                            }
                        );
                    }
                });
            });
        }

        set_events();
        safe.initialize();
        safe_list.initialize();
        invoice_.initialize();
        trust.initialize();
        cost.initialize();
    }

    let safe = {
        class_list: {
            PAYMENTS: ".e_payments",
            PAYMENTS_OTHER: ".e_payments_other",
            PAYMENTS_TOTAL: ".e_payments_total",
            PAYMENTS_TRUST: ".e_payments_trust"
        },
        id_list: {
            SAFE: "#safe"
        },
        data_types: {
            PAYMENTS: "payments",
            ORDERS: "orders",
            ORDER_PRODUCTS: "order_products",
            ORDER_PRODUCT_OPTIONS: "order_product_options"
        },
        variable_list: {
            SELECTED_SAFE_ID: 0,
            DATA: Array()
        },
        get: function () {
            let self = this;

            function get_data(){
                let data = {
                    "payments_other": {
                        "cancel": 0,
                        "discount": 0,
                        "catering": 0,
                        "cost": 0,
                        "trust": 0
                    },
                    "payments_total": {
                        "total": 0,
                        "total_real": 0,
                        "open_tables": 0,
                        "open_and_close_tables": 0
                    },
                    "payments": [],
                    "payments_trust": []
                };

                let order_id_saved = {
                    "cancel": Array(),
                    "catering": Array(),
                    "cost": Array()
                };

                self.variable_list.DATA[self.data_types.PAYMENTS].forEach(payment => {
                    try {
                        let price = parseFloat(payment.price);
                        let payment_find = array_list.find(main.data_list.PAYMENT_TYPES, payment.type, "id");

                        // Payments Other
                        if (payment.status !== helper.db.order_payment_status_types.PAID) {
                            let column_payment_other = "";
                            switch (payment.status) {
                                case helper.db.order_payment_status_types.CANCEL:
                                    column_payment_other = "cancel";
                                    break;
                                case helper.db.order_payment_status_types.CATERING:
                                    column_payment_other = "catering";
                                    break;
                                case helper.db.order_payment_status_types.COST:
                                    column_payment_other = "cost";
                                    data.payments_other[column_payment_other] += parseFloat(payment.price);
                                    return;
                                    break;
                            }

                            if(order_id_saved[column_payment_other].includes(payment.order_id)) return;
                            order_id_saved[column_payment_other].push(payment.order_id);
                            let order_products_find = array_list.find_multi(self.variable_list.DATA[self.data_types.ORDER_PRODUCTS], payment.order_id, "order_id");

                            order_products_find.forEach(order_product => {
                                if(order_product.status == payment.status) {
                                    data.payments_other[column_payment_other] += parseFloat(order_product.price);
                                }
                            });

                            return;
                        }

                        // Payments
                        let payment_key = (payment.order_id > 0) ? "payments" : "payments_trust";
                        let index = array_list.index_of(data[payment_key], payment_find.name, "name");
                        if (typeof index !== "undefined" && index > -1) {
                            data[payment_key][index]["price"] += price;
                        } else {
                            data[payment_key].push({
                                "name": payment_find.name,
                                "price": price
                            });
                        }

                        // Payments Total
                        if (payment.type !== 6 && payment.order_id > 0) data.payments_total.total += price;
                        if (price > 0) data.payments_total.total_real += price;
                        if (payment.order_id === 0) data.payments_other.trust += price;
                    }catch (e) {
                        console.error(e);
                    }
                });

                self.variable_list.DATA[self.data_types.ORDERS].forEach(order => {
                    if(order.date_end === null || order.date_end === ""){
                        let products = array_list.find_multi(self.variable_list.DATA[self.data_types.ORDER_PRODUCTS], order.id, "order_id");
                        products.forEach(product => {
                            if(product.status !== helper.db.order_product_status_types.ACTIVE) return;
                            data.payments_total.open_tables += parseFloat(product.price);
                        });
                    }
                });

                self.variable_list.DATA[self.data_types.ORDER_PRODUCTS].forEach(order_product => {
                    if(order_product.type == helper.db.order_product_types.DISCOUNT && order_product.status != helper.db.order_product_status_types.CANCEL) data.payments_other.discount += parseFloat(order_product.price);
                })

                data.payments_total.open_and_close_tables = data.payments_total.open_tables + data.payments_total.total;

                data.payments_total.total += data.payments_other.cost;

                return data;
            }

            function create_element(){
                let element = {
                    payments: ``,
                    payments_total: ``,
                    payments_other: ``,
                    payments_trust: ``
                };

                let data = get_data();

                data.payments.forEach(payment => {
                    element.payments += `
                        <tr> 
                            <td class="text-left">${payment.name}</td> 
                            <td class="text-right">${payment.price.toFixed(2) + main.data_list.CURRENCY}</td>
                        </tr>
                    `;
                });

                data.payments_trust.forEach(payment => {
                    element.payments_trust += `
                        <tr> 
                            <td class="text-left">${payment.name}</td> 
                            <td class="text-right">${payment.price.toFixed(2) + main.data_list.CURRENCY}</td>
                        </tr>
                    `;
                });

                element.payments_other = `
                    <tr> 
                        <td class="text-left">${language.data.TOTAL_CANCEL}</td>
                        <td class="text-right">${data.payments_other.cancel.toFixed(2) + main.data_list.CURRENCY}</td>
                    </tr>
                    <tr>
                        <td class="text-left">${language.data.TOTAL_DISCOUNT}</td>
                        <td class="text-right">${data.payments_other.discount.toFixed(2) + main.data_list.CURRENCY}</td>
                    </tr>
                    <tr>
                        <td class="text-left">${language.data.TOTAL_CATERING}</td>
                        <td class="text-right">${data.payments_other.catering.toFixed(2) + main.data_list.CURRENCY}</td>
                    </tr>
                    <tr>
                        <td class="text-left">${language.data.TOTAL_COST}</td>
                        <td class="text-right">${data.payments_other.cost.toFixed(2) + main.data_list.CURRENCY}</td>
                    </tr>
                    <tr>
                        <td class="text-left">${language.data.TOTAL_CREDIT}</td>
                        <td class="text-right">${data.payments_other.trust.toFixed(2) + main.data_list.CURRENCY}</td>
                    </tr>
                `;

                element.payments_total = `
                    <tr>
                        <td class="text-left">${language.data.TOTAL}</td>
                        <td class="text-right">${data.payments_total.total.toFixed(2) + main.data_list.CURRENCY}</td>
                    </tr>
                    <tr>
                        <td class="text-left">${language.data.TOTAL_TURNOVER}</td>
                        <td class="text-right">${data.payments_total.total_real.toFixed(2) + main.data_list.CURRENCY}</td>
                    </tr>
                    <tr>
                        <td class="text-left">${language.data.OPEN_TICKETS}</td>
                        <td class="text-right">${data.payments_total.open_tables.toFixed(2) + main.data_list.CURRENCY}</td>
                    </tr>
                    <tr>
                        <td class="text-left">${language.data.ADDITON_QUERY}</td>
                        <td class="text-right">${data.payments_total.open_and_close_tables.toFixed(2) + main.data_list.CURRENCY}</td>
                    </tr>
                `;

                return element;
            }

            if(self.variable_list.DATA.length < 1){
                //todo
                helper_sweet_alert.wait(language.data.CHANGE_SAFE, "Kasa verileri alınıyor...");
                get(
                    get_types.SAFE,
                    {"safe_id": self.variable_list.SELECTED_SAFE_ID},
                    function (data) {
                        data = JSON.parse(data);
                        console.log(data);
                        if(data.status){
                            self.variable_list.DATA = data.rows;
                            main.data_list.ORDERS = self.variable_list.DATA[self.data_types.ORDERS];
                            main.data_list.ORDER_PRODUCTS = self.variable_list.DATA[self.data_types.ORDER_PRODUCTS];
                            main.data_list.ORDER_PRODUCT_OPTIONS = self.variable_list.DATA[self.data_types.ORDER_PRODUCT_OPTIONS];
                            let elements = create_element();
                            $(self.class_list.PAYMENTS).html(elements.payments);
                            $(self.class_list.PAYMENTS_OTHER).html(elements.payments_other);
                            $(self.class_list.PAYMENTS_TOTAL).html(elements.payments_total);
                            $(self.class_list.PAYMENTS_TRUST).html(elements.payments_trust);
                        }
                        helper_sweet_alert.close();
                    },
                    true
                );
            }
        },
        initialize: function (){
            let self = this;

            function set_events(){
            }

            set_events();
            self.get();
        }
    }

    let safe_list = {
        class_list: {
            SAFE_LIST: ".e_safe_list",
            SAFE: ".e_safe_list_safe",
            SAFE_LIST_SELECT: ".e_safe_list_select"
        },
        id_list: {
            SAFE_LIST: "#safe_list"
        },
        variable_list: {
            DATA: Array()
        },
        get: function () {
            let self = this;

            function create_element(){
                let elements = `
                    <tr safe-id="0" class="e_safe_list_safe" style="background-color: #0ba110">
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td><h4>${language.data.ACTIVE_CASE}</h4></td>
                        <td>
                            <button class="e_safe_list_select btn btn-info w-100">
                                Seç 
                                <i class="fa fa-arrow-right"></i>
                            </button>
                        </td>
                    </tr>
                `;

                self.variable_list.DATA.forEach(safe => {
                    elements += `
                        <tr safe-id="${safe.id}" class="e_safe_list_safe">
                            <td>${safe.id}</td>
                            <td>${safe.date_start}</td>
                            <td>${safe.date_end}</td>
                            <td>${safe.name}</td>
                            <td>${safe.comment}</td>
                            <td>
                                <button class="e_safe_list_select btn btn-info w-100">
                                    ${language.data.CHOOSE} 
                                    <i class="fa fa-arrow-right"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });

                return elements;
            }

            if(self.variable_list.DATA.length < 1){
                get(
                    get_types.SAFE_LIST,
                    {},
                    function (data) {
                        data = JSON.parse(data);
                        console.log(data);
                        if(data.status){
                            self.variable_list.DATA = data.custom_data;
                        }
                    }
                );
            }

            $(self.class_list.SAFE_LIST).html(create_element());
        },
        initialize: function (){
            let self = this;

            function set_events(){
                $(document).on("click", self.class_list.SAFE_LIST_SELECT, function () {
                    safe.variable_list.SELECTED_SAFE_ID = parseInt($(this).closest("[safe-id]").attr("safe-id"));
                    safe.variable_list.DATA = Array();
                    invoice_.variable_list.DATA = Array();
                    $(safe.class_list.PAYMENTS).html("");
                    $(safe.class_list.PAYMENTS_OTHER).html("");
                    $(safe.class_list.PAYMENTS_TOTAL).html("");
                    $(safe.class_list.PAYMENTS_TRUST).html("");
                    $(`${class_list.NAVIGATION_BUTTON}[function='safe']`).trigger("click");
                });
            }

            set_events();
            self.get();
        }
    }

    let invoice_ = {
        class_list: {
            INVOICES: ".e_invoices",
            INVOICE: ".e_invoice",
            INVOICE_SHOW_ELEMENTS: ".e_invoice_show_elements",
            BUTTON_INVOICE_SHOW: ".e_invoice_show_btn",
            INVOICE_EDIT_ITEMS: ".e_modal_invoice_edit_items"
        },
        id_list: {
            INVOICE: "#invoice",
            MODAL_INVOICE_SHOW: "#modal_invoice_show",
            MODAL_INVOICE_EDIT: "#modal_invoice_edit",
            FORM_INVOICE_EDIT: "#modal_invoice_edit_form"
        },
        data_types: {
            PAYMENTS: "payments",
            CUSTOMER_USERS: "customer_users",
            BRANCH_USERS: "branch_users",
            ORDERS: "orders"
        },
        variable_list:{
            SELECTED_ORDER_ID: 0,
            DATA: Array(),
        },
        get: function () {
            let self = this;

            function create_element() {
                let element = ``;
                let data = Array();

                self.variable_list.DATA[self.data_types.PAYMENTS].forEach(payment => {
                    if(payment.type === 0 || payment.order_id === 0) return;
                    if(typeof data[payment.order_id] === "undefined")
                        data[payment.order_id] = {
                            "id": payment.order_id,
                            "no": payment.order_no,
                            "table_id": payment.table_id,
                            "payments": Array()
                        };
                    if(typeof data[payment.order_id].payments[payment.type] === "undefined") {
                        let account_find = array_list.find(
                            ((payment.account_type == helper.db.account_types.WAITER)
                                ? self.variable_list.DATA[self.data_types.BRANCH_USERS]
                                : self.variable_list.DATA[self.data_types.CUSTOMER_USERS]),
                            payment.account_id,
                            "id"
                        );

                        data[payment.order_id].payments[payment.type] = {
                            "type": payment.type,
                            "price": 0,
                            "date": payment.date,
                            "account_type": account_find.type,
                            "account_name": account_find.name,
                        };
                    }

                    data[payment.order_id].payments[payment.type].price += parseFloat(payment.price);
                });

                console.log(data);

                data.forEach(order => {
                    if(order.table_id == 0) return;
                    let table = array_list.find(main.data_list.TABLES, order.table_id, "id");
                    if(typeof table === "undefined") return;
                    let table_section = array_list.find(main.data_list.SECTION_TYPES, array_list.find(main.data_list.SECTIONS, table.section_id, "id").section_id, "id");
                    let data_payment = Array();
                    let payment_element = ``;
                    let total = 0;

                    order.payments.forEach(payment => {
                        let payment_type = array_list.find(main.data_list.PAYMENT_TYPES, payment.type, "id");
                        data_payment = payment;
                        total += payment.price;
                        payment_element += `${payment_type.name} (${payment.price.toFixed(2) + main.data_list.CURRENCY}), `;
                    });

                    payment_element = payment_element.slice(0, -2);
                    element += `
                            <tr order-id='${order.id}' class="e_invoice">
                                <td>${order.no}</td>
                                <td>(${data_payment.account_type}) <span function="name">${data_payment.account_name}</span></td>
                                <td><span function="table">${table_section.name} ${table.no}</span></td>
                                <td>${data_payment.date}</td>
                                <td>${total.toFixed(2) + main.data_list.CURRENCY}</td>
                                <td>${payment_element}</td>
                                <td class="p-1"><button function="view" class="btn btn-sm m-0 btn-s1 w-100"><i class="fas fa-eye"></i></button></td>
                                <td class="p-1"><button function="edit" class="btn btn-sm m-0 btn-s1 w-100"><i class="fas fa-pencil-alt"></i></button></td>
                            </tr>
                        `;
                });

                return element;
            }

            if(self.variable_list.DATA.length < 1){
                get(
                    get_types.INVOICE,
                    {"safe_id": safe.variable_list.SELECTED_SAFE_ID},
                    function (data) {
                        data = JSON.parse(data);
                        console.log(data);
                        if(data.status){
                            self.variable_list.DATA = data.rows;
                        }
                    }
                );
            }

            $(self.class_list.INVOICES).html(create_element());
        },
        initialize: function (){
            let self = this;

            function set_events(){

                $(document).on("click", `${self.class_list.INVOICE} button`, function () {
                    let element = $(this);
                    let function_name = element.attr("function");
                    self.variable_list.SELECTED_ORDER_ID = parseInt(element.closest("[order-id]").attr("order-id"));

                    switch (function_name){
                        case "view":
                            invoice.return_html = true;
                            let name = $(`${self.class_list.INVOICE}[order-id="${self.variable_list.SELECTED_ORDER_ID}"] span[function="name"]`).html();
                            let table = $(`${self.class_list.INVOICE}[order-id="${self.variable_list.SELECTED_ORDER_ID}"] span[function="table"]`).html();
                            console.log(name,table);
                            $.ajax({
                                url: "../public/assets/printer/invoice.php",
                                type: "POST",
                                data: {elements: invoice.payyed_payment_receipt(self.variable_list.SELECTED_ORDER_ID, true)},
                                async: false,
                                success: function (data) {
                                    $(`${self.class_list.INVOICE_SHOW_ELEMENTS} iframe`).attr("srcdoc", data);
                                    $(self.id_list.MODAL_INVOICE_SHOW).modal("show");
                                }
                            });
                            break;
                        case "edit":
                            $(self.id_list.MODAL_INVOICE_EDIT).modal("show");
                            break;
                    }
                });

                $(self.id_list.MODAL_INVOICE_SHOW).on("hidden.bs.modal", function (){
                    $(`${self.class_list.INVOICE_SHOW_ELEMENTS} iframe`).attr("srcdoc", "");
                });

                $(self.class_list.BUTTON_INVOICE_SHOW).on("click", function () {
                    let function_name = $(this).attr("function");

                    switch (function_name){
                        case "print":
                            $(`${self.class_list.INVOICE_SHOW_ELEMENTS} iframe`).get(0).contentWindow.print();
                            break;
                    }
                });

                $(self.id_list.MODAL_INVOICE_EDIT).on("show.bs.modal", function () {
                    let total = 0;

                    function create_element(){
                        let element = ``;

                        let index = 0;
                        array_list.find_multi(self.variable_list.DATA[self.data_types.PAYMENTS], self.variable_list.SELECTED_ORDER_ID, "order_id").forEach(data => {
                            total += parseFloat(data.price);
                            element += `
                                <div class="col-12 mt-1 mb-1" index="${index}">
                                    <div class="row">
                                        <input type="hidden" name="payments[${index}][id]" value="${data.id}">
                                        <input type="hidden" name="payments[${index}][old_type]" value="${data.type}">
                                        <div class="col-md-6">
                                            <select class="form-input" name="payments[${index}][new_type]">${helper.get_select_options(main.data_list.PAYMENT_TYPES, "id", "name", data.type)}</select>
                                        </div>
                                        <div class="col-md-6">
                                            <input class="form-input" type="number" min="0" max="999999999" step="0.01" name="payments[${index}][price]" value="${data.price}" required>
                                        </div>
                                    </div>
                                </div>
                            `;
                            index++;
                        });

                        return element;
                    }

                    $(self.class_list.INVOICE_EDIT_ITEMS).html(create_element());
                    $(`${self.id_list.FORM_INVOICE_EDIT} input[name="total"]`).val(total);
                    $(`${self.id_list.FORM_INVOICE_EDIT} input[name="total_missing"]`).closest(".col-12").hide();
                });

                $(document).on("change", `${self.class_list.INVOICE_EDIT_ITEMS} select`, function () {
                    let element = $(this);

                    let index = element.closest("[index]").attr("index");
                    let type = element.val();
                    console.log(index, type);
                    if(type == 9){
                        $(`
                            ${self.class_list.INVOICE_EDIT_ITEMS} [index] select,
                            ${self.class_list.INVOICE_EDIT_ITEMS} [index] input
                        `).prop("disabled", true);
                        $(`
                            ${self.class_list.INVOICE_EDIT_ITEMS} [index="${index}"] select,
                            ${self.class_list.INVOICE_EDIT_ITEMS} [index="${index}"] input[type="hidden"]
                        `).prop("disabled", false);
                    }else{
                        $(`
                            ${self.class_list.INVOICE_EDIT_ITEMS} [index] select,
                            ${self.class_list.INVOICE_EDIT_ITEMS} [index] input
                        `).prop("disabled", false);
                    }
                });

                $(document).on("keyup change", `${self.class_list.INVOICE_EDIT_ITEMS} input[type="number"]`, function () {
                    let elements = Array.from($(`${self.class_list.INVOICE_EDIT_ITEMS} input[type="number"]`));

                    let total = $(`${self.id_list.FORM_INVOICE_EDIT} input[name="total"]`).val();
                    let total_elements = 0;
                    elements.forEach(element => {
                        let price = parseFloat($(element).val());
                        price = (isNaN(price)) ? 0 : price;
                        total_elements += price;
                    });

                    let missing = total - total_elements;
                    let input_missing = $(`${self.id_list.FORM_INVOICE_EDIT} input[name="total_missing"]`);
                    if(missing !== 0){
                        input_missing.val(missing);
                        input_missing.closest(".col-12").show();
                    }else{
                        input_missing.closest(".col-12").hide();
                    }
                });

                $(self.id_list.FORM_INVOICE_EDIT).submit(function (e) {
                    e.preventDefault();
                    console.log($(this).serializeObject());
                    set(
                        set_types.INVOICE_EDIT,
                        Object.assign($(this).serializeObject(), {"safe_id": safe.variable_list.SELECTED_SAFE_ID, "order_id": self.variable_list.SELECTED_ORDER_ID}),
                        function (data) {
                            console.log(data);
                            data = JSON.parse(data);
                            if (data.status){
                                helper_sweet_alert.success(language.data.CHANGE_RECEIPT, language.data.PROCESS_SUCCESS);
                                self.variable_list.DATA = Array();
                                self.get();
                                safe.variable_list.DATA = Array();
                                $(self.id_list.MODAL_INVOICE_EDIT).modal("hide");
                            }else{
                                helper_sweet_alert.error(language.data.CHANGE_RECEIPT, language.data.PLEASE_CHECK_PRICES);
                            }
                        }
                    )
                })
            }

            set_events();
        }
    }

    let trust = {
        class_list: {
            ACCOUNTS: ".e_trust_accounts",
            SEARCH_TRUST_ACCOUNT: ".e_search_trust_account",
            ACCOUNT_BTN: ".e_trust_account_btn",
            ACCOUNT_PAYMENTS: ".e_trust_account_info_payments",
            ACCOUNT_PAYMENT_TYPES_CUSTOM: ".e_modal_trust_account_payment_types",
            BUTTON_ACCOUNT_PAYMENT: ".e_trust_payment_btn"
        },
        id_list: {
            TRUST: "#trust",
            FORM: "#form_trust_account",
            MODAL: "#modal_new_trust_account",
            MODAL_INFO: "#modal_trust_account_info",
            MODAL_INFO_TITLE: "#modal_trust_account_info_title",
            MODAL_PAYMENT: "#modal_trust_account_payment",
            FORM_PAYMENT: "#modal_trust_account_payment_form"
        },
        variable_list: {
            SELECTED_ACCOUNT_ID: 0,
            ACCOUNTS: Array()
        },
        function_types:{
            account: {
                INSERT: 0x0001,
                DELETE: 0x0002
            },
            payment: {
                DELETE: 0x0002
            }
        },
        search_types: {
            ID: 1,
            NAME: 2
        },
        data_types: {
            TRUST_ACCOUNTS: "trust_accounts",
            OLD_DATA: "old_data",
            PAYMENTS: "payments"
        },
        get: function (search_type = 0, search = "") {
            let self = this;

            function create_element(){
                let element = ``;

                self.variable_list.ACCOUNTS.forEach(account => {
                    if(search.length > 0) {
                        let search_key = (search_type === self.search_types.ID) ? "id" : "name";
                        if(!String(account[search_key]).match(new RegExp(search, "gi"))) {
                            return;
                        }
                    }

                    let total_price_color = (account.total < 0) ? "text-danger" : "text-success";

                    element += `
                        <tr account-id="${account.id}">
                            <td>${account.id}</td>
                            <td>${account.name}</td>
                            <td>${account.discount}</td>
                            <td><b class="${total_price_color}">${account.total.toFixed(2)}</b>${main.data_list.CURRENCY}</td>
                            <td class="text-center">
                                <button function="info" class="e_trust_account_btn btn btn-primary"><i class="fa fa-eye"></i></button>
                            </td>
                            <td class="text-center">
                                <button function="edit" class="e_trust_account_btn btn btn-warning"><i class="fa fa-pencil-alt"></i></button>
                            </td>
                            <td class="text-center">
                                <button function="delete" class="e_trust_account_btn btn btn-danger"><i class="fa fa-trash-alt"></i></button>
                            </td>
                        </tr>
                    `;
                });

                return element;
            }

            if(self.variable_list.ACCOUNTS.length < 1) {
                get(
                    get_types.TRUST,
                    {},
                    function (data) {
                        data = JSON.parse(data);
                        console.log(data);
                        if (data.status) {
                            if (data.custom_data[self.data_types.TRUST_ACCOUNTS].length > 0) {
                                self.variable_list.ACCOUNTS = data.custom_data[self.data_types.TRUST_ACCOUNTS];

                                if (data.custom_data[self.data_types.OLD_DATA].length > 0) {
                                    data.custom_data[self.data_types.OLD_DATA].forEach(old_data => {
                                        let index = array_list.index_of(self.variable_list.ACCOUNTS, old_data.trust_account_id, "id");
                                        if(index > -1){
                                            self.variable_list.ACCOUNTS[index].total += parseFloat(old_data.total);
                                        }
                                    });
                                }
                            }
                        }
                    }
                );
            }

            $(self.class_list.ACCOUNTS).html(create_element());
        },
        get_payment_types: function () {
            let self = this;

            function create_element(){
                let element = ``;

                main.data_list.BRANCH_PAYMENT_TYPES.forEach(branch_payment_type => {
                    let payment_type = array_list.find(main.data_list.PAYMENT_TYPES, branch_payment_type.type_id, "id");
                    element += `
                        <button type="submit" class="col-4 btn btn-dark" type-id="${payment_type.id}">${payment_type.name}</button>
                    `;
                });

                return element;
            }

            $(self.class_list.ACCOUNT_PAYMENT_TYPES_CUSTOM).html(create_element());
        },
        get_account_payments: function () {
            let self = this;

            let data_payments = Array();

            function create_element() {
                let element = ``;
                console.log(data_payments);
                let total_price = 0;
                data_payments.forEach(account_payment => {
                    let payment_type = array_list.find(main.data_list.PAYMENT_TYPES, account_payment.type, "id");
                    let isPayment = account_payment.order_id == 0;
                    let price = parseFloat(account_payment.price);
                    price = ((account_payment.order_id > 0) ? -1 : 1) * price;
                    total_price += price;
                    let btn_delete = (account_payment.order_id == 0)
                        ? `
                            <button function="delete" class="e_trust_payment_btn btn btn-s3">
                                <i class="fa fa-trash-alt"></i> Sil
                            </button>
                        `
                        : "";
                    let price_color = (price < 0) ? "text-danger" : "text-success";
                    let total_price_color = (total_price < 0) ? "text-danger" : "text-success";
                    element += `
                        <tr trust-payment-id="${account_payment.id}" payment-id="${account_payment.payment_id}" safe-id="${account_payment.safe_id}">
                            <td>${isPayment ? `(${payment_type.name})` : ""} ${account_payment.comment}</td>
                            <td>${account_payment.date}</td>
                            <td>${!isPayment ? account_payment.discount : ""}</td>
                            <td><b class="${price_color}">${price.toFixed(2)}</b>${main.data_list.CURRENCY}</td>
                            <td><b class="${total_price_color}">${total_price.toFixed(2)}</b>${main.data_list.CURRENCY}</td>
                            <td>
                                ${btn_delete}
                            </td>
                        </tr>
                    `;
                });

                return element;
            }

            get(
                get_types.TRUST_PAYMENTS,
                {"account_id": self.variable_list.SELECTED_ACCOUNT_ID},
                function (data) {
                    data = JSON.parse(data);
                    console.log(data);
                    if(data.status){
                        if (data.custom_data[self.data_types.PAYMENTS].length > 0) {
                            data_payments = data.custom_data[self.data_types.PAYMENTS];
                        }

                        if (data.custom_data[self.data_types.OLD_DATA].length > 0) {
                            data_payments = data.custom_data[self.data_types.OLD_DATA];
                            data_payments = data_payments.concat(data.custom_data[self.data_types.PAYMENTS]);
                        }
                    }
                }
            );

            $(self.class_list.ACCOUNT_PAYMENTS).html(create_element());
        },
        initialize: function (){
            let self = this;

            function set_events(){
                $(self.id_list.FORM).submit(function (e) {
                    e.preventDefault();
                    set(
                        set_types.TRUST_ACCOUNT_INSERT,
                        Object.assign($(this).serializeObject(), {
                            "id": self.variable_list.SELECTED_ACCOUNT_ID,
                            "function_type": self.function_types.account.INSERT
                        }),
                        function (data) {
                            data = JSON.parse(data);
                            if(data.status){
                                self.variable_list.ACCOUNTS = Array();
                                self.get();
                                $(self.id_list.MODAL).modal("hide");
                                $(this).trigger("reset");
                                helper_sweet_alert.success(language.data.PROCESS_SUCCESS_TITLE, language.data.PROCESS_SUCCESS);
                            }
                        }
                    );
                });

                $(self.class_list.SEARCH_TRUST_ACCOUNT).on("keyup change", function () {
                    let function_name = $(this).attr("function");

                    let search_type = (function_name === "id") ? self.search_types.ID : self.search_types.NAME;

                    self.get(search_type, $(this).val());
                });

                $(document).on("click", self.class_list.ACCOUNT_BTN, function () {
                    let element = $(this);

                    let function_name = element.attr("function");
                    self.variable_list.SELECTED_ACCOUNT_ID = parseInt(element.closest("[account-id]").attr("account-id"));


                    switch (function_name) {
                        case "edit":
                            $(self.id_list.FORM).autofill(array_list.find(self.variable_list.ACCOUNTS, self.variable_list.SELECTED_ACCOUNT_ID, "id"));
                            $(self.id_list.MODAL).modal();
                            break;
                        case "info":
                            $(self.id_list.MODAL_INFO).modal();
                            break;
                        case "delete":
                            Swal.fire({
                                icon: "question",
                                title: language.data.DELETE_PROCESS_TITLE,
                                html: `<b>'${array_list.find(self.variable_list.ACCOUNTS, self.variable_list.SELECTED_ACCOUNT_ID, "id").name}'</b> ${language.data.DELETE_CREDIT_AMOUNT_HTML}`,
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
                                    set(
                                        set_types.TRUST_ACCOUNT_INSERT,
                                        {
                                            "id": self.variable_list.SELECTED_ACCOUNT_ID,
                                            "function_type": self.function_types.account.DELETE
                                        },
                                        function (data) {
                                            data = JSON.parse(data);
                                            if(data.status){
                                                let index = array_list.index_of(self.variable_list.ACCOUNTS, self.variable_list.SELECTED_ACCOUNT_ID, "id");
                                                delete self.variable_list.ACCOUNTS[index];
                                                self.get();
                                                $(self.id_list.MODAL).modal("hide");
                                                helper_sweet_alert.success(language.data.PROCESS_SUCCESS_TITLE, language.data.PROCESS_SUCCESS);
                                                self.variable_list.SELECTED_ACCOUNT_ID = 0;
                                            }
                                        }
                                    );
                                }
                            });
                            break;
                        case "add":
                            self.variable_list.SELECTED_ACCOUNT_ID = 0;
                            $(self.id_list.MODAL).modal();
                            break;
                    }
                });

                $(self.id_list.MODAL_INFO).on("show.bs.modal", function () {
                    let account_info = array_list.find(self.variable_list.ACCOUNTS, self.variable_list.SELECTED_ACCOUNT_ID, "id");
                    $(`${self.id_list.MODAL_INFO_TITLE} [function='name']`).html(account_info.name);
                    self.get_account_payments();
                });

                $(self.id_list.MODAL_PAYMENT).on("show.bs.modal", function () {
                    self.get_payment_types();
                });

                $(self.id_list.FORM_PAYMENT).submit(function (e) {
                    e.preventDefault();
                    let id = parseInt($(`${self.id_list.FORM_PAYMENT} button[type=submit]:focus`).attr("type-id"));
                    let form_data = $(this).serializeObject();

                    function get_data() {
                        let data = {
                            "orders": Array(),
                            "table_id": 0
                        };

                        data.orders.push({
                            "id": 0,
                            "price": form_data.price,
                            "comment": form_data.comment
                        });

                        return data;
                    }

                    let data = get_data();
                    data = Object.assign(data, {
                        "order_type": 1,
                        "payment_type": id,
                        "trust_account_id": self.variable_list.SELECTED_ACCOUNT_ID,
                        "set_type": 0x0008
                    });
                    helper_sweet_alert.wait(language.data.PROCESS_PROGRESS_TITLE, language.data.PROCESS_WAIT_CONTENT);
                    $.ajax({
                        url: `${default_ajax_path_pos_orders}set.php`,
                        type: "POST",
                        data: data,
                        success: function (data) {
                            console.log(data);
                            helper_sweet_alert.success(language.data.PROCESS_SUCCESS_TITLE, language.data.UPDATED_CREDIT_AMOUNT_HTML);
                            main.get_payments_related_things(main.get_type_for_payments_related_things.PAYMENTS);
                            self.variable_list.ACCOUNTS = [];
                            self.get();
                            self.get_account_payments();
                            safe.get();
                            $(self.id_list.FORM_PAYMENT).trigger("reset");
                            $(self.id_list.MODAL_PAYMENT).modal("hide");
                        },error: helper_sweet_alert.close(), timeout: settings.ajax_timeouts.NORMAL
                    });
                });

                $(document).on("click", self.class_list.BUTTON_ACCOUNT_PAYMENT, function () {
                    let element = $(this);
                    let function_name = element.attr("function");
                    let find_element = element.closest("tr");

                    let id = parseInt(find_element.attr("trust-payment-id"));
                    let payment_id = parseInt(find_element.attr("payment-id"));
                    let safe_id = parseInt(find_element.attr("safe-id"));

                    switch (function_name){
                        case "delete":
                            Swal.fire({
                                icon: "question",
                                title: language.data.DELETE_PROCESS_TITLE,
                                html: language.data.DELETE_CREDIT_ACCOUNT_PRICE_HTML,
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
                                    set(
                                        set_types.TRUST_ACCOUNT_PAYMENT,
                                        {
                                            "account_id": self.variable_list.SELECTED_ACCOUNT_ID,
                                            "id": id,
                                            "payment_id": payment_id,
                                            "safe_id": safe_id,
                                            "function_type": self.function_types.payment.DELETE
                                        },
                                        function (data) {
                                            data = JSON.parse(data);
                                            if(data.status){
                                                find_element.remove();
                                                helper_sweet_alert.success(language.data.PROCESS_SUCCESS_TITLE,language.data.PROCESS_SUCCESS);
                                                setTimeout(function () {
                                                    location.reload();
                                                }, 1500)
                                            }
                                        }
                                    );
                                }
                            });
                            break;
                    }
                });
            }

            set_events();
        }
    }

    let cost = {
        class_list: {
            COSTS: ".e_costs",
            COST_BTN: ".e_cost_btn"
        },
        id_list: {
            COST: "#cost",
            FORM: "#modal_cost_form",
            MODAL: "#modal_cost",
        },
        variable_list: {
            DATA: Array()
        },
        function_types: {
            INSERT: 1,
            DELETE: 2
        },
        get: function () {
            let self = this;

            function create_element(){
                let element = ``;

                self.variable_list.DATA.forEach(cost => {
                    let bg = (cost.safe_id == safe.variable_list.SELECTED_SAFE_ID) ? "green" : "transparent";
                    let users = array_list.find_multi(main.data_list.BRANCH_USERS, cost.account_type, "account_type");
                    let user_name = array_list.find(users, cost.account_id, "id");
                    user_name = (typeof user_name === "undefined") ? language.data.DELETED_ACCOUNT : user_name.name;
                    console.log(users);
                    element += `
                        <tr style="background: ${bg};" payment-id="${cost.id}" safe-id="${cost.safe_id}">
                            <td>${cost.date}</td>
                            <td user_id="${cost.account_id}">${user_name}</td>
                            <td>${cost.comment}</td>
                            <td>${cost.price}</td>
                            <td><button function="delete" class="e_cost_btn btn btn-s3"><i class="fa fa-trash"></i></button></td>
                        </tr>
                    `;
                });

                return element;
            }

            if(self.variable_list.DATA.length < 1){
                get(
                    get_types.COST,
                    {},
                    function (data) {
                        data = JSON.parse(data);
                        console.log(data);
                        self.variable_list.DATA = data.rows.new.concat(data.rows.old);
                    }
                );
            }

            $(self.class_list.COSTS).html(create_element());
        },
        initialize: function (){
            let self = this;

            function set_events(){
                $(self.id_list.FORM).submit(function (e) {
                    e.preventDefault();
                    set(
                        set_types.COST,
                        Object.assign($(this).serializeObject(), {"function_type": self.function_types.INSERT}),
                        function (data) {
                            data = JSON.parse(data);
                            if(data.status){
                                self.variable_list.DATA = Array();
                                self.get();
                                $(self.id_list.MODAL).modal("hide");
                                helper_sweet_alert.success(language.data.PROCESS_SUCCESS_TITLE, language.data.PROCESS_SUCCESS);
                            }
                        }
                    );
                });

                $(document).on("click", self.class_list.COST_BTN, function () {
                    let element = $(this);
                    let function_name = element.attr("function");

                    switch(function_name){
                        case "add":
                            $(self.id_list.FORM).trigger("reset");
                            $(self.id_list.MODAL).modal("show");
                            break;
                        case "delete":
                            let find_element = element.closest("tr");
                            let id = parseInt(find_element.attr("payment-id"));
                            let safe_id = parseInt(find_element.attr("safe-id"));
                            Swal.fire({
                                icon: "question",
                                title: language.data.DELETE_PROCESS_TITLE,
                                html: language.data.DELETE_CHARGE_TEXT,
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
                                    set(
                                        set_types.COST,
                                        {
                                            "id": id,
                                            "safe_id": safe_id,
                                            "function_type": self.function_types.DELETE
                                        },
                                        function (data) {
                                            data = JSON.parse(data);
                                            if(data.status){
                                                let index = array_list.index_of(self.variable_list.DATA, id, "id");
                                                self.variable_list.DATA.splice(index, 1);
                                                self.get();
                                                $(self.id_list.MODAL).modal("hide");
                                                helper_sweet_alert.success(language.data.PROCESS_SUCCESS_TITLE, language.data.PROCESS_SUCCESS);
                                            }
                                        }
                                    );
                                }
                            });
                            break;
                    }
                });
            }

            set_events();
        }
    }

    function set (set_type, data, success_function){
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

    function get(get_type, data, success_function, async = false){
        data["get_type"] = get_type;
        $.ajax({
            url: `${default_ajax_path}get.php`,
            type: "POST",
            data: data,
            async: async,
            success: function (data) {
                console.log(data);
                success_function(data);
            }, timeout: settings.ajax_timeouts.NORMAL
        });
    }

    return report_safe;
})();

$(function () {
    let _report_safe = new report_safe();
});
