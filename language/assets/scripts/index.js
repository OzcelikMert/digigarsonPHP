let index = (function () {
    let id_list = {};
    let class_list = {
        LANGUAGE_FORM: ".e_language_form",
        LANGUAGE_TABLE: ".e_language_table",
        LANGUAGE_TABLE_VALUES: ".e_language_table_values",
        BUTTON_SYNC: ".e_sync",
        CANCEL_BUTTON: ".cancel",
    };
    let get_types = {
        LANGUAGES: 0x0001
    };
    let set_types = {
        INSERT: 0x0001,
        UPDATE: 0x0002,
        DELETE: 0x0003
    };
    let SELECTED_LANGUAGE_ID = 0;

    function index(){ initialize(); }

    function get(get_type = get_types.LANGUAGES){
        function create_element(languages){
            let element = ``;

            languages.forEach(language => {
                element += `
                    <tr language-id="${language.id}">
                         <td><button class="btn btn-danger" function="delete">Delete</button></td>
                        <td><button class="btn btn-light" function="edit">Edit</button></td>
                        <td function="key"><b>${language.const_name.toString().toUpperCase()}</b></td>
                        <td function="tr">${language.name_tr}</td>
                        <td function="en">${language.name_en}</td>
                        <td function="ar">${language.name_ar}</td>
                        <td function="de">${language.name_de}</td>
                        <td function="fr">${language.name_fr}</td>
                        <td function="it">${language.name_it}</td>
                        <td function="nl">${language.name_nl}</td>
                        <td function="pt">${language.name_pt}</td>
                        <td function="ro">${language.name_ro}</td>
                        <td function="ru">${language.name_ru}</td>
                        <td function="sp">${language.name_sp}</td>
                        <td function="zh">${language.name_zh}</td>

                    </tr>
                `;
            });

            return element;
        }

        $.ajax({
            url: "./functions/index/get.php",
            type: "POST",
            data: {get_type: get_type},
            success: function (data) {
                data = JSON.parse(data);
                console.log(data)
                $(class_list.LANGUAGE_TABLE_VALUES).html(create_element(data.rows));
            }
        });
    }

    function set(set_type, data, success_function){
        data["set_type"] = set_type;
        $.ajax({
            url: "./functions/index/set.php",
            type: "POST",
            data: data,
            success: function (data) {
                success_function(data);
                SELECTED_LANGUAGE_ID = 0;
            }
        });
    }

    function initialize(){
        function set_events(){
            $(class_list.LANGUAGE_FORM).submit(function () {
                let set_type = set_types.INSERT;
                let data = $(class_list.LANGUAGE_FORM).serializeObject();
                if(SELECTED_LANGUAGE_ID > 0){
                    set_type = set_types.UPDATE;
                    data = Object.assign(data, {"id": SELECTED_LANGUAGE_ID});
                }

                set(
                    set_type,
                    data,
                    function (data) {
                        data = JSON.parse(data);
                        if(data.error_code === settings.error_codes.SUCCESS){
                            helper_sweet_alert.success("Success", "Save successfully");
                            $(class_list.LANGUAGE_FORM).trigger("reset");
                            get();
                        }
                    }
                );
                return false;
            });

            $(document).on("click", `button`, function () {
                let element = $(this);
                let function_name = element.attr("function");
                let id = element.closest("[language-id]").attr("language-id");
                let key = element.closest("[language-id]").children("[function='key']").children("b").html();
                let data = {
                    "id": id
                };
                switch (function_name) {
                    case "delete":
                        Swal.fire({
                            title: 'Language Item Delete',
                            html: `Are you sure for delete language item? <br>(key: <b>'${key}'</b>)`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes',
                            cancelButtonText: "No",
                        }).then((result) => {
                            if (result.value) {
                                set(
                                    set_types.DELETE,
                                    data,
                                    function (data) {
                                        data = JSON.parse(data);
                                        console.log(data);
                                        if(data.error_code === settings.error_codes.SUCCESS){
                                            helper_sweet_alert.success("Success", "Delete successfully");
                                            get();
                                        }
                                    }
                                );
                            }
                        });
                        break;
                    case "edit":
                        $(class_list.CANCEL_BUTTON).show();
                        $(class_list.LANGUAGE_FORM).autofill({
                            "key": key.toString().toLowerCase(),
                            "tr": element.closest("[language-id]").children("[function='tr']").html(),
                            "en": element.closest("[language-id]").children("[function='en']").html(),
                            "ar": element.closest("[language-id]").children("[function='ar']").html(),
                            "de": element.closest("[language-id]").children("[function='de']").html(),
                            "fr": element.closest("[language-id]").children("[function='fr']").html(),
                            "it": element.closest("[language-id]").children("[function='it']").html(),
                            "nl": element.closest("[language-id]").children("[function='nl']").html(),
                            "pt": element.closest("[language-id]").children("[function='pt']").html(),
                            "ro": element.closest("[language-id]").children("[function='ro']").html(),
                            "ru": element.closest("[language-id]").children("[function='ru']").html(),
                            "sp": element.closest("[language-id]").children("[function='sp']").html(),
                            "zh": element.closest("[language-id]").children("[function='zh']").html()
                        });
                        SELECTED_LANGUAGE_ID = id;
                        break;
                    case "cancel":
                        $(class_list.CANCEL_BUTTON).hide();
                        $(class_list.LANGUAGE_FORM)[0].reset();
                        SELECTED_LANGUAGE_ID = 0;
                        break;
                }
            });

            $(document).on("click", `${class_list.LANGUAGE_TABLE_VALUES} td[function='key'] b`, function () {
                navigator.clipboard.writeText("language.data." + $(this).html());
            });

            $(class_list.BUTTON_SYNC).on("click", function () {
                $.ajax({
                    url: "../public/config/languages/creator.php",
                    success: function (data) {
                        helper_sweet_alert.success("Success", "Sync is successfully");
                    }
                });
            });
        }

        get();
        set_events();
    }

    return index;
})();

$(function () {
    let _index = new index();
})