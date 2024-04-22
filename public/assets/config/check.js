let check = (function () {

    function check() {
        initialize();
    }

    function initialize(){
        image.initialize();
    }

    let image = {
        regex: {
            LOGO: "logo.webp"
        },
        initialize: function () {
            let self = this;

            function set_events() {
                Array.from($(`img[src$='${self.regex.LOGO}']`)).forEach(element => {
                    element = $(element);
                    element.attr("src", `${element.attr("src")}?v=${variable.date_format(new Date(), "yyyymmddHHMMss")}`);
                })
            }

            set_events();
        }
    };

    return check;
})();

$(function () {
    (new check());
})