<?php
    if(page_name != "index" && page_name != "__admin_login") {
       // include "./sameparts/functions/sessions/check.php";
        \config\sessions\check::check();
    }
?>


<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?=page_title?></title>
    <?php require "./tools/head.php"; ?>
    <?=custom_links?>
</head>
<body>
    <div id="page">
    <?php
    if(page_name != "index"){
        require "./sameparts/views/navbar/top_navbar.php";
        require "./sameparts/views/navbar/modal_sing_out.php";
        require "./sameparts/views/navbar/page_pop.php";
        require "./sameparts/views/caller_id/modal_new_customer.php";
        require "./sameparts/views/caller_id/modal_new_order.php";
        require "./sameparts/views/notifications/index.php";

    }
    require "./sameparts/views/keyboard/keyboard.html";
    ?>
    <?=page_body?>
    </div>

    <?php require "./tools/scripts.php"; ?>
    <?=custom_scripts?>
    <script src="./../public/assets/config/check.js?v=1.0.0"></script>
</body>
</html>