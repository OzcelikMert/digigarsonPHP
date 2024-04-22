<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <form class="e_language_form language-form">
                <div class="row inputs">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col">
                                <label><b>Key</b></label>
                                <input type="text" class="form-control" placeholder="Key" name="key">
                            </div>
                            <div class="col">
                                <label><b>TR</b></label>
                                <input type="text" class="form-control" placeholder="TR" name="tr">
                            </div>
                            <div class="col">
                                <label><b>EN</b></label>
                                <input type="text" class="form-control" placeholder="EN" name="en">
                            </div>
                            <div class="col">
                                <label><b>AR</b></label>
                                <input type="text" class="form-control" placeholder="AR" name="ar">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col">
                                <label><b>DE</b></label>
                                <input type="text" class="form-control" placeholder="DE" name="de">
                            </div>
                            <div class="col">
                                <label><b>FR</b></label>
                                <input type="text" class="form-control" placeholder="FR" name="fr">
                            </div>
                            <div class="col">
                                <label><b>İT</b></label>
                                <input type="text" class="form-control" placeholder="İT" name="it">
                            </div>
                            <div class="col">
                                <label><b>NL</b></label>
                                <input type="text" class="form-control" placeholder="NL" name="nl">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="row">
                                <div class="col">
                                    <label><b>PT</b></label>
                                    <input type="text" class="form-control" placeholder="PT" name="pt">
                                </div>
                            <div class="col">
                                <label><b>RO</b></label>
                                <input type="text" class="form-control" placeholder="RO" name="ro">
                            </div>
                            <div class="col">
                                <label><b>RU</b></label>
                                <input type="text" class="form-control" placeholder="RU" name="ru">
                            </div>
                            <div class="col">
                                <label><b>SP</b></label>
                                <input type="text" class="form-control" placeholder="SP" name="sp">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-3">
                                <label><b>ZH</b></label>
                                <input type="text" class="form-control" placeholder="ZH" name="zh">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col">
                        <button type="submit" class="btn btn-success w-100">Save</button>
                    </div>
                </div>
            </form>
            <button function="cancel" class="btn cancel btn-danger w-100 mt-3" style="display: none">Cancel</button>
        </div>
    </div>

    <div class="row mt-5 h-25">
        <table class="e_language_table language-table table table-hover table-light table-sticky">
            <thead class="thead-dark">
                <tr>
                    <th></th>
                    <th></th>
                    <th width="25%">Key</th>
                    <th width="25%">TR</th>
                    <th width="25%">EN</th>
                    <th width="25%">AR</th>
                    <th width="25%">DE</th>
                    <th width="25%">FR</th>
                    <th width="25%">İT</th>
                    <th width="25%">NL</th>
                    <th width="25%">PT</th>
                    <th width="25%">RO</th>
                    <th width="25%">RU</th>
                    <th width="25%">SP</th>
                    <th width="25%">ZH</th>

                </tr>
            </thead>
            <tbody class="e_language_table_values"></tbody>
        </table>
    </div>
</div>
<div>
    <button class="e_sync btn btn-light btn-sync">
        <i class="fa fa-sync"></i>
        Sync JS File
    </button>
</div>