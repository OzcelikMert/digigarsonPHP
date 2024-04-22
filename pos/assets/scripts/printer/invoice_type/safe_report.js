let SafeReport =  (function() {
    let InvoiceInfo = [];
    let OrderDetails = [];
    let CancelOrderDetails = [];
    let PaymentDetails = [];

    function SafeReport(invoiceInfo, orderDetails, cancelOrderDetails, paymentDetails){
        InvoiceInfo = invoiceInfo;
        OrderDetails = orderDetails;
        CancelOrderDetails = cancelOrderDetails;
        PaymentDetails = paymentDetails;
    }

    function Header(){
        return `
            <div class="header">
                <span class="font-size-xxm bold text-center mb-5 w-100 d-block">${InvoiceInfo.BranchName}</span>
                <span class="font-size-xxm bold text-center border-top border-xs w-100 d-block">${InvoiceInfo.GroupName}</span>
                <span class="font-size-xxs bold text-left w-100 d-block">Kasa Açılış: ${InvoiceInfo.StartDate + ` ` + InvoiceInfo.StartTime}</span>
                <span class="font-size-xxs bold text-left w-100 d-block">Kasa Kapanış: ${InvoiceInfo.EndDate + ` ` + InvoiceInfo.EndTime}</span>
                <span class="font-size-xxs bold text-left w-100 d-block">Sipariş Miktarı: ${InvoiceInfo.OrderCount}</span>
                <span class="font-size-xxs bold text-left w-100 d-block">Rapor No: ${InvoiceInfo.ReportID}</span>
            </div>
        `;
    }

    function Body(){
        return `
            <div class="body border-top border-xs">
                ${Payments() + Products() + ProductsCancelled()}
            </div>
        `;
    }

    function Payments(){
        let Payments = `
            <div class="payments">
                <span class="font-size-xs bold text-center w-100 d-block">Ödeme Bilgileri</span>
                <table width="100%">
                    <tr>
                        <th width="40%">
                            <span class="font-size-xs bold text-left w-100 d-block">Ödeme Tipi</span>
                        </th>
                        <th width="60%" align="Left">
                            <span class="font-size-xs bold w-100 d-block">Fiyat</span>
                        </th>
                    </tr>
        `;

        PaymentDetails.Values.forEach(Value => {
            Payments += `
                <tr>
                    <td>
                        <span class="font-size-xs text-left w-100 d-block">${Value.PaymentName}</span>
                    </td>
                    <td>
                        <span class="font-size-xs text-left bold text-right w-100 d-block">${parseFloat(Value.Price).toFixed(2)}<span class="lighter">${InvoiceInfo.Currency}</span></span>
                    </td>
                </tr>
            `;
        });

        PaymentDetails.TotalValues.forEach((TotalValue, Index) => {
            let BorderClass = (Index === 0) ? `class="border-xs border-top"` : ``;
            Payments += `
                <tr>
                    <td ${BorderClass}>
                        <span class="font-size-xs text-left w-100 d-block">${TotalValue.PaymentName}</span>
                    </td>
                    <td ${BorderClass}>
                        <span class="font-size-xs text-left bold text-right w-100 d-block">${parseFloat(TotalValue.Price).toFixed(2)}<span class="lighter">${InvoiceInfo.Currency}</span></span>
                    </td>
                </tr>
            `;
        });

        Payments += `
                </table>
            </div>
        `;

        return Payments;
    }

    function Products(){
        let Products = `
            <div class="products border-xs border-top">
                <span class="font-size-xs bold text-center mt-2 w-100 d-block">Satılan Ürün Bilgileri</span>
                <table width="100%">
                    <tr>
                        <th width="75%">
                            <span class="font-size-xs bold text-left w-100 d-block">Ürün</span>
                        </th>
                        <th width="25%" align="left">
                            <span class="font-size-xs bold w-100 d-block">Fiyat</span>
                        </th>
                    </tr>
        `;

        OrderDetails.Values.forEach(Value =>{
            Products += `
                <tr>
                    <td>
                        <span class="font-size-xxxs text-left w-100 d-block">${Value.Qty + `x ` + Value.ProductName}</span>
                    </td>
                    <td>
                        <span class="font-size-xxs text-left bold text-right w-100 d-block">${parseFloat(Value.Price).toFixed(2)}<span class="lighter">${InvoiceInfo.Currency}</span></span>
                    </td>
                </tr>
            `;
            // Check Quantity
            Products += (isEmpty(Value.quantity_id) || Value.quantity_id < 2) ? ``
                : `
                    <tr>
                        <td colspan="2">
                            <span class="font-size-xxxs text-left ml-2 w-100 d-block">•(${Value.quantity+` `+Value.quantity_name})</span>
                        </td>
                    </tr>
                `;
            // Check Options
            Value.ProductOptions.forEach(ProductOption =>{
                Products += (ProductOption.Price === 0) ? ``
                    : `
                        <tr>
                            <td>
                                <span class="font-size-xxxs text-left ml-2 w-100 d-block">•${ProductOption.Qty+`x `+ProductOption.Name}</span>
                            </td>
                            <td>
                                <span class="font-size-xxs text-left bold text-left w-100 d-block">${parseFloat(ProductOption.Price).toFixed(2)}<span class="lighter">${InvoiceInfo.Currency}</span></span>
                            </td>
                        </tr>
                    `;
            });
            // Check Subtotal
            Products += (Value.Price === Value.Subtotal) ? ``
                : `
                    <tr>
                        <td>
                            <span class="font-size-xxxs text-left w-100 d-block">Ara Toplam</span>
                        </td>
                        <td>
                            <span class="font-size-xxs text-left bold text-right w-100 d-block">${parseFloat(Value.Subtotal).toFixed(2)}<span class="lighter">${InvoiceInfo.Currency}</span></span>
                        </td>
                    </tr>
                `;
        });

        Products += `
                </table>
                <table class="mt-2" width="100%">
                    <tr>
                        <th width="30%"></th>
                        <th width="70%"></th>
                    </tr>
                    <tr>
                        <td><span class="font-size-xxs bold text-left w-100 d-block">T. Miktar</span></td>
                        <td><span class="font-size-xs bold text-right w-100 d-block">${OrderDetails.TotalQty}</span></td>   
                    </tr>
                    <tr>
                        <td><span class="font-size-xs bold text-left w-100 d-block">T. Fiyat</span></td>
                        <td><span class="font-size-xm bold text-right w-100 d-block">${parseFloat(OrderDetails.TotalPrice).toFixed(2)}<span class="lighter">${InvoiceInfo.Currency}</span></span></td>   
                    </tr>
                </table>
            </div>
        `;

        return Products;
    }

    function ProductsCancelled(){
        let ProductsCancelled = `
            <div class="products-cancelled border-xs border-top">
                <span class="font-size-xs bold text-center mt-2 w-100 d-block">İptal Ürün Bilgileri</span>
                <table width="100%">
                    <tr>
                        <th width="75%">
                            <span class="font-size-xs bold text-left w-100 d-block">Ürün</span>
                        </th>
                        <th width="25%" align="Left">
                            <span class="font-size-xs bold w-100 d-block">Fiyat</span>
                        </th>
                    </tr>
        `;

        CancelOrderDetails.Values.forEach(Value =>{
            ProductsCancelled += `
                <tr>
                    <td>
                        <span class="font-size-xxxs text-left w-100 d-block">${Value.Qty + `x ` + Value.ProductName}</span>
                    </td>
                    <td>
                        <span class="font-size-xxs text-left bold text-right w-100 d-block">${parseFloat(Value.Price).toFixed(2)}<span class="lighter">${InvoiceInfo.Currency}</span></span>
                    </td>
                </tr>
            `;
            // Check Quantity
            ProductsCancelled += (isEmpty(Value.quantity_id) || Value.quantity_id < 2) ? ``
                : `
                    <tr>
                        <td colspan="2">
                            <span class="font-size-xxxs text-left ml-2 w-100 d-block">•(${Value.quantity+` `+Value.quantity_name})</span>
                        </td>
                    </tr>
                `;
            // Check Options
            Value.ProductOptions.forEach(ProductOption =>{
                ProductsCancelled += (ProductOption.Price === 0) ? ``
                    : `
                        <tr>
                            <td>
                                <span class="font-size-xxxs text-left ml-2 w-100 d-block">•${ProductOption.Qty+`x `+ProductOption.Name}</span>
                            </td>
                            <td>
                                <span class="font-size-xxs text-left bold text-left w-100 d-block">${parseFloat(ProductOption.Price).toFixed(2)}<span class="lighter">${InvoiceInfo.Currency}</span></span>
                            </td>
                        </tr>
                    `;
            });
            // Check Subtotal
            ProductsCancelled += (Value.Price === Value.Subtotal) ? ``
                : `
                    <tr>
                        <td>
                            <span class="font-size-xxxs text-left w-100 d-block">Ara Toplam</span>
                        </td>
                        <td>
                            <span class="font-size-xxs text-left bold text-right w-100 d-block">${parseFloat(Value.Subtotal).toFixed(2)}<span class="lighter">${InvoiceInfo.Currency}</span></span>
                        </td>
                    </tr>
                `;
        });

        ProductsCancelled += `
                </table>
                <table class="mt-2" width="100%">
                    <tr>
                        <th width="30%"></th>
                        <th width="70%"></th>
                    </tr>
                    <tr>
                        <td><span class="font-size-xxs bold text-left w-100 d-block">T. Miktar</span></td>
                        <td><span class="font-size-xs bold text-right w-100 d-block">${CancelOrderDetails.TotalQty}</span></td>   
                    </tr>
                    <tr>
                        <td><span class="font-size-xs bold text-left w-100 d-block">T. Fiyat</span></td>
                        <td><span class="font-size-xm bold text-right w-100 d-block">${parseFloat(CancelOrderDetails.TotalPrice).toFixed(2)}<span class="lighter">${InvoiceInfo.Currency}</span></span></td>   
                    </tr>
                </table>
            </div>
        `;

        return ProductsCancelled;
    }

    function Bottom(){
        return `
            <div class="bottom border-top border-xs">
                <span class="font-size-xxs text-center w-100 d-block">
                    <span class="bold">Digigarson</span>'u kullandığınız için teşekkür ederiz. 
                    <span class="bold">www.digigarson.com</span>
                </span>
            </div>
        `;
    }

    SafeReport.prototype.Invoice = function(){
        return `
            <div class="invoice">
                ${Header() + Body() + Bottom()}
            </div>
        `;
    }

    return SafeReport;
})();