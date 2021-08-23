var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

$(document).ready(function () {
    $.ajaxSetup({
        cache:false
    });

})

function saveAccessLog(functionName){
   var currentDateTime = getCurrentDateAndTime();
   var loginUserName = $("#hiddenLoginUser").val();
   console.log(loginUserName)
   $.ajax({
       type     : "post",
       url      : "../common/saveAccessLog",
       data     : {_token: CSRF_TOKEN,message : "saveAccessLog", functionName : functionName, loginUserName : loginUserName,currentDateTime :currentDateTime},
       success  : function(data){
            console.log(data);
       },
       error    : function(err){
            console.log(err);
        }
   });
   
}

function getCurrentDateAndTime(){
    var current = new Date();
    var year = current.getFullYear();
    var month = current.getMonth()+1;
    var day = current.getDate();
    var hour = current.getHours();
    var minute = current.getMinutes();
    var result = year+"."+month+"."+day+" "+hour+":"+minute;
    return result;
}