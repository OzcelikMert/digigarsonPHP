<div class="modal fade" id="modal_discount" tabindex="-1" role="dialog" aria-labelledby="modal_discount" aria-hidden="true">
    <div class="modal-dialog modal-xxl modal-lg" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h3 class="modal-title" id="modal_discount_title"><lang>DISCOUNT_COUNT</lang></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <form id="modal_discount_form">
                <!-- Modal Body -->
                <div class="modal-body p-0">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 form-group">
                                <label><lang>DESCRIPTION</lang></label>
                                <input type="text" name="comment" class="form-input">
                                <small class="form-text text-muted"><lang>DISCOUNT_APPLYING</lang></small>
                            </div>
                            <div class="col-6 form-group">
                                <label><lang>QUANTITY</lang></label>
                                <input type="number" step="0.01" min="0" name="price" class="form-input" required>
                                <small class="form-text text-muted"><lang>DISCOUNT_AMOUNT	</lang></small>
                            </div>
                            <div class="col-6 form-group pt-4">
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                    <label class="btn btn-secondary btn-lg w-50 active">
                                        <input type="radio" name="type" autocomplete="off" value="1" checked> <i class="fa fa-money-bill-alt"></i>
                                    </label>
                                    <label class="btn btn-secondary btn-lg w-50">
                                        <input type="radio" name="type" value="2" autocomplete="off"> <i class="fa fa-percent"></i>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-lg float-right w-25"><lang>ADD</lang></button>
                </div>
            </form>
        </div>
    </div>
</div>

