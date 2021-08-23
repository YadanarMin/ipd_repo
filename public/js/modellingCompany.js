var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
var g_isNewCreate = false;
$(document).ready(function() {
    $.ajaxSetup({
        cache: false
    });

    var login_user_id = $("#hidLoginID").val();
    var img_src = "../public/image/user_settings.png";
    var url = "modellingCompany/index";
    var content_name = "モデリング会社管理";
    recordAccessHistory(login_user_id, img_src, url, content_name);

    $("#insertBtn1").addClass("disabledBtn");

    //Show in side bar table
    LoadAllModellingCompany();
    getCompanyType();

    //Show data in companyname selectbox
    ShowSuggestedCompany();
    //ShowSuggestedPartnerJobtype();
    //ShowSuggestedPartnerInchargeName();

    $("#companyTypeSelect").change(function() {

        if ($(this).text() !== "" || $(this).text() !== "選択してください") {
            $("#companyTypeSelect_err").html('');
        }
    });

    $("#txtBranch").on("keyup", function(event) {
        $("#txtCode").val("");
        $("#txtPostalCode").val("");
        $("#txtAddress").val("");
        $("#txtCode").prop("disabled", false);
        $("#txtPostalCode").prop("disabled", false);
        $("#txtAddress").prop("disabled", false);
    });


    $("#partnerCompanyNameList tbody").on("click", "tr", function(e) {
        //var id = $(this).find($('input[type=hidden]')).val();
        var id = $(this).find($('.hidId')).val();
        var branch = $(this).find($('.hidBranch')).val();
        var name = $(this).text();
        console.log(id)
        console.log(branch)
        $("#errorMsg").html("");
        $(this).addClass("selected").siblings().removeClass("selected");
        ShowSelectedModellingCompany(id, name, branch);
    });

    //Increase number of Incharge
    $("#numOfIncharge").change(function() {
        var index = parseInt($(this).val());
        //var count = $("#inchargeNameDiv input").length;
        var count = $("#personalInfo div.personalView").length;
        console.log(count)

        if (count < index) {
            for (var i = count; i < index; i++) {
                var personalDiv = "<div class='personalView' id='personalView" + (i + 1) + "'><div><div class='form-group col-md-6' style='padding: 0'>" +
                    "<label>担当者氏名：性</label>" +
                    "<input type='text' class='form-control' name='firstName" + (i + 1) + "' id='firstName" + (i + 1) + "'/>" +

                    "</div><div class='form-group col-md-6' style='padding-right: 0'>" +
                    "<label>担当者名</label>" +
                    "<input type='text' class='form-control' name='lastName" + (i + 1) + "' id='lastName" + (i + 1) + "'/>" +

                    "</div></div>" +
                    "<div class='form-group'>" +
                    "<label>電話番号「携帯」</label>" +
                    "<input type='text' class='form-control' name='phone" + (i + 1) + "' id='phone" + (i + 1) + "'/>" +

                    "</div>" +
                    "<div class='form-group'>" +
                    "<label>電話番号「外線」</label>" +
                    "<input type='text' class='form-control' name='outsideCall" + (i + 1) + "' id='outsideCall" + (i + 1) + "'/>" +

                    "</div>" +
                    "<div class='form-group'>" +
                    "<label>メール</label>" +
                    "<input type='text' class='form-control' name='email" + (i + 1) + "' id='email" + (i + 1) + "'/>" +

                    "</div></div>";

                //var inchargeName = "<input type='text' class='form-control' name='inchargeName"+ i + "' id='inchargeName" + i + "'/> ";
                $("#personalInfo").append(personalDiv);
            }
        }
        else {
            console.log(count)
            console.log(index);
            for (var i = index; i < count; i++) {

                $("#personalView" + (i + 1)).remove();
            }
        }

    })

});

//**********会社名から情報取得*************//
function GetCompanyInfo() {
    var companyName = $("#companyName option:selected").text();
    if (companyName != "選択してください") {
        $.ajax({
            url: "../modellingCompany/getData",
            async: true,
            type: 'post',
            data: { _token: CSRF_TOKEN, message: "getModellingCompanyInfoByName", companyName: companyName },
            success: function(data) {
                if (data.length > 0) {
                    ShowCompanyInfo(data[0]);
                }

            },
            error: function(err) {
                console.log(err);
            }
        });
    }
    else {
        ShowErrMessage("会社名を選択してください。");
    }
}

function ShowCompanyInfo(data) {
    console.log("Show Company Info");
    console.log(data)
    $("#hidCompanyId").val(data['id'])
    $("#jobType").prop("disabled", true);
    $("#jobType").val(data['industry_type']);
    $("#branch").prop("disabled", true);
    $("#branch").val(data['name']);
    $("#postalCode").prop("disabled", true);
    $("#postalCode").val(data['postal_code']);
    $("#address").prop("disabled", true);
    $("#address").val(data['address']);

}

function ShowErrMessage(msg) {
    $("#errorMsg").html(msg);
}
//**********会社名から情報取得*************//

//Toggle Button
function InsertBtn() {
    window.location = "../modellingCompany/index";

}

//Toggle Button
function ShowBtn() {
    $("#showBtn").addClass("disabledBtn");
    window.location = '../modellingCompany/list';
}

//********Data in company name select box ***************//
function ShowSuggestedCompany() {
    var companyList = [];
    $.ajax({
        url: "../modellingCompany/getData",
        async: true,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "getModellingCompanyList", companyTypeId: 4 },
        success: function(data) {
            console.log(data)
            if (data) {
                createModellingCompanySelectBox(data);
                // $( "#companyName" ).autocomplete({
                //   source: companyList,
                //   minLength:0
                // }).bind('focus', function(){ $(this).autocomplete("search"); } );


            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function createModellingCompanySelectBox(companyList) {
    var checkbranch = [];
    var counts = {};
    var appendText = "";
    appendText += "<option value='0'>選択してください</option>";

    for (var i = 0; i < companyList.length; i++) {
        checkbranch.push(companyList[i]['name']);
    }
    for (const num of checkbranch) {
        counts[num] = counts[num] ? counts[num] + 1 : 1;
    }

    for (var i = 0; i < companyList.length; i++) {
        if (counts[companyList[i]['name']] > 1) {
            appendText += "<option value='" + companyList[i]['name'] + "'>" + companyList[i]['name'] + "【" + companyList[i]['branch'] + "】"　　 + "</option>";
        }
        else {
            appendText += "<option value='" + companyList[i]['name'] + "'>" + companyList[i]['name'] + "</option>";
        }
    }

    $("select#companyName option").remove();
    $("select#companyName").append(appendText);
    $("select#companyName").select2();

}

//********Data in company name select box ***************//


//********Company list in side bar ***************//
function LoadAllModellingCompany() {
    $.ajax({
        url: "../modellingCompany/getData",
        async: true,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "getAllModellingCompany" },
        success: function(data) {
            if (data) {
                ShowModellingCompanyList(data);
            }
        },
        error: function(err) {
            console.log(err);
            alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });
}

function ShowModellingCompanyList(companyList) {
    console.log("start")
    console.log(companyList)
    var checkbranch = [];
    var counts = {};
    var checkDupicate = [];
    var row = "";
    for (var i = 0; i < companyList.length; i++) {
        checkbranch.push(companyList[i]['name']);
    }
    console.log(checkbranch)
    for (const num of checkbranch) {
        counts[num] = counts[num] ? counts[num] + 1 : 1;
    }

    console.log(counts)

    for (var i = 0; i < companyList.length; i++) {
        if (counts[companyList[i]['name']] > 1) {
            var index = companyList[i]['name'] + "【" + companyList[i]['branch'] + "】";
            if (!checkDupicate.includes(index)) {
                checkDupicate.push(index);
                row += "<tr><td>" + index + "<input type='hidden' class='hidId'  value='" + companyList[i]['company_id'] + "'/><input type='hidden' class='hidBranch' value='" + companyList[i]['branch'] + "'></td></tr>";
            }
        }
        else {
            row += "<tr><td>" + companyList[i]['name'] + "<input type='hidden'  class='hidId' value='" + companyList[i]['company_id'] + "'/><input type='hidden' class='hidBranch' value='" + companyList[i]['branch'] + "'></td></tr>";
        }
    }
    $("#partnerCompanyNameList tbody").append(row);

    // $.each(data, function(key, value) {
    //     $.each(value, function(tname, tdata) {
    //         if (duplicateCompanyList.includes(tdata['name'])) {
    //             tdata['name'] = tdata['name'] + "【" + tdata['branch'] + "】";
    //         }
    //         else {
    //             duplicateCompanyList.push(tdata['name']);
    //         }
    //         var row = "<tr><td>" + tdata['name'] + "<input type='hidden' value='" + tdata['company_id'] + "'/></td></tr>";
    //         $("#partnerCompanyNameList tbody").append(row);
    //     });

    // });
}

//********Company list in side bar ***************//

//********Show selected data in form view ***************//
function ShowSelectedModellingCompany(id, name, branch) {
    $.ajax({
        url: "../modellingCompany/getData",
        async: true,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "getModellingCompanyByIdTest", id: id, companyName: name, branch: branch },
        success: function(data) {
            console.log(data)
            if (data[0].length > 0) {
                ShowModellingCompanyFormDataById(data[0]);
            }
            else {
                console.log("No data");
                ClearFormItems();
                $("#isPartnerCompany").prop("checked", false)
                $("#companyId").val(id);
                // $("form #companyName").val(name).trigger("change");
                // $("form #companyName").prop("disabled", true);
                $("form #txtCompanyName").val(name);
                $("form #txtCompanyName").prop("disabled", true);
                $("form #txtCompanyName").show();
                $("form #cName").hide();
                $("form #firstName1").prop("disabled", false);
                $("form #lastName1").prop("disabled", false);
                $("form #phone1").prop("disabled", false);
                $("form #outsideCall1").prop("disabled", false);
                $("form #email1").prop("disabled", false);
                $("#saveModellingCompany").prop("disabled", true);
                $("form #insertNewCompany").prop("onclick", null);
                $("form #updateInsertedCompany").prop("onclick", null);
            }
        },
        error: function(err) {
            console.log(err);
            alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });
}

function ShowModellingCompanyFormDataById(data) {
    // $.each(data, function(key,value){
    //     $.each(value, function(tname, tdata) {
    //         var id = tdata["id"];
    //         var partnerCompanyName = tdata["partnerCompanyName"];
    //         var partnerJobType = tdata["partnerJobType"];
    //         var partnerCompanyBranch = tdata["partnerCompanyBranch"];
    //         var partnerMailCode = tdata["partnerMailCode"];
    //         var partnerCompanyAddress = tdata["partnerCompanyAddress"];
    //         var partnerInchargeName = tdata["partnerInchargeName"];
    //         var partnerPhone = tdata["partnerPhone"];
    //         var partnerEmail = tdata["partnerEmail"];

    //         ClearFormItems();

    //         $("#partnerCompanyId").val(id);
    //         $("form #partnerCompanyName").val(partnerCompanyName);
    //         $("form #partnerJobType").val(partnerJobType);
    //         $("form #partnerCompanyBranch").val(partnerCompanyBranch);
    //         $("form #partnerMailCode").val(partnerMailCode);
    //         $("form #partnerCompanyAddress").val(partnerCompanyAddress);
    //         $("form #partnerInchargeName").val(partnerInchargeName);
    //         $("form #partnerPhone").val(partnerPhone);
    //         $("form #partnerEmail").val(partnerEmail);

    //     });
    // });
    var defaultStr = "選択してください。";

    var id = data[0]["id"];
    var companyName = data[0]["company_name"].trim();
    var industry_type = data[0]["industry_type"];
    var postal_code = data[0]['postal_code'];
    var address = data[0]['address'];
    var branch = data[0]["name"];
    var isPartnerCompany = data[0]['isPartnerCompany'];

    var first_name = data[0]["first_name"];
    var last_name = data[0]["last_name"];
    var phone = data[0]["phone"];
    var outsideCall = data[0]["outsideCall"];
    var mail = data[0]["mail"];

    ClearFormItems();

    $("#companyId").val(id);
    // $("form #companyName").val(companyName).trigger('change');
    // $("form #companyName").prop("disabled", true);
    $("form #txtCompanyName").val(companyName);
    $("form #txtCompanyName").prop("disabled", true);
    $("form #txtCompanyName").show();
    $("form #cName").hide();
    $("form #insertNewCompany").attr("onclick", null);
    $("form #updateInsertedCompany").attr("onclick", null);
    $("form #getCompanyInfo").prop("disabled", true);
    $("form #jobType").val(industry_type);
    $("form #jobType").prop("disabled", true);

    $("form #postalCode").val(postal_code);
    $("form #postalCode").prop("disabled", true);
    $("form #address").val(address);
    $("form #address").prop("disabled", true);

    $("form #branch").val(branch);
    $("form #branch").prop("disabled", true);
    if (isPartnerCompany) {
        $("#isPartnerCompany").prop("checked", true);
    }
    else {
        $("#isPartnerCompany").prop("checked", false);
    }

    if (first_name && mail) {
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
    }
    else {
        $("form #firstName1").prop("disabled", false);
        $("form #lastName1").prop("disabled", false);
        $("form #phone1").prop("disabled", false);
        $("form #outsideCall1").prop("disabled", false);
        $("form #email1").prop("disabled", false);
    }

    //More than one incharge
    if (data.length > 1) {

        $("#numOfIncharge").val(data.length);
        $("#numOfIncharge").attr("min", data.length);
        for (var i = 1; i < data.length; i++) {
            var personalDiv = "<div class='personalView' id='personalView" + (i + 1) + "'><div><div class='form-group col-md-6' style='padding: 0'>" +
                "<label>担当者氏名：性</label>" +
                "<input type='text' class='form-control' name='firstName" + (i + 1) + "' id='firstName" + (i + 1) + "' value='" + data[i]["first_name"] + "' disabled/>" +

                "</div><div class='form-group col-md-6' style='padding-right: 0'>" +
                "<label>担当者名</label>" +
                "<input type='text' class='form-control' name='lastName" + (i + 1) + "' id='lastName" + (i + 1) + "' value='" + data[i]["last_name"] + "' disabled/>" +

                "</div></div>" +
                "<div class='form-group'>" +
                "<label>電話番号「携帯」</label>" +
                "<input type='text' class='form-control' name='phone" + (i + 1) + "' id='phone" + (i + 1) + "' value='" + data[i]["phone"] + "' disabled/>" +

                "</div>" +
                "<div class='form-group'>" +
                "<label>電話番号「外線」</label>" +
                "<input type='text' class='form-control' name='outsideCall" + (i + 1) + "' id='outsideCall" + (i + 1) + "' value='" + data[i]["outsideCall"] + "' disabled/>" +

                "</div>" +
                "<div class='form-group'>" +
                "<label>メール</label>" +
                "<input type='text' class='form-control' name='email" + (i + 1) + "' id='email" + (i + 1) + "' value='" + data[i]["mail"] + "' disabled/>" +

                "</div></div>";
            $("#personalInfo").append(personalDiv);
        }
    }

    $("#saveModellingCompany").prop("disabled", true);
}



//**************Create new modelling company info **********//
function insertModellingCompany() {
    var validate;
    var numOfIncharge = parseInt($("#numOfIncharge").val());

    //**********Validation[Start]**************//
    if ($("#companyName option:selected").text() != "選択してください") {
        validate = true;
    }
    else {
        ShowErrMessage("会社名を選択してください。");
        return;
    }
    //**********Validation[end]**************//

    if (validate) {
        var idList = [];
        var firstNameList = [];
        var lastNameList = [];
        var phoneList = [];
        var outSideCallList = [];
        var mailList = [];
        var companyName = $("#companyName option:selected").text();
        var companyId = $("#hidCompanyId").val();
        var jobType = $("form #jobType").val();
        // var branch = ($("form #branch").val() == "選択してください。") ? " " : $("form #branch").val();
        var branch = $("form #branch").val();
        var isPartnerCompany = $("#isPartnerCompany").is(":checked") ? 1 : 0;


        //**********担当者**********//
        for (var i = 1; i <= numOfIncharge; i++) {
            idList.push(i);
            firstNameList.push($("#firstName" + i).val());
            lastNameList.push($("#lastName" + i).val());
            phoneList.push($("#phone" + i).val());
            outSideCallList.push($("#outsideCall" + i).val());
            mailList.push($("#email" + i).val());
        }

        var inChargeInfoList = idList.map((id, index) => {
            return {
                first_name: firstNameList[index],
                last_name: lastNameList[index],
                phone: phoneList[index],
                outsideCall: outSideCallList[index],
                mail: mailList[index]
            }
        });

        var modellingCompanyInfo = {
            'company_id': companyId,
            'jobType': jobType,
            'branch': branch,
            'isPartnerCompany': isPartnerCompany,
            'inchargeInfo': inChargeInfoList
        }

        console.log(modellingCompanyInfo);
        $.ajax({
            url: "../modellingCompany/saveData",
            async: true,
            type: 'post',
            data: { _token: CSRF_TOKEN, message: "insertModellingCompany", modellingCompanyInfo: modellingCompanyInfo },
            success: function(data) {
                console.log(data);
                if (data) {
                    alert("情報入力しました。");
                    location.reload()

                }
            },
            error: function(err) {
                console.log("err" + JSON.stringify(err));
                alert("情報入力に失敗しました。\n管理者に問い合わせてください。");
            }
        });

    }

}

//**************Update modelling company info *************//
function updateModellingCompany() {
    var validate;
    var numOfIncharge = parseInt($("#numOfIncharge").val());
    var id = $("#companyId").val();

    //**********Validation[Start]**************//
    if (id) {
        validate = true;
    }
    else {
        ShowErrMessage("変更したいモデリング会社を選択してください。");
        return;
    }
    //**********Validation[end]**************//

    if (validate) {
        var idList = [];
        var firstNameList = [];
        var lastNameList = [];
        var phoneList = [];
        var outSideCallList = [];
        var mailList = [];
        var companyName = $("#companyName option:selected").text();
        var jobType = $("form #jobType").val();
        var branch = $("form #branch").val();
        var isPartnerCompany = $("#isPartnerCompany").is(":checked") ? 1 : 0;


        //**********担当者**********//
        for (var i = 1; i <= numOfIncharge; i++) {
            idList.push(i);
            firstNameList.push($("#firstName" + i).val());
            lastNameList.push($("#lastName" + i).val());
            phoneList.push($("#phone" + i).val());
            outSideCallList.push($("#outsideCall" + i).val());
            mailList.push($("#email" + i).val());
        }

        var inChargeInfoList = idList.map((id, index) => {
            return {
                first_name: firstNameList[index],
                last_name: lastNameList[index],
                phone: phoneList[index],
                outsideCall: outSideCallList[index],
                mail: mailList[index]
            }
        });

        var modellingCompanyInfo = {
            'company_id': id,
            'jobType': jobType,
            'branch': branch,
            'isPartnerCompany': isPartnerCompany,
            'inchargeInfo': inChargeInfoList
        }

        console.log(modellingCompanyInfo);
        $.ajax({
            url: "../modellingCompany/saveData",
            async: true,
            type: 'post',
            data: { _token: CSRF_TOKEN, message: "insertModellingCompany", modellingCompanyInfo: modellingCompanyInfo },
            success: function(data) {
                console.log(data);
                if (data) {
                    alert("情報変更しました。");
                    location.reload()

                }
            },
            error: function(err) {
                console.log("err" + JSON.stringify(err));
                alert("情報変更に失敗しました。\n管理者に問い合わせてください。");
            }
        });

    }




}


//**************Delete modelling company info *************//
function deleteModellingCompany() {
    var id = $("#companyId").val();
    console.log("ID" + id);
    if (id) {
        var confirmResult = confirm("このモデリング会社を削除してよろしいですか。");
        if (confirmResult) {
            $.ajax({
                url: "../modellingCompany/deleteData",
                async: true,
                type: 'post',
                data: { _token: CSRF_TOKEN, message: "deleteModellingCompanyById", id: id },
                success: function(data) {
                    if (data) {　
                        alert("削除しました！");
                        window.location.reload();
                    }
                },
                error: function(err) {
                    console.log(err);
                    alert("情報削除に失敗しました。\n管理者に問い合わせてください。");
                }
            });
        }
        else {

        }


    }
    else {
        alert("削除するデータはありません！\n 削除したいモデリング会社を選択してください。")
    }
}

function ClearFormItems() {
    $("#companyId").val("");
    $("isPartnerCompany").prop("checked", false);

    $("form #txtCompanyName").hide();
    $("form #cName").show();
    $("form #companyName").val(0);
    $("form #jobType").val("");
    $("form #branch").val("選択してください。");

    $("form #address").val("");
    $("form #postalCode").val("");
    $("#numOfIncharge").val(1);
    var count = $("#personalInfo div.personalView").length;
    for (var i = count; i > 1; i--) {
        $("#personalView" + i).remove();
    }
    $("form #firstName1").val("");
    $("form #lastName1").val("");
    $("form #phone1").val("");
    $("form #outsideCall1").val("");
    $("form #email1").val("");
}


function createNewModellingCompany() {
    ClearFormItems();
    $("#partnerCompanyNameList tbody tr").removeClass("selected");
    $("#companyNameList tbody tr").removeClass("selected");
    $("form #txtCompanyName").hide();
    $("form #cName").show();
    $("form #companyName").prop("disabled", false);
    $("form #companyName").focus();
    $("form #companyName").val(0).trigger('change');
    document.getElementById('insertNewCompany').onclick = DisplayPopup;
    document.getElementById('updateInsertedCompany').onclick = ShowSuggestedCompany;
    $("form #firstName1").prop("disabled", false);
    $("form #lastName1").prop("disabled", false);
    $("form #phone1").prop("disabled", false);
    $("form #outsideCall1").prop("disabled", false);
    $("form #email1").prop("disabled", false);
    $("#saveModellingCompany").prop("disabled", false);
}



// Create New Company PopUp
function DisplayPopup() {
    g_isNewCreate = true;
    $("#createUser").css({ visibility: "visible", opacity: "1" });
}

function ClosePopup() {
    $("#createUser").css({ visibility: "hidden", opacity: "0" });
}

function getCompanyType() {
    $.ajax({
        url: "../company/getType",
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "getAllCompanyType", typeId: 0, typeName: "" },
        success: function(result) {
            console.log(result);
            createCompanyTypeSelect(result);
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function createCompanyTypeSelect(typeData) {
    var appendText = "";
    appendText += "<option value='0'>選択してください</option>";

    for (var i = 0; i < typeData.length; i++) {
        appendText += "<option value='" + typeData[i]["id"] + "'>" + typeData[i]["name"] + "</option>";
    }

    $("select#companyTypeSelect option").remove();
    $("select#companyTypeSelect").append(appendText);

}

function CreateCompany() {
    var isValid = Validataion();
    if (!isValid) return;
    var form = $(document.forms["createCompanyForm"]).serializeArray();
    console.log("Test")
    console.log(form);
    $.ajax({
        url: "../company/saveData",
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "singleCompany", companyData: form },
        success: function(message) {

            if (message.includes("success")) {
                alert("successfully saved!");
                $("#createUser").css({ visibility: "hidden", opacity: "0" });
            }

        },
        error: function(err) {
            alert(JSON.stringify(err));
            console.log(err);
        }
    });
}




function Validataion() {
    if ($("#txtName").val() == "") {
        $("#txtName_err").html("企業名を入力してください。");
        console.log($("#txtName").val());
        return false;
    }
    else if ($('#companyTypeSelect :selected').text() == '' || $('#companyTypeSelect :selected').text() == '選択してください') {
        console.log($('#companyTypeSelect :selected').text());
        $("#companyTypeSelect_err").html("企業種別を選択してください。");
        return false;
    }
    else if (g_isNewCreate) {
        var result = DuplicateCompanyChecking($("#txtName").val());
        if (result != "" && result[0]['count'] >= 1) {
            $("#txtName_err").html("会社が既に存在しています。");
            $("#txtName").focus();
            return false;
        }
        else {
            return true;
        }
    }
    else {
        return true;
    }
}

function DuplicateCompanyChecking(name) {
    var data = "";
    $.ajax({
        url: "/iPD/login/account/get",
        type: 'post',
        async: false,
        data: { _token: CSRF_TOKEN, message: "check_duplicate_company", name: name },
        success: function(result) {
            // console.log(result);
            data = result;
        },
        error: function(err) {
            console.log(err);
        }
    });
    return data;
}

//Suggested Data For Modelling Company
// function ShowSuggestedModellingCompany(){
//     var companyList = [];
//     $.ajax({
//         url: "../modellingCompany/getData",
//         async:true,
//         type: 'post',
//         data:{_token: CSRF_TOKEN, message:"getAllPartnerCompanyContact"},
//         success :function(data) {
//             if(data){
//                 $.each(data, function(key,value){
//                     $.each(value, function(tname, tdata){
//                         if(!companyList.includes(tdata['partnerCompanyName'])){
//                             companyList.push(tdata['partnerCompanyName']);
//                         }


//                     });

//                 });
//                 $( "#partnerCompanyName" ).autocomplete({
//                   source: companyList,
//                   minLength:0
//                 }).bind('focus', function(){ $(this).autocomplete("search"); } );
//             }
//         },
//         error:function(err){
//             console.log(err);
//         }
//     });
// }

//Suggested Data For JobType
// function ShowSuggestedPartnerJobtype(){
//     var jobTypeList = [];
//     $.ajax({
//         url: "../modellingCompany/getData",
//         async:true,
//         type: 'post',
//         data:{_token: CSRF_TOKEN, message:"getAllPartnerCompanyContact"},
//         success :function(data) {
//             if(data){
//                 $.each(data, function(key,value){
//                     $.each(value, function(tname, tdata){
//                         if(tdata['partnerJobType']){
//                             if(!jobTypeList.includes(tdata['partnerJobType'])){
//                                 jobTypeList.push(tdata['partnerJobType']);
//                             }
//                         }
//                     });

//                 });

//                 $( "#partnerJobType" ).autocomplete({
//                   source: jobTypeList,
//                   minLength:0
//                 }).bind('focus', function(){ $(this).autocomplete("search"); } );
//             }
//         },
//         error:function(err){
//             console.log(err);
//         }
//     });
// }

//Suggested Data For InchargeName
// function ShowSuggestedPartnerInchargeName(){
//     var inChargeList = [];
//     $.ajax({
//         url: "../modellingCompany/getData",
//         async:true,
//         type: 'post',
//         data:{_token: CSRF_TOKEN, message:"getAllPartnerCompanyContact"},
//         success :function(data) {
//             if(data){
//                 $.each(data, function(key,value){
//                     $.each(value, function(tname, tdata){
//                         if(tdata['partnerInchargeName']){
//                             if(!inChargeList.includes(tdata['partnerInchargeName'])){
//                                 inChargeList.push(tdata['partnerInchargeName']);
//                             }

//                         }


//                     });

//                 });

//                 $( "#partnerInchargeName" ).autocomplete({
//                   source: inChargeList,
//                   minLength:0
//                 }).bind('focus', function(){ $(this).autocomplete("search"); } );
//             }
//         },
//         error:function(err){
//             console.log(err);
//         }
//     });
// }

// function insertPartnerCompanyContact(){
//     var partnerCompanyName = $("form #partnerCompanyName").val();
//     var partnerJobType = $("form #partnerJobType").val();
//     var partnerCompanyBranch = $("form #partnerCompanyBranch").val();
//     var partnerMailCode = $("form #partnerMailCode").val();
//     var partnerCompanyAddress = $("form #partnerCompanyAddress").val();
//     var partnerInchargeName = $("form #partnerInchargeName").val();
//     var partnerPhone = $("form #partnerPhone").val();
//     var partnerEmail = $("form #partnerEmail").val();

//     console.log(partnerCompanyName);

//     if(partnerCompanyName){
//             var partnerCompanyContact = {
//                 'partnerCompanyName' : partnerCompanyName,
//                 'partnerJobType'     : partnerJobType,
//                 'partnerCompanyBranch'     : partnerCompanyBranch,
//                 'partnerMailCode'     : partnerMailCode,
//                 'partnerCompanyAddress'     : partnerCompanyAddress,
//                 'partnerInchargeName'     : partnerInchargeName,
//                 'partnerPhone'     : partnerPhone,
//                 'partnerEmail'     : partnerEmail
//             }

//             $.ajax({
//                     url: "../modellingCompany/saveData",
//                     async:true,
//                     type: 'post',
//                     data:{_token: CSRF_TOKEN, message:"insertPartnerCompanyContact", partnerCompanyContact : partnerCompanyContact},
//                     success :function(data) {
//                         alert("情報入力しました。");
//                         window.location="../partnerCompanyContact/index";
//                     },
//                     error:function(err){
//                         console.log("err" +JSON.stringify(err));
//                         alert("情報入力に失敗しました。\n管理者に問い合わせてください。");
//                     }
//                 }); 
//     }
// }

// function updatePartnerCompanyContact(){
//     var id = $("#partnerCompanyId").val();
//     var partnerCompanyName = $("form #partnerCompanyName").val();
//     var partnerJobType = $("form #partnerJobType").val();
//     var partnerCompanyBranch = $("form #partnerCompanyBranch").val();
//     var partnerMailCode = $("form #partnerMailCode").val();
//     var partnerCompanyAddress = $("form #partnerCompanyAddress").val();
//     var partnerInchargeName = $("form #partnerInchargeName").val();
//     var partnerPhone = $("form #partnerPhone").val();
//     var partnerEmail = $("form #partnerEmail").val();

//     var partnerCompanyContact = {
//                 'partnerCompanyName' : partnerCompanyName,
//                 'partnerJobType'     : partnerJobType,
//                 'partnerCompanyBranch'     : partnerCompanyBranch,
//                 'partnerMailCode'     : partnerMailCode,
//                 'partnerCompanyAddress'     : partnerCompanyAddress,
//                 'partnerInchargeName'     : partnerInchargeName,
//                 'partnerPhone'     : partnerPhone,
//                 'partnerEmail'     : partnerEmail
//             }

//     if(id){
//             $.ajax({
//                     url: "../modellingCompany/saveData",
//                     async:true,
//                     type: 'post',
//                     data:{_token: CSRF_TOKEN, message:"updateById", partnerCompanyContact : partnerCompanyContact,id : id},
//                     success :function(data) {
//                         if(data['isUpdated']){
//                             alert("情報編集しました。");
//                             location.reload();
//                         }
//                     },
//                     error:function(err){
//                         console.log("err" +JSON.stringify(err));
//                         alert("情報編集に失敗しました。\n管理者に問い合わせてください。");
//                     }
//                 }); 
//     }else{
//         alert("編集するデータはありません！");
//     }

// }

// function deletePartnerCompany(){
//     var id = $("#partnerCompanyId").val();
//     console.log("ID" +id);
//     if(id){
//         $.ajax({
//                 url: "../modellingCompany/deleteData",
//                 async:true,
//                 type: 'post',
//                 data:{_token: CSRF_TOKEN, message:"deletePartnerCompanyContactById", id : id},
//                 success :function(data) {
//                             if(data){
//                                 　alert("削除しました！");
//                                   window.location.reload();
//                                 }
//                             },
//                             error:function(err){
//                                 console.log(err);
//                                 alert("情報削除に失敗しました。\n管理者に問い合わせてください。");
//                             }
//             });  
//     }else{
//         alert("削除するデータはありません！")
//     }
// }
