function creating_mtoast(json,setHtmlLocation){
    if(json == "[]"){
        $(setHtmlLocation).html("");
        return;
    }

    var Data = JSON.parse(json);
    var Count = 0;
    var Size = 45;
    var ToastList = "";
    Data.forEach(value => {
        Count++;
        Size +=50;
        var Message = value.table_section+" "+value.table_no+" "+value.service_name+ " İsteği Gönderdi!";
        ToastList += addToast(Size+"px",Message,"SetServicesNotification('"+value.id+"');",value.id);
        $(setHtmlLocation).html(ToastList);
    });
}

function addToast(Top,Message,CloseMethod,id){
    return ToastHtml = '<div class="__mtoast" id="__mtoast-s'+id+'" style="top:'+Top+'"><a href="javascript:void(0)" onclick="'+CloseMethod+'">'+Message+'</span></div>';
}   

	
function GetServicesNotification(){
	$.ajax({
        url:'./sameparts/toast/php/notification_requests.php',
		type:'POST',
		data:{Method:"get"},
		success: function(data){ 
            creating_mtoast(data,"._mtoast-div");
            $("._mtoast-div").fadeIn();
         },
		error: function(jqXHR, textStatus) { (textStatus == 'timeout') ? MergePays(BranchCode ,CusID ,OrderID,TableNo) : alert(data); },timeout:7000
	});
}

function SetServicesNotification(id){
    $("#__mtoast-s"+id).fadeOut();

	$.ajax({
        url:'./sameparts/toast/php/notification_requests.php',
        type:'POST',
		data:{Method:"set",Id:id},
		//success: function(data){  },
		error: function(jqXHR, textStatus) { (textStatus == 'timeout') ? MergePays(BranchCode ,CusID ,OrderID,TableNo) : alert(data); },timeout:3000
	});
}
    
$(document).ready(function() {
    startNotification();
    GetServicesNotification();
});
	


function startNotification(){
    setInterval(function(){ GetServicesNotification(); }, 30000);
}