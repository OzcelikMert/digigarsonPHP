<div class="modal fade" id="modal_invoice_show" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-xxl modal-lg" role="document">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h3 class="modal-title"><lang>RECEIPT_VIEW</lang></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-0">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="e_invoice_show_elements" style="overflow: auto; height: 550px;">
                                <iframe name="invoice_iframe" style="height: 100%; width: 100%; border: 0;"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button function="print" class="e_invoice_print_btn_modal btn btn-primary btn-lg w-50"><i class="fa fa-print"></i><lang>PRINT</lang></button>
            </div>

        </div>
    </div>
</div>