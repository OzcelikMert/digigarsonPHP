let print_interval = null;

let invoice = (function () {
    let invoice_data = Array();
    let printer_name = "";
    let view = false;
    let order_id = 0;
    let interval_auto_print = null;
    let printer_ajax_path = `${settings.paths.primary.PHP}orders/`;
    invoice.table_and_section = null;
    invoice.user_name = null;
    invoice.return_html = false;
    invoice.print_type = {
        TABLE: 1,
        ORDER: 2,
        SAFE_ORDER: 3,
        VIEW: 4
    }
    invoice.print_invoce_type = {
        PAYMENT_RECEIPT: 1,
        KITCHEN: 2,
        CANCEL: 3,
        Z_REPORT: 4
    }

    function invoice(name,type,invoce_type,id = 0, data = Array(),address_str = ""){
        printer_name = name;
        switch (invoce_type){
            case invoice.print_invoce_type.PAYMENT_RECEIPT:
                if (invoice.print_type.SAFE_ORDER === type){ //kasaadan yazdırma işlemi
                    invoice_data = get_safe_data(data);
                }else {
                    if (order_id > 0) id = order_id;
                    invoice_data = invoice.get_data(type,id,order_id);
                }
                if (invoice.print_type.VIEW === type) view = true; //fiş görüntüleme
                if (invoice.return_html) return safe_invoice(address_str);
                safe_invoice(address_str);
                break;
            case invoice.print_invoce_type.KITCHEN:
            case invoice.print_invoce_type.CANCEL:
                console.log("listener_types.KITCHEN")
                kitchen_invoice(data);
                break;
            case invoice.print_invoce_type.Z_REPORT:
                if (invoice.print_type.VIEW === type) view = true;
                if (invoice.return_html) return z_report_invoice(data);
                z_report_invoice(data);
                break;
        }
        return true;
    }
    function get_safe_data(data = Array()){
        data.products.forEach(function (product){
            let p = array_list.find(main.data_list.PRODUCTS,parseInt(product.id),"id")
            product.quantity_id = p.quantity_id;
            product.name = p.name;

            product.options.forEach(function (option){
                option.name = array_list.find(main.data_list.PRODUCT_OPTIONS_ITEMS,parseInt(option.option_item_id),"id").name
            })
        })
        data.table = "Kasa Satış"
        return data;
    }
    function setPrint(value,printer_name = app.printer.safe.printer_name){
        if (!view){
            app.printer_settings.print_invoice(printer_name,value);
        }else {
            view = false;
            app.printer_settings.view_invoice(printer_name,value);
        }
    }

    function safe_invoice(address_str = 1){
        console.log(invoice_data)
        let e = new Safe(invoice_data,address_str);
        let data = e.invoice()
        if (invoice.return_html && view) return data.html;
        setPrint(data.html);
    }
    function z_report_invoice(invoice_data){
        console.log(invoice_data)
        if(typeof invoice_data.info === "undefined") invoice_data.info = {};
        invoice_data.info.currency = "₺";
        if(typeof invoice_data.info.safe === "undefined") invoice_data.info.safe = 0;
        let e = new z_report(invoice_data);
        let data = e.invoice();
        if (invoice.return_html && view) return data.html;
        setPrint(data.html);
    }
    function kitchen_invoice(data = Array()){
        console.log(data);
        let product = null;
        let table = array_list.find(main.data_list.TABLES,data.orders[0].table_id,"id");
        let section = array_list.find(main.data_list.SECTIONS,table.section_id,"id")
        let table_name = array_list.find(main.data_list.SECTION_TYPES,section.section_id,"id").name + " "+ table.no
        let product_groups = Array();

        data.products.forEach(function (item){
            product = array_list.find(main.data_list.PRODUCTS,parseInt(item.product_id),"id")
            if (!Array.isArray(product_groups[product.category_id])) product_groups[product.category_id] = Array();
            item.name = product.name;
            item.quantity_id = product.quantity_id;
            item.category_id = product.category_id;
            if (item.options.length > 0){
                item.options.forEach(function (option){
                    console.log(option)
                    option.name = array_list.find(main.data_list.PRODUCT_OPTIONS_ITEMS,option.option_item_id,"id").name
                    option.type = array_list.find(main.data_list.PRODUCT_OPTIONS,option.option_id,"id").type;
                })
            }
            product_groups[product.category_id].push(item);
        })
        console.log(product_groups);

        let print = Array();
        let index = 0
        app.printer.groups.forEach(function (group){
            print[index] = Array();
            print[index].products = Array();
            print[index].group_name = group.name;
            print[index].printer_name = group.printer_name;
            print[index].table_name = table_name;

            group.categories.forEach(function (category){
                if (product_groups[category] !== undefined && Array.isArray(product_groups[category])){
                    product_groups[category].forEach(function (item){
                        print[index].products.push(item)
                    })
                }
            })
            index++;
        })
        console.log(print);

        let today = new Date();
        let date = today.getFullYear()+'/'+(today.getMonth()+1)+'/'+today.getDate();
        let time = today.getHours() + ":" + today.getMinutes();
        let info = {
            type: data.type,
            OrderDate: date,
            OrderTime: time,
            OrderID: data.orders[0].no,
            Currency: "₺",
            UserName: data.user_name
        }
        print.forEach(function (print_page){
            if (print_page.products.length > 0){
                let e = new Kitchen(info,print_page);
                let data = e.invoice()
                setPrint(data.html,print_page.printer_name);
            }
        })

    }
    function get_table_string(table_id){
        try {
            let table = array_list.find(main.data_list.TABLES,table_id,"id");
            let section = array_list.find(main.data_list.SECTIONS,table.section_id,"id");
            let section_name = array_list.find(main.data_list.SECTION_TYPES,section.section_id,"id").name;
            return `${section_name} ${table.no} `;
        }catch (e) {
            console.error("error: get_table_string()");
            return "";
        }
    }

    invoice.z_report = function(data, is_view = false){
        let type = (is_view) ? invoice.print_type.VIEW : invoice.print_type.ORDER
        return invoice(
            null,
            type,
            invoice.print_invoce_type.Z_REPORT,
            null,
            data
        );

    }
    invoice.payment_receipt = function (data,table_id = 0){
        order_id = 0;
        let type = (table_id === 0) ? invoice.print_type.SAFE_ORDER : invoice.print_type.TABLE
        invoice(
            printer_name,
            type,
            invoice.print_invoce_type.PAYMENT_RECEIPT,
            table_id,
            data
        )
    }
    invoice.payment_receipt_takeaway = function (t_order_id,address_string){
        order_id = t_order_id;
        invoice(
            printer_name,
            invoice.print_type.TABLE,
            invoice.print_invoce_type.PAYMENT_RECEIPT,
            0,
            null,
            address_string
        )
    }
    invoice.payyed_payment_receipt = function (order, is_view = false,table_and_section = null,user_name = null,payment = null){
        let type = (is_view) ? invoice.print_type.VIEW : invoice.print_type.ORDER
        order_id = order;
        invoice.table_and_section = table_and_section;
        invoice.user_name = user_name;
        return invoice(null, type, invoice.print_invoce_type.PAYMENT_RECEIPT, 0)
    }
    invoice.auto_print = function (){
        interval_auto_print = setInterval(function (){
            if (app.printer.groups.length > 0){
                console.log("INVOICE.AUTO_PRINT: get invoices")
                $.ajax({
                    url: `${printer_ajax_path}set.php`,
                    type: "POST",
                    data: {set_type : 9},
                    success: function (data) {
                        data = JSON.parse(data);
                        data.rows.forEach(function (item){
                            item.data = JSON.parse(item.data);

                            switch (item.data.type) {
                                case invoice.print_invoce_type.PAYMENT_RECEIPT:
                                    invoice.waiter_invoice(
                                        app.printer.printer_name,
                                        invoice.print_type.TABLE,
                                        invoice.print_invoce_type.PAYMENT_RECEIPT,
                                        item.data.table_id,
                                        item.data.order_id
                                    )
                                    break;
                                case invoice.print_invoce_type.KITCHEN:
                                case invoice.print_invoce_type.CANCEL:
                                    invoice(null, null, invoice.print_invoce_type.KITCHEN, null, item.data);
                                    break;
                            }
                        })
                    }, timeout: settings.ajax_timeouts.FAST
                });
            }else {
                clearInterval(print_interval);
            }
        },settings.ajax_timeouts.FAST)
    }
    invoice.waiter_invoice = function (name,type,invoce_type,id = 0, invoice_order_id= 0 ){
        order_id = invoice_order_id;
        invoice(name,type,invoce_type,id);
    }
    invoice.get_data = function (type,get_id,order_id = 0){
        let product_counts = [];
        let catering_products = [];
        let table_id = 0;

        let print_data = {
            "orders_id": Array(),
            "products": Array(),
            "orders" : Array(),
            "table": "",
            "user_name": ""
        }

        if (type === invoice.print_type.TABLE){
            //get table orders
            print_data.orders = (order_id === 0)
                ? array_list.find_multi(main.data_list.ORDERS,get_id,"table_id")
                : array_list.find_multi(main.data_list.ORDERS,order_id,"id");

            //table get order id
            print_data.orders.forEach(function(e){
                if (order_id === 0 || order_id === e.id){
                    print_data.orders_id.push(e.id)
                    table_id = e.table_id;
                }
            });

        }else if(type === invoice.print_type.ORDER || type === invoice.print_type.VIEW){
            //get order id
            print_data.orders = array_list.find_multi(main.data_list.ORDERS,get_id,"id"); // get_id => table_id
            print_data.orders_id.push(get_id) // get_id => order_id
        }

        print_data.table = (invoice.table_and_section !== null) ? invoice.table_and_section
            : (table_id > 0) ? get_table_string(table_id) : "";

        main.data_list.ORDER_PRODUCTS.forEach(function(product){
            console.log(product);
            //if(product.status == helper.db.order_product_status_types.CANCEL) return;
            if(print_data.orders_id.includes(product.order_id)) {
                let product_data = {};
                if (product.status === helper.db.order_product_status_types.CATERING || product.status === helper.db.order_product_status_types.CANCEL){
                    product.price = 0;
                }
                if(product.type === helper.db.order_product_types.DISCOUNT){
                    product_data = {
                        "name": "Iskonto",
                        "quantity_id": 1
                    };
                }else{
                    product_data = array_list.find( main.data_list.PRODUCTS,product.product_id,"id");
                }
                product.name = product_data.name;
                product.quantity_id = product_data.quantity_id;
                product.options = Array();
                print_data.products.push(product);
            }
        })

        // Get orders Options and Options item product.option array
        print_data.products.forEach(function(product){
            let options_items = array_list.find_multi(main.data_list.ORDER_PRODUCT_OPTIONS,product.id,"order_product_id");
            options_items.forEach(function (item){
                console.log(item);
                let option_item = array_list.find( main.data_list.PRODUCT_OPTIONS_ITEMS,item.option_item_id,"id");
                if(typeof option_item !== "undefined"){
                    item.name = option_item.name;
                    //get main options
                    if (array_list.find(main.data_list.PRODUCT_OPTIONS,item.option_id,"id") === undefined){
                        print_data.options.push(array_list.find(main.data_list.PRODUCT_OPTIONS,item.option_id,"id"))
                    }
                }
            })

            if (options_items.length > 0) product.options = options_items

            if (product.status !== helper.db.order_product_status_types.CATERING){
                if (typeof product_counts[product.product_id] == "undefined") product_counts[product.product_id]  = [];
                product_counts[product.product_id].push({
                    name:product.name,
                    comment:product.comment,
                    options: product.options,
                    price: product.price,
                    qty:product.qty,
                    quantity:product.quantity,
                    quantity_id: product.quantity_id,
                    order_id: product.order_id,
                    id: product.id,
                    status: product.status,
                })
            }else {
                catering_products.push({
                    name:product.name,
                    comment:product.comment,
                    options: product.options,
                    price: product.price,
                    qty:product.qty,
                    quantity:product.quantity,
                    quantity_id: product.quantity_id,
                    order_id: product.order_id,
                    id: product.id,
                    status: product.status,
                });
            }
        });
        print_data.user_name = ((typeof app == "undefined" || main.data_list.PAYMENT_INVOICE_USER) )
            ? (invoice.user_name !== null)
                ? invoice.user_name : print_data.products[print_data.products.length-1].account_name
            : "";

        //Merge Product and Options
        let merge_products = [],qty,price,option_list,last_product = {};
        //  helper.log(product_counts,"PRODUCT COUNTS")

        //for (const item in product_counts) {
        product_counts.forEach(function (item) {
            qty = 0;
            price = 0;
            option_list = Array();
            //last_product = product_counts[item][0];
            last_product = item[0];

            // console.log("item ----------")
            // console.log(item)

            // product_counts[item].forEach(function (data){ //ürün
            item.forEach(function (data){ //ürün
                qty += data.qty;
                price += data.price;

                if (option_list.length === 0) {
                    data.options.forEach(function (option) {
                        option_list.push({
                            id: option.id,
                            name: option.name,
                            price: option.price,
                            qty: option.qty,
                            option_id: option.option_id,
                            option_item_id: option.option_item_id,
                            order_product_id: option.order_product_id,
                        })
                    })
                }else {
                    data.options.forEach(function (option){ //opsiyon
                        let index = array_list.index_of(option_list,option.option_item_id,"option_item_id");
                        if (index === -1){
                            option_list.push({
                                id: option.id,
                                name: option.name,
                                price: option.price,
                                qty: option.qty,
                                option_id: option.option_id,
                                option_item_id: option.option_item_id,
                                order_product_id: option.order_product_id,
                            });
                        }else {
                            option_list[index].qty += option.qty;
                            option_list[index].price += option.price;
                        }
                    });
                }
            });

            last_product.options = option_list;
            last_product.qty = qty;
            last_product.price = price;
            merge_products.push(last_product);
        })

        print_data.products = merge_products.concat(catering_products);
        //End
        // helper.log(print_data,"PRINT DATA LAST")
        return print_data;
    }

    return invoice;
})();


