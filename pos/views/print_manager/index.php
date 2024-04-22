<!doctype html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Yazdırma Yöneticisi</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h3 class="text-center"><lang>PRINT_MANAGER</lang></h3>
        </div>
        <div class="col-7">
            <p><lang>PRINT_CONTROL</lang></p>
            <table class="table table-striped w-100">
                <thead>
                    <tr><th><lang>HOUR</lang></th> <th><lang>TABLE</lang></th> <th><lang>GROUP</lang></th> <th><lang>PRINTER</lang></th> <th><lang>STATUS</lang></th> <th>T.Yazdır</th> </tr>
                </thead>
                <tbody class="e_print_log"></tbody>
            </table>
        </div>
        <div class="col-5">
            <p><lang>PRINT_REQUEST</lang></p>
            <table class="table table-striped w-100">
                <thead>
                <tr> <th><lang>HOUR</lang></th> <th><lang>INFO</lang></th>  <th><lang>RECEIPT_TYPE</lang></th></tr>
                </thead>
                <tbody class="e_print_data_log"></tbody>
            </table>
        </div>
    </div>
</div>
</body>
<?php
$v = "?v=".time();
?>
<script>let app = {print_manager:true}; </script>
<script>if (typeof module === 'object') {window.module = module; module = undefined;}</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" ></script>
<script src="../../../public/assets/config/settings.js<?=$v?>"></script>
<script src="../../../matrix_library/js/operations/array_list.js<?=$v?>"></script>
<script src="../../../public/sameparts/js/helper.js?<?=$v;?>"></script>
<script src="../../../public/assets/config/languages/language_tr.js<?=$v;?>"></script>
<script src="../../../public/assets/config/language.js<?=$v;?>"></script>
<script src="../../assets/scripts/app.js<?=$v?>"></script>
<script src="../../assets/scripts/printer/invoice_new.js<?=$v?>"></script>
<script src="../../assets/scripts/printer/print_manager.js<?=$v?>"></script>
<script src="../../assets/scripts/printer/invoice_type/safe.js<?=$v?>"></script>
<script src="../../assets/scripts/printer/invoice_type/kitchen.js<?=$v?>"></script>
<script src="../../assets/scripts/printer/invoice_type/z_report.js<?=$v?>"></script>
</html>