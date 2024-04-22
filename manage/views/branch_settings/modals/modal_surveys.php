<div class="modal fade" id="modal_surveys" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-xxl modal-lg" role="document">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h3 class="modal-title" id="modal_new_account_title"><lang>SURVEY_SETTINGS</lang></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <form class="e_surveys">
                <!-- Modal Body -->
                <div class="modal-body p-0">
                    <div class="card-body pt-1 row">
                        <input type="hidden" name="id" value="0">

                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-8">
                                    <label><lang>SURVEY_TITLE</lang></label>
                                    <input  name="name" type="text" class="form-input" required>
                                </div>
                                <div class="col-4">
                                    <label><lang>TYPE</lang></label>
                                    <select name="type" class="form-input" required>
                                        <option value="">Seçim Yapınız</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 pt-4">
                            <button class="btn btn-s1 e_edit_btn"><lang>NEW_ADD</lang></button>
                            <button style="display: none" type="button" function="4" class="btn btn-s2 e_cancel_btn" ><lang>CANCEL</lang></button>
                        </div>

                        <div class="col-12 pt-4">
                            <h3><lang>SAVED_SURVEYS</lang></h3>
                            <table class="table table-striped">
                                <tr>
                                    <th width="120px"><lang>EDIT</lang></th>
                                    <th width="120px"><lang>DELETE</lang></th>
                                    <th><lang>SURVEY</lang></th>
                                    <th><lang>TYPE</lang></th>
                                </tr>
                                <tbody class="e_survey_table">
                                    <tr>

                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer"></div>

            </form>

        </div>
    </div>
</div>