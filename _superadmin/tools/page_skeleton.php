<?php if(page_name != "login") {
    include "./sameparts/functions/sessions/check.php"; \_superadmin\sameparts\functions\sessions\check::check();
} ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" href="./sameparts/assets/img/digilogo.png">
    <title><?=page_title?></title>
    <?php require "./tools/head.php"; ?>
    <?=custom_links?>
</head>

<body>

<?php
if(page_name != "login"){
    require "./sameparts/view/sidebar.php";
}
?>

<div class="content">
    <?= page_body;?>
</div>
<!-- End content -->


<?php require "./tools/scripts.php"; ?>
<?=custom_scripts?>
</body>
</html>