<div class="modal fade" id="modal_new_customer" tabindex="-1" role="dialog" aria-labelledby="modal_new_customer" aria-hidden="true">
    <div class="modal-dialog modal-xxl modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h3 class="modal-title" ><lang>NEW_CUSTOMER</lang></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <form id="modal_new_customer_form">

                <div class="modal-body p-0">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label><lang></lang></label>
                                        <input type="text" class="form-input" name="name" placeholder="İsim" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label><lang>TELEPHONE</lang></label>
                                        <input type="tel" class="form-input" name="phone" placeholder="Telefon" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label><lang>ADDRESS_TYPE</lang></label>
                                        <input type="text" class="form-input" name="address_type" placeholder="Adres Tipi" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label><lang>TITLE</lang></label>
                                        <input type="text" class="form-input" name="title" placeholder="Başlık" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label><lang>CITY</lang> - <lang>DISTRICT</lang> - <lang>BLOCK</lang> - <lang>NEIGHBORHOOD</lang></label>
                                    <select class="form-input medium-select col-12 mt-2" name="city" function="1" autocomplete="off" required><option value="-1"><lang>SELECT_CITY</lang></option></select>
                                    <select class="form-input medium-select col-12 mt-2" name="town" function="2" required><option value="-1"><lang>SELECT_DISTRICT</lang></option></select>
                                    <select class="form-input medium-select col-12 mt-2" name="district" function="3" required><option value="-1"><lang>SELECT_BLOCK</lang>.</option></select>
                                    <select class="form-input medium-select col-12 mt-2" name="neighborhood" function="4" required><option value="-1"><lang>SELECT_NEIGHBORHOOD</lang>.</option></select>
                                </div>

                                <div class="form-group">
                                    <label><lang>STREET</lang></label>
                                    <input type="text" name="street" class="form-control" placeholder="1234 Main St" required>
                                </div>
                                <div class="form-group">
                                    <label><lang>ADDRESS_DIRECTIONS</lang></label>
                                    <input type="text" name="address_detail" class="form-control" placeholder="">
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label><lang>BUILDING_NO</lang></label>
                                        <input type="text"  class="form-input" name="building_no" placeholder="Bina No" required>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label><lang>FLOOR</lang></label>
                                        <input type="text" class="form-input" name="floor" required>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label><lang>APARTMENT_NUMBER</lang></label>
                                        <input type="text" class="form-input" name="apartment_no" required>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="e_caller_cancel btn btn-danger"><lang>CANCEL</lang></button>
                    <button type="submit" class="btn btn-success"><lang>APPROVE</lang></button>
                </div>
            </form>
        </div>
    </div>
</div>
