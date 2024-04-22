let helper_sweet_alert = (function() {

    function helper_sweet_alert(){}

    helper_sweet_alert.wait = function(title, html) {
        Swal.fire({
            title: title,
            html: html,
            onBeforeOpen () {
                Swal.showLoading ()
            },
            onAfterClose () {
                Swal.hideLoading()
            },
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false
        });
    }

    helper_sweet_alert.error = function(title, html,timer = 7500) {
        Swal.fire({
            icon: "error",
            position: 'center',
            title: title,
            html: html,
            showConfirmButton: false,
            timer: timer
        });
    }

    helper_sweet_alert.success = function (title, html) {
        Swal.fire({
            icon: "success",
            position: 'center',
            title: title,
            html: html,
            showConfirmButton: false,
            timer: 1000
        });
    }

    helper_sweet_alert.close = function () {
        Swal.close();
    }

    return helper_sweet_alert;
})();