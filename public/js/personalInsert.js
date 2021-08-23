/* ajax通信トークン定義 */
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
var g_personalData = {};
var g_allStoreData = [];
var g_authorityData = [];
var g_isOverwrite = false;

$(document).ready(function() {

    var login_user_id = $("#hidLoginID").val();
    var img_src = "../public/image/JPG/鍵のクローズアイコン素材.jpeg";
    var url = "personalInsert/index";
    var content_name = "人員情報設定";
    recordAccessHistory(login_user_id, img_src, url, content_name);

    g_isOverwrite = false;

    $("#txtCode_err,#txtEmail").on("keyup", function(event) {
        var id = $(this).attr('id');
        if ($(this).val() !== "") {
            $("#" + id + "_err").html('');
        }
    });

    $("#companyNameSelect").change(function() {
        var selectedCompanyName = $("option:selected", this).text();
        if (selectedCompanyName === "大林組") {
            $("#branchNameSelect").prop("disabled", false);
            $("#txtDepartment").prop("disabled", false);
            $("#isAdditionalPostSelect").prop("disabled", false);

            var tmp_dept = $("#hidDeptList").val();
            var deptList = JSON.parse(tmp_dept);
            var deptNameArray = [];
            for (var i = 0; i < deptList.length; i++) {
                deptNameArray.push(deptList[i]["name"]);
            }
            $("#txtDepartment").autocomplete({
                source: deptNameArray,
                minLength: 0
            }).bind('focus', function() { $(this).autocomplete("search"); });

        }
        else {
            $("#branchNameSelect").prop("disabled", true);
            $("#txtDepartment").prop("disabled", true);
            $("#isAdditionalPostSelect").prop("disabled", true);

            var defStr = "選択してください";
            selectedOption("branchNameSelect", defStr);
            $("#txtDepartment").val("");
            selectedOption("isAdditionalPostSelect", defStr);
        }
    });

    $("#branchNameSelect").change(function() {
        var branch_id = $("option:selected", this).val();
        var branch_name = $("option:selected", this).text();
        if (branch_name === "選択してください") {
            reloadAllDeptList();
        }
        else {
            reloadDeptListByBranchId(branch_id);
        }

    });

    $("#companyTypeSelect").change(function() {
        if (!g_isOverwrite) {
            return;
        }

        var company_type_id = parseInt($("option:selected", this).val());
        var company_type_name = $("option:selected", this).text();
        var companyData = JSON.parse($("#hidCompanyList").val());

        var appendText = "";
        appendText += "<option value=''>選択してください</option>";

        for (var i = 0; i < companyData.length; i++) {

            if (company_type_name === "選択してください") {
                appendText += "<option value='" + companyData[i]["id"] + "'>" + companyData[i]["name"] + "</option>";
            }
            else {
                var curCompanyId = 0;
                curCompanyId = companyData[i]["company_type_id"];

                if (curCompanyId === company_type_id) {
                    appendText += "<option value='" + companyData[i]["id"] + "'>" + companyData[i]["name"] + "</option>";
                }
            }
        }

        $("select#companyNameSelect option").remove();
        $("#companyNameSelect").append(appendText);
    });

    $("#hakenCompanyTypeSelect").change(function() {
        if (!g_isOverwrite) {
            return;
        }

        var company_type_id = parseInt($("option:selected", this).val());
        var company_type_name = $("option:selected", this).text();

        var companyData = JSON.parse($("#hidCompanyList").val());

        var appendText = "";
        appendText += "<option value=''>選択してください</option>";

        for (var i = 0; i < companyData.length; i++) {

            if (company_type_name === "選択してください") {
                if (companyData[i]["id"] !== 1) {
                    appendText += "<option value='" + companyData[i]["id"] + "'>" + companyData[i]["name"] + "</option>";
                }
            }
            else {
                var curCompanyId = 0;
                curCompanyId = companyData[i]["company_type_id"];

                if (curCompanyId === company_type_id) {
                    appendText += "<option value='" + companyData[i]["id"] + "'>" + companyData[i]["name"] + "</option>";
                }
            }
        }

        $("select#hakenCompanyNameSelect option").remove();
        $("#hakenCompanyNameSelect").append(appendText);
    });

    $("#txtAutoLoad").val("");
});

function createCompanyTypeSelect() {

    $.ajax({
        url: "/iPD/company/getType",
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "getAllCompanyType", typeId: 0, typeName: "" },
        success: function(result) {
            console.log(result);

            var appendText = "";
            var appendHakenText = "";
            appendText += "<option value='' selected>選択してください</option>";
            appendHakenText += "<option value='' selected>選択してください</option>";

            for (var i = 0; i < result.length; i++) {
                appendText += "<option value='" + result[i]["id"] + "'>" + result[i]["name"] + "</option>";

                if (result[i]["id"] !== 1) {
                    appendHakenText += "<option value='" + result[i]["id"] + "'>" + result[i]["name"] + "</option>";
                }
            }

            $("select#companyTypeSelect option").remove();
            $("#companyTypeSelect").append(appendText);

            $("select#hakenCompanyTypeSelect option").remove();
            $("#hakenCompanyTypeSelect").append(appendHakenText);
        },
        error: function(err) {
            console.log(err);
        }
    });

}

function reloadAllDeptList() {

    var deptNameList = [];
    $.ajax({
        url: "../application/getData",
        async: true,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "getAllDeptList", branchId: 0 },
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

                $("#txtDepartment").autocomplete({
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

function reloadDeptListByBranchId(branch_id) {

    if (branch_id === 0) {
        return;
    }

    var deptNameList = [];
    $.ajax({
        url: "../application/getData",
        async: true,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "getDeptList", branchId: branch_id },
        success: function(data) {
            // console.log(data)
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

                $("#txtDepartment").autocomplete({
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

function CreateUser() {

    var isValid = Validataion();
    console.log(isValid);
    if (!isValid) return;
    var personalData = createPersonalDataSet();
    console.log(personalData);

    $.ajax({
        url: "../personalInsert/saveData",
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "onePerson", personalData: personalData },
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

function createPersonalDataSet() {

    var result = {};

    /*** for tb_personal ***/
    result["mail"] = $("#txtEmail").val();
    result["first_name"] = $("#txtFirstName").val();
    result["last_name"] = $("#txtLastName").val();
    result["first_name_kana"] = $("#txtFirstNameKana").val();
    result["last_name_kana"] = $("#txtLastNameKana").val();
    result["phone"] = $("#txtPhoneNumber").val();
    result["work_location"] = $("#txtAWorkLocation").val();
    result["code"] = $("#txtCode").val();
    result["position"] = $("#txtPosition").val();
    result["outsideCall"] = $("#txtOutsideCall").val();
    result["fax"] = $("#txtFAX").val();
    result["isC3User"] = 0;
    result["isStudyAbraod"] = 0;
    result["isSpeedCourse"] = 0;

    var tmp_branch_id = $("#branchNameSelect").children("option:selected").val();
    result["branch_id"] = tmp_branch_id === "" ? 0 : parseInt(tmp_branch_id);

    var tmp_company_id = $("#companyNameSelect").children("option:selected").val();
    result["company_id"] = tmp_company_id === "" ? 0 : parseInt(tmp_company_id);

    //dept_id
    var dept_name = $("#txtDepartment").val();
    result["dept_name"] = dept_name;
    result["dept_id"] = 0;
    var deptList = JSON.parse($("#hidDeptList").val());
    for (var i = 0; i < deptList.length; i++) {
        if (deptList[i]["name"] === dept_name && deptList[i]["branch_id"] == result["branch_id"]) {
            result["dept_id"] = parseInt(deptList[i]["id"]);
            break;
        }
    }

    //isAdditionalPost
    var isAdditionalPostVal = $("#isAdditionalPostSelect").children("option:selected").val();
    if (isAdditionalPostVal === "兼務") {
        result["isAdditionalPost"] = 1;
    }
    else {
        result["isAdditionalPost"] = 0;
    }

    //comtractType
    var tmpContractStr = $("#contractTypeSelect").children("option:selected").val();
    result["contract_type"] = parseInt(tmpContractStr);

    /*** for tb_haken ***/
    var tmpHakenCompanyId = $("#hakenCompanyNameSelect").children("option:selected").val();
    result["haken_company_id"] = tmpHakenCompanyId === "" ? 0 : parseInt(tmpHakenCompanyId);

    return result;
}

function Validataion() {
    if ($("#txtEmail").val() == "") {
        $("#err_message").html("メールアドレスを入力してください。");
        $("#txtEmail").focus();
        return false;
    }
    else if ($("#txtFirstName").val() == "" || $("#txtLastName").val() == "") {
        $("#err_message").html("氏名を選択してください。");
        if ($("#txtFirstName").val() == "") {
            $("#txtFirstName").focus();
        }
        else {
            $("#txtLastName").focus();
        }

        return false;
    }
    else if ($('select[id="companyNameSelect"] option:selected').text() === "" || $('select[id="companyNameSelect"] option:selected').text() === "選択してください") {
        $("#err_message").html("企業名を選択してください。");
        $("#companyNameSelect").focus();

        return false;
    }
    else if ($('select[id="contractTypeSelect"] option:selected').text() === "" || $('select[id="contractTypeSelect"] option:selected').text() === "選択してください") {
        $("#err_message").html("個人種別を選択してください。");
        $("#contractTypeSelect").focus();

        return false;
    }
    else if ($('select[id="companyNameSelect"] option:selected').text().includes("大林組") && $('select[id="branchNameSelect"] option:selected').text().includes("選択して")) {
        console.log($('select[id="branchNameSelect"] option:selected').html());
        $("#err_message").html("支店名を選択してください。");
        $("#branchNameSelect").focus();

        return false;
    }
    else if ($('select[id="companyNameSelect"] option:selected').text().includes("大林組") && $("#txtDepartment").val().trim() == "") {
        $("#err_message").html("組織名を選択してください。");

        return false;
    }
    else if (g_isNewCreate) {
        var result = DuplicateEmailChecking($("#txtEmail").val());
        if (result != "" && result[0]['count'] >= 1) {
            $("#err_message").html("メールアドレスが既に存在しています。\n別のメールアドレスを使ってください。");
            $("#txtEmail").focus();
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

function DuplicateEmailChecking(email) {
    var data = "";
    $.ajax({
        url: "/iPD/login/account/get",
        type: 'post',
        async: false,
        data: { _token: CSRF_TOKEN, message: "check_duplicate_email", mail: email },
        success: function(result) {
            console.log(result);
            data = result;
        },
        error: function(err) {
            console.log(err);
        }
    });
    return data;
}

function UpdateCompanyNameSelect() {

    $.ajax({
        url: "../company/getData",
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "getAllCompany", companyId: 0, companyName: "" },
        success: function(result) {
            console.log(result);
            if (result.length > 0) {

                //companyNameSelect
                var appendText = "";
                appendText += "<option value=''>選択してください</option>";

                for (var i = 0; i < result.length; i++) {
                    appendText += "<option value='" + result[i]["id"] + "'>" + result[i]["name"] + "</option>";
                }

                $("select#companyNameSelect option").remove();
                $("#companyNameSelect").append(appendText);

                //hakenCompanyNameSelect
                appendText = "";
                appendText += "<option value=''>選択してください</option>";

                for (var i = 0; i < result.length; i++) {
                    if (result[i]["id"] !== 1) {
                        appendText += "<option value='" + result[i]["id"] + "'>" + result[i]["name"] + "</option>";
                    }
                }

                $("select#hakenCompanyNameSelect option").remove();
                $("#hakenCompanyNameSelect").append(appendText);
            }
        },
        error: function(err) {
            alert(JSON.stringify(err));
            console.log(err);
        }
    });
}

function ClosePopup() {
    $("#createUser").css({ visibility: "hidden", opacity: "0" });

    if (!$("#theadName").hasClass("fixed0201index")) {
        $("#theadName").addClass("fixed0201index");
    }
    $("#tblUser tbody tr").each(function(index) {
        if (!$(this).children("td:first").hasClass("fixed02index")) {
            $(this).children("td:first").addClass("fixed02index");
        }
    });

    $("#txtAutoLoad").val("");
    $("#err_message").html("");
    g_isNewCreate = false;
    g_isOverwrite = false;
}

var g_isNewCreate = false;

function DisplayPopup(id) {

    var defOptSelectedStr = "選択してください";

    if (id != undefined || id != null) {
        g_isNewCreate = false;
        $.ajax({
            url: "../personal/getData",
            type: 'post',
            data: { _token: CSRF_TOKEN, message: "personalById", id: id },
            success: function(result) {
                console.log(result);
                if (result != null && result.length > 0) {
                    var data = result[0];
                    $("#txtCode").val(data["code"]);
                    $("#txtFirstName").val(data["first_name"]);
                    $("#txtLastName").val(data["last_name"]);
                    $("#txtFirstNameKana").val(data["first_name_kana"]);
                    $("#txtLastNameKana").val(data["last_name_kana"]);

                    $("#txtEmail").prop("disabled", true);
                    $("#txtEmail").val(data["mail"]);
                    $("#txtPosition").val(data["position"]);
                    $("#txtPhoneNumber").val(data["phone"]);
                    $("#txtOutsideCall").val(data["outsideCall"]);
                    $("#txtFAX").val(data["fax"]);
                    $("#txtAWorkLocation").val(data["work_location"]);
                    $("#txtDepartment").val(data["dept_name"]);

                    selectedOption("companyTypeSelect", data["company_type_name"]);
                    selectedOption("hakenCompanyTypeSelect", data["haken_company_type_name"]);
                    selectedOption("companyNameSelect", data["company_name"]);
                    selectedOption("hakenCompanyNameSelect", data["haken_company_name"]);

                    var tmpComtract = defOptSelectedStr;
                    if (data["contract_type"] === 1) {
                        tmpComtract = "社員";
                    }
                    else if (data["contract_type"] === 2) {
                        tmpComtract = "派遣";
                    }
                    else if (data["contract_type"] === 3) {
                        tmpComtract = "外部";
                    }
                    selectedOption("contractTypeSelect", tmpComtract);

                    if (data["company_id"] === 1) {
                        $("#branchNameSelect").prop("disabled", false);
                        $("#txtDepartment").prop("disabled", false);
                        $("#isAdditionalPostSelect").prop("disabled", false);

                        selectedOption("branchNameSelect", data["branch_name"]);
                        $("#txtDepartment").val(data["dept_name"]);

                        if (data["isAdditionalPost"] == 0) {
                            selectedOption("isAdditionalPostSelect", "本務");
                        }
                        else {
                            selectedOption("isAdditionalPostSelect", "兼務");
                        }

                    }
                    else {
                        $("#branchNameSelect").prop("disabled", true);
                        $("#txtDepartment").prop("disabled", true);
                        $("#isAdditionalPostSelect").prop("disabled", true);
                        selectedOption("branchNameSelect", defOptSelectedStr);
                        selectedOption("isAdditionalPostSelect", defOptSelectedStr);
                    }

                    g_isOverwrite = true;
                    $("#createUser").css({ visibility: "visible", opacity: "1" });
                }
            },
            error: function(err) {
                console.log(err);
            }
        });

    }
    else {
        //ユーザ新規作成
        g_isNewCreate = true;

        $("#txtCode").val("");
        $("#txtFirstName").val("");
        $("#txtLastName").val("");
        $("#txtFirstNameKana").val("");
        $("#txtLastNameKana").val("");
        $("#txtEmail").prop("disabled", false);
        $("#txtEmail").val("");
        $("#txtPosition").val("");
        $("#txtPhoneNumber").val("");
        $("#txtOutsideCall").val("");
        $("#txtFAX").val("");
        $("#txtAWorkLocation").val("");
        $("#txtDepartment").val("");

        selectedOption("companyTypeSelect", defOptSelectedStr);
        selectedOption("hakenCompanyTypeSelect", defOptSelectedStr);
        selectedOption("companyNameSelect", defOptSelectedStr);
        selectedOption("hakenCompanyNameSelect", defOptSelectedStr);
        selectedOption("branchNameSelect", defOptSelectedStr);
        selectedOption("isAdditionalPostSelect", defOptSelectedStr);
        selectedOption("contractTypeSelect", defOptSelectedStr);

        g_isOverwrite = true;
        $("#createUser").css({ visibility: "visible", opacity: "1" });
    }

    if ($("#theadName").hasClass("fixed0201index")) {
        $("#theadName").removeClass("fixed0201index");
    }
    $("#tblUser tbody tr").each(function(index) {
        if ($(this).children("td:first").hasClass("fixed02index")) {
            $(this).children("td:first").removeClass("fixed02index");
        }
    });

}

function selectedOption(id_name, option_name) {

    let $element = $('#' + id_name);
    let val = $element.find("option:contains('" + option_name + "')").val();
    $element.val(val).trigger('change');
}

function DeleteUser(id, name) {

    var result = confirm("本当に削除しますか？ 【ユーザー名:" + name + "】");
    if (result === true) {
        $.ajax({
            url: "../personalInsert/deleteData",
            type: 'post',
            data: { _token: CSRF_TOKEN, personalId: id },
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

function AutoLoadPopup() {
    $("#autoLoadPersonalData").css({ visibility: "visible", opacity: "1" });
}

function CloseAutoLoad() {
    $("#autoLoadPersonalData").css({ visibility: "hidden", opacity: "0" });
}

function LoadAutoLoadString() {
    console.log("LoadAutoLoadString start");

    var autoLoadString = $("#txtAutoLoad").val();

    var tmpArray = autoLoadString.split("\t");

    if (tmpArray.length === 0) {
        alert("フォーマットエラー.");
        return;
    }

    console.log(autoLoadString);
    console.log(tmpArray);

    var tmpName = tmpArray[1];
    var aryName = tmpName.split("　");

    var tmpCodeBranchAndDept = tmpArray[6];
    var tmpBranchCode = "";
    var tmpDeptCode = "";
    if (tmpCodeBranchAndDept.length === 6) {
        tmpBranchCode = tmpCodeBranchAndDept.substring(0, 1);
        tmpDeptCode = tmpCodeBranchAndDept.substring(1);
    }
    else if (tmpCodeBranchAndDept.length === 7) {
        tmpBranchCode = tmpCodeBranchAndDept.substring(0, 2);
        tmpDeptCode = tmpCodeBranchAndDept.substring(2);
    }
    else {
        //NOP
    }

    var tmpNameBranchAndDept = tmpArray[8];
    var aryNameBranchAndDept = tmpNameBranchAndDept.split(" ");

    var code = tmpArray[0] === "" ? "" : "U" + tmpArray[0];
    var first_name = aryName.length !== 2 ? "" : aryName[0];
    var last_name = aryName.length !== 2 ? "" : aryName[1];
    var first_name_kana = tmpArray[2];
    var last_name_kana = tmpArray[14];
    var position = tmpArray[3];
    var mail = tmpArray[4];
    var honmu = tmpArray[5];
    var branch_code = tmpBranchCode;
    var dept_code = tmpDeptCode;
    var company_name = tmpArray[7];
    var branch_name = aryNameBranchAndDept[0];
    var dept_name = aryNameBranchAndDept[1];
    var address = tmpArray[9];
    var naisen = tmpArray[10];
    var gaisen = tmpArray[11];
    var fax = tmpArray[12];

    var companyList = JSON.parse($("#hidCompanyList").val());
    var company_type_id = 0;
    for (var i = 0; i < companyList.length; i++) {
        if (companyList[i]["name"] === company_name) {
            company_type_id = companyList[i]["company_type_id"];
            break;
        }
    }

    var tmpCompanyTypeName = "";
    if (company_type_id !== 0) {
        var companyTypeList = JSON.parse($("#hidCompanyTypeList").val());
        for (var i = 0; i < companyTypeList.length; i++) {
            if (companyTypeList[i]["id"] === company_type_id) {
                tmpCompanyTypeName = companyTypeList[i]["name"];
                break;
            }
        }
    }

    if (tmpCompanyTypeName !== "") {
        selectedOption("companyTypeSelect", tmpCompanyTypeName);
    }

    selectedOption("hakenCompanyTypeSelect", "選択してください");


    if (code !== "") { $("#txtCode").val(code); }
    if (first_name !== "") { $("#txtFirstName").val(first_name); }
    if (last_name !== "") { $("#txtLastName").val(last_name); }
    if (first_name_kana !== "") { $("#txtFirstNameKana").val(first_name_kana); }
    if (last_name_kana !== "") { $("#txtLastNameKana").val(last_name_kana); }
    if (mail !== "") { $("#txtEmail").val(mail); }
    if (position !== "") { $("#txtPosition").val(position); }
    if (naisen !== "") { $("#txtPhoneNumber").val(naisen); }
    if (gaisen !== "") { $("#txtOutsideCall").val(gaisen); }
    if (fax !== "") { $("#txtFAX").val(fax); }
    if (address !== "") { $("#txtAWorkLocation").val(address); }
    if (dept_name !== "") { $("#txtDepartment").val(dept_name); }

    selectedOption("companyNameSelect", company_name);
    // selectedOption("hakenCompanyNameSelect", "");
    selectedOption("branchNameSelect", branch_name);
    selectedOption("isAdditionalPostSelect", honmu);
    selectedOption("contractTypeSelect", "社員");

    $("#autoLoadPersonalData").css({ visibility: "hidden", opacity: "0" });
}
