<div class="dashboard-header">
    <nav class="navbar navbar-expand-lg fixed-top bg-navbar animate__fast">
        <ul class="navbar-nav navbar-left-top">
            <li class="nav-item dropdown connection">
                <a class="nav-link size-type-xl pl-3" href="dashboard.php">
                    <img width="200" src="https://localhost/HomeFiles/images/logo/mimi.png" alt="Mimi">
                </a>
            </li>
        </ul>
        <div class="collapse navbar-collapse " id="navbarSupportedContent">
            <ul class="e_top_navbar_section_right navbar-nav ml-auto navbar-right-top">

                <?php if($_SESSION["is_main"]) { ?>
                    <li class="nav-item pt-2">
                        <button function="modal" class="e_branch_btn btn btn-primary btn-lg"><i class="fa fa-building"></i> <span class="e_company_title"><lang>MY_BRANCH</lang></span></button>
                    </li>
                <?php } ?>

                <li class="nav-item dropdown notification d-none">
                    <a class="nav-link nav-icons" href="#" id="navbarDropdownMenuLink1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-fw fa-bell"></i> <span class="indicator"></span></a>
                    <ul class="dropdown-menu dropdown-menu-right notification-dropdown">
                       
                        <li>
                            <div class="notification-title"><lang>NOTIFICATION</lang></div>
                            <div class="notification-list">
                                <div class="list-group">
                                    <a href="#" class="list-group-item list-group-item-action active">
                                        <div class="notification-info">
                                            <div class="notification-list-user-img"><img src="assets/images/avatar-2.jpg" alt="" class="user-avatar-md rounded-circle"></div>
                                            <div class="notification-list-user-block"><span class="notification-list-user-name">Jeremy Rakestraw</span>accepted your invitation to join the team.
                                                <div class="notification-date">2 min ago</div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="list-footer"> <a href="#"><lang>VIEW_ALL_NOTIFICATION</lang></a></div>
                        </li>
                    </ul>
                </li>

                <li class="nav-item dropdown nav-user">
                    <a class="nav-link nav-user-img" href="#" id="user" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="../images/branches/<?=$_SESSION["branch_id"]?>/logo/logo.webp" alt="" class="user-avatar-md rounded-circle"></a>
                    <div class="dropdown-menu dropdown-menu-right nav-user-dropdown" aria-labelledby="user">
                        <div class="nav-user-info">
                            <h5 class="mb-0 text-white nav-user-name"><?=$_SESSION["user_name"]?> </h5>
                            <small class="text-light" id="branch_name"><?=$_SESSION["branch_name"]?></small>
                        </div>
                        <a function="quit" class="e_navbar_btn dropdown-item text-dark"><i class="fas fa-times mr-2"></i><lang>EXIT</lang></a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</div>