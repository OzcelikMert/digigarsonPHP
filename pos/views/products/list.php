<div class="container-fluid" style="margin-top: 90px;" xmlns:v-bind="http://www.w3.org/1999/xhtml">

    <div class="products-top card bg-card">
        <div class="row">

            <div class="col-md-6">
                <h3><lang>TOOLS</lang></h3>
            </div>

            <div class="col-md-6 mb-3">
              <input class="form-input" id="search" placeholder="PRODUCT_SEARCH" type="text">
            </div>

            <div class="col-md-2 p-1">
                <button class="btn btn-s1 br w-100 size-type-lg" data-toggle="modal" data-target="#modal_select_category"><lang>SELECT_CATEGORY</lang></button>
            </div>

            <div class="col-md-2 p-1">
                <button class="btn btn-s1 br w-100 size-type-lg" data-toggle="modal" data-target="#modal_set_category"><lang>ADD_CATEGORY</lang></button>
            </div>

            <div class="col-md-2 p-1">
                <button class="btn btn-s4 br w-100 size-type-lg" data-toggle="modal" data-target="#modal_translates"><lang>TRANSLATE</lang></button>
            </div>

            <div class="col-md-2 p-1">
                <button id="option_list_btn" class="btn btn-s1 br w-100 size-type-lg" data-toggle="modal" data-target="#modal_get_options"><lang>OPTION_LIST</lang></button>
            </div>

            <div class="col-md-2 p-1">
                <button id="new_option_btn" class="btn btn-s1 br w-100 size-type-lg" data-toggle="modal" data-target="#modal_set_options"><lang>ADD_OPTION</lang></button>
            </div>

            <div class="col-md-2 p-1">
                <button class="e_btn_tools btn btn-s1 br w-100 size-type-lg" function="product" data-toggle="modal" data-target="#modal_set_product"><lang>ADD_PRODUCT</lang></button>
            </div>

            <div class="col-md-4 p-1 e_scales_products_open_btn" style="display: none">
                <button class="e_btn_tools btn btn-s1 br w-100 size-type-lg" data-toggle="modal" data-target="#modal_scales_products"><lang>Terazi Ürünleri Ayarı</lang></button>
            </div>
        </div>
    </div>

    <div id="product_list">

    </div>
</div>

