<div class="modal fade" id="modal_invoice_edit" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-xxl modal-lg" role="document">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h3 class="modal-title"><lang>RECEIPT_EDIT</lang></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <form id="modal_invoice_edit_form">

                <!-- Modal Body -->
                <div class="modal-body p-0">
                    <div class="container p-3">
                        <div class="e_modal_invoice_edit_items row">

                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <b><lang>TOTAL</lang></b>
                                    </div>
                                    <div class="col-md-6">
                                        <input class="form-input" name="total" type="number" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mt-1 mb-1">
                                <div class="row">
                                    <div class="col-md-6">
                                        <b>Kalan</b>
                                    </div>
                                    <div class="col-md-6">
                                        <input class="form-input" name="total_missing" type="number" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success"><lang>UPDATE</lang></button>
                </div>

            </form>
        </div>
    </div>
</div>