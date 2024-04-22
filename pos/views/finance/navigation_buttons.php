<div class="buttons p-0 pt-2 pb-2  row" style="overflow: hidden;">
    <div class="col-12">
        <?php if(isset($_SESSION["permission"][1])) { ?><button class="e_safe_close btn btn-s3 size-type-lg float-left"><i class="fas fa-power-off"></i> <lang>SAFE_CLOSE</lang></button><?php } ?>
        <?php if(isset($_SESSION["permission"][1])) { ?><button class="e_print_z_report btn btn-s1 ml-2 size-type-lg float-left"><i class="fas fa-print"></i> Z Raporu Yazdır</button><?php } ?>
        <?php if(isset($_SESSION["permission"][2])) { ?><button function="trust" class="e_navigation_btn btn btn-s1 size-type-lg float-right"><i class="fas fa-th-list"></i> <lang>DEBT_LIST</lang></button><?php } ?>
        <button function="invoice" class="e_navigation_btn btn btn-s1 size-type-lg float-right mr-2"><i class="fas fa-receipt"></i><lang>REPCEIPT_LIST</lang></button>
        <button function="safe" class="e_navigation_btn btn btn-s1 size-type-lg float-right mr-2"><i class="fas fa-archive"></i> <lang>CASING</lang></button>
        <?php if(isset($_SESSION["permission"][16])) { ?><button function="cost" class="e_navigation_btn btn btn-s4 size-type-lg float-right mr-2"><i class="fas fa-coins"></i> <lang>TOTAL_COST</lang></button><?php } ?>
    </div>
</div>