let product = (function () {
    let default_ajax_path = `${settings.paths.primary.PHP}product/`;
    let set_types = {
        COPY : 1,
    };
    let get_types = {
        PRODUCTS : 1,
        BRANCH_ID : 2,
        BRANCH: 3,
    };
    let branch_product_info = [];

    function notification(){ initialize(); }

    function initialize(){
        set_product.initialize();
        get_product.initialize();
    }

    let get_product = {
        variable_list : {
          BRANCH_LIST : ".branch_list",
          BRANCH_ID: "#branch_id",

        },
        get_branch: function (){
            let self = this;
            let branch = Array();
            function create_element(){
                let elements = '';
                branch.forEach(e => {
                    elements += `<option value="${e.id}">${e.name}</option>`;
                });

                return elements
            }
            /* SELECT-2*/
            $(self.variable_list.BRANCH_LIST).select2();
            get(get_types.BRANCH, {}, function (data){
                branch = data.rows;
                $(self.variable_list.BRANCH_LIST).html(create_element());
            });
        },
        initialize: function (){
            let self = this;
            function set_events(){
                $(document).on("click", "button[function='get']", function(){
                   let branch_id = $(self.variable_list.BRANCH_ID).val();
                    console.log(branch_id)
                    get(get_types.BRANCH_ID,{"branch_id": branch_id}, function(data){
                        let element = '';
                        branch_product_info = data.rows;
                        console.log(branch_product_info)
                        data.rows.forEach(function(e){
                            element += `
                                <tr>
                                    <td>${e.id}</td>
                                    <td>${e.product_name}</td>
                                    <td>${e.category_name}</td>
                                </tr>
                            `;
                        });
                        $('#product_table').html(element);
                    });
                });
            }
            set_events();
            self.get_branch();
        }
    }
    let set_product = {
        variable_list: {
            ID : "",
            FORM_PRODUCT : "#form_product",
        },
        class_list: {
            FORM_LOGIN: ".e_login-form"
        },
        data_types : {
            ADMIN_DATA : [],
            MESSAGE_DATA : Array(),
        },
        initialize: function (){
            let self = this;
            function set_events(){
                $(document).on("submit", self.variable_list.FORM_PRODUCT, function(e){
                    e.preventDefault();
                    let serialize = $(this).serializeObject();
                    console.log(serialize)
                    Swal.fire({
                        title: 'Ürün Kopyalama',
                        text: `${serialize.branch_id_owner}' da kayıtlı olan ürünleri ${serialize.branch_id_target}' a kopyalamak istediğinizden emin misiniz?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Evet',
                        cancelButtonText: "Hayır",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false
                    }).then((result) => {
                        if (result.value) {
                            set(
                                set_types.COPY,
                                serialize,
                                function(data){
                                    data = JSON.parse(data);
                                    console.log(data);
                                    if (data.status){
                                        helper_sweet_alert.success("İşlem Başarılı", "Ürünler başarı ile kopyalandı");
                                    }
                            });
                        }
                    });
                });
            }
            set_events();
        },
    }


    function set(set_type, data, success_function){
        helper_sweet_alert.wait("İşlem Sürdürülüyor", "İşleminiz yapılıyor lütfen bekleyiniz...");
        data["set_type"] = set_type;
        $.ajax({
            url: `${default_ajax_path}set.php`,
            type: "POST",
            data: data,
            success: function (data) {
                console.log(data);
                success_function(data);
            },error: helper_sweet_alert.close(),timeout: settings.ajax_timeouts.NORMAL
        });
    }

    function get(get_type, data, success_function = null){
        if (get_type !== null)  data["get_type"] = get_type;
        $.ajax({
            url: `${default_ajax_path}get.php`,
            type: "POST",
            data: data,
            success: function (data) {
                data = JSON.parse(data);
                if (success_function !== null){
                    success_function(data);
                }
            }, timeout: settings.ajax_timeouts.NORMAL
        });
    }
    return notification;
})();

$(function () {
    let _product = new product();
});
