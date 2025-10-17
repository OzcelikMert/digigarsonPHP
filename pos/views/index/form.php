<div class="container-fluid">
    <?php
    if ($_SERVER['SERVER_NAME'] == "localhost") {
        echo "<H1>BU BİR TEST SAYFASIDIR</H1>";
    }
    ?>
    <div class="login-form">
        <div class="program-btn">
            <div app-function="app_quit" class="p-btn"><i class="mdi mdi-close"></i> </div>
            <!--div class="p-btn"><i class="mdi mdi-rectangle-outline"></i></div-->
            <div app-function="app_minimize" class="p-btn"><i class="mdi mdi-minus"></i></div>
        </div>


        <div class="image">
            <img src="https://localhost/HomeFiles/images/logo/logodigi.png" alt="">
        </div>


        <div class="login" style="margin-top:2rem;">
            <form id="branch_login">
                <div class="row" style="padding: 20px">
                    <div class="col-md-12">
                        <h3 class="text-center">
                            <lang>LOGIN_TITLE</lang>
                        </h3>
                    </div>
                    <div class="col-md-12 d-flex justify-content-center">
                        <input name="security_code" id="security_code-input" class="form-input justify-content-center w-50 p-3 text-center size-type-lg" style="display: none" value="" type="text" placeholder="Güvenlik kodunu giriniz.">
                    </div>

                    <div class="col-md-12 d-flex justify-content-center">
                        <input name="password" id="pwd-input" class="form-input justify-content-center w-50 p-3 text-center size-type-lg" style="display: none" type="password" placeholder="Sifrenizi Giriniz.">
                    </div>

                    <div class="col-md-12 mt-2 d-flex justify-content-center">
                        <button class="btn-lg btn-primary w-50">
                            <lang>LOGIN</lang>
                        </button>
                    </div>
                </div>
            </form>

            <div class="e_token_logout" style="display: none">
                <h5 style="position: fixed;bottom: 0px;width: 100%;right: 0;color: #333 !important;text-align: right;">x</h5>
            </div>

        </div>


    </div>
</div>