let variable = (function() {
    variable.clear_types = {
        STRING: 0x0001,
        EMAIL: 0x0002,
        INT: 0x0003,
        FLOAT: 0x0004,
        SEO_URL: 0x0005
    };

    const filter_type = {
        EMAIL: 0x0001,
        INT: 0x0002,
        FLOAT: 0x0003
    }

    function variable(){}

    variable.clear = function(variable, type = this.clear_types.STRING, clear_html_tags = true){
        // Check is set
        variable = (typeof variable != "undefined") ? variable : null;
        if(variable !== null){
            // Check clear html tags
            variable = (clear_html_tags) ? strip_tags(variable) : variable;
            // Make default clear
            variable = variable.trim();
            variable = html_special_chars(variable);
            // Check type
            switch (type){
                case this.clear_types.INT:
                    variable = Number.parseInt(filter_var(variable, filter_type.INT));
                    break;
                case this.clear_types.FLOAT:
                    variable = Number.parseFloat(filter_var(variable, filter_type.FLOAT));
                    break;
                case this.clear_types.EMAIL:
                    variable = filter_var(variable, filter_type.EMAIL);
                    break;
                case this.clear_types.SEO_URL:
                    variable = convert_seo_url(variable);
                    break;
            }
        }

        return variable;
    }

    variable.rnd = function (min,max){ //random on steroids
        if (min instanceof Array){ //returns random array item
            if(min.length === 0){
                return undefined;
            }
            if(min.length === 1){
                return min[0];
            }
            return min[this.rnd(0,min.length-1)];
        }
        if(typeof min === "object"){ // returns random object member
            min = Object.keys(min);
            return min[this.rnd(min.length-1)];
        }
        min = min === undefined?100:min;
        if (!max){
            max = min;
            min = 0;
        }
        return	Math.floor(Math.random() * (max-min+1) + min);
    };

    variable.is_empty = function (variable){ return (!variable || 0 === variable.length); }

    variable.isset = function (variable){
        try {
            // Note we're seeing if the returned value of our function is not undefined
            return typeof variable() !== 'undefined'
        } catch (e) {
            // And we're able to catch the Error it would normally throw for referencing a property of undefined
            return false
        }
    }

    variable.is_base64 = function (string) {
        try {
            if (string.split(',')[0].indexOf('base64') >= 0) string = string.split(',')[1];
            window.atob(string);
            return true;
        } catch(e) {
            return false;
        }
    }

    variable.base64_to_blob = function (base64) {
        let byteString;
        if (base64.split(',')[0].indexOf('base64') >= 0)
            byteString = atob(base64.split(',')[1]);
        else
            byteString = unescape(base64.split(',')[1]);
        let mimeString = base64.split(',')[0].split(':')[1].split(';')[0];
        let ia = new Uint8Array(byteString.length);
        for (let i = 0; i < byteString.length; i++) {
            ia[i] = byteString.charCodeAt(i);
        }
        return new Blob([ia], {type:mimeString});
    }

    variable.convert_form_data = function (object) {
        let values = new FormData();

        for ( let key in object ) {
            values.append(key, object[key]);
        }

        return values;
    }

    variable.diff_minutes = function (dt2, dt1)
    {

        let diff =(dt2.getTime() - dt1.getTime()) / 1000;
        diff /= 60;
        return Math.abs(Math.round(diff));

    }

     variable.date_format = function (date, mask, utc) {

         let i18n = {
             dayNames: [
                 "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat",
                 "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
             ],
             monthNames: [
                 "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
                 "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
             ]
         };

        let token = /d{1,4}|m{1,4}|yy(?:yy)?|([HhMsTt])\1?|[LloSZ]|"[^"]*"|'[^']*'/g,
            timezone = /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,
            timezoneClip = /[^-+\dA-Z]/g,
            pad = function (val, len) {
                val = String(val);
                len = len || 2;
                while (val.length < len) val = "0" + val;
                return val;
            };

            // You can't provide utc if you skip other args (use the "UTC:" mask prefix)
            if (arguments.length == 1 && Object.prototype.toString.call(date) == "[object String]" && !/\d/.test(date)) {
                mask = date;
                date = undefined;
            }

            // Passing date through Date applies Date.parse, if necessary
            date = date ? new Date(date) : new Date;
            if (isNaN(date)) throw SyntaxError("invalid date");

            // Allow setting the utc argument via the mask
            if (mask.slice(0, 4) == "UTC:") {
                mask = mask.slice(4);
                utc = true;
            }

            var _ = utc ? "getUTC" : "get",
                d = date[_ + "Date"](),
                D = date[_ + "Day"](),
                m = date[_ + "Month"](),
                y = date[_ + "FullYear"](),
                H = date[_ + "Hours"](),
                M = date[_ + "Minutes"](),
                s = date[_ + "Seconds"](),
                L = date[_ + "Milliseconds"](),
                o = utc ? 0 : date.getTimezoneOffset(),
                flags = {
                    d:    d,
                    dd:   pad(d),
                    ddd:  i18n.dayNames[D],
                    dddd: i18n.dayNames[D + 7],
                    m:    m + 1,
                    mm:   pad(m + 1),
                    mmm:  i18n.monthNames[m],
                    mmmm: i18n.monthNames[m + 12],
                    yy:   String(y).slice(2),
                    yyyy: y,
                    h:    H % 12 || 12,
                    hh:   pad(H % 12 || 12),
                    H:    H,
                    HH:   pad(H),
                    M:    M,
                    MM:   pad(M),
                    s:    s,
                    ss:   pad(s),
                    l:    pad(L, 3),
                    L:    pad(L > 99 ? Math.round(L / 10) : L),
                    t:    H < 12 ? "a"  : "p",
                    tt:   H < 12 ? "am" : "pm",
                    T:    H < 12 ? "A"  : "P",
                    TT:   H < 12 ? "AM" : "PM",
                    Z:    utc ? "UTC" : (String(date).match(timezone) || [""]).pop().replace(timezoneClip, ""),
                    o:    (o > 0 ? "-" : "+") + pad(Math.floor(Math.abs(o) / 60) * 100 + Math.abs(o) % 60, 4),
                    S:    ["th", "st", "nd", "rd"][d % 10 > 3 ? 0 : (d % 100 - d % 10 != 10) * d % 10]
                };

            return mask.replace(token, function ($0) {
                return $0 in flags ? flags[$0] : $0.slice(1, $0.length - 1);
            });
    };
    // Internationalization strings
    
    function html_special_chars(variable) {
        return variable.replace('&', '&amp;').replace('"', '&quot;').replace("'", '&#039;').replace('<', '&lt;').replace('>', '&gt;');
    }

    function strip_tags(variable) {
        variable = variable.toString();
        return variable.replace(/<\/?[^>]+>/gi, '');
    }

    function filter_var(variable, filter_type) {
        let regex;

        // Check Filter Type
        switch(filter_type){
            case filter_type.EMAIL:
                regex = /([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/gi;
                break;
            case filter_type.INT:
                regex = /((?!(0))[0-9]+)/g;
                break;
            case filter_type.FLOAT:
                regex = /[+-]?([0-9]*[.])[0-9]+/g;
        }
        // Check Defined
        let match;
        if ((match = regex.exec(variable)) != null) {
            variable = match[0];
        } else {
            variable = "";
        }

        return variable;
    }

    function convert_seo_url(variable) {
        variable = html_special_chars(strip_tags(variable.toString().toLowerCase().trim()));
        variable = variable.replace("'", '');
        let tr = Array('ş','Ş','ı','I','İ','ğ','Ğ','ü','Ü','ö','Ö','Ç','ç','(',')','/',':',',','!');
        let eng = Array('s','s','i','i','i','g','g','u','u','o','o','c','c','','','_','_','','');
        variable = variable.replaceArray(tr, eng);
        variable = variable.replace(/&amp;amp;amp;amp;amp;amp;amp;amp;amp;.+?;/g, '');
        variable = variable.replace(/\s+/g, '_');
        variable = variable.replace(/|-+|/g, '_');
        variable = variable.replace(/#/g, '');
        variable = variable.replace('.', '');
        return variable;
    }

    return variable;
})();

String.prototype.replaceArray = function(find, replace) {
    var replaceString = this;

    for (var i = 0; i < find.length; i++) {
        replaceString = replaceString.replace(find[i], replace[i]);
    }
    return replaceString;
};
