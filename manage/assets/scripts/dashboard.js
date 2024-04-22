let page_dashboard = (function() {
    let default_ajax_path = `${settings.paths.primary.PHP}dashboard/`;
    let set_types = {};
    let get_types = {
        TOP_CHARTS: 0x0001,
        MIDDLE_CHART: 0x0002,
        LAST_PAYMENTS: 0x0003,
        LAST_ORDERS: 0x0004
    };
    function page_dashboard(){ initialize(); }

    function initialize(){
        charts.initialize();
        tables.initialize();
    }

    let charts = {
        id_list: {
            CUSTOMER: "#sparkline-1",
            WAITER: "#sparkline-2",
            PRICE: "#sparkline-3",
            COST: "#sparkline-4"
        },
        class_list: {
            MIDDLE_CHART_INFO: ".e_middle_chart_info"
        },
        data_types: {
            CUSTOMER: "customer",
            WAITER: "waiter",
            PRICE: "price",
            COST: "cost",
            WEEK: "week",
            PREVIOUS_WEEK: "prev_week"
        },
        get_top_charts: function () {
            let self = this;

            let chart_data = Array();
            chart_data[self.data_types.CUSTOMER] = [0];
            chart_data[self.data_types.WAITER] = [0];
            chart_data[self.data_types.PRICE] = [0];
            chart_data[self.data_types.COST] = [0];

            function initialize_charts(){
                let percent = 0;
                let value = 0;
                let chart_element = null;

                /* CUSTOMER */
                chart_element = $(self.id_list.CUSTOMER);
                chart_element.sparkline(chart_data[self.data_types.CUSTOMER], {
                    type: 'line',
                    width: '99.5%',
                    height: '100',
                    lineColor: 'white',
                    fillColor: 'black',
                    lineWidth: 2,
                    spotColor: "red",
                    minSpotColor: "red",
                    maxSpotColor: "blue",
                    highlightSpotColor: "black",
                    highlightLineColor: "red",
                    resize:true
                });
                value = chart_data[self.data_types.CUSTOMER][chart_data[self.data_types.CUSTOMER].length - 1];
                percent = (chart_data[self.data_types.CUSTOMER].length > 1)
                    ? ((value - chart_data[self.data_types.CUSTOMER][chart_data[self.data_types.CUSTOMER].length - 2]) / chart_data[self.data_types.CUSTOMER][chart_data[self.data_types.CUSTOMER].length - 2]) * 100
                    : 100;
                chart_element
                    .prev()
                        .children("div[function='value']")
                        .children("[function='value']")
                            .html(value);
                chart_element
                    .prev()
                    .children("div[function='percent']")
                        .html(`
                            <i class="fa fa-fw fa-caret-${(percent < 0) ? `down text-danger` : `up text-success`}"></i><span function="percent">${percent.toFixed(2)}</span><span>%</span>
                        `);

                /* WAITER */
                chart_element = $(self.id_list.WAITER);
                chart_element.sparkline(chart_data[self.data_types.WAITER], {
                    type: 'line',
                    width: '99.5%',
                    height: '100',
                    lineColor: 'white',
                    fillColor: 'darkblue',
                    lineWidth: 2,
                    spotColor: "red",
                    minSpotColor: "red",
                    maxSpotColor: "blue",
                    highlightSpotColor: "black",
                    highlightLineColor: "red",
                    resize:true
                });
                value = chart_data[self.data_types.WAITER][chart_data[self.data_types.WAITER].length - 1];
                percent = (chart_data[self.data_types.WAITER].length > 1)
                    ? ((value - chart_data[self.data_types.WAITER][chart_data[self.data_types.WAITER].length - 2]) / chart_data[self.data_types.WAITER][chart_data[self.data_types.WAITER].length - 2]) * 100
                    : 100;
                chart_element
                    .prev()
                    .children("div[function='value']")
                    .children("[function='value']")
                    .html(value);
                chart_element
                    .prev()
                    .children("div[function='percent']")
                        .html(`
                            <i class="fa fa-fw fa-caret-${(percent < 0) ? `down text-danger` : `up text-success`}"></i><span function="percent">${percent.toFixed(2)}</span><span>%</span>
                        `);

                /* PRICE */
                chart_element = $(self.id_list.PRICE);
                chart_element.sparkline(chart_data[self.data_types.PRICE], {
                    type: 'line',
                    width: '99.5%',
                    height: '100',
                    lineColor: 'white',
                    fillColor: 'darkgreen',
                    lineWidth: 2,
                    spotColor: "red",
                    minSpotColor: "red",
                    maxSpotColor: "blue",
                    highlightSpotColor: "black",
                    highlightLineColor: "red",
                    resize:true
                });
                value = chart_data[self.data_types.PRICE][chart_data[self.data_types.PRICE].length - 1];
                percent = (chart_data[self.data_types.PRICE].length > 1)
                    ? ((value - chart_data[self.data_types.PRICE][chart_data[self.data_types.PRICE].length - 2]) / chart_data[self.data_types.PRICE][chart_data[self.data_types.PRICE].length - 2]) * 100
                    : 100;
                chart_element
                    .prev()
                    .children("div[function='value']")
                    .children("[function='value']")
                    .html(`${value + main.data_list.CURRENCY}`);
                chart_element
                    .prev()
                    .children("div[function='percent']")
                        .html(`
                            <i class="fa fa-fw fa-caret-${(percent < 0) ? `down text-danger` : `up text-success`}"></i><span function="percent">${percent.toFixed(2)}</span><span>%</span>
                        `);

                /* COST */
                chart_element = $(self.id_list.COST);
                chart_element.sparkline(chart_data[self.data_types.COST], {
                    type: 'line',
                    width: '99.5%',
                    height: '100',
                    lineColor: 'white',
                    fillColor: 'darkred',
                    lineWidth: 2,
                    spotColor: "red",
                    minSpotColor: "red",
                    maxSpotColor: "blue",
                    highlightSpotColor: "black",
                    highlightLineColor: "red",
                    resize:true,
                });
                value = chart_data[self.data_types.COST][chart_data[self.data_types.COST].length - 1];
                percent = (chart_data[self.data_types.COST].length > 1)
                    ? ((value - chart_data[self.data_types.COST][chart_data[self.data_types.COST].length - 2]) / chart_data[self.data_types.COST][chart_data[self.data_types.COST].length - 2]) * 100
                    : 100;
                chart_element
                    .prev()
                    .children("div[function='value']")
                    .children("[function='value']")
                    .html(`${value + main.data_list.CURRENCY}`);
                chart_element
                    .prev()
                    .children("div[function='percent']")
                        .html(`
                            <i class="fa fa-fw fa-caret-${(percent < 0) ? `down text-danger` : `up text-success`}"></i><span function="percent">${percent.toFixed(2)}</span><span>%</span>
                        `);
            }

            get(
                get_types.TOP_CHARTS,
                {},
                function (data) {
                    data = JSON.parse(data);
                    console.log(data);
                    if(data.status){
                        if(data.custom_data[self.data_types.CUSTOMER].length > 0){
                            chart_data[self.data_types.CUSTOMER] = [];
                            data.custom_data[self.data_types.CUSTOMER].forEach(data => {
                                chart_data[self.data_types.CUSTOMER].push(data.count);
                            });
                        }

                        if(data.custom_data[self.data_types.WAITER].length > 0){
                            chart_data[self.data_types.WAITER] = [];
                            data.custom_data[self.data_types.WAITER].forEach(data => {
                                chart_data[self.data_types.WAITER].push(data.count);
                            });
                        }

                        if(data.custom_data[self.data_types.PRICE].length > 0){
                            chart_data[self.data_types.PRICE] = [];
                            data.custom_data[self.data_types.PRICE].forEach(data => {
                                chart_data[self.data_types.PRICE].push(data.total.toFixed(2));
                            });
                        }

                        if(data.custom_data[self.data_types.COST].length > 0){
                            chart_data[self.data_types.COST] = [];
                            data.custom_data[self.data_types.COST].forEach(data => {
                                chart_data[self.data_types.COST].push(data.total.toFixed(2));
                            });
                        }
                    }
                }
            );

            initialize_charts();
        },
        get_middle_chart: function () {
            let self = this;

            let chart_data = Array();
            chart_data[self.data_types.WEEK] = {"values": [0], "safes": [`Kasa: ${0}`]};
            chart_data[self.data_types.PREVIOUS_WEEK] = {"values": [0], "safes": [`Kasa: ${0}`]};

            function initialize_chart(){
                console.log(chart_data);
                let ctx = document.getElementById('revenue').getContext('2d');
                let myChart = new Chart(ctx, {
                    type: 'line',

                    data: {
                        labels: ['', '', '', '', '', '', '', ''],
                        datasets: [{
                            label: 'Bu Hafta',
                            data: chart_data[self.data_types.WEEK].values,
                            backgroundColor: "rgba(89, 105, 255,0.5)",
                            borderColor: "rgba(89, 105, 255,0.7)",
                            borderWidth: 2

                        }, {
                            label: 'Önceki Hafta',
                            data: chart_data[self.data_types.PREVIOUS_WEEK].values,
                            backgroundColor: "rgba(255, 64, 123,0.5)",
                            borderColor: "rgba(255, 64, 123,0.7)",
                            borderWidth: 2
                        }]
                    },
                    options: {

                        legend: {
                            display: true,
                            position: 'bottom',

                            labels: {
                                fontColor: '#71748d',
                                fontFamily: 'Circular Std Book',
                                fontSize: 14,
                            }
                        },


                        scales: {
                            xAxes: [{
                                ticks: {
                                    fontSize: 14,
                                    fontFamily: 'Circular Std Book',
                                    fontColor: '#71748d',
                                }
                            }],
                            yAxes: [{
                                ticks: {
                                    fontSize: 14,
                                    fontFamily: 'Circular Std Book',
                                    fontColor: '#71748d',
                                }
                            }]
                        }

                    }
                });

                $(`${self.class_list.MIDDLE_CHART_INFO} [function='day_total']`).html(chart_data[self.data_types.WEEK].values[chart_data[self.data_types.WEEK].values.length - 1] + main.data_list.CURRENCY);

                let total = 0.0;
                chart_data[self.data_types.WEEK].values.forEach(value => {
                    total += parseFloat(value);
                });
                $(`${self.class_list.MIDDLE_CHART_INFO} [function='week_total']`).html(total.toFixed(2) + main.data_list.CURRENCY);

                total = 0.0;
                chart_data[self.data_types.PREVIOUS_WEEK].values.forEach(value => {
                    total += parseFloat(value);
                });
                $(`${self.class_list.MIDDLE_CHART_INFO} [function='prev_total']`).html(total.toFixed(2) + main.data_list.CURRENCY);

            }

            get(
                get_types.MIDDLE_CHART,
                {},
                function (data) {
                    data = JSON.parse(data);
                    console.log(data);
                    if(data.status){
                        if(data.custom_data[self.data_types.WEEK].length > 0){
                            chart_data[self.data_types.WEEK].values = [];
                            chart_data[self.data_types.WEEK].safes = [];
                            data.custom_data[self.data_types.WEEK].forEach(data => {
                                chart_data[self.data_types.WEEK].values.push(data.total.toFixed(2));
                                chart_data[self.data_types.WEEK].safes.push(`Kasa: ${data.safe_id}`);
                            });
                        }

                        if(data.custom_data[self.data_types.PREVIOUS_WEEK].length > 0){
                            chart_data[self.data_types.PREVIOUS_WEEK].values = [];
                            chart_data[self.data_types.PREVIOUS_WEEK].safes = [];
                            data.custom_data[self.data_types.PREVIOUS_WEEK].forEach(data => {
                                chart_data[self.data_types.PREVIOUS_WEEK].values.push(data.total.toFixed(2));
                                chart_data[self.data_types.PREVIOUS_WEEK].safes.push(`Kasa: ${data.safe_id}`);
                            });
                        }
                    }
                }
            );

            initialize_chart();
        },
        initialize: function () {
            let self = this;

            function set_events() {

            }

            set_events();
            self.get_top_charts();
            self.get_middle_chart();
        }
    }

    let tables = {
        id_list: {

        },
        class_list: {
            LAST_PAYMENTS: ".e_last_payments",
            LAST_ORDERS: ".e_last_orders"
        },
        get_last_payments: function () {
            let self = this;

            let data_payments = Array();

            function create_element(){
                let elements = ``;

                data_payments.forEach(payment => {
                    elements += `
                        <tr>
                          <td>${payment.no}</td>
                          <td>${payment.price.toFixed(2) + main.data_list.CURRENCY}</td>
                          <td>${payment.type}</td>
                          <td>${payment.status}</td>
                          <td>(${payment.account_type}) ${payment.account_name}</td>
                          <td>${payment.date}</td>
                        </tr>
                    `;
                });

                return elements;
            }

            get(
                get_types.LAST_PAYMENTS,
                {},
                function (data) {
                    data = JSON.parse(data);
                    console.log(data);
                    if(data.status){
                        data_payments = data.custom_data;
                    }
                }
            );

            $(self.class_list.LAST_PAYMENTS).html(create_element());
        },
        get_last_orders: function () {
            let self = this;

            let data_payments = Array();

            function create_element(){
                let elements = ``;

                data_payments.forEach(order => {
                    elements += `
                        <tr>
                            <td class="border-0">${order.no}</td>
                            <td class="border-0">${order.name}</td>
                            <td class="border-0">${order.quantity_name} ${(order.quantity_id > 1) ? `(${order.quantity})` : ``}</td>
                            <td class="border-0">${order.qty}</td>
                            <td class="border-0">${order.price.toFixed(2) + main.data_list.CURRENCY}</td>
                            <td class="border-0">${order.time}</td>
                            <td class="border-0">${order.type}</td>
                            <td class="border-0">${order.status}</td>
                            <td class="border-0">(${order.account_type}) ${order.account_name}</td>
                            <td class="border-0">${order.comment}</td>
                        </tr>
                    `;
                });

                return elements;
            }

            get(
                get_types.LAST_ORDERS,
                {},
                function (data) {
                    data = JSON.parse(data);
                    console.log(data);
                    if(data.status){
                        data_payments = data.custom_data;
                    }
                }
            );

            $(self.class_list.LAST_ORDERS).html(create_element());
        },
        initialize: function () {
            let self = this;

            function set_events() {

            }

            set_events();
            self.get_last_payments();
            self.get_last_orders();
        }
    }

    function set(set_type, data, success_function){
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

    return page_dashboard;
})();

$(function () {
    let _dashboard = new page_dashboard();
});