<?php  if(page_name != "index"){
    include "./sameparts/functions/sessions/check.php";
    \waiter_terminal\sameparts\functions\sessions\check::check();
}?>
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
                require "./sameparts/views/header/header.php";
            }
        ?>
        <?=page_body?>
    </div>

    <?php require "./tools/scripts.php"; ?>
    <?=custom_scripts?>
</body>
</html>