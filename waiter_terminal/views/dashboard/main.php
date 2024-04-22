<div id="main" class="page-main">
    <!-- Table Slides And Scroller -->
    <div class="swiper-container pb-5" id="main-swiper" style="background: #000000c4;">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
              <div class="container" style="margin-top:16px;">
                  <div class="row e_table_sections"></div>
              </div>
            </div>
            <div class="swiper-slide">
              <div class="container">
                  <div class="row e_tables e-tables-style"></div>
              </div>
            </div>
            <div class="swiper-slide">
                <div class="container">
                    <div class="row">
                        <div class="col-12 mt-5">
                            <button function="select_table_sections" class="e_setting_btn btn btn-info btn-lg w-100"><lang>SET_TABLE_SECTION</lang></button>
                        </div>
                        <div class="col-12 mt-3">
                            <button function="select_tables" class="e_setting_btn btn btn-secondary btn-lg w-100" disabled="true"><lang>SET_TABLES</lang></button>
                        </div>
                        <div class="col-12 main-pages-settings-exit">
                            <button function="exit" class="e_setting_btn btn btn-danger btn-lg w-100"><lang>CLOSE_SESSION</lang></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Add Pagination -->
        <div class="swiper-pagination" id="main-swiper-pagination"></div>
        <!-- Add Scrollbar -->
        <div class="swiper-scrollbar" id="main-swiper-scrollbar"></div>
    </div>
</div>