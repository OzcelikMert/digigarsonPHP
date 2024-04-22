<div class="pop_page_container" style="display: none">

    <div class="nav-menu">
        <div class="title box-s-s1">
            <h3><lang>MENU</lang></h3>
        </div>
        <div class="pop_page ">
            <a class="box-s-s1" href="orders.php"> <i class="fas fa-desktop"></i><lang>ORDERS</lang></a>
            <?php if(isset($_SESSION["permission"][12])) {?><a class="box-s-s1" href="products.php"><i class="fas fa-utensils"></i><lang>PRODUCTS</lang></a><?php } ?>
            <?php if(isset($_SESSION["permission"][13])) {?><a class="box-s-s1" href="finance.php"><i class="fas fa-calculator"></i><lang>CASING</lang></a><?php } ?>
            <a class="box-s-s1" href="settings.php"><i class="fas fa-cog"></i><lang>SETTINGS</lang></a>
        </div>
    </div>

</div>