<div class="modal fade" id="modal_catering_product" tabindex="-1" role="dialog" aria-labelledby="modal_catering_product" aria-hidden="true">
    <div class="modal-dialog modal-xxl modal-lg" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h3 class="modal-title" id="payment_catering_product"><lang>PRODUCT_CATERING</lang></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <form id="modal_catering_form">
                <!-- Modal Body -->
                <div class="modal-body p-0">
                    <div class="container">
                        <div class="row">
                            <div class="col-6 form-group">
                                <label><lang>CATERING_ONE_WHO</lang></label>
                                <select class="form-input" name="owner"></select>
                                <small class="form-text text-muted"><lang>CHOOSE_GIFT__GIVER</lang></small>
                            </div>
                            <div class="col-6 form-group">
                                <label><lang>GIFTING_QUESTION</lang></label>
                                <select class="form-input" name="question"></select>
                                <small class="form-text text-muted"><lang>CHOOSE_WHY_GIFTING</lang></small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <table class="table">
                                    <thead class="thead-dark">
                                    <tr>
                                        <th scope="col" width="20%"><lang>QUANTITY</lang></th>
                                        <th scope="col" width="60%"><lang>PRODUCTS</lang></th>
                                        <th scope="col" width="20%"><lang>PRICE</lang></th>
                                    </tr>
                                    </thead>
                                    <tbody class="e_catering_products"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-s3 btn-lg float-right w-25"><lang>CATERING</lang></button>
                </div>
            </form>
        </div>
    </div>
</div>

