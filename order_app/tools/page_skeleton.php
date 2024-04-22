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
    <page>
        <top-navbar>
            <?php if(page_name != "index"){ require "./sameparts/views/navbar/top_navbar.php"; } ?>
        </top-navbar>
        <page-in>
            <?=page_body?>
        </page-in>
        <bottom-navbar>
            <?php  if(page_name != "index"){  require "./sameparts/views/navbar/bottom_navbar.php"; }?>
        </bottom-navbar>
    </page>

    <scripts>
        <?php require "./tools/scripts.php"; ?>
        <?=custom_scripts?>
        <script src="./../public/assets/config/check.js?v=1.0.0"></script>
    </scripts>
</body>
</html>



<?php
//if(page_name != "index"){
?>