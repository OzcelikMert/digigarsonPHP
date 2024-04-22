<div class="modal fade" id="modal_change_price" tabindex="-1" role="dialog" aria-labelledby="modal_change_price" aria-hidden="true">
    <div class="modal-dialog modal-xxl modal-lg" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h3 class="modal-title" id="change_price_title">Fiyat Değiştir</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <form id="modal_change_price_form">
                <!-- Modal Body -->
                <div class="modal-body p-0">
                    <div class="e_change_price container change-price">
                        <div class="row">
                            <button type="submit" class="col-4 btn btn-secondary" change-id="1">Normal</button>
                            <button type="submit" class="col-4 btn btn-secondary" change-id="2"><lang>CASING</lang></button>
                            <button type="submit" class="col-4 btn btn-secondary" change-id="3"><lang>TAKEAWAY</lang></button>
                            <button type="submit" class="col-4 btn btn-secondary" change-id="4"><lang>COME_TAKE</lang></button>
                            <button type="submit" class="col-4 btn btn-secondary" change-id="5">Personel</button>
                            <button type="submit" class="col-4 btn btn-secondary" change-id="6"><lang>OTHER</lang></button>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <table class="table">
                                    <thead class="thead-dark">
                                    <tr>
                                        <th scope="col" width="20%">Miktar</th>
                                        <th scope="col" width="60%">Ürün</th>
                                        <th scope="col" width="20%">Fiyat</th>
                                    </tr>
                                    </thead>
                                    <tbody class="e_change_price_products"></tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="submit" class="col-4 btn btn-secondary" change-id="7">Özel</button>
                </div>
            </form>

        </div>
    </div>
</div>

