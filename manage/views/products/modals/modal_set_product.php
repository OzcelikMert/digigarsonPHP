<div class="modal fade" id="modal_set_product" tabindex="-1" role="dialog" aria-labelledby="product_settings " aria-hidden="true">
    <div class="modal-dialog modal-xxl modal-lg" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h3 class="modal-title" id="product_settings_title"><lang>ADD_PRODUCT</lang> & <lang>EDIT</lang></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <form id="form_set_product" action="">
                <!-- Modal Body -->
                <div class="modal-body p-0">
                    <div class="card-body pt-1 row">
                            <input name="id" type="hidden">
                            <div class="col-3">
                                <label for="recipient-name" class="col-form-label"><lang>PRODUCT_IMAGE</lang></label>
                                <div class="product-image-area">
                                    <div class="image">
                                        <img id="product_edit_image" draggable="false" src="" alt="Product Image" main-src="">
                                    </div>
                                    <div class="float-left w-100 mt-1">
                                        <label class="custom-control custom-checkbox custom-control small">
                                            <input name="default_category_image" type="checkbox" class="custom-control-input"><span class="custom-control-label mt-1"><lang>DEFAULT_CATEGORY_IMAGE</lang></span>
                                        </label>
                                    </div>
                                    <!--button type="button" class="e_btn_default_category_image btn btn-primary w-100 btn-xs mt-1">Varsayılan Kategori Resimi</button-->
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="recipient-name" class="col-form-label  d-block"><lang>PRODUCT_ACTIVE</lang></label>

                                <label class="custom-control custom-checkbox custom-control">
                                    <input name="active_mobile" type="checkbox" class="custom-control-input"><span class="custom-control-label"><lang>TABLE_ORDER</lang></span>
                                </label>

                                <label class="custom-control custom-checkbox custom-control">
                                    <input name="active_take_away" type="checkbox" class="custom-control-input"><span class="custom-control-label"><lang>TAKEAWAY</lang></span>
                                </label>

                                <label class="custom-control custom-checkbox custom-control">
                                    <input name="active_come_take" type="checkbox" class="custom-control-input"><span class="custom-control-label"><lang>COME_TAKE</lang></span>
                                </label>

                                <label class="custom-control custom-checkbox custom-control">
                                    <input name="active_pos" type="checkbox" class="custom-control-input"><span class="custom-control-label"><lang>POS_AND_TERMINAL</lang></span>
                                </label>
                            </div>
                            <div class="col-md-5 px-1">
                                <label for="recipient-name" class="col-form-label "><lang>ACTIVE_STATE</lang></label>
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <div class="input-group-append pr-1" style="width:50%;">
                                            <div class="form-group">
                                                <label for="StartTime"><b><lang>SHOW</lang></b></label>
                                                <input name="start_time" type="time" class="form-input" value="00:00:00" min="00:00" max="24:00">
                                                <small class="form-text size-type-sm"> <lang>PRODUCT_START_TIME_DESCRIPTION</lang></small>
                                            </div>
                                        </div>
                                        <div class="input-group-append" style="width:50%;">
                                            <div class="form-group">
                                                <label for="EndTime"><b><lang>HIDE</lang></b></label>
                                                <input name="end_time" type="time" class="form-input" value="00:00:00" min="00:00" max="24:00">
                                                <small class="form-text size-type-sm"> <lang>PRODUCT_STOP_TIME_DESCRIPTION</lang></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 px-1">
                                <label class="col-form-label "><lang>PRODUCT_NAME</lang></label>
                                <input name="name" type="text" class="form-input" required>

                                <label class="col-form-label "><lang>STOCK_CODE</lang></label>
                                <input name="code" type="text" class="form-input">

                                <label class="col-form-label "><lang>PRODUCT_DESCRIPTION</lang></label>
                                <textarea name="comment" class="form-input" rows="3"></textarea>

                                <label class="col-form-label "><lang>SELECT_CATEGORY</lang></label>
                                <select name="category_id" class="form-input" required>
                                    <option><lang>SELECT_CATEGORY</lang></option>
                                </select>
                                
                                <label class="col-form-label"><lang>PRODUCT_TYPE</lang></label>
                                <select name="quantity_id" class="form-input" required></select>

                                <label class="col-form-label"><lang>RANK</lang></label>
                                <input name="rank" type="number" class="form-input">
                            </div>
                            <div class="col-md-4 row m-0 px-1">
                                <div class="col-8 p-1">
                                    <label class="col-form-label"><lang>NORMAL_SALE_PRICE</lang></label>
                                    <input name="price" type="number" class="form-input" placeholder="NORMAL_SALE_PRICE" step="0.01" required>
                                </div>
                                <div class="col-4 p-1">
                                    <label class="col-form-label "><lang>VAT</lang></label>
                                    <input name="vat" type="number" class="form-input" placeholder="VAT" required>
                                </div>
                                <div class="col-8 p-1">
                                    <label class="col-form-label "><lang>SAFE_SALE_PRICE</lang></label>
                                    <input name="price_safe" type="number" class="form-input" step="0.01">
                                </div>
                                <div class="col-4 p-1">
                                    <label class="col-form-label "><lang>VAT</lang></label>
                                    <input name="vat_safe" type="number" class="form-input" >
                                </div>
                                <div class="col-8 p-1">
                                    <label class="col-form-label "><lang>TAKEAWAY_SALE_PRICE</lang></label>
                                    <input name="price_take_away" type="number" class="form-input" step="0.01">
                                </div>
                                <div class="col-4 p-1">
                                    <label class="col-form-label "><lang>VAT</lang></label>
                                    <input name="vat_take_away" type="number" class="form-input" >
                                </div>
                                <div class="col-8 p-1">
                                    <label class="col-form-label "><lang>COME_TAKE_SALE_PRICE</lang></label>
                                    <input name="price_come_take" type="number" class="form-input" step="0.01">
                                </div>
                                <div class="col-4 p-1">
                                    <label class="col-form-label "><lang>VAT</lang></label>
                                    <input name="vat_come_take" type="number" class="form-input" >
                                </div>
                                <div class="col-8 p-1">
                                    <label class="col-form-label "><lang>PERSONAL_SALE_PRICE</lang></label>
                                    <input name="price_personal" type="number" class="form-input" step="0.01">
                                </div>
                                <div class="col-4 p-1">
                                    <label class="col-form-label "><lang>VAT</lang></label>
                                    <input name="vat_personal" type="number" class="form-input" >
                                </div>
                                <div class="col-8 p-1">
                                    <label class="col-form-label "><lang>OTHER_SALE_PRICE</lang></label>
                                    <input name="price_other" type="number" class="form-input" step="0.01">
                                </div>
                                <div class="col-4 p-1">
                                    <label class="col-form-label "><lang>VAT</lang></label>
                                    <input name="vat_other" type="number" class="form-input" >
                                </div>
                            </div>
                            <div class="col-md-4 px-1">
                                <input name="product_liked_options" type="hidden">
                                <label class="col-form-label mt-1"><lang>ADD_OPTION</lang></label>
                                <button type="button" id="product_linked_btn" class="btn btn-primary w-100 mt-2"><lang>ADD</lang></button>
                                <table class="table table-striped table-sticky table-md bg-theme-2 text-left mt-2">
                                    <thead><tr>
                                        <th><lang>NAME</lang></th>
                                        <th style="width: 55px;"><lang>LIMIT</lang></th>
                                        <th style="width: 50px;"><lang>DELETE</lang></th></tr></thead>
                                    <tbody class="e_product_linked_option_area">

                                    </tbody>
                                </table>

                            </div>
                        </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <div class="w-100">
                        <button type="button" class="btn bg-c2 w-25" data-toggle="modal" data-target="#modal_set_category"><lang>ADD_CATEGORY</lang></button>
                    </div>
                    <button type="submit" class="btn bg-c1 w-25"><lang>SAVE</lang></button>
                </div>
            </form>
        </div>
    </div>
</div>
