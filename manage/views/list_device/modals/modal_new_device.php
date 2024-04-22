<div class="modal fade" id="modal_new_device" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-xxl modal-lg" role="document">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h3 class="modal-title" id="modal_new_device_title"><lang>DEVICE_EDIT</lang></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <form id="form_device">

                <!-- Modal Body -->
                <div class="modal-body p-0">
                    <div class="card-body pt-1 row">

                        <div class="col-md-12">
                            <label><lang>ACCOUNT_NAME</lang></label>
                            <input name="name" type="text" class="form-input" required>
                        </div>

                        <div class="col-md-8">
                            <label><lang>DEVICE_TYPE</lang></label>
                            <select class="form-input" name="type">
                                <optgroup class="e_types"></optgroup>
                            </select>
                        </div>

                        <div class="col-md-4 mt-4">
                            <label> <input class="checkbox-lg" type="checkbox" name="is_connect"><lang>CONNECTED</lang></label>
                            <label> <input class="checkbox-lg ml-1" type="checkbox" name="caller_id_active"><lang>CALLER_ID</lang></label>
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