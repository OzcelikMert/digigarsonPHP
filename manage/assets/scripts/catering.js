let catering = (function () {
    let default_ajax_path = `${settings.paths.primary.PHP}catering/`;
    let set_types = {
        QUESTION: 0x0001,
        OWNER: 0x0002
    };
    let get_types = {
        QUESTION: 0x0001,
        OWNER: 0x0002
    };
    let class_list = {
        BUTTON: ".e_catering_btn"
    };

    function catering(){ initialize(); }

    function initialize(){
        function set_events(){
            $(class_list.BUTTON).on("click", function () {
                let function_name = $(this).attr("function");

                switch(function_name){
                    case "add_owner":
                        list_owners.variable_list.SELECTED_ID = 0;
                        modal_new_owner.get();
                        break;
                    case "add_question":
                        list_questions.variable_list.SELECTED_ID = 0;
                        modal_new_question.get();
                        break;
                }
            });
        }

        set_events();
        list_owners.initialize();
        modal_new_owner.initialize();
        list_questions.initialize();
        modal_new_question.initialize();
    }

    let list_owners = {
        class_list: {
            LIST: ".e_owners",
            BUTTON: ".e_owner_btn"
        },
        id_list: {},
        function_types:{
            INSERT: 0x0001,
            DELETE: 0x0002
        },
        variable_list: {
            SELECTED_ID: 0,
            DATA: Array()
        },
        get: function () {
            let self = this;

            function create_element(){
                let element = ``;

                self.variable_list.DATA.forEach(owner => {
                    element += `
                        <tr owner-id="${owner.id}">
                            <td>${owner.id}</td>
                            <td>${owner.name}</td>
                            <td class="text-center">
                                <button function="edit" class="e_owner_btn btn btn-warning"><i class="fa fa-pencil-alt"></i></button>
                            </td>
                            <td class="text-center">
                                <button function="delete" class="e_owner_btn btn btn-danger"><i class="fa fa-trash-alt"></i></button>
                            </td>
                        </tr>
                    `;
                });

                return element;
            }

            if(self.variable_list.DATA.length < 1) {
                get(
                    get_types.OWNER,
                    {},
                    function (data) {
                        data = JSON.parse(data);
                        console.log(data);
                        if (data.status) {
                            self.variable_list.DATA = data.rows;
                        }
                    }
                );
            }

            $(self.class_list.LIST).html(create_element());
        },
        initialize: function (){
            let self = this;

            function set_events(){
                $(document).on("click", self.class_list.BUTTON, function () {
                    let element = $(this);

                    let function_name = element.attr("function");

                    self.variable_list.SELECTED_ID = parseInt(element.closest("[owner-id]").attr("owner-id"));
                    $(modal_new_owner.id_list.FORM).trigger("reset");

                    switch (function_name) {
                        case "edit":
                            modal_new_owner.get();
                            break;
                        case "delete":
                            Swal.fire({
                                icon: "question",
                                title: language.data.DELETE_PROCESS_TITLE,
                                html: `<b>'${array_list.find(self.variable_list.DATA, self.variable_list.SELECTED_ID, "id").name}'</b> ${language.data.DELETE_AUTHORITY_HTML}`,
                                allowEscapeKey: false,
                                allowOutsideClick: false,
                                showCancelButton: true,
                                confirmButtonText: language.data.DELETE,
                                cancelButtonText: language.data.CANCEL,
                                confirmButtonClass: 'btn btn-success btn-lg mr-3 mt-5',
                                cancelButtonClass: 'btn btn-danger btn-lg ml-3 mt-5',
                                buttonsStyling: false
                            }).then((result) => {
                                if (result.value) {
                                    set(
                                        set_types.OWNER,
                                        {
                                            "id": self.variable_list.SELECTED_ID,
                                            "function_type": self.function_types.DELETE
                                        },
                                        function (data) {
                                            data = JSON.parse(data);
                                            if(data.status){
                                                let index = array_list.index_of(self.variable_list.DATA, self.variable_list.SELECTED_ID, "id");
                                                self.variable_list.DATA.splice(index, 1);
                                                self.get();
                                                $(modal_new_owner.id_list.MODAL).modal("hide");
                                                helper_sweet_alert.success(language.data.PROCESS_SUCCESS_TITLE, language.data.PROCESS_SUCCESS);
                                                self.variable_list.SELECTED_ID = 0;
                                            }
                                        }
                                    );
                                }
                            });
                            break;
                    }
                });
            }

            set_events();
            self.get();
        }
    }

    let modal_new_owner = {
        class_list: {
        },
        id_list: {
            MODAL: "#modal_new_owner",
            FORM: "#modal_new_owner_form"
        },
        get: function () {
            let self = this;

            if(list_owners.variable_list.SELECTED_ID > 0){
                $(self.id_list.FORM).autofill(array_list.find(list_owners.variable_list.DATA, list_owners.variable_list.SELECTED_ID, "id"));
            }

            $(self.id_list.MODAL).modal("show");
        },
        initialize: function (){
            let self = this;

            function set_events(){
                $(self.id_list.FORM).submit(function (e) {
                    e.preventDefault();
                    set(
                        set_types.OWNER,
                        Object.assign($(this).serializeObject(), {
                            "id": list_owners.variable_list.SELECTED_ID,
                            "function_type": list_owners.function_types.INSERT
                        }),
                        function (data) {
                            data = JSON.parse(data);
                            if(data.status){
                                list_owners.variable_list.DATA = Array();
                                list_owners.get();
                                helper_sweet_alert.success(language.data.PROCESS_SUCCESS_TITLE, language.data.SET_AUTHORIZED_NAME_HTML);
                            }
                        }
                    );
                });
            }

            set_events();
        }
    }

    let list_questions = {
        class_list: {
            LIST: ".e_questions",
            BUTTON: ".e_question_btn"
        },
        id_list: {},
        function_types:{
            INSERT: 0x0001,
            DELETE: 0x0002
        },
        variable_list: {
            SELECTED_ID: 0,
            DATA: Array()
        },
        get: function () {
            let self = this;

            function create_element(){
                let element = ``;

                self.variable_list.DATA.forEach(question => {
                    element += `
                        <tr question-id="${question.id}">
                            <td>${question.id}</td>
                            <td>${question.comment}</td>
                            <td class="text-center">
                                <button function="edit" class="e_question_btn btn btn-warning"><i class="fa fa-pencil-alt"></i></button>
                            </td>
                            <td class="text-center">
                                <button function="delete" class="e_question_btn btn btn-danger"><i class="fa fa-trash-alt"></i></button>
                            </td>
                        </tr>
                    `;
                });

                return element;
            }

            if(self.variable_list.DATA.length < 1) {
                get(
                    get_types.QUESTION,
                    {},
                    function (data) {
                        data = JSON.parse(data);
                        console.log(data);
                        if (data.status) {
                            self.variable_list.DATA = data.rows;
                        }
                    }
                );
            }

            $(self.class_list.LIST).html(create_element());
        },
        initialize: function (){
            let self = this;

            function set_events(){
                $(document).on("click", self.class_list.BUTTON, function () {
                    let element = $(this);

                    let function_name = element.attr("function");

                    self.variable_list.SELECTED_ID = parseInt(element.closest("[question-id]").attr("question-id"));
                    $(modal_new_question.id_list.FORM).trigger("reset");

                    switch (function_name) {
                        case "edit":
                            modal_new_question.get();
                            break;
                        case "delete":
                            Swal.fire({
                                icon: "question",
                                title: language.data.DELETE_PROCESS_TITLE,
                                html: `<b>'${array_list.find(self.variable_list.DATA, self.variable_list.SELECTED_ID, "id").comment}'</b> ${language.data.QUESTION_DELETE_HTML}`,
                                allowEscapeKey: false,
                                allowOutsideClick: false,
                                showCancelButton: true,
                                confirmButtonText: language.data.DELETE,
                                cancelButtonText: language.data.CANCEL,
                                confirmButtonClass: 'btn btn-success btn-lg mr-3 mt-5',
                                cancelButtonClass: 'btn btn-danger btn-lg ml-3 mt-5',
                                buttonsStyling: false
                            }).then((result) => {
                                if (result.value) {
                                    set(
                                        set_types.QUESTION,
                                        {
                                            "id": self.variable_list.SELECTED_ID,
                                            "function_type": self.function_types.DELETE
                                        },
                                        function (data) {
                                            data = JSON.parse(data);
                                            if(data.status){
                                                let index = array_list.index_of(self.variable_list.DATA, self.variable_list.SELECTED_ID, "id");
                                                self.variable_list.DATA.splice(index, 1);
                                                self.get();
                                                $(modal_new_question.id_list.MODAL).modal("hide");
                                                helper_sweet_alert.success(language.data.PROCESS_SUCCESS_TITLE, language.data.PROCESS_SUCCESS);
                                                self.variable_list.SELECTED_ID = 0;
                                            }
                                        }
                                    );
                                }
                            });
                            break;
                    }
                });
            }

            set_events();
            self.get();
        }
    }

    let modal_new_question = {
        class_list: {
        },
        id_list: {
            MODAL: "#modal_new_question",
            FORM: "#modal_new_question_form"
        },
        get: function () {
            let self = this;

            if(list_questions.variable_list.SELECTED_ID > 0){
                $(self.id_list.FORM).autofill(array_list.find(list_questions.variable_list.DATA, list_questions.variable_list.SELECTED_ID, "id"));
            }

            $(self.id_list.MODAL).modal("show");
        },
        initialize: function (){
            let self = this;

            function set_events(){
                $(self.id_list.FORM).submit(function (e) {
                    e.preventDefault();
                    set(
                        set_types.QUESTION,
                        Object.assign($(this).serializeObject(), {
                            "id": list_questions.variable_list.SELECTED_ID,
                            "function_type": list_questions.function_types.INSERT
                        }),
                        function (data) {
                            data = JSON.parse(data);
                            if(data.status){
                                list_questions.variable_list.DATA = Array();
                                list_questions.get();
                                helper_sweet_alert.success(language.data.PROCESS_SUCCESS_TITLE, language.data.SET_AUTHORIZED_NAME_HTML);
                            }
                        }
                    );
                });
            }

            set_events();
        }
    }

    function set (set_type, data, success_function){
        helper_sweet_alert.wait(language.data.PROCESS_PROGRESS_TITLE, language.data.PROCESS_WAIT_CONTENT);
        data["set_type"] = set_type;
        $.ajax({
            url: `${default_ajax_path}set.php`,
            type: "POST",
            data: data,
            success: function (data) {
                console.log(data);
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
                console.log(data);
                success_function(data);
            }, timeout: settings.ajax_timeouts.NORMAL
        });
    }

    return catering;
})();

$(function () {
    let _catering = new catering();
});
