<div class="modal fade" id="modal_new_account" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-xxl modal-lg" role="document">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h3 class="modal-title" id="modal_new_account_title"><lang>EDIT_ACCOUNT</lang></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <form id="form_account">

                <!-- Modal Body -->
                <div class="modal-body p-0">
                    <div class="card-body pt-1 row">

                        <div class="col-md-8">
                            <label><lang>ACCOUNT_NAME</lang></label>
                            <input name="name" type="text" class="form-input" required>
                        </div>

                        <div class="col-md-4 mt-4">
                            <label> <input class="checkbox-lg" type="checkbox" name="active"><lang>ACTIVE</lang></label>
                        </div>

                        <div class="col-md-8">
                            <label><lang>PASSWORD</lang></label>
                            <input name="password" type="text" class="form-input">
                        </div>

                        <div class="col-md-12 mt-3">
                            <fieldset>
                                <legend class="text-success"><lang>AUTHORIZED</lang></legend>
                                <div class="col-12">
                                    <input type="text" class="e_search_permission form-input w-100" placeholder="Arama">
                                </div>
                                <div class="col-12 row mt-2 pl-5">
                                    <button type="button" class="btn btn-s6 col-3 w-85"><lang>WAITER</lang></button>
                                    <button type="button" class="btn btn-s6 col-3 w-85"><lang>CASHIER</lang> - 1</button>
                                    <button type="button" class="btn btn-s6 col-3 w-85"><lang>CASHIER</lang> - 2</button>
                                    <button type="button" class="btn btn-s6 col-3 w-85"><lang>CASHIER</lang> - 3</button>
                                </div>
                                <div class="e_permissions row p-2">

                                </div>
                            </fieldset>
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