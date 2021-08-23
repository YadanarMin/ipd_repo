/* ajax通信トークン定義 */
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
var personal_info = [];

$(document).ready(function(){
    $.ajaxSetup({
        cache:false
    });
    
   LoadData();
});

function LoadData(){
    $.ajax({
        url: "/iPD/login/account/get",
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"get_authority_list"},
        success :function(data) {
            //alert(data);return;
            if(data != null){
               BindAuthorityData(data);
            }
        },
        error:function(err){
            console.log(err);
        }
    });
}

function BindAuthorityData(data){
    var authorityList = [];
    console.log(data);
    $.each(data,function(index,item){
        var id = item["id"];
        var authorityName = item['name'].trim();

        //if(!Object.values(authorityList).includes(authorityName) && authorityName != undefined && authorityName != ""){
            authorityList.push({"label":authorityName,"value":id});
            //authorityList[id] = authorityName;
        //}
    });
    //console.log(Object.values(authorityList));
    $( "#txtAuthority" ).autocomplete({
       minLength:0,
       source: authorityList,
       select: function(event, ui) {
			var selectedObj = ui.item;
			$(this).val(selectedObj.label);
			$("#hidAuthoritId").val(selectedObj.value);
			return false;
		}
    }).bind('focus', function(){ $(this).autocomplete("search"); } );
}

function ApproveByChiefAdmin(userID){
    var chk = $("#chkAccept").prop('checked');
    if(chk == false){
        var message = "※ 規約に同意するチェックを付けてください。"
        $("#err_message").html(message);
        return;
    }

    $.ajax({
        url: "/iPD/login/account/save",
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"approve_by_chief_admin",userID:userID},
        success :function(data) {
            //alert(data);return;
            if(data.includes("success")){
               alert("承認しました。");
               window.location = "/iPD/user/index";
            }
        },
        error:function(err){
            console.log(err);
        }
    });
}


function ApproveByCCCAdmin(userID,chiefAdminId){
    var authorityId = $("#hidAuthoritId").val();
    if(authorityId == ""){
        var message = "※ ユーザー権限を選択してください。";
        $("#err_message").html(message);
        return;
    }

    $.ajax({
        url: "/iPD/login/account/save",
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"approve_by_ccc_admin",userID:userID,authorityId:authorityId},
        success :function(data) {
            //alert(data);return;
            if(data.includes("success")){
               alert("承認しました。");
               window.location = "/iPD/user/index";
            }
        },
        error:function(err){
            console.log(err);
        }
    });
}

function Decline(personalId,companyId){
    if(personalId == "") return;
    var result = confirm("却下していいですか？");
    if(result == false)return;
    $.ajax({
        url: "/iPD/login/account/delete",
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"delete_login_info",personalId:personalId,companyId:companyId},
        success :function(data) {
            //alert(data);return;
            if(data.includes("success")){
               alert("却下しました。")
               window.location = "/iPD/user/index";
            }
        },
        error:function(err){
            console.log(err);
        }
    });
    
}

function AJAX(){
    //alert("mail");
    $.ajax({
        url: "/iPD/login/email",
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"send_mail_to_chief_admin"},
        success :function(data) {
            //alert(data);return;
            if(data.includes("success")){
               
            }
        },
        error:function(err){
            console.log(err);
        }
    });
}

