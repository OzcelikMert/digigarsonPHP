<div class="container">
    <form id="login_form">
        <div class="row mt-3 p-3">
            <div class="e_security_code col-12">
                <div class="form-group">
                    <label><lang>SECURITY_CODE</lang></label>
                    <input type="text" class="form-control" placeholder="Güvenlik Kodu" name="security_code">
                    <small class="form-text text-muted"> Yönetim paneli üzerinden oluşturulmuş cihaz hesabına otomatik oluşturulan kod.</small>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label for="verify_code"><lang>PASSWORD</lang></label>
                    <input type="password" class="form-control" placeholder="Şifre" name="password">
                    <small class="form-text text-muted"><lang>USER_PASS</lang></small>
                </div>
            </div>
            <div class="col-12">
                <font color="red" id="login_error"></font>
                <button type="submit" class="btn btn-primary w-100"><lang>LOGIN</lang></button>
            </div>
        </div>
    </form>
</div>
<div class="container" >
    <div class="row">
        <div class="col-12">
            <div class="version-info">
                <font id="version_info">v1.0.0.0</font>
            </div>
        </div>
    </div>
</div>


