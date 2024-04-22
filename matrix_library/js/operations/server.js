let server = (function() {

    function server(){}

    server.is_valid_url = function(url) {
        try {
            new URL(url);
        } catch (_) {
            return false;
        }
        return true;
    }
    
    server.get_url_methods = function (name) {
        let methods={};
        location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi,function(s,k,v){methods[k]=v})
        return name?methods[name]:methods;
    }

    server.get_page_name = function (){
        return window.location.pathname.split("/").pop().replace('.html', '').replace('.php', '');
    }

    return server;
})();