var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
function GetThreeLeggedAuth(){
    var btnText = $("#btnForgeLogin").text();
    $.ajax({
        url: "../forge/login",
        type: 'post',
        data:{_token: CSRF_TOKEN,btnText:btnText},
        success :function(data) {
            if(data.includes("LOGIN")){
                window.location.href="/iPD/login/successlogin";
            }else{
                location.href = data; 
            }
                       
        },
        error:function(err){
            console.log(err);
        }
    });    
}

function Logout(){
    //var winref = window.open(null,"pdfWindow");
    //winref.close();
    window.location="/iPD/login/logout";
    window.focus("/iPD/login/logout")
}

function DownloadFiles(){
    //alert("download");
    var downloadFileList = [];
    $('input[type=checkbox]').each(function () {
        
        if($(this).prop("checked") == true){
           var parentClass = $(this).parent().prop('className');
            if(parentClass == "content-item"){
                var fileName = $(this).parent().find("label").html();
                downloadFileList.push(fileName);
            } 
        }
    });

    if(downloadFileList.length > 0){
        window.location="/iPD/common/download/"+JSON.stringify(downloadFileList);
    }
}

function UploadFiles(){
    window.open("/iPD/common/upload","upload");
}