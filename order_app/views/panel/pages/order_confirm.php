<div id="page_order_confirm" class="pop-page" style="display: none">
    <!--===  TOP BAR  ===--->
    <div class="top-bar">
        <div class="left-btn icon"><a href="#" close_page="page_order_confirm"><i class="fa fa-arrow-left"></i></a></div>
        <div class="page-title"><span><lang>COMPLETE_ORDER</lang></span></div>
        <div class="right-btn icon"></div>
    </div>

    <div class="pop-page-in">
        <div class="order info">

            <div class="item address">
                <div class="title"><lang>ADDRESS_SELECTION</lang></div>
                <div class="alert alert-info m-0" area="address-alert" style="display: none">...</div>
                <div class="select">
                    <i class="icon fa fa-map-marker-alt"></i>
                    <select class="form-input" area="address-selection" required></select>
                    <span></span>
                </div>
            </div>

            <div class="item payment-types">
                <div class="title"><lang>PAYMENT_SELECTION</lang></div>
                <div class="alert alert-info m-0" area="payment-alert" style="display: none">...</div>
                <div class="select">
                    <i class="icon fa fa-credit-card"></i>
                    <select class="form-input" area="payment-selection" required></select>
                </div>
            </div>

            <div class="item note">
                <div class="title"><lang>ORDER_NOTE</lang></div>
                <div class="select">
                    <i class="icon fa fa-sticky-note"></i>
                    <input class="form-input" area="order-note" type="text" placeholder="Siparişiniz ile ilgili detayları belirtebilirsiniz">
                </div>
            </div>

            <div class="send">
                <button type="button" class="btn btn-s4  w-100 p-3 mt-2" disabled="disabled" function="send-takeaway"><lang>CONFIRM_ORDER</lang></button>
            </div>
        </div>

    </div>

</div>
