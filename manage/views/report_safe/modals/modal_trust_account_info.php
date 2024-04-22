<div class="modal fade" id="modal_trust_account_info" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-xxl modal-lg" role="document">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h3 class="modal-title" id="modal_trust_account_info_title"><lang>ACCOUNT_INFO</lang> **(<b function="name"></b>)** </h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-0">
                <div class="row">
                    <div class="col-12">
                        <table class="table table-striped table-sticky bg-theme-2 br text-left">
                            <tr>
                                <th><lang>DESCRIPTION</lang></th>
                                <th><lang>DATE</lang></th>
                                <th><lang>DISCOUNT</lang> (%)</th>
                                <th><lang>PRICE</lang></th>
                                <th><lang>CURRENT_AMOUNT</lang></th>
                                <th width="20"><lang>DELETE</lang></th>
                            </tr>
                            <tbody class="e_trust_account_info_payments"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <div class="col-12">
                    <div class="col-4 float-right">
                        <button class="btn btn-secondary w-100" data-target="#modal_trust_account_payment" data-toggle="modal"><lang>PAY</lang></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>