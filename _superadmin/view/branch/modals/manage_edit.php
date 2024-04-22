<div class="modal fade" id="manage_user_edit" tabindex="-1" role="dialog" aria-hidden="true">
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
                    <form id="manage_user">
                        <input type="number" name="id" style="z-index: -1; width: 0; height: 0; opacity: 0;">
                        <!-- Name -->
                        <div class="form-group">
                            <label for="name">Ad & Soyad</label>
                            <input type="text" name="name" placeholder="Ad & Soyad" id="name" class="form-control item" required>
                        </div>
                        <!-- Email Address -->
                        <div class="form-group">
                            <label for="email">Email Adresi</label>
                            <input type="email" name="email" placeholder="Email Adresi" id="email" class="form-control item">
                        </div>
                        <!-- Phone Number -->
                        <div class="form-group">
                            <label for="tel">
                                Telefon Numarası
                            </label>
                            <input type="tel" name="phone"  placeholder="Telefon : 5xx xxx xxx" id="tel" class="form-control item"  minlength="10" maxlength="11">
                        </div>
                        <!-- Password -->
                        <div class="form-group" style="position:relative;">
                            <label for="password">
                                Şifre
                            </label>
                            <input type="password" name="password" placeholder="Şifre" id="password" class="form-control item" autocomplete="off" required>
                            <span style="position: relative;z-index: 1; top: -28px; right: 8px; display: inline-block; float: right"><span id="show-passw"><i class="fas fa-eye eye"></i></span></span>
                        </div>

                        <button type="submit" class="btn btn-block btn-primary">
                            Hesabı Oluştur / Güncelle
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

