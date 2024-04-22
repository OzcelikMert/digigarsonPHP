<?php
    $v = "?v=".date("Ymdhis");
?>
<!-- Script -->
<script src="../public/assets/plugins/JQuery/jquery.min.js"></script>
<script src="../public/assets/plugins/JQuery/jquery-ui.min.js"></script>
<script src="./../public/assets/plugins/JQuery/jquery.serializeObject.js"></script>
<script src="./../public/assets/plugins/JQuery/jquery.formautofill.js"></script>
<script src="../public/assets/plugins/JQuery/jquery-confirm.js"></script>
<script src="../public/assets/plugins/Mixed/base64.js"></script>
<script src="../public/assets/plugins/Mixed/mousetrap.min.js"></script>
<script src="../public/assets/plugins/Mixed/perfect-scrollbar.min.js"></script>
<script src="../public/assets/plugins/Bootstrap/bootstrap.bundle.min.js"></script>
<script src="../public/assets/plugins/Bootstrap/bootstrap-notify.min.js"></script>
<script src="../public/assets/plugins/Polyfill/polyfill.js"></script>
<script src="../public/assets/plugins/SweetAlert/sweetalert2.all.js"></script>

<script src="../../matrix_library/js/animations/custom_pop_up.js"></script>
<script src="./../matrix_library/js/animations/helper_swal.js"></script>
<script src="./../matrix_library/js/operations/variable.js?v=1.001"></script>
<script src="./../matrix_library/js/operations/array_list.js?v=1.001"></script>
<script src="./../matrix_library/js/operations/server.js?v=1.001"></script>

<script src="./../public/assets/config/settings.js?v=1.001"></script>
<script src="./../public/assets/config/locations.js?v=1.001"></script>

<script src="./../public/assets/config/languages/language_tr.js<?=$v;?>"></script>
<script src="./../public/assets/config/language.js"<?=$v;?>></script>

<script src="./../public/sameparts/js/helper.js?v=1.004"></script>

<script src="./assets/scripts/application.js?v=<?=$v;?>"></script>

<?php if(page_name != "index") { ?>
    <script src="./assets/scripts/main.js?v=<?=$v;?>"></script>
<?php } ?>