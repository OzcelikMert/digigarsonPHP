<div class="modal fade" id="modal_scales_products" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xxl modal-lg" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h3 class="modal-title" id="product_settings_title"><lang>PRODUCT_SELECT_SCALES	</lang></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <!--form id="form_set_product" action=""-->
                <!-- Modal Body -->
                <div class="modal-body p-2">

                    <ul class="nav nav-tabs">
                        <li class="active"><button id="scales_get_saved_btn" class="btn btn-s1 " type="button" href="#scales_products_area" data-toggle="tab">Ürünler</button></li>
                        <li>                <button id="scales_get_all_saved_btn"  class="btn btn-s1"type="button" href="#scales_add_products_area" data-toggle="tab">Ürün Ara ve Ekle</button></li>
                    </ul>


                    <div class="tab-content">
                        <div id="scales_products_area" class="tab-pane pt-4 active show">
                            <h2><lang>SCALES_PRODUCTS</lang></h2>
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th><lang>BUTTON_SEQUENCE</lang></th>
                                    <th><lang>PRODUCT_NAME</lang></th>
                                    <th><lang>QR_NUMBER</lang></th>
                                    <th><lang>PRICE</lang></th>
                                </tr>
                                </thead>
                                <tbody class="e_get_products_scales"></tbody>
                            </table>
                            <button type="button" class="btn bg-c1 w-25" function="save_all"><lang>TRANSFER_FILE</lang></button>

                        </div>

                        <div id="scales_add_products_area" class="tab-pane pt-4 fade">
                            <h2>Ürün Ekle</h2>
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th width="100px"><lang>PROCESS</lang></th>
                                    <th><lang>PRODUCT_NAME</lang></th>
                                    <th><lang>QR_NUMBER</lang></th>
                                    <th width="150px"><lang>SAFE_SALE_PRICE</lang></th>
                                </tr>
                                </thead>
                                <tbody class="e_get_all_products_scales">

                                </tbody>
                            </table>
                            <button type="button" class="btn bg-c1 w-25" function="save"><lang>SAVE</lang></button>
                        </div>

                    </div>




                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                </div>
            <!--/form-->

        </div>
    </div>
</div>
