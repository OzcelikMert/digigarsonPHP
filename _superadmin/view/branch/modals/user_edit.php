<div class="modal fade" id="branch_user_edit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <div class="modal-title" id="">
                    <h2>Kullanıcıyı güncelle</h2>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- Modal Body-->
            <div class="modal-body p-0">
                <div class="container pt-1">
                    <form id="branch_user_form">
                        <input type="number" name="id" style="z-index: -1; width: 0; height: 0; opacity: 0;">
                        <!-- Name -->
                        <div class="form-group">
                            <label>Kullanıcı adı</label>
                            <input type="text" name="name" placeholder="Ad & Soyad" class="form-control item">
                        </div>
                        <div class="form-group" style="position:relative;">
                            <label>Şifre </label>
                            <input type="password" name="password" placeholder="Ad & Soyad" class="form-control item">
                            <span style="position: relative;z-index: 1; top: -28px; right: 8px; display: inline-block; float: right"><span id="show-passw"><i class="fas fa-eye eye"></i></span></span>
                        </div>
                        <div class="form-group"  >
                            <a class="btn btn-success e_active" function="active">Aktif et</a>
                            <a class="btn btn-danger e_not_active" function="not_active">aktifliği kaldır</a>
                        </div>
                        <button type="submit" class="btn btn-block btn-primary">
                            Güncelle
                        </button>
                    </form>
                </div>
            </div>
            <!-- Modal Footer -->
            <div class="modal-footer">
                <hr>
            </div>
        </div>
    </div>
</div>

