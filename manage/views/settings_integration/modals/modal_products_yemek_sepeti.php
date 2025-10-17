<div class="modal fade" id="modal_products_yemek_sepeti" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-xxl modal-lg" role="document">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h3 class="modal-title"><lang>MATCH_PRODUCT</lang> (Yemek Sepeti)</h3>
                <button function="1" class="e_btn_list btn btn-success ml-3"><lang>PRODUCT</lang></button>
                <button function="2" class="e_btn_list btn btn-warning ml-3"><lang>OPTION</lang></button>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <form id="form_products_yemek_sepeti">
                <!-- Modal Body -->
                <div class="modal-body p-0">
                    <div class="row p-2">
                        <div class="col-md-6">
                            <h4>Yemek Sepeti</h4>
                            <input type="text" function="integrate" class="e_search_product form-input mb-1" placeholder="Arama">
                            <div class="products-table-div">
                                <table class="e_products_integrate_table table table-striped table-sticky bg-theme-2 br text-left">
                                    <tr>
                                        <th><lang>PRODUCT</lang></th>
                                    </tr>
                                    <tbody class="e_products_integrate">

                                    </tbody>
                                </table>
                            </div>

                        </div>
                        <div class="col-md-6">
                            <h4>MimiPos</h4>
                            <input type="text" function="mimi" class="e_search_product form-input mb-1" placeholder="Arama">
                            <div class="products-table-div">
                                <table class="e_products_table table table-striped table-sticky bg-theme-2 br text-left">
                                    <tr>
                                        <th><lang>PRODUCT</lang></th>
                                    </tr>
                                    <tbody class="e_products">

                                    </tbody>
                                </table>
                            </div>
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