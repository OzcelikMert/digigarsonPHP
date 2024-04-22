<div class="modal fade" id="modal_new_trust_account" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-xxl modal-lg" role="document">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h3 class="modal-title" id="modal_new_account_title"><lang>EDIT_ACCOUNT</lang></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <form id="form_trust_account">

                <!-- Modal Body -->
                <div class="modal-body p-0">
                    <div class="card-body pt-1 row">

                        <div class="col-md-6">
                            <label><lang>ACCOUNT_NAME</lang></label>
                            <input name="name" type="text" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label><lang>DISCOUNT</lang> (%)</label>
                            <input class="form-control" name="discount" type="number" step="0.01" min="0" max="100" value="0">
                        </div>

                        <div class="col-md-6">
                            <label><lang>ADDRESS</lang></label>
                            <input name="address" type="text" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label><lang>TELEPHONE</lang></label>
                            <input name="phone" type="text" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label><lang>TAX_NUM</lang></label>
                            <input name="tax_no" type="text" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label><lang>TAX_ADMIN</lang></label>
                            <input name="tax_administration" type="text" class="form-control">
                        </div>

                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button id="btn_set_category" type="submit" class="btn bg-c1">Kaydet</button>
                </div>

            </form>

        </div>
    </div>
</div>