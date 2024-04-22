<div class="container">
    <div class="row">
        <div class="col-12 text-center">
            <h2>Firma Bilgileri</h2>
        </div>
        <table class="table ">
            <thead class="">
                <tr>
                    <th>Şube Kodu</th>
                    <th>İsmi</th>
                    <th>Düzenle</th>
                </tr>
            </thead>
            <tbody id="branch-id"></tbody>
        </table>
        <button class="btn  btn-info" function="branch_img">Resim Ekle</button>
    </div>
    <div class="row margin_top_large">
        <div class="col-12 text-center">
            <h2>Yönetici Hesapları</h2>
        </div>
     <table class="table">
         <thead class="">
             <tr>
                 <th>id</th>
                 <th>İsim</th>
                 <th>Düzenle</th>
             </tr>
         </thead>
         <tbody id="manage_user_info"></tbody>
     </table>
     <button class="btn btn-info" function="add_manage_user">Kullanıcı Ekle</button>
 </div>
    <div class="row margin_top_large">
        <div class="col-12 text-center title">
            <h2>Kullanıcı Hesapları</h2>
        </div>
        <table class="table ">
            <thead class="">
            <tr>
                <th>id</th>
                <th>İsmi</th>
                <th>Düzenle</th>
            </tr>
            </thead>
            <tbody id="branch_user"></tbody>
        </table>
    </div>
    <div class="row margin_top_large">
        <div class="col-12 text-center">
            <h2>Şube Sipariş Silme</h2>
        </div>

        <div class="col-lg-12">
            <form class="form_choose_time">
                <div class="form-group">
                    <label for="">Zaman Seçin</label>
                    <input type="date" class="form-control col-6 orders_date">
                </div>
                <div class="form-group">
                    <button class="btn btn-success" function="delete_orders_list">Verileri Çek</button>
                </div>
            </form>

        </div>
        <table class="table">
            <thead class="">
            <tr>
                <th>id</th>
                <th>Yüklenme Zamanı</th>
                <th>Düzenle</th>
            </tr>
            </thead>
            <tbody id="delete_products"></tbody>
        </table>

        <div class="col-lg-12 d-flex align-items-center justify-content-lg-end">
            <div class="form-group">
                <button class="btn btn-danger" function="orders_all_delete">Hepsini Sil</button>
            </div>
            <div class="form-group ml-3">
                <button class="btn btn-success" function="orders_selected_delete">Seçilenleri Sil</button>
            </div>
        </div>
    </div>
</div>
