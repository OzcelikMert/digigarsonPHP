function getNewRequests(){
    let AccountID = getAccountID_AV();
    let RemovedTableSections = getTableSections_AAC(AccountID);
    $.ajax({
        url:'./sameparts/application_classes/php/functions/get_new_requests.php',
        type:'POST',
        data: {RemovedTableSections:RemovedTableSections},
        success:function(data){
            console.log(data);
            data = JSON.parse(data);
            if(data.table_section != null && data.table_section.length > 0){
                let notificationInfos = {
                    "title": language.data.USER_REQUEST,
                    "message": `${language.data.TABLE} ${data.table_section} ${data.table_no} ${data.service_name} ${language.data.HAS_REQUEST}`
                };
                showNewRequest_AN(JSON.stringify(notificationInfos));
                showNewRequestPopup(data);
            }
        },
        error: function(){
            console.error("Error getNewRequest")
        }
    });
}

function showNewRequestPopup(requestInfo){
    let newRequestPopupTitle = language.data.USER_REQUEST;
    let newRequestPopupMessage = `${language.data.TABLE} <b>${requestInfo.table_section}${requestInfo.table_no}</b> <i>${requestInfo.service_name}</i> ${language.data.HAS_REQUEST}.`;
    Swal.fire({
        icon: "info",
        title: newRequestPopupTitle,
        html: newRequestPopupMessage,
        allowEscapeKey: false,
        allowOutsideClick: false,
        showCancelButton: false,
        timer: 2500,
        timerProgressBar: true,
        confirmButtonText: language.data.OK,
        confirmButtonClass: 'btn btn-success btn-lg mr-3 mt-5',
        cancelButtonClass: 'btn btn-danger btn-lg ml-3 mt-5',
        buttonsStyling: false
    }).then((result) => {
        if (result.value) {
            updateRequestWaiter(requestInfo.id, requestInfo.table_no, requestInfo.table_section);
        }
    });
}

function updateRequestWaiter(requestID, tableNo, tableSection){
    $.ajax({
        url:'./sameparts/application_classes/php/functions/set_request_waiter.php',
        type:'POST',
        data: {RequestID:requestID, TableNo:tableNo, TableSection:tableSection},
        success:function(data){
            console.log(data);
        }
    });
}

function setPrinterOrderForTable(tableNo, tableSection){
    TableInfo.TableNo = tableNo;
    TableInfo.TableSection = tableSection;
    showPrinterOrderQuestion("", "");
}