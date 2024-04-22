<div class="modal fade" id="modal_settings_edit_working_times" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-xxl modal-lg" role="document">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h3 class="modal-title" id="modal_new_account_title"><lang>BRANCH_NAME</lang></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <form>
                <!-- Modal Body -->
                <div class="modal-body p-0">
                    <div class="card-body pt-1 row">
                        <div class="col-12">
                            <h3><lang>USAGE</lang>: </h3>
                            <p>Lütfen Açılış Saatlerini 24 saat formatı olarak giriş yapanız, Örnek: 9:00 - 23:59 , 8:30 - 23:00 Gibi</p> <hr>
                        </div>

                        <div class="col-md-12">
                            <table class="w-100">
                                <thead>
                                    <tr>
                                        <th><lang>DAY</lang></th>
                                        <th><lang>OPENING_TIME</lang></th>
                                        <th><lang>CLOSING_TIME</lang></th>
                                        <th><lang>ACTIVE</lang></th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <tr>
                                        <td><lang>MONDAY</lang></td>
                                        <td><input  name="day[0][0]"   type="time" class="form-input" value="00:00" required></td>
                                        <td><input  name="day[0][1]"  type="time" class="form-input" value="00:00" required></td>
                                        <td><select name="day[0][2]" type="time" class="form-input" required><option value="1"><lang>OPEN</lang></option><option value="0"><lang>CLOSED</lang></option></select></td>
                                    </tr>
                                    <tr>
                                        <td><lang>TUESDAY</lang></td>
                                        <td><input  name="day[1][0]"    type="time" class="form-input" value="00:00" required></td>
                                        <td><input  name="day[1][1]"   type="time" class="form-input" value="00:00" required></td>
                                        <td><select name="day[1][2]" type="time" class="form-input"  required><option value="1"><lang>OPEN</lang></option><option value="0"><lang>CLOSED</lang></option></select></td>
                                    </tr>
                                    <tr>
                                        <td><lang>WEDNESDAY</lang></td>
                                        <td><input  name="day[2][0]"  type="time" class="form-input" value="00:00" required></td>
                                        <td><input  name="day[2][1]"  type="time" class="form-input" value="00:00" required></td>
                                        <td><select name="day[2][2]" type="time" class="form-input" required><option value="1"><lang>OPEN</lang></option><option value="0"><lang>CLOSED</lang></option></select></td>
                                    </tr>
                                    <tr>
                                        <td><lang>THURSDAY</lang></td>
                                        <td><input  name="day[3][0]"  type="time" class="form-input" value="00:00" required></td>
                                        <td><input  name="day[3][1]"  type="time" class="form-input" value="00:00" required></td>
                                        <td><select name="day[3][2]" type="time" class="form-input" required><option value="1"><lang>OPEN</lang></option><option value="0"><lang>CLOSED</lang></option></select></td>
                                    </tr>
                                    <tr>
                                        <td><lang>FRIDAY</lang></td>
                                        <td><input  name="day[4][0]"  type="time" class="form-input" value="00:00" required></td>
                                        <td><input  name="day[4][1]"  type="time" class="form-input" value="00:00" required></td>
                                        <td><select name="day[4][2]" type="time" class="form-input"  required><option value="1"><lang>OPEN</lang></option><option value="0"><lang>CLOSED</lang></option></select></td>
                                    </tr>
                                    <tr>
                                        <td><lang>SATURDAY</lang></td>
                                        <td><input  name="day[5][0]"  type="time" class="form-input" value="00:00" required></td>
                                        <td><input  name="day[5][1]"  type="time" class="form-input" value="00:00" required></td>
                                        <td><select name="day[5][2]" type="time" class="form-input" required><option value="1"><lang>OPEN</lang></option><option value="0"><lang>CLOSED</lang></option></select></td>
                                    </tr>
                                    <tr>
                                        <td><lang>SUNDAY</lang></td>
                                        <td><input  name="day[6][0]"  type="time" class="form-input" value="00:00" required></td>
                                        <td><input  name="day[6][1]"  type="time" class="form-input" value="00:00" required></td>
                                        <td><select name="day[6][2]" type="time" class="form-input" required><option value="1"><lang>OPEN</lang></option><option value="0"><lang>CLOSED</lang></option></select></td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="submit" class="btn bg-c1"><lang>SAVE</lang></button>
                </div>

            </form>

        </div>
    </div>
</div>