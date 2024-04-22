<div class="page-table-detail animate__animated animate__faster table-detail-pages" style="display:none;" id="table_detail">
    <!-- Table Header -->
    <div class="container header" id="table_header">
        <div class="row pt-2">
            <div class="col-2 d-flex flex-row">
                <i class="e_table_page_btn mdi mdi-arrow-left-circle table-page-btn" function='back'></i>
            </div>
            <div class="col-6 d-flex flex-row">
                <font class="e_table_info  table-section-and-number" id="table-section-and-number"></font>
            </div>
            <div class="col-4 d-flex flex-row-reverse">
                <div class="e_table_page_btn table-page-btn" style="display:contents;" function='show'>
                    <i class="mdi mdi-cart"></i>
                    <p class="mr-2" id="table_basket_count">0</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Table Slides And Scroller -->
    <div class="swiper-container pb-5" id="table-swiper">
        <div class="swiper-wrapper" id="table-swiper-wrapper">
          <div class="swiper-slide pb-5" style="background: #3b3b3b" >
            <div class="container" style="margin-top: 19px;">
                <div class="row">
                    <div class="col-12 order-total-text">
                        <lang>TOTAL_AMOUNT</lang>: <font class="e_table_total order-total-price" id="order-total-price"></font>
                        <button class="e_add_new_order btn btn-primary btn-sm float-right mt-2 add_new_order" function="new">
                            <i class="mdi mdi-plus"></i>
                            <lang>NEW_ORDER</lang>
                        </button>
                    </div>
                    <div class="col-12 mt-5">
                        <div class="accordion row e_orders" id="orders"></div>
                    </div>
                </div>
            </div>
          </div>
          <div class="swiper-slide product-pages">
            <div class="container pb-5 ">
                <div class="input-group mt-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text product-search" id="search_category_icon"><i class="mdi mdi-magnify"></i></span>
                    </div>
                    <input type="search" class="form-control" id="search_category" placeholder="Kategori İsimi" aria-describedby="search_category_icon">
                </div>
                <div class="row e_product_categories">
                </div>
            </div>
          </div>
          <div class="swiper-slide product-pages">
            <div class="container pb-5">
                <div class="input-group mt-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text product-search" id="search_product_icon"><i class="mdi mdi-magnify"></i></span>
                    </div>
                    <input type="search" class="form-control" id="search_product" placeholder="Ürün İsmi" aria-describedby="search_product_icon">
                </div>
                <div class="row e_products" id="products"></div>
            </div>
          </div>
        </div>
        <!-- Add Pagination -->
        <div class="swiper-pagination" id="table-swiper-pagination"></div>
        <!-- Add Scrollbar -->
        <div class="swiper-scrollbar" id="table-swiper-scrollbar"></div>
    </div>
</div>