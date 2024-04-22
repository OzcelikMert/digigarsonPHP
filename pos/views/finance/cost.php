<div id="cost" class="cost pt-3 row" style="display: none">
    <div class="col-12 mb-2">
        <?php if(isset($_SESSION["permission"][16])) { ?><button function="add" class="e_cost_btn btn btn-s5 float-right"><i class="fa fa-plus"></i> Masraf Ekle</button><?php } ?>
    </div>

    <div class="list col-12 p-0">
        <table class="table table-striped table-sticky bg-theme-2 br text-left">
            <tr>
                <th><lang>DATE</lang></th>
                <th><lang>WAITER_NAME</lang></th>
                <th><lang>DESCRIPTION</lang></th>
                <th><lang>PRICE</lang></th>
                <th><lang>DELETE</lang></th>
            </tr>
            <tbody class="e_costs">
            </tbody>
        </table>
    </div>

</div>