<div class="container header" id="header">
    <div class="row pt-2">
        <div class="col-6 d-flex">
            <i class="mdi mdi-domain"></i>
            <p class="ml-2 branch-name" id="branch_name"><?php echo $_SESSION["branch_name"];?></p>
        </div>
        <div class="col-6 d-flex flex-row-reverse">
            <i class="mdi mdi-account"></i>
            <p class="mr-2 waiter-name" id="waiter_name"><?php echo $_SESSION["name"];?></p>
        </div>
    </div>
</div>