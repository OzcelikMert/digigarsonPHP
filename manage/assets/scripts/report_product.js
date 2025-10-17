let report_product = (function () {
    let default_ajax_path = `${settings.paths.primary.PHP}report_product/`;
    let get_types = {
        REPORT: 0x0001
    }
    function report_product(){ initialize(); }

    function initialize(){
        report_form.initialize();
        report_table.initialize();
    }

    let report_form = {
        id_list: {
            FORM: "#report_form"
        },
        class_list: {},
        initialize: function () {
            let self = this;

            function set_events(){
                $(self.id_list.FORM).submit(function (e) {
                    e.preventDefault();
                    let element = $(`${self.id_list.FORM} button:focus`);
                    report_table.variable_list.TYPE = parseInt(element.attr("value"));
                    get(
                        get_types.REPORT,
                        Object.assign($(this).serializeObject(), {
                            "type": report_table.variable_list.TYPE
                        }),
                        function (data) {
                            data = JSON.parse(data);
                            console.log(data);
                            report_table.variable_list.DATA = data.rows;
                            report_table.get();
                            $(report_table.class_list.TITLE).html(element.html());
                        }
                    );
                });
            }

            set_events();
            $(`${self.id_list.FORM} input[type='date']`).val(variable.date_format(new Date(), "yyyy-mm-dd"));
        }
    }

    let report_table = {
        id_list: {
            TABLES: "#report_tables",
            TABLE_PREFIX: "report_table_"
        },
        class_list: {
            RESULT: ".e_result_table",
            CONVERT: ".e_convert",
            BUTTON_CONVERT: ".e_convert_btn",
            TITLE: ".e_report_title",
            TABLE: ".e_report_table"
        },
        report_types:{
            TOTAL_PRICE: 1,
            PAYMENT_TYPES: 2,
            SALES_PRODUCT: 3,
            SALES_CUSTOMER: 4,
            SALES_WAITER: 5,
            SALES_TABLE: 6,
            SALES_PRODUCT_BY_TABLE: 7,
            RUSH_HOURS: 8,
            QUESTIONS_POINT: 9,
            QUESTIONS_TEXT: 10,
            ORDERS_CANCEL: 11,
            ORDERS_CATERING: 12,
            ORDERS_TAKE_AWAY: 13,
            SALES_PRODUCT_CATEGORY: 14,
            SALES_PRODUCT_WAITER: 15
        },
        variable_list: {
            DATA: Array(),
            TYPE: 0
        },
        get: function () {
            let self = this;
            let table_element = {
                "head": "",
                "body": ""
            };
            let elements = ``;
            let convert = false;

            function get_table_elements(head, body, table_id, sheet_name){
                return `
                    <div class="mt-2 report-result-div">
                        <table class='e_report_table table table-sticky table-striped table-bordered table-hover' id="${self.id_list.TABLE_PREFIX + table_id}" sheet-name="${sheet_name}">
                            <thead class='thead-dark'>
                                <tr>${head}</tr>
                            </thead>
                            <tbody>${body}</tbody>
                        </table>
                    </div>
                `;
            }

            function total_price(){
                table_element.head = `
                    <th><strong>${language.data.COMPANY_NAME}:</strong></th>
                    <th><strong>${language.data.TOTAL}</strong></th>
                `;

                let total = [];
                let total_all = 0;

                self.variable_list.DATA.old.forEach(data => {
                    if (typeof total[data.branch_id] == "undefined"){
                        total[data.branch_id] = 0;
                    }
                    total[data.branch_id] += data.total
                    total_all += data.total;
                });

                self.variable_list.DATA.new.forEach(data => {
                    if (typeof total[data.branch_id] == "undefined"){
                        total[data.branch_id] = 0;
                    }
                    total[data.branch_id] += data.total
                    total_all += data.total;
                });

                console.log(total)
                total.forEach(function (item,key){
                    let branch_name = array_list.find(main.data_list.BRANCHES,key,"id").name;
                    table_element.body = `
                        <tr>
                            <td>${branch_name}</td>
                            <td>${item.toFixed(2) + main.data_list.CURRENCY}</td>
                        </tr>
                    `;
                    elements += get_table_elements(table_element.head, table_element.body, key, branch_name);
                });

                if(main.data_list.BRANCH_INFO.is_main){
                    let branch_name = "Toplam";
                    table_element.body = `
                        <tr>
                            <td>${branch_name}</td>
                            <td>${total_all.toFixed(2) + main.data_list.CURRENCY}</td>
                        </tr>
                    `;
                    elements += get_table_elements(table_element.head, table_element.body, 0, branch_name);
                }
            }

            function payment_types(){
                table_element.head = `
                    <th><strong>${language.data.PAYMENT_METHOD}</strong></th>
                    <th><strong>${language.data.TOTAL}</strong></th>
                `;

                let data_element = Array();
                let total_all = Array();

                self.variable_list.DATA.old.forEach(data => {
                    let payment_type = (data.type == 0) ? {"name": "Masraf"} : array_list.find(self.variable_list.DATA.payment_types, data.type, "id");
                    if(typeof data_element[data.branch_id] === "undefined") data_element[data.branch_id] = [];
                    if(typeof data_element[data.branch_id][data.type] === "undefined") data_element[data.branch_id][data.type] = {"name": payment_type.name, "total": 0};
                    if(typeof total_all[data.type] === "undefined") total_all[data.type] = {"name": data_element[data.branch_id][data.type].name, "total": 0};

                    data_element[data.branch_id][data.type].total += parseFloat(data.total);
                    total_all[data.type].total += parseFloat(data.total);
                });

                self.variable_list.DATA.new.forEach(data => {
                    let payment_type = (data.type == 0) ? {"name": "Masraf"} : array_list.find(self.variable_list.DATA.payment_types, data.type, "id");
                    if(typeof data_element[data.branch_id] === "undefined") data_element[data.branch_id] = [];
                    if(typeof data_element[data.branch_id][data.type] === "undefined") data_element[data.branch_id][data.type] = {"name": payment_type.name, "total": 0};
                    if(typeof total_all[data.type] === "undefined") total_all[data.type] = {"name": data_element[data.branch_id][data.type].name, "total": 0};

                    data_element[data.branch_id][data.type].total += parseFloat(data.total);
                    total_all[data.type].total += parseFloat(data.total);
                });

                data_element.forEach(function (data,key) {
                    let branch_name = array_list.find(main.data_list.BRANCHES,key,"id").name;
                    table_element.body = `<tr style="background: #446"> <td colspan="2"><b>${branch_name.toUpperCase()}</b></td> </tr>`;

                    data.forEach(function (item){
                        table_element.body += `
                            <tr>
                                <td>${item.name}</td>
                                <td>${item.total.toFixed(2) + main.data_list.CURRENCY}</td>
                            </tr>
                        `;
                    })

                    elements += get_table_elements(table_element.head, table_element.body, key, branch_name);
                });

                if(main.data_list.BRANCH_INFO.is_main){
                    let branch_name = "Toplam";
                    table_element.body = `<tr style="background: #446"> <td colspan="2"><b>${branch_name}</b></td> </tr>`;
                    total_all.forEach(data => {
                        table_element.body += ` <tr><td>${data.name}</td><td>${data.total.toFixed(2) + main.data_list.CURRENCY}</td></tr>`;
                    })
                    elements += get_table_elements(table_element.head, table_element.body, 0, branch_name);
                }
            }

            function sales_product(){
                table_element.head = `
                    <th><strong>${language.data.PRODUCT_NAME}</strong></th>
                    <th><strong>${language.data.TOTAL}</strong></th>
                    <th><strong>${language.data.TOTAL_AMOUNT}</strong></th>
                `;
                let data_element = Array();
                let total_all = Array();

                self.variable_list.DATA.old.forEach(data => {
                    if(data.product_id == 0) return;
                    let product = array_list.find(self.variable_list.DATA.products, data.product_id, "id");
                    let quantity_type = array_list.find(main.data_list.PRODUCT_QUANTITY_TYPES, product.quantity_id, "id");
                    if(typeof data_element[data.branch_id] === "undefined") data_element[data.branch_id] = [];
                    if(typeof data_element[data.branch_id][data.product_id] === "undefined")
                        data_element[data.branch_id][data.product_id] = {
                            "name": product.name,
                            "quantity_name": quantity_type.name,
                            "quantity_id": product.quantity_id,
                            "qty": 0,
                            "quantity": 0,
                            "total": 0
                        };
                    data_element[data.branch_id][data.product_id].qty += parseFloat(data.total_qty);
                    data_element[data.branch_id][data.product_id].quantity += parseFloat(data.total_quantity);
                    data_element[data.branch_id][data.product_id].total += parseFloat(data.total);


                    if(product.code != ""){
                        if(typeof total_all[product.code] === "undefined")
                            total_all[product.code] = {
                                "name": product.name,
                                "quantity_name": quantity_type.name,
                                "quantity_id": product.quantity_id,
                                "qty": 0,
                                "quantity": 0,
                                "total": 0
                            };

                        total_all[product.code].qty += parseFloat(data.total_qty);
                        total_all[product.code].quantity += parseFloat(data.total_quantity);
                        total_all[product.code].total += parseFloat(data.total);
                    }
                });

                console.log(self.variable_list.DATA.products);

                self.variable_list.DATA.new.forEach(data => {
                    if(data.product_id == 0) return;
                    let product = array_list.find(self.variable_list.DATA.products, data.product_id, "id");
                    let quantity_type = array_list.find(main.data_list.PRODUCT_QUANTITY_TYPES, product.quantity_id, "id");
                    if(typeof data_element[data.branch_id] === "undefined") data_element[data.branch_id] = [];
                    if(typeof data_element[data.branch_id][data.product_id] === "undefined")
                        data_element[data.branch_id][data.product_id] = {
                            "name": product.name,
                            "quantity_name": quantity_type.name,
                            "quantity_id": product.quantity_id,
                            "qty": 0,
                            "quantity": 0,
                            "total": 0
                        };
                    data_element[data.branch_id][data.product_id].qty += parseFloat(data.total_qty);
                    data_element[data.branch_id][data.product_id].quantity += parseFloat(data.total_quantity);
                    data_element[data.branch_id][data.product_id].total += parseFloat(data.total);


                    if(product.code != ""){
                        if(typeof total_all[product.code] === "undefined")
                            total_all[product.code] = {
                                "name": product.name,
                                "quantity_name": quantity_type.name,
                                "quantity_id": product.quantity_id,
                                "qty": 0,
                                "quantity": 0,
                                "total": 0
                            };

                        total_all[product.code].qty += parseFloat(data.total_qty);
                        total_all[product.code].quantity += parseFloat(data.total_quantity);
                        total_all[product.code].total += parseFloat(data.total);
                    }
                });

                console.log(data_element);

                data_element.forEach(function (data,key) {
                    let branch_name = array_list.find(main.data_list.BRANCHES,key,"id").name;
                    table_element.body = `<tr style="background: #446"> <td colspan="3"><b>${branch_name.toUpperCase()}</b></td> </tr>`;

                    array_list.sort(data, "total", array_list.sort_types.DESC).forEach(item => {
                       table_element.body += `
                           <tr>
                               <td>${item.name}</td>
                               <td>${item.qty + ((item.quantity_id > 1) ? ` (${item.quantity.toFixed(2)} ${item.quantity_name})` : ``)}</td>
                               <td>${item.total.toFixed(2) + main.data_list.CURRENCY}</td>
                           </tr>
                       `;
                    });

                    elements += get_table_elements(table_element.head, table_element.body, key, branch_name);
                });

                console.log(total_all);

                if(main.data_list.BRANCH_INFO.is_main){
                    let branch_name = "Toplam";
                    table_element.body = `<tr style="background: #446"> <td colspan="3"><b>${branch_name}</b></td> </tr>`;
                    for (const [key, data] of Object.entries(total_all)) {
                        table_element.body += `
                            <tr>
                                <td>${data.name}</td>
                                <td>${data.qty + ((data.quantity_id > 1) ? ` (${data.quantity.toFixed(2)} ${data.quantity_name})` : ``)}</td>
                                <td>${data.total.toFixed(2) + main.data_list.CURRENCY}</td>
                            </tr>
                        `;
                    }
                    elements += get_table_elements(table_element.head, table_element.body, 0, branch_name);
                }
            }

            function sales_product_category(){
                table_element.head = `
                    <th><strong>${language.data.CATEGORY_NAME}</strong></th>
                    <th><strong>${language.data.TOTAL_PRICE}</strong></th>
                `;

                let data_element = Array();
                let total_all = Array();

                self.variable_list.DATA.old.forEach(data => {
                    if(data.product_id == 0) return;
                    let product = array_list.find(self.variable_list.DATA.products, data.product_id, "id");
                    let category = array_list.find(self.variable_list.DATA.categories, product.category_id, "id");
                    if(typeof category === "undefined") {
                        category = {
                            "id": 0,
                            "name": "Silinmiş"
                        }
                    }

                    if(typeof data_element[data.branch_id] === "undefined") data_element[data.branch_id] = [];
                    if(typeof data_element[data.branch_id][category.id] === "undefined")
                        data_element[data.branch_id][category.id] = {
                            "name": category.name,
                            "total": 0
                        };
                    data_element[data.branch_id][category.id].total += parseFloat(data.total);

                    let key = array_list.convert_string_to_key(category.name);
                    if(typeof total_all[key] === "undefined")
                        total_all[key] = {
                            "name": category.name,
                            "total": 0
                        };
                    total_all[key].total += parseFloat(data.total);
                });

                self.variable_list.DATA.new.forEach(data => {
                    if(data.product_id == 0) return;
                    let product = array_list.find(self.variable_list.DATA.products, data.product_id, "id");
                    let category = array_list.find(self.variable_list.DATA.categories, product.category_id, "id");
                    if(typeof category === "undefined") {
                        category = {
                            "id": 0,
                            "name": "Silinmiş"
                        }
                    }

                    if(typeof data_element[data.branch_id] === "undefined") data_element[data.branch_id] = [];
                    if(typeof data_element[data.branch_id][category.id] === "undefined")
                        data_element[data.branch_id][category.id] = {
                            "name": category.name,
                            "total": 0
                        };
                    data_element[data.branch_id][category.id].total += parseFloat(data.total);

                    let key = array_list.convert_string_to_key(category.name);
                    if(typeof total_all[key] === "undefined")
                        total_all[key] = {
                            "name": category.name,
                            "total": 0
                        };
                    total_all[key].total += parseFloat(data.total);
                });


                data_element.forEach(function (data,key) {
                    let branch_name = array_list.find(main.data_list.BRANCHES,key,"id").name;
                    table_element.body = `<tr style="background: #446"> <td colspan="2"><b>${branch_name.toUpperCase()}</b></td> </tr>`;

                    array_list.sort(data, "total", array_list.sort_types.DESC).forEach(item => {
                        table_element.body += `
                            <tr>
                                <td>${item.name}</td>
                                <td>${item.total.toFixed(2) + main.data_list.CURRENCY}</td>
                            </tr>
                        `;
                    });
                    elements += get_table_elements(table_element.head, table_element.body, key, branch_name);
                });

                console.log(total_all);

                if(main.data_list.BRANCH_INFO.is_main){
                    let branch_name = "Toplam";
                    table_element.body = `<tr style="background: #446"> <td colspan="2"><b>${branch_name}</b></td> </tr>`;
                    for (const [key, data] of Object.entries(total_all)) {
                        table_element.body += `
                            <tr>
                                <td>${data.name}</td>
                                <td>${data.total.toFixed(2) + main.data_list.CURRENCY}</td>
                            </tr>
                        `;
                    }
                    elements += get_table_elements(table_element.head, table_element.body, 0, branch_name);
                }
            }

            function sales_product_waiter(){
                table_element.head = `
                    <th><strong>Garson İsmi</strong></th>
                    <th><strong>${language.data.PRODUCT_NAME}</strong></th>
                    <th><strong>${language.data.TOTAL}</strong></th>
                    <th><strong>${language.data.TOTAL_PRICE}</strong></th>
                `;

                let data_element = Array();

                self.variable_list.DATA.old.forEach(data => {
                    if(data.account_id == 0) return;
                    if(data.product_id == 0) return;
                    let product = array_list.find(self.variable_list.DATA.products, data.product_id, "id");
                    let quantity_type = array_list.find(main.data_list.PRODUCT_QUANTITY_TYPES, product.quantity_id, "id");

                    if(typeof data_element[data.branch_id] === "undefined") data_element[data.branch_id] = [];
                    if(typeof data_element[data.branch_id][data.account_id] === "undefined")
                        data_element[data.branch_id][data.account_id] = {
                            "name": "",
                            "products": Array()
                        };
                    if(typeof data_element[data.branch_id][data.account_id].products[data.product_id] === "undefined")
                        data_element[data.branch_id][data.account_id].products[data.product_id] = {
                            "name": product.name,
                            "quantity_name": quantity_type.name,
                            "quantity_id": product.quantity_id,
                            "qty": 0,
                            "quantity": 0,
                            "total": 0
                        };

                    data_element[data.branch_id][data.account_id].name = array_list.find(self.variable_list.DATA.accounts, data.account_id, "id").name;
                    data_element[data.branch_id][data.account_id].products[data.product_id].qty += parseFloat(data.total_qty);
                    data_element[data.branch_id][data.account_id].products[data.product_id].quantity += parseFloat(data.total_quantity);
                    data_element[data.branch_id][data.account_id].products[data.product_id].total += parseFloat(data.total);
                });

                self.variable_list.DATA.new.forEach(data => {
                    if(data.account_id == 0) return;
                    if(data.product_id == 0) return;
                    let product = array_list.find(self.variable_list.DATA.products, data.product_id, "id");
                    let quantity_type = array_list.find(main.data_list.PRODUCT_QUANTITY_TYPES, product.quantity_id, "id");

                    if(typeof data_element[data.branch_id] === "undefined") data_element[data.branch_id] = [];
                    if(typeof data_element[data.branch_id][data.account_id] === "undefined")
                        data_element[data.branch_id][data.account_id] = {
                            "name": "",
                            "products": Array()
                        };
                    if(typeof data_element[data.branch_id][data.account_id].products[data.product_id] === "undefined")
                        data_element[data.branch_id][data.account_id].products[data.product_id] = {
                            "name": product.name,
                            "quantity_name": quantity_type.name,
                            "quantity_id": product.quantity_id,
                            "qty": 0,
                            "quantity": 0,
                            "total": 0
                        };

                    data_element[data.branch_id][data.account_id].name = array_list.find(self.variable_list.DATA.accounts, data.account_id, "id").name;
                    data_element[data.branch_id][data.account_id].products[data.product_id].qty += parseFloat(data.total_qty);
                    data_element[data.branch_id][data.account_id].products[data.product_id].quantity += parseFloat(data.total_quantity);
                    data_element[data.branch_id][data.account_id].products[data.product_id].total += parseFloat(data.total);
                });

                console.log(data_element);

                data_element.forEach(function (data,key) {
                    let branch_name = array_list.find(main.data_list.BRANCHES,key,"id").name;
                    table_element.body = `<tr style="background: #446"> <td colspan="4"><b>${branch_name.toUpperCase()}</b></td> </tr>`;

                    data.forEach(item => {
                        item.products.forEach(item2 => {
                            table_element.body += `
                                <tr>
                                    <td>${item.name}</td>
                                    <td>${item2.name}</td>
                                    <td>${item2.qty + ((item2.quantity_id > 1) ? ` (${item2.quantity.toFixed(2)} ${item2.quantity_name})` : ``)}</td>
                                    <td>${item2.total.toFixed(2) + main.data_list.CURRENCY}</td>
                                </tr>
                            `;
                        });
                    });
                    elements += get_table_elements(table_element.head, table_element.body, key, branch_name);
                });
            }

            function sales_customer(){
                table_element.head = `
                    <th><strong>${language.data.CUSTOMER_NAME}</strong></th>
                    <th><strong>${language.data.TOTAL}</strong></th>
                `;

                let data_element = Array();

                self.variable_list.DATA.old.forEach(data => {
                    if(typeof data_element[data.branch_id] === "undefined") data_element[data.branch_id] = [];
                    if(typeof data_element[data.branch_id][data.account_id] === "undefined")
                        data_element[data.branch_id][data.account_id] = {
                            "name": "",
                            "total": 0,
                            "type": data.account_type
                        };

                    data_element[data.branch_id][data.account_id].name = array_list.find(
                        (
                            (data.account_type == helper.db.account_types.CUSTOMER)
                                ? self.variable_list.DATA.accounts
                                : (data.account_type == helper.db.account_types.YEMEK_SEPETI)
                                    ? self.variable_list.DATA.accounts_yemek_sepeti
                                    : {"id": data.account_id, "name": "NULL"}
                        ), data.account_id, "id").name;
                    data_element[data.branch_id][data.account_id].total += parseFloat(data.total);
                });

                self.variable_list.DATA.new.forEach(data => {
                    if(typeof data_element[data.branch_id] === "undefined") data_element[data.branch_id] = [];
                    if(typeof data_element[data.branch_id][data.account_id] === "undefined")
                        data_element[data.branch_id][data.account_id] = {
                            "name": "",
                            "total": 0,
                            "type": data.account_type
                        };

                    data_element[data.branch_id][data.account_id].name = array_list.find(
                        (
                            (data.account_type == helper.db.account_types.CUSTOMER)
                                ? self.variable_list.DATA.accounts
                                : (data.account_type == helper.db.account_types.YEMEK_SEPETI)
                                    ? self.variable_list.DATA.accounts_yemek_sepeti
                                    : {"id": data.account_id, "name": "NULL"}
                        ), data.account_id, "id").name;
                    data_element[data.branch_id][data.account_id].total += parseFloat(data.total);
                });

                data_element.forEach(function (data,key) {
                    let branch_name = array_list.find(main.data_list.BRANCHES,key,"id").name;
                    table_element.body = `<tr style="background: #446"> <td colspan="2"><b>${branch_name.toUpperCase()}</b></td> </tr>`;

                    data.forEach(item => {
                        let type = array_list.find(self.variable_list.DATA.types, item.type, "id").name;
                        table_element.body += `
                        <tr>
                            <td>(${type}) ${item.name}</td>
                            <td>${item.total.toFixed(2) + main.data_list.CURRENCY}</td>
                        </tr>
                    `;
                    });
                    elements += get_table_elements(table_element.head, table_element.body, key, branch_name);
                });
            }

            function sales_waiter(){
                table_element.head = `
                    <th><strong>${language.data.WAITER_NAME}</strong></th>
                    <th><strong>${language.data.TOTAL}</strong></th>
                `;

                let data_element = Array();

                self.variable_list.DATA.old.forEach(data => {
                    if(data.account_id == 0) return;
                    if(typeof data_element[data.branch_id] === "undefined") data_element[data.branch_id] = [];
                    if(typeof data_element[data.branch_id][data.account_id] === "undefined")
                        data_element[data.branch_id][data.account_id] = {
                            "name": "",
                            "total": 0
                        };

                    data_element[data.branch_id][data.account_id].name = array_list.find(self.variable_list.DATA.accounts, data.account_id, "id").name;
                    data_element[data.branch_id][data.account_id].total += parseFloat(data.total);
                });

                self.variable_list.DATA.new.forEach(data => {
                    if(data.account_id == 0) return;
                    if(typeof data_element[data.branch_id] === "undefined") data_element[data.branch_id] = [];
                    if(typeof data_element[data.branch_id][data.account_id] === "undefined")
                        data_element[data.branch_id][data.account_id] = {
                            "name": "",
                            "total": 0
                        };

                    data_element[data.branch_id][data.account_id].name = array_list.find(self.variable_list.DATA.accounts, data.account_id, "id").name;
                    data_element[data.branch_id][data.account_id].total += parseFloat(data.total);
                });

                data_element.forEach(function (data,key) {
                    let branch_name = array_list.find(main.data_list.BRANCHES,key,"id").name;
                    table_element.body = `<tr style="background: #446"> <td colspan="2"><b>${branch_name.toUpperCase()}</b></td> </tr>`;

                    data.forEach(item => {
                        table_element.body += `
                        <tr>
                            <td>${item.name}</td>
                            <td>${item.total.toFixed(2) + main.data_list.CURRENCY}</td>
                        </tr>
                    `;
                    });
                    elements += get_table_elements(table_element.head, table_element.body, key, branch_name);
                });
            }

            function sales_table(){
                table_element.head = `
                    <th><strong>${language.data.TABLE}</strong></th>
                    <th><strong>${language.data.TOTAL}</strong></th>
                `;

                let data_element = Array();

                self.variable_list.DATA.old.forEach(data => {
                    if(typeof data_element[data.branch_id] === "undefined") data_element[data.branch_id] = [];
                    if(typeof data_element[data.branch_id][data.table_id] === "undefined")
                        data_element[data.branch_id][data.table_id] = {
                            "name": "",
                            "no": 0,
                            "total": 0
                        };

                    let table_detail = array_list.find(self.variable_list.DATA.tables, data.table_id, "id");
                    data_element[data.branch_id][data.table_id].name = table_detail.section_name;
                    data_element[data.branch_id][data.table_id].no = table_detail.no;
                    data_element[data.branch_id][data.table_id].total += parseFloat(data.total);
                });

                self.variable_list.DATA.new.forEach(data => {
                    if(typeof data_element[data.branch_id] === "undefined") data_element[data.branch_id] = [];
                    if(typeof data_element[data.branch_id][data.table_id] === "undefined")
                        data_element[data.branch_id][data.table_id] = {
                            "name": "",
                            "no": 0,
                            "total": 0
                        };

                    let table_detail = array_list.find(self.variable_list.DATA.tables, data.table_id, "id");
                    data_element[data.branch_id][data.table_id].name = table_detail.section_name;
                    data_element[data.branch_id][data.table_id].no = table_detail.no;
                    data_element[data.branch_id][data.table_id].total += parseFloat(data.total);
                });

                data_element.forEach(function (data,key) {
                    let branch_name = array_list.find(main.data_list.BRANCHES,key,"id").name;
                    table_element.body = `<tr style="background: #446"> <td colspan="2"><b>${branch_name.toUpperCase()}</b></td> </tr>`;

                    array_list.sort(data, "total", array_list.sort_types.DESC).forEach(item => {
                        item.no = (item.no == 0) ? "" : item.no;
                        table_element.body += `
                        <tr>
                            <td>${item.name} ${item.no}</td>
                            <td>${item.total.toFixed(2) + main.data_list.CURRENCY}</td>
                        </tr>
                    `;
                    });
                    elements += get_table_elements(table_element.head, table_element.body, key, branch_name);
                });
            }

            function rush_hours(){
                table_element.head = `
                    <th>${language.data.HOUR}</th>
                    <th><strong>${language.data.TOTAL}</strong></th>
                `;

                let data_element = Array();

                self.variable_list.DATA.old.forEach(data => {
                    if(typeof data_element[data.branch_id] === "undefined") data_element[data.branch_id] = [];

                    let key = data.time.substring(0, 2);
                    if(typeof data_element[data.branch_id][key] === "undefined")
                        data_element[data.branch_id][key] = {
                            "name": "",
                            "total": 0
                        };

                    data_element[data.branch_id][key].name = data.time
                    data_element[data.branch_id][key].total += parseFloat(data.total);
                });

                self.variable_list.DATA.new.forEach(data => {
                    if(typeof data_element[data.branch_id] === "undefined") data_element[data.branch_id] = [];

                    let key = data.time.substring(0, 2);
                    if(typeof data_element[data.branch_id][key] === "undefined")
                        data_element[data.branch_id][key] = {
                            "name": "",
                            "total": 0
                        };

                    data_element[data.branch_id][key].name = data.time
                    data_element[data.branch_id][key].total += parseFloat(data.total);
                });

                console.log(data_element);

                data_element.forEach(function (data,key) {
                    let branch_name = array_list.find(main.data_list.BRANCHES,key,"id").name;
                    table_element.body = `<tr style="background: #446"> <td colspan="2"><b>${branch_name.toUpperCase()}</b></td> </tr>`;

                    data.forEach(item => {
                        table_element.body += `
                        <tr>
                            <td>${item.name}</td>
                            <td>${item.total.toFixed(2) + main.data_list.CURRENCY}</td>
                        </tr>
                    `;
                    });
                    elements += get_table_elements(table_element.head, table_element.body, key, branch_name);
                });
            }

            function orders_cancel(){
                table_element.head = `k
                    <th><strong>${language.data.PRODUCT_NAME}</strong></th>
                    <th><strong>${language.data.TOTAL}</strong></th>
                    <th><strong>${language.data.TOTAL_PRICE}</strong></th>
                `;

                let data_element = Array();

                self.variable_list.DATA.old.forEach(data => {
                    if(data.product_id == 0) return;
                    if(typeof data_element[data.branch_id] === "undefined") data_element[data.branch_id] = [];
                    if(typeof data_element[data.branch_id][data.product_id] === "undefined")
                        data_element[data.branch_id][data.product_id] = {
                            "name": "",
                            "quantity_name": "",
                            "quantity_id": 0,
                            "qty": 0,
                            "quantity": 0,
                            "total": 0
                        };

                    data_element[data.branch_id][data.product_id].name = array_list.find(self.variable_list.DATA.products, data.product_id, "id").name;
                    data_element[data.branch_id][data.product_id].quantity_name = array_list.find(
                        main.data_list.PRODUCT_QUANTITY_TYPES,
                        array_list.find(
                            self.variable_list.DATA.products,
                            data.product_id,
                            "id"
                        ).quantity_id,
                        "id"
                    ).name;
                    data_element[data.branch_id][data.product_id].quantity_id = array_list.find(
                        self.variable_list.DATA.products,
                        data.product_id,
                        "id"
                    ).quantity_id;
                    data_element[data.branch_id][data.product_id].qty += parseFloat(data.total_qty);
                    data_element[data.branch_id][data.product_id].quantity += parseFloat(data.total_quantity);
                    data_element[data.branch_id][data.product_id].total += parseFloat(data.total);
                });

                self.variable_list.DATA.new.forEach(data => {
                    if(data.product_id == 0) return;
                    if(typeof data_element[data.branch_id] === "undefined") data_element[data.branch_id] = [];
                    if(typeof data_element[data.branch_id][data.product_id] === "undefined")
                        data_element[data.branch_id][data.product_id] = {
                            "name": "",
                            "quantity_name": "",
                            "quantity_id": 0,
                            "qty": 0,
                            "quantity": 0,
                            "total": 0
                        };

                    data_element[data.branch_id][data.product_id].name = array_list.find(self.variable_list.DATA.products, data.product_id, "id").name;
                    data_element[data.branch_id][data.product_id].quantity_name = array_list.find(
                        main.data_list.PRODUCT_QUANTITY_TYPES,
                        array_list.find(
                            self.variable_list.DATA.products,
                            data.product_id,
                            "id"
                        ).quantity_id,
                        "id"
                    ).name;
                    data_element[data.branch_id][data.product_id].quantity_id = array_list.find(
                        self.variable_list.DATA.products,
                        data.product_id,
                        "id"
                    ).quantity_id;
                    data_element[data.branch_id][data.product_id].qty += parseFloat(data.total_qty);
                    data_element[data.branch_id][data.product_id].quantity += parseFloat(data.total_quantity);
                    data_element[data.branch_id][data.product_id].total += parseFloat(data.total);
                });

                data_element.forEach(function (data,key) {
                    let branch_name = array_list.find(main.data_list.BRANCHES,key,"id").name;
                    table_element.body = `<tr style="background: #446"> <td colspan="3"><b>${branch_name.toUpperCase()}</b></td> </tr>`;

                    array_list.sort(data, "total", array_list.sort_types.DESC).forEach(item => {
                        table_element.body += `
                        <tr>
                            <td>${item.name}</td>
                            <td>${item.qty + ((data.quantity_id > 1) ? ` (${item.quantity.toFixed(2)} ${item.quantity_name})` : ``)}</td>
                            <td>${item.total.toFixed(2) + main.data_list.CURRENCY}</td>
                        </tr>
                    `;
                    });
                    elements += get_table_elements(table_element.head, table_element.body, key, branch_name);
                });

            }

            function orders_catering(){
                table_element.head = `
                    <th><strong>${language.data.PRODUCT_NAME}</strong></th>
                    <th><strong>${language.data.TOTAL}</strong></th>
                    <th><strong>${language.data.TOTAL_PRICE}</strong></th>
                `;

                let data_element = Array();

                self.variable_list.DATA.old.forEach(data => {
                    if(data.product_id == 0) return;
                    if(typeof data_element[data.branch_id] === "undefined") data_element[data.branch_id] = [];
                    if(typeof data_element[data.branch_id][data.product_id] === "undefined")
                        data_element[data.branch_id][data.product_id] = {
                            "name": "",
                            "quantity_name": "",
                            "quantity_id": 0,
                            "qty": 0,
                            "quantity": 0,
                            "total": 0
                        };

                    data_element[data.branch_id][data.product_id].name = array_list.find(self.variable_list.DATA.products, data.product_id, "id").name;
                    data_element[data.branch_id][data.product_id].quantity_name = array_list.find(
                        main.data_list.PRODUCT_QUANTITY_TYPES,
                        array_list.find(
                            self.variable_list.DATA.products,
                            data.product_id,
                            "id"
                        ).quantity_id,
                        "id"
                    ).name;
                    data_element[data.branch_id][data.product_id].quantity_id = array_list.find(
                        self.variable_list.DATA.products,
                        data.product_id,
                        "id"
                    ).quantity_id;
                    data_element[data.branch_id][data.product_id].qty += parseFloat(data.total_qty);
                    data_element[data.branch_id][data.product_id].quantity += parseFloat(data.total_quantity);
                    data_element[data.branch_id][data.product_id].total += parseFloat(data.total);
                });

                self.variable_list.DATA.new.forEach(data => {
                    if(data.product_id == 0) return;
                    if(typeof data_element[data.branch_id] === "undefined") data_element[data.branch_id] = [];
                    if(typeof data_element[data.branch_id][data.product_id] === "undefined")
                        data_element[data.branch_id][data.product_id] = {
                            "name": "",
                            "quantity_name": "",
                            "quantity_id": 0,
                            "qty": 0,
                            "quantity": 0,
                            "total": 0
                        };

                    data_element[data.branch_id][data.product_id].name = array_list.find(self.variable_list.DATA.products, data.product_id, "id").name;
                    data_element[data.branch_id][data.product_id].quantity_name = array_list.find(
                        main.data_list.PRODUCT_QUANTITY_TYPES,
                        array_list.find(
                            self.variable_list.DATA.products,
                            data.product_id,
                            "id"
                        ).quantity_id,
                        "id"
                    ).name;
                    data_element[data.branch_id][data.product_id].quantity_id = array_list.find(
                        self.variable_list.DATA.products,
                        data.product_id,
                        "id"
                    ).quantity_id;
                    data_element[data.branch_id][data.product_id].qty += parseFloat(data.total_qty);
                    data_element[data.branch_id][data.product_id].quantity += parseFloat(data.total_quantity);
                    data_element[data.branch_id][data.product_id].total += parseFloat(data.total);
                });

                data_element.forEach(function (data,key) {
                    let branch_name = array_list.find(main.data_list.BRANCHES,key,"id").name;
                    table_element.body = `<tr style="background: #446"> <td colspan="3"><b>${branch_name.toUpperCase()}</b></td> </tr>`;

                    array_list.sort(data, "total", array_list.sort_types.DESC).forEach(item => {
                        table_element.body += `
                        <tr>
                            <td>${item.name}</td>
                            <td>${item.qty + ((item.quantity_id > 1) ? ` (${item.quantity.toFixed(2)} ${item.quantity_name})` : ``)}</td>
                            <td>${item.total.toFixed(2) + main.data_list.CURRENCY}</td>
                        </tr>
                    `;
                    });
                    elements += get_table_elements(table_element.head, table_element.body, key, branch_name);
                });
            }

            switch (self.variable_list.TYPE){
                case self.report_types.TOTAL_PRICE: total_price(); break;
                case self.report_types.PAYMENT_TYPES: payment_types(); convert = true; break;
                case self.report_types.SALES_PRODUCT: sales_product(); convert = true; break;
                case self.report_types.SALES_CUSTOMER: sales_customer(); convert = true; break;
                case self.report_types.SALES_WAITER: sales_waiter(); convert = true; break;
                case self.report_types.SALES_TABLE: sales_table(); convert = true; break;
                case self.report_types.RUSH_HOURS: rush_hours(); convert = true; break;
                case self.report_types.ORDERS_CANCEL: orders_cancel(); convert = true; break;
                case self.report_types.ORDERS_CATERING: orders_catering(); convert = true; break;
                case self.report_types.SALES_PRODUCT_CATEGORY: sales_product_category(); convert = true; break;
                case self.report_types.SALES_PRODUCT_WAITER: sales_product_waiter(); convert = true; break;
            }

            $(`${self.id_list.TABLES}`).html(elements);
            $(self.class_list.RESULT).show();
            if(convert){ $(self.class_list.CONVERT).show(); }
            else{ $(self.class_list.CONVERT).hide(); }
        },
        initialize: function () {
            let self = this;

            function set_events(){
                $(document).on("click", self.class_list.BUTTON_CONVERT, function () {
                    let date_start = $(`input[name="date_start"]`).val();
                    let date_end = $(`input[name="date_end"]`).val();
                    let report_name = $(`${report_form.id_list.FORM} button[value="${self.variable_list.TYPE}"]`).html();
                    let file_name = `MimiPos ${report_name} ${date_start}-${date_end}`;

                    let values = {
                        "tables": Array(),
                        "wsnames": Array()
                    };
                    Array.from($(self.class_list.TABLE)).forEach(table => {
                        table = $(table);
                        values.tables.push(table.attr("id"));
                        values.wsnames.push(table.attr("sheet-name"));
                    });

                    html_to_excel(values.tables, values.wsnames, `${file_name}.xls`, 'Excel')
                });
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

    return report_product;
})();

$(function () {
    let _report_product = new report_product();
})