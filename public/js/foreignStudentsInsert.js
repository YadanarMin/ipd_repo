/* ajax通信トークン定義 */
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

$(document).ready(function() {
    $.ajaxSetup({
        cache: false
    });

    $("#insertBtn").addClass("disabledBtn");
    $("#firstname").focus();
    var all = $("#all").is(":checked");

    if (all) {
        LoadAllStudents();
    }

    //SearchFunction
    $("#userSearch1").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#searchableUserList1 tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // ShowSuggestedMise();
    ShowSuggestedSkill();
    ShowSuggestedField();
    //ShowSuggestedHakenPlace();
    //ShowSuggestedObayashiPlace();
    ShowSuggestedStudentType();

    getBranchList();
    getCompanyType();
    getHakenCompanyList();

    $("#studentNameList tbody").on("click", "tr", function(e) {
        var id = $(this).find($('input[type=hidden]')).val();
        $(this).addClass("selected").siblings().removeClass("selected");
        ShowSelectedStudent(id);
    });

    //***********支店を選択すると組織表示[Start]******************//
    $("#s-place").change(function() {
        var branchId = $("option:selected", this).val();
        LoadDepartmentByBranchId(branchId, "s-obayashi");
    })

    $("#e-place").change(function() {
        var branchId = $("option:selected", this).val();
        LoadDepartmentByBranchId(branchId, "e-obayashi");
    })
    //***********支店を選択すると組織表示[End]******************//

    //***********企業タイプを選択すると会社名表示[Start]******************//
    $("#s-companyType").change(function() {
        var companyTypeId = $("option:selected", this).val();
        if (companyTypeId == 1) {
            $("#s-hakenplace").prop("disabled", true);
            $("#s-hakenplace").show();
            $("#s-hakenplaceSelectBox").hide();
        }
        else {
            $("#s-hakenplace").prop("disabled", false);
            if (companyTypeId == 10) {
                companyTypeId = 0;
                $("#s-hakenplace").hide();
                $("#s-hakenplaceSelectBox").show();
            }
            else {
                $("#s-hakenplace").show();
                $("#s-hakenplaceSelectBox").hide();
                LoadCompanyList(companyTypeId, "s-hakenplace");
            }


        }

    })

    $("#e-companyType").change(function() {
        var companyTypeId = $("option:selected", this).val();
        if (companyTypeId == 1) {
            $("#e-hakenplace").prop("disabled", true);
            $("#e-hakenplace").show();
            $("#e-hakenplaceSelectBox").hide();
        }
        else {
            $("#e-hakenplace").prop("disabled", false);
            if (companyTypeId == 10) {
                companyTypeId = 0;
                $("#e-hakenplace").hide();
                $("#e-hakenplaceSelectBox").show();
            }
            else {
                $("#e-hakenplace").show();
                $("#e-hakenplaceSelectBox").hide();
                LoadCompanyList(companyTypeId, "e-hakenplace");
            }

        }

    })
    //***********企業タイプを選択すると会社名表示[End]******************//

});

function LoadDepartmentByBranchId(branchId, deptTextBoxId) {
    console.log(branchId)
    var deptNameList = [];
    $.ajax({
        url: "../application/getData",
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
        url: "../application/getData",
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

function getHakenCompanyList() {
    var companyNameList = [];
    $.ajax({
        url: "../application/getData",
        async: true,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "getCompanyList", companyTypeId: 0 },
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
                createHakenCompanyListSelectBox(companyNameList);

            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function createHakenCompanyListSelectBox(data) {

    var optionStr = "";
    optionStr += "<option value='0' selected>選択してください</option>";
    for (var i = 0; i < data.length; i++) {
        optionStr += "<option value='" + data[i] + "'>" + data[i] + "</option>";
    }
    // var selectBoxId = companyNameTextBoxId + "SelectBox";
    $("#e-hakenplaceSelectBox option").remove();
    $("#s-hakenplaceSelectBox option").remove();
    $("#s-hakenplaceSelectBox").append(optionStr);
    $("#e-hakenplaceSelectBox").append(optionStr);
}

//*********支店名リスト表示「Start」**********
function getBranchList() {
    $.ajax({
        url: "../company/getBranch",
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "getAllBranch" },
        success: function(result) {
            createBranchSelect(result);

        },
        error: function(err) {
            console.log(err);
        }
    });
}

function createBranchSelect(result) {
    var optionStr = "";
    optionStr += "<option value='0' selected>選択してください</option>";
    $.each(result, function(tname, tdata) {
        if (tdata['name']) {
            optionStr += "<option value='" + tdata["id"] + "'>" + tdata["name"] + "</option>";
        }
    });
    console.log(optionStr);
    $("#s-place option").remove();
    $("#e-place option").remove();
    $("#s-place").append(optionStr);
    $("#e-place").append(optionStr);

}
//*********支店名リスト表示「End」**********


//*********企業タイプリスト表示「Start」**********
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
        if (typeData[i]['name'] == "大林組") {
            appendText += "<option value='" + typeData[i]["id"] + "'>" + typeData[i]["name"] + "(社員)</option>";
            appendText += "<option value='10'>" + typeData[i]["name"] + "(派遣)</option>";
        }
        else {
            appendText += "<option value='" + typeData[i]["id"] + "'>" + typeData[i]["name"] + "</option>";
        }

    }
    $("select#s-companyType option").remove();
    $("select#e-companyType option").remove();
    $("select#companyType option").remove();

    $("select#s-companyType").append(appendText);
    $("select#e-companyType").append(appendText);
    $("select#companyType").append(appendText);

}
//*********企業タイプリスト表示「End」**********


//*********tb-personalにすでにあるユーザーの情報取得「Start」**********
function getUserAttribute() {
    var mail = $("#email").val();
    console.log("getUserAttribute:" + mail);
    if (mail) {
        $.ajax({
            url: "../personal/getUser",
            type: 'post',
            data: { _token: CSRF_TOKEN, message: "getStudentByMail", mail: mail },
            success: function(result) {
                console.log(result);
                if (result.length > 0) {
                    setUserAttribute(result[0]);
                }
                else {
                    $("#s-place").prop("disabled", false);
                    $("#e-place").prop("disabled", false);
                    $("#s-obayashi").prop("disabled", false);
                    $("#e-obayashi").prop("disabled", false);
                    $("#s-code").prop("disabled", false);
                    $("#e-code").prop("disabled", false);
                    $("#s-companyType").prop("disabled", false);
                    $("#e-companyType").prop("disabled", false);
                    $("#s-hakenplace").prop("disabled", false);
                    $("#e-hakenplace").prop("disabled", false);
                    $("#s-skill").prop("disabled", false);
                    $("#e-skill").prop("disabled", false);
                    $("#s-field").prop("disabled", false);
                    $("#e-field").prop("disabled", false);
                    $("#s-type").prop("disabled", false);
                    $("#e-type").prop("disabled", false);
                    $("#startDate").prop("disabled", false);
                    $("#endDate").prop("disabled", false);
                    $("#puchi1").prop("disabled", false);
                    $("#puchi2").prop("disabled", false);
                    $("#puchi3").prop("disabled", false);
                    $("#puchi4").prop("disabled", false);

                }
            },
            error: function(err) {
                console.log(err);
            }
        });
    }
    else {
        ShowErrorMessage("メールアドレス");
    }

}

function setUserAttribute(userData) {
    console.log(userData);
    $("#firstname").val(userData["first_name"]);
    $("#lastname").val(userData["last_name"]);
    $("#firstnameKana").val(userData["first_name_kana"]);
    $("#lastnameKana").val(userData["last_name_kana"]);


    $("#s-place").prop("disabled", false);
    var sBranchId = userData["s_branch_id"];
    $("#s-place option[value='" + sBranchId + "']").attr("selected", true);

    $("#e-place").prop("disabled", true);
    var eBranchId = userData["genzai_branch_id"];
    $("#e-place option[value='" + eBranchId + "']").attr("selected", true);

    $("#s-obayashi").prop("disabled", false);
    $("#s-obayashi").val(userData['s_dept_name']);

    $("#e-obayashi").prop("disabled", true);
    $("#e-obayashi").val(userData['genzai_dept_name']);

    $("#s-code").prop("disabled", false);
    $("#s-code").val(userData["s_code"]);

    $("#e-code").prop("disabled", true);
    $("#e-code").val(userData["code"]);

    $("#s-companyType").prop("disabled", false);
    var sHakenTypeId = userData['s_haken_company_type_id'];
    $("#s-companyType option[value='" + sHakenTypeId + "']").attr("selected", true);

    $("#s-hakenplace").prop("disabled", false);
    $("#s-hakenplace").val(userData["s_haken_company_name"]);

    $("#e-companyType").prop("disabled", true);
    var eHakenTypeId = userData['genzai_haken_company_type_id'];
    $("#e-companyType option[value='" + eHakenTypeId + "']").attr("selected", true);

    $("#e-hakenplace").prop("disabled", true);
    $("#e-hakenplace").val(userData["genzai_haken_company_name"]);

    $("#s-skill").prop("disabled", false);
    $("#s-skill").val(userData["s_skill"]);

    $("#e-skill").prop("disabled", false);
    $("#e-skill").val(userData["genzai_skill"]);

    $("#s-field").prop("disabled", false);
    $("#s-field").val(userData["s_field"]);

    $("#e-field").prop("disabled", false);
    $("#e-field").val(userData["genzai_field"]);

    $("#s-type").prop("disabled", false);
    $("#s-type").val(userData["s_type"]);

    $("#e-type").prop("disabled", false);
    $("#e-type").val(userData["genzai_type"]);

    $("#startDate").prop("disabled", false);
    $("#startDate").val(userData["startDate"]);

    $("#endDate").prop("disabled", false);
    $("#endDate").val(userData["endDate"]);

    $("#puchi1").prop("disabled", false);
    $("#puchi1").val(userData["puchi1"]);

    $("#puchi2").prop("disabled", false);
    $("#puchi2").val(userData["puchi2"]);

    $("#puchi3").prop("disabled", false);
    $("#puchi3").val(userData["puchi3"]);

    $("#puchi4").prop("disabled", false);
    $("#puchi4").val(userData["puchi4"]);

    $("#isSpeedCourse").val(userData["isSpeedCourse"]);
    $("#isC3User").val(userData["isC3User"]);
    $("#position").val(userData["position"]);
    $("#companyId").val(userData["company_id"]);


}
//*********tb-personalにすでにあるユーザーの情報取得「End」**********

function ShowSuggestedMise() {
    var mise = [];
    $.ajax({
        url: "../foreignStudents/getData",
        async: true,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "getAllMise" },
        success: function(data) {
            if (data) {
                $.each(data, function(key, value) {
                    $.each(value, function(tname, tdata) {
                        if (tdata['s_place']) {
                            if (!mise.includes(tdata['s_place'])) {
                                mise.push(tdata['s_place']);
                            }
                        }


                    });

                });
                console.log("Mise " + JSON.stringify(mise));

                $("#s-place, #e-place").autocomplete({
                    source: mise,
                    minLength: 0
                }).bind('focus', function() { $(this).autocomplete("search"); });
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function ShowSuggestedField() {
    var field = [];
    $.ajax({
        url: "../foreignStudents/getData",
        async: true,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "getAllField" },
        success: function(data) {
            if (data) {
                $.each(data, function(key, value) {
                    $.each(value, function(tname, tdata) {
                        if (tdata['s_field']) {
                            if (!field.includes(tdata['s_field'])) {
                                field.push(tdata['s_field']);
                            }
                        }
                        if (tdata['genzai_field']) {
                            if (!field.includes(tdata['genzai_field'])) {
                                field.push(tdata['genzai_field']);
                            }
                        }


                    });

                });
                console.log("Field " + JSON.stringify(field));
                $("#s-field, #e-field").autocomplete({
                    source: field,
                    minLength: 0
                }).bind('focus', function() { $(this).autocomplete("search"); });
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function ShowSuggestedSkill() {
    var skill = [];
    $.ajax({
        url: "../foreignStudents/getData",
        async: true,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "getAllSkill" },
        success: function(data) {
            if (data) {
                $.each(data, function(key, value) {
                    $.each(value, function(tname, tdata) {
                        if (tdata['s_skill']) {
                            if (!skill.includes(tdata['s_skill'])) {
                                skill.push(tdata['s_skill']);
                            }
                        }
                        if (tdata['genzai_skill']) {
                            if (!skill.includes(tdata['genzai_skill'])) {
                                skill.push(tdata['genzai_skill']);
                            }
                        }


                    });

                });
                console.log("skill " + JSON.stringify(skill));
                $("#s-skill, #e-skill").autocomplete({
                    source: skill,
                    minLength: 0
                }).bind('focus', function() { $(this).autocomplete("search"); });
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function ShowSuggestedHakenPlace() {
    var hakenplace = [];
    $.ajax({
        url: "../foreignStudents/getData",
        async: true,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "getAllhakenplace" },
        success: function(data) {
            if (data) {
                $.each(data, function(key, value) {
                    $.each(value, function(tname, tdata) {
                        if (tdata['s_haken_department']) {
                            if (!hakenplace.includes(tdata['s_haken_department'])) {
                                hakenplace.push(tdata['s_haken_department']);
                            }
                        }

                    });

                });
                console.log("hakenplace " + JSON.stringify(hakenplace));
                $("#s-hakenplace, #e-hakenplace").autocomplete({
                    source: hakenplace,
                    minLength: 0
                }).bind('focus', function() { $(this).autocomplete("search"); });
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function ShowSuggestedObayashiPlace() {
    var obayashi = [];
    $.ajax({
        url: "../foreignStudents/getData",
        async: true,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "getAllObayashi" },
        success: function(data) {
            if (data) {
                $.each(data, function(key, value) {
                    $.each(value, function(tname, tdata) {
                        if (tdata['s_obayashi_department']) {
                            if (!obayashi.includes(tdata['s_obayashi_department'])) {
                                obayashi.push(tdata['s_obayashi_department']);
                            }
                        }


                    });

                });
                console.log("obayashi " + JSON.stringify(obayashi));
                $("#s-obayashi, #e-obayashi").autocomplete({
                    source: obayashi,
                    minLength: 0
                }).bind('focus', function() { $(this).autocomplete("search"); });
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function ShowSuggestedStudentType() {
    var type = [];
    $.ajax({
        url: "../foreignStudents/getData",
        async: true,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "getAllType" },
        success: function(data) {
            if (data) {
                $.each(data, function(key, value) {
                    $.each(value, function(tname, tdata) {
                        if (tdata['s_type']) {
                            if (!type.includes(tdata['s_type'])) {
                                type.push(tdata['s_type']);
                            }
                        }
                        if (tdata['genzai_type']) {
                            if (!type.includes(tdata['genzai_type'])) {
                                type.push(tdata['genzai_type']);
                            }
                        }
                    });

                });
                console.log("type " + JSON.stringify(type));
                $("#s-type, #e-type").autocomplete({
                    source: type,
                    minLength: 0
                }).bind('focus', function() { $(this).autocomplete("search"); });
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}




// 留学終了の学生
function LoadFinishedStudent() {
    $.ajax({
        url: "../foreignStudents/getData",
        async: true,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "getFinishedStudents" },
        success: function(data) {
            console.log("ListOfStudents" + JSON.stringify(data, null, 4));
            if (data) {
                ShowAllStudents(data);
            }
        },
        error: function(err) {
            console.log(err);
            alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });
}

//留学中の学生
function LoadNotFinishedStudent() {
    $.ajax({
        url: "../foreignStudents/getData",
        async: true,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "getNotFinishedStudents" },
        success: function(data) {
            if (data) {
                ShowAllStudents(data);
            }
        },
        error: function(err) {
            console.log(err);
            alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });
}

//留学予定
function LoadNotYetStudent() {
    $.ajax({
        url: "../foreignStudents/getData",
        async: true,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "getNotYetStudents" },
        success: function(data) {
            console.log("ListOfStudents" + JSON.stringify(data, null, 4));
            if (data) {
                ShowAllStudents(data);
            }
        },
        error: function(err) {
            console.log(err);
            alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });
}

//留学中と留学終了 LoadNotFinishedAndNotYetStudent
function LoadNotFinishedAndFinishedStudent() {
    $.ajax({
        url: "../foreignStudents/getData",
        async: true,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "getNotFinishedAndFinishedStudents" },
        success: function(data) {
            console.log("ListOfStudents" + JSON.stringify(data, null, 4));
            if (data) {
                ShowAllStudents(data);
            }
        },
        error: function(err) {
            console.log(err);
            alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });
}

//留学中と留学予定
function LoadNotFinishedAndNotYetStudent() {
    $.ajax({
        url: "../foreignStudents/getData",
        async: true,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "getNotFinishedAndNotYetStudents" },
        success: function(data) {
            console.log("ListOfStudents" + JSON.stringify(data, null, 4));
            if (data) {
                ShowAllStudents(data);
            }
        },
        error: function(err) {
            console.log(err);
            alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });
}

//留学終了と留学予定
function LoadNotYetAndFinishedStudent() {
    $.ajax({
        url: "../foreignStudents/getData",
        async: true,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "getNotYetAndFinishedStudents" },
        success: function(data) {
            console.log("ListOfStudents" + JSON.stringify(data, null, 4));
            if (data) {
                ShowAllStudents(data);
            }
        },
        error: function(err) {
            console.log(err);
            alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });
}

function ClearAllStudents() {
    $("#studentNameList tbody").empty();
}

// function ShowAllStudents(data) {
//     $.each(data, function(key,value){
//         $.each(value, function(tname, tdata){
//             var row =  "<tr><td>" + tdata['username']  +  "<input type='hidden' value='" + tdata['id'] + "'/></td>"+
//                         "<td style='display:none;'>" + tdata['s_code']  + "</td>" +
//                         "<td style='display:none;'>" + tdata['s_field']  + "</td>" +
//                         "<td style='display:none;'>" + tdata['s_haken_department']  + "</td>" +
//                         "<td style='display:none;'>" + tdata['s_obayashi_department']  + "</td>" +
//                         "<td style='display:none;'>" + tdata['s_type']  + "</td>" +
//                         "<td style='display:none;'>" + tdata['s_place']  + "</td>" +
//                         "<td style='display:none;'>" + tdata['s_skill']  + "</td>" +
//             "</tr>";
//             $("#studentNameList tbody").append(row);
//         });

//     });
// }

//************留学生全員取得****************//
function LoadAllStudents() {
    $.ajax({
        url: "../foreignStudents/getData",
        async: true,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "getAllStudents" },
        success: function(data) {
            if (data) {
                ShowAllStudents(data);
            }
        },
        error: function(err) {
            console.log(err);
            alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });
}

//**********留学生全員表示**************//
function ShowAllStudents(data) {
    console.log(data);
    $.each(data, function(key, value) {
        $.each(value, function(tname, tdata) {
            var row = "<tr><td>" + tdata['first_name'] + " " + tdata['last_name'] + "<input type='hidden' value='" + tdata['id'] + "'/></td>" +
                "<td style='display:none;'>" + tdata['code'] + "</td>" +
                "<td style='display:none;'>" + tdata['s_code'] + "</td>" +
                "<td style='display:none;'>" + tdata['s_skill'] + "</td>" +
                "<td style='display:none;'>" + tdata['genzai_skill'] + "</td>" +
                "<td style='display:none;'>" + tdata['s_field'] + "</td>" +
                "<td style='display:none;'>" + tdata['genzai_field'] + "</td>" +
                "<td style='display:none;'>" + tdata['s_type'] + "</td>" +
                "<td style='display:none;'>" + tdata['genzai_type'] + "</td>" +
                "<td style='display:none;'>" + tdata['s_haken_company_name'] + "</td>" +
                "<td style='display:none;'>" + tdata['genzai_haken_company_name'] + "</td>" +
                "<td style='display:none;'>" + tdata['genzai_dept_name'] + "</td>" +
                "<td style='display:none;'>" + tdata['genzai_branch_name'] + "</td>" +
                "<td style='display:none;'>" + tdata['s_dept_name'] + "</td>" +
                "<td style='display:none;'>" + tdata['s_branch_name'] + "</td>" +

                "</tr>";
            $("#studentNameList tbody").append(row);
        });

    });
}

//**********留学生全員表示[Id]**************//
function ShowSelectedStudent(id) {
    $.ajax({
        url: "../foreignStudents/getData",
        async: true,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "getStudentById", id: id },
        success: function(data) {
            console.log("StudentById" + JSON.stringify(data, null, 4));
            if (data) {
                ShowStudentFormDataById(data);
            }
        },
        error: function(err) {
            console.log(err);
            alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });

}

function ShowStudentFormDataById(data) {
    $.each(data, function(key, value) {
        $.each(value, function(tname, tdata) {
            console.log(tdata);
            var id = tdata['id'];
            var first_name = tdata['first_name'];
            var last_name = tdata['last_name'];
            var first_name_kana = tdata['first_name_kana'];
            var last_name_kana = tdata['last_name_kana'];
            var mail = tdata['mail'];
            var genzai_branch_id = tdata['branch_id'];
            var genzai_dept_name = tdata['dept_name'];
            var genzai_code = tdata['code'];
            var genzai_haken_company_type_id = tdata['genzai_haken_company_type_id'];
            var genzai_haken_company_name = tdata['genzai_haken_company_name'];
            var genzai_skill = tdata['genzai_skill'];
            var genzai_field = tdata['genzai_field'];
            var genzai_type = tdata['genzai_type'];
            var contractType = tdata['contract_type'];
            var s_contract_type = tdata['s_contract_type'];

            var s_branch_id = tdata['s_branch_id'];
            var s_dept_name = tdata['s_dept_name'];
            var s_code = tdata['s_code'];
            var s_haken_company_type_id = tdata['s_haken_company_type_id'] ? tdata['s_haken_company_type_id'] : 0;
            var s_haken_company_name = tdata['s_haken_company_name'];
            var s_skill = tdata['s_skill'];
            var s_field = tdata['s_field'];
            var s_type = tdata['s_type'];
            var startDate = tdata['startDate'];
            var endDate = tdata['endDate'];
            var puchi1 = tdata['puchi1'];
            var puchi2 = tdata['puchi2'];
            var puchi3 = tdata['puchi3'];
            var puchi4 = tdata['puchi4'];

            ClearFormItems();

            $("form #studentId").val(id);
            $("form #firstname").prop("disabled", true);
            $("form #firstname").val(first_name);
            $("form #lastname").prop("disabled", true);
            $("form #lastname").val(last_name);
            $("form #firstnameKana").prop("disabled", true);
            $("form #firstnameKana").val(first_name_kana);
            $("form #lastnameKana").prop("disabled", true);
            $("form #lastnameKana").val(last_name_kana);
            $("form #email").prop("disabled", true);
            $("form #email").val(mail);

            // $("form #s-place option[value='"+ s_branch_id + "']").attr("selected",true);
            // $("form #e-place option[value='"+ genzai_branch_id + "']").attr("selected",true);
            $("form #s-place").prop("disabled", false);
            $("form #s-place").val(s_branch_id);
            $("form #e-place").val(genzai_branch_id);
            $("form #s-obayashi").prop("disabled", false);
            $("form #s-obayashi").val(s_dept_name);
            $("form #e-obayashi").val(genzai_dept_name);
            $("form #s-code").prop("disabled", false);
            $("form #s-code").val(s_code);
            $("form #e-code").val(genzai_code);
            // $("#s-companyType option[value='"+ s_haken_company_type_id + "']").attr("selected",true);
            // $("#e-companyType option[value='"+ genzai_haken_company_type_id + "']").attr("selected",true);
            $("form #s-companyType").prop("disabled", false);
            if (s_contract_type == 2) {
                $("form #s-companyType").val(10);
                $("form #s-hakenplace").prop("disabled", false);
                $("form #s-hakenplaceSelectBox").show();
                $("form #s-hakenplace").hide();
                $("form #s-hakenplaceSelectBox").val(s_haken_company_name);
            }
            else {
                $("form #s-companyType").val(s_haken_company_type_id);
                $("form #s-hakenplaceSelectBox").hide();
                $("form #s-hakenplace").show();
                $("form #s-hakenplace").prop("disabled", false);
                $("form #s-hakenplace").val(s_haken_company_name);
            }

            if (contractType == 2) {
                $("form #e-companyType").val(10);
            }
            else {
                $("form #e-companyType").val(genzai_haken_company_type_id);
            }


            $("form #e-hakenplace").val(genzai_haken_company_name);
            $("form #s-skill").prop("disabled", false);
            $("form #s-skill").val(s_skill);
            $("form #e-skill").prop("disabled", false);
            $("form #e-skill").val(genzai_skill);
            $("form #s-field").prop("disabled", false);
            $("form #s-field").val(s_field);
            $("form #e-field").prop("disabled", false);
            $("form #e-field").val(genzai_field);
            $("form #s-type").prop("disabled", false);
            $("form #s-type").val(s_type);
            $("form #e-type").prop("disabled", false);
            $("form #e-type").val(genzai_type);
            $("form #startDate").prop("disabled", false);
            $("form #startDate").val(startDate);
            $("form #endDate").prop("disabled", false);
            $("form #endDate").val(endDate);
            $("form #puchi1").prop("disabled", false);
            $("form #puchi1").val(puchi1);
            $("form #puchi2").prop("disabled", false);
            $("form #puchi2").val(puchi2);
            $("form #puchi3").prop("disabled", false);
            $("form #puchi3").val(puchi3);
            $("form #puchi4").prop("disabled", false);
            $("form #puchi4").val(puchi4);
            $("#saveStudent").prop("disabled", true);

        });
    });

}

function ClearFormItems() {
    $("form #studentId").val('');
    $("form #firstname").val('');
    $("form #lastname").val('');
    $("form #firstnameKana").val('');
    $("form #lastnameKana").val('');
    $("form #email").val('');
    $("form #s-place").val(0);
    $("form #e-place").val(0);


    $("form #s-obayashi").val('');
    $("form #e-obayashi").val('');
    $("form #s-code").val('');
    $("form #e-code").val('');
    $("form #s-companyType").val(0);
    $("form #e-companyType").val(0);
    $("form #s-hakenplace").val('');
    $("form #e-hakenplace").val('');
    $("form #s-skill").val('');
    $("form #e-skill").val('');
    $("form #s-field").val('');
    $("form #e-field").val('');
    $("form #s-type").val('');
    $("form #e-type").val('');
    $("form #startDate").val('');
    $("form #endDate").val('');
    $("form #puchi1").val('');
    $("form #puchi2").val('');
    $("form #puchi3").val('');
    $("form #puchi4").val('');
}

//Toggle Button
function InsertBtn() {

    window.location = "../foreignStudents/insert";

}

//Toggle Button
function ShowBtn() {
    $("#showBtn").addClass("disabledBtn");
    window.location = '../foreignStudents/show';
}

//登録ボタン
// function SaveStudent(){
//     var id = $("form #studentId").val();

//     var username = $("form #username").val();
//     if(username){

//         //Get data from form data
//         var s_place = $("form #s-place").val();
//         var e_place = $("form #e-place").val();
//         var s_skill = $("form #s-skill").val();
//         var e_skill = $("form #e-skill").val();
//         var s_field = $("form #s-field").val();
//         var e_field = $("form #e-field").val();
//         var s_hakenplace = $("form #s-hakenplace").val();
//         var e_hakenplace = $("form #e-hakenplace").val();
//         var s_obayashi = $("form #s-obayashi").val();
//         var e_obayashi = $("form #e-obayashi").val();
//         var s_code = $("form #s-code").val();
//         var e_code = $("form #e-code").val();
//         var s_type = $("form #s-type").val();
//         var e_type = $("form #e-type").val();
//         var startDate = $("form #startDate").val();
//         var endDate = $("form #endDate").val();
//         var puchi1 = $("form #puchi1").val();
//         var puchi2 = $("form #puchi2").val();
//         var puchi3 = $("form #puchi3").val();
//         var puchi4 = $("form #puchi4").val();

//         var studentData = {
//                 'username'      : username,
//                 's-place'       : s_place,
//                 's-skill'       : s_skill,
//                 's-field'       : s_field,
//                 's-hakenplace'  : s_hakenplace,
//                 's-obayashi'    : s_obayashi,
//                 's-code'        : s_code,
//                 's-type'        : s_type,
//                 'e-place'       : e_place,
//                 'e-skill'       : e_skill,
//                 'e-field'       : e_field,
//                 'e-hakenplace'  : e_hakenplace,
//                 'e-obayashi'    : e_obayashi,
//                 'e-code'        : e_code,
//                 'e-type'        : e_type,
//                 'startDate'     : startDate,
//                 'endDate'       : endDate,
//                 'puchi1'        : puchi1,
//                 'puchi2'        : puchi2,
//                 'puchi3'        : puchi3,
//                 'puchi4'        : puchi4
//              };
//              if(id){
//                 $.ajax({
//                     url: "../foreignStudents/saveData",
//                     async:true,
//                     type: 'post',
//                     data:{_token: CSRF_TOKEN, message:"updateStudentById", id : id, studentData : studentData},
//                     success :function(data) {
//                         console.log(data);
//                         if(data['isUpdated']){
//                             alert("情報更新しました。");
//                             location.reload();
//                         }
//                     },
//                     error:function(err){
//                         console.log(err);
//                         alert("情報更新に失敗しました。\n管理者に問い合わせてください。");
//                     }
//                 });   

//             }else{
//                 $.ajax({
//                     url: "../foreignStudents/saveData",
//                     async:true,
//                     type: 'post',
//                     data:{_token: CSRF_TOKEN, message:"insertStudent", studentData : studentData},
//                     success :function(data) {
//                         if(data['isInserted']){
//                             alert("情報入力しました。");
//                             location.reload();
//                         }
//                     },
//                     error:function(err){
//                         console.log(err);
//                         alert("情報入力に失敗しました。\n管理者に問い合わせてください。");
//                     }
//                 }); 
//             }

//     }else{
//         alert("登録データはありません！");
//     }
// }

function SaveStudent() {
    console.log("留学生情報入力スタート");
    if (ValidateInput()) {
        //tb_personalに入力する項目
        var first_name = $("#firstname").val();
        var last_name = $("#lastname").val();
        var first_name_kana = $("#firstnameKana").val();
        var last_name_kana = $("#lastnameKana").val();
        var email = $("#email").val();

        var branch_id = $("#e-place").val();
        var dept = $("#e-obayashi").val();
        var genzai_code = $("#e-code").val();
        var position = $("#position").val();
        var company_id = $("#companyId").val();
        var isStudyAbraod = 1;
        var isSpeedCourse = $("#isSpeedCourse").val();
        var isC3User = $("#isC3User").val();

        //tb_studentsに入力する項目
        var genzai_skill = $("#e-skill").val();
        var genzai_field = $("#e-field").val();
        var genzai_type = $("#e-type").val();

        var s_skill = $("#s-skill").val();
        var s_field = $("#s-field").val();
        var s_type = $("#s-type").val();
        var s_branch_id = $("#s-place").val();
        var s_dept_name = $("#s-obayashi").val();
        var s_code = $("#s-code").val();
        //Get s-haken-company-id 
        var s_haken_company_type_id = $("#s-companyType").val();
        if (s_haken_company_type_id == 10) {
            var s_haken_company_name = ($("#s-hakenplaceSelectBox option:selected").text() == '選択してください') ? "" : $("#s-hakenplaceSelectBox option:selected").text();
        }
        else {
            var s_haken_company_name = $("#s-hakenplace").val();
        }



        var startDate = $("#startDate").val();
        var endDate = $("#endDate").val();
        var puchi1 = $("#puchi1").val();
        var puchi2 = $("#puchi2").val();
        var puchi3 = $("#puchi3").val();
        var puchi4 = $("#puchi4").val();

        //tb_hakenに入力する項目
        var genzai_haken_company_type_id = $("#e-companyType").val();
        if (genzai_haken_company_type_id == 10) {
            var genzai_haken_company_name = ($("#e-hakenplaceSelectBox option:selected").text() == '選択してください') ? "" : $("#e-hakenplaceSelectBox option:selected").text();
        }
        else {
            var genzai_haken_company_name = $("#e-hakenplace").val();
        }


        var studentData = {
            first_name: first_name,
            last_name: last_name,
            first_name_kana: first_name_kana,
            last_name_kana: last_name_kana,
            email: email,
            branch_id: branch_id,
            dept: dept,
            genzai_code: genzai_code,
            genzai_skill: genzai_skill,
            genzai_field: genzai_field,
            genzai_type: genzai_type,
            genzai_haken_company_type_id: genzai_haken_company_type_id,
            genzai_haken_company_name: genzai_haken_company_name,
            position: position,
            company_id: company_id,
            s_branch_id: s_branch_id,
            s_dept_name: s_dept_name,
            s_code: s_code,
            s_haken_company_type_id: s_haken_company_type_id,
            s_haken_company_name: s_haken_company_name,
            s_skill: s_skill,
            s_field: s_field,
            s_type: s_type,
            startDate: startDate,
            endDate: endDate,
            puchi1: puchi1,
            puchi2: puchi2,
            puchi3: puchi3,
            puchi4: puchi4,
            isStudyAbraod: isStudyAbraod,
            isSpeedCourse: isSpeedCourse,
            isC3User: isC3User

        }

        console.log(studentData);

        $.ajax({
            url: "../foreignStudents/saveData",
            async: true,
            type: 'post',
            data: { _token: CSRF_TOKEN, message: "insertForeignStudent", studentData: studentData },
            success: function(data) {
                console.log(data);
                alert("Successful");
                location.reload();
            },
            error: function(err) {
                return;
                console.log(err);
                alert("情報入力に失敗しました。\n管理者に問い合わせてください。");
            }
        });

    }

}

function UpdateStudent() {
    var id = $("form #studentId").val();
    if (id) {
        //tb_studentsに入力する項目
        var s_branch_id = $("#s-place").val();
        var s_dept_name = $("#s-obayashi").val();
        var s_code = $("#s-code").val();
        //Get s-haken-company-id
        var s_haken_company_type_id = $("#s-companyType").val();
        if (s_haken_company_type_id == 10) {
            var s_haken_company_name = ($("#s-hakenplaceSelectBox option:selected").text() == '選択してください') ? "" : $("#s-hakenplaceSelectBox option:selected").text();
        }
        else {
            var s_haken_company_name = $("#s-hakenplace").val();
        }

        var genzai_skill = $("#e-skill").val();
        var genzai_field = $("#e-field").val();
        var genzai_type = $("#e-type").val();

        var s_skill = $("#s-skill").val();
        var s_field = $("#s-field").val();
        var s_type = $("#s-type").val();

        var startDate = $("#startDate").val();
        var endDate = $("#endDate").val();
        var puchi1 = $("#puchi1").val();
        var puchi2 = $("#puchi2").val();
        var puchi3 = $("#puchi3").val();
        var puchi4 = $("#puchi4").val();

        var studentData = {
            genzai_skill: genzai_skill,
            genzai_field: genzai_field,
            genzai_type: genzai_type,
            s_branch_id: s_branch_id,
            s_dept_name: s_dept_name,
            s_code: s_code,
            s_haken_company_type_id: s_haken_company_type_id,
            s_haken_company_name: s_haken_company_name,
            s_skill: s_skill,
            s_field: s_field,
            s_type: s_type,
            startDate: startDate,
            endDate: endDate,
            puchi1: puchi1,
            puchi2: puchi2,
            puchi3: puchi3,
            puchi4: puchi4
        }
        $.ajax({
            url: "../foreignStudents/saveData",
            async: true,
            type: 'post',
            data: { _token: CSRF_TOKEN, message: "updateForeignStudent", studentData: studentData, userId: id },
            success: function(data) {
                console.log(data);
                alert("Successful");
                location.reload();
            },
            error: function(err) {
                return;
                console.log(err);
                alert("情報変更に失敗しました。\n管理者に問い合わせてください。");
            }
        });
    }
    else {
        alert("まず変更したい留学生を選んでください！");
    }

}

function ValidateInput() {
    if (!$("#firstname").val()) {
        return ShowErrorMessage("氏名：姓");
    }
    else if (!$("#lastname").val()) {
        return ShowErrorMessage("名");
    }
    else if (!$("#email").val()) {
        return ShowErrorMessage("メールアドレス");
    }
    return true;
}

function ShowErrorMessage(message) {
    var fix_msg = "を入力してください。";
    $("#err_message").html(message + fix_msg);
    return false;
}


//新規作成ボタン
function CreateNewStudent() {
    location.reload();

}

//削除ボタン
// function DeleteStudent(){

//     var id = $("form #studentId").val();
//     if(id){
//         $.ajax({
//                 url: "../foreignStudents/deleteData",
//                 async:true,
//                 type: 'post',
//                 data:{_token: CSRF_TOKEN, message:"deleteStudentById", id : id},
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

function DeleteStudent() {
    var id = $("form #studentId").val();
    if (id) {
        var confirmResult = confirm("削除してもよろしいですか。");
        if (confirmResult) {
            console.log(id);
            $.ajax({
                url: "../foreignStudents/deleteData",
                async: true,
                type: 'post',
                data: { _token: CSRF_TOKEN, message: "deleteStudentById", userId: id },
                success: function(data) {
                    if (data) {
                        alert("削除しました！");
                        window.location.reload();
                    }
                },
                error: function(err) {
                    console.log(err);
                    alert("情報変更に失敗しました。\n管理者に問い合わせてください。");
                }
            });
        }
        else {

        }
    }
    else {
        alert("まず削除したい留学生を選んでください！");
    }
}

function DisplayPopup() {
    $("#createUser").css({ visibility: "visible", opacity: "1" });
}

function ClosePopup() {
    $("#createUser").css({ visibility: "hidden", opacity: "0" });
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
        return false;
    }
    else if ($('#companyType :selected').text() == '' || $('#companyType :selected').text() == '選択してください') {
        $("#companyTypeSelect_err").html("権限を選択してください。");
        return false;
    }
    else {
        return true;
    }
}


// function LoadHakenCompanyList(companyTypeId, companyNameTextBoxId) {
//     console.log(companyTypeId)
//     var companyNameList = [];
//     $.ajax({
//         url: "../application/getData",
//         async: true,
//         type: 'post',
//         data: { _token: CSRF_TOKEN, message: "getCompanyList", companyTypeId: companyTypeId },
//         success: function(data) {
//             console.log(data)
//             if (data) {
//                 $.each(data, function(key, value) {
//                     $.each(value, function(tname, tdata) {
//                         if (tdata['name']) {
//                             if (!companyNameList.includes(tdata['name'])) {
//                                 if (tdata['name'] !== "大林組") {
//                                     companyNameList.push(tdata['name']);
//                                 }
//                             }
//                         }

//                     });

//                 });
//                 createHakenCompanyListSelectBox(companyNameList, companyNameTextBoxId);

//             }
//         },
//         error: function(err) {
//             console.log(err);
//         }
//     });

// }
