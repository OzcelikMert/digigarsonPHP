<div class="modal fade" id="modal_printer_settings" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xxl modal-lg" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <form id="form_printer_settings">

                <div class="modal-header">
                    <h3 class="modal-title" id="product_settings_title">
                        <lang>PRINTER_SETTINGS</lang>
                    </h3>
                    <button type="button" class="btn bg-c1 text-c6 ml-5 e_save_printer_settings">
                        <lang>SAVE</lang>
                    </button>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>

                <div class="container-fluid">
                    <div class="row mx-1 mt-1">
                        <!-- Other Settings -->
                        <div class="col-md-4 pl-0 e_safe_printer mb-3">
                            <div class="safe_settings">
                                <h5 class="m-0">
                                    <lang>GENERAL_SETTINGS</lang>
                                </h5>

                                <label class="col-form-label ">
                                    <lang>CHECKOUT_PRINT_SELECT</lang>
                                </label>
                                <select name="safe_printer" class="form-input" required>
                                    <option value="">
                                        <lang>MAKE_CHOICE</lang>
                                    </option>
                                </select>

                                <label class="col-form-label ">
                                    <lang>
                                        <lang>NAME_TO_DISPLAY_RECEIPT</lang>
                                    </lang>
                                </label>
                                <input name="title" type="text" class="form-input" required>

                                <div class="form-check my-3">
                                    <input class="checkbox-md form-check-input" type="checkbox" name="payment_invoice_user" id="payment_invoice_user">
                                    <label class="form-check-label pt-1 pl-4" for="payment_invoice_user">
                                        <lang>SHOW_PERSON_TICKET</lang>
                                    </label>
                                </div>

                                <div class="form-check my-3">
                                    <input class="checkbox-md form-check-input" type="checkbox" name="payment_invoice_show_quantity" id="payment_invoice_show_quantity">
                                    <label class="form-check-label pt-1 pl-4" for="payment_invoice_show_quantity">
                                        <lang>SHOW_QUANTITY_NAME</lang>
                                    </label>
                                </div>

                            </div>


                        </div>
                        <!-- Section Middle -->
                        <div class="col-4 pl-0">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <h5>
                                            <lang>CHOOSE_PRINTER_GROUP</lang>
                                        </h5>
                                        <div class="form-inline">
                                            <div class="col-12 pl-0">
                                                <select name="printer" class="form-input w-100 printer-select group-print" id="group_printer"></select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <h5>
                                            <lang>CREATE_PRINTER_GROUP</lang>
                                        </h5>
                                        <div class="form-inline">
                                            <div class="col-8 p-1">
                                                <input type="text" name="printer_group_name" class="form-input w-100 group-print" id="printer_new_group_name" placeholder="Grup İsimi">
                                            </div>
                                            <div class="col-4 p-0 m-0">
                                                <button type="button" class="btn btn-success w-100 group-print e_new_group_btn">
                                                    <lang>CREATE</lang>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <h5>
                                        <lang>CHOOSE_GROUP</lang>
                                    </h5>
                                    <div class="table-responsive group-print" style="overflow-y:scroll;">
                                        <table class="table table-md table-hover">
                                            <thead class="bg-blue-1">
                                                <tr>
                                                    <th>
                                                        <lang>GROUP_NAME</lang>
                                                    </th>
                                                    <th class="width-20-px text-center">
                                                        <lang>DELETE</lang>
                                                    </th>
                                                </tr>
                                            </thead>

                                            <tbody class="e_printer_groups"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Section Right -->
                        <div class="col-4 pl-0">
                            <div class="row">
                                <div class="col-12">
                                    <h5>
                                        <lang>PRODUCT_CATEGORIES</lang>
                                    </h5>
                                    <div class="table-responsive group-print" style="height:60ch;overflow-y:scroll;">
                                        <table class="table table-md table-hover">
                                            <thead class="bg-blue-1">
                                                <tr>
                                                    <th>
                                                        <lang>CATEGORY_NAME</lang>
                                                    </th>
                                                    <th class="width-20-px text-center">
                                                        <lang>ADD</lang>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody id="printer_settings_product_categories"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>