<div class="container-fluid my-3 py-2 ">
    <div class="d-flex justify-content-end align-items-center">
        <button class="btn btn-primary" function="add">
            Firma Ekle
        </button>
    </div>
    <div class="row">
        <!-- Grid column -->
        <div class="col-md-12 pb-5">
            <h2 class="pt-3 pb-4 text-center font-bold font-up deep-purple-text "  >FİRMA TABLOSU</h2>
            <div class="input-group md-form form-sm form-2 pl-0 search">
                <input class="form-control pl-3 purple-border py-3" type="text" id="branch_search" placeholder="Firma Arayın..." aria-label="Search">
                <span class="input-group-addon waves-effect purple lighten-2" id="basic-addon1">
                    <a>
                        <i class="fa fa-search white-text" aria-hidden="true"></i>
                    </a>
                </span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <table class="table table-striped">
                <thead class=" table-dark">
                    <tr class="" style="background-color: #007bff;">
                        <th>#</th>
                        <th>Firma Adı</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody id="branch_table" class="td-th">
                <!--Table Content-->
                </tbody>
            </table>
        </div>
    </div>
</div>


