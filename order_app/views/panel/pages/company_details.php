<div id="page_company_details" class="pop-page" style="display: unset">
    <!--===  TOP BAR  ===--->
    <div class="top-bar">
        <!--<div  class="left-btn icon"><a href="#" close_page="company_details"><i class="fa fa-arrow-left"></i> </a></div>-->
        <div class="left-btn icon"><img class="e_select_language" src="./assets/images/icons/language-icon.svg" alt="language" style="width: 40px;height: 100%;margin: 0 auto;display: block;"> </div>
        <div class="page-title"><span><lang>RESTAURANT</lang></span></div>
        <div class="right-btn-firm icon e_top_nav_basket_btn"><i class="fa fa-shopping-basket" open_page="basket" style="text-align: center; line-height:2; padding: 0px; font-size:20px; color:green !important;"></i></div>
    </div>

    <!--===  TOP BAR END  ===--->
    <div class="pop-page-in">
        <!------ Include the above in your HEAD tag ---------->
        <div class="container-fluid p-0 m-0" style="height: calc(100vh + 10px);">
            <div class="mx-0 pb-5">

                <div class="company info">
                    <div class="profile">
                        <div class="image">
                            <img function="image" class="d-block w-100 rounded" src="./assets/images/card/card.jpg" alt="" style="height: 85px;">
                        </div>
                        <div class="title">
                            <p function="title">...</p>
                            <p function="address">...</p>
                        </div>
                    </div>
                    <div class="details">
                        <div class="item"> <span class="fav-discount"><i class="fa fa-star fa-color-8"></i> <span function="table_type"></span> </div>
                        <div class="item"> <span><i class="fa fa-clock fa-color-9 "></i> <span function="min_time">...</span></span></div>
                        <div class="item"> <span><i class="fa fa-lira-sign fa-color-9"></i> <span function="min_price">...</span></span></div>
                    </div>
                </div>
                <div class="company menu w-100">
                    <button class="btn w-100 filter-button e_get_categories" data-filter="df-company-categories"><lang>CATEGORIES</lang></button>
                    <button class="btn w-100 filter-button e_get_products" data-filter="df-company-products"><lang>PRODUCTS</lang></button>
                    <button class="btn w-100 e_notification_show active"><lang>Servisler</lang></button>
                </div>

                <div area="categories" class="company categories filter df-all df-company-categories">
                    <div class="title">
                        <h6 class="category-title text-left text-secondary" function="bread_crumb"><lang>CATEGORIES</lang></h6>
                    </div>
                    <div class="list" function="categories">
                       ...
                    </div>
                </div>
                <div area="products" class="company products filter df-all df-company-products">
                    <div class="title">
                        <h6 class="category-title text-left text-secondary"><lang>PRODUCTS</lang></h6>
                    </div>
                    <div class="list" function="products">
                        ...
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
