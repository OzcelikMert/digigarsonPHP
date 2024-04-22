<div class="modal fade" id="modal_settings_takeaway_min_total_and_time" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-xxl modal-lg" role="document">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h3 class="modal-title" id="modal_new_account_title"><lang>BRANCH_NAME</lang></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <form>
                <!-- Modal Body -->
                <div class="modal-body p-0">
                    <div class="card-body pt-1 row">
                        <div class="col-md-12">
                            <label><lang>MIN_AMOUNT</lang></label>
                            <input area="min_time_input" name="min_total" type="number" step="0.01" class="form-input" required>
                        </div>
                        <div class="col-md-12 mt-2">
                            <label><lang>AVERAGE_TIME</lang> (<lang>MINUTE</lang>)</label>
                            <input  area="min_total_input" name="min_time" type="text" class="form-input" required>
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