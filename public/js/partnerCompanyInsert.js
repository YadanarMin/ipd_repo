var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

$(document).ready(function () {
    $.ajaxSetup({
        cache:false
    });
    
    var login_user_id = $("#hidLoginID").val();
    var img_src = "../public/image/user_settings.png";
    var url = "partnerCompany/index";
    var content_name = "協力会社管理";
    recordAccessHistory(login_user_id,img_src,url,content_name);
    
    $("#insertBtn").addClass("disabledBtn");
    
    //Show in side bar table
    LoadAllPartnerCompany();
    //Show in select box
    GetAllStoreProjectName();
    getCompanyType();
    
    ShowSuggestedCompany();
    //ShowSuggestedJobType();
    //ShowSuggestedInCharge();
    
    
    $("#companyNameList tbody").on("click","tr", function(e){
         var id = $(this).find($('input[type=hidden]')).val();
         var name = $(this).text();
         $("#errorMsg").html("");
         $(this).addClass("selected").siblings().removeClass("selected");
         ShowSelectedCompany(id,name);
    });
    
    //Increase number of Incharge
    $("#numOfIncharge").change(function(){
        var index = parseInt($(this).val());
        //var count = $("#inchargeNameDiv input").length;
        var count = $("#personalInfo div.personalView").length;
        console.log(count)
        
        if(count<index){
            for(var i=count ; i<index ; i++){
                var personalDiv = "<div class='personalView' id='personalView" + (i+1) + "'><div><div class='form-group col-md-6' style='padding: 0'>"+
                                    "<label>担当者氏名：性</label>"+
                                    "<input type='text' class='form-control' name='firstName" + (i+1) + "' id='firstName"+ (i+1) + "'/>"+ 
                    
                                    "</div><div class='form-group col-md-6' style='padding-right: 0'>"+
                                    "<label>担当者名</label>"+
                                    "<input type='text' class='form-control' name='lastName" + (i+1) + "' id='lastName"+ (i+1) + "'/>"+ 
                    
                                    "</div></div>"+
                                    "<div class='form-group'>"+
                                    "<label>電話番号「携帯」</label>"+
                                    "<input type='text' class='form-control' name='phone" + (i+1) + "' id='phone"+ (i+1) + "'/>"+ 
                    
                                    "</div>"+
                                    "<div class='form-group'>"+
                                    "<label>電話番号「外線」</label>"+
                                    "<input type='text' class='form-control' name='outsideCall" + (i+1) + "' id='outsideCall"+ (i+1) + "'/>"+ 
                    
                                    "</div>"+
                                    "<div class='form-group'>"+
                                    "<label>メール</label>"+
                                    "<input type='text' class='form-control' name='email" + (i+1) + "' id='email"+ (i+1) + "'/>"+ 
                    
                                    "</div></div>";
                    
                //var inchargeName = "<input type='text' class='form-control' name='inchargeName"+ i + "' id='inchargeName" + i + "'/> ";
                $("#personalInfo").append(personalDiv);
            }
        }else{
            console.log(count)
            console.log(index);
            for(var i=index ; i<count ; i++){
            
                $("#personalView"+(i+1)).remove();
            }
        }
        
    })
});

function GetCompanyInfo(){
    var companyName = $("#companyName option:selected").text();
    if(companyName != "選択してください"){
        $.ajax({
            url: "../partnerCompany/getData",
            async:true,
            type: 'post',
            data:{_token: CSRF_TOKEN, message:"getCompanyInfoByName", companyName : companyName},
            success :function(data) {
                     if(data.length>0){
                         console.log(data)
                         ShowCompanyInfo(data[0]);
                     }else{
                         $("#jobType").val("");
                         $("#jobType").prop("disabled",false);
                     }
                    
            },
            error:function(err){
                console.log(err);
            }
        });
    }else{
        ShowErrMessage("会社名を選択してください。");
    }
}

function ShowCompanyInfo(data){
    console.log(data)
    $("#jobType").prop("disabled",true);
    $("#jobType").val(data['industry_type']);
    
}

function ShowErrMessage(msg){
    $("#errorMsg").html(msg);
}

//Suggested Data For Partner Company
function ShowSuggestedCompany(){
    var companyList = [];
    $.ajax({
        url: "../application/getData",
        async:true,
        type: 'post',
        data:{_token: CSRF_TOKEN,  message:"getCompanyList", companyTypeId : 3},
        success :function(data) {
            if(data){
                $.each(data, function(key,value){
                    $.each(value, function(tname, tdata){
                        if(!companyList.includes(tdata['name'])){
                            companyList.push(tdata['name']);
                        }
                        
                        
                    });
                    
                });
                
                // $( "#companyName" ).autocomplete({
                //   source: companyList,
                //   minLength:0
                // }).bind('focus', function(){ $(this).autocomplete("search"); } );
                
                createPartnerCompanySelectBox(companyList);
            }
        },
        error:function(err){
            console.log(err);
        }
    });
}

function createPartnerCompanySelectBox(companyList){
    console.log(companyList);
    var appendText = "";
    appendText += "<option value='0'>選択してください</option>";
            
    for(var i=0; i<companyList.length; i++){
            appendText += "<option value='" + companyList[i]  + "'>" +  companyList[i] + "</option>";
    }
        
    $("select#companyName option").remove();
    $("select#companyName").append(appendText);
    $("select#companyName").select2();
    
}

//Suggested Data For JobType
function ShowSuggestedJobType(){
    var jobTypeList = [];
    $.ajax({
        url: "../partnerCompany/getData",
        async:true,
        type: 'post',
        data:{_token: CSRF_TOKEN, message:"getAllPartnerCompany"},
        success :function(data) {
            if(data){
                $.each(data, function(key,value){
                    $.each(value, function(tname, tdata){
                        if(tdata['jobType']){
                            if(!jobTypeList.includes(tdata['jobType'])){
                                jobTypeList.push(tdata['jobType']);
                            }
                        }
                        
                        
                    });
                    
                });
                
                $( "#jobType" ).autocomplete({
                   source: jobTypeList,
                   minLength:0
                }).bind('focus', function(){ $(this).autocomplete("search"); } );
            }
        },
        error:function(err){
            console.log(err);
        }
    });
}

//Suggested Data For InchargeName
function ShowSuggestedInCharge(){
    var inChargeList = [];
    $.ajax({
        url: "../partnerCompany/getData",
        async:true,
        type: 'post',
        data:{_token: CSRF_TOKEN, message:"getAllPartnerCompany"},
        success :function(data) {
            if(data){
                $.each(data, function(key,value){
                    $.each(value, function(tname, tdata){
                        if(tdata['inchargeName']){
                            if(!inChargeList.includes(tdata['inchargeName'])){
                                inChargeList.push(tdata['inchargeName']);
                            }
                            
                        }
                        
                        
                    });
                    
                });
                
                $( "#inchargeName" ).autocomplete({
                   source: inChargeList,
                   minLength:0
                }).bind('focus', function(){ $(this).autocomplete("search"); } );
            }
        },
        error:function(err){
            console.log(err);
        }
    });
}

//Toggle Button
function InsertBtn(){
    window.location="../partnerCompany/index";
    
}

//Toggle Button
function ShowBtn(){
    $("#showBtn").addClass("disabledBtn");
    window.location='../partnerCompany/list';
}

//Get data for select box
function GetAllStoreProjectName() {
    $.ajax({
        url: "../document/getData",
        async:false,
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"get_allstore_name_pjid"},
        success :function(data) {
            //console.log(data);
            if(data){
                DisplayAllStoreProjectName(data);
            }
        },
        error:function(err){
            console.log(err);
        }
    });

}

function DisplayAllStoreProjectName(data) {
    var option="<option value=''> </option>";
    $.each(data,function(key,value){
        option += "<option value='" + value['project_name'] +"'>"+value['project_name']+"</option>";
    });
    $("#satelliteProjName").append(option);
    $(".satelliteProjName").select2();
}

function insertPartnerCompany(){
    var validate;
    var numOfIncharge = parseInt($("#numOfIncharge").val());
    
    //**********Validation[Start]**************//
    if($("#companyName option:selected").text() != "選択してください"){
        validate = true;
    }else{
        ShowErrMessage("会社名を選択してください。");
        return;
    }
    
    // for(var i=1 ; i<=numOfIncharge ; i++){
    //     if($("#firstName"+i).val()){
    //         validate = true;
    //     }else{
    //         ShowErrMessage("氏名：性を入力してください。");
    //         return;
    //     }
        
    //     if($("#lastName"+i).val()){
    //         validate = true;
    //     }else{
    //         ShowErrMessage("名を入力してください。");
    //         return;
    //     }
        
    //     if($("#email"+i).val()){
    //         validate = true;
    //     }else{
    //         ShowErrMessage("メール入力してください。");
    //         return;
    //     }
    // }
    //**********Validation[end]**************//
    
    if(validate){
        var idList = [];
        var firstNameList = [];
        var lastNameList = [];
        var phoneList = [];
        var outSideCallList = [];
        var mailList = [];
        var companyName = $("#companyName option:selected").text();
        var jobType = $("form #jobType").val();
        
        //SelectBox
        var yaruki = ($("form #yaruki").val() == "選択してください。") ? " " : $("form #yaruki").val() ;
        var revit = ($("form #revit").val() == "選択してください。") ? " " : $("form #revit").val() ;
        var ipd = ($("form #ipd").val() == "選択してください。") ? " " : $("form #ipd").val() ;
        var satelliteExp = ($("form #satelliteExp").val() == "選択してください。") ? " " : $("form #satelliteExp").val() ;
        var satelliteProjName = ($("form #satelliteProjName").val() == "選択してください。") ? " " : $("form #satelliteProjName").val() ;
        //SelectBox
        var remark = $("form #remark").val();
        
        //**********担当者**********//
        for(var i=1 ; i<= numOfIncharge ; i++){
            idList.push(i);
            firstNameList.push($("#firstName"+i).val());
            lastNameList.push($("#lastName"+i).val());
            phoneList.push($("#phone"+i).val());
            outSideCallList.push($("#outsideCall" +i).val());
            mailList.push($("#email"+i).val());
        }
    
        var inChargeInfoList =  idList.map((id, index) => {
          return {
            first_name: firstNameList[index],
            last_name: lastNameList[index],
            phone: phoneList[index],
            outsideCall: outSideCallList[index],
            mail: mailList[index]
          }
        });
    
        var partnerCompanyInfo = {
                'companyName' : companyName,
                'jobType'     : jobType,
                'yaruki'     : yaruki,
                'revit'     : revit,
                'ipd'     : ipd,
                'satelliteExp'     : satelliteExp,
                'satelliteProjName'     : satelliteProjName,
                'remark'     : remark,
                'inchargeInfo' : inChargeInfoList
                
            }
    
        console.log(partnerCompanyInfo);
        $.ajax({
                url: "../partnerCompany/saveData",
                async:true,
                type: 'post',
                data:{_token: CSRF_TOKEN, message:"insertPartnerCompany", partnerCompanyInfo : partnerCompanyInfo},
                success :function(data) {
                    console.log(data);
                    
                        if(data){
                            alert("情報入力しました。");
                            location.reload()
                            
                        }
                },
                error:function(err){
                        console.log("err" +JSON.stringify(err));
                        alert("情報入力に失敗しました。\n管理者に問い合わせてください。");
                    }
                }); 
        
    }
    //         $.ajax({
    //                 url: "../partnerCompany/saveData",
    //                 async:true,
    //                 type: 'post',
    //                 data:{_token: CSRF_TOKEN, message:"insertPartnerCompany", partnerCompanyInfo : partnerCompanyInfo},
    //                 success :function(data) {
    //                     if(data){
    //                         alert("情報入力しました。");
    //                         location.reload()
                            
    //                     }
    //                 },
    //                 error:function(err){
    //                     console.log("err" +JSON.stringify(err));
    //                     alert("情報入力に失敗しました。\n管理者に問い合わせてください。");
    //                 }
    //             }); 
    // }
}

function updatePartnerCompany(){
    var validate;
    var numOfIncharge = parseInt($("#numOfIncharge").val());
    var id = $("#companyId").val();
    
    //**********Validation[Start]**************//
    if(id){
        validate = true;
    }else{
        ShowErrMessage("変更したい協力会社を選択してください。");
        return;
    }
    
    
    if(validate){
        var idList = [];
        var firstNameList = [];
        var lastNameList = [];
        var phoneList = [];
        var outSideCallList = [];
        var mailList = [];
        var companyName = $("#companyName option:selected").text();
        var jobType = $("form #jobType").val();
        
        //SelectBox
        var yaruki = ($("form #yaruki").val() == "選択してください。") ? " " : $("form #yaruki").val() ;
        var revit = ($("form #revit").val() == "選択してください。") ? " " : $("form #revit").val() ;
        var ipd = ($("form #ipd").val() == "選択してください。") ? " " : $("form #ipd").val() ;
        var satelliteExp = ($("form #satelliteExp").val() == "選択してください。") ? " " : $("form #satelliteExp").val() ;
        var satelliteProjName = ($("form #satelliteProjName").val() == "選択してください。") ? " " : $("form #satelliteProjName").val() ;
        //SelectBox
        var remark = $("form #remark").val();
        
        //**********担当者**********//
        for(var i=1 ; i<= numOfIncharge ; i++){
            idList.push(i);
            firstNameList.push($("#firstName"+i).val());
            lastNameList.push($("#lastName"+i).val());
            phoneList.push($("#phone"+i).val());
            outSideCallList.push($("#outsideCall" +i).val());
            mailList.push($("#email"+i).val());
        }
    
        var inChargeInfoList =  idList.map((id, index) => {
          return {
            first_name: firstNameList[index],
            last_name: lastNameList[index],
            phone: phoneList[index],
            outsideCall: outSideCallList[index],
            mail: mailList[index]
          }
        });
    
        var partnerCompanyInfo = {
                'companyName' : companyName,
                'jobType'     : jobType,
                'yaruki'     : yaruki,
                'revit'     : revit,
                'ipd'     : ipd,
                'satelliteExp'     : satelliteExp,
                'satelliteProjName'     : satelliteProjName,
                'remark'     : remark,
                'inchargeInfo' : inChargeInfoList
                
            }
    
        console.log(partnerCompanyInfo);
        $.ajax({
                url: "../partnerCompany/saveData",
                async:true,
                type: 'post',
                data:{_token: CSRF_TOKEN, message:"insertPartnerCompany", partnerCompanyInfo : partnerCompanyInfo},
                success :function(data) {
                    console.log(data);
                        if(data){
                            alert("情報変更しました。");
                            location.reload()
                            
                        }
                },
                error:function(err){
                        console.log("err" +JSON.stringify(err));
                        alert("情報入力に失敗しました。\n管理者に問い合わせてください。");
                    }
                }); 
        
    }

}

function LoadAllPartnerCompany(){
    
    $.ajax({
        url: "../partnerCompany/getData",
        async:true,
        type: 'post',
        data:{_token: CSRF_TOKEN, message:"getAllPartnerCompany"},
        success :function(data) {
            console.log("AllPartnerCompany" + JSON.stringify(data,null,4));
            if(data){
                ShowPartnerCompanyList(data);
                
            }
        },
        error:function(err){
            console.log(err);
            alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });
}

function ShowPartnerCompanyList(data) {
    
    $.each(data, function(key,value){
        $.each(value, function(tname, tdata){
            var row =  "<tr><td>" + tdata['name']  +  "<input type='hidden' value='" + tdata['id'] + "'/></td></tr>";
            $("#companyNameList tbody").append(row);
        });
        
    });
}

function ShowSelectedCompany(id,name){
    $.ajax({
        url: "../partnerCompany/getData",
        async:true,
        type: 'post',
        data:{_token: CSRF_TOKEN, message:"getPartnerCompanyById", id : id},
        success :function(data) {
            console.log(data);
            if(data[0].length>0){
                ShowPartnerCompanyFormDataById(data[0]);
            }else{
                console.log("No data");
                ClearFormItems();
                $("#companyId").val(id);
                $("form #companyName").val(name).trigger("change");
                $("form #companyName").prop("disabled", true);
                $("form #firstName1").prop("disabled", false);
                $("form #lastName1").prop("disabled", false);
                $("form #phone1").prop("disabled", false);
                $("form #outsideCall1").prop("disabled", false);
                $("form #email1").prop("disabled", false);
                $("#savePartnerCompany").prop("disabled",true);
                $("form #insertNewCompany").prop("onclick", null);
                $("form #updateInsertedCompany").prop("onclick", null);
                
            }
        },
        error:function(err){
            console.log(err);
            alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });
}

function ShowPartnerCompanyFormDataById(data){
    // $.each(data, function(key,value){
    //     $.each(value, function(tname, tdata) {
    //         var id = tdata["id"];
    //         var companyName = tdata["companyName"];
    //         var jobType = tdata["jobType"];
    //         var yaruki = tdata["yaruki"];
    //         var revit = tdata["revit"];
    //         var ipd = tdata["ipd"];
    //         var satelliteExp = tdata["satelliteExp"];
    //         var satelliteProjName = tdata["satelliteProjName"];
    //         var remark = tdata["remark"];
    //         var inchargeName = tdata["inchargeName"];
    //         var phone = tdata["phone"];
    //         var email = tdata["email"];
            
    //         ClearFormItems();
            
    //         $("#companyId").val(id);
    //         $("form #companyName").val(companyName);
    //         $("form #jobType").val(jobType);
    //         $("form #yaruki").val(yaruki);
    //         $("form #revit").val(revit);
    //         $("form #ipd").val(ipd);
    //         $("form #satelliteExp").val(satelliteExp);
    //         $("form #satelliteProjName").val(satelliteProjName);
    //         $("form #remark").val(remark);
    //         $("form #inchargeName").val(inchargeName);
    //         $("form #phone").val(phone);
    //         $("form #email").val(email);
    //     });
    // });
        var defaultStr = "選択してください。";
        
        var id = data[0]["company_id"];
        var companyName = data[0]["name"];
        var industry_type = data[0]["industry_type"];
        var yaruki = (data[0]["yaruki"] == "NULL")? defaultStr :  data[0]["yaruki"];
        var revit = (data[0]["revit"] == "NULL") ?  defaultStr : data[0]["revit"];
        var ipd = (data[0]["iPDStudent"] == "NULL") ?  defaultStr :data[0]["iPDStudent"];
        var satelliteExp =(data[0]["satelliteExp"] == "NULL") ?  defaultStr : data[0]["satelliteExp"];
        var satelliteName = (data[0]["satelliteName"] == "NULL" ) ?  defaultStr : data[0]["satelliteName"];
        var remark = (data[0]["remark"] == "NULL" ) ?  " " : data[0]["remark"]; 
        
        
        var first_name = data[0]["first_name"];
        var last_name = data[0]["last_name"];
        var phone = data[0]["phone"];
        var outsideCall = data[0]["outsideCall"];
        var mail = data[0]["mail"];
        
        ClearFormItems();
            
        $("#companyId").val(id);
        $("form #companyName").val(companyName).trigger('change');
        $("form #companyName").prop("disabled", true);
        $("form #insertNewCompany").prop("onclick", null);
        $("form #updateInsertedCompany").prop("onclick", null);
        $("form #jobType").val(industry_type);
        $("form #jobType").prop("disabled", true);
        $("form #yaruki").val(yaruki);
        $("form #revit").val(revit);
        $("form #ipd").val(ipd);
        $("form #satelliteExp").val(satelliteExp);
        $("form .satelliteProjName").val(satelliteName).trigger('change');
        $("form #remark").val(remark);
        
        if(first_name && mail){
            $("form #firstName1").val(first_name);
            $("form #firstName1").prop("disabled", true);
            $("form #lastName1").val(last_name);
            $("form #lastName1").prop("disabled", true);
            $("form #phone1").val(phone);
            $("form #phone1").prop("disabled", true);
            $("form #outsideCall1").val(outsideCall);
            $("form #outsideCall1").prop("disabled", true);
            $("form #email1").val(mail);
            $("form #email1").prop("disabled", true);
        }else{
            $("form #firstName1").prop("disabled", false);
            $("form #lastName1").prop("disabled", false);
            $("form #phone1").prop("disabled", false);
            $("form #outsideCall1").prop("disabled", false);
            $("form #email1").prop("disabled", false);
        }
        
        //More than one incharge
        if(data.length >1 ){
            
            $("#numOfIncharge").val(data.length);
            $("#numOfIncharge").attr("min", data.length);
            for(var i=1; i<data.length ; i++){
             var personalDiv = "<div class='personalView' id='personalView" + (i+1) + "'><div><div class='form-group col-md-6' style='padding: 0'>"+
                                    "<label>担当者氏名：性</label>"+
                                    "<input type='text' class='form-control' name='firstName" + (i+1) + "' id='firstName"+ (i+1) + "' value='" + data[i]["first_name"] + "' disabled/>"+ 
                    
                                    "</div><div class='form-group col-md-6' style='padding-right: 0'>"+
                                    "<label>担当者名</label>"+
                                    "<input type='text' class='form-control' name='lastName" + (i+1) + "' id='lastName"+ (i+1) + "' value='" + data[i]["last_name"] + "' disabled/>"+ 
                    
                                    "</div></div>"+
                                    "<div class='form-group'>"+
                                    "<label>電話番号「携帯」</label>"+
                                    "<input type='text' class='form-control' name='phone" + (i+1) + "' id='phone"+ (i+1) + "' value='" + data[i]["phone"]  +  "' disabled/>"+ 
                    
                                    "</div>"+
                                    "<div class='form-group'>"+
                                    "<label>電話番号「外線」</label>"+
                                    "<input type='text' class='form-control' name='outsideCall" + (i+1) + "' id='outsideCall"+ (i+1) + "' value='" +  data[i]["outsideCall"] + "' disabled/>"+ 
                    
                                    "</div>"+
                                    "<div class='form-group'>"+
                                    "<label>メール</label>"+
                                    "<input type='text' class='form-control' name='email" + (i+1) + "' id='email"+ (i+1) + "' value='" + data[i]["mail"] + "' disabled/>"+ 
                    
                                    "</div></div>";
                $("#personalInfo").append(personalDiv);
            }
        }
        
        $("#savePartnerCompany").prop("disabled",true);
}

function deleteCompany(){
    var id = $("#companyId").val();
    console.log("ID" +id);
    if(id){
        var confirmResult = confirm("この協力会社を削除してよろしいですか。");
        if(confirmResult){
             $.ajax({
                url: "../partnerCompany/deleteData",
                async:true,
                type: 'post',
                data:{_token: CSRF_TOKEN, message:"deletePartnerCompanyById", id : id},
                success :function(data) {
                            if(data){
                                　alert("削除しました！");
                                  window.location.reload();
                                }
                            },
                            error:function(err){
                                console.log(err);
                                alert("情報削除に失敗しました。\n管理者に問い合わせてください。");
                            }
            });
        }else{
            
        }
        
         
    }else{
        alert("削除するデータはありません！\n 削除したい協力会社を選択してください。")
    }
}

function updateCompany(){
    var id = $("#companyId").val();
    var companyName = $("#companyName option:selected").text();
    var jobType = $("form #jobType").val();
    var yaruki = $("form #yaruki").val();
    var revit = $("form #revit").val();
    var ipd = $("form #ipd").val();
    var satelliteExp = $("form #satelliteExp").val();
    var satelliteProjName = $("form #satelliteProjName").val();
    var remark = $("form #remark").val();
    var inchargeName = $("form #inchargeName").val();
    var phone = $("form #phone").val();
    var email = $("form #email").val();
  
    var partnerCompanyInfo = {
        'companyName' : companyName,
        'jobType'     : jobType,
        'yaruki'     : yaruki,
        'revit'     : revit,
        'ipd'     : ipd,
        'satelliteExp'     : satelliteExp,
        'satelliteProjName'     : satelliteProjName,
        'remark'     : remark,
        'inchargeName'     : inchargeName,
        'phone'     : phone,
        'email'     : email
        
    }
    if(id){
            $.ajax({
                    url: "../partnerCompany/saveData",
                    async:true,
                    type: 'post',
                    data:{_token: CSRF_TOKEN, message:"updateById", partnerCompanyInfo : partnerCompanyInfo,id : id},
                    success :function(data) {
                        if(data){
                            alert("情報編集しました。");
                            location.reload()
                            
                        }
                    },
                    error:function(err){
                        console.log("err" +JSON.stringify(err));
                        alert("情報編集に失敗しました。\n管理者に問い合わせてください。");
                    }
                }); 
    }else{
        alert("編集するデータはありません！");
    }
    
}

function createNewCompany(){
    
    ClearFormItems();
    $("#companyNameList tbody tr").removeClass("selected");
    $("form #companyName").prop("disabled", false);
    $("form #companyName").focus();
    $("form #companyName").val(0).trigger('change');
    document.getElementById('insertNewCompany').onclick = DisplayPopup;
    document.getElementById('updateInsertedCompany').onclick = ShowSuggestedCompany;
    // $("form #insertNewCompany").on("click", DisplayPopup());
    // $("form #updateInsertedCompany").on("click", ShowSuggestedCompany());
    $("form #firstName1").prop("disabled", false);
    $("form #lastName1").prop("disabled", false);
    $("form #phone1").prop("disabled", false);
    $("form #outsideCall1").prop("disabled", false);
    $("form #email1").prop("disabled", false);
    $("#savePartnerCompany").prop("disabled", false);
    
}

function ClearFormItems(){
            $("#companyId").val("");
            $("form #companyName").val(0);
            $("form #jobType").val("");
            $("form #yaruki").val("選択してください。");
            $("form #revit").val("選択してください。");
            $("form #ipd").val("選択してください。");
            $("form #satelliteExp").val("選択してください。");
            $("form #satelliteProjName").val("選択してください。").trigger("change");
            $("form #remark").val("");
            $("#numOfIncharge").val(1);
            var count = $("#personalInfo div.personalView").length;
            for(var i= count; i>1 ; i--){
                $("#personalView"+i).remove();
            }
            $("form #firstName1").val("");
            $("form #lastName1").val("");
            $("form #phone1").val("");
            $("form #outsideCall1").val("");
            $("form #email1").val("");
}

// Create New Company PopUp
function DisplayPopup(){
    $("#createUser").css({ visibility: "visible",opacity: "1"});
}

function ClosePopup(){
    $("#createUser").css({ visibility: "hidden",opacity: "0"});
}

function getCompanyType(){
    $.ajax({
        url: "../company/getType",
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"getAllCompanyType",typeId:0,typeName:""},
        success :function(result) {
            console.log(result);
            createCompanyTypeSelect(result);
        },
        error:function(err){
            console.log(err);
        }
    });
}

function createCompanyTypeSelect(typeData){
        var appendText = "";
        appendText += "<option value='0'>選択してください</option>";
            
        for(var i=0; i<typeData.length; i++){
            appendText += "<option value='" + typeData[i]["id"] + "'>" + typeData[i]["name"] + "</option>";
        }
        
        $("select#companyType option").remove();
        $("select#companyType").append(appendText);
        
}

function CreateCompany(){
    var isValid =  Validataion();
    if(!isValid) return;
    var form = $(document.forms["createCompanyForm"]).serializeArray();
    console.log("Test")
    console.log(form);
    $.ajax({
        url: "../company/saveData",
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"singleCompany",companyData:form},
        success :function(message) {
            
           if(message.includes("success")){
               alert("successfully saved!");
               $("#createUser").css({ visibility: "hidden",opacity: "0"});
           }
           
        },
        error:function(err){
            alert(JSON.stringify(err));
            console.log(err);
        }
    });  
}

function Validataion(){
    if($("#txtName").val() == ""){
        $("#txtName_err").html("企業名を入力してください。");
        return false;
    }else if($('#companyType :selected').text() == '' || $('#companyType :selected').text() == '選択してください'){
        $("#companyTypeSelect_err").html("権限を選択してください。");
        return false;
    }else{
        return true;
    }
}