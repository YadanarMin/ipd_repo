/* ajax通信トークン定義 */
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

var allstoreDataGroupA = [];
var allstoreDataGroupB = [];
var g_allstoreData = [];
var accessable_item_index_list = [];
var g_allstore_offset = 0;
var g_pre_filter = {};
var g_total_record_num = 0;

var allstore_db_item_list = ["id", "a_pj_code", "a_kouji_kikan_code", "a_shiten", "a_kakudo", "a_pj_name", "a_kouji_kubun", "a_kouji_type", "a_ukeoikin", "a_youto1", "a_youto2", "a_sekou_basyo", "a_sekkei_state", "a_sekkei", "a_kouzou", "a_kaisuu", "a_tijo", "a_tika", "a_ph", "a_nobe_menseki", "a_tyakkou", "a_syunkou", "b_pj_state", "b_sekkei_state", "b_sekou_state", "b_jiyuu_kinyuu", "b_pj_name", "b_tmp_pj_name", "b_shiten", "b_kakudo", "b_hattyuusya", "b_sekkeisya1", "b_sekkeisya2", "b_sekou_basyo", "b_youto", "b_kouzou", "b_kaisuu", "b_tika", "b_tijo", "b_ph", "b_nobe_menseki", "b_tousuu", "b_syotyou", "b_kouji_jimusyo", "b_kouji_katyou", "b_kouji_buka", "b_eigyou_tantousya", "b_eigyou_tantoubu", "b_isyou_sekkei", "b_isyou_syozoku", "b_isyou_model", "b_isyou_model_syozoku", "b_kouzou_sekkei", "b_kouzou_syozoku", "b_kouzou_model", "b_kouzou_model_syozoku", "b_setubi_kuutyou_sekkei", "b_setubi_kuutyou_syozoku", "b_setubi_kuutyou_model", "b_setubi_kuutyou_model_syozoku", "b_setubi_eisei_sekkei", "b_setubi_eisei_syozoku", "b_setubi_eisei_model", "b_setubi_eisei_model_syozoku", "b_setubi_denki_sekkei", "b_setubi_denki_syozoku", "b_setubi_denki_model", "b_setubi_denki_model_syozoku", "b_ss_designer_name", "b_ss_designer_dept", "b_ss_modeler_name", "b_ss_modeler_dept", "b_sekou_tantou", "b_sekou_syozoku", "b_seisan_modeler_name", "b_seisan_modeler_dept", "b_seisan_gijutu_tantou", "b_seisan_gijutu_syozoku", "b_sekisan_mitumori_tantou", "b_sekisan_mitumori_syozoku", "b_bim_maneka_tantou", "b_bim_maneka_syozoku", "b_ipd_center_tantou", "b_ipd_center_syozoku", "b_partner_company", "b_partner_company_dept", "b_bim_m", "b_bim_manager_dept", "b_bim_coordinator_tantou", "b_bim_coordinator_syozoku", "b_hattyuu_keitai_kentiku", "b_hattyuu_keitai_setubi", "b_nyuusatu_jiki", "b_nyuusatu_kettei_jiki", "b_koutei_kihonsekkei_start", "b_koutei_kihonsekkei_end", "b_koutei_jissisekkei_start", "b_koutei_jissisekkei_end", "b_koutei_sekkei_model_start", "b_koutei_sekkei_model_end", "b_koutei_kakunin_sinsei_start", "b_koutei_kakunin_sinsei_end", "b_koutei_sekisan_model_tougou_start", "b_koutei_sekisan_model_tougou_end", "b_koutei_kouji_juujisya_kettei_start", "b_koutei_kouji_juujisya_kettei_end", "b_koutei_genba_koutei_kettei_start", "b_koutei_genba_koutei_kettei_end", "b_koutei_kouji_start", "b_koutei_kouji_end", "b_handover_start", "b_handover_end", "b_modeling_state", "b_bikou", "c_bikou", "c_order_status", "c_additional_item"];

var allstore_headers = ["No.", "iPDコード", "工事基幹コード", "支店", "確度(A)", "プロジェクト名称(A)", "工事区分", "工事区分名", "請負金", "建物用途1", "建物用途2", "施工場所", "設計State", "設計", "構造(A)", "階数(A)", "地上(A)", "地下(A)", "PH(A)", "延べ面積(A)", "着工", "竣工", "プロジェクト稼働状況", "設計段階", "施工段階", "自由記入欄", "プロジェクト名称(B)", "BIM360プロジェクト名称", "支店(B)", "確度(B)", "発注者", "設計者(B)", "設計者(B)支店", "施工場所(B)", "用途(B)", "構造(B)", "階数(B)", "地下(B)", "地上(B)", "PH(B)", "延べ面積(B)", "棟数", "工事事務所所長_氏名", "工事事務所_組織", "工事部担当者_氏名", "工事部担当者_組織", "営業担当者_氏名", "営業担当者_組織", "意匠設計担当者_氏名", "意匠設計担当者_組織", "意匠モデラー_氏名", "意匠モデラー_組織", "構造設計担当者_氏名", "構造設計担当者_組織", "構造モデラー_氏名", "構造モデラー_組織", "設備空調担当者_氏名", "設備空調担当者_組織", "設備空調モデラー_氏名", "設備空調モデラー_組織", "設備衛生担当者_氏名", "設備衛生担当者_組織", "設備衛生モデラー_氏名", "設備衛生モデラー_組織", "設備電気担当者_氏名", "設備電気担当者_組織", "設備電気モデラー_氏名", "設備電気モデラー_組織", "生産設計担当者_氏名", "生産設計担当者_組織", "生産設計モデラー_氏名", "生産設計モデラー_組織", "施工管理担当者_氏名", "施工管理担当者_組織", "生産モデラー_氏名", "生産モデラー_組織", "生産技術担当者_氏名", "生産技術担当者_組織", "積算担当者_氏名", "積算担当者_組織", "BIMマネ課担当者_氏名", "BIMマネ課担当者_組織", "iPDセンター担当者_氏名", "iPDセンター担当者_組織", "協力会社担当者_氏名", "協力会社担当者_組織", "BIMマネージャー_氏名", "BIMマネージャー_組織", "BIMコーディネーター_氏名", "BIMコーディネーター_組織", "建築工事発注形態", "設備工事発注形態", "入札start", "入札end", "基本設計start", "基本設計end", "実施設計start", "実施設計end", "設計モデル作成start", "設計モデル作成end", "確認申請start", "確認申請end", "積算見積モデル統合・追記修正start", "積算見積モデル統合・追記修正end", "工事従事者決定start", "工事従事者決定end", "現場工程決定start", "現場工程決定end", "工事start", "工事end", "引渡しstart", "引渡しend", "モデリング会社区分", "備考1(B)", "備考2(C)", "受注状況", "追加項目"];

$(document).ready(function() {

    var login_user_id = $("#hidLoginID").val();
    var img_src = "../public/image/JPG/会員証のアイコン素材.jpeg";
    var url = "allstore/index";
    var content_name = "全店物件情報";
    recordAccessHistory(login_user_id, img_src, url, content_name);

    g_allstore_offset = 0;
    g_pre_filter = {};
    allstoreDataGroupA = [];
    allstoreDataGroupB = [];
    getAllstoreManagementInfo();
    getAllstoreUpdateHistory();

    // $("#txtiPDCodeSearch,#txtProjectNameSearch,#txtBranchSearch,#txtiPDinChargeSearch,#txtKoujiTypeSearch").keyup(delay(function(e) {
    //     console.log('Time elapsed!', this.value);
    //     TableRowFilter();
    // }, 500));

    $("#tbCheckBox").on('change', "input[type='checkbox']", function(e) {
        var pj_code = $(this).val();
        var check_id = $(this).attr("id");

        if (check_id === "display_report_flag") {
            var flag = 0;
            if ($(this).prop("checked") == true) {
                flag = 1;
            }
            UpdateDisplayReportFlag(pj_code, flag);

        }
        else if (check_id === "output_bimmaneka_flag") {
            var flag = 0;
            if ($(this).prop("checked") == true) {
                flag = 1;
            }
            UpdateOutputSettingsBIMmaneka(pj_code, flag);


        }
        else if (check_id === "output_setubibim_flag") {
            var flag = 0;
            if ($(this).prop("checked") == true) {
                flag = 1;
            }
            UpdateOutputSettingsSetubiBIM(pj_code, flag);
        }
    });

    $("#koujiTypeSelect").change(function() {
        if ($(this).val() != "") {
            $("#koujiTypeSelect").css("color", "black");
        }
        else {
            $("#koujiTypeSelect").css("color", "darkgrey");
        }
    });

});

function UpdateDisplayReportFlag(pjCode, flag) {

    $.ajax({
        url: "../allstore/updateFlag",
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "update_report_flag", projectCode: pjCode, flag: flag },
        success: function(data) {
            if (data.includes("success")) {
                console.log("succssfully updated!");

                for (var i = 0; i < g_allstoreData.length; i++) {
                    var storeData = g_allstoreData[i];

                    if (storeData["a_pj_code"] === pjCode) {
                        g_allstoreData[i]["display_report_flag"] = flag;
                        break;
                    }
                }
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function UpdateOutputSettingsBIMmaneka(pjCode, flag) {
    $.ajax({
        url: "../allstore/updateFlag",
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "update_bimmaneka_flag", projectCode: pjCode, flag: flag },
        success: function(data) {
            if (data.includes("success")) {
                console.log("succssfully updated!");

                for (var i = 0; i < g_allstoreData.length; i++) {
                    var storeData = g_allstoreData[i];

                    if (storeData["a_pj_code"] === pjCode) {
                        g_allstoreData[i]["output_bimmaneka_flag"] = flag;
                        break;
                    }
                }
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function UpdateOutputSettingsSetubiBIM(pjCode, flag) {
    $.ajax({
        url: "../allstore/updateFlag",
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "update_setubibim_flag", projectCode: pjCode, flag: flag },
        success: function(data) {
            if (data.includes("success")) {
                console.log("succssfully updated!");

                for (var i = 0; i < g_allstoreData.length; i++) {
                    var storeData = g_allstoreData[i];

                    if (storeData["a_pj_code"] === pjCode) {
                        g_allstoreData[i]["output_setubibim_flag"] = flag;
                        break;
                    }
                }
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function wrapSaveAllStore() {

    var personal_id = $("#hidLoginID").val();
    if (personal_id === 7) {
        saveAllstoreManagementInfo();
    }
    else {
        alert("メンテナンス中...");
    }
}

function saveAllstoreManagementInfo() {
    ShowLoading();
    console.log("saveAllstoreManagementInfo start");

    $.ajax({
        url: "../allstore/getBOXData",
        type: 'post',
        data: { _token: CSRF_TOKEN },
        success: function(data) {
            // console.log(data);
            if (data.includes("success")) {
                console.log("succssfully updated!");
                HideLoading();
                location.reload();

            }
            else if (data.includes("no_token")) {
                alert("BOXにログインされていないため更新できませんでした。");

            }
            else if (data.includes("no_authority")) {
                alert("権限がありません。");

            }
            else {
                //NOP
            }

            HideLoading();

        },
        error: function(err) {
            console.log(err);
        }
    });
}

function saveAllstoreManagementInfo_old() {
    ShowLoading();

    var doneCount = 0;

    if (allstoreDataGroupB.length == 0) {
        alert("[ERROR] No update data.\nファイルを選択してから更新してください。");
        HideLoading();
        return;
    }

    if (allstoreDataGroupA.length !== allstoreDataGroupB.length) {
        alert("[ERROR] Invalid Number of Data.\n読込ファイルのフォーマットを確認してください。");
        HideLoading();
        return;
    }

    console.log("allstoreDataGroupB.length");
    console.log(allstoreDataGroupB.length);

    for (let i = 0; i < allstoreDataGroupB.length; i++) {

        $.ajax({
            url: "../allstore/saveData",
            async: false,
            type: 'post',
            data: { _token: CSRF_TOKEN, storeDataA: allstoreDataGroupA[i], storeDataB: allstoreDataGroupB[i] },
            success: function(data) {
                console.log(data);

                doneCount++;
                if (doneCount === allstoreDataGroupB.length) {
                    HideLoading();
                    alert("保存完了しました。");
                    location.reload();
                }
            },
            error: function(err) {
                HideLoading();
                console.log(err);
            }
        });
    }
}

function getAllstoreManagementInfo() {
    ShowLoading();

    if (g_allstore_offset === 0) {
        getRecordNumByAllstore(g_pre_filter);
    }

    $.ajax({
        url: "../allstore/getData",
        type: 'post',
        data: { _token: CSRF_TOKEN, filter: g_pre_filter, allstore_offset: g_allstore_offset },
        success: function(data) {
            if (data != null) {
                console.log(data);

                if (g_allstore_offset === 0) {
                    g_allstoreData = data;
                }
                else {
                    g_allstoreData = g_allstoreData.concat(data);
                }

                GetAccessSetting();
                displayAllstoreManagementInfo(g_allstoreData);
                g_allstore_offset += 50;

                window.setTimeout(displayRecordNum, 100);

                HideLoading();
            }
        },
        error: function(err) {
            console.log(err);
        }
    });

}

function displayAllstoreManagementInfo(allstoreData) {

    $("#tbUser tr th").remove();
    $("#tbUser thead").remove();
    $("#tbUser tr td").remove();
    $("#tbUser tbody").remove();

    $("#tbCheckBox tr th").remove();
    $("#tbCheckBox thead").remove();
    $("#tbCheckBox tr td").remove();
    $("#tbCheckBox tbody").remove();
    var newRow = "";
    var newChkRow = "";
    newRow += "<thead>";
    newChkRow += "<thead>";

    newRow += "<tr>";
    var accessable_item_array = [];
    if (accessable_item_index_list.length > 0)
        accessable_item_array = accessable_item_index_list.split(",");

    if (accessable_item_array <= 0) {
        var userName = $("#hidUserName").val();
        $("#tbUser").append("<tr><td><label style='padding:10px;color:darkblue'>全店物件情報アクセス権限がないため、表示できません。<label></td></tr>");
        return;
    }
    $.each(allstore_headers, function(key, value) {
        if (accessable_item_array.includes(key.toString())) {
            // alert(accessable_item_index_list+"\n"+key);
            newRow += "<th>" + value + "</th>";
        }
    });

    newRow += "</tr>";
    newRow += "</thead>";


    newChkRow += "<tr>";
    newChkRow += "<th>";
    newChkRow += "&nbsp";
    newChkRow += "<span class='icon-stack'><i id='sortPjReport' class='fa fa-sort-down icon-stack-3x' onclick='sortTablePJReportChecked();'></i></span>";
    newChkRow += "&nbsp";

    var login_user_id = $("#hidLoginID").val();
    if (login_user_id === '7' || login_user_id === '1' || login_user_id === '8') {
        newChkRow += "<i onclick='DisplayPopup();'>案件報告設定</i>";
    }
    else {
        newChkRow += "<i>案件報告設定</i>";
    }
    newChkRow += "</th>";


    // newChkRow +=        "<th>";
    // newChkRow +=            "&nbsp";
    // newChkRow +=            "<span class='icon-stack'><i id='sortBIMManeka' class='fa fa-sort-down icon-stack-3x' onclick='sortTableBIMmanekaChecked();'></i></span>";
    // newChkRow +=            "&nbsp";
    // newChkRow +=            "BIMマネ管理表出力";
    // newChkRow +=            "&nbsp";
    // newChkRow +=            "<a href='javascript:void(0)' onClick='OutputBIMmaneka();'>";
    // newChkRow +=                "<img class='appIconBig' src='../public/image/downloadfree.png' alt='' height='15' width='15' />";
    // newChkRow +=            "</a>";
    // newChkRow +=            "&nbsp";
    // newChkRow +=        "</th>";
    // newChkRow +=        "<th>"
    // newChkRow +=            "&nbsp";
    // newChkRow +=            "<span class='icon-stack'><i id='sortSetubiBIM' class='fa fa-sort-down icon-stack-3x' onclick='sortTableSetubiBIMChecked();'></i></span>";
    // newChkRow +=            "&nbsp";
    // newChkRow +=            "設備BIMシート出力";
    // newChkRow +=            "&nbsp";
    // newChkRow +=            "<a href='javascript:void(0)' onClick='OutputSetubiBIM();'>";
    // newChkRow +=                "<img class='appIconBig' src='../public/image/downloadfree.png' alt='' height='15' width='15' />";
    // newChkRow +=            "</a>";
    // newChkRow +=            "&nbsp";
    // newChkRow +=        "</th>";
    newChkRow += "</tr>";
    newChkRow += "</thead>";

    if (g_allstoreData.length == 0) {

        newRow += "<tbody>";
        newRow += "<tr>";
        newRow += "<td></td>";

        var COLUMN_NUM = 52;
        for (var k = 0; k < COLUMN_NUM; k++) {

            if (k === 0) {
                newRow += "<td>No Data</td>";
            }
            else {
                newRow += "<td></td>";
            }
        }
        newRow += "</tr>";
        newRow += "</tbody>";

    }
    else {

        newRow += "<tbody>";
        newRow += "</tbody>";

        newChkRow += "</tbody>";
        newChkRow += "<tbody>";
    }

    $("#tbUser").append(newRow);
    $("#tbCheckBox").append(newChkRow);

    createTblBodyAllStore(allstoreData);
}

function createTblBodyAllStore(allstoreData) {

    var flag_cnt = 0;
    var newRow = "";
    var newChkRow = "";
    var accessable_item_array = [];
    if (accessable_item_index_list.length > 0)
        accessable_item_array = accessable_item_index_list.split(",");
    for (var i = 0; i < allstoreData.length; i++) {

        newRow += "<tr>";

        var storeData = allstoreData[i];
        var pjCode = storeData["a_pj_code"];
        $.each(accessable_item_array, function(key, index) { //allstore_db_item_list
            var itemName = allstore_db_item_list[index];
            //var itemName = allstore_db_item_list[key];
            newRow += "<td>" + storeData[itemName] + "</td>";
            //newitemChkRow += "<th><input type='checkbox' name='"+value+"' id='"+key+"'<th>";

        });

        newRow += "</tr>";

        newChkRow += "<tr>";
        if (storeData["display_report_flag"] === 1) {
            newChkRow += "<td><input type='hidden' value='" + storeData["a_pj_name"] + "$" + storeData["b_ipd_center_tantou"] + "'><input id='display_report_flag' type='checkbox' value='" + storeData["a_pj_code"] + "' checked></td>";
            flag_cnt++;
        }
        else {
            newChkRow += "<td><input type='hidden' value='" + storeData["a_pj_name"] + "$" + storeData["b_ipd_center_tantou"] + "'><input id ='display_report_flag' type='checkbox' value='" + storeData["a_pj_code"] + "'></td>";
        }
        // if(storeData["output_bimmaneka_flag"] == 1){
        //     newChkRow += "<td><input type='hidden' value='"+storeData["a_pj_name"]+"$"+storeData["b_ipd_center_tantou"]+"'><input id='output_bimmaneka_flag' type='checkbox' value='"+storeData["a_pj_code"]+"' checked></td>";
        // }else{
        //     newChkRow += "<td><input type='hidden' value='"+storeData["a_pj_name"]+"$"+storeData["b_ipd_center_tantou"]+"'><input id='output_bimmaneka_flag' type='checkbox' value='"+storeData["a_pj_code"]+"'></td>";
        // }
        // if(storeData["output_setubibim_flag"] == 1){
        //     newChkRow += "<td><input type='hidden' value='"+storeData["a_pj_name"]+"$"+storeData["b_ipd_center_tantou"]+"'><input id='output_setubibim_flag' type='checkbox' value='"+storeData["a_pj_code"]+"' checked></td>";
        // }else{
        //     newChkRow += "<td><input type='hidden' value='"+storeData["a_pj_name"]+"$"+storeData["b_ipd_center_tantou"]+"'><input id='output_setubibim_flag' type='checkbox' value='"+storeData["a_pj_code"]+"'></td>";
        // }
        newChkRow += "</tr>";

    }

    $("#tbUser > tbody > tr").remove();
    $("#tbCheckBox > tbody > tr").remove();
    $("#tbUser tbody").append(newRow);
    $("#tbCheckBox tbody").append(newChkRow);
}

function deleteAllstoreManagementInfo() {

    $.ajax({
        url: "../allstore/deleteData",
        type: 'post',
        data: { _token: CSRF_TOKEN },
        success: function() {
            alert("全てのデータ削除が完了しました。");
            location.reload();
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function OutputBIMmaneka() {
    window.location = "/iPD/allstore/excelDownloadBIMmane";
}

function OutputSetubiBIM() {
    window.location = "/iPD/allstore/excelDownloadfaciBIM";
}

var isSortedPJReport = false;

function sortTablePJReportChecked() {
    console.log("sortTablePJReportChecked start");

    if (isSortedPJReport) {
        createTblBodyAllStore(g_allstoreData);
        $("#sortPjReport").removeClass("sortOn");
        isSortedPJReport = false;
        console.log("sortTablePJReportChecked end(default)");
        return;
    }

    var checkedPJCodeList = [];

    $("#tbCheckBox tbody tr").each(function(index) {
        var curInput = $(this).find("#display_report_flag");
        var pj_code = curInput.val();
        var id = curInput.attr("id");
        var isChecked = curInput.prop("checked");
        // console.log(pj_code+"\n"+id+"\n"+isChecked);

        if (isChecked) {
            checkedPJCodeList.push(pj_code);
        }
    });
    // console.log(checkedPJCodeList);

    if (checkedPJCodeList.length === 0) {
        return; //not sort
    }

    var sortedAllstoreData = [];
    var checkedData = [];
    var allStoreWithoutCheckedData = [];
    for (var i = 0; i < g_allstoreData.length; i++) {
        var storeData = g_allstoreData[i];

        if (checkedPJCodeList.indexOf(storeData["a_pj_code"]) !== -1) {
            checkedData.push(storeData);
        }
        else {
            allStoreWithoutCheckedData.push(storeData);
        }
    }

    sortedAllstoreData = checkedData.concat(allStoreWithoutCheckedData);
    // console.log(sortedAllstoreData);
    createTblBodyAllStore(sortedAllstoreData);
    isSortedPJReport = true;
    $("#sortPjReport").addClass("sortOn");
    console.log("sortTablePJReportChecked end");
}

var isSortedBIMmaneka = false;

function sortTableBIMmanekaChecked() {
    console.log("sortTableBIMmanekaChecked start");

    if (isSortedBIMmaneka) {
        createTblBodyAllStore(g_allstoreData);
        $("#sortBIMManeka").removeClass("sortOn");
        isSortedBIMmaneka = false;
        console.log("sortTablePJReportChecked end(default)");
        return;
    }

    var checkedPJCodeList = [];

    $("#tbCheckBox tbody tr").each(function(index) {
        var curInput = $(this).find("#output_bimmaneka_flag");
        var pj_code = curInput.val();
        var id = curInput.attr("id");
        var isChecked = curInput.prop("checked");
        // console.log(pj_code+"\n"+id+"\n"+isChecked);

        if (isChecked) {
            checkedPJCodeList.push(pj_code);
        }
    });
    // console.log(checkedPJCodeList);

    if (checkedPJCodeList.length === 0) {
        return; //not sort
    }

    var sortedAllstoreData = [];
    var checkedData = [];
    var allStoreWithoutCheckedData = [];
    for (var i = 0; i < g_allstoreData.length; i++) {
        var storeData = g_allstoreData[i];

        if (checkedPJCodeList.indexOf(storeData["a_pj_code"]) !== -1) {
            checkedData.push(storeData);
        }
        else {
            allStoreWithoutCheckedData.push(storeData);
        }
    }

    sortedAllstoreData = checkedData.concat(allStoreWithoutCheckedData);
    // console.log(sortedAllstoreData);
    createTblBodyAllStore(sortedAllstoreData);
    $("#sortBIMManeka").addClass("sortOn");
    isSortedBIMmaneka = true;
    console.log("sortTableBIMmanekaChecked end");
}

var isSortedSetubiBIM = false;

function sortTableSetubiBIMChecked() {
    console.log("sortTableSetubiBIMChecked start");

    if (isSortedSetubiBIM) {
        createTblBodyAllStore(g_allstoreData);
        $("#sortSetubiBIM").removeClass("sortOn");
        isSortedSetubiBIM = false;
        console.log("sortTablePJReportChecked end(default)");
        return;
    }

    var checkedPJCodeList = [];

    $("#tbCheckBox tbody tr").each(function(index) {
        var curInput = $(this).find("#output_setubibim_flag");
        var pj_code = curInput.val();
        var id = curInput.attr("id");
        var isChecked = curInput.prop("checked");
        // console.log(pj_code+"\n"+id+"\n"+isChecked);

        if (isChecked) {
            checkedPJCodeList.push(pj_code);
        }
    });
    // console.log(checkedPJCodeList);

    if (checkedPJCodeList.length === 0) {
        return; //not sort
    }

    var sortedAllstoreData = [];
    var checkedData = [];
    var allStoreWithoutCheckedData = [];
    for (var i = 0; i < g_allstoreData.length; i++) {
        var storeData = g_allstoreData[i];

        if (checkedPJCodeList.indexOf(storeData["a_pj_code"]) !== -1) {
            checkedData.push(storeData);
        }
        else {
            allStoreWithoutCheckedData.push(storeData);
        }
    }

    sortedAllstoreData = checkedData.concat(allStoreWithoutCheckedData);
    // console.log(sortedAllstoreData);
    createTblBodyAllStore(sortedAllstoreData);
    $("#sortSetubiBIM").addClass("sortOn");
    isSortedSetubiBIM = true;
    console.log("sortTableSetubiBIMChecked end");
}

function getAllstoreUpdateHistory() {

    $.ajax({
        url: "../allstore/getHistory",
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "box_load_history" },
        success: function(data) {
            // console.log("getHistory complete.");
            // console.log(data);

            if (data != null || data.length != 0) {

                var latestIndex = data.length - 1;
                var appendStr = "";
                var userName = data[latestIndex]["name"];
                var updating = data[latestIndex]["updating"];
                var updateHistory = userName + " さんが " + updating + " にBOX取込";

                appendStr += "<div class='dropdown'>";
                appendStr += "<div class='select'>";
                appendStr += "<i class='fa fa-chevron-left'></i>";
                appendStr += "<span>" + updateHistory + "</span>";
                appendStr += "</div>";
                appendStr += "<input type='hidden' name='gender'>";
                appendStr += "<ul class='dropdown-menu'>";

                for (var i = latestIndex - 1; i >= 0; i--) {
                    var tmp_id = data[i]["id"];
                    var tmp_userName = data[i]["name"];
                    var tmp_updating = data[i]["updating"];
                    var tmp_updateHistory = tmp_userName + " さんが " + tmp_updating + " にBOX取込";

                    appendStr += "<li id='updateHistory" + tmp_id + "'>" + tmp_updateHistory + "</li>";
                }

                appendStr += "</ul>";
                appendStr += "</div>&nbsp";

                $('#update_history').append(appendStr);


                /*Dropdown Menu*/
                $('.dropdown').click(function() {
                    $(this).attr('tabindex', 1).focus();
                    $(this).toggleClass('active');
                    $(this).find('.dropdown-menu').slideToggle(300);
                });
                $('.dropdown').focusout(function() {
                    $(this).removeClass('active');
                    $(this).find('.dropdown-menu').slideUp(300);
                });
                // $('.dropdown .dropdown-menu li').click(function () {
                //     $(this).parents('.dropdown').find('span').text($(this).text());
                //     $(this).parents('.dropdown').find('input').attr('value', $(this).attr('id'));
                // });
                /*End Dropdown Menu*/

                // $('.dropdown-menu li').click(function () {
                //   var input = '<strong>' + $(this).parents('.dropdown').find('input').val() + '</strong>',
                //       msg = '<span class="msg">Hidden input value: ';
                //   $('.msg').html(msg + input + '</span>');
                // });
            }

        },
        error: function(err) {
            console.log(err);
        }
    });

}

function GetAccessSetting() {

    var login_user_id = $("#hidLoginID").val();
    if (login_user_id == undefined || login_user_id == "") return;
    $.ajax({
        url: "/iPD/projectAccessSetting/getData",
        type: 'post',
        async: false,
        data: { _token: CSRF_TOKEN, message: "get_access_setting", access_user_id: login_user_id },
        success: function(data) {
            // console.log(data);
            if (data != null) {
                accessable_item_index_list = data[0]["accessable_item"];
            }
        },
        error: function(err) {
            console.log(err);
        }
    });

}

// function TableRowFilter() {

//     $("#tbUser tbody tr").each(function() {
//         $(this).show();
//     });
//     $("#tbCheckBox tbody tr").each(function() {
//         $(this).show();
//     });

//     var ipdCode_index = "";
//     var pj_name_index = "";
//     var pj_name_b_index = "";
//     var pj_name_c_index = "";
//     var branch_index = "";
//     var incharge_index = "";
//     var koujiType_index = "";
//     $("#tbUser thead tr th").each(function() {
//         var thText = $(this).text();
//         if (thText.trim() == "iPDコード") {
//             ipdCode_index = $(this).index();
//         }
//         if (thText.trim() == "プロジェクト名称(A)") {
//             pj_name_index = $(this).index();
//         }
//         if (thText.trim() == "プロジェクト名称(B)") {
//             pj_name_b_index = $(this).index();
//         }
//         if (thText.trim() == "BIM360プロジェクト名称") {
//             pj_name_c_index = $(this).index();
//         }
//         if (thText.trim() == "支店") {
//             branch_index = $(this).index();
//         }
//         if (thText.trim() == "iPDセンター担当者_氏名") {
//             incharge_index = $(this).index();
//         }
//         if (thText.trim() == "工事区分名") {
//             koujiType_index = $(this).index();
//         }
//     });

//     var txtiPDCodeSearch = $("#txtiPDCodeSearch").val().trim();
//     var txtProjectNameSearch = $("#txtProjectNameSearch").val().trim();
//     var txtBranchSearch = $("#txtBranchSearch").val().trim();
//     var txtiPDinChargeSearch = $("#txtiPDinChargeSearch").val().trim();
//     var txtKoujiTypeSearch = $("#txtKoujiTypeSearch").val();
//     if (txtiPDCodeSearch !== "" ||
//         txtProjectNameSearch !== "" ||
//         txtBranchSearch !== "" ||
//         txtiPDinChargeSearch !== "" ||
//         txtKoujiTypeSearch !== "") {

//         $("#tbUser tbody tr").each(function(index) {

//             var ipdCode = (ipdCode_index === "") ? "" : $(this).find("td:eq(" + ipdCode_index + ")").text();
//             var projectName = (pj_name_index === "") ? "" : $(this).find("td:eq(" + pj_name_index + ")").text();
//             var projectName_b = (pj_name_b_index === "") ? "" : $(this).find("td:eq(" + pj_name_b_index + ")").text();
//             var projectName_c = (pj_name_c_index === "") ? "" : $(this).find("td:eq(" + pj_name_c_index + ")").text();
//             var branch = (branch_index === "") ? "" : $(this).find("td:eq(" + branch_index + ")").text();;
//             var inCharge = (incharge_index === "") ? "" : $(this).find("td:eq(" + incharge_index + ")").text();
//             var koujiType = (koujiType_index === "") ? "" : $(this).find("td:eq(" + koujiType_index + ")").text();

//             if ((!ipdCode.includes(txtiPDCodeSearch)) ||
//                 (!projectName.includes(txtProjectNameSearch) && !projectName_b.includes(txtProjectNameSearch) && !projectName_c.includes(txtProjectNameSearch)) ||
//                 !branch.includes(txtBranchSearch) ||
//                 !inCharge.includes(txtiPDinChargeSearch) ||
//                 !koujiType.includes(txtKoujiTypeSearch)) {

//                 $("#tbCheckBox tbody tr").eq(index).hide();
//                 $(this).hide();
//             }
//             else {
//                 $("#tbCheckBox tbody tr").eq(index).show();
//                 $(this).show();
//             }
//         });

//     }
//     else {
//         $("#tbUser tbody tr").each(function() {
//             $(this).show();
//         });
//         $("#tbCheckBox tbody tr").each(function() {
//             $(this).show();
//         });
//     }

//     $("#tbUser >tbody >tr:visible:odd").css("background-color", "#f2f2f2");
//     $("#tbUser >tbody >tr:visible:even").css("background-color", "#fff");
// }

function delay(callback, ms) {
    var timer = 0;
    return function() {
        var context = this,
            args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function() {
            callback.apply(context, args);
        }, ms || 0);
    };
}

function DisplayPopup() {

    $.ajax({
        url: "../allstore/getHistory",
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "report_flag_history" },
        success: function(data) {
            console.log(data);

            if (data != null || data.length != 0) {

                var latestIndex = data.length - 1;
                var appendStr = "";
                var userName = data[latestIndex]["personal_name"];
                var updating = data[latestIndex]["update_time"];
                var pjName = data[latestIndex]["a_pj_name"];
                var state = "";
                if (data[latestIndex]["cur_flag"] === 0 && data[latestIndex]["new_flag"] === 1) {
                    state = "チェックを入れました。"
                }
                else if (data[latestIndex]["cur_flag"] === 1 && data[latestIndex]["new_flag"] === 0) {
                    state = "チェックをはずしました。"
                }

                var updateHistory = userName + " さんが " + updating + " に " + pjName + " の " + state;

                appendStr += "<div class='dropdownp'>";
                appendStr += "<div class='select'>";
                appendStr += "<i class='fa fa-chevron-left'></i>";
                appendStr += "<span>" + updateHistory + "</span>";
                appendStr += "</div>";
                appendStr += "<input type='hidden' name='gender'>";
                appendStr += "<ul class='dropdownp-menu'>";

                for (var i = latestIndex - 1; i >= 0; i--) {
                    var tmp_id = data[i]["id"];
                    var tmp_userName = data[i]["personal_name"];
                    var tmp_updating = data[i]["update_time"];
                    var tmp_pjName = data[i]["a_pj_name"];
                    var tmp_state = "";
                    if (data[i]["cur_flag"] === 0 && data[i]["new_flag"] === 1) {
                        tmp_state = "チェックを入れました。"
                    }
                    else if (data[i]["cur_flag"] === 1 && data[i]["new_flag"] === 0) {
                        tmp_state = "チェックをはずしました。"
                    }

                    var tmp_updateHistory = tmp_userName + " さんが " + tmp_updating + " に " + tmp_pjName + " の " + tmp_state;

                    appendStr += "<li id='updateHistory" + tmp_id + "'>" + tmp_updateHistory + "</li>";
                }

                appendStr += "</ul>";
                appendStr += "</div>&nbsp";

                $("#report_flag_history div").remove();
                $('#report_flag_history').append(appendStr);


                /*Dropdown Menu*/
                $('.dropdownp').click(function() {
                    $(this).attr('tabindex', 1).focus();
                    $(this).toggleClass('active');
                    $(this).find('.dropdownp-menu').slideToggle(300);
                });
                $('.dropdownp').focusout(function() {
                    $(this).removeClass('active');
                    $(this).find('.dropdownp-menu').slideUp(300);
                });
                // $('.dropdownp .dropdownp-menu li').click(function () {
                //     $(this).parents('.dropdownp').find('span').text($(this).text());
                //     $(this).parents('.dropdownp').find('input').attr('value', $(this).attr('id'));
                // });
                /*End Dropdown Menu*/

                // $('.dropdownp-menu li').click(function () {
                //   var input = '<strong>' + $(this).parents('.dropdownp').find('input').val() + '</strong>',
                //       msg = '<span class="msg">Hidden input value: ';
                //   $('.msg').html(msg + input + '</span>');
                // });

                $("#reportFlagHistoryPopup").css({ visibility: "visible", opacity: "1" });
            }

        },
        error: function(err) {
            console.log(err);
        }
    });

}

function ClosePopup() {
    $("#reportFlagHistoryPopup").css({ visibility: "hidden", opacity: "0" });
}

function searchStoreInfo() {
    ShowLoading();

    var ipdCode = $("#txtiPDCodeSearch").val();
    var koujikikanCode = $("#txtKoujiKikanCodeSearch").val();
    var pjName = $("#txtProjectNameSearch").val();
    var branch = $("#txtBranchSearch").val();
    var koujiType = $("#koujiTypeSelect option:selected").text();
    koujiType = koujiType === "工事区分名" ? "" : koujiType;
    var ipdIncharge = $("#txtiPDinChargeSearch").val();
    var tmpArray = {
        "ipd_code": ipdCode,
        "kikan_code": koujikikanCode,
        "pj_name": pjName,
        "branch_name": branch,
        "kouji_type": koujiType,
        "ipd_tantou": ipdIncharge
    };

    var isMatch = array_equal(g_pre_filter, tmpArray);
    if (!isMatch) {
        g_allstore_offset = 0;
    }

    if (g_allstore_offset === 0) {
        getRecordNumByAllstore(tmpArray);
    }

    $.ajax({
        url: "../allstore/getData",
        type: 'post',
        data: { _token: CSRF_TOKEN, filter: tmpArray, allstore_offset: g_allstore_offset },
        success: function(data) {
            if (data != null) {
                // console.log(data);

                g_allstoreData = data;
                GetAccessSetting();
                displayAllstoreManagementInfo(g_allstoreData);

                g_allstore_offset += 50;
                g_pre_filter = tmpArray;

                window.setTimeout(displayRecordNum, 100);

                HideLoading();
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function readAllAllstoreInfo() {

    ShowLoading();

    $.ajax({
        url: "../allstore/getData",
        type: 'post',
        data: { _token: CSRF_TOKEN, filter: g_pre_filter },
        success: function(data) {
            if (data != null) {
                console.log(data);

                g_allstoreData = data;
                GetAccessSetting();
                displayAllstoreManagementInfo(g_allstoreData);

                var recordNumStr = g_total_record_num.toString() + " 件表示中 (" + g_total_record_num.toString() + "件中)";
                $("#recordNum").html(recordNumStr);

                $('#readPartOfAllstore').attr('disabled', 'disabled');
                $("#readAllAllstore").attr("disabled", "disabled");

                HideLoading();
            }
        },
        error: function(err) {
            console.log(err);
        }
    });

}

function array_equal(a, b) {

    var a_len = Object.keys(a).length;
    var b_len = Object.keys(b).length;
    if (a_len != b_len || a_len === 0 || b_len === 0) {
        return false;
    }

    if (a["ipd_code"] != b["ipd_code"] ||
        a["kikan_code"] != b["kikan_code"] ||
        a["pj_name"] != b["pj_name"] ||
        a["branch_name"] != b["branch_name"] ||
        a["kouji_type"] != b["kouji_type"] ||
        a["ipd_tantou"] != b["ipd_tantou"]) {

        return false;
    }

    return true;
}

function getRecordNumByAllstore(filterArray) {

    var recordNum = 0;
    $.ajax({
        url: "../allstore/getRecordNum",
        type: 'post',
        data: { _token: CSRF_TOKEN, filter: filterArray },
        success: function(data) {
            if (data != null) {
                console.log(data);
                g_total_record_num = data[0]["num"];
            }
        },
        error: function(err) {
            console.log(err);
        }
    });

}

function displayRecordNum() {
    var recordNumStr = "";
    if (g_total_record_num <= g_allstore_offset) {
        recordNumStr = g_total_record_num.toString() + " 件表示中 (" + g_total_record_num.toString() + "件中)";
        $('#readPartOfAllstore').attr('disabled', 'disabled');
        $("#readAllAllstore").attr("disabled", "disabled");
    }
    else {
        recordNumStr = g_allstore_offset.toString() + " 件表示中 (" + g_total_record_num.toString() + "件中)";
        $('#readPartOfAllstore').removeAttr('disabled');
        $("#readAllAllstore").removeAttr("disabled");
    }
    $("#recordNum").html(recordNumStr);
}
