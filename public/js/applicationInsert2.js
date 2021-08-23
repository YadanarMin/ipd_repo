var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

$(document).ready(function() {
    $.ajaxSetup({
        cache: false
    });

    ShowSuggestedData();
    getCompanyType();
    getBranchList();

    var userInfoList = JSON.parse($("#hidUserInfoList").val());
    var id_num = $("#hidNumOfApplicants").val();
    console.log(userInfoList);
    console.log(id_num);

    if (userInfoList !== null && userInfoList !== undefined) {
        id_num = userInfoList.length;
    }

    if (userInfoList) {
        for (var i = 0; i < userInfoList.length; i++) {
            var id = userInfoList[i]['isExistingUser'];
            if (id) {
                $("#companyType" + id).prop("disabled", true);
                $("#companyName" + id).prop("disabled", true);
                $("#branch" + id).prop("disabled", true);
                $("#dept" + id).prop("disabled", true);
                $("#code" + id).prop("disabled", true);
                $("#position" + id).prop("disabled", true);
            }
        }
    }


    for (var i = 1; i <= id_num; i++) {
        $("#companyType" + i).change(function() {
            var selectedCompanyTypeName = $("option:selected", this).text();
            if (selectedCompanyTypeName === "大林組(社員)") {

                $(this).parent().next().children("input:first").val("大林組");
                $(this).parent().next().children("input:first").prop("disabled", true);
                $(this).parent().next().next().children("select").prop("disabled", false);
                $(this).parent().next().next().next().children("input:first").prop("disabled", false);
                $(this).parent().next().next().next().next().children("input:first").val("1");

                //getBranchList();

            }
            else if (selectedCompanyTypeName === "大林組(派遣)") {

                $(this).parent().next().children("input:first").prop("disabled", false);
                $(this).parent().next().next().children("select").prop("disabled", false);
                $(this).parent().next().next().next().children("input:first").prop("disabled", false);
                $(this).parent().next().next().next().next().children("input:first").val("2");

                var companyNameTextBoxId = $(this).parent().next().children("input:first").attr("id");
                LoadCompanyList("0", companyNameTextBoxId);
                //getBranchList();

            }
            else if (selectedCompanyTypeName === "派遣会社") {

                $(this).parent().next().children("input:first").val("");
                $(this).parent().next().children("input:first").prop("disabled", false);
                $(this).parent().next().next().children("select").find("option").remove();
                $(this).parent().next().next().children("select").prop("disabled", true);
                $(this).parent().next().next().next().children("input:first").val("");
                $(this).parent().next().next().next().children("input:first").prop("disabled", true);
                $(this).parent().next().next().next().next().children("input:first").val("3");

                var companyTypeId = $("option:selected", this).val();
                var companyNameTextBoxId = $(this).parent().next().children("input:first").attr("id");
                LoadCompanyList(companyTypeId, companyNameTextBoxId);

            }
            else if (selectedCompanyTypeName === "協力会社") {

                $(this).parent().next().children("input:first").val("");
                $(this).parent().next().children("input:first").prop("disabled", false);
                $(this).parent().next().next().children("select").find("option").remove();
                $(this).parent().next().next().children("select").prop("disabled", true);
                $(this).parent().next().next().next().children("input:first").val("");
                $(this).parent().next().next().next().children("input:first").prop("disabled", true);
                $(this).parent().next().next().next().next().children("input:first").val("3");

                var companyTypeId = $("option:selected", this).val();
                var companyNameTextBoxId = $(this).parent().next().children("input:first").attr("id");
                LoadCompanyList(companyTypeId, companyNameTextBoxId);

            }
            else if (selectedCompanyTypeName === "モデリング会社") {

                $(this).parent().next().children("input:first").val("");
                $(this).parent().next().children("input:first").prop("disabled", false);
                $(this).parent().next().next().children("select").find("option").remove();
                $(this).parent().next().next().children("select").prop("disabled", true);
                $(this).parent().next().next().next().children("input:first").val("");
                $(this).parent().next().next().next().children("input:first").prop("disabled", true);
                $(this).parent().next().next().next().next().children("input:first").val("3");

                var companyTypeId = $("option:selected", this).val();
                var companyNameTextBoxId = $(this).parent().next().children("input:first").attr("id");
                LoadCompanyList(companyTypeId, companyNameTextBoxId);

            }
            else {
                var companyName = $(this).parent().next().children("input:first").val();
                if (companyName === "大林組") {
                    $(this).parent().next().children("input:first").val("");
                    $(this).parent().next().next().next().children("input:first").val("");
                }
                $(this).parent().next().children("input:first").prop("disabled", false);
                $(this).parent().next().next().next().children("input:first").prop("disabled", false);
                $(this).parent().next().next().children("select").prop("disabled", false);
                $(this).parent().next().next().next().next().children("input:first").val("3");
            }
        });


        //支店を選択すると組織表示
        $("#branch" + i).change(function() {
            var branchId = $("option:selected", this).val();
            var deptTextBoxId = $(this).parent().next().children("input:first").attr("id");
            console.log(deptTextBoxId);
            LoadDepartmentByBranchId(branchId, deptTextBoxId);
        })
    }
});

function LoadDepartmentByBranchId(branchId, deptTextBoxId) {
    console.log(branchId)
    var deptNameList = [];
    $.ajax({
        url: "/iPD/application/getData",
        async: true,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "getDeptList", branchId: branchId },
        success: function(data) {
            console.log(data)
            if (data) {
                $.each(data, function(key, value) {
                    $.each(value, function(tname, tdata) {
                        if (tdata['name']) {
                            if (!deptNameList.includes(tdata['name'])) {
                                deptNameList.push(tdata['name']);
                            }
                        }

                    });

                });
                console.log("DeptList " + JSON.stringify(deptNameList));
                $("#" + deptTextBoxId).autocomplete({
                    source: deptNameList,
                    minLength: 0
                }).bind('focus', function() { $(this).autocomplete("search"); });
            }
        },
        error: function(err) {
            console.log(err);
        }
    });

}

function LoadCompanyList(companyTypeId, companyNameTextBoxId) {
    console.log(companyTypeId)
    var companyNameList = [];
    $.ajax({
        url: "/iPD/application/getData",
        async: true,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "getCompanyList", companyTypeId: companyTypeId },
        success: function(data) {
            console.log(data)
            if (data) {
                $.each(data, function(key, value) {
                    $.each(value, function(tname, tdata) {
                        if (tdata['name']) {
                            if (!companyNameList.includes(tdata['name'])) {
                                if (tdata['name'] !== "大林組") {
                                    companyNameList.push(tdata['name']);
                                }
                            }
                        }

                    });

                });
                console.log("CompanyNameList " + JSON.stringify(companyNameList));
                $("#" + companyNameTextBoxId).autocomplete({
                    source: companyNameList,
                    minLength: 0
                }).bind('focus', function() { $(this).autocomplete("search"); });
            }
        },
        error: function(err) {
            console.log(err);
        }
    });

}

function getUserAttribute(id) {

    // var mail = $("#email"+id).val() === undefined ? "" : $("#email"+id).val();
    var mail = $("#email" + id).val();
    if (mail) {
        $.ajax({
            url: "/iPD/personal/getUser",
            type: 'post',
            data: { _token: CSRF_TOKEN, message: "getUserByMail", mail: mail },
            success: function(result) {
                console.log(result);
                if (result.length > 0) {
                    setUserAttribute(result[0], id);
                }
                else {
                    var companyType = document.getElementById("companyType" + id);
                    companyType.disabled = false;
                    var comName = document.getElementById("companyName" + id);
                    comName.disabled = false;
                    var dept = document.getElementById("dept" + id);
                    dept.disabled = false;
                    var branch = document.getElementById("branch" + id);
                    branch.disabled = false;
                    var code = document.getElementById("code" + id);
                    code.disabled = false;
                    var position = document.getElementById("position" + id);
                    position.disabled = false;
                }
            },
            error: function(err) {
                console.log(err);
            }
        });
    }
    else {
        alert("メールアドレスを入力してください。")
    }

}

function setUserAttribute(userData, id) {
    console.log(userData);
    console.log(id)
    $("#username" + id).val(userData['first_name']);
    $("#lastname" + id).val(userData['last_name']);
    $("#hidCompanyTypeID" + id).val(userData["company_type_id"]);
    if (userData["contract_type"] === 2) {
        $("#companyType" + id).val(userData["company_type_id"] + "0");
    }
    else {
        $("#companyType" + id).val(userData["company_type_id"]);
    }
    $("#companyType" + id).prop("disabled", true);
    $("#companyName" + id).val(userData["company_name"]);
    $("#companyName" + id).prop("disabled", true);
    $("#hidCompanyID" + id).val(userData["company_id"]);
    $("#dept" + id).prop("disabled", true);
    $("#dept" + id).val(userData["dept_name"]);
    $("#hidDeptID" + id).val(userData["dept_id"]);
    $("#branch" + id).prop("disabled", true);
    $("#branch" + id).val(userData["branch_id"]);
    $("#code" + id).val(userData["code"]);
    $("#position" + id).val(userData["position"]);
    $("#isStudyAbroad" + id).val(userData["isStudyAbroad"]);
    $("#isC3User" + id).val(userData["isC3User"]);
    $("#isAdditionalPost" + id).val(userData["isAdditionalPost"]);
    $("#isExistingUser" + id).val(id);
    $("#hidContractType" + id).val(userData["contract_type"]);
    //selectedOption("companyType"+id, userData["company_type_name"]);

}

function selectedOption(id_name, option_name) {

    let $element = $('#' + id_name);
    let val = $element.find("option:contains('" + option_name + "')").val();
    $element.val(val).trigger('change');
}

function getCompanyType() {
    $.ajax({
        url: "/iPD/company/getType",
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

function getBranchList() {
    $.ajax({
        url: "/iPD/company/getBranch",
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "getAllBranch" },
        success: function(result) {
            console.log(result);
            createBranchSelect(result);
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function createBranchSelect(typeData) {

    var applicants = $("#applicants").val();
    console.log(applicants)
    //Testing 
    for (var j = 1; j <= applicants; j++) {
        var appendText = "";
        var defaultVal = $("#hidBranchID" + j).val();

        if (defaultVal) {
            appendText += "<option value='0'>選択してください</option>";
            for (var i = 0; i < typeData.length; i++) {
                var selected = (typeData[i]['id'] == defaultVal) ? "selected" : "";
                appendText += "<option value='" + typeData[i]["id"] + "'" + selected + ">" + typeData[i]["name"] + "</option>";
            }
            $("select#branch" + j + " option").remove();
            $("select#branch" + j).append(appendText);
        }
        else {
            appendText += "<option value='0' >選択してください</option>";
            for (var i = 0; i < typeData.length; i++) {
                appendText += "<option value='" + typeData[i]["id"] + "'>" + typeData[i]["name"] + "</option>";
            }

            $("select#branch" + j + " option").remove();
            $("select#branch" + j).append(appendText);
        }
    }
    //Testing

}

function createCompanyTypeSelect(typeData) {

    var applicants = $("#applicants").val();
    console.log(applicants);

    // var userInfoList = JSON.parse( $("#hidUserInfoList").val() );
    // console.log("userInfoList");console.log(userInfoList);

    //Testing 
    for (var j = 1; j <= applicants; j++) {
        var appendText = "";
        var defaultVal = $("#hidCompanyTypeID" + j).val();
        var contractType = $("#hidContractType" + j).val();

        if (defaultVal) {
            appendText += "<option value=''>選択してください</option>";
            for (var i = 0; i < typeData.length; i++) {
                var selected = (typeData[i]['id'] == defaultVal) ? "selected" : "";
                if (typeData[i]["name"] === "大林組") {
                    if (contractType === "1") {
                        appendText += "<option value='" + typeData[i]["id"] + "'" + selected + ">" + typeData[i]["name"] + "(社員)</option>";
                        appendText += "<option value='" + typeData[i]["id"] + "0'>" + typeData[i]["name"] + "(派遣)</option>";
                    }
                    else {
                        appendText += "<option value='" + typeData[i]["id"] + "'>" + typeData[i]["name"] + "(社員)</option>";
                        appendText += "<option value='" + typeData[i]["id"] + "0'" + selected + ">" + typeData[i]["name"] + "(派遣)</option>";
                    }
                }
                else {
                    appendText += "<option value='" + typeData[i]["id"] + "'" + selected + ">" + typeData[i]["name"] + "</option>";
                }
            }
            $("select#companyType" + j + " option").remove();
            $("select#companyType" + j).append(appendText);
        }
        else {
            appendText += "<option value='' selected>選択してください</option>";
            for (var i = 0; i < typeData.length; i++) {
                if (typeData[i]["name"] === "大林組") {
                    appendText += "<option value='" + typeData[i]["id"] + "'>" + typeData[i]["name"] + "(社員)</option>";
                    appendText += "<option value='" + typeData[i]["id"] + "0'>" + typeData[i]["name"] + "(派遣)</option>";
                }
                else {
                    appendText += "<option value='" + typeData[i]["id"] + "'>" + typeData[i]["name"] + "</option>";
                }
            }

            $("select#companyType" + j + " option").remove();
            $("select#companyType" + j).append(appendText);
        }
    }
    //Testing

}

function SaveSessionAndGoToPage3() {
    var idList = [];
    var nameList = [];
    var lastNameList = [];
    var mailList = [];
    var companyTypeList = [];
    var companyTypeIdList = [];
    var companyNameList = [];
    var companyIdList = [];
    var deptList = [];
    var deptIdList = [];
    var branchList = [];
    var branchIdList = [];
    var codeList = [];
    var positionTypeList = [];
    var isStudyAbroadList = [];
    var isC3UserList = [];
    var isAdditionalPostList = [];
    var isExistingUserList = [];
    var contractTypeList = [];
    var applicants = $("#applicants").val();
    console.log(applicants)
    for (var i = 1; i <= applicants; i++) {
        idList.push($("#" + i).val());

        console.log($('#companyType :selected').text());

        // Validation

        if ($("#obayashi" + i).val()) {
            if (!$("#username" + i).val()) {
                var error = "<div class='alert alert-danger alert-dismissible' style='width: 90%'>" +
                    "<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>" +
                    "<strong>氏名を入力してください!</strong> </div>";
                $("#insertLabel").after(error);
                $("#username" + i).focus();
                return;
            }
            else if (!$("#email" + i).val()) {
                var error = "<div class='alert alert-danger alert-dismissible' style='width: 90%'>" +
                    "<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>" +
                    "<strong>メールアドレスを入力してください!</strong> </div>";
                $("#insertLabel").after(error);
                $("#email" + i).focus();
                return;
            }
            else if ($("#companyType" + i + " :selected").text() == "" || $("#companyType" + i + " :selected").text() == "選択してください") {
                var error = "<div class='alert alert-danger alert-dismissible' style='width: 90%'>" +
                    "<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>" +
                    "<strong>企業種別を入力してください!</strong> </div>";
                $("#insertLabel").after(error);
                $("#companyTypeSelect" + i).focus();
                return;
            }
        }
        else {
            if (!$("#username" + i).val()) {
                var error = "<div class='alert alert-danger alert-dismissible' style='width: 90%'>" +
                    "<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>" +
                    "<strong>氏名を入力してください!</strong> </div>";
                $("#insertLabel").after(error);
                $("#username" + i).focus();
                return;
            }
            else if (!$("#email" + i).val()) {
                var error = "<div class='alert alert-danger alert-dismissible' style='width: 90%'>" +
                    "<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>" +
                    "<strong>メールアドレスを入力してください!</strong> </div>";
                $("#insertLabel").after(error);
                $("#email" + i).focus();
                return;
            }
            else if ($("#companyType" + i + " :selected").text() == '' || $("#companyType" + i + " :selected").text() == '選択してください') {
                var error = "<div class='alert alert-danger alert-dismissible' style='width: 90%'>" +
                    "<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>" +
                    "<strong>企業種別を入力してください!</strong> </div>";
                $("#insertLabel").after(error);
                $("#companyTypeSelect" + i).focus();
                return;
            }
        }

        //Validation
        nameList.push($("#username" + i).val());
        lastNameList.push($("#lastname" + i).val());
        mailList.push($("#email" + i).val());
        companyTypeList.push($("#companyType" + i + " option:selected").text());
        companyTypeIdList.push($("#companyType" + i).val());
        companyNameList.push($("#companyName" + i).val());
        //companyIdList.push($("#hidCompanyID"+ i).val());
        companyIdList.push(($("#hidCompanyID" + i).val()) ? $("#hidCompanyID" + i).val() : 0);
        deptList.push($("#dept" + i).val());
        deptIdList.push(($("#hidDeptID" + i).val()) ? $("#hidDeptID" + i).val() : "");
        branchList.push(($("#branch" + i + " option:selected").text() == "選択してください") ? " " : $("#branch" + i + " option:selected").text());
        branchIdList.push(($("#branch" + i).val()) ? $("#branch" + i).val() : "");
        codeList.push($("#code" + i).val());
        positionTypeList.push($("#position" + i).val());
        isStudyAbroadList.push($("#isStudyAbroad" + i).val());
        isC3UserList.push($("#isC3User" + i).val());
        isAdditionalPostList.push($("#isAdditionalPost" + i).val());
        isExistingUserList.push($("#isExistingUser" + i).val());
        contractTypeList.push($("#hidContractType" + i).val());

    }
    console.log(idList)
    console.log(nameList)
    console.log(lastNameList)
    console.log(mailList)
    console.log(companyTypeList)
    console.log(companyTypeIdList)
    console.log(companyNameList)
    console.log(companyIdList)
    console.log(deptList)
    console.log(deptIdList)
    console.log(branchList)
    console.log(branchIdList)
    console.log(codeList)
    console.log(positionTypeList)
    console.log(contractTypeList)

    var userInfoList = idList.map((id, index) => {
        return {
            id: id,
            name: nameList[index],
            lastname: lastNameList[index],
            email: mailList[index],
            companyType: companyTypeList[index],
            companyTypeId: companyTypeIdList[index],
            companyName: companyNameList[index],
            companyId: companyIdList[index],
            dept: deptList[index],
            deptId: deptIdList[index],
            branch: branchList[index],
            branchId: branchIdList[index],
            code: codeList[index],
            position: positionTypeList[index],
            isStudyAbroad: isStudyAbroadList[index],
            isC3User: isC3UserList[index],
            isAdditionalPost: isAdditionalPostList[index],
            isExistingUser: isExistingUserList[index],
            contractType: contractTypeList[index],
        }
    });

    console.log(userInfoList);
    $.ajax({
        type: "post",
        url: "/iPD/application/saveInsertData",
        data: { _token: CSRF_TOKEN, message: "saveInsertData2", userInfoList: userInfoList },
        success: function(data) {
            console.log(data)
            window.location.href = "/iPD/application/insert/page3"

        },
        error: function(err) {
            console.log(err);
        }
    });

}

function ShowSuggestedData() {
    var mise = [];
    var obayashi = [];
    var hakenplace = [];
    var job = [];
    var applicants = $("#applicants").val();

    return; //TODO

    $.ajax({
        url: "/iPD/application/getData",
        async: true,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "applicantsInfo" },
        success: function(data) {
            console.log(data)
            if (data) {
                $.each(data, function(key, value) {
                    $.each(value, function(tname, tdata) {
                        if (tdata['place']) {
                            if (!mise.includes(tdata['place'])) {
                                mise.push(tdata['place']);
                            }
                        }
                        if (tdata['obayashi']) {
                            if (!obayashi.includes(tdata['obayashi'])) {
                                obayashi.push(tdata['obayashi']);
                            }
                        }
                        if (tdata['hakenplace']) {
                            if (!hakenplace.includes(tdata['hakenplace'])) {
                                hakenplace.push(tdata['hakenplace']);
                            }
                        }
                        if (tdata['job']) {
                            if (!job.includes(tdata['job'])) {
                                job.push(tdata['job']);
                            }
                        }
                    });

                });
                console.log("Mise " + JSON.stringify(mise));

                for (var i = 1; i <= applicants; i++) {
                    $("#place" + i).autocomplete({
                        source: mise,
                        minLength: 0
                    }).bind('focus', function() { $(this).autocomplete("search"); });
                    $("#obayashi" + i).autocomplete({
                        source: obayashi,
                        minLength: 0
                    }).bind('focus', function() { $(this).autocomplete("search"); });
                    $("#hakenplace" + i).autocomplete({
                        source: hakenplace,
                        minLength: 0
                    }).bind('focus', function() { $(this).autocomplete("search"); });
                    $("#job" + i).autocomplete({
                        source: job,
                        minLength: 0
                    }).bind('focus', function() { $(this).autocomplete("search"); });
                }
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}
