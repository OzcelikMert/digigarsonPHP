<div class="modal fade" id="modal_account_yemek_sepeti" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-xxl modal-lg" role="document">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h3 class="modal-title"><lang>ACCOUNT</lang> (Yemek Sepeti)</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <form id="form_account_yemek_sepeti" type="1">
                <!-- Modal Body -->
                <div class="modal-body p-0">
                    <div class="card-body pt-1 row">
                        <div class="e_account_info col-md-12">
                            <label><lang>USER_NAME</lang></label>
                            <input name="user_name" type="text" class="form-input" required>
                        </div>
                        <div class="e_account_info col-md-12 mt-2">
                            <label><lang>PASSWORD</lang></label>
                            <input name="password" type="password" class="form-input" required>
                        </div>
                        <div class="e_account_status col-md-12 mt-2">
                            <label>Durum</label>
                            <select name="status" class="form-input" required>
                                <option value="1"><lang>OPEN</lang></option>
                                <option value="0"><lang>CLOSED</lang></option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="submit" class="btn bg-c1"><lang>SAVE</lang></button>
                </div>

            </form>

        </div>
    </div>
</div>