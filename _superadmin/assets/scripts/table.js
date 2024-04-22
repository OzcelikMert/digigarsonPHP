let table_edit = (function () {
    let default_ajax_path = `${settings.paths.primary.PHP}table_edit/`;
    let set_types = {
        DELETE : 0x0001,
        INSERT : 0x0002,
        ADD_SECTION: 0x0003
    };
    let get_types = {
        TABLE_SECTION : 0x0001,
        GET_BRANCH_LIST : 0x0002,
        BRANCH : 0x0003,
    };
    let function_type = {
        INSERT : 0x0001,
        UPDATE : 0x0002,
        DELETE : 0x0003,
    }
    let count = 1;
    function notification(){ initialize(); }
    function initialize(){
        get_table.initialize();
        set_table.initialize();
        copy_text.initialize();
    }

    /* Special F */
    function createTableElement(colums,type,id="") {
        let array = colums.split(",");
        let str = "";
        if (id !== ""){
            id = `id="${id}"`;
        }
        array.forEach(function (e) {
            str += `<${type}>${e}</${type}>`;
        })
        return `<tr ${id}> ${str} </tr>`;
    }

    let get_table = {
        variable_list: {
            ID : "",
            TABLEID :"#table_section",
            BRANCH_LIST : ".branch_list",
            SEARCH_ID : "#search_branch_id",
        },
        data_types : {
            ADMIN_DATA : [],
            MESSAGE_DATA : Array(),
        },
        get_section : function() {
            /* SELECT-2*/
            $(this.variable_list.BRANCH_LIST).select2();
            $(this.variable_list.TABLEID).select2();

            get(
                get_types.TABLE_SECTION,
                {},
                function (data) {
                    $("#table_section").html(helper.get_select_options(data.rows, "id", "name"));
                }
            )},
        get_branch_table : function (id) {
            get(
                get_types.GET_BRANCH_LIST,
                {
                    "id": id,
                },
                function (data) {
                    let elements = ``;
                    let th = createTableElement("Kopyala,Bölüm,No,Url,Sil","th");
                    let td = "";
                    let i =0;
                    data.rows.forEach(function (e) {
                        let del = `<button class="btn-sm btn-danger" function="delete_table" ">X</button>`;
                        let copy = `<button   class="btn-sm btn-info" function="copy_url" url="${e.url}">+</button>`;

                        let elements = ` <td>${copy}</td> <td>${e.section_name}</td><td>${e.no}</td> <td>${e.url}</td> <td>${e.del}</td>`;
                        td += createTableElement(`${copy},${e.section_name},${e.no},${e.url},${del}`,"td",`${e.id}`);

                    });
                    data.rows.forEach(function (val) {
                        i++;
                        console.log(val);
                        elements +=`
                            <li>
                                <span>${val.section_name} ${val.no},https://digigarson.net/order_app/panel.php?url=${val.url}</span>
                            </li>`;

                        if(i == 10){
                            elements += `<br>`
                            i = 0;
                        }
                    })
                    $("#section_url").html(elements);
                    $(".out_table").html(`<table class="table"> ${th} ${td} </table>`);
                }
            )
        },
        get_branch:function(){
            let self = this;

            get(get_types.BRANCH, {}, function (data) {
                let elements = '';
                data.rows.forEach(branch => {
                    elements += `
                        <option value="${branch.id}">${branch.name}</option>
                    `;
                });

                $(self.variable_list.BRANCH_LIST).html(elements);
            });
        },
        initialize: function (){
            let  self = this;
            function set_events() {
                $(document).on("click", "button", function () {
                    let function_name = $(this).attr("function");
                    let inputValue = $("#search_branch_id").val();

                    switch (function_name){
                        case "getBranchTable":
                            self.get_branch_table(inputValue);
                            break;
                    }
                });
            }

            set_events();
            this.get_section();
            this.get_branch();
        },
    }
    let set_table = {
        variable_list: {
            ID: "",
        },
        data_types: {
            DATA : Array(),
            DELETE_DATA: Array()
        },
        initialize: function(){
            let self = this;
            function set_events() {
                $(document).on("click", "button", function () {
                    let function_name = $(this).attr("function");
                    let inputBranchValue = $("#search_branch_id").val();
                    switch (function_name){
                        case "getBranchTable":
                            get_table.get_branch_table(inputBranchValue);
                           /* $("#addBranchTable input[name='branch_no']").val(inputBranchValue);*/
                            break;
                        case "copy_url":
                            var url = $(this).attr("url");
                            let txt = `${document.location.origin}/order_app/panel.php?url=${url}`;
                            $("#copy_table").val(txt);
                            var copyText = document.getElementById("copy_table");
                            copyText.select();
                            copyText.setSelectionRange(0, 99999);
                            document.execCommand("copy");
                            break;
                        case "delete_table":
                            let del_id = $(this).closest("tr").attr("id");
                            self.data_types.DELETE_DATA = {"branch_id": inputBranchValue,"del_id" : del_id};
                            set(set_types.DELETE, self.data_types.DELETE_DATA, function (response) {
                                response = JSON.parse(response);
                                get_table.get_branch_table(inputBranchValue);
                            })
                            break;
                        case "new_section":
                            $("#new_section_modal").modal();
                            break;
                    }
                });
                $(document).on("submit", "#addBranchTable", function (e) {

                    e.preventDefault();
                    let data = $("#addBranchTable").serializeObject();
                    set(set_types.INSERT, data, function (response) {
                        response = JSON.parse(response);
                        if(response.status){
                            /*$("#search_branch_id").val(data.branch_no);*/
                            get_table.get_branch_table(data.branch_no);
                        }
                    });
                });

                $(document).on("submit", "#section_form",function () {
                   let data = $(this).serializeObject();
                        set(set_types.ADD_SECTION, data, function (response) {
                            if (response.status == true){
                                get_table.get_section();
                            }
                        })
                    return false;
                });
            }

            set_events();
        }

    }
    let copy_text = {
        id_list: {
            LIST : "#section_url",
        },
        initialize: function () {
            let self = this;
            function set_events(){
                /*$(document).on("click", self.id_list.LIST, function () {
                    console.log($(this));
                });*/
            }

            set_events();
        }
    }
    function set (set_type, data, success_function){
        helper_sweet_alert.wait("İşlem Sürdürülüyor", "İşleminiz yapılıyor lütfen bekleyiniz...");
        data["set_type"] = set_type;
        $.ajax({
            url: `${default_ajax_path}set.php`,
            type: "POST",
            data: data,
            success: function (data) {
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
    let _table_edit = new table_edit();
});