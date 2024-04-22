<div class="container">
    <div class="row mt-5">
        <div class="col-lg-5">
            <form id="form_product">
                <div class="form-header ">
                    <h3>Firma ürünleri</h3>
                </div>
                <div class="form-group ">
                    <label for="">Kopyala</label>
                    <select name="branch_id_owner" id="branch_id" class="form-control item branch_list"></select>

                </div>
                <div class="form-group" >
                    <label for="">Yapıştır</label>
                    <select name="branch_id_target" class="form-control item branch_list"></select>

                </div>
                <div class="form-group ">
                    <button type="button" function="get" class="btn btn-info  mb-2 create-account"> Getir</button>
                    <button type="submit" function="copy_paste" class="btn btn-danger  create-account" style="position: absolute; right: 20px;"> Kopyala & Yapıştır</button>

                </div>
            </form>
        </div>
        <div class="col-lg-7 mt-5">
            <table class="table table-dark">
                <thead>
                <tr>
                    <td>#</td>
                    <td>isim</td>
                    <td>kategori</td>
                </tr>
                </thead>
                <tbody id="product_table"></tbody>
            </table>
        </div>
    </div>
</div>