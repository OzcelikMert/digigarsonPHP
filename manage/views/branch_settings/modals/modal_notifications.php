<div class="modal fade" id="modal_notifications" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-xxl modal-lg" role="document">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h3 class="modal-title" id="modal_new_account_title"><lang>NOTFI_SERV_SETTINGS</lang></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <form class="e_branch_notifications">
                <!-- Modal Body -->
                <div class="modal-body p-0">
                    <div class="card-body pt-1 row">
                        <input type="hidden" name="id" value="0">

                        <div class="col-md-10">
                            <label>Bildirim Adı (Örn: Garson Çağır,Masamı Temizle,Hesabı Getir)</label>
                            <input  name="name" type="text" class="form-input" required>
                        </div>

                        <div class="col-md-2 pt-4">
                            <button class="btn btn-s1 e_edit_btn" ><lang>NEW_ADD</lang></button>
                            <button style="display: none" type="button" function="4" class="btn btn-s2 e_cancel_btn" ><lang>CANCEL</lang></button>
                        </div>

                        <div class="col-12 pt-4">
                            <h3>Kayıtlı Bildirim Servisleri</h3>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th width="120px"><lang>EDIT</lang></th>
                                        <th width="120px"><lang>DELETE</lang></th>
                                        <th><lang>NOTIFICATION</lang></th>
                                    </tr>
                                </thead>
                                <tbody class="e_service_table">
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