var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
var selectedProjectList = [];
var checkDuplicate = [];
$(document).ready(function() {

    //LoadEstimateProject();
    LoadModellingCompany();
    CheckBoxLogin();
    CheckBoxLoginToShowExistingFolder();


    $("#tblProjectList tbody").on("click", "tr", function(e) {
        var id = $(this).find($('input:checkbox')).attr('id');
        $("tr." + id).toggle(200);
    });

    $("input:checkbox[class='folder_flag']").click(function() {
        $("input:checkbox[class='folder_flag']").not(this).prop('checked', false);
    });

    var rowCount = $("#tblDuringProjectList > tbody > tr").length;
    var rowCount1 = $("#tblFinishedProjectList > tbody > tr").length;
    if (rowCount == 0) {
        $("#tblDuringProjectList tbody").append("<tr><td colspan='10' style='text-align:center'>見積中プロジェクトがありません。</td></tr>")
    }
    if (rowCount1 == 0) {
        $("#tblFinishedProjectList tbody").append("<tr><td colspan='10' style='text-align:center'>見積完了プロジェクトがありません。</td></tr>")
    }


});

function LoadEstimateProject() {
    $.ajax({
        url: "/iPD/estimate/getData",
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "get_estimate_project" },
        success: function(data) {
            if (data) {
                ShowProject(data, "tblProjectList", 'estimateProjectSelect');
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function LoadEstimateDuringProject() {
    console.log("Start")
    $.ajax({
        url: "/iPD/estimate/getData",
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "get_estimate_during_project" },
        success: function(data) {
            if (data) {
                ShowProject(data, "tblDuringProjectList", 'estimateDuringProjectSelect');
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function LoadEstimateFinishedProject() {
    $.ajax({
        url: "/iPD/estimate/getData",
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "get_estimate_finished_project" },
        success: function(data) {
            if (data) {
                ShowProject(data, "tblFinishedProjectList", 'estimateFinishedProjectSelect');
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function ShowProject(data, tblName, className) {
    console.log(data)
    var row = "";
    $.each(data, function(key, value) {
        row += "<tr>" +
            "<td><input type='checkbox' class='" + className + "' id='" + value['a_pj_code'] + "' value='" + value['a_pj_name'] + "'></td>" +
            "<td>" + value['a_pj_code'] + "</td>" +
            "<td>" + value['a_pj_name'] + "</td>" +
            "<td>" + "" + "</td>" +
            "<td>" + StringToDateFormat(value['a_tyakkou']) + "</td>" +
            "<td>" + "" + "</td>" +
            "<td>" + " " + "</td>" +
            // "<td>" + "" + "</td>" +
            "<td>" + "" + "</td>" +
            "<td>" + "" + "</td>" +
            "<td>" + "" + "</td>" +
            "</tr>";
    });
    $("#" + tblName + " tbody").empty();
    $("#" + tblName + " tbody").append(row);
}

function StringToDateFormat(str) {
    var year = str.substring(0, 4);
    var month = str.substr(4, 2);
    var day = str.substr(6, 2);
    var strDate = year + "/" + month + "/" + day;
    return strDate;
}

function ShowBoxFolder() {
    var access_token = $("#access_token_value").val();
    if (access_token) {
        var projectCodeList = [];
        var isChecked = $(".estimateProjectSelect").is(":checked");
        if (isChecked) {
            $('input:checkbox[class=estimateProjectSelect]:checked').each(function() {
                var id = $(this).attr("id");
                var pjCode = $(this).val();
                projectCodeList.push(id);
            })

            LoadEstimateProjectWithBoxFolder(projectCodeList)

        }
        else {
            alert("プロジェクトを選択してください。")
        }
    }
    else {

        alert("Boxにログインされていないため、見積フォルダーがあるかないか表示できません。")

    }

}

function LoadEstimateProjectWithBoxFolder(projectCodeList) {
    ShowLoading();
    $.ajax({
        url: "/iPD/estimate/check",
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "check_existing_folder", pj_code: projectCodeList },
        success: function(data) {
            console.log(data);

            if (data) {
                if (typeof(data) == 'string') {
                    if (data.includes('401 Unauthorized` response')) {
                        $("#box-login-check").html("BOX TOKEN の有効期限が切れたため、処理できません。BOXに再ログインしてください。");
                        HideLoading();
                    }
                }
                else {
                    console.log(data);
                    ShowProjectWithBoxFolderInfo(data);
                    HideLoading();
                }
            }
        },
        error: function(err) {
            if (err) {
                $("#box-login-check").html("接続タイムアウトしました。再ロードしてください。");
            }
            HideLoading();
            console.log(err);

        }
    });

}

function ShowProjectWithBoxFolderInfo(data) {
    var row = "";
    var projectList = data['list_of_estimate_project'];
    var boxFolderInfo = data['box_folder_info'];
    $.each(projectList, function(index, project) {
        var pj_code = project['a_pj_code'];
        if (boxFolderInfo[0][pj_code]) {
            var numOfCompany = boxFolderInfo[0][pj_code].length;
            var companyList = boxFolderInfo[0][pj_code];
            var countOfKouZouZu = 0;
            var countOfishouZu = 0;
            var countOfKoTeiHyou = 0;
            var countOfFiles = 0;
            $.each(companyList, function(index, company) {
                var itemInfo = company[2];
                var userInfo = company[1];
                // if (itemInfo[pj_code]['構造図']) {
                //     countOfKouZouZu++;
                // }
                // if (itemInfo[pj_code]['意匠図']) {
                //     countOfishouZu++;
                // }
                // if (itemInfo[pj_code]['工程']) {
                //     countOfKoTeiHyou++;
                // }
                if (itemInfo[pj_code]['秘密保持(提出)']) {
                    countOfFiles++;
                }
            });

            row += "<tr>" +
                "<td><input type='checkbox' class='estimateProjectSelect' id='" + project['a_pj_code'] + "' value='" + project['a_pj_name'] + "'></td>" +
                "<td>" + project['a_pj_code'] + "</td>" +
                "<td><div style='display: flex; justify-content: space-between'>" + project['a_pj_name'] + "<span class='glyphicon glyphicon-triangle-bottom' value='" + pj_code + "' ></span></div></td>" +
                "<td>" + numOfCompany + "</td>" +
                "<td>" + StringToDateFormat(project['a_tyakkou']) + "</td>" +
                // "<td>" + countOfKouZouZu + "/" + numOfCompany + "</td>" +
                // "<td>" + countOfishouZu + "/" + numOfCompany + "</td>" +
                // "<td>" + countOfKoTeiHyou + "/" + numOfCompany + "</td>" +
                "<td>" + "" + "</td>" +
                "<td>" + "" + "</td>" +
                "<td>" + countOfFiles + "/" + numOfCompany + "</td>" +
                "<td>" + "" + "</td>" +
                "<td>" + "" + "</td>" +
                "</tr>";
            $.each(companyList, function(index, company) {
                var companyName = company[0];
                var itemInfo = company[2];
                var userInfo = company[1];
                console.log(itemInfo)
                // var kou_zou_zu = itemInfo[pj_code]['構造図'] ? '〇' : 　'×';
                // var isho_zu = itemInfo[pj_code]['意匠図'] ? '〇' : 　'×';
                // var kou_ji_hyou = itemInfo[pj_code]['工程'] ? '〇' : 　'×';
                var box_latest_access_person = userInfo[pj_code]['created_user'];
                var box_access_date = userInfo[pj_code]['created_date'];
                var secret_file = itemInfo[pj_code]['秘密保持(提出)'] ? '〇' : 　'×';
                row += "<tr class='" + pj_code + "' style='display :none; background: #eee'>" +
                    "<td>" + "" + "</td>" +
                    "<td>" + "" + "</td>" +
                    "<td>" + companyName + "</td>" +
                    "<td>" + "〇" + "</td>" +
                    "<td>" + "" + "</td>" +
                    // "<td>" + kou_zou_zu + "</td>" +
                    // "<td>" + isho_zu + "</td>" +
                    // "<td>" + kou_ji_hyou + "</td>" +
                    "<td>" + box_latest_access_person + "</td>" +
                    "<td>" + box_access_date + "</td>" +
                    "<td>" + secret_file + "</td>" +
                    "<td>" + " " + "</td>" +
                    "<td>" + " " + "</td>" +
                    "</tr>";
            });


        }
        else {
            row += "<tr>" +
                "<td><input type='checkbox' class='estimateProjectSelect' id='" + project['a_pj_code'] + "' value='" + project['a_pj_name'] + "'></td>" +
                "<td>" + project['a_pj_code'] + "</td>" +
                "<td>" + project['a_pj_name'] + "</td>" +
                "<td>" + "" + "</td>" +
                "<td>" + StringToDateFormat(project['a_tyakkou']) + "</td>" +
                "<td>" + "" + "</td>" +
                "<td>" + " " + "</td>" +
                // "<td>" + "" + "</td>" +
                "<td>" + "" + "</td>" +
                "<td>" + "" + "</td>" +
                "<td>" + "" + "</td>" +
                "</tr>";
        }

    });
    $("#tblProjectList tbody").empty();
    $("#tblProjectList tbody").append(row);
}

function GoToProjectSetting() {
    var win = window.open("/iPD/estimateSetting/index/newTab", '_blank');
    win.focus();
}

function MoveToEstimate() {
    var projectCodeList = [];
    var isChecked = $(".estimateProjectSelect").is(":checked");
    if (isChecked) {
        $('input:checkbox[class=estimateProjectSelect]:checked').each(function() {
            var id = $(this).attr("id");
            var pjCode = $(this).val();
            projectCodeList.push(id);
        })

        $.ajax({
            url: "/iPD/estimate/updateData",
            type: 'post',
            data: { _token: CSRF_TOKEN, message: "move_to_estimatechuu", pj_code: projectCodeList },
            success: function(data) {
                if (data) {
                    window.location.reload();
                }
            },
            error: function(err) {
                console.log(err);
            }
        });

    }
    else {
        alert("プロジェクトを選択してください。")
    }
}

function MoveToEstimateFinished() {
    var projectCodeList = [];
    var isChecked = $(".estimateDuringProjectSelect").is(":checked");
    if (isChecked) {
        $('input:checkbox[class=estimateDuringProjectSelect]:checked').each(function() {
            var id = $(this).attr("id");
            var pjCode = $(this).val();
            projectCodeList.push(id);
        })

        $.ajax({
            url: "/iPD/estimate/updateData",
            type: 'post',
            data: { _token: CSRF_TOKEN, message: "move_to_estimate_finished", pj_code: projectCodeList },
            success: function(data) {
                if (data) {
                    window.location.reload();
                }
            },
            error: function(err) {
                console.log(err);
            }
        });

    }
    else {
        alert("プロジェクトを選択してください。")
    }
}

function EstimateMainPage() {
    window.location.href = "/iPD/estimate/index";
}

function EstimateProjectSelect() {
    var isChecked = $(".estimateProjectSelect").is(":checked");
    if (isChecked) {
        $('input:checkbox[class=estimateProjectSelect]:checked').each(function() {
            var id = $(this).attr("id");
            var pjCode = $(this).val();
            selectedProjectList.push(id);
        })

        window.open('/iPD/estimate/project/' + selectedProjectList, '_blank');

    }
    else {
        alert("プロジェクトを選択してください。")
    }
}

function LoadModellingCompany() {
    $.ajax({
        url: "/iPD/estimate/getData",
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "get_modelling_company" },
        success: function(data) {
            if (data) {
                console.log(data);
                ShowListOfCompany(data);
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function ShowListOfCompany(data) {
    var row = "";
    $.each(data, function(key, value) {
        row += "<tr>" +
            "<td><input type='checkbox' class='selectedModellingCompany' value='" + value['name'] + "' id='" + value['id'] + "'>  " + value['name'] + "</td>";
        "</tr>";
    });
    $("#tblModellingCompany tbody").empty();
    $("#tblModellingCompany tbody").append(row);

}

function AddToList() {
    console.log("Adding");
    var isChecked = $(".selectedModellingCompany").is(":checked");
    if (isChecked) {
        $('input:checkbox[class=selectedModellingCompany]:checked').each(function() {
            var id = $(this).attr('id');
            var name = $(this).val();
            console.log(name);
            if (!checkDuplicate.includes(name)) {
                checkDuplicate.push(name);

                var row = "<a class='list-group-item' id='" + id + "'><input type='checkbox' class='removeSelectedCompany' value='" + name + "'>  " + name + "</a>";
                $(".selectedCompany").append(row);
            }
        });
        $("#hidCompanyName").val("");
        $("#hidCompanyName").val(checkDuplicate.toString());

    }
}

function RemoveFromList() {
    var isChecked = $(".removeSelectedCompany").is(":checked");
    if (isChecked) {
        $('input:checkbox[class=removeSelectedCompany]:checked').each(function() {
            var id = $(this).parent().attr('id');
            var name = $(this).val();

            $("a#" + id + ".list-group-item").remove();
            $("#" + id + ".selectedModellingCompany").prop('checked', false);
            var filtered = checkDuplicate.filter(function(value, index, arr) {
                return value != name;
            });
            checkDuplicate = filtered;

        });

        $("#hidCompanyName").val("");
        $("#hidCompanyName").val(checkDuplicate.toString());

    }
}

//Folder Creation Screen
function CheckBoxLogin() {
    var access_token = $("#access_token").val();
    if (access_token) {
        return true;
    }
    else {
        $("#boxLoginWarning").html("※　Boxにログインされていないため、フォルダ作成ができません。");
        return false;
    }

}

//Show Existing Folder Screen
function CheckBoxLoginToShowExistingFolder() {

    var access_token = $("#access_token_value").val();
    if (access_token) {
        ShowLoading();
        LoadEstimateProject();
        LoadEstimateDuringProject();
        LoadEstimateFinishedProject();
        HideLoading();
        //LoadEstimateProjectWithBoxFolder();
    }
    else {
        ShowLoading();
        LoadEstimateProject();
        LoadEstimateDuringProject();
        LoadEstimateFinishedProject();
        HideLoading();
        $("#box-login-check").html("※　Boxにログインされていないため、見積フォルダーがあるかないか表示できません。")

    }
}

function CreateFolderInBox() {
    ShowLoading();
    var access_token = $("#access_token").val();
    var projectInfo = {};
    var folder_flag;

    var companyNameStr = $("#hidCompanyName").val();
    var ipdCodeStr = $("#hidIPDCode").val();

    var ipdCodeList = ipdCodeStr.split(",");
    var companyNameList = companyNameStr.split(",");
    for (var key of ipdCodeList) {
        projectInfo[key] = $("li#" + key).html().trim();
    }

    if ($("input:checkbox[class='folder_flag']").is(":checked")) {
        folder_flag = $("input:checkbox[class='folder_flag']:checked").val();
        console.log(folder_flag);
    }
    else {
        folder_flag = "";
    }

    console.log(companyNameList);
    console.log(JSON.stringify(projectInfo, null, 2))

    if (access_token) {
        if (companyNameStr) {
            var confirmStatus = confirm("フォルダーを作成してよろしいですか。");
            if (confirmStatus) {
                $.ajax({
                    url: "/iPD/estimate/createFolder",
                    type: 'post',
                    data: { _token: CSRF_TOKEN, message: "create_folder", project_info: projectInfo, company_list: companyNameList, folder_flag: folder_flag },
                    success: function(data) {
                        if (data.includes("success")) {
                            HideLoading();
                            alert("フォルダ作成完了しました！");

                        }
                        else if (data.includes('401 Unauthorized` response')) {
                            HideLoading();
                            alert("BOX TOKEN の有効期限が切れたため、処理できません。BOXに再ログインしてください。");
                        }
                    },
                    error: function(err) {
                        console.log(err);
                        HideLoading();
                    }
                });
            }
            else {
                HideLoading();
            }

        }
        else {
            alert("見積業者を選んでください。");
            HideLoading();
        }

    }
    else {
        $("#boxLoginWarning").html("");
        $("#boxLoginWarning").html("※　Boxにログインされていないため、フォルダ作成ができません。\n ※　Boxにログインしてください。");
        HideLoading();

    }
}

function GoToUploadPage() {
    var win = window.open("/iPD/estimate/upload", '_blank');
    win.focus();
}

//Unused function//
// function ShowExistingBoxFolder(data, pjCodeList) {
//     console.log(data);
//     console.log(pjCodeList);
//     for (var i = 0; i < pjCodeList.length; i++) {
//         var pjInfo = data[0][pjCodeList[i]];

//         if (pjInfo) {
//             var text = $("tr#" + pjCodeList[i] + " td:eq(1)").text();
//             var html = text + "<span class='glyphicon glyphicon-triangle-bottom' style='margin-left : 200px'><span>";
//             $("tr#" + pjCodeList[i] + " td:eq(1)").html(html);
//             $.each(pjInfo, function(key, value) {
//                 console.log(value);
//                 var row = "";
//                 var companyName = value[0];
//                 var itemInfo = value[1];
//                 $.each(itemInfo, function(item, item_value) {
//                     console.log(itemInfo);
//                     console.log(item_value)
//                     var kou_zou_zu = item_value['構造図'] ? '〇' : 　'×';
//                     var isho_zu = item_value['意匠図'] ? '〇' : 　'×';
//                     var kou_ji_hyou = item_value['工程'] ? '〇' : 　'×';
//                     var secret_file = item_value['秘密保持(提出)'] ? '〇' : 　'×';
//                     row += "<tr class=''>" +
//                         "<td colspan='2'>" + companyName + "</td>" +
//                         "<td>〇</td>" +
//                         "<td>" + "" + "</td>" +
//                         "<td>" + kou_zou_zu + "</td>" +
//                         "<td>" + isho_zu + "</td>" +
//                         "<td>" + kou_ji_hyou + "</td>" +
//                         "<td>" + kou_ji_hyou + "</td>" +
//                         "<td>" + " " + "</td>" +
//                         "<td>" + " " + "</td>" +
//                         "</tr>";
//                 });
//                 $(row).insertAfter($("tr#" + pjCodeList[i]));
//             })


//         }
//         else {

//         }
//     }
// }
