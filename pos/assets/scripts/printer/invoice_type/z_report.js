let z_report =  (function() {
    let data,height;

    function z_report(get = {payments:[],trust_payments:[],cancel_products:[] }){
        height = 0;
        data = get;
        //data.info = {"currency": "₺", "safe_open": "safe_open","safe_close":"safe_close","no":"no",safe: 0}
        data.info.cancel_price = 0;
        data.cancel_products.forEach(function (product){
            data.info.cancel_price += product.price;
        })

    }

    function Header(){
        height = 90;
        console.log(data)
        if(data.info.safe !== 0 ){
            return `
            <div class="header">
                <span class="font-size-xxm bold text-center mb-5 w-100 d-block">Z RAPORU</span>
                <span class="font-size-xxs bold text-left w-100 d-block">Kasa Açılış: ${data.info.safe_open}</span>
                <span class="font-size-xxs bold text-left w-100 d-block">Kasa Kapanış: ${data.info.safe_close}</span>
                <span class="font-size-xs bold text-left w-100 d-block">Kasa No: ${data.info.safe}</span>
            </div>
            `;
        }else {
            return `
            <div class="header">
                <span class="font-size-xxm bold text-center mb-5 w-100 d-block">Z RAPORU</span>
                <span class="font-size-xxs bold text-left w-100 d-block">Z Raporu Tarih: ${(new Date()).toLocaleDateString('tr-TR')}</span>
            </div>
            `;
        }
    }

    function Body(){
        let Body = `
            <div class="body border-top border-xs">
                <div class="products">
                    <span class="font-size-xs bold text-center w-100 d-block">Ödeme Bilgileri</span>
                    <table width="100%">
                        <tr>
                            <th width="75%"><span class="font-size-xs bold text-left w-100 d-block">Ödeme Tipi</span></th>
                            <th width="25%" align="Left"><span class="font-size-xs bold w-100 d-block">Fiyat</span></th>
                        </tr>
        `;

        let total_safe_price = 0;
        let costs = 0.0;

        data.costs.forEach(function (payment){
            costs += payment.price;
        })
        Body += `
            <tr>
                <td><span class="font-size-xs text-left w-100 d-block">MASRAF</span></td>
                <td>
                    <span class="font-size-xs text-left bold text-right w-100 d-block">
                        ${ (-1 * costs).toFixed(2)}<span class="lighter">${data.info.currency}</span>
                    </span>
                </td>
            </tr>
        `;
        height += 6;

        data.payments.forEach(function (payment){
            //payment = {type:1, name: "NAKİT", total_price: 100.5}  Example
            height += 5.2;

            if (payment.type !== helper.db.payment_types.CANCEL){
                Body += `
                    <tr>
                        <td><span class="font-size-xs text-left w-100 d-block">${(payment.name).toUpperCase()}</span></td>
                        <td>
                            <span class="font-size-xs text-left bold text-right w-100 d-block">
                                ${parseFloat(payment.price).toFixed(2)}<span class="lighter">${data.info.currency}</span>
                            </span>
                        </td>
                    </tr>
                `;
                total_safe_price += parseFloat(payment.price);

                if (payment.type === helper.db.payment_types.CASH){
                    Body += `
                        <tr>
                            <td><span class="font-size-xs text-left w-100 d-block">${(payment.name).toUpperCase()} - MASRAF</span></td>
                            <td>
                                <span class="font-size-xs text-left bold text-right w-100 d-block">
                                    ${ (parseFloat(payment.price) + costs).toFixed(2)}<span class="lighter">${data.info.currency}</span>
                                </span>
                            </td>
                        </tr>
                    `;
                    total_safe_price += costs
                }
            }
        })


        data.trust_payments.forEach(function (payment){
            height += 5.2;
            Body += `
                <tr>
                    <td><span class="font-size-xs text-left w-100 d-block">VERESİYE (${(payment.name).toUpperCase()})</span></td>
                    <td>
                        <span class="font-size-xs text-left bold text-right w-100 d-block">
                            ${parseFloat(payment.price).toFixed(2)}<span class="lighter">${data.info.currency}</span>
                        </span>
                    </td>
                </tr>
            `;
            total_safe_price += parseFloat(payment.price);
        })

        height += 20;
        Body += `
        <tr>
            <td><span class="font-size-xs text-left w-100 d-block">İPTAL</span></td>
            <td><span class="font-size-xs text-left bold text-right w-100 d-block"> ${parseFloat(data.info.cancel_price).toFixed(2)}<span class="lighter">${data.info.currency}</span></span></td>
        </tr>
        
        <tr>
            <td><span class="font-size-xs text-left w-100 d-block">TOPLAM TUTAR</span></td>
            <td><span class="font-size-xs text-left bold text-right w-100 d-block"> ${total_safe_price.toFixed(2)}<span class="lighter">${data.info.currency}</span></span></td>
        </tr>

        
        <tr><td colspan='2'><hr></td></tr>
        <tr><td colspan='2'><span class="font-size-xs bold text-center w-100 d-block">Ürünler</span></td></tr>`;

        data.products.forEach(function (product){
            height += 4.8;

            Body += `
                <tr>
                    <td><span class="font-size-x text-left w-100 d-block">${product.qty+"x "+product.name.toUpperCase()}</span></td>
                    <td>
                        <span class="font-size-x text-left bold text-right w-100 d-block">
                            ${parseFloat(product.price).toFixed(2)}<span class="lighter">${data.info.currency}</span>
                        </span>
                    </td>
                </tr>
            `;
            //Piece id = 1
            if (product.quantity_id > 1) {
                let quantity_name = array_list.find(main.data_list.PRODUCT_QUANTITY_TYPES,product.quantity_id,"id").name;
                Body += `<tr><td colspan="2"><span class="font-size-xxs text-left ml-2 w-100 d-block">- ${product.quantity} ${quantity_name}</span></td></tr>`;
                height += 4.8;
            }

            product.options.forEach(option =>{
                height += 4.4;
                Body += `
                    <tr>
                        <td><span class="font-size-xxs text-left ml-2 w-100 d-block">• ${option.qty+`x `+option.name}</span></td>
                        <td>
                            <span class="font-size-xxs text-left bold text-right w-100 d-block">${parseFloat(option.price).toFixed(2)}
                            <span class="lighter">${data.info.currency}</span></span>
                        </td>
                    </tr>
                `;
            });
        })
        Body += `<tr><td colspan='2'><hr></td></tr> <tr><td colspan='2'><span class="font-size-xs bold text-center w-100 d-block">İptal Ürünler</span></td></tr>`;
        height += 10;

        data.cancel_products.forEach(function (product){
            height += 4.4;
            Body += `
                <tr>
                    <td><span class="font-size-x text-left w-100 d-block">${product.qty+"x "+product.name.toUpperCase()}</span></td>
                    <td>
                        <span class="font-size-x text-left bold text-right w-100 d-block">
                            ${parseFloat(product.price).toFixed(2)}<span class="lighter">${data.info.currency}</span>
                        </span>
                    </td>
                </tr>
            `;
            //Piece id = 1
            if (product.quantity_id > 1) {
                let quantity_name = array_list.find(main.data_list.PRODUCT_QUANTITY_TYPES,product.quantity_id,"id").name;
                Body += `<tr><td colspan="2"><span class="font-size-xxxs text-left ml-2 w-100 d-block">- ${product.quantity} ${quantity_name}</span></td></tr>`;
                height += 4.2;
            }
            product.options.forEach(option =>{
                height += 4.2;
                Body += `
                    <tr>
                        <td><span class="font-size-xxs text-left ml-2 w-100 d-block">• ${option.qty+`x `+option.name}</span></td>
                        <td>
                            <span class="font-size-xxs text-left bold text-right w-100 d-block">${parseFloat(option.price).toFixed(2)}
                            <span class="lighter">${data.info.currency}</span></span>
                        </td>
                    </tr>
                `;
            });
        })
        return Body + '</table></div></div>';
    }

    function Bottom(){
        return `
            <div class="bottom border-top border-xs">
                <span class="font-size-xxs text-center w-100 d-block">
                    <span class="bold">MimiPos</span>'u kullandığınız için teşekkür ederiz.
                </span>
            </div>
        `;
    }

    z_report.prototype.invoice = function(){
        let html = `
            <div class="invoice">
                ${Header() + Body() + Bottom()}
            </div>
        `;
        height = (height < 80) ? 80 : height;
        console.log("height: "+height);
        return {html:html,height:height}
    }

    return z_report;
})();