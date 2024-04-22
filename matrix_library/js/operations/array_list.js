let array_list = (function() {

    array_list.sort_types = {
        ASC: 0x0001,
        DESC: 0x0002
    }

    function array_list(){}

    array_list.index_of = function(array, value, key = ""){
        return array.map(data => {
            return (key === "") ? data : data[key];
        }).indexOf(value);
    };

    array_list.total = function(array, key = ""){
        let total = 0.0;

        array.map(data => {
            total += parseFloat((key === "") ? data : data[key]);
        });

        return total;
    };

    array_list.find = function(array, value, key = ""){
        return array.find(function(data){return ((key === "") ? data : data[key]) == value});
    };

    array_list.find_multi = function(array, value, key = ""){
        let founds = Array();
        array.find(function(data){if(((key === "") ? data : data[key]) === value) founds.push(data);});
        return founds;
    };

    array_list.extend = function(obj){
        let key, args = Array.prototype.slice.call(arguments,1);
        args.forEach(function(value,index,array){
            for (key in value){
                if (value.hasOwnProperty(key)){
                    obj[key] = value[key];
                }
            }
        });
        return obj;
    };

    array_list.sort = function (array, key, sort_type = array_list.sort_types.ASC){
        return array.sort(function (a, b) {
            if (!a.hasOwnProperty(key) || !b.hasOwnProperty(key)) {
                // property doesn't exist on either object
                return 0;
            }

            const varA = (typeof a[key] === 'string')
                ? a[key].toUpperCase() : a[key];
            const varB = (typeof b[key] === 'string')
                ? b[key].toUpperCase() : b[key];

            let comparison = 0;
            if (varA > varB) {
                comparison = 1;
            } else if (varA < varB) {
                comparison = -1;
            }
            return (
                (sort_type === array_list.sort_types.DESC) ? (comparison * -1) : comparison
            );
        });
    }

    array_list.del_index_of = function (array = Array() ,value){
        array.splice(array.indexOf(value), 1);
    }

    array_list.convert_string_to_key = function (string){
        return unescape(encodeURIComponent(variable.clear(string.toString(), variable.clear_types.SEO_URL)));
    }

    return array_list;
})();


