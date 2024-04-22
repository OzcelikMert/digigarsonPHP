<div class="modal fade" id="confirm_delete" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <div class="modal-title" id="">
                    <h2>Kullanıcı Sil</h2>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- Modal Body-->
            <div class="modal-body p-0">
                <div class="container pt-1">
                    <input type="text" name="id" style="z-index: 1; width:0 ; height:0; opacity: 0;" id="user_id">
                    <input type="text" name="type" style="z-index: 1; width:0 ; height:0 ; opacity: 0;" id="type">
                    <p>
                        <span id="description_name" class="text-black"></span>
                        <span class="text-black-50" id="desc">Adlı Kullanıcı Silmek için</span>
                    </p>
                    <div class="form-group">
                        <label for="delete_confirm">Onay Kutusu</label>
                        <input type="text" name="check" class="form-control" id="delete_confirm" placeholder="Silmek için 'onayla' yazınız" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-danger btn-primary confirm_delete" function="check_input">
                            Gönder
                        </button>
                    </div>
                </div>
            </div>
            <!-- Modal Footer-->
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

