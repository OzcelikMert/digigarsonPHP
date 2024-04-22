<div class="modal fade" id="modal_delete_product" tabindex="-1" role="dialog" aria-labelledby="modal_delete_product" aria-hidden="true">
    <div class="modal-dialog modal-xxl modal-lg" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h3 class="modal-title" id="payment_delete_product"><lang>DELETE_PRODUCT</lang></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <form id="modal_delete_form">
                <!-- Modal Body -->
                <div class="modal-body p-0">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 form-group">
                                <label><lang>DESCRIPTION</lang></label>
                                <input type="text" name="comment" class="form-input" required>
                                <small class="form-text text-muted"><lang>DELETE_PRODUCT_DESCRIPTION</lang></small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <table class="table">
                                    <thead class="thead-dark">
                                    <tr>
                                        <th scope="col" width="20%"><lang>QUANTITY</lang></th>
                                        <th scope="col" width="60%"><lang>PRODUCT</lang></th>
                                        <th scope="col" width="20%"><lang>PRICE</lang></th>
                                    </tr>
                                    </thead>
                                    <tbody class="e_delete_products"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-s3 btn-lg float-right w-25"><lang>DELETE</lang></button>
                </div>
            </form>
        </div>
    </div>
</div>

