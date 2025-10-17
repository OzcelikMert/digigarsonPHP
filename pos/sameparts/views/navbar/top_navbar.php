<div class="dashboard-header">
    <nav class="navbar navbar-expand-lg fixed-top bg-navbar animate__fast">
        <ul class="navbar-nav ml-auto navbar-left-top">
            <li class="nav-item dropdown connection">
                <a class="nav-link" style="font-size: 25px;margin-left: 15px;" href="javascript:$('.pop_page_container').toggle()" id="e_location_pop_button"> <i class="fas fa-fw fa-th"></i> </a>
            </li>
        </ul>

        <a class="nav-link size-type-xl pl-0" href="#">MimiPos</a>
        <button class="e_btn_back_move_table btn btn-danger" style="display:none;"><i class="fa fa-arrow-alt-circle-left" ></i><lang>BACK</lang></button>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse " id="navbarSupportedContent">
            <ul class="e_top_navbar_section_right navbar-nav ml-auto navbar-right-top">
                <li class="nav-item dropdown notification" style="display: none">
                    <a class="nav-link nav-icons" href="#" id="navbarDropdownMenuLink1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-fw fa-bell"></i> <span class="indicator"></span></a>
                    <ul class="dropdown-menu dropdown-menu-right notification-dropdown">
                        <li>
                            <div class="notification-title"><lang>NOTIFICATION</lang></div>
                            <div class="notification-list">
                                <div class="list-group">
                                    <a href="#" class="list-group-item list-group-item-action active">
                                        <div class="notification-info">
                                            <div class="notification-list-user-img"><img src="assets/images/avatar-2.jpg" alt="" class="user-avatar-md rounded-circle"></div>
                                            <div class="notification-list-user-block"><span class="notification-list-user-name">
                                                <div class="notification-date"></div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="list-footer"> <a href="#"></a></div>
                        </li>
                    </ul>
                </li>
                <li class="nav-item" style="padding-top: 5px;padding-bottom: 4px;padding-right: 10px;">
                    <div class="d-block float-right" style="">
                        <button class="e_session_change btn btn-lg br btn-s2 size-type-lg"><i class="mdi mdi-outdoor-lamp"></i><lang>LOCK_SESSION</lang></button>
                    </div>
                </li>

                <li class="nav-item dropdown nav-user">
                    <a class="nav-link nav-user-img" href="#" id="user" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img id="branch_avatar" src="../images/branches/<?=$_SESSION["branch_id"]?>/logo/logo.webp" alt="" class="user-avatar-md rounded-circle"></a>
                    <div class="dropdown-menu dropdown-menu-right nav-user-dropdown" aria-labelledby="user">
                        <div class="nav-user-info">
                            <h5 class="mb-0 text-white nav-user-name"><?=$_SESSION["user_name"]?> </h5>
                            <small class="text-light"><?=$_SESSION["branch_name"]?></small>
                        </div>

                        <!--a class="dropdown-item" href="#"><i class="fas fa-spin mr-2"></i>Yaklaştır</a-->
                        <a app-function="full_screen" class="dropdown-item" href="#"><i class="fas fa-spin mr-2"></i><lang>FULL_SCREEN</lang></a>
                        <a app-function="app_page_reload" class="dropdown-item" href="#"><i class="fas fa-spin mr-2"></i><lang>REFRESH</lang></a>
                        <a app-function="app_minimize" class="dropdown-item" href="#"><i class="fas fa-minus mr-2"></i><lang>HIDE</lang></a>
                        <a app-function="app_quit" class="dropdown-item" href="#"><i class="fas fa-times mr-2"></i><lang>CLOSE</lang></a>
                    </div>
                </li>

                <li class="nav-item" style="padding-top: 5px;padding-bottom: 4px;padding-right: 10px;">
                     <div id="order-navbar-buttons" class="d-block float-right" style="">
                         <?php if(page_name == "orders") { ?>
                             <?php if(isset($_SESSION["permission"][10])) {?><button table-type="3" table-id="2" table-takeaway-manual="" class="e_table btn btn-lg br btn-s3 size-type-lg"><i class="mdi mdi-motorbike"></i><lang>TAKEAWAY</lang></button><?php } ?>
                             <?php if(isset($_SESSION["permission"][11])) {?><button table-type="2" table-id="1" class="e_table btn btn-lg br btn-s1 size-type-lg"><i class="mdi mdi-wallet-outline"></i><lang>SAFE_SALE</lang></button><?php } ?>
                         <?php }  ?>
                     </div>
                </li>
            </ul>
        </div>
    </nav>
</div>