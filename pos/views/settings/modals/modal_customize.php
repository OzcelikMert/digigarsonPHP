<div class="modal fade" id="modal_customize" tabindex="-1" role="dialog" aria-labelledby="modal_customize" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h3 class="modal-title">
                    <lang>PERSONALISE</lang>
                </h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <form id="modal_customize_form">
                <!-- Modal Body -->
                <div class="modal-body p-0 py-3">
                    <div class="container">
                        <div class="form-check my-3">
                            <input class="checkbox-md form-check-input" type="checkbox" name="active_trigger_product_edit" id="active_trigger_product_edit">
                            <label class="form-check-label pt-1 pl-4" for="active_trigger_product_edit">
                                <lang>TRIGGER_PRODUCT_OPTION</lang>
                            </label>
                        </div>

                        <div class="form-check my-3">
                            <input class="checkbox-md form-check-input" type="checkbox" name="barcode_system" id="barcode_system">
                            <label class="form-check-label pt-1 pl-4" for="barcode_system">
                                <lang>BARCODE_TRANSACTION</lang>(BETA)
                            </label>
                        </div>

                        <div class="form-check my-3">
                            <input class="checkbox-md form-check-input" type="checkbox" name="notifications" id="notifications">
                            <label class="form-check-label pt-1 pl-4" for="notifications">
                                <lang>NOTIFICATIONS_AND_SERVICES</lang>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="submit" class="btn bg-c1 text-c6">
                        <lang>SAVE</lang>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>