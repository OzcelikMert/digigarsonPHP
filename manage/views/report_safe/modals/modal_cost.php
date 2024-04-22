<div class="modal fade" id="modal_cost" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-xxl modal-lg" role="document">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h3 class="modal-title" id="modal_cost_title"><lang>ADD_COST</lang></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <form id="modal_cost_form">

                <!-- Modal Body -->
                <div class="modal-body p-0">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <label><lang>DESCRIPTION</lang></label>
                                <input type="text" name="comment" class="form-input" required>
                                <small class="form-text text-muted"><lang>ENTER_DESC_COST</lang></small>
                            </div>
                            <div class="col-12">
                                <label>Fiyat</label>
                                <input type="number" step="0.01" name="price" min="0.01" class="form-input" required>
                                <small class="form-text text-muted"><lang>ENTER_HOW_MUCH_COST</lang></small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success"><lang>ADD</lang></button>
                </div>

            </form>
        </div>
    </div>
</div>