
// Navbar
$(document).ready(function (){
    new navbar();
})

let navbar = (function () {
    function  navbar() {
        change_session.initialize()
    }
    let id_list = {IMAGE_AVATAR: "#branch_avatar"}
    let navbar_class = $(".navbar");
    let navbar_page_class = $(".nav-mt-show");
    let navbar_enable = true;
    let set_type = {
        PASSWORD_CHECK: 0x0001
    }
    navbar.class_list = {
        SECTION_RIGHT: ".e_top_navbar_section_right",
        BACK_MOVE_TABLE: ".e_btn_back_move_table"
    }
    navbar.is_enable = function is_enable(enable = null){
        if (enable === null){
            let e = (!navbar_enable === true);
            navbar.is_enable(e);
        }else if (enable){
            navbar_enable = true;
            navbar_page_class.removeClass("nav-mt-hide");
            navbar_class.addClass("animate__animated animate__fadeInDown").removeClass("animate__fadeOutUp")
           // navbar_class.fadeIn();
        }else{
            navbar_enable = false;
            navbar_page_class.addClass("nav-mt-hide");
            navbar_class.addClass("animate__animated animate__fadeOutUp").removeClass("animate__fadeInDown")
            //navbar_class.fadeOut();
        }

    }


    let change_session  = {
        id_list: {
            PAGE: "#screen_change_session",
            BRANCH_LOGIN: "#branch_login",
            INPUT: "#pwd-input"
        },
        class_list: {
          BUTTON: ".e_session_change"

        },
        initialize: function (){
            let self = this;
            function set_events(){
                $(document).on("click",self.class_list.BUTTON,function () {
                    $(self.id_list.PAGE).toggle()
                    $(self.INPUT).focus();
                })
                $(document).on("submit", self.id_list.BRANCH_LOGIN, function (){
                    let data = $(self.id_list.BRANCH_LOGIN).serializeObject();
                    set(set_type.PASSWORD_CHECK, data, function (response){
                        response = JSON.parse(response);
                        console.log(response);
                        if (response.status) location.reload();
                        if (!response.status) helper_sweet_alert.error(language.data.INCORRECT_PASS,language.data.RE_ENTER_PASS)
                    })
                    return false;
                })
            }
            set_events();
           // set();
        }
    }
    function set(set_type, data, success_function){
        data["set_type"] = set_type;
        $.ajax({
            url: `./sameparts/functions/change_session/set.php`,
            type: "POST",
            data: data,
            success: function (data) {

                success_function(data);
            },timeout: settings.ajax_timeouts.NORMAL
        });
    }

    return navbar;
})();

