<div class="container header" id="header">
    <div class="row pt-2">
        <div class="col-6 d-flex">
            <i class="mdi mdi-domain text-warning"></i>
            <p class="ml-2 branch-name text-secondary" id="branch_name"><?php echo $_SESSION["branch_name"];?></p>
        </div>
        <div class="col-6 d-flex flex-row-reverse">
            <i class="mdi mdi-account text-warning"></i>
            <p class="mr-2 waiter-name text-secondary" id="waiter_name"><?php echo $_SESSION["name"];?></p>
            <i id="restart_page" class="mdi mdi-restart mr-2 h5 text-success"></i>
        </div>
    </div>
</div>