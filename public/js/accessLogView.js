var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

$(document).ready(function () {
    $.ajaxSetup({
        cache:false
    });
    
    LoadAccessLog();
    ShowSuggestedUser();
    
    $("#username").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#searchableAccessLog tr").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
     });
})

function LoadAccessLog(){
    ShowLoading();
    $.ajax({
       type     : "post",
       url      : "../common/getAccessLog",
       data     : {_token: CSRF_TOKEN,message : "getAccessLog"},
       success  : function(data){
            DisplayAccessLog(data);
            HideLoading();
       },
       error    : function(err){
            console.log(err);
            HideLoading();
        }
   });
}

function DisplayAccessLog(data){
    $.each(data, function(key,value){
        var i = 1;
        $.each(value, function(tname,tdata){
            var row="<tr>"+
                    "<td>"+ (i++) + "</td>"+   
                    "<td>"+ tdata['loginUser'] + "</td>"+
                    "<td>"+ tdata['funName'] + "</td>"+
                    "<td>"+ tdata['curDateTime'] + "</td>"+
                    "</tr>";
            $("#accessLogTable tbody").append(row);
        });
    });
}

function searchAccessLog(){
    var name = $('#username').val();
    console.log(name);
    var startDate = $('#startDate').val();
    var endDate = $('#endDate').val();
    $.ajax({
        type     : "post",
        url      : "../common/getAccessLog",			   
        data     :{_token: CSRF_TOKEN, message:"searchData", name:name, startDate :startDate, endDate:endDate},
        success:function(data)
        {
            console.log(data[0].length)
            if(data[0].length){
                ClearTable();
                console.log("SearchData" + JSON.stringify(data));
                DisplayAccessLog(data);
            }else{
                alert("検索情報はすでにありません！");
                location.reload();  
            }
            
        },
        error    : function(err){
            console.log(err);
        }
        
    });

}

function ClearTable(){
    $("#accessLogTable tbody").empty();
}

function ShowSuggestedUser(){
    var user = [];
    $.ajax({
        url: "../common/getAccessLog",
        async:true,
        type: 'post',
        data:{_token: CSRF_TOKEN, message:"getAccessLog"},
        success :function(data) {
            if(data){
                $.each(data, function(key,value){
                    $.each(value, function(tname, tdata){
                        if(tdata['loginUser']){
                            if(!user.includes(tdata['loginUser'])){
                                 user.push(tdata['loginUser']);
                            }
                        }
                    });
                    
                });
                $( "#username" ).autocomplete({
                   source: user,
                   minLength:0
                }).bind('focus', function(){ $(this).autocomplete("search"); } );
            }
        },
        error:function(err){
            console.log(err);
        }
    });
}