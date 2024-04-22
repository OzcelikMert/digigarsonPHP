/* Events */
$(document).ready(function(){
    $(document).on("contextmenu",function(){ return false; });

    $(document.body).on('focus','input[type="number"]', function(e) {
        InputID = $(this);
        showNumberKeyboard();
        hideTextKeyboard();
    });

    $(document.body).on('focus','input[type="text"],input[type=password], textarea', function(e) {
        let keyboard = $(this).attr("keyboard=false");
        if (typeof keyboard !=="undefined" && keyboard === false) return false;
        InputID = $(this);
        showTextKeyboard();
        hideNumberKeyboard();
    });

    $(document.body).on('focus','textarea', function(e) {
        InputID = $(this);
        showTextKeyboard();
        hideNumberKeyboard();
    });

    $(window).click(function(e){
        let isKeyboard = $(e.target).hasClass("keyboard");
        let isInput = $(e.target).prop("type");
        let Data = $(e.target).attr("data");
        if (!(isKeyboard || isInput || Data == "keyboard")) {
            hideNumberKeyboard();
            hideTextKeyboard();
        }
    });
});
/* end Events */

/* Variables */
var InputID = '';
var isUpper = 0;
const IsClicked = {
    Dot: false,
    Minus: false,
    Capslock: 0
};
/* end Variables */

/* Functions */
function showTextKeyboard(){
    $(".keyboard-text").addClass("keyboard-active");
}

function hideTextKeyboard(){
    $(".keyboard-text").removeClass("keyboard-active");
}

function typingText(text){
    if (text === 'Aa'){
        IsClicked.Capslock = (IsClicked.Capslock) ? 0 : 1;
        KeyboardIsUp(IsClicked.Capslock);
        return true;
    }

    if (isUpper === 1) {
        text = text.toUpperCase();
    }
    //let Input = $("#"+InputID);
    let Input = InputID;
    let oldText = Input.val();
    let newText = oldText + text;
    Input.trigger("focus");
    Input.val(newText).trigger("keyup");
}

function typingNumber(number){
    let Input =  InputID;
    let oldNumber = Input.val();
    let newNumber = oldNumber + number;

    if(number === '-'){
        newNumber = oldNumber;
        if(IsClicked.Minus) IsClicked.Minus = false;
        else IsClicked.Minus = true;
    }
    if(IsClicked.Minus && number !== '-') {
        if(oldNumber === "") {
            newNumber = (parseFloat(number) * -1);
        }else {
            newNumber = (parseFloat(number) * -1);
        }
        console.log(newNumber);
        IsClicked.Minus = false;
    }

    if(number === '.'){
        newNumber = oldNumber;
        if(IsClicked.Dot) IsClicked.Dot = false;
        else IsClicked.Dot = true;
    }

    if(IsClicked.Dot && number != '.') {
        if(oldNumber == "") oldNumber = "0";
        newNumber = oldNumber + "." + number
        IsClicked.Dot = false;
    }

    checkKeyIsClicked("keyboard_number_button_dot", IsClicked.Dot);
    // if(!checkKeyboardNumberInputValue(newNumber)) newNumber = oldNumber;
    Input.trigger("focus");
    Input.val(newNumber).trigger("keyup")

}

function Capslock(){
    var data = $(this).attr("data");
}

function Enter(){
    var e = $.Event( "keypress", { keyCode: 13 } );
    InputID.trigger(e);
}

function backSpace(){
    var oldText = InputID.val()
    var newText = oldText.slice(0, -1);
    if($(".keyboard-number").hasClass("keyboard-active")){
        if(newText.slice(-1) == "."){
            newText = newText.slice(0, -1);
        }
    }
    InputID.val(newText).trigger("keyup");
}

function showNumberKeyboard(){
    $(".keyboard-number").addClass("keyboard-active");
}

function hideNumberKeyboard(){
    $(".keyboard-number").removeClass("keyboard-active");
}

function checkKeyIsClicked(keyID, clickedVariable){
    if(clickedVariable)
        $("#"+keyID).addClass("keyboard-btn-is-clicked");
    else
        $("#"+keyID).removeClass("keyboard-btn-is-clicked");
}

function checkKeyboardNumberInputValue(number){
    let regex = /^[0-9]\d{0,9}(\.\d{1,3})?%?$/g;

    return regex.test(number);
}
function KeyboardIsUp(key){
    if (key === 1){
        $(".keyboard").addClass("keyboard-up");
        isUpper = 1;
    }else {
        $(".keyboard").removeClass("keyboard-up");
        isUpper = 0;
    }
}

function ShowVKeyboard(){}
function SetGlobalElementID(id){}
/* end Functions */