<div class="modal fade" id="modal_create_user" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <div class="modal-title" id="">
                    <h2>Kullanıcı Oluştur</h2>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- Modal Body-->
            <div class="modal-body p-0">
                <div class="container pt-1">
                    <form method="post" id="branch_user">
                            <!-- Name -->
                            <div class="form-group">
                                <input type="text" name="name" placeholder="Ad & Soyad" class="form-control item">
                            </div>
                            <!-- Email Address -->
                            <div class="form-group">
                                <input type="email" name="email" placeholder="Email Adresi" class="form-control item">
                            </div>
                            <!-- Phone Number -->
                            <div class="form-group">
                                <input type="tel" name="phone"  placeholder="Telefon : 5xx xxx xxx" class="form-control item" pattern="\d{11}" minlength="10" maxlength="11" required>
                            </div>
                            <!-- Password -->
                            <div class="form-group">
                                <input type="password" name="password" placeholder="Şifre" class="form-control item">
                            </div>

                            <button type="submit" class="btn btn-block btn-primary create-account">
                                Hesabı Oluştur
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

