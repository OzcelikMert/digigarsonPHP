<input type="text" style="opacity: 0" id="copy_table" value="">
<div class="container">
    <div class="row">
        <div class="col-lg-5">
            <form id="addBranchTable">
                <div class="segment">
                    <h1 style="font-size: 24px !important;">Masa Oluştur</h1>
                </div>
                <div class="form-group">
                    <label> Şube Seç</label>
                    <select name="branch_no" id="branch_id" class="form-control branch_list">

                    </select>
                </div>
                <div class="form-group">
                    <label>Alan Seçimi</label>
                    <div class="islem">
                        <select id="table_section" class="form-control" name="table_section"></select>
                    </div>
                </div>


                <div class="form-group">
                    <input type="number" class="form-control " id="table_start" name="table_start" placeholder="Masa Başlangıç" />
                </div>
                <div class="form-group">
                    <input type="number" class="form-control " id="table_end" name="table_end" placeholder="Masa Bitiş" />
                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="without_session"> Oturumsuz Sipariş (Mobilden Gelen Müşteriler İçin)
                    </label>
                </div>
                <div class="form-group text-right mr-4">

                    <button type="button" class="btn btn-primary d-inline-block mt-3" function="new_section">Yeni Bölüm Oluştur</button>

                    <button class="btn btn-success d-inline-block mt-3" type="submit"><i class="icon ion-md-lock"></i> Masa Ekle</button>
                </div>
            </form>
            <div class="search_table" style=" margin-top: 50px;">
                <div class="segment">
                    <h1 style="font-size: 24px !important;">Masa Görüntüle</h1>
                </div>
                <div class="form-group">
                    <label>Şube Seç</label>
                    <select id="search_branch_id" class="form-control branch_list"></select>
                </div>
                <div class="form-group text-right mr-4">
                    <button class="btn btn-warning mt-2" function="getBranchTable" type="submit">Masa Ara <i class="fa fa-search"></i></button>
                </div>
            </div>
            <div class="col-lg-12">
                <ol id="section_url">

                </ol>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="out_table">
                <!-- Table Content -->
            </div>
        </div>

    </div>
</div>