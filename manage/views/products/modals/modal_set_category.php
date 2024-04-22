<div class="modal fade" style="z-index: 1051;" id="modal_set_category" tabindex="-1" role="dialog" aria-labelledby="product_settings " aria-hidden="true">
    <div class="modal-dialog modal-xxl modal-lg" role="document">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h3 class="modal-title" id="product_settings_title"><lang>CATEGORY_EDIT_TITLE</lang></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <form id="form_set_category">
                <input type="hidden" name="id" value="0" data-default-value="0">
                <input type="hidden" name="function" value="2">
                <!-- Modal Body -->
                <div class="modal-body p-0">
                    <div class="card-body pt-1 row">
                        <div class="form row m-0" style="padding-bottom: 15px;border-bottom: 1px solid #80808059;">
                            <div class="col-md-4">
                                <label><lang>CATEGORY_NAME</lang></label>
                                <input name="name" type="text" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label><lang>UPPER_CATEGORY</lang></label>
                                <select name="main_id" id="main_category" class="form-control">
                                    <option value="0" selected><lang>NO</lang></option>
                                    <optgroup id="main_categories" label="Kategoriler"></optgroup>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <div class="input-group-append" style="width:50%;">
                                            <div class="form-group">
                                                <label for="StartTime"><b><lang>NOTATION</lang></b></label>
                                                <input name="start_time" type="time" style="display: inline" class="form-control" id="StartTime" value="00:00" min="00:00" max="24:00">
                                            </div>
                                        </div>
                                        <div class="input-group-append" style="width:50%;">
                                            <div class="form-group">
                                                <label for="EndTime"><b><lang>HIDING</lang></b></label>
                                                <input name="end_time" type="time" style="display: inline" class="form-control" id="EndTime" value="00:00" min="00:00" max="24:00">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label><lang>RANK</lang></label>
                                <input name="rank" type="number" class="form-control" required>
                            </div>
                            <div class="col-md-10 mt-5" style="display: grid;grid-template-columns:135px 1fr 1fr 1fr 1fr;">
                                <label><h4 style="margin: 0;position: relative;top: 6px;"><lang>ACTIVE_STATE</lang>: </h4> </label>
                                <label> <input class="checkbox-lg" type="checkbox" name="active_table"><lang>TABLE_ORDER</lang></label>
                                <label> <input class="checkbox-lg" type="checkbox" name="active_safe"> <lang>SAFE_SALE</lang></label>
                                <label> <input class="checkbox-lg" type="checkbox" name="active_take_away"><lang>TAKEAWAY</lang></label>
                                <label> <input class="checkbox-lg" type="checkbox" name="active_come_take"><lang>COME_TAKE</lang></label>
                            </div>
                        </div>

                        <div class="col-md-12 pt-3">
                            <table id="categories_table" class="display"></table>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer d-block">

                    <button value="2" type="submit" class="e_category_operation_btn btn bg-c3 w-25"><lang>DELETE</lang></button>
                    <button value="1" type="submit" class="e_category_operation_btn btn bg-c1 w-25 float-right"><lang>SAVE</lang></button>
                </div>
            </form>
        </div>
    </div>
</div>



<!---->
<!---->
<!--<label>Aktiflik</label>-->
<!--<label class="custom-control custom-checkbox" for="category_active" style="margin-top: -6px;margin-left: 8px;">-->
<!--    <input name="category_active" id="category_active" type="checkbox" class="custom-control-input">-->
<!--    <span for="category_active" class="custom-control-label checkbox-lg"></span>-->
<!--</label>-->