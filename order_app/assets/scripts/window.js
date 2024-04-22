/*======================== PAGE BACK SYSTEM ====================*/
let originalPotion = false;
let bottom_navbar = false;

window.onpopstate = function() {
    pages.close_last_page();
    window.onbeforeunload = function(e) { return false };
};
$(document).ready(function(){
    if (originalPotion === false) originalPotion = $(window).width() + $(window).height();
});
$(document).on('focus blur', 'select, textarea, input[type=text], input[type=date], input[type=password], input[type=email], input[type=number]', function(e){
    let $obj = $(this);
    let nowWithKeyboard = (e.type === 'focusin');
    $('body').toggleClass('view-withKeyboard', nowWithKeyboard);
    onKeyboardOnOff(nowWithKeyboard);
});
$(window).on('resize orientationchange', function(){ applyAfterResize();});

function set_uri_patch(patch){
    window.history.pushState("forward", null, `#${patch}`);
}
function onKeyboardOnOff(isOpen) {
    if (bottom_navbar){
        if (isOpen) {
            $("#navbar_main").hide();
        } else {
            $("#navbar_main").show();
        }
    }
}
function getMobileOperatingSystem() {
    var userAgent = navigator.userAgent || navigator.vendor || window.opera;
    if (/windows phone/i.test(userAgent)) {return "winphone";}
    if (/android/i.test(userAgent)) {return "android";}
    if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {return "ios"; }
    return "";
}
function applyAfterResize() {
    if (getMobileOperatingSystem() !== 'ios') {
        if (originalPotion !== false) {
            var wasWithKeyboard = $('body').hasClass('view-withKeyboard');
            var nowWithKeyboard = false;
            var diff = Math.abs(originalPotion - ($(window).width() + $(window).height()));
            if (diff > 100) nowWithKeyboard = true;

            $('body').toggleClass('view-withKeyboard', nowWithKeyboard);
            if (wasWithKeyboard !== nowWithKeyboard) {
                onKeyboardOnOff(nowWithKeyboard);
            }
        }
    }
}

