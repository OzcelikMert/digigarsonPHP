let Safe = (function() {
    let products_infos = [];
    let invoice_info = [];
    let class_types = {
        products: "font-size-xxs bold text-left w-100 d-block",
        options_name: "font-size-xxxs bold text-left ml-2 w-100 d-block",
        options_price: "font-size-xxxs bold text-left bold text-right w-100 d-block",
        quantity: "font-size-xxxs bold text-left ml-2 w-100 d-block",
        product_price: "font-size-xxs text-left bold text-right w-100 d-block",
        catering: "font-size-xxxs text-left bold text-right w-100 d-block",
    }

    function Safe(data,address = ""){
        let today = new Date();
        let date = today.getFullYear()+'/'+(today.getMonth()+1)+'/'+today.getDate();
        let time = today.getHours() + ":" + today.getMinutes();

        invoice_info = {
            BranchName: (typeof app === "undefined") ? main.data_list.BRANCH_INFO.name : app.printer.title,
            OrderDate: date,
            OrderTime: time,
            OrderID: data.orders[0].no,
            Currency: "₺",
            Total: 0,
            Address: address,
            Table: data.table,
            UserName: data.user_name,
            discount: 0,
            height: 0
        }
        products_infos = data;
    }

    function Header(){
        invoice_info.height +=  65;
        if (invoice_info.OrderID !=="") invoice_info.height += 5;
        if (invoice_info.UserName !=="") invoice_info.height += 5;
        return `
            <div class="header">
                ${(invoice_info.BranchName !=="") ? `<span class="font-size-xxm bold text-center mb-5 w-100 d-block">${invoice_info.BranchName}</span>` : ""} 
                <span class="font-size-xs bold text-left w-100 d-block">Tarih: ${invoice_info.OrderDate +` `+ invoice_info.OrderTime}</span>
                <span class="font-size-xs bold text-left w-100 d-block">Masa: ${invoice_info.Table}</span>
                ${(invoice_info.OrderID !=="") ? `<span class="font-size-xs bold text-left w-100 d-block">No: ${invoice_info.OrderID}</span>` : ""}
                ${invoice_info.UserName !== "" ? `<span class="font-size-xs bold text-left w-100 d-block">Kişi: ${invoice_info.UserName}</span>` : ""}
                <div class="w-100">${invoice_info.Address}</div>
            </div>
        `;
    }

    function Body(){
        let values = "";

        products_infos.products.forEach(product =>{
            invoice_info.Total += product.price;
            console.log(product)
            if (product.price < 0) {
                invoice_info.discount += product.price;
                return;
            }
            let classes = (parseFloat(product.price) === 0 ) ? class_types.catering : class_types.product_price;
            console.log(product.status);
            let display_price = (product.status === helper.db.order_product_status_types.CATERING)
                ? `İkram`
                : (product.status === helper.db.order_product_status_types.CANCEL)
                    ? "İptal"
                    : parseFloat(product.price).toFixed(2);
            values += element_creator([class_types.products,classes],[(`${product.qty}x ${product.name}`),display_price]);
            invoice_info.height +=  4.2;

            if ((product.quantity_id !== helper.db.quantity_types.PIECE)){
                if(typeof app !== "undefined" && !app.printer.settings.showQuantityName) return;
               /* if (![helper.db.quantity_types.KILOGRAM].includes(product.quantity_id)) {
                    console.log(`QTY: ${product.qty} quant: ${product.quantity}`)
                    qty = product.qty * parseFloat(product.quantity);
                }else {*/
                let quantity_name = array_list.find(main.data_list.PRODUCT_QUANTITY_TYPES,product.quantity_id,"id").name
                values += `<tr><td colspan="2"> <span class="${class_types.quantity}">• ${product.quantity+" "+quantity_name}</span></td></tr>`;
                invoice_info.height +=  4.2;
            }

            product.options.forEach(option =>{
                let display_price_option = (product.price === 0) ? `` : parseFloat(option.price).toFixed(2)
                if(option.price > 0 || invoice_info.Address !== "")
                    values +=  element_creator([class_types.options_name,class_types.options_price],[`•${option.qty} x ${option.name}`,display_price_option])
                invoice_info.height +=  4.2;
            });
        });

        let discount_html = (invoice_info.discount === 0) ? "" : `
            <tr>
                <td width="30%"><span class="font-size-xxxs bold text-left w-100 d-block">Ara Toplam</span></td>
                <td width="70%"><div class="font-size-xs bold text-right w-100 d-block">${(invoice_info.Total + (invoice_info.discount * -1)).toFixed(2)}<span class="lighter">${invoice_info.Currency}</span></div></td>   
            </tr>
            <tr>
                <td width="30%"><span class="font-size-x bold text-left w-100 d-block">İskonto</span></td>
                <td width="70%"><div class="font-size-xs bold text-right w-100 d-block">${(invoice_info.discount * -1).toFixed(2)}<span class="lighter">${invoice_info.Currency}</span></div></td>   
            </tr>
        `;
        invoice_info.height += (invoice_info.discount === 0) ? 25 : 0;

        return `
            <div class="values border-top border-xs">
                <div class="products">
                    <span class="font-size-xs bold text-center w-100 d-block">Sipariş Bilgileri</span>
                    <table width="100%">
                    <thead><tr>
                    <th width="75%"><span class="font-size-xs bold text-left w-100 d-block">Ürün</span></th>
                    <th width="25%" align="Left"><span class="font-size-xs bold w-100 d-block">Fiyat</span></th>
                    </tr></thead>
                    <tbody>${values}</tbody>
                    </table>
                <table class="mt-2" width="100%">
                    ${discount_html}
                    <tr>
                        <td width="30%"><span class="font-size-xs bold text-left w-100 d-block">Toplam</span></td>
                        <td width="70%"><div class="font-size-xm bold text-right w-100 d-block">${parseFloat(invoice_info.Total).toFixed(2)}<span class="lighter">${invoice_info.Currency}</span></div></td>   
                    </tr>
                </table>
                </div>
                </div>
        `;
    }

    function element_creator(class_list, value){
        return `<tr><td><span class="${class_list[0]}">${value[0]}</span></td><td><span class="${class_list[1]}">${value[1]}</span></td></tr>`;
    }
    function Bottom(){
        return `
            <div class="bottom border-top border-xs">
                <span class="font-size-xxxs text-center w-100 d-block">
                    <span>MimiPos</span>'u kullandığınız için teşekkür ederiz. 
                    <span>www.mimipos.com</span>
                </span>
            </div>
        `;
    }

    Safe.prototype.invoice = function(){
        let result = {html:`<div class="invoice">${Header() + Body() + Bottom()}</div>`, height:invoice_info.height}
        invoice_info.height = (invoice_info.height < 80) ? 81 : invoice_info.height;
        console.log("height: "+invoice_info.height);
        return result;
    }

    return Safe;
})();