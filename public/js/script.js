$(document).ready(function(){

    $('#txtSearch').keyup(function(){
        var textboxValue = $('#txtSearch').val();
        $("#tblSetting tr").each(function(index) {
            if (index !== 0) {
                $row = $(this);
                console.log($row);
                var projectName = $row.find("td:first").text();
                if(!projectName.includes(textboxValue)){
                    $row.hide();
                }else{
                    $row.show();
                }
            }
        });
    });

    var projectId = $("#hidProjectId").val();
    var projectName = $("#hidProjectName").val();
    if(projectId != undefined && projectName != undefined)
        GetProjectFolders(projectId,projectName);
});

function SaveAdminSetting(){

  var autoSaveProject = [];
  //var autoBackupProject = [];
  $('#tblSetting tr').each(function() {
    var projectName =	$(this).find("td:eq(0)").text();
    var chkAutoSave = $(this).find("input[name=chkAutoSave]");
    //var chkBackup = $(this).find("input[name=chkBackup]");
    if(chkAutoSave.prop('checked')==true){   
      autoSaveProject.push(projectName);
    }
    /*if(chkBackup.prop('checked') == true){
      autoBackupProject.push(projectName);
    }*/
  });

  if(autoSaveProject.length < 0) return;
  $.ajax({
      url: "../forge/saveData",
      type: 'post',
      data:{_token: CSRF_TOKEN,message:"update_project_auto_save",projects:autoSaveProject},//,backupProjects:autoBackupProject
      success :function(message) {
         if(message.includes("success")){
             alert("successfully saved!");
             location.reload();
         }
                     
      },
      error:function(err){
          console.log(err);
      }
  }); 
}

function SaveBackupSetting(){
  var autoBackupProject = [];
  $('#tblSetting tr').each(function() {
    var projectName =	$(this).find("td:eq(0)").text();
    var chkBackup = $(this).find("input[name=chkBackup]");
    if(chkBackup.prop('checked') == true){
          autoBackupProject.push(projectName);
    }
  });

  if(autoBackupProject.length < 0) return;
  $.ajax({
      url: "../forge/saveData",
      type: 'post',
      data:{_token: CSRF_TOKEN,message:"update_backup_project",backupProjects:autoBackupProject},//,backupProjects:autoBackupProject
      success :function(message) {
         if(message.includes("success")){
             alert("successfully saved!");
             location.reload();
         }
                     
      },
      error:function(err){
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


function GetProjectFolders(projectId,projectName) {
  $.ajax({
          url: "../bim360/permission",
          type: 'post',
          data:{_token: CSRF_TOKEN,message:"getFolders",projectId:projectId,projectName:projectName},
          success :function(data) {
            var folderList = Object.entries(data);
            CreateFolderList(folderList);
                                           
          },
          error : function(err){
            console.log(err);
          }
   });
  
}

function CreateFolderList(folderList){
  var appendStr="";
  var projectId = $("#hidProjectId").val();
  $.each(folderList,function(key,data){
    appendStr += "<tr>";
    appendStr += "<td>";
    appendStr += '<a href="javascript:void(0);" onclick="GetFolderUsers(\'' + data[0] + '\',\'' + projectId + '\')">'+data[1]+'</a>';
    appendStr += "</td><td></td>";
    appendStr += "</tr>";
    
  });
  
  $("#tblUserPermission").append(appendStr);
}

function GetFolderUsers(folderId,projectId) {
  alert(folderId+"\n"+projectId);
   $.ajax({
          url: "../bim360/permission",
          type: 'post',
          data:{_token: CSRF_TOKEN,message:"getFolderUsers",projectId:projectId,folderId:folderId},
          success :function(data) {
            alert(data);
            //var folderList = Object.entries(data);
            //CreateFolderList(folderList);
                                           
          },
          error : function(err){
            console.log(err);
          }
   });
}