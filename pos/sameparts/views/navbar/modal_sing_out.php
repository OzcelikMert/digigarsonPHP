<style>
    div#screen_change_session {
        position: fixed;
        width: 100vw;
        height: 100vh;
        background: var(--theme-bg);
        top: 0;
        bottom: 0;
        z-index: 1059;
    }

    #screen_change_session .middle {
        padding: 20px;
        background: var(--theme-bg2);
        width: 500px;
        height: 400px;
        border-radius: 10px;
        margin-top: calc(50vh - 200px);
        margin-right: calc(50vw - 250px);
    }
</style>

<div class="fullscreen" style="display:none;" id="screen_change_session">
    <div class="login-form">
            <div class="program-btn">
                <div app-function="app_quit" class="p-btn"><i class="mdi mdi-close"></i>      </div>
                <!--div class="p-btn"><i class="mdi mdi-rectangle-outline"></i></div-->
                <div app-function="app_minimize"  class="p-btn"><i class="mdi mdi-minus"></i></div>
            </div>


            <div class="image">
                <img src="https://localhost/HomeFiles/images/logo/logodigi.png" style="width: 250px" alt="">
            </div>


            <div class="login" style="margin-top:2rem;">
                <form id="branch_login">
                    <div class="row" style="padding: 20px">
                        <div class="col-md-12">
                            <h3 class="text-center"><lang>LOGIN_TITLE</lang></h3>
                        </div>

                        <div class="col-md-12 d-flex justify-content-center">
                            <input name="password" id="pwd-input" class="form-input justify-content-center w-50 p-3 text-center size-type-lg" type="password" placeholder="Sifrenizi Giriniz.">
                        </div>

                        <div class="col-md-12 mt-2 d-flex justify-content-center">
                            <button class="btn-lg btn-primary w-50"><lang>LOGIN</lang></button>
                        </div>
                    </div>
                </form>


            </div>


        </div>
</div>