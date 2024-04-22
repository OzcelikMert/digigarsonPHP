let main = (function () {
    let page_name = "";
    main.data_list = {
        USER: {ADDRESSES: [], INFO: {user_id: 0}},
        BRANCH: {INFO:[],TABLE:[],PAYMENT_TYPES: []},
        PAYMENT_TYPES: [],
        PRODUCTS: Array(),
        PRODUCT_LINKED_OPTIONS: Array(),
        OPTION_TYPES: Array(),
        PRODUCT_OPTIONS: Array(),
        PRODUCT_OPTION_ITEMS: Array(),
        CATEGORIES: Array(),
        GET_ORDERS: Array(),
        ADDRESS: {CITY: []},
    };

    function main() { initialize(); }

    function initialize(){}
    return main;
})();

$(function () {
    let _main = new main();
});
