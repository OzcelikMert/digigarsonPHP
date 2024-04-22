<div class="modal fade" id="modal_trust_account_payment" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-xxl modal-lg" role="document">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h3 class="modal-title" id="modal_trust_account_payment_title"><lang>PAY</lang></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <form id="modal_trust_account_payment_form">

                <!-- Modal Body -->
                <div class="modal-body p-0">
                    <div class="container payment-types">
                        <div class="row">
                            <div class="col-12">
                                <label><lang>DESCRIPTION</lang></label>
                                <input type="text" name="comment" class="form-input">
                                <small class="form-text text-muted">Ödeme hakkında kısa bir açıklama giriniz.</small>
                            </div>
                            <div class="col-12">
                                <label><lang>PRICE</lang></label>
                                <input type="number" step="0.01" name="price" min="0" class="form-input" required>
                                <small class="form-text text-muted">Ne kadarlık bir ödeme yapılacak buraya giriniz.</small>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <button type="submit" class="col-6 btn btn-primary" type-id="1"><lang>CASH</lang></button>
                            <button type="submit" class="col-6 btn btn-primary" type-id="2"><lang>CREDIT_CARD</lang></button>
                        </div>
                        <div class="e_modal_trust_account_payment_types row"></div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer"></div>

            </form>
        </div>
    </div>
</div>