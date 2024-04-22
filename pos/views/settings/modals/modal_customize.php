<div class="modal fade" id="modal_customize" tabindex="-1" role="dialog" aria-labelledby="modal_customize" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h3 class="modal-title"><lang>PERSONALISE</lang></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <form id="modal_customize_form">
                <!-- Modal Body -->
                <div class="modal-body p-0">
                    <div class="container">
                        <table class="table">
                            <tr for="active_trigger_product_edit">
                                <td><input class="checkbox-md pl-2" type="checkbox" name="active_trigger_product_edit"></td>
                                <td><lang>ACTIVE_OPTION</lang></td>
                            </tr>

                            <tr for="payment_invoice_user">
                                <td><input class="checkbox-md pl-2" type="checkbox" name="payment_invoice_user"></td>
                                <td> <lang>SHOW_PERSON_TICKET</lang></td>
                            </tr>

                            <tr for="barcode_system">
                                <td><input class="checkbox-md pl-2" type="checkbox" name="barcode_system"></td>
                                <td><lang>BARCODE_TRANSACTION</lang> (BETA)</td>
                            </tr>

                            <tr for="notifications">
                                <td><input class="checkbox-md pl-2" type="checkbox" name="notifications"></td>
                                <td><lang>Bildirimler & Gönderilen Servisler</lang></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="submit" class="btn bg-c1 text-c6"><lang>SAVE</lang></button>
                </div>
            </form>

        </div>
    </div>
</div>

