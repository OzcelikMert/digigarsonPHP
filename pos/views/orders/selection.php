<div class="container-fluid p-0 nav-mt-show">
    <div id="tables" class="tables-and-selections">
        <div class="order-table-group w-100 row m-0 p-0 pt-2" >
            <div id="table_group_integrations">
                <div class="e_table_group_yemek_sepeti"></div>
            </div>
            <div id="table_group"></div>
        </div>

        <div class="bottom-groups fixed-bottom">
            <div id="selection-buttons" class="in d-flex" style="overflow-y: hidden;overflow-x: auto">#sections</div>
 
        </div>
    </div>

    <div id="table_details" class="order-table-in row p-1 m-0" style="display: none">
            <div class="col-4 p-0">
                <div class="row w-100 p-0 m-0">
                    <div class="col-md-12 p-1 m-0 row">
                        <div class="col-2 p-0">
                            <button id="back_order_detail" style="margin-top: -3px" class="btn-icon-lg"><i class="fa fa-arrow-alt-circle-left text-c2" ></i></button>
                        </div>
                        <div class="col-3 p-0 mt-2 text-center">
                            <h4 class="e_table_title m-0 table-title overflow-hidden">SECTION NO</h4>
                            <h5 class="e_table_title_info"></h5>
                        </div>
                        <div class="col-7 p-0 mt-2">
                            <h5 class="e_table_price_total m-0 amount-display"><span function="price">00.00</span><span function="currency">₺</span> </h5>
                        </div>
                    </div>

                    <div class="col-9 p-0 m-0 list scroll-y">
                        <table id="order_list_table" class="table table-striped table-sticky bg-theme-2 br text-left table-sm">
                            <thead>
                                <tr class="size-type-xxsm">
                                    <th width="5%" class="e_order_table_hide_column" style="display: none"></th>
                                    <th width="15%" style="width:5px;"><lang>QUANTITY</lang></th>
                                    <th width="60%"><lang>PRODUCT</lang></th>
                                    <th width="20%"><lang>PRICE</lang></th>
                                </tr>
                            </thead>

                            <tbody id="order_list" class="order-list">


                            </tbody>

                        </table>
                    </div>

                    <div class="buttons col-3 p-0 m-0" style="overflow: hidden;padding-left: 5px !important;" >
                        <button table="" function="new_order" class="e_order_btn btn-s1 br fw-6 w-100"><lang>NEW_ORDER</lang></button>
                        <?php if(isset($_SESSION["permission"][5])) {?><button table="" function="move_table" class="e_order_btn btn-s1 br fw-6 mt-1 w-100"><lang>TABLE_MOVE</lang></button><?php } ?>
                        <button table="" function="order_combining" class="e_order_btn btn-s1 br fw-6 mt-1 w-100"><lang>ADDITON_COMBINATION</lang></button>
                        <?php if(isset($_SESSION["permission"][6])) {?><button table="" table-take-away-confirmed="" function="separate_product" disable-group="separate_product" class="e_order_btn btn-s1 br fw-6 mt-1 w-100"><lang>PRODUCT_SEPARTION</lang></button><?php } ?>
                        <button table="" function="separate_product_cancel" disable-group="separate_product" class="e_order_btn btn-s3 br fw-6 mt-1 w-100" style="display: none;"><lang>CANCEL</lang></button>
                        <?php if(isset($_SESSION["permission"][3])) {?><button table="" function="catering_product" disable-group="catering_product" class="e_order_btn btn-s1 br fw-6 mt-1 w-100"><lang>CATERING</lang></button><?php } ?>
                        <button table="" function="catering_product_cancel" disable-group="catering_product" class="e_order_btn btn-s3 br fw-6 mt-1 w-100" style="display: none;"><lang>CANCEL</lang></button>
                        <?php if(isset($_SESSION["permission"][4])) {?><button table="" table-take-away-confirmed="" table-take-away="" function="delete_product" table-integrate="" disable-group="delete_product" class="e_order_btn btn-s1 br fw-6 mt-1 w-100"><lang>DELETE_PRODUCT</lang></button><?php } ?>
                        <button table="" table-take-away="" function="delete_product_cancel" disable-group="delete_product" table-integrate="" class="e_order_btn btn-s3 br fw-6 mt-1 w-100" style="display: none;"><lang>CANCEL</lang></button>
                        <?php if(isset($_SESSION["permission"][7])) {?><button table="" table-take-away-confirmed="" table-take-away="" table-integrate="" table-safe="" function="fast_payment" class="e_order_btn btn-s1 br fw-6 mt-1 w-100"><lang>FAST_PAY</lang></button><?php } ?>
                        <?php if(isset($_SESSION["permission"][7])) {?><button table="" table-take-away-confirmed="" table-take-away="" table-integrate="" function="payment" class="e_order_btn btn-s1 br fw-6 mt-1 w-100"><lang>PAYING</lang></button><?php } ?>
                        <button table="" table-take-away-confirmed="" table-take-away="" table-integrate="" table-safe="" function="print_safe" class="e_order_btn btn-s1 br fw-6 mt-1 w-100"><lang>PRINT</lang></button>
                        <button table-integrate="" function="change_status" class="e_order_btn btn-s1 br fw-6 mt-1 w-100"><lang>CHANGE_STATU</lang></button>
                        <!--button table-safe="" function="#" class="e_order_btn btn-s1 br fw-6 mt-1 w-100">M. Yazdır</button-->
                    </div>


                    <div class="e_take_away_info col-12 m-0 p-1 pt-3" style="display: none;border-radius: 10px;background: #6c6cff12;">
                        <div class="confirm" style="position: relative">
                            <div class="row e_takeaway_confirm_area" style=" position: absolute;bottom: 30px;width: 100%;margin: 0 !important;background: rgba(108, 108, 255, 0.07);padding: 10px !important;">
                                <div class="col-12">
                                    <h4 class="text-center mb-1"><lang>ORDER__CONFIRM</lang></h4>
                                </div>
                                <div class="col-6 m-0 p-0"><button confirm="1" class="e_takeaway_confirm btn btn-s1 w-100"><lang>APPROVE</lang></button></div>

                                <div class="col-6 m-0 p-0"><button confirm="-1" class="e_takeaway_confirm btn btn-s3 w-100"><lang>CANCEL</lang></button></div>
                            </div>
                        </div>
                        <h4 class="mb-0 size-type-md text-center"><lang>ADDRESS_INFO</lang></h4>
                        <span function="address" class="size-type-sm"></span>
                    </div>

                    <div class="col-12 m-0 p-1 buttons">
                        <button table="" function="read_barcode"  style="width: 32.5%;" class="e_order_btn btn btn-lg btn-s4 br p-2 size-type-lg"><lang>QR_READING</lang></button>
                        <button function="#" table="" style="width: 32.5%; opacity: .5;" class="e_order_btn btn btn-lg btn-s4 br p-2 size-type-lg">...</button>
                        <button table="" table-safe="" function="change_price_cancel" disable-group="change_price" style="width: 32.5%; display: none;" class="e_order_btn btn btn-lg btn-s3 br p-2 size-type-lg"><lang>CANCEL</lang></button>
                        <?php if(isset($_SESSION["permission"][8])) {?><button table="" table-take-away="" function="discount"  style="width: 32.5%;" class="e_order_btn btn btn-lg btn-s4 br p-2 size-type-lg"><lang>DISCOUNT_COUNT</lang></button><?php } ?>
                    </div>

                    <div class="col-12 m-0 p-0 pt-2 buttons">
                        <button table="" table-take-away="" table-integrate="" function="insert" style="width: 32.5%" class="e_order_btn btn btn-lg btn-s3 br p-2 size-type-lg "><lang>SUBMIT</lang></button>
                        <button table="" function="#" style="width: 32.5%" class="e_order_btn btn btn-lg btn-s3 br p-2 size-type-lg "><lang>DETAILS</lang></button>
                        <?php if(isset($_SESSION["permission"][9])) {?><button table="" table-take-away="" table-safe="" function="change_price" disable-group="change_price" style="width: 32.5%" class="e_order_btn btn btn-lg btn-s3 br p-2 size-type-lg "><lang>CHANGE_PRICE</lang></button><?php } ?>
                    </div>
                </div>
            </div>



        <!-- Middle And Right Section -->
        <div class="col-8 row m-0 p-0">
            <!-- Top Buttons -->
            <div class="col-12 buttons p-0 pl-2 pb-2 m-0" style="display:none;">
                <button function="#" class="e_order_btn btn-s1 br fw-6 mr-1 tw"> - </button>
                <button function="#" class="e_order_btn btn-s1 br fw-6 mr-1 tw"> - </button>
                <button function="#" class="e_order_btn btn-s1 br fw-6 mr-1 tw"> - </button>
                <button function="#" class="e_order_btn btn-s1 br fw-6 mr-1 tw"> - </button>
                <button function="#" class="e_order_btn btn-s1 br fw-6 mr-1 tw"> - </button>
                <button function="#" class="e_order_btn btn-s1 br fw-6 mr-1 tw"> - </button>
                <button function="#" class="e_order_btn btn-s1 br fw-6 mr-1 tw"> - </button>
                <button function="#" class="e_order_btn btn-s1 br fw-6 mr-0 tw"> - </button>
            </div>
            <!-- Top Buttons End -->

            <!-- Middle Section -->
            <div class="col-8 row pl-1 pt-0 pr-0 m-0">
                <input type="text" class="form-input" id="search_product" placeholder="Ürün İsimi">
                <!-- Orders List-->
                <div id="product_list" class="product-list scroll-y text-light row m-0">
                    <!-- (get data) Products -->
                </div>
                <!-- Orders List End-->

                <!-- Order Count Key Bar -->
                <div class="count-bar w-100 pt-1">
                    <div class="row-1">
                        <button class="e_btn_qty_show bg-c1">-</button>
                        <button function="qty" class="e_btn_qty btn-s1">1</button>
                        <button function="qty" class="e_btn_qty btn-s1">2</button>
                        <button function="qty" class="e_btn_qty btn-s1">3</button>
                        <button function="qty" class="e_btn_qty btn-s1">4</button>
                        <button function="qty" class="e_btn_qty btn-s1">5</button>
                    </div>
                    <div class="row-2">
                        <button function="clear" class="e_btn_qty btn-s1">C</button>
                        <button function="qty" class="e_btn_qty btn-s1">6</button>
                        <button function="qty" class="e_btn_qty btn-s1">7</button>
                        <button function="qty" class="e_btn_qty btn-s1">8</button>
                        <button function="qty" class="e_btn_qty btn-s1">9</button>
                        <button function="qty" class="e_btn_qty btn-s1">0</button>
                    </div>
                </div>
                <!-- Order Count Key Bar End -->
            </div>
            <!-- Middle Section End-->

            <!-- (Right) Category Section -->
            <div class="category-list col-4 p-0 pl-1 scroll-y">
                <input type="text" class="form-input" id="search_category" placeholder="Kategori İsimi">
                <div class="e_categories_list row p-0 m-0">
                    <!-- (get data) Category Buttons -->
                </div>
            </div>
            <!-- (Right) Category End -->
        </div>
        <!-- Middle And Right Section End -->
    </div>
</div>

