let finance = (function () {
    let default_ajax_path = `${settings.paths.primary.PHP}finance/`;
    let default_ajax_path_orders = `${settings.paths.primary.PHP}orders/`;
    let default_ajax_path_manage_report_safe = `../manage/${settings.paths.primary.PHP}report_safe/`;
    let set_types = {
        TRUST_ACCOUNT_INSERT: 0x0001,
        CLOSE_SAFE: 0x0002,
        COST: 0x0003
    };
    let get_types = {
        COST: 0x0001,
        Z_REPORT: 0x0002
    };
    let id_list = {
        CLOSE_SAFE_COMMENT: "#close_safe_comment"
    }
    let class_list = {
        NAVIGATION_BUTTON: ".e_navigation_btn",
        SAFE_CLOSE: ".e_safe_close"
    }

    function finance(){ initialize(); }
    function initialize(){

        function set_events() {
            $(class_list.NAVIGATION_BUTTON).on("click", function () {
                let function_name = $(this).attr("function");
                $(`${safe.id_list.SAFE},${invoices.id_list.INVOICE},${trust.id_list.TRUST},${cost.id_list.COST}`).hide();
                switch (function_name) {
                    case "safe": safe.get(); $(safe.id_list.SAFE).show(); break;
                    case "invoice": invoices.get(); $(invoices.id_list.INVOICE).show(); break;
                    case "trust": trust.get(); $(trust.id_list.TRUST).show(); break;
                    case "cost": cost.get(); $(cost.id_list.COST).show(); break;
                }
            });
            $(class_list.SAFE_CLOSE).on("click", function () {
                Swal.fire({
                    icon: "question",
                    title: language.data.SAFE_CLOSE_PROCESS,
                    html: `
                    <label for="close_safe_comment">${language.data.DESCRIPTION}</label>
                    <input id="close_safe_comment" placeholder="${language.data.DESCRIPTION}" class="form-input" type="text" />
                    ${language.data.SAFE_CLOSE_QUESTION}
                    `,
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    showCancelButton: true,
                    confirmButtonText: language.data.ACCEPT,
                    cancelButtonText: language.data.DECLINE,
                    confirmButtonClass: 'btn btn-danger btn-lg mr-3 mt-5',
                    cancelButtonClass: 'btn btn-primary btn-lg ml-3 mt-5',
                    buttonsStyling: false
                }).then((result) => {

                    if (result.value) {
                        console.log("print z report")
                        z_report.get();
                        console.log("close safe");
                        set(
                            set_types.CLOSE_SAFE,
                            {
                                "comment": $(id_list.CLOSE_SAFE_COMMENT).val()
                            },
                            function (data) {
                                data = JSON.parse(data);
                                console.log(data);
                                if(data.status){
                                    helper_sweet_alert.success(language.data.PROCESS_SUCCESS_TITLE, language.data.SAFE_CLOSE_TEXT);
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
        invoices.initialize();
        trust.initialize();
        z_report.initialize();
        cost.initialize();
    }

    let safe = {
        class_list: {
            PAYMENTS: ".e_payments",
            PAYMENTS_OTHER: ".e_payments_other",
            PAYMENTS_TOTAL: ".e_payments_total",
            PAYMENTS_TRUST: ".e_payments_trust",
        },
        id_list: {
            SAFE: "#safe"
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

                main.data_list.PAYMENTS.forEach(payment => {
                    try {
                        let price = parseFloat(payment.price);
                        let payment_find = array_list.find(main.data_list.PAYMENT_TYPES, payment.type, "id");

                        // Payments Other
                        if (payment.status !== helper.db.order_payment_status_types.PAID) {
                            let order_products_find = array_list.find_multi(main.data_list.ORDER_PRODUCTS, payment.order_id, "order_id");
                            console.log(order_products_find);

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
                                    break;
                            }

                            if(order_id_saved[column_payment_other].includes(payment.order_id)) return;
                            order_id_saved[column_payment_other].push(payment.order_id);
                            order_products_find.forEach(order_product => {
                                if(order_product.status !== helper.db.order_product_status_types.ACTIVE) {
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
                main.data_list.ORDERS.forEach(order => {
                    if(order.date_end === null || order.date_end === ""){
                        let products = array_list.find_multi(main.data_list.ORDER_PRODUCTS, order.id, "order_id");
                        products.forEach(product => {
                            if(product.status !== helper.db.order_product_status_types.ACTIVE) return;
                            data.payments_total.open_tables += parseFloat(product.price);
                        });
                    }
                });
                main.data_list.ORDER_PRODUCTS.forEach(order_product => {
                    if(order_product.type === helper.db.order_product_types.DISCOUNT && order_product.status !== helper.db.order_product_status_types.CANCEL)
                        data.payments_other.discount += parseFloat(order_product.price);
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
            let elements = create_element();
            $(self.class_list.PAYMENTS).html(elements.payments);
            $(self.class_list.PAYMENTS_OTHER).html(elements.payments_other);
            $(self.class_list.PAYMENTS_TOTAL).html(elements.payments_total);
            $(self.class_list.PAYMENTS_TRUST).html(elements.payments_trust);
        },
        initialize: function (){
            let self = this;
            function set_events(){
            }
            set_events();
            self.get();
        }
    }
    let invoices = {
        class_list: {
            INVOICE_SHOW_ELEMENTS: ".e_invoice_show_elements",
            INVOICES: ".e_invoices",
        },
        id_list: {
            INVOICE: "#invoice",
            MODAL_INVOICE_SHOW: "#modal_invoice_show",
        },
        get: function () {
            let self = this;
            function create_element() {
                let element = ``;
                let data = Array();

                main.data_list.PAYMENTS.forEach(payment => {
                    if(payment.type === 0 || payment.order_id === 0) return;
                    let order = array_list.find(main.data_list.ORDERS, payment.order_id, "id");
                    if(typeof order === "undefined") return;
                    if(typeof data[order.id] === "undefined")
                        data[order.id] = {
                            "id": order.id,
                            "no": order.no,
                            "table_id": order.table_id,
                            "payments": Array()
                        };
                    if(typeof data[order.id].payments[payment.type] === "undefined")
                        data[order.id].payments[payment.type] = {
                            "type": payment.type,
                            "price": 0,
                            "date": "",
                            "account_type": 0,
                            "account_name": "",
                        };

                    data[order.id].payments[payment.type].price += parseFloat(payment.price);
                    data[order.id].payments[payment.type].date = payment.date;
                    data[order.id].payments[payment.type].account_type = payment.account_type;
                    data[order.id].payments[payment.type].account_name = payment.account_name;
                });

                console.log(data);
                data.forEach(order => {
                    if(order.table_id === 0) return;
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
                            <tr order-id='${order.id}' order-no="${order.no}" order-section="${table_section.name} ${table.no}" payment="${payment_element}" user="${data_payment.account_name}">
                                <td>${order.no}</td>
                                <td>(${data_payment.account_type}) ${data_payment.account_name}</td>
                                <td>${table_section.name} ${table.no}</td>
                                <td>${data_payment.date}</td>
                                <td>${total.toFixed(2) + main.data_list.CURRENCY}</td>
                                <td>${payment_element}</td>
                                <td class="p-1"><button function="view" class="btn btn-sm m-0 btn-s1 w-100"><i class="fas fa-eye"></i></button></td>
                                <td class="p-1"><button function="print" class="btn btn-sm m-0 btn-s1 w-100"><i class="fas fa-print"></i></button></td>
                            </tr>
                        `;
                });
                return element;
            }
            $(self.class_list.INVOICES).html(create_element());
        },
        initialize: function (){
            let self = this;

            function set_events(){
                $(document).on("click",`${self.class_list.INVOICES} button[function]`,function (){
                    let element = $(this).closest("tr");
                    let order_id = parseInt(element.attr("order-id"));
                   // let order_no = element.attr("order-no");
                   // let payment = element.attr("payment");
                    let user = element.attr("payment");
                    let order_section = element.attr("order-id");
                    let function_type = $(this).attr("function")
                    switch(function_type){
                        case "print": invoice.payyed_payment_receipt(order_id); break;
                        case "view":
                            invoice.return_html = true;
                            $.ajax({
                                    url: "../public/assets/printer/invoice.php",
                                    type: "POST",
                                    data: {elements: invoice.payyed_payment_receipt(order_id,true,order_section,user)},
                                    async: false,
                                    success: function (data) {
                                        $(`${self.class_list.INVOICE_SHOW_ELEMENTS} iframe`).attr("srcdoc", data);
                                        $(self.id_list.MODAL_INVOICE_SHOW).modal("show");
                                    }
                            });
                        break;
                    }
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
            ACCOUNT_PAYMENT_TYPES_CUSTOM: ".e_modal_trust_account_payment_types"
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
            trust_account_insert: {
                INSERT: 0x0001,
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

                    element += `
                        <tr account-id="${account.id}">
                            <td>${account.id}</td>
                            <td>${account.name}</td>
                            <td>${account.discount}</td>
                            <td>${account.total.toFixed(2) + main.data_list.CURRENCY}</td>
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
                get_manage(
                    0x0004,
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
        /*get: function (search_type = 0, search = "") {
            let self = this;
            function create_element(){
                let element = ``;

                main.data_list.TRUST_ACCOUNTS.forEach(account => {
                    if(search.length > 0) {
                        let search_key = (search_type === self.search_types.ID) ? "id" : "name";
                        if(!String(account[search_key]).match(new RegExp(search, "gi"))) {
                            return;
                        }
                    }

                    let price = 0;
                    let account_payments = array_list.find_multi(main.data_list.TRUST_ACCOUNT_PAYMENTS, account.id, "trust_account_id");
                    account_payments.forEach(account_payment => {
                        console.log(account_payment);
                        let payment = array_list.find(main.data_list.PAYMENTS, account_payment.payment_id, "id");
                        console.log(payment)
                        if(typeof payment === "undefined") return;
                        price += ((payment.order_id > 0) ? -1 : 1) * parseFloat(payment.price);
                    });

                    element += `
                        <tr account-id="${account.id}">
                            <td>${account.id}</td>
                            <td>${account.name}</td>
                            <td>${account.discount}</td>
                            <td>${price.toFixed(2) + main.data_list.CURRENCY}</td>
                            <td class="text-center">
                                <button function="info" class="e_trust_account_btn btn btn-primary"><i class="fa fa-eye"></i></button>
                            </td>
                            <td class="text-center">
                                <button function="edit" class="e_trust_account_btn btn btn-warning"><i class="fa fa-pencil-alt"></i></button>
                            </td>
                        </tr>
                    `;
                });

                return element;
            }
            $(self.class_list.ACCOUNTS).html(create_element());
        },*/
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
                    let price = parseFloat(account_payment.price);
                    price = ((account_payment.order_id > 0) ? -1 : 1) * price;
                    total_price += price;
                    element += `
                        <tr trust-payment-id="${account_payment.id}" payment-id="${account_payment.payment_id}" safe-id="${account_payment.safe_id}">
                            <td>${account_payment.comment}</td>
                            <td>${account_payment.date}</td>
                            <td>${account_payment.discount}</td>
                            <td>${price.toFixed(2) + main.data_list.CURRENCY}</td>
                            <td>${total_price.toFixed(2) + main.data_list.CURRENCY}</td>
                        </tr>
                    `;
                });

                return element;
            }

            get_manage(
                0x0005,
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
                            "function_type": self.function_types.trust_account_insert.INSERT
                        }),
                        function (data) {
                            data = JSON.parse(data);
                            if(data.status){
                                main.get_trust_accounts_related_things(main.get_type_for_branch_trust_accounts_related_things.ACCOUNTS);
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
                            $(self.id_list.FORM).autofill(array_list.find(main.data_list.TRUST_ACCOUNTS, self.variable_list.SELECTED_ACCOUNT_ID, "id"));
                            $(self.id_list.MODAL).modal();
                            break;
                        case "info":
                            $(self.id_list.MODAL_INFO).modal();
                            break;
                        case "delete":
                            Swal.fire({
                                icon: "question",
                                title: language.data.DELETE_PROCESS_TITLE,
                                html: `<b>'${array_list.find(main.data_list.TRUST_ACCOUNTS, self.variable_list.SELECTED_ACCOUNT_ID, "id").name}'</b> isimli veresiye hesabını silmek istediğinizden emin misiniz?`,
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
                                            "function_type": self.function_types.trust_account_insert.DELETE
                                        },
                                        function (data) {
                                            data = JSON.parse(data);
                                            if(data.status){
                                                main.get_trust_accounts_related_things(main.get_type_for_branch_trust_accounts_related_things.ACCOUNTS);
                                                self.get();
                                                $(self.id_list.MODAL).modal("hide");
                                                $(this).trigger("reset");
                                                helper_sweet_alert.success(language.data.PROCESS_SUCCESS_TITLE, language.data.PROCESS_SUCCESS);
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
                    let account_info = array_list.find(main.data_list.TRUST_ACCOUNTS, self.variable_list.SELECTED_ACCOUNT_ID, "id");
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
                    console.log(data);
                    helper_sweet_alert.wait(language.data.PROCESS_PROGRESS_TITLE, language.data.PROCESS_WAIT_CONTENT);
                    $.ajax({
                        url: `${default_ajax_path_orders}set.php`,
                        type: "POST",
                        data: data,
                        success: function (data) {
                            console.log(data);
                            helper_sweet_alert.success(language.data.PROCESS_SUCCESS_TITLE, language.data.PAID_SUCCESS_TEXT);
                            main.get_payments_related_things(main.get_type_for_payments_related_things.PAYMENTS);
                            main.get_trust_accounts_related_things(main.get_type_for_branch_trust_accounts_related_things.PAYMENTS);
                            self.get();
                            self.get_account_payments();
                            safe.get();
                        }, timeout: settings.ajax_timeouts.NORMAL
                    });
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

                let costs = array_list.find_multi(
                    main.data_list.PAYMENTS,
                    helper.db.order_payment_status_types.COST,
                    "status"
                );

                console.log(costs);
                costs.forEach(cost => {
                    element += `
                        <tr payment-id="${cost.id}">
                            <td>${cost.date}</td>
                            <td>${cost.account_name}</td>
                            <td>${cost.comment}</td>
                            <td>${cost.price}</td>
                            <td>
                                <button function="delete" class="e_cost_btn btn btn-s3"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    `;
                });

                return element;
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
                                main.get_payments_related_things(main.get_type_for_payments_related_things.PAYMENTS);
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
                                                main.get_payments_related_things(main.get_type_for_payments_related_things.PAYMENTS);
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
    let z_report = {
        class_list: {
            PRINT_Z_REPORT_BTN: ".e_print_z_report",
        },
        get: function (){
            let self = this;
                get(
                    get_types.Z_REPORT,
                    {},
                    function (data){
                        data = JSON.parse(data)
                        console.log(data);
                        convert_invoce_data(data.rows)
                    }
                );

                function convert_invoce_data(data){
                    data.products.forEach(function (product) {
                        product.options = array_list.find_multi(data.product_options,product.product_id,"product_id")
                    });
                    data.cancel_products.forEach(function (product) {
                        product.options = array_list.find_multi(data.product_options,product.product_id,"product_id")
                    });
                    invoice.z_report(data);

                }
        },
        initialize: function (){
            let self = this;
            function set_events(){
                $(document).on("click",self.class_list.PRINT_Z_REPORT_BTN,function (){
                    self.get();
                })
            }
            set_events();
        }
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
    function set (set_type, data, success_function,async= false){
        helper_sweet_alert.wait(language.data.PROCESS_PROGRESS_TITLE, language.data.PROCESS_WAIT_CONTENT);
        data["set_type"] = set_type;
        $.ajax({
            url: `${default_ajax_path}set.php`,
            type: "POST",
            data: data,
            async: async,
            success: function (data) {
                console.log(data);
                success_function(data);
            }, timeout: settings.ajax_timeouts.NORMAL
        });
    }
    function get_manage(get_type, data, success_function){
        data["get_type"] = get_type;
        $.ajax({
            url: `${default_ajax_path_manage_report_safe}get.php`,
            type: "POST",
            data: data,
            async: false,
            success: function (data) {
                console.log(data);
                success_function(data);
            }, timeout: settings.ajax_timeouts.NORMAL
        });
    }


    return finance;
})();

$(function () {
    let _finance = new finance();
});
