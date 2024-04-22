<div class="modal fade" id="modal_trust_account" tabindex="-1" role="dialog" aria-labelledby="modal_trust_account" aria-hidden="true">
    <div class="modal-dialog modal-xxl modal-lg" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h3 class="modal-title" id="payment_trust_account"><lang>CREDIT</lang></h3>
                <div class="w-100 text-right pr-5">
                    <button class="e_new_trust_account btn btn-success">
                        <i class="fa fa-plus"></i>
                        <lang>NEW_ADD</lang>
                    </button>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-0">
                <div class="container">
                    <div class="row mt-3">
                        <div class="col">
                            <input function="id" type="number" class="e_search_trust_account form-control" placeholder="No">
                        </div>
                        <div class="col">
                            <input function="name" type="text" class="e_search_trust_account form-control" placeholder="İsim">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <table class="table">
                                <thead class="thead-dark">
                                <tr>
                                    <th scope="col" width="20%"><lang>ID</lang></th>
                                    <th scope="col" width="60%"><lang>NAME</lang></th>
                                    <th scope="col" width="20%"><lang>DISCOUNT</lang> (%)</th>
                                </tr>
                                </thead>
                                <tbody class="e_trust_accounts"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer"></div>

        </div>
    </div>
</div>

