let pop_pages  = (function() {
    let pages;
    let last_page_z_index = 1000;
    let last_pages = Array();



    function pop_pages(){
        pages = Array();
    }

    pop_pages.prototype.event_types = {
        CLOSE: 0,
        OPEN: 1,
        CLOSE_ALL: 2,
    }

    function set_element_status_effect(element, add_class, remove_class){
        element.addClass(add_class);
        element.removeClass(remove_class);
    }
    function dispatch_event(data = {}){
        document.dispatchEvent(new CustomEvent("_pop_pages", {
            detail: data
        }));
    }

    pop_pages.prototype.add = function (page_name,element_id, showing_animation_class="animate__slideInRight", closing_animation_class="animate__slideOutRight") {
        this.element_id = element_id;
        this.showing_animation_class = showing_animation_class;
        this.closing_animation_class = closing_animation_class;
        $(this.element_id).addClass("animate__animated animate__faster")
        pages[page_name] = {
            element_id: element_id,
            showing_animation_class: showing_animation_class,
            closing_animation_class: closing_animation_class,
        };
    }
    pop_pages.prototype.del = function (page_name) {
      return delete pages.page_name;
    }
    pop_pages.prototype.list = function (){
       console.table(pages);
    }
    pop_pages.prototype.open = function(pop_name) {
        last_page_z_index++;
        try{
            set_element_status_effect($(pages[pop_name].element_id), pages[pop_name].showing_animation_class, pages[pop_name].closing_animation_class);
            $(pages[pop_name].element_id).css("z-index",last_page_z_index);
            $(pages[pop_name].element_id).show(0);
            last_pages.push(pop_name);
            dispatch_event({name:pop_name, type: 1})

        }catch (exception){ return exception;}


    }
    pop_pages.prototype.close = function(pop_name) {
        try{
            set_element_status_effect($(pages[pop_name].element_id), pages[pop_name].closing_animation_class, pages[pop_name].showing_animation_class);
            $(pages[pop_name].element_id).delay(500).hide(0);
            dispatch_event({name:pop_name, type: 0})
        }catch (exception){ return exception;}
    }
    pop_pages.prototype.close_last_page = function() {
        let index = last_pages.length - 1;
        this.close(last_pages[index]);
        last_pages.splice(index,1);
    }
    pop_pages.prototype.close_all = function(not_like="") {
        last_pages = Array();
        for (let name in pages) {
            if (not_like != null && name !== not_like ){
                set_element_status_effect($(pages[name].element_id), pages[name].closing_animation_class,pages[name].showing_animation_class);
                $(pages[name].element_id).delay(500).hide(0);
            }
        }
        dispatch_event({name:"ALL", type: 2})
    }
    pop_pages.prototype.open_all = function() {
        try{
            for (let name in pages) {
                set_element_status_effect($(pages[name].element_id), pages[name].showing_animation_class,pages[name].closing_animation_class);
                $(pages[name].element_id).delay(500).hide(0);
            }
        }catch (exception){ return exception;}
    }
    return pop_pages;
})();


