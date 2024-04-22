let Kitchen =  (function() {
    let products_infos = [];
    let invoice_info = [];
    let class_types = {
        products: "font-size-xs text-left w-100 d-block",
        options: "font-size-xs text-left ml-2 w-100 d-block",
        quantity: "font-size-xs text-left ml-2 w-100 d-block",
        comment: "font-size-xs text-left ml-2 w-100 d-block",
        
        invoice_no: "font-size-xs bold text-left w-100 d-block",
        group_name: "font-size-xs bold text-center mb-5 w-100 d-block",
        date_time: "font-size-xs bold text-left w-100 d-block",
        table_name: "font-size-xs bold text-left w-100 d-block",
        user_name: "font-size-xs bold text-left w-100 d-block",
        bottom: "font-size-xxs text-center w-100 d-block",
    }


    function Kitchen(info,data){
        invoice_info = info;
        invoice_info["total"] = 0;
        invoice_info["height"] = 0;
        products_infos = data;
        products_infos.materials = Array();

        if (info.type !== invoice.print_invoce_type.CANCEL){
            products_infos.top_title = "Sipariş Bilgileri";
            products_infos.invoice_no = `<span class="${class_types.invoice_no}">Fiş No: ${invoice_info.OrderID}</span>`;
        }else {
            products_infos.top_title = "İptal olan ürünler";
            products_infos.group_name = "İPTAL FİŞİ";
            products_infos.invoice_no = "";
        }
    }
    function Header(){
        invoice_info.height +=  30;
        return `
            <div class="header">
                <span class="${class_types.group_name}">${products_infos.group_name}</span>
                <span class="${class_types.date_time}">Tarih: ${invoice_info.OrderDate +` `+ invoice_info.OrderTime}</span>
                <span class="${class_types.table_name}">Masa: ${products_infos.table_name}</span>
                ${(invoice_info.UserName !== null ? `<span class="${class_types.user_name}">Kişi: ${invoice_info.UserName}</span>` : "")}
                ${products_infos.invoice_no}
            </div>
        `;
    }
    function Body(){
        let values = "";

        products_infos.products.forEach(product =>{
            invoice_info.total += product.price;
            values += element_creator(class_types.products, (`${product.qty} x ${product.name}`));
            invoice_info.height +=  4.6;

            if (product.quantity_id !== helper.db.quantity_types.PIECE){
                let quantity_name = array_list.find(main.data_list.PRODUCT_QUANTITY_TYPES,product.quantity_id,"id").name
                values += element_creator(class_types.quantity, (` ${product.quantity} ${quantity_name}`));
                invoice_info.height +=  4.6;
            }
            product.materials = [];
            product.options.forEach(option =>{
                if (option.type === helper.db.product_option_group_types.MATERIALS) {
                    product.materials.push(option.name);
                }else {
                    let qty = (product.qty > 1) ? `${product.qty}x ` : "";
                    values += element_creator(class_types.options, (`• ${qty}${option.name}`));
                    invoice_info.height +=  4.6;
                }
            });
            
            // Options Materials
            if (product.materials.length > 0)  values += element_creator(class_types.options, (`<b>Çıkarılıcak:</b> ${product.materials.join(", ")}`));
            invoice_info.height +=  10;
            // Note
            if (typeof product.comment == "string" && product.comment.length > 0)
                values += element_creator(class_types.comment, (`Not: ${product.comment}`));
            invoice_info.height +=  4.6;
        });

        return `
            <div class="values border-top border-xs">
                <div class="products">
                    <span class="font-size-xs bold text-center w-100 d-block">${products_infos.top_title}</span>
                    <table class="w-100">
                        <thead><tr><th><span class="font-size-xs bold text-left w-100 d-block">Ürün</span></th></tr></thead>
                        <tbody>${values}</tbody>
                    </table>
                </div>
            </div>
        `;
    }
    function element_creator(class_list, value){
        return `<tr><td><span class="${class_list}">${value}</span></td></tr>`;
    }

    Kitchen.prototype.invoice = function(){
        let html = `<div class="invoice">${Header() + Body()} </div>`;
        invoice_info.height = (invoice_info.height < 80) ? 80 : invoice_info.height;
        console.log("height: "+invoice_info.height);
        return {
            html:html,
            height:invoice_info.height,
            table:products_infos.table_name,
            group: products_infos.group_name
        };
    }
    return Kitchen;
})();

