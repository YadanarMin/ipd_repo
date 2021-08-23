var dataArray = [];

function SearchUserData()
{
    var projectName = $("#projectName").val();
    var startDate = $("#startDate").val();
    var endDate = $("#endDate").val();
    $.ajax({
        type:"POST",
        url:"/RevitWebSystem/searchUser.php",			   
        data:{message:"searchData",name:projectName,startDate:startDate,endDate:endDate},
        success:function(data)
        {
            //alert(JSON.stringify(data));
            var results = JSON.parse(data);
            if(results.includes("empty") || results === undefined || results.length == 0){
                alert("検索情報はすでにありません！");
                location.reload();              
            }
            var newRow = "";
            if(results.length > 0)
            {
                $("#tbUser tr td").remove();
                var count = 1;
                results.forEach(function(ele) {
                    if(ele["name"] == null || ele["name"] == "")return;
                    if(!dataArray.includes(ele["name"]))
                        dataArray.push(ele["name"]);
                    newRow += "<tr>";
                    newRow += "<td height='20px'>"+count+"</td>";
                    newRow += "<td>"+ele["name"]+"</td>";
                    newRow += "<td>"+ele["document"]+"</td>";
                    newRow += "<td>"+ele["button_name"]+"</td>";                    
                    newRow += "<td>"+ele["count"]+"</td>";
                    newRow += "<td>"+ele["used_date"]+"</td>";
                    newRow += "</tr>";
                    count++;
                });
                $("#tbUser").append(newRow);             
            }
        }
    });

} 

$( document ).ready(function() {
    SearchUserData();
  
      $("#projectName").autocomplete({
        source: dataArray
      });    
});

function DisplayUserControlData(){
    $.ajax({
        type:"POST",
        url:"/RevitWebSystem/searchUser.php",			   
        data:{message:"get_user"},
        success:function(data)
        {
            var results = JSON.parse(data);
            
            var newRow = "";
            if(results.length > 0)
            {
                $("#tbUserControl tr td").remove();
                var count = 1;
                results.forEach(function(ele) {
                    if(ele["name"] == null || ele["name"] == "")return;
                    newRow += "<tr>";
                    newRow += "<td height='20px'>"+count+".</td>";
                    newRow += "<td>"+ele["name"]+"</td>";
                    if(ele["unauthorized"] == 1){
                        newRow += "<td><input type='checkbox' id='chkUseable' name='chkuseable' class='largerCheckbox' checked/></td>";   
                    }else{
                        newRow += "<td><input type='checkbox' id='chkUseable' name='chkuseable' class='largerCheckbox'/></td>";   
                    }
                       
                    if(ele["revit_added_transaction"] == 1){
                        newRow += "<td><input type='checkbox' id='chkAdded' name='chkAdded' class='largerCheckbox' checked/></td>";   
                    }else{
                        newRow += "<td><input type='checkbox' id='chkAdded' name='chkAdded' class='largerCheckbox'/></td>";   
                    }

                    if(ele["revit_edited_transaction"] == 1){
                        newRow += "<td><input type='checkbox' id='chkEdited' name='chkEdited' class='largerCheckbox' checked/></td>";   
                    }else{
                        newRow += "<td><input type='checkbox' id='chkEdited' name='chkEdited' class='largerCheckbox'/></td>";   
                    }

                    if(ele["revit_deleted_transaction"] == 1){
                        newRow += "<td><input type='checkbox' id='chkDeleted' name='chkDeleted' class='largerCheckbox' checked/></td>";   
                    }else{
                        newRow += "<td><input type='checkbox' id='chkDeleted' name='chkDeleted' class='largerCheckbox'/></td>";   
                    }

                    newRow += "</tr>";
                    count++;
                });
                $("#tbUserControl tbody").append(newRow);     
            }        
        }
    });
}

function UserControlPopupDisplay(){
    $("#UserControlPopup").css({ visibility: "visible",opacity: "1"});
    DisplayUserControlData();
}

function CloseUserControlPopup(){
    $("#UserControlPopup").css({ visibility: "hidden",opacity: "0"});
}

function SaveUserControl(){

    var updateData = [];
    
    $('#tbUserControl tbody tr').each(function() {
        var authorized = 0;
        var chkAddedUser = 0;       
        var chkEditedUser = 0;      
        var chkDeletedUser = 0;

        var userName = $(this).find("td:nth-child(2)").html();  
        if(userName == undefined)return;//need to check why undefine
        var chkuseable = $(this).find("td:nth-child(3) input[type=checkbox]");
        var chkAdded = $(this).find("td:nth-child(4) input[type=checkbox]");
        var chkEdited = $(this).find("td:nth-child(5) input[type=checkbox]");
        var chkDeleted = $(this).find("td:nth-child(6) input[type=checkbox]");
        if(chkuseable.prop('checked')==true) authorized = 1;
        if(chkAdded.prop('checked') == true) chkAddedUser = 1;
        if(chkEdited.prop('checked') == true) chkEditedUser = 1;
        if(chkDeleted.prop('checked') == true) chkDeletedUser = 1;
       
        updateData.push({"userName":userName,"authorize":authorized,"added":chkAddedUser,"edited":chkEditedUser,"deleted":chkDeletedUser});               
    });

    if(updateData.length > 0){
        $.ajax({
            type:"POST",
            url:"/RevitWebSystem/searchUser.php",			   
            data:{message:"update_user",updateData:JSON.stringify(updateData)},
            success:function(data)
            {
                console.log(data);
                var results = JSON.parse(data);
                if(results.includes("success")){
                    $("#UserControlPopup").css({ visibility: "hidden",opacity: "0"});  
                }                   
            }
        });
    }
}