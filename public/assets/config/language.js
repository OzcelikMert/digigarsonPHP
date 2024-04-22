console.time("LLT");
let element = Array.from( $('lang,option,input'));
let element_attr = Array.from( $('input[placeholder]'));
let txt = null;

element.forEach(data => {
    $(data).html(function(i, e) {
        txt = language.data[ e.replace('<lang>',"").replace('</lang>',"")];
        if (txt !== null) return txt;
    });
});

element_attr.forEach(data => {
    $(data).html(function() {
        txt = language.data[ $(data).attr("placeholder")];
        if (txt !== undefined) $(data).attr("placeholder",txt);
    });
});


console.timeEnd("LLT");
