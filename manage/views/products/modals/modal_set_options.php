<div class="modal fade" id="modal_set_options" style="z-index: 1051;"  tabindex="-1" role="dialog" aria-labelledby="modal_set_options" aria-hidden="true">
    <div class="modal-dialog modal-xxl modal-lg" role="document">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h3 class="modal-title" id="product_settings_title"><lang>OPTION_EDIT_TITLE</lang></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>


            <!-- Modal Body -->
            <div class="modal-body p-0 py-3">
                <form id="option_form" autocomplete="off">
                <div class="container">
                    <div class="row">
                            <div class="e_option_info col-md-5 pr-2" style="border-right: 2px solid #80808033;">
                                <div class="item">
                                    <input type="hidden" name="option_id" value="0">
                                </div>
                                <div class="item">
                                    <label class="col-form-label mt-2"><lang>OPTION_SPECIAL_NAME</lang></label>
                                    <p class="mb-1 small"><lang>OPTION_SMALL_TEXT</lang></p>
                                    <input name="search_name" type="text" class="form-input" required="">
                                </div>
                                <div class="item">
                                    <label class="col-form-label mt-2"><lang>DISPLAY_NAME</lang></label>
                                    <p class="mb-1 small"><lang>DISPLAY_NAME_SMALL_TEXT</lang></p>
                                    <input name="name" type="text" class="form-input" required="">
                                </div>
                                <div class="item">
                                    <label class="col-form-label mt-2"><lang>OPTION_TYPE</lang></label>
                                    <p class="mb-1 small"><lang>OPTION_TYPE_SMALL_TEXT</lang></p>
                                    <select name="type" id="option_type" class="form-input" required="">
                                        <option value="1"><lang>CONTENTS</lang></option>
                                        <option value="2"><lang>SINGLE_SELECTION</lang></option>
                                        <option value="3"><lang>MULTI_SELECTION</lang></option>
                                        <!--option value="4">Tekli Ürün Seçimi</option-->
                                        <option value="5"><lang>QUANTITY</lang></option>

                                    </select>
                                </div>
                                <div class="item e_option_list_area">
                                    <label class="col-form-label mt-2"><lang>OPTION_TITLE</lang></label>
                                    <p class="mb-1 small"><lang>OPTION_TITLE_TEXT</lang></p>
                                    <input name="option_list" type="text" class="form-input" required="" VALUE="">
                                </div>
                                <div class="item">
                                    <div class="row mt-2">
                                        <div class="col-md-6 pr-1 e_option_limit_area">
                                            <label class="col-form-label mt-2"><lang>CHOICE_LIMIT</lang></label>
                                            <p class="mb-1 small"><lang>CHOICE_LIMIT_TEXT</lang></p>
                                            <input name="limit" type="number" class="form-input" min="0" max="100" required="" value="1">
                                        </div>

                                        <div class="col-md-6 pl-0 e_option_char_area">
                                            <label class="col-form-label mt-2"><lang>SEPERATION_CHARACTER</lang></label>
                                            <p class="mb-1 small"><lang>SEPERATION_CHARACTER_TEXT</lang></p>
                                            <select name="char" class="form-input" required="">
                                                <option value=",">,</option>
                                                <option value=".">.</option>
                                                <option value="*">*</option>
                                                <option value="\">\</option>
                                                <option value="/">/</option>
                                                <option value="|">|</option>
                                                <option value="-">-</option>
                                                <option value="_">_</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="item e_option_create_area">
                                    <div class="mt-2">
                                        <button type="button" class="e_create_option_btn btn bg-c3 w-100"><lang>CREATE_OPTION</lang></button>
                                        <p class="mb-1 small"><lang>CREATE_OPTION_TEXT</lang></p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-7 pl-2">

                                <div class="row mt-0 pl-0 e_option_items_title">

                                </div>

                                <div class="row mt-0 pb-5 e_option_items_area">

                                </div>

                                <div class="row mt-3">
                                    <div class="col-12 row m-0 mt-2" style="position: absolute;bottom: 0;">
                                        <div class="col-8 m-0 p-0"><button type="button" class="e_option_add_item btn bg-c1 w-100"><lang>ADD_ITEM</lang></button> </div>
                                        <div class="col-4 m-0 pl-1"><button type="submit" class="e_option_save_all btn bg-c1 w-100"><lang>SAVE</lang></button></div>
                                    </div>
                                </div>

                            </div>
                    </div>
                </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn bg-c3 text-c6" data-dismiss="modal"><lang>CLOSED</lang></button>
            </div>

        </div>
    </div>
</div>

