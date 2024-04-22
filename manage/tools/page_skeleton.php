<?php if(page_name != "index") { \config\sessions\check::check(); } ?>
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
    <div id="page" <?php if(page_name != "index" ) { ?> class="dashboard-main-wrapper" <?php } ?>>

        <?php
            if(page_name != "index"){
                require "./sameparts/views/navbar/modals.php";
                require "./sameparts/views/navbar/top_navbar.php";
                require "./sameparts/views/navbar/page_pop.php";
                require "./sameparts/views/navbar/side_bar.php";
            }
        ?>
        <?php if(page_name != "index" ) { ?>
        <div class="dashboard-wrapper">
            <div class="container-fluid  dashboard-content">
        <?php } ?>

                <?=page_body?>

        <?php if(page_name != "index" ) { ?>
            </div>
        </div> 
        <?php } ?>

    </div>

    <?php require "./tools/scripts.php"; ?>
    <?=custom_scripts?>
    <script src="./../public/assets/config/check.js?v=1.0.0"></script>
</body>
</html>