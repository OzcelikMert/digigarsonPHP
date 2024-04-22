<div class="nav-left-sidebar sidebar-dark">
    <div class="menu-list">
        <nav class="navbar navbar-expand-lg navbar-light" >
            <a class="d-xl-none d-lg-none" href="#"><lang>MANAGE_PANEL</lang></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#sidebar" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="sidebar">
                <ul class="navbar-nav flex-column">
                    <li class="nav-divider">
                        <lang>MENU</lang>
                    </li>

                    <li class="nav-item nav-item-mobile">
                        <a class="nav-link" href="javascript:void(0)" aria-expanded="false"><i class="fas fa-user"></i> <?=$_SESSION["user_name"]?></a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#" data-toggle="collapse" aria-expanded="false" data-target="#submenu-1" aria-controls="submenu-1"><i class="fa fa-fw fa-user-circle"></i>Raporlar<span class="badge badge-success">6</span></a>
                        <div id="submenu-1" class="collapse submenu" style="">
                            <ul class="nav flex-column">
                                <li class="nav-item"><a page="dashboard" class="nav-link" href="dashboard.php"><lang>HOME_PAGE</lang></a></li>
                                <li class="nav-item"><a page="report_safe" class="nav-link" href="report_safe.php"><lang>CASE_REP</lang></a></li>
                                <li class="nav-item"><a page="report_product" class="nav-link" href="report_product.php"><lang>PRODUCT_REP</lang></a></li>
                            </ul>
                        </div>
                    </li>

                    <!--li class="nav-item">
                        <a class="nav-link" href="#" data-toggle="collapse" aria-expanded="false" data-target="#submenu-2" aria-controls="submenu-2"><i class="fa fa-utensils"></i>Ürünler</a>
                        <div id="submenu-2" class="collapse submenu" style="">
                            <ul class="nav flex-column">
                                <li class="nav-item"><a class="nav-link" href="#">Ürünler Listesi</a></li>
                                <li class="nav-item"><a class="nav-link" href="#">Ürünler Ekle</a></li>
                                <li class="nav-item"><a class="nav-link" href="#">Opsiyon Listesi</a></li>
                                <li class="nav-item"><a class="nav-link" href="#">Opsiyon Ekle</a></li>
                                <li class="nav-item"><a class="nav-link" href="#">Kategori Listesi</a></li>
                                <li class="nav-item"><a class="nav-link" href="#">Kategori Ekle</a></li>
                            </ul>
                        </div>
                    </li-->

                    <li class="nav-item">
                        <a page="products" class="nav-link" href="products.php" aria-expanded="false"><i class="fa fa-fw fa-utensils"></i><lang>PRODUCTS</lang></a>
                    </li>

                    <!--li class="nav-item">
                        <a page="tables" class="nav-link" href="tables.php" aria-expanded="false"><i class="fa fa-fw fa-chair"></i>Masa Düzeni</a>
                    </li-->

                    <li class="nav-item">
                        <a class="nav-link" href="#" data-toggle="collapse" aria-expanded="false" data-target="#submenu-5" aria-controls="submenu-5"><i class="fa fa-fw fa-bookmark"></i><lang>AUTHORUZATION</lang></a>
                        <div id="submenu-5" class="collapse submenu" style="">
                            <ul class="nav flex-column">
                                <li class="nav-item">
                                    <a class="nav-link" page-group="settings" href="#" data-toggle="collapse" aria-expanded="false" data-target="#submenu-7" aria-controls="submenu-7">POS</a>
                                    <div id="submenu-7" class="collapse submenu" style="">
                                        <ul class="nav flex-column">
                                            <li class="nav-item"><a page="list_user" class="nav-link" href="list_user.php"><lang>USER_LIST</lang></a></li>
                                            <li class="nav-item"><a page="list_device" class="nav-link" href="list_device.php"><lang>DEVICE_LIST</lang></a></li>
                                            <li class="nav-item"><a page="catering" class="nav-link" href="catering.php"><lang>CATERING</lang></a></li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#" data-toggle="collapse" aria-expanded="false" data-target="#submenu-3" aria-controls="submenu-3"><i class="fas fa-fw fa-cogs"></i>Ayarlar</a>
                        <div id="submenu-3" class="collapse submenu" style="">
                            <ul class="nav flex-column">
                                <li class="nav-item">
                                    <a page="branch_settings" class="nav-link" href="branch_settings.php">Firma</a>
                                </li>
                                <li class="nav-item">
                                    <a page="settings_integration" class="nav-link" href="settings_integration.php">Entegrasyon</a>
                                </li>
                                <li class="nav-item">
                                    <a page="settings_table" class="nav-link" href="settings_table.php">Masalar</a>
                                </li>
                            </ul>

                        </div>
                    </li>

                    <?php if($_SESSION["is_main"]) { ?>
                        <li class="nav-item nav-item-mobile">
                            <a function="modal" class="e_branch_btn nav-link"><i class="fa fa-building"></i> <span class="e_company_title"> Firmalarım</span></a>
                        </li>
                    <?php } ?>

                    <li class="nav-item nav-item-mobile">
                        <a function="quit" class="e_navbar_btn nav-link" aria-expanded="false"><i class="fas fa-sign-out-alt"></i> Çıkış</a>
                    </li>

                </ul>


            </div>
        </nav>
    </div>
</div>