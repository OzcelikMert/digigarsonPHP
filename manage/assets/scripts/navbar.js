
// Navbar
let navbar = (function () {
    let default_ajax_path = `${settings.paths.primary.PHP_SAME_PARTS}navbar/`;
    let set_types = {
        COMPANY: 0x0001
    };
    let id_list = {
        BRANCH_NAME: "#branch_name",
        SIDEBAR: "#sidebar"
    }
    let class_list = {
        SECTION_RIGHT: ".e_top_navbar_section_right",
        BACK_MOVE_TABLE: ".e_btn_back_move_table",
        BUTTON: ".e_navbar_btn"
    }

    function  navbar() {
        initialize();
    }

    let modal_companies = {
        id_list: {
            MODAL: "#modal_companies",
            SEARCH: "#search_company"
        },
        class_list: {
            COMPANIES: ".e_companies",
            BRANCH_BTN: ".e_branch_btn"
        },
        variable_list: {
            DATA: Array(),
            SEARCH: ""
        },
        get: function () {
            let self = this;

            function create_element(){
                let name = array_list.find(main.data_list.BRANCHES, main.data_list.BRANCH_ID_MAIN,"id").name;

                let element = `
                   <div class="col-12 m-0 p-0">
                            <div branch-id="0" class="w-100 p-1">
                                <button function="show" style="background: green;padding: 10px;font-size: 20px" class="e_branch_btn btn btn-primary w-100 h-100">${name} (${language.data.MAIN_ACCOUNT})</button>
                            </div>
                   </div>
                `;

                main.data_list.BRANCHES.forEach(branch => {
                    if(self.variable_list.SEARCH.length > 0) {
                        if(!String(branch.name).match(new RegExp(self.variable_list.SEARCH, "gi"))) {
                            return;
                        }
                    }

                    if(branch.id === main.data_list.BRANCH_ID) return;

                    element += `
                        <div class="col-12 m-0 p-0">
                            <div branch-id="${branch.id}" class="w-100 p-1" >
                                <button function="show" style="padding: 10px;font-size: 20px" class="e_branch_btn btn btn-primary w-100 h-100">${branch.name}</button>
                            </div>
                        </div>

                    `;

                });

                return element;
            }

            $(self.class_list.COMPANIES).html(create_element());
        },
        initialize: function () {
            let self = this;

            function set_events(){
                $(document).on("click", self.class_list.BRANCH_BTN, function () {
                    let element = $(this);
                    let function_name = element.attr("function");

                    switch (function_name) {
                        case "modal":
                            self.get();
                            $(self.id_list.MODAL).modal("show");
                            break;
                        case "show":
                            let id = parseInt(element.closest("div").attr("branch-id"));
                            ////console.log(id)
                            set(
                                set_types.COMPANY,
                                {id: id},
                                function (data) {
                                    data = JSON.parse(data);
                                    ////console.log(data);
                                    if(data.status){
                                        location.reload();
                                    }
                                }
                            );
                            break;
                    }
                    let id = $(this).attr("branch-id");
                });

                $(self.id_list.SEARCH).on("keyup change", function () {
                    self.variable_list.SEARCH = $(this).val();
                    self.get();
                })
            }

            set_events();
        }
    }

    function initialize() {
        let self = this;

        function set_events(){
            $(class_list.BUTTON).on("click", function () {
                let function_name = $(this).attr("function");

                switch (function_name) {
                    case "quit":
                        $.ajax({url: `${default_ajax_path}quit.php`,success: function(data){/*console.log(data);*/ location.reload();}, timeout: settings.ajax_timeouts.NORMAL});
                        break;
                }
            });
        }

        set_events();
        let page_name = server.get_page_name();
        $(`${id_list.SIDEBAR}`).removeClass("active");
        let item = $(`${id_list.SIDEBAR} a[page="${page_name}"]`);
        let item_sub = item.closest(".submenu");
        if(item_sub.length > 0){
            let i = 0;
            while( i < 1){
                //console.log(item_sub)
                if(item_sub.length === 0) {
                    i = 1;
                }else {
                    item_sub.prev().click();
                    item_sub = item_sub.prev().closest(".submenu");
                }
            }
        }
        item.addClass("active");
        modal_companies.initialize();
    }

    function set (set_type, data, success_function){
        helper_sweet_alert.wait(language.data.PROCESS_PROGRESS_TITLE, language.data.PROCESS_WAIT_CONTENT);
        data["set_type"] = set_type;
        $.ajax({
            url: `${default_ajax_path}set.php`,
            type: "POST",
            data: data,
            success: function (data) {
                //console.log(data);
                success_function(data);
            },error: helper_sweet_alert.close(), timeout: settings.ajax_timeouts.NORMAL
        });
    }

    function get(get_type, data, success_function){
        data["get_type"] = get_type;
        $.ajax({
            url: `${default_ajax_path}get.php`,
            type: "POST",
            data: data,
            async: false,
            success: function (data) {
                //console.log(data);
                success_function(data);
            }, timeout: settings.ajax_timeouts.NORMAL
        });
    }

    return navbar;
})();

$(function () {
    let _navbar = new navbar();
})

