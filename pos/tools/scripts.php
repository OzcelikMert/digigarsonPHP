<?php
$v = "?v=" . date("Ymdhis");
?>


<script>
    if (typeof module === 'object') {
        window.module = module;
        module = undefined;
    }
</script>
<script src="./../public/assets/plugins/JQuery/jquery.min.js"></script>
<script src="./../public/assets/plugins/JQuery/jquery.serializeObject.js"></script>
<script src="./../public/assets/plugins/JQuery/jquery.formautofill.js"></script>
<script src="./../public/assets/plugins/JQuery/jquery-confirm.js"></script>
<!--script src="./../public/assets/plugins/Bootstrap/bootstrap.min.js"></script-->
<script src="./../public/assets/plugins/Bootstrap/bootstrap.bundle.js"></script>
<script src="./../public/assets/plugins/SweetAlert/sweetalert2.all.js"></script>

<script src="./../matrix_library/js/animations/helper_swal.js"></script>
<script src="./../matrix_library/js/operations/variable.js?v=1.003"></script>
<script src="./../matrix_library/js/operations/array_list.js?v=1.002"></script>
<script src="./../matrix_library/js/operations/server.js?v=1.002"></script>

<script src="./../public/assets/config/settings.js?v=1.004"></script>
<script src="./../public/assets/config/locations.js?v=1.002"></script>

<script src="./../public/assets/config/languages/language_tr.js<?= $v; ?>"></script>
<script src="./../public/assets/config/language.js<?= $v; ?>"></script>
<script src="./../public/sameparts/js/helper.js?<?= $v; ?>"></script>
<script src="./sameparts/views/keyboard/keyboard.js<?= $v; ?>"></script>

<script src="./assets/scripts/app.js<?= $v; ?>"></script>

<?php if (page_name != "index") { ?>
    <script src='./assets/scripts/main.js?<?= $v; ?>'></script>
    <script src='./assets/scripts/navbar.js?<?= $v; ?>'></script>
    <!--------------PRINTER --------------->
    <script src='./assets/scripts/printer/invoice.js<?= $v ?>'></script>

    <script src='./assets/scripts/printer/invoice_type/safe.js<?= $v ?>'></script>
    <script src='./assets/scripts/printer/invoice_type/kitchen.js<?= $v ?>'></script>
    <script src="./assets/scripts/notifications.js<?= $v ?>"></script>

    <!--------------CALLER ID--------------->
    <?php if (isset($_SESSION["permission"][10])) { ?><script src='./assets/scripts/caller_id.js<?= $v ?>'></script><?php } ?>
    <!-- INTEGRATIONS -->
    <script src="./../integrations/companies/integrated/integrated_companies.js<?= $v ?>"></script>
    <?php if (unserialize($_SESSION["integrations"]["yemek_sepeti"])->status == 1) { ?><script src="./../integrations/companies/integrated/yemek_sepeti/script/initialize.js<?= $v ?>"></script><?php } ?>
<?php } ?>