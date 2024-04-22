<MODALS>
    <?php
        if (isset($_SESSION["permission"][15])) require_once(__DIR__."/modals/modal_new_account.php");
        require_once(__DIR__."/modals/modal_trust_account_info.php");
        require_once(__DIR__."/modals/modal_trust_account_payment.php");
        require_once(__DIR__."/modals/modal_invoice_show.php");
    if (isset($_SESSION["permission"][16])) require_once(__DIR__."/modals/modal_cost.php");
    ?>
</MODALS>

