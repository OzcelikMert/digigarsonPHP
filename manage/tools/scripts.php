<?php
$v = "?v=".date("Ymdhis");
?>
<script>
    if (typeof module === 'object') {window.module = module; module = undefined;}
</script>
<script src="./../public/assets/plugins/JQuery/jquery.min.js"></script>
<script src="./../public/assets/plugins/JQuery/jquery.serializeObject.js"></script>
<script src="./../public/assets/plugins/JQuery/jquery.formautofill.js"></script>
<script src="./../public/assets/plugins/JQuery/jquery-confirm.js"></script>

<!--script src="./../public/assets/plugins/Bootstrap/bootstrap.min.js"></script-->
<script src="./../public/assets/plugins/Bootstrap/bootstrap.bundle.js"></script>
<script src="./../public/assets/plugins/Bootstrap/bootstrap.bootstrap.min.js"></script>
<script src="./../public/assets/plugins/SweetAlert/sweetalert2.all.js"></script>

<script src="./../matrix_library/js/animations/helper_swal.js"></script>
<script src="./../matrix_library/js/operations/variable.js?v=1.0.3"></script>
<script src="./../matrix_library/js/operations/array_list.js?v=1.0.3"></script>
<script src="./../matrix_library/js/operations/server.js"></script>
<script src="./../matrix_library/js/convertors/html_to_excel.js"></script>

<script src="./../public/assets/config/settings.js"></script>
<script src="./../public/assets/config/locations.js"></script>

<script src="./../public/assets/config/languages/language_tr.js<?=$v;?>"></script>
<script src="./../public/assets/config/language.js"></script>

<script src="./../public/sameparts/js/helper.js?v=1.006"></script>

<script src="./assets/scripts/main.js?v=1.0.5"></script>
<script src="./assets/scripts/navbar.js?v=1.0.8"></script>

<script src="./../integrations/companies/integrated/integrated_companies.js"></script>
<script src="./../integrations/companies/integrated/yemek_sepeti/script/initialize.js"></script>

<?php if(page_name == "report_safe") { ?>
    <script src = '../pos/assets/scripts/printer/invoice.js<?=$v?>' ></script >
    <script src = '../pos/assets/scripts/printer/invoice_type/safe.js<?=$v?>' ></script >
    <script src = '../pos/assets/scripts/printer/invoice_type/z_report.js<?=$v?>' ></script >
<?php } ?>

<script>
    if (window.module) module = window.module;
</script>
