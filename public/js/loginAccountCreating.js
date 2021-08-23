/* ajax通信トークン定義 */
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

var company_info = [];
var deptList = [];
$(document).ready(function(){
    $.ajaxSetup({
        cache:false
    });
    
    $('input:text,input:hidden,input:password').each(function(){
        $(this).val($.trim($(this).val()));
    });
    
    $('textarea').each(function(){
        $(this).html($.trim($(this).html()));
    });
    
    $('select[id="txtBranch"]').on('change', function() {
        var branchId = $(this).val();
        var branchName = $('select[id="txtBranch"] option:selected').text();
        if(branchId != "" && !branchName.includes("選択してください")){
           $("#hidBranchId").val(branchId); 
           LoadDepartmentByBranchId(branchId);
        }else{
           $("#hidBranchId").val(''); 
           BindDepartmentTextBox(deptList);
        }
   });
    
    $('select[id="txtCompanyType"]').on('change', function() {
        var typeName = $('select[id="txtCompanyType"] option:selected').text();
        var typeId = $(this).val();

        if(typeName == "大林組"){
             var companyId = "";
             $.each(company_info,function(key, item) {
                 if(item["name"] == "大林組"){
                     companyId = item["id"];
                     return;
                 }
             });
            $("#txtCompanyName").val("大林組");
            $("#hidCompanyId").val(companyId);
            $("#hidCompanyTypeId").val(typeId);
            $("#txtCompanyName").attr("disabled","disabled");
            $("#txtBranch").removeAttr("disabled");
            $("#txtDepartment").removeAttr("disabled");
        }else{
            if($("#hidCompanyId").val() != "")
                $("#txtCompanyName").val("");
            $("#hidCompanyTypeId").val(typeId);
            $("#txtCompanyName").removeAttr("disabled");
            if(!typeName.includes("選択して")){
                $("#txtBranch").attr("disabled","disabled");
                $("#txtDepartment").attr("disabled","disabled");
            }
 
            $('#txtBranch').val($('#txtBranch option:eq(0)').val()).trigger('change');
            //$("#txtBranch").val('');
            $("#txtDepartment").val("");
            $("#hidBranchId").val("");
            $("#hidDepartmentId").val("");
            CompanyFilterByType(typeId);
        }
    });
    
    $('#txtCompanyName').on('change', function() {
        var currentCompanyName = $(this).val();
        var isNotNew = false;
        $.each(company_info,function(key, item) {
             if(item["name"] == currentCompanyName){
                 isNotNew = true;
                 return;
             }
         });
        if(!isNotNew){
            $("#hidCompanyId").val('');
        } 
    });
    
    $('#txtChiefAdmin').on('change', function() {
        $("#hidChiefAdminId").val('');
    });
    
    if(window.location.href.includes("step1")){
       LoadCompanyType();    
       LoadCompanyData(); 
       
       var companyTypeId = $("#hidCompanyTypeId").val();
       var companyId = $("#hidCompanyId").val();
       var branchId = $("#hidBranchId").val();

       if(companyTypeId != ""){
           $('select[id="txtCompanyType"] option').each(function() {
               var id = $(this).val();
              if(id == companyTypeId){
                  $("#txtCompanyType").val(id).change();
                  return;
              }
           });
       }
       
       if(companyId != ""){
           var companyName = "";
             $.each(company_info,function(key, item) {
                 if(item["id"] == companyId){
                     companyName = item["name"];
                     return;
                 }
             });
          $("#txtCompanyName").val(companyName);
       }
       
        if(branchId != ""){
           $('select[id="txtBranch"] option').each(function() {
               var id = $(this).val();
              if(id == branchId){
                  $("#txtBranch").val(id).change();
                  return;
              }
           });
        }
    }
    
   
    if(window.location.href.includes("step2")){
        LoadChiefAdminData(); 
    }
    
});

function CompanyFilterByType(typeId){
    var companyListByType = [];
    $.each(company_info,function(index, item) {
        var comId = item["id"];
        var comName = item["name"];
        if(item["company_type_id"] == typeId){
            companyListByType.push({"label":comName,"value":comId});
            //companyListByType[comId] = comName;
        }
    });
    
    BindCompanyTextBox(companyListByType);
   
    
}

function HideAndShowPassword(){

    if($("#hideAndShowPass").find('i').hasClass("fa-eye")){//current is show,need to hide
        $('#txtPassword').attr('type', 'password');
        $("#hideAndShowPass").find('i').removeClass("fa-eye");
        $("#hideAndShowPass").find('i').addClass("fa-eye-slash")
    }else{

        $('#txtPassword').attr('type', 'text');
        $("#hideAndShowPass").find('i').removeClass("fa-eye-slash");
        $("#hideAndShowPass").find('i').addClass("fa-eye")
    }
}

function GoTo(str,status){

    if(str == "step1"){
        
    }else if(str == "step2" && status != undefined){
      var isValid =  ValidationForStep1();
      if(!isValid) return;
      Step1ToSession();
    }else if(str == "step3" && status != undefined){
      var isValid =  ValidationForStep2();
      if(!isValid) return;
      Step2ToSession();
    }
    window.location = "/iPD/login/create/"+str;
}

function CreateUserAccount(){
    var chk = $("#chkAccept").prop('checked');
    if(chk == false){
        var message = "※ 規約に同意するチェックを付けてください。"
        $("#err_message").html(message);
        return;
    }
    
    SaveLoginAccountInfo();
    //SendMailToChiefAdmin();
    
}

function SaveLoginAccountInfo(){
    
    $.ajax({
        url: "/iPD/login/account/save",
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"save_login_account_info"},
        success :function(data) {
            //console.log(data);return;
            if(data.includes("success")){
                alert("アカウント作成依頼が提出されました。\n管理者の承認後CCCにアクセス可能になります。。");
               window.location = "/iPD/login";
            }else if(data.includes("required")){
                $("#err_message").html("必要な情報を全て入力してください。")
                window.location="/iPD/login/create/step1";
            }
        },
        error:function(err){
            console.log(err);
        }
    });
}

function SendMailToChiefAdmin(){
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

function ValidationForStep1(){
    var message = "";
    if($("#txtFirstName").val().trim() == ""){
       return ShowErrorMessage("氏名：姓");
    }else if($("#txtLastName").val().trim() == ""){
       return  ShowErrorMessage("名");
    }else if($("#txtFirstNameKana").val().trim() == ""){
       return  ShowErrorMessage("氏名：姓（カナ）");
    }else if($("#txtLastNameKana").val().trim() == ""){
       return  ShowErrorMessage("名（カナ）");
    }else if($("#txtPassword").val().trim() == ""){
       return  ShowErrorMessage("パスワード");
    }else if($("#txtEmail").val().trim() == ""){
       return  ShowErrorMessage("メールアドレス");
    }else if($("#txtEmail").val().trim() != ""){
        var result = DuplicateEmailChecking($("#txtEmail").val());
        if(result != "" && result[0]['count'] >= 1){
           $("#err_message").html("メールアドレスが既に存在しています。\n別のメールアドレスを使ってください。");
            return  false; 
        }
    }else if($('select[id="txtCompanyType"] option:selected').text().includes("選択して")){
        $("#err_message").html("企業種別を選択してください。");
       return  false;
    }else if($("#txtCompanyName").val().trim() == ""){
       return  ShowErrorMessage("企業名");
    }else if($('select[id="txtCompanyType"] option:selected').text().includes("大林組") && $('select[id="txtBranch"] option:selected').text().includes("選択して")){
        $("#err_message").html("支店名を選択してください。");
       return  false;
    }else if($('select[id="txtCompanyType"] option:selected').text().includes("大林組") && $("#txtDepartment").val().trim() == ""){
        return  ShowErrorMessage("組織名");
    }
   
    return true;
    
    // else if($("#txtCompanyName").val().trim() != "大林組" && $("#hidCompanyId").val().trim() == ""){
    //     $("#err_message").html("企業名を選択してください。");
    //   return  false;
    // }
}

function DuplicateEmailChecking(email){
    var data = "";
    $.ajax({
        url: "/iPD/login/account/get",
        type: 'post',
        async:false,
        data:{_token: CSRF_TOKEN,message:"check_duplicate_email",mail:email},
        success :function(result) {
            console.log(result);
            data = result;
        },
        error:function(err){
            console.log(err);
        }
    });
    return data;
}

function ValidationForStep2(){
    
    if($("#txtChiefAdmin").val() == "" || $("#hidChiefAdminId").val() == ""){
        var message = "管理責任者を選択してください。";
        $("#err_message").html(message);
        return false;
    }
    return true;
}

function ShowErrorMessage(message){
    var fix_msg = "を入力してください。";
    $("#err_message").html(message+fix_msg);
    return false;
}

function Step1ToSession(){
    var firstName = $("#txtFirstName").val();
    var lastName = $("#txtLastName").val();
    var firstNameKana = $("#txtFirstNameKana").val();
    var lastNameKana = $("#txtLastNameKana").val();
    var password = $("#txtPassword").val();
    var email = $("#txtEmail").val();
    var companyTypeId = $("#hidCompanyTypeId").val();
    var companyType = $("#txtCompanyType").text();
    
    var companyId = $("#hidCompanyId").val();
    var company = $("#txtCompanyName").val();
    var branchId = $("#hidBranchId").val();
    var branch = $("#txtBranch").val();
    var departmentId = $("#hidDepartmentId").val();
    var department = $("#txtDepartment").val();
    var phone = $("#txtPhoneNumber").val();
    var workingPlace = $("#txtAWorkLocation").val();

    var accountStep1 ={"firstName":firstName,"lastName":lastName,"firstNameKana":firstNameKana,"lastNameKana":lastNameKana,
                        "password":password,"email":email,"companyType":companyType,"companyTypeId":companyTypeId,"company":company,"companyId":companyId,
                        "branchId":branchId,"departmentId":departmentId,"branch":branch,"department":department,"phone":phone,"workingPlace":workingPlace};
//alert(companyId);
    $.ajax({
        url: "/iPD/login/account/save",
        type: 'post',
        async:false,
        data:{_token: CSRF_TOKEN,message:"step1_to_session",step1Data:JSON.stringify(accountStep1)},
        success :function(data) {
            //alert(data);return;
            if(data.includes("success")){
                console.log("successfully saved that step1 data to session")
            }
        },
        error:function(err){
            console.log(err);
        }
    });
}

function Step2ToSession(){
    var chiefAdmin = $("#txtChiefAdmin").val();
    var chiefAdminId = $("#hidChiefAdminId").val();
    //alert(chiefAdminId+chiefAdmin);
    $.ajax({
        url: "/iPD/login/account/save",
        type: 'post',
        async:false,
        data:{_token: CSRF_TOKEN,message:"step2_to_session",chiefAdmin:chiefAdmin,chiefAdminId:chiefAdminId},
        success :function(data) {
            if(data.includes("success")){
               console.log("successfully saved that step2 data to session")
            }
        },
        error:function(err){
            console.log(err);
        }
    });
}

function LoadCompanyType() {
     $.ajax({
        url: "/iPD/login/account/get",
        type: 'post',
        async:false,
        data:{_token: CSRF_TOKEN,message:"get_company_type"},
        success :function(data) {
            console.log(data);
            if(data != ""){
                BindCompanyType(data);
            }
        },
        error:function(err){
            console.log(err);
        }
    });
}

function LoadDepartmentByBranchId(branchId) {

     $.ajax({
        url: "/iPD/login/account/get",
        type: 'post',
        async:false,
        data:{_token: CSRF_TOKEN,message:"get_department_by_branchId",branchId:branchId},
        success :function(result) {
            console.log(result);
            if(result != ""){
                var data = [];
                $.each(result,function(key, item) {
                    data.push({"label":item["name"],"value":item["id"]});
                });
                BindDepartmentTextBox(data);
            }
        },
        error:function(err){
            console.log(err);
        }
    });
}

function LoadCompanyData() {
     $.ajax({
        url: "/iPD/login/account/get",
        type: 'post',
        async:false,
        data:{_token: CSRF_TOKEN,message:"get_company_data"},
        success :function(data) {
            console.log(data);
            if(data != ""){
                company_info = data;
                BindCompanyData(data);
            }
        },
        error:function(err){
            console.log(err);
        }
    });
}

function LoadChiefAdminData() {
     $.ajax({
        url: "/iPD/login/account/get",
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"get_chief_admin_info"},
        success :function(data) {
            console.log(data);
            if(data != ""){
               BindChiefAdminTextBox(data);
            }
        },
        error:function(err){
            console.log(err);
        }
    });
}

function BindCompanyType(data){
    var placeholder = "選択してください。";
    var option = "<option>選択してください。</option>";
    $.each(data,function(key,item){
        option += "<option value="+item["id"]+">"+item["name"]+"</option>";
    });
    //$("#txtCompanyType option").remove();
    //$("#txtCompanyType").append(option);
    $("select#txtCompanyType option").remove();
    $("#txtCompanyType").append(option).select2({minimumResultsForSearch: Infinity});
}

function BindBranch(data){

    var option = "<option val=''>選択してください。</option>";
    $.each(data,function(key,item){
        if(item == undefined)return;
        option += "<option value="+key+">"+item+"</option>";
    });
    //$("#txtBranch option").remove();
    //$("#txtBranch").append(option);
    $("select#txtBranch option").remove();
    $("#txtBranch").append(option).select2({minimumResultsForSearch: Infinity});
    $('#txtBranch').maximizeSelect2Height();//make auto height of select2 without scroll y
}

function BindCompanyData(data){
    var companyList = [];
    var branchList = [];
    deptList = [];
    var companyName_arr = [];
    //var branchName_arr = [];
    var deptName_arr = [];
    $.each(data,function(index, item) {
        var comName = item["name"];
        var comId = item["id"];
        var branchName = item["branch_name"];
        var branchId = item["branch_id"];
        var deptName = item["dept_name"];
        var deptId = item["dept_id"];
        
        if(!companyName_arr.includes(comName) && comName != "" && comName != null){
            companyName_arr.push(comName);
            companyList.push({"label":comName,"value":comId});// companyList[comId] = comName;
        }
        if(!Object.values(branchList).includes(branchName) && branchName != "" && branchName != null){
            //branchName_arr.push(branchName);
            //branchList.push({"label":branchName,"value":branchId});
            branchList[branchId] = branchName;
        }
        if(!deptName_arr.includes(deptName) && deptName != "" && deptName != null){
            deptName_arr.push(deptName);
            deptList.push({"label":deptName,"value":deptId});
            //deptList[deptId] = deptName;
        }
    });
    BindCompanyTextBox(companyList);
    BindBranch(branchList);
    //BindBranchTextBox(branchList);
    BindDepartmentTextBox(deptList);

}

function BindCompanyTextBox(data){
     //   source: function (request, response) {
    //         response($.map(data, function (value, key) {
    //             if(value == undefined) return;
    //             return {
    //                 label: value,
    //                 value: key
    //             };
    //         }));
    //   },
     $( "#txtCompanyName" ).autocomplete({
       minLength:0,
       source:data,
       select: function(event, ui) {
			var selectedObj = ui.item;
			$(this).val(selectedObj.label);
			$("#hidCompanyId").val(selectedObj.value);
			return false;
		}
       
    }).bind('focus', function(){ $(this).autocomplete("search"); } );
}

function BindBranchTextBox(data){
   
    $( "#txtBranch" ).autocomplete({
      minLength:0,
      source:data,
      select: function(event, ui) {
			var selectedObj = ui.item;
			$(this).val(selectedObj.label);
			$("#hidBranchId").val(selectedObj.value);
			return false;
		}
       
    }).bind('focus', function(){ /*$(this).autocomplete("search");*/ } );
}

function BindDepartmentTextBox(data){
    $( "#txtDepartment" ).autocomplete({
       minLength:0,
       source: data,
       select: function(event, ui) {
			var selectedObj = ui.item;
			$(this).val(selectedObj.label);
			$("#hidDepartmentId").val(selectedObj.value);
			return false;
		}
       
    }).bind('focus', function(){ $(this).autocomplete("search"); } );
}

function BindChiefAdminTextBox(data) {
   var chiefAdmin_list = [];
   $.each(data,function(index,item){
       //if(!Object.values(chiefAdmin_list).includes(item['name']) && item["name"] != ""){
           var chiefId = item["id"];
           var chiefName = item["name"];
           if(item['position'] != ""){
               chiefName += "　"+item['position'];
           }
            
           if(item["department"] != ""){
               chiefName += "　"+item['department'];
           }    
           chiefAdmin_list.push({"label":chiefName,"value":chiefId});
           //chiefAdmin_list[chiefId] = item['name'];
      // }
       
   });
    $( "#txtChiefAdmin" ).autocomplete({
       minLength:0,
       source: chiefAdmin_list,
       select: function(event, ui) {
			var selectedObj = ui.item;
			$(this).val(selectedObj.label);
			$("#hidChiefAdminId").val(selectedObj.value);
			return false;
		}
    }).bind('focus', function(){ $(this).autocomplete("search"); } );
    
}