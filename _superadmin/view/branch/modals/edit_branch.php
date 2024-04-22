<div class="modal fade" id="modal_edit_branch" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">

            <!-- Modal Body-->
            <div class="modal-body">
                <div class="modal-header">
                    <div class="modal-title" id="">
                        <h2>Firma Düzenle</h2>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="container">
                    <form id="edit_branch_form">
                        <input type="hidden" name="id" style="display: none;">
                        <div class="form-group">
                            <label for="branch_name"> Şube Adı</label>
                            <input type="text" name="name" class="form-control item" id="branch_name" placeholder="Firma İsmi">
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="waiter_app_limit"> Garson Uygulama Limiti</label>
                                <input type="number" name="waiter_app_limit" class="form-control item" id="waiter_app_limit" placeholder="Garson Uygulama Limiti">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="pos_app_limit"> Pos Uygulama Limiti</label>
                                <input type="number" name="pos_app_limit" class="form-control item" id="pos_app_limit" placeholder="Pos Uygulama Limiti">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="license_time_id">Lisans Süresi(geçiçi)</label>
                                <select class="form-control item" name="license_time_id">
                                    <option value="1">6 aylık</option>
                                    <option value="2">1 sene</option>
                                    <option value="3">2 sene</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="license_type_id">Lisans Tipi(geçiçi)</label>
                                <select class="form-control item" name="license_type_id">
                                    <option value="1">Lisans tipi 1</option>
                                    <option value="2">Lisans tipi 2</option>
                                    <option value="3">Lisans tipi 3</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="is_main_control">Ana Şube</label>
                                <select class="form-control item" name="is_main" id="is_main_control">
                                    <option value="1">Evet</option>
                                    <option value="0">Hayır</option>
                                </select>
                            </div>
                            <div class="form-group col-md-12" id="select_branch_box" style="display:none;">
                                <label for="is_main_control">Ana Şube Seç : </label>
                                <select class="form-control item" name="main_id" id="main_select_branch">
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-block btn-primary create-account">Kaydet</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Modal Footer-->
            <div class="modal-footer">
                <button  function="branch_delete" class="btn btn-danger" href="#">
                    Sil
                </button>
            </div>
        </div>
    </div>
</div>

