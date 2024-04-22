<div id="login_and_register" class="pop-page" style="display: none">
    <!--===  TOP BAR  ===--->
    <div class="top-bar">
        <div  class="left-btn icon"><a href="#" close_page="login_and_register"><i class="fa fa-arrow-left"></i></a></div>
        <div class="page-title"><span><lang>REGISTER_OR_LOGIN</lang></span></div>
        <div class="right-btn icon"></div>
    </div>

    <div class="pop-page-in " style="padding-bottom: 500px">
        <div class="container">
            <div class="row">
                <div class="col-12 mt-3">
                    <img src="../images/theme/logo.webp?V=2" class="p-3 m-auto d-block" alt="..." style="width:200px;">
                    <h2 class="text-center title pb-4"> <lang>REGISTER</lang> </h2>
                    <div class="login-form">
                        <!--------REGISTER  ------->
                        <div id="register" >
                            <form id="register_form" method="post" action="#" autocomplete="off">

                                <div class="mb-3 input-icon">
                                    <label class="left-icon" for="i-phone"><i class="fa fa-phone-alt"></i></label>
                                    <input id="i-phone" name="phone" type="text" placeholder="Telefon Numaranız" class="form-input bd-bb bg-none" minlength="10" maxlength="11" required />
                                </div>

                                <div class="mb-3 input-icon">
                                    <label for="i-name" class="left-icon" for="user-input"><i class="fa fa-user-alt"></i></label>
                                    <input id="i-name" name="name" type="text" placeholder="Username" class="form-input bd-bb bg-none" required />
                                </div>

                                <div class="mb-0 mt-5">
                                    <button type="submit" class="btn btn-s1 w-100 br e_question" function="enter_code">
                                        <i class="fa fa-sign-in-alt"></i>
                                        <lang>REGISTER_OR_LOGIN</lang>
                                    </button>

                                    <button close_page="login_and_register" type="button" class="mt-3 btn btn-s4 w-100 br e_question" function="show_menu">
                                        <i class="fa fa-book-reader"></i><lang>VIEW_MENU</lang>
                                    </button>

                                </div>
                            </form>
                        </div>

                        <div id="verify_code_area" class="verification shadow" style="display: none;">
                            <h4 class="text-center mt-2"><span class="e_verify_count_down"  style="background: #a2cdec;border: 1px dotted #00000036;padding: 10px;border-radius: 5px;">180 Saniye</span></h4>
                            <h2><lang>ENTER_VERIFICATION_CODE</lang> </h2>
                            <p><lang>PHONE_VERIFICATION</lang>..</p>
                            <form id="verify_code">
                                <div class="code_group">
                                    <input type="text" class="w-100 form-input" name="verify_code" maxlength="4" max="9999" min="1000"  placeholder="0 0 0 0">
                                    <button type="submit" class="btn btn-s1 w-100 br mt-2"><lang>APPROVE</lang></button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
