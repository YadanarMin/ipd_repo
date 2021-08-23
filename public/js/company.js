var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

var toggleState = {};
var g_isNewCreate = false;
var g_branchList = [];

$(document).ready(function() {

    var login_user_id = $("#hidLoginID").val();
    var img_src = "../public/image/JPG/鍵のクローズアイコン素材.jpeg";
    var url = "company/index";
    var content_name = "企業情報設定";
    recordAccessHistory(login_user_id, img_src, url, content_name);

    createCompanyTypeSelect();

    $("#txtCode_err,#txtName,#txtPassword,#txtEmail").on("keyup", function(event) {
        var id = $(this).attr('id');
        if ($(this).val() !== "") {
            $("#" + id + "_err").html('');
        }
    });

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

    if (window.location.href.includes("is_other_page")) {
        DisplayPopup();
    }

    var companyData = JSON.parse($("#hidCompanyData").val());
    for (var i = 0; i < companyData.length; i++) {
        toggleState[companyData[i]["name"]] = "close";
    }

    // console.log(JSON.parse($("#hidCompanyData").val()));
    // console.log(JSON.parse($("#hidBranchData").val()));
    // console.log(toggleState);
});

function CreateUser() {
    console.log("CreateUser start");
    var isValid = Validataion();
    console.log(isValid);
    if (!isValid) return;
    var form = $(document.forms["createCompanyForm"]).serializeArray();

    console.log(form);
    var company_type_id = getCompanyTypeId(form[1]["value"]);
    form[1]["value"] = company_type_id;
    console.log(company_type_id);
    console.log(form);

    $.ajax({
        url: "/iPD/company/saveData",
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "singleCompany", companyData: form },
        success: function(message) {

            if (message.includes("success")) {
                alert("successfully saved!");
                location.reload();
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

function ClosePopup() {
    $("#createUser").css({ visibility: "hidden", opacity: "0" });

    $("#txtName_err").html("");
    $("#companyTypeSelect_err").html("");
    $("#txtCode").prop("disabled", true);
    $("#txtPostalCode").prop("disabled", true);
    $("#txtAddress").prop("disabled", true);
}

function DisplayPopup(id) {

    if (id != undefined || id != null) {
        g_isNewCreate = false;

        $.ajax({
            url: "/iPD/company/getData",
            type: 'post',
            data: { _token: CSRF_TOKEN, message: "getCompanyById", companyId: id, companyName: "" },
            success: function(result) {
                console.log(result);
                if (result.length > 0) {
                    var data = result[0];
                    $("#txtName").val(data["name"]);
                    $("#txtIndustryType").val(data["industry_type"]);
                    $("#txtBranch").val("");
                    $("#txtCode").val("");
                    $("#txtPostalCode").val("");
                    $("#txtAddress").val("");

                    selectedCompanyTypeName(data["company_type_name"]);
                    createBranchAutoComplete(data["branchList"]);
                    g_branchList = data["branchList"];

                    $("#createUser").css({ visibility: "visible", opacity: "1" });
                }
            },
            error: function(err) {
                alert(JSON.stringify(err));
                console.log(err);
            }
        });
    }
    else {
        //企業新規作成
        g_isNewCreate = true;

        $("#txtName").val("");
        $("#txtIndustryType").val("");
        $("#txtCode").val("");
        $("#txtPostalCode").val("");
        $("#txtAddress").val("");
        $("#companyTypeSelect").val("");
        $("#txtBranch").val("");

        $("#createUser").css({ visibility: "visible", opacity: "1" });
    }
}

function createBranchAutoComplete(branchList) {

    console.log("createBranchOfficeSelect start");
    console.log(branchList);

    var branchNameList = [];

    if (branchList.length > 0) {
        for (var i = 0; i < branchList.length; i++) {
            branchNameList.push(branchList[i]["name"]);
        }
    }

    $("#txtBranch").autocomplete({
        source: branchNameList,
        minLength: 0,
        select: function(event, ui) {
            var selectedObj = ui.item;
            $(this).val(selectedObj.label);
            setPostalCodeAndAddress(selectedObj.value);

            return false;
        }
    }).bind('focus', function() { $(this).autocomplete("search"); });

}

function setPostalCodeAndAddress(branchName) {

    if (branchName === "") {
        return;
    }

    for (var i = 0; i < g_branchList.length; i++) {
        if (g_branchList[i]["name"] === branchName) {

            var code = g_branchList[i]["code"];
            var postal_code = g_branchList[i]["postal_code"];
            var address = g_branchList[i]["address"];
            $("#txtCode").val(code);
            $("#txtPostalCode").val(postal_code);
            $("#txtAddress").val(address);
            $("#txtCode").prop("disabled", false);
            $("#txtPostalCode").prop("disabled", false);
            $("#txtAddress").prop("disabled", false);
            return false;
        }
    }

}

function createCompanyTypeSelect() {

    $.ajax({
        url: "/iPD/company/getType",
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "getAllCompanyType", typeId: 0, typeName: "" },
        success: function(result) {
            console.log(result);

            var appendText = "";
            appendText += "<option value='' selected>選択してください</option>";

            for (var i = 0; i < result.length; i++) {
                appendText += "<option value='" + result[i]["name"] + "'>" + result[i]["name"] + "</option>";
            }

            $("select#companyTypeSelect option").remove();
            $("#companyTypeSelect").append(appendText);
        },
        error: function(err) {
            console.log(err);
        }
    });

}

function selectedCompanyTypeName(name) {

    let $element = $('#companyTypeSelect');
    let val = $element.find("option:contains('" + name + "')").val();
    $element.val(val).trigger('change');
}

function getCompanyTypeId(companyTypeName) {
    var company_type_id = 0;

    $.ajax({
        url: "/iPD/company/getType",
        type: 'post',
        async: false,
        data: { _token: CSRF_TOKEN, message: "getCompanyTypeByName", typeId: 0, typeName: companyTypeName },
        success: function(result) {
            if (result.length > 0) {
                var data = result[0];
                company_type_id = data["id"];
            }
        },
        error: function(err) {
            alert(JSON.stringify(err));
            console.log(err);
        }
    });

    return company_type_id;
}

function DeleteUser(id, name) {
    var result = confirm("本当に削除しますか？ 【ユーザー名:" + name + "】");
    if (result === true) {
        $.ajax({
            url: "/iPD/company/deleteData",
            type: 'post',
            data: { _token: CSRF_TOKEN, companyId: id },
            success: function(message) {
                if (message.includes("success")) {
                    location.reload();
                }
            },
            error: function(err) {
                alert(JSON.stringify(err));
                console.log(err);
            }
        });
    }
}

function DeleteBranch(id, name) {
    var result = confirm("本当に削除しますか？ 【支店名:" + name + "】");

    if (result === true) {
        $.ajax({
            url: "/iPD/company/deleteBranch",
            type: 'post',
            data: { _token: CSRF_TOKEN, branchId: id },
            success: function(message) {
                if (message.includes("success")) {
                    location.reload();
                }
            },
            error: function(err) {
                alert(JSON.stringify(err));
                console.log(err);
            }
        });
    }
}

function toggleServiceList(id, name) {
    console.log("toggleServiceList start");

    if (!toggleState[name]) {
        alert("支店が属する会社が存在しません。\n管理者に問い合わせてください。")
        return;
    }

    var preState = toggleState[name];
    if (preState === "open") {
        // すべての要素を非表示に切替
        $('table#tblUser tbody tr').each(function() {
            // trCompanyクラスが付与されていなければ非表示に切替
            if (!$(this).hasClass("trCompany")) {
                $(this).css("display", "none");
            }
        });

        toggleState[name] = "close";
    }
    else {

        // すべての要素を非表示に切替
        $('table#tblUser tbody tr').each(function() {
            // trCompanyクラスが付与されていなければ非表示に切替
            if (!$(this).hasClass("trCompany")) {
                $(this).css("display", "none");
            }
        });

        // 選択した会社のみ表示に切替
        var isToggle = false;
        var branchCnt = 0;
        $('table#tblUser tbody tr').each(function() {

            // trCompanyクラスが付与されていなければ表示に切替
            if (isToggle) {
                $(this).css("display", "table-row");
                branchCnt++;
            }

            // 表示切替開始判定
            if ($(this).hasClass("trCompany") && $(this).find(".companyTitle").text().trim() === name) {
                isToggle = true;
            }

            // 表示切替終了判定
            var tmpBranchList = JSON.parse($("#hidBranchData").val());
            var filterBranchList = [];
            for (var i = 0; i < tmpBranchList.length; i++) {
                if (tmpBranchList[i]["company_id"] === id) {
                    filterBranchList.push(tmpBranchList[i]["name"]);
                }
            }

            var branchNum = filterBranchList.length;
            if (branchCnt == branchNum) {
                isToggle = false;
                return false; //break
            }
        });

        toggleState[name] = "open";

        $("#tblUser tbody tr").each(function() {

            if ($(this).hasClass("trCompany")) {
                return true; //continue
            }

            var bgColor = $(this).css('background');
            $(this).hover(function() {
                $(this).css("background-color", "wheat");
            }, function() {
                $(this).css("background-color", bgColor);
            });
        });
    }

}
