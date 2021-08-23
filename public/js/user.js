var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
$(document).ready(function(){
    
    // var tmp_authority = JSON.parse( $("#hidAuthorityData").val() );
    // console.log(tmp_authority);
    
    var login_user_id = $("#hidLoginID").val();
    var img_src = "../public/image/JPG/鍵のクローズアイコン素材.jpeg";
    var url = "user/index";
    var content_name = "ﾕｰｻﾞ設定";
    recordAccessHistory(login_user_id,img_src,url,content_name);
    
    createAuthoritySelect();

    $("#show_hide_password a,#show_hide_newpassword a,#show_hide_confirmpassword a").on("click",function(event){
        event.preventDefault();
        var id = $(this).parent().siblings().closest('input').attr('id');

        if($(this).find('i').hasClass("fa-eye")){//current is show,need to hide
            $('#'+id).attr('type', 'password');
            $(this).find('i').removeClass("fa-eye");
            $(this).find('i').addClass("fa-eye-slash")
        }else{

            $('#'+id).attr('type', 'text');
            $(this).find('i').removeClass("fa-eye-slash");
            $(this).find('i').addClass("fa-eye")
        }
    })
    
     $("#txtPassword").on("keyup",function(event){
         var id = $(this).attr('id');
         if($(this).val() !== ""){
             $("#"+id+"_err").html('');
         }
     });
     
    $("#emailSelect").change(function(){

        var selectedMail = $("option:selected",this).val();
        var notC3UserData = JSON.parse($("#hidNotC3Users").val());
        
        console.log(selectedMail);
        console.log(notC3UserData);
        
        for(var i = 0; i < notC3UserData.length; i++){
            if(notC3UserData[i]["mail"] === selectedMail){
                console.log(notC3UserData[i]["name"]);
                console.log(notC3UserData[i]["branch_name"] + " " + notC3UserData[i]["dept_name"]);
                
                var dept_name = "";
                if(notC3UserData[i]["branch_name"] === ""){
                    dept_name = notC3UserData[i]["dept_name"];
                }else{
                    if(notC3UserData[i]["dept_name"] === ""){
                        dept_name = notC3UserData[i]["branch_name"];
                    }else{
                        dept_name = notC3UserData[i]["branch_name"] + " " + notC3UserData[i]["dept_name"];
                    }
                }

                $("#hidPersonalId").val(notC3UserData[i]["id"]);
                $("#txtName").val(notC3UserData[i]["first_name"] + " " + notC3UserData[i]["last_name"]);
                $("#txtDeptName").val(dept_name);
                return false;
            }
        }

        if($(this).text() !== "" || $(this).text() !== "選択してください" ){
            $("#emailSelect_err").html('');
        }
    });
    
    $("#authoritySelect").change(function(){

        if($(this).text() !== "" || $(this).text() !== "選択してください" ){
            $("#authoritySelect_err").html('');
        }
    });
    
    $("#tblUser input[type='checkbox']").change(function() {
        var chkName = $(this).attr('name');
        var personalId = $(this).val();
        var status = 0;
        if(this.checked) {
            status = 1;
        }
        ChangeLoginUserSetting(status,personalId,chkName);
    });
});
  
function ChangeLoginUserSetting(status,personalId,chkName){
    $.ajax({
        url: "../user/changeSetting",
        type: 'post',
        data:{_token: CSRF_TOKEN,status:status,personalId:personalId,checkboxName:chkName},
        success :function(message) {
           // console.log(message);
           if(message.includes("success")){
               alert("設定しました。");
               location.reload();
           }
           
        },
        error:function(err){
            alert(JSON.stringify(err));
            console.log(err);
        }
    });    
}    
function CreateUser(){
    
    var isValid =  Validataion();
    if(!isValid) return;
    var form = $(document.forms["createUserForm"]).serializeArray();
    // console.log(form);

    $.ajax({
        url: "../user/create",
        type: 'post',
        data:{_token: CSRF_TOKEN,form},
        success :function(message) {
            console.log(message);
           if(message.includes("success")){
               alert("successfully saved!");
               location.reload();
           }
           
        },
        error:function(err){
            alert(JSON.stringify(err));
            console.log(err);
        }
    });    
}

function Validataion(){
    if($("#txtPassword").val() == ""){
        $("#txtPassword_err").html("パスワードを入力してください。");
        return false;
    }else if($('#emailSelect :selected').text() == '' || $('#emailSelect :selected').text() == '選択してください'){
        $("#emailSelect_err").html("メールアドレスを選択してください。");
        return false;
    }else if($('#authoritySelect :selected').text() == '' || $('#authoritySelect :selected').text() == '選択してください'){
        $("#authoritySelect_err").html("権限を選択してください。");
        return false;
    }else{
        return true;
    }
}

function ClosePopup(){
    $("#createUser").css({ visibility: "hidden",opacity: "0"});
    $("#emailSelect").prop("disabled", true);
}

function DisplayPopup(id){

    if(id != undefined || id != null){

        $.ajax({
            url: "../user/getData",
            type: 'post',
            data:{_token: CSRF_TOKEN,userID:id},
            success :function(result) {
                console.log(result);
               if(result.length > 0){
                    var data = result[0];
                    $("#hidPersonalId").val(data["id"]);
                    $("#hidIsC3User").val(data["isC3User"]);
                    $("#txtPassword").val(data["password"]);
                    $("#txtName").val(data["name"]);
                    $("#txtDeptName").val(data["branch_name"] + " " + data["dept_name"]);

                    selectedOption("authoritySelect", data["authority_name"]);
                    createEmailSelect(false, data["mail"]);

                    $("#hidOldAuthorityID").val($('#authoritySelect option:selected').val());

                    $("#createUser").css({ visibility: "visible",opacity: "1"});
               }                          
            },
            error:function(err){
                alert(JSON.stringify(err));
                console.log(err);
            }
        });
    }else{
        createEmailSelect(true, "選択してください");
        
        //ユーザ新規作成
        $("#hidIsC3User").val(0);
        $("#hidPersonalId").val(0);
        $("#txtPassword").val("");
        $("#txtName").val("");
        $("#txtDeptName").val("");
        $('#authoritySelect').val("");
        $("#hidOldAuthorityID").val(0);

        $("#createUser").css({ visibility: "visible",opacity: "1"});
    }
    
}

function createAuthoritySelect(){
    
    $.ajax({
        url: "../user/getAuthorityData",
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"getAllAuthority",authority_id:0,authority_name:""},
        success :function(result) {
           if(result.length > 0){
                // console.log(result);

                var appendText = "";
                appendText += "<option value=''>選択してください</option>";

                for (var i = 0; i < result.length; i++) {
                    appendText += "<option value='"+result[i]["id"]+"'>"+result[i]["name"]+"</option>";
                }

                $("select#authoritySelect option").remove();
                $("#authoritySelect").append(appendText);
           }                          
        },
        error:function(err){
            console.log(err);
        }
    });

}

function createEmailSelect(isNew, selectedName){
    
    $.ajax({
        url: "../personal/getData",
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"AllPersonalData",id:0},
        success :function(data) {
            if(data != null){
            	console.log(data);

                var appendText = "";
                appendText += "<option value=''>選択してください</option>";

                if(isNew){
                    for (var i = 0; i < data.length; i++) {
                        if(data[i]["isC3User"] === 0){
                            appendText += "<option value='" + data[i]["mail"] + "'>"+data[i]["mail"]+"</option>";
                        }
                    }
                    $("#emailSelect").prop("disabled", false);
                }else{
                    for (var i = 0; i < data.length; i++) {
                        if(data[i]["isC3User"] !== 0){
                            if(data[i]["mail"]===  selectedName){
                                appendText += "<option value='" + data[i]["mail"] + "' selected>"+data[i]["mail"]+"</option>";
                            }else{
                                appendText += "<option value='" + data[i]["mail"] + "'>"+data[i]["mail"]+"</option>";
                            }
                        }
                    }
                    $("#emailSelect").prop("disabled", true);
                }

                $("select#emailSelect option").remove();
                $("#emailSelect").append(appendText);
            }
        },
        error:function(err){
            console.log(err);
        }
    });
    
}

function selectedOption(id_name,option_name){
    let $element = $('#'+id_name);
    let val = $element.find("option:contains('"+option_name+"')").val();
    $element.val(val).trigger('change');
}

function DeleteUser(id, name) {

    var result = confirm("本当に削除しますか？ 【ユーザー名:"+name+"】");
    if(result === true){
        $.ajax({
            url: "../user/deleteData",
            type: 'post',
            data:{_token: CSRF_TOKEN,userID:id},
            success :function(message) {
               if(message.includes("success")){
                   location.reload();
               }   
            },
            error:function(err){
                alert(JSON.stringify(err));
                console.log(err);
            }
        });     
    }
}

function CreateRandomPassword(){
    var pass = ''; 
    var str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'; 
    for (var i = 1; i <= 8; i++) { 
        pass += str.charAt(Math.floor(Math.random() * str.length));
    } 

    $("#txtPassword").val(pass);
    
}

function ChangePassword(){
    //$current_password = $("#txtPassword").val();
    var new_password = $("#txtNewPassword").val();
    var confirm_new_password = $("#txtConfirmNewPassword").val();
    if(new_password !== confirm_new_password){
        $("#txtWarning").html("新パスワードと新パスワード確認が違っています。")
        return;
    }else if(new_password == "" && confirm_new_password == ""){
        $("#txtWarning").html("新パスワードを入力してください。")
        return;
    }
    //console.log(new_password);
    $.ajax({
            url: "../change/newpassword",
            type: 'post',
            data:{_token: CSRF_TOKEN,newPassword:new_password},
            success :function(message) {
               if(message.includes("success")){
                   location.href="/iPD/login/successlogin";
               }else{
                   console.log(message);
               }   
            },
            error:function(err){
                alert(JSON.stringify(err));
                console.log(err);
            }
        });   
}

function ApproveByChiefAdmin(userId){
    window.location = "/iPD/login/approve/step1/"+userId;
}

function ApproveByCCCAdmin(userId,chiefAdminId){
   
    window.location = "/iPD/login/approve/step2/"+userId+"/"+chiefAdminId;
}

