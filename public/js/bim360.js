var projectId;
var folderList;
$(document).ready(function(){
  
    var login_user_id = $("#hidLoginID").val();
    var img_src = "../public/image/JPG/会員証のアイコン素材.jpeg";
    var url = "bim360/index";
    var content_name = "BIM360権限設定";
    recordAccessHistory(login_user_id,img_src,url,content_name);
    
   projectId = $("#hidProjectId").val();
  var projectName = $("#hidProjectName").val();
  if(projectId != undefined && projectName != undefined)
    GetProjectUsers(projectId,projectName);
    

  $('#txtSearchBim360User').keyup(function(){
    //alert("keyup");
      var textboxValue = $('#txtSearchBim360User').val();
      $("#bodytable tbody >tr").each(function(index) {
          $row = $(this);
          var name = $row.find("td:eq(0)").text();
          var email = $row.find("td:eq(1)").text();
          //alert(name+"\n"+email);
          if(!name.includes(textboxValue) && !email.includes(textboxValue)){
              //if(index != 0)//skip when body header row
              $row.hide();
          }else{
              $row.show();
          }
    });
  });
  
  $('#txtPopupBim360User').keyup(function(){

      var textboxValue = $('#txtPopupBim360User').val();
      $("#tblBim360Users tbody >tr").each(function(index) {
          $row = $(this);
          var name = $row.find("td:eq(1)").text();
          var email = $row.find("td:eq(2)").text();
          //alert(name+"\n"+email);
          if(!name.includes(textboxValue) && !email.includes(textboxValue)){
                  $row.hide();
          }else{
              $row.show();
          }
    });
  });
  
  $("#bodytable").on("change", ":checkbox", function() {
    //var name = $(this).parents("tr:first").find('td:eq(1)').text();
    var isChecked = $(this).prop('checked');
    var colIndex = $(this).closest('td').index();
    var userId = $(this).closest('tr').attr('id');
    var folderId = $("#headertable > thead >tr:nth-child(2)").find("th:eq("+colIndex+")").find('input[type=hidden]').val();
    if(folderId !== null || folderId != undefined){
      if(isChecked){
        //add permission
        AddFolderPermission(projectId,folderId,userId);
      }else{
        //remove permission
        DeleteFolderPermission(projectId,folderId,userId);
      }
    }
});
 
 $(".table-body").on( 'scroll', function(){
   var offset;
    if(scroll_width >  1500)
      offset = -1*$(this).scrollLeft()+24;//+120
    else
      offset = -1*$(this).scrollLeft()+24;//+120
    
    $(".table-header").offset({ left:offset });
    $('thead').css("left", -$("tbody").scrollLeft());
    $('tbody').css("left", -$("tbody").scrollLeft());
 });

 //table sorting by th
 $('#headertable thead').on('click','tr th',function(){
    var header = $(this).text();
    if(header == "User Information" || header == "Folders")return;
    
    var table = $('#bodytable');// .parents('table').eq(0)
    var rows = $('#bodytable tbody').find('tr').toArray().sort(comparer($(this).index()));
    this.asc = !this.asc
    
    //disable all order icon except click index
    for(var i = 0; i < 4 ; i++){
      if(i != $(this).index()){
        var index = i+1;
        $('#headertable th:nth-child('+index+')').find('.fa-sort-up').addClass('disable-color');
        $('#headertable th:nth-child('+index+')').find('.fa-sort-down').addClass('disable-color');
      }
    }
    
    //set icon enable
    if(this.asc){
        $(this).find('.fa-sort-up').removeClass('disable-color');
        $(this).find('.fa-sort-down').addClass('disable-color');
    }else{
        $(this).find('.fa-sort-down').removeClass('disable-color');
        $(this).find('.fa-sort-up').addClass('disable-color');
    }
    
    
    if (!this.asc){rows = rows.reverse()}
    for (var i = 0; i < rows.length; i++){table.append(rows[i])}
 });
 
});

function comparer(index) {
    return function(a, b) {
        var valA = getCellValue(a, index), valB = getCellValue(b, index)
        return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.toString().localeCompare(valB)
    }
}

function getCellValue(row, index){ 
  return $(row).children('td').eq(index).text(); 
  
}

function DeleteFolderPermission(projectId,folderId,userId){
  //var result = confirm("Are you sure!");
  //if(result){//if true
    //console.log(projectId+"\n"+folderId+"\n"+userId);return;
      $.ajax({
          url: "../bim360/managePermission",
          type: 'post',
          data:{_token: CSRF_TOKEN,message:"deleteFolderPermission",projectId:projectId,folderId:folderId,userId:userId},
          success :function(data) {
           alert(data);return;
           if(data.includes("success")){
             //alert("success");
             console.log("success");
           }                             
          },
          error : function(err){
            console.log(err);
          }
    });
  //}
}

function AddFolderPermission(projectId,folderId,userId){
  //var result = confirm("Are you sure!");
  //console.log(projectId+"\n"+folderId+"\n"+userId);return;
 // if(result){
      $.ajax({
          url: "../bim360/managePermission",
          type: 'post',
          data:{_token: CSRF_TOKEN,message:"addFolderPermission",projectId:projectId,folderId:folderId,userId:userId},
          success :function(data) {
           if(data.includes("success")){
             console.log("success");
             //alert("success");
           }                             
          },
          error : function(err){
            console.log(err);
          }
    });
  //}
}

function GetProjectUsers(projectId,projectName) {
  ShowLoading();
  $.ajax({
          url: "../bim360/getPermissionData",
          type: 'post',
          data:{_token: CSRF_TOKEN,message:"getProjectUsers",projectId:projectId},
          success :function(data) {
           if(data.length > 0){
             console.log(data);
             GetProjectFolders(projectId,projectName,data)
           }
                                           
          },
          error : function(err){
            HideLoading();
            console.log(err);
          }
  });
}

function ProjectPermission(projectId,projectName){

   $.ajax({
          url: "../bim360/setProjectIdToSession",
          type: 'post',
          data:{_token: CSRF_TOKEN,projectId:projectId,projectName:projectName},
          success :function(data) {
            if(data.includes("success")){
              window.open('/iPD/bim360/permission',"_blink");
            }
            // window.location = '';
                                           
          },
          error : function(err){
            console.log(err);
          }
   });
  
}

function GetProjectFolders(projectId,projectName,projectUsersList) {

  $.ajax({
        url: "../bim360/getPermissionData",
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"getFolders",projectId:projectId,projectName:projectName},
        success :function(data) {
          folderList = Object.entries(data);
          CreateTable(folderList,projectUsersList);
          Enable();
                        
        },
        error : function(err){
          HideLoading();
          console.log(err);
          alert(err);
        }
   });
  
}

function Enable(){
  HideLoading();
  //$(".loader").hide();//hide loading div
  $("#btnAllFolderPermission").prop("disabled", false);;
  $("#btnBim360Users").prop("disabled", false);;
}

var scroll_width;
function CreateTable(folderList,projectUserList){
  var appendStr="";
  var projectId = $("#hidProjectId").val();
  //header append string start
  appendStr +="<tr>";
  appendStr += "<th class='head-row' colspan='4'>User Information</th>";
  appendStr += "<th class='head-row' colspan='"+folderList.length+"'>Folders</th>";
  appendStr +="</tr>";
  
  
  appendStr += '<tr>';
  appendStr += "<th class='header-cell col1'>name &nbsp;";
    appendStr += '<span class="icon-stack">';
      appendStr += '<i class="fa fa-sort-up icon-stack-3x disable-color"></i>';
      appendStr += '<i class="fa fa-sort-down icon-stack-3x disable-color"></i>';
    appendStr += '</span>';
  appendStr += "</th>";
  appendStr += "<th class='header-cell col2'>email &nbsp;";
    appendStr += '<span class="icon-stack">';
      appendStr += '<i class="fa fa-sort-up icon-stack-3x disable-color"></i>';
      appendStr += '<i class="fa fa-sort-down icon-stack-3x disable-color"></i>';
    appendStr += '</span>';
  appendStr += "</th>";
  appendStr += "<th class='header-cell col3'>user_type &nbsp;";
    appendStr += '<span class="icon-stack">';
      appendStr += '<i class="fa fa-sort-up icon-stack-3x disable-color"></i>';
      appendStr += '<i class="fa fa-sort-down icon-stack-3x disable-color"></i>';
    appendStr += '</span>';
  appendStr += "</th>";
  appendStr += "<th class='header-cell col4'>company_name &nbsp;";
  appendStr += '<span class="icon-stack">';
      appendStr += '<i class="fa fa-sort-up icon-stack-3x disable-color"></i>';
      appendStr += '<i class="fa fa-sort-down icon-stack-3x disable-color"></i>';
    appendStr += '</span>';
  appendStr += "</th>";
  var colIndex = 3;
 $.each(folderList,function(key,data){
    colIndex++;
    appendStr += '<th class="header-cell col5">'+data[1];
    appendStr += '<input type="hidden" value="'+data[0]+'">';
    appendStr += '</th>';
 });
 appendStr += "</tr>";//header end
 
 $("#headertable thead").append(appendStr);
 //$("#tblUserPermission thead").append(appendStr);
   scroll_width = 800+(folderList.length)*70 + 24;//col1+col2+col3+col4 = 800,
 
 
 //body start
  appendStr = "";

  $.each(projectUserList,function(key,data){
    appendStr += '<tr id="'+data["id"]+'">';
    appendStr += "<td class='body-cell col1'>"+data["name"]+"</td>";
    appendStr += "<td class='body-cell col2'>"+data["email"]+"</td>";
    appendStr += "<td class='body-cell col3'></td>";
    appendStr += "<td class='body-cell col4'>"+data["company_name"]+"</td>";
    
    for(var i = 0 ; i < folderList.length; i++){
      appendStr += "<td class='body-cell col5'><input type='checkbox' id='chk"+i+"'></td>";
    }
    appendStr += "</tr>";
    
  });

  $("#bodytable").append(appendStr);
  //$("#tblUserPermission tbody").append(appendStr);
  //if(scroll_width < 1500){
    $(".table-body").css("width",scroll_width);
  //}
  //$("#tblUserPermission tbody").css("width",scroll_width);
}

function GetFolderUsers(projectId,folderIdArray) {
  //alert(folderId+"\n"+projectId);
   $.ajax({
          url: "../bim360/getPermissionData",
          type: 'post',
          data:{_token: CSRF_TOKEN,message:"getFolderUsers",projectId:projectId,folderIdArray:JSON.stringify(folderIdArray)},
          success :function(data) {
            console.log(data);
            if(!$.isEmptyObject( data )){
              CheckCheckBoxs(data);
            }else{
              console.log(data);
            }
            HideLoading();
             //$(".loader").hide();                              
          },
          error : function(err){
            HideLoading();
            console.log(err);
          }
   });
}

function CheckCheckBoxs(folderUsersList){
  var colIndex = 4;
  $.each(folderUsersList,function(key, folderUsers) {
      $.each(folderUsers,function(key, data) {
      var email = data["email"];
      var userType = data["userType"];
    
      $('#bodytable > tbody  > tr').each(function(index, tr) { 
       var tblEmail = $(tr).find("td:eq(1)").text();
       var td = $(tr).find("td:eq("+colIndex+")");
      
       if(tblEmail.trim() === email.trim()){
         td.find('input[type=checkbox]').prop('checked', true);
         var userTypetd = $(tr).find("td:eq("+2+")");//userType column index
         userTypetd.text(userType);
       }

      });
    });
    colIndex++;
  });
  
}


function CreateUserList(users,rowId){
    
    /*var appendStr = "";
    var count = 0;
    $.each(users,function(key,user){
        count++;
        appendStr +="<tr id='"+user["id"]+"'>"
        appendStr +="<td>"+count+".</td>";
        appendStr +="<td>"+user["name"]+"</td>";
        appendStr +="<td>"+user["email"]+"</td>";
        appendStr +="<td>"+user["userType"]+"</td>";
        appendStr +="</tr>"
    });
    $('#tblCurrentFolderUsers tbody >tr').remove();
    $('#tblCurrentFolderUsers tbody').append(appendStr)*/
  
}

function GetAllUsers(){
  ShowLoading();
   $.ajax({
          url: "../bim360/getUsers",
          type: 'post',
          data:{_token: CSRF_TOKEN,message:"getUsers"},
          success :function(data) {
            HideLoading();
             if(data.includes("success")){
               alert("successfully refreshed!");
               //location.reload();
             }
          },
          error : function(err){
            HideLoading();
            console.log(err);
          }
   });
}

var Bim360Users = [];
function DisplayBim360UsersPopup(){
  if(Bim360Users.length > 0){
    CreateBim360UserTable(Bim360Users);
    return;
  }
  $.ajax({
          url: "../bim360/getPermissionData",
          type: 'post',
          data:{_token: CSRF_TOKEN,message:"getBim360Users"},
          success :function(data) {
             if(data.length > 0){
              Bim360Users = data;
              CreateBim360UserTable(data);
             }
          },
          error : function(err){
            console.log(err);
          }
   });
 
}

function CreateBim360UserTable(data){
  var appendStr="";
  var i = 0;
  $.each(data,function(key, row) {
      i++;
      appendStr += '<tr id="'+row["id"]+'">';
      appendStr += "<td width='31'><input type='checkbox' id='chk"+i+"'></td>";
      appendStr += "<td width='200'>"+row["name"]+"</td>";
      appendStr += "<td width='315'>"+row["email"]+"</td>";
      appendStr += "</tr>";
  });
  $("#tblBim360Users > tbody > tr").remove();
  $("#tblBim360Users tbody").append(appendStr);
 $('#myModal').modal('show');
}

function AddUsersToTable(){
  ShowLoading();
  var UserIdList =[];
  $('#tblBim360Users tr').each(function(i) {
    var chkbox = $(this).find('input[type="checkbox"]');
    if(chkbox.prop('checked')){
      var userId = $(this).attr('id');
      //alert($(this).attr('id'));
      UserIdList.push(userId);
    }
  });

 if(UserIdList.length > 0){

   var result = GetBim360UserByGivenUserIds(UserIdList);
   var addedNewMember = AddNewMemberToProject(UserIdList,projectId);
   if(addedNewMember.includes("success")){
     AddNewRowToTable(result);
   }else{
     alert(addedNewMember);//error alert
   }
   
 }
HideLoading();
}

function AddNewMemberToProject(UserIds,projectId){
  var result;
  $.ajax({
          url: "../bim360/managePermission",
          type: 'post',
          async:false,
          data:{_token: CSRF_TOKEN,message:"addNewMember",UserIds:JSON.stringify(UserIds),projectId:projectId},
          success :function(data) {
             result = data;
          },
          error : function(err){
            console.log(err);
            result = err;
          }
   });
   return result;
}

function GetBim360UserByGivenUserIds(UserIds){
  var result;
  $.ajax({
          url: "../bim360/getPermissionData",
          type: 'post',
          async:false,
          data:{_token: CSRF_TOKEN,message:"getBim360UsersByIds",UserIds:JSON.stringify(UserIds)},
          success :function(data) {
             if(data.length > 0){
               result= data;
             }
          },
          error : function(err){
            console.log(err);
          }
   });
   return result;
}

function AddNewRowToTable(users){
  var appendStr = "";
  $.each(users,function(key, data) {
      var userId = data["id"];
      var alreadyExist = false;
      $("#bodytable tbody  tr").each(function(index) {
          if(index == 0)return;//if tbody first row skip coz of header
          var hid_user_id = $(this).attr('id');
          if(userId == hid_user_id){
            alreadyExist = true;return;
          }
      });
      
      if(!alreadyExist){
          appendStr += '<tr id="'+data["id"]+'">';
          appendStr += "<td class='body-cell col1'>"+data["name"]+"</td>";
          appendStr += "<td class='body-cell col2'>"+data["email"]+"</td>";
          appendStr += "<td class='body-cell col3'></td>";
          appendStr += "<td class='body-cell col4'>"+data["company_name"]+"</td>";
          var colCount = $("#bodytable  tbody  tr:first  td").length;
          
          for(var i = 0 ; i < colCount-4; i++){//minus 4 cos of not folder column
            appendStr += "<td class='body-cell col5'><input type='checkbox' id='chk"+i+"'></td>";
          }
          appendStr += "</tr>";
      }
  });
  
  $("#bodytable tbody").append(appendStr);
  $("#myModal").modal('hide');
}

function DisplayAllFolderPermission(){

  var folderIdArray=[];
  $.each(folderList,function(key,data ) {
      var id = data[0];
      folderIdArray.push(id);
  });
  if(folderIdArray.length > 0){
    ShowLoading();
    //$(".loader").show();
    GetFolderUsers(projectId,folderIdArray);
  }
}