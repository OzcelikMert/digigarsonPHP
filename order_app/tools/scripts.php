<?php

    $language_tag = \matrix_library\php\operations\user::session(\order_app\sameparts\functions\sessions\keys::LANG_TAG);
    $language_tag = ($language_tag == "tr" || $language_tag == "" || $language_tag == null) ? "tr" : "en";
    $v = "?v=1.12.10";
?>

<script src="./../public/assets/plugins/JQuery/jquery.min.js"></script>
<script src="./../public/assets/plugins/JQuery/jquery.serializeObject.js"></script>
<script src="./../public/assets/plugins/JQuery/jquery.formautofill.js"></script>
<script src="./../public/assets/plugins/JQuery/jquery.toast.js"></script>
<!--<script src="./../public/assets/plugins/Bootstrap/bootstrap.min.js"></script>-->
<script src="./../public/assets/plugins/SweetAlert/sweetalert2.all.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js" integrity="sha512-XKa9Hemdy1Ui3KSGgJdgMyYlUg1gM+QhL6cnlyTe2qzMCYm4nAZ1PsVerQzTTXzonUR+dmswHqgJPuwCq1MaAg==" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script src="./../matrix_library/js/animations/custom_pop_up.js"></script>
<script src="./../public/assets/config/languages/language_<?=$language_tag?>.js<?=$v;?>"></script>

<script src="./../public/assets/config/language.js<?=$v?>"></script>

<script src="./../matrix_library/js/animations/helper_swal.js"></script>
<script src="./../matrix_library/js/operations/variable.js<?=$v?>"></script>
<script src="./../matrix_library/js/operations/array_list.js<?=$v?>"></script>
<script src="./../matrix_library/js/operations/server.js<?=$v?>"></script>

<script src="./../public/assets/config/settings.js<?=$v?>"></script>
<script src="./../public/assets/config/locations.js<?=$v?>"></script>

<script src="./../public/sameparts/js/helper.js<?=$v?>"></script>

<!--script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>AOS.init();</script-->

