<div class="modal fade" id="modal_payment" tabindex="-1" role="dialog" aria-labelledby="modal_payment_types_fast" aria-hidden="true">
    <div class="modal-dialog modal-xxl modal-lg" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h3 class="modal-title" id="payment_title"><lang>PAYING</lang></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-0">
                <div class="container-fluid m-0 p-0 modal_payment" style="min-height: 400px">
                    <div class="row m-0">
                        <div class="col-4 px-1">
                            <h4 class="mb-0"><lang>COLLECTIONS</lang></h4>
                            <table class="table table-sm table-striped">
                                <tbody class="e_modal_payment_payments"></tbody>
                            </table>

                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th><lang>QUANTITY</lang></th>
                                        <th><lang>PRODUCT</lang></lang></th>
                                        <th><lang>PRICE</lang></th>
                                    </tr>
                                </thead>
                                <tbody class="e_modal_payment_products"></tbody>
                            </table>
                        </div>

                        <div class="middle col-5 px-1">
                            <div class="top mt-4">
                                <div class="item">
                                    <p><lang>TOTAL_REMAIN</lang>:</p>
                                    <h3 function="total_price" class="text-right">
                                        <span function="price">0,00</span>
                                        <span function="currency">₺</span>
                                    </h3>
                                </div>
                                <div class="item">
                                    <p><lang>PAY_QUANTITY</lang>:</p>
                                    <h3 function="payment_price" class="text-right">
                                        <span function="price">0,00</span>
                                        <span function="currency">₺</span>
                                    </h3>
                               </div>
                                <div class="e_change_price item" style="display: none">
                                    <p><lang>REMAINDER_OF_MONEY</lang>:</p>
                                    <h3 function="change_price" class="text-right">
                                        <span function="price">0,00</span>
                                        <span function="currency">₺</span>
                                    </h3>
                                </div>
                            </div>
                            <div class="middle">
                                <table class="table table-sm table-striped">
                                    <thead>
                                    <tr>
                                        <th><lang>QUANTITY</lang></th>
                                        <th><lang>PRODUCT</lang></th>
                                        <th><lang>PRICE</lang></th>
                                    </tr>
                                    </thead>
                                    <tbody class="e_modal_payment_products_selected"></tbody>
                                </table>
                            </div>
                            <div class="e_payment_calculator bottom mt-5">
                                <button value="-6" class="btn btn-s8 btn-numPad">10</button>
                                <button value="1" class="btn btn-s8 btn-numPad">1</button>
                                <button value="2" class="btn btn-s8 btn-numPad">2</button>
                                <button value="3" class="btn btn-s8 btn-numPad">3</button>
                                <button value="-3" class="btn btn-s8 btn-numPad">C</button>
                                <button value="-6" class="btn btn-s8 btn-numPad">20</button>
                                <button value="4" class="btn btn-s8 btn-numPad">4</button>
                                <button value="5" class="btn btn-s8 btn-numPad">5</button>
                                <button value="6" class="btn btn-s8 btn-numPad">6</button>
                                <button value="-1" class="btn btn-s8 btn-numPad"><-</button>
                                <button value="-6" class="btn btn-s8 btn-numPad">50</button>
                                <button value="7" class="btn btn-s8 btn-numPad">7</button>
                                <button value="8" class="btn btn-s8 btn-numPad">8</button>
                                <button value="9" class="btn btn-s8 btn-numPad">9</button>
                                <button value="-5" class="btn btn-s8 btn-numPad"><lang>ALL</lang></button>
                                <button value="-6" class="btn btn-s8 btn-numPad">100</button>
                                <button value="-2" class="btn btn-s8 btn-numPad">,</button>
                                <button value="-9" class="btn btn-s8 btn-numPad">00</button>
                                <button value="0" class="btn btn-s8 btn-numPad">0</button>
                                <button value="-4" class="btn btn-s8 btn-numPad"><sup>1</sup>/<sub><lang>PERSON</lang></sub></button>
                                <button value="-7" class="btn btn-s8 btn-numPad" style="grid-column: 3/6;grid-row: 5;width: 100%;"><lang>ROUNDING</lang></button>
                                <button value="-8" class="btn btn-s8 btn-numPad" style="grid-column: 1/3;grid-row: 5;width: 100%;"><i class="fa fa-percent"></i> <lang>DISCOUNT_COUNT</lang></button>
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="payment-btn-list">
                                <button class="e_payment_btn btn btn-s1 w-100 p-4 mt-2" type-id="1"><lang>CASH</lang></button>
                                <button class="e_payment_btn btn btn-s1 w-100 p-4 mt-2" type-id="2"><lang>CREDIT_CARD</lang></button>
                                <button class="e_payment_btn btn btn-s1 w-100 p-4 mt-2" type-id="6"><lang>CREDIT</lang></button>
                                <div class="e_modal_payment_payment_types_custom"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>

