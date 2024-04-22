<div class="card">
    <div class="card-body">

        <table class="table w-100 table-striped">
            <tr><td colspan="3"><h3 class="m-0"><lang>BRANCH_SETTING</lang></h3></td></tr>
            <tr>
                <td style="width: 80px;"><button class="btn-sm btn-s1 w-100" data-toggle="modal" data-target="#modal_settings_change_name"><lang>EDIT</lang></button></td>
                <td><lang>DISPLAY_NAME</lang>:</td>
                <td class="e_table_name" area="branch_name"><lang>LOADING</lang></td>
            </tr>
            <tr>
                <td style="width: 80px;"><button class="btn-sm btn-s1 w-100" data-toggle="modal" data-target="#modal_settings_edit_long_address"><lang>EDIT</lang></button></td>
                <td><lang>LONG_ADDRESS</lang>: </td>
                <td class="e_table_long_address" area="long_address"><lang>LOADING</lang></td>
            </tr>

            <tr>
                <td style="width: 80px;"><button class="btn-sm btn-s1 w-100" data-toggle="modal" data-target="#modal_settings_edit_working_times"><lang>EDIT</lang></button></td>
                <td><lang>WORK_HOURS</lang></td>
                <td class="p-0 pt-2">
                    <ul area="working_times"></ul>
                </td>
            </tr>

            <tr>
                <td style="width: 80px;"><button class="btn-sm btn-s1 w-100" data-toggle="modal" data-target="#modal_payment_settings"><lang>EDIT</lang></button></td>
                <td><lang>ACCEPTED_PAYMENT</lang></td>
                <td area="payment_types"></td>
            </tr>

            <tr><td colspan="3"><h3 class="m-0"><lang>PACK_SERV_SETTINGS</lang></h3></td></tr>

            <tr>
                <td style="width: 80px;"><button class="btn-sm btn-s1 w-100" data-toggle="modal" data-target="#modal_takeaway_address"><lang>EDIT</lang></button></td>
                <td><lang>ACCEPT_ADDRES</lang> (<lang>SEMT</lang>)</td>
                <td ><ul area="address_list"></ul></td>
            </tr>

            <tr>
                <td style="width: 80px;"><button class="btn-sm btn-s1 w-100" data-toggle="modal" data-target="#modal_settings_takeaway_min_total_and_time"><lang>EDIT</lang></button></td>
                <td><lang>MIN_ORDER_TERM</lang></td>
                <td area="min_time_and_total"><lang>LOADING</lang></td>
            </tr>

            <tr>
                <td style="width: 80px;"><button class="btn-sm btn-s1 w-100" data-toggle="modal" data-target="#modal_notifications"><lang>EDIT</lang></button></td>
                <td colspan="2"><lang>SERVICES</lang></td>
            </tr>

            <!--tr>
                <td style="width: 80px;"><button class="btn-sm btn-s1 w-100" data-toggle="modal" data-target="#modal_surveys"><lang>EDIT</lang></button></td>
                <td colspan="2"><lang>SURVEY_SETTINGS</lang></td>
            </tr-->

            <tr><td colspan="3"><h3 class="m-0"><lang>QR_SETTINGS</lang></h3></td></tr><tr>

                <td colspan="2"><lang>QR_SECURITY</lang> (<lang>ACTIVE</lang> / <lang>NOT_ACTIVE</lang>)</td>
                <td ><input area="qr_security" type="checkbox" class="form-input checkbox-lg"> </td>
            </tr>



        </table>
    </div>
</div>

