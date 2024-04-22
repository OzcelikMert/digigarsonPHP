<MODALS>
    <?php
        $dir = __DIR__;
        require_once($dir."/modals/modal_product_details.php");
        require_once($dir."/modals/modal_barcode_system.php");
        require_once($dir."/modals/modal_order_status_types.php");
        if (isset($_SESSION["permission"][7])) require_once($dir."/modals/modal_payment_types.php");
        if (isset($_SESSION["permission"][7])) require_once($dir."/modals/modal_payment.php");
        if (isset($_SESSION["permission"][4])) require_once($dir."/modals/modal_delete_product.php");
        if (isset($_SESSION["permission"][3])) require_once($dir."/modals/modal_catering_product.php");
        if (isset($_SESSION["permission"][9])) require_once($dir."/modals/modal_change_price.php");
        if (isset($_SESSION["permission"][6])) require_once($dir."/modals/modal_separate_product.php");
        require_once($dir."/modals/modal_trust_account.php");
        if (isset($_SESSION["permission"][14])) require_once($dir."/modals/modal_new_trust_account.php");
        if (isset($_SESSION["permission"][8])) require_once($dir."/modals/modal_discount.php");
        if (isset($_SESSION["permission"][10])) require_once($dir."/modals/modal_caller_choose.php");
    ?>
</MODALS>

