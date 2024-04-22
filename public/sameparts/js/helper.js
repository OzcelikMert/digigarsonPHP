let helper = (function () {
    function helper() {}
    helper.logger = false;

    helper.element_types = {
        INPUT: "input",
        SELECT: "select"
    }

    helper.get_form_element_with_name = function(form_id, element_name, element_type = helper.element_types.INPUT) { return $(`${form_id} ${element_type}[name="${element_name}"]`); },
    helper.get_select_options = function (data, key_value_name, key_text_name, selected_value = null) {
        let elements = ``;

        data.forEach(select_data =>{
            let selected = (select_data[key_value_name] == selected_value) ? "selected" : "";
            elements += `<option value="${select_data[key_value_name]}" ${selected}>${select_data[key_text_name]}</option>`;
        });

        return elements;
    }

    helper.db = {
        language_columns: {
            TURKISH: "tr",
            ENGLISH: "en",
            DUTCH: "nl",
            GERMAN: "de",
            ARABIC: "ar",
            RUSSIAN: "ru",
            CHINESE: "zh",
            SPANISH: "sp",
            ITALIAN: "it",
            PORTUGUESE: "pt",
            ROMANIAN: "ro",
            FRENCH: "fr",
        },
        order_types: {
            TABLE: 1,
            TAKEAWAY: 2,
            COME_TAKE: 3,
            SAFE: 4,
            RESERVED: 5,
            YEMEK_SEPETI: 6
        },
        order_status_types: {
            PENDING: 1,
            COOKING: 2,
            ON_THE_WAY: 3,
            GETTING_READY: 4,
            ORDER_COMBINING: 5,
            DELIVERED: 6
        },
        order_product_status_types: {
            ACTIVE: 1,
            CANCEL: 2,
            CATERING: 3
        },
        order_product_types: {
            PRODUCT: 1,
            DISCOUNT: 2
        },
        order_payment_status_types: {
            PAID: 1,
            CANCEL: 2,
            CATERING: 3,
            COST: 4
        },
        product_option_group_types: {
            MATERIALS: 1,
            SINGLE_SELECT: 2,
            MULTI_SELECT: 3,
            PRODUCT_SELECT: 4,
            QUANTITY: 5
        },
        branch_table_shape_types: {
            DEFAULT: 1,
            ROUND: 2,
            LONG: 3,
            LARGE: 4
        },
        branch_table_types: {
            TABLE: 1,
            SAFE: 2,
            TAKEAWAY: 3,
            COME_TAKE: 4,
            DIGITAL_MENU: 5,
            YEMEK_SEPETI: 6,
            OTHER_SALE: 7,
            PERSON_SALE: 8
        },
        branch_tables_static: {
            SAFE: 1,
            TABLE: 1,
            TAKE_AWAY: 2,
            COME_TAKE: 3,
            DIGITAL_MENU: 4,
            OTHER_SALE: 7,
            PERSON_SALE: 8
        },
        account_types: {
            CUSTOMER: 1,
            WAITER: 2,
            MANAGE: 3,
            YEMEK_SEPETI: 4
        },
        branch_tables: {
            SAFE: 1,
            TAKE_AWAY: 2,
            COME_TAKE: 3,
            YEMEK_SEPETI: 4
        },
        payment_types: {
            CASH: 1,
            CANCEL: 9
        },
        quantity_types: {
            PIECE:  1,
            KILOGRAM:  2,
            LITER:  3,
            METER:  4,
            PORTION:  5
        },
        integrate_types: {
            YEMEK_SEPETI: 1,
            GETIR: 2
        }
    }


    helper.log = function (data,description=null) {
        if (!helper.logger) return;
        /*
            let e = new Error();
            let frame = e.stack.split("\n")[2];
            let lineNumber = frame.split(":").reverse()[1];
            let functionName = frame.split(" ")[5];
            [${functionName.replace(location.origin,"") + ":" + lineNumber}]
        */
        if (typeof data == "string" || typeof data == "number" || typeof data == "boolean") {
            console.log(`%c${data}`,"color:orange;font-size:14px");
        }else {
            if (description !== null) console.log( `%c${description.toUpperCase()}`, "color:orange;font-size:14px" )
            console.log(data);
            console.log(".")
        }
    }

    helper.create_table_columns = function (tr = {},columns = []){
        let class_list = "";
        let attr_list = "";
        let id = "";
        let td = "";

        try {
            if (typeof columns !== 'undefined'){
                if (typeof columns === 'string'){
                    td = columns;
                }else if (typeof columns === 'object'){

                    columns.forEach(function (item){
                        if (typeof item == "string") td += "<td>"+item + "</td> ";
                        if (typeof item == "object") {
                            let data = {
                                html: (item.html !== undefined) ? item.html : "",
                                id: (item.id !== undefined) ? `id='${item.id}' ` : "",
                                class: (item.class !== undefined) ? `class='${item.class}' ` : "",
                                style: (item.style !== undefined) ? `style='${item.style}' ` : "",
                                attr: (item.attr !== undefined) ? `style='${item.attr}' ` : "",
                            }
                            td += `<td ${data.id}${data.class}${data.style}${data.attr}>${data.html}</td>`;
                        }
                    })
                    td.slice(0,-1)
                }
            }
            // ===-| TR |-===
            if (typeof tr !== 'undefined'){
                if (typeof tr !== 'undefined'){
                    // -> class
                    if (typeof tr.class === 'string'){
                        class_list = `class='${tr.class}'`;
                    }else if (typeof tr.class === 'object'){
                        tr.class.forEach(function (item){ class_list += item +" "; })
                        class_list = `class='${class_list.slice(0,-1)}'`;
                    }
                    // -> id
                    if (typeof tr.id == 'string'){ id = `id='${tr.id}'` }

                    // -> attributes
                    if (typeof tr.attr === 'string'){
                        attr_list = tr.attr;
                    }else if (typeof tr.attr === 'object'){
                        for (const key in tr.attr) {
                            attr_list += `${key}="${tr.attr[key]}" `
                        }
                        attr_list.slice(0,-1)
                    }
                }
                tr = `<tr ${id} ${class_list} ${attr_list}>${td}</tr>`;
            }
            return tr;
        }catch (e) {return e}
    }

    helper.get_pagination_elements = function (total, per_count, current){
        let elements = ``;
        let total_page = Math.ceil((total / per_count));
        current = (Number.isNaN(current) || current <= 0) ? 1 : current;
        current = (Number.isNaN(current) || current > total_page) ? total_page : current;
        /* Button Count */

        let length = 4;
        length = (length > total_page) ? total_page - 1 : length;
        let start = current - Math.floor(length / 2);
        start = Math.max(start, 1);
        start = Math.min(start, total_page - length);

        for (let i = 0; i <= length; ++i){
            let index = i + start;
            elements += `
                <li class="page-item ${(current === index) ? "active" : ""}">
                    <a class="page-link ${(current !== index) ? "text-dark" : ""}" index=${index}">${index}</a>
                </li>
            `;
        }

        elements = `
            <ul style="list-style: none; display: inline-flex;">
                <li class="page-item ${(current <= 1) ? "disabled" : ""}">
                    <a class="page-link text-dark" index=${current - 1}" tabindex="-1">Önceki</a>
                </li>
                ${elements}
                <li class="page-item ${(current >= total_page) ? "disabled" : ""}">
                    <a class="page-link text-dark" index=${current + 1}">Sonraki</a>
                </li>
            </ul>
        `;
        return elements;
    }


    helper.array_remove_duplicates = function (array){
        let arr = array.concat()
        for(let i=0; i<arr.length; ++i) for(let j=i+1; j<arr.length; ++j) if(arr[i] === arr[j]) arr.splice(j, 1);
        return arr;
    }


    helper.get_table_and_section_with_id = function (table_id){
        try{
          let table =  array_list.find(main.data_list.TABLES,parseInt(table_id),"id");
          let section = array_list.find(main.data_list.SECTIONS,table.section_id,"id");
          let section_type = array_list.find(main.data_list.SECTION_TYPES,section.section_id,"id").name;
          return section_type+" "+table.no;
        }catch (e){
            return "Bilinmeyen Masa";
        }
    }


    return helper;
})();