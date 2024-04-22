<div class="modal fade" id="modal_new_order" tabindex="-1" role="dialog" aria-labelledby="modal_new_order" aria-hidden="true">
    <div class="modal-dialog modal-xxl modal-lg" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h3 class="modal-title" ><lang>NEW_ORDER</lang></h3>
                <div class="e_modal_new_order_type_buttons row w-50 pl-4">
                    <button function="take_away" class="btn btn-danger btn-lg col-6"><lang>TAKEAWAY</lang></button>
                    <!--button function="come_take" class="btn btn-warning btn-lg col-6">Gel Al</button-->
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <form id="modal_new_order_form">
                <!-- Modal Body -->
                <div class="modal-body p-3" >
                    <h3 function="order_type_title" class="w-100 text-center"><lang>TAKEAWAY</lang></h3>
                    <div class="row mt-3">
                        <div class="col-6">
                            <input type="text" class="form-input" name="name" disabled required>
                        </div>
                        <div class="col-6">
                            <input type="tel" class="form-input" name="phone" disabled required>
                        </div>
                    </div>
                    <h3 order-type="take_away" class="mt-3"><lang>REGISTERED_ADDRESS</lang>
                        <button type="button" class="e_new_address_for_customer btn btn-success"><i class="fa fa-plus"></i> Yeni Ekle</button>
                    </h3>
                    <div order-type="take_away"  class="e_modal_new_order_customer_address row mt-3">
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="e_caller_cancel btn btn-danger"><lang>CANCEL</lang></button>
                    <button type="submit" class="btn btn-success"><lang>APPROVE</lang></button>
                </div>
            </form>
        </div>
    </div>
</div>