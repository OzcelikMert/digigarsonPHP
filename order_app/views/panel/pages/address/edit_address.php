<div id="page_edit_address" class="pop-page" style="display: none">
    <!--===  TOP BAR  ===--->
    <div class="top-bar">
        <div class="left-btn icon"><a href="#" close_page="address_edit"><i class="fa fa-arrow-left"></i> </a></div>
        <div class="page-title"><span><lang>EDIT_ADDRESS</lang></span></div>
        <div class="right-btn icon"></div>
    </div>
    <!--===  TOP BAR END  ===--->

    <!--===  PAGE IN  ===--->
    <div class="pop-page-in">


        <!--        address_type  title  phone  city  town  district  neighborhood-->
        <!--===  ADRESS LIST  ===--->
        <div class="address_list" style="background: white">
            <form id="address_form" autocomplete="off">
                <div class="row pt-2">
                    <input type="hidden" name="id" value="0">
                    <div class="col-5 pr-1">
                        <div class="user-box">
                            <input name="address_type" type="text" required="">
                            <label><lang>ADDRESS_TYPE</lang></label>
                        </div>
                    </div>
                    <div class="col-7 pl-1">
                        <div class="user-box">
                            <input type="text" name="title" required="">
                            <label><lang>ADDRESS_TITLE</lang></label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="user-box">
                            <input type="text" minlength="10" maxlength="11" name="phone" required="">
                            <label><lang>MOBILE_PHONES</lang></label>
                        </div>
                    </div>
                    <div class="col-12 mt-1">
                        <div class="user-box">
                            <select function="1" name="city" required="">
                                <option value=""><lang>SELECT_CITY</lang></option>
                            </select>
                            <label><lang>CITY</lang></label>

                        </div>
                    </div>
                    <div class="col-12 mt-1">
                        <div class="user-box">
                            <select function="2" name="town" required="">
                                <option value=""><lang>SELECT_DISTRICT</lang></option>
                            </select>
                            <label><lang>DISTRICT</lang></label>
                        </div>
                    </div>
                    <div class="col-12 mt-1">
                        <div class="user-box">
                            <select function="3" name="district" required="">
                                <option value=""><lang>SELECT_BLOCK</lang></option>
                            </select>
                            <label><lang>BLOCK</lang></label>

                        </div>
                    </div>
                    <div class="col-12 mt-1">
                        <div class="user-box">
                            <select function="4" name="neighborhood" required="">
                                <option value=""><lang>SELECT_NEIGHBORHOOD</lang></option>
                            </select>
                            <label><lang>NEIGHBORHOOD</lang></label>

                        </div>
                    </div>
                    <div class="col-12">
                        <div class="user-box">
                            <input type="text" name="street" required="">
                            <label><lang>STREET</lang></label>
                        </div>
                    </div>
                    <div class="col-4 pr-1">
                        <div class="user-box">
                            <input type="text" maxlength="10" name="apartment_number" required="">
                            <label><lang>BUILDING_NO</lang></label>
                        </div>
                    </div>
                    <div class="col-4 user-box pr-1 pl-1">
                        <div class="user-box">
                            <input type="number" min="0" max="50" name="floor" required="">
                            <label><lang>FLOOR</lang></label>
                        </div>
                    </div>
                    <div class="col-4 user-box pl-1">
                        <div class="user-box">
                            <input type="number" min="1" max="99" name="home_number" required="">
                            <label><lang>APARTMENT_NUMBER</lang></label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="user-box">
                            <textarea type="text" name="address_description" required=""></textarea>
                            <label><lang>ADDRESS_DIRECTIONS</lang></label>
                        </div>
                    </div>

                </div>


                <button id="add_address_btn" class="btn btn-s1 w-100 mb-2"><i class="fa fa-map"></i> <lang>SAVE</lang></button>


            </form>
        </div>
        <!--===  ADRESS LIST END ===--->
    </div>
    <!--===  PAGE IN  ===--->
</div>