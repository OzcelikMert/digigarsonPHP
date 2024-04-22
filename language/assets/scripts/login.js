let login = (function () {
    let id_list = {};
    let class_list = {
        FORM_LOGIN: ".e_form_login",
        WRAPPER: ".e_wrapper"
    };

    console.log('admin : 123*#Qwe');

    function login(){ initialize(); }

    function set(){
        let data = $(class_list.FORM_LOGIN).serialize();
        $.ajax({
            url: "./functions/login/set.php",
            type: "POST",
            data: data,
            success: function (data) {
                data = JSON.parse(data);
                console.log(data);
                if(data.status){
                    $(class_list.FORM_LOGIN).fadeOut(500);
                    $(class_list.WRAPPER).addClass('form-success');
                    setTimeout(()=> { window.location.href = "index.php"; },500);
                }
            }
        });
    }

    function initialize(){
        function set_events(){
            $(class_list.FORM_LOGIN).submit(function(){
                set();
                return false;
            });
        }

        set_events();
    }

    return login;
})();

$(function () {
    let _login = new login();
})