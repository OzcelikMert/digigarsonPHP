$(function (){
    /* code try try try*/

    $('.dismiss, .overlay').on('click', function() {
        $('.sidebar').removeClass('active');
        $('.overlay').removeClass('active');
    });

    $('.open-menu').on('click', function(e) {
        e.preventDefault();
        $('.sidebar').addClass('active');
        $('.overlay').addClass('active');
        // close opened sub-menus
        $('.collapse.show').toggleClass('show');
        $('a[aria-expanded=true]').attr('aria-expanded', 'false');
    });
    $('.to-top a').on('click', function(e) {
        e.preventDefault();
        if($(window).scrollTop() != 0) {
            $('html, body').stop().animate({scrollTop: 0}, 1000);
        }
    });
})