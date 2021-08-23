/* ajax通信トークン定義 */
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

// 全店物件情報ファイル(エクセル)フォーマットのセル番号を表す定数
var TARGET_SHEETNAME = "一覧表";
var TARGET_INDEX_ROW_START = 6; // 7行 格納データの先頭行
var TARGET_INDEX_COLUMN_GROUP_START = 1; // B列 グループA内で取得を開始する先頭データ(1:PJコード)
var TARGET_INDEX_COLUMN_GROUPA_PJ = 5; // F列
var TARGET_INDEX_COLUMN_GROUPA_END = 20; // B列 グループA内で取得を開始する先頭データ(1:PJコード)
var TARGET_INDEX_COLUMN_GROUPB_START = 21; // V列 グループBの始まり
var TARGET_INDEX_COLUMN_CHK_MAX = 150; // 取得するコラム数が150を超える場合は増やす必要有

var current_set_content_id = "";
var current_right_clicked_tr = "";
var allstoreDataGroupA = [];
var allstoreDataGroupB = [];
var g_allstoreData = [];
var accessable_item_index_list = [];
var accessable_pj_code_list = [];
var accessable_model_id_list = [];
var timer = "";
var public_allstore_set_id = "";
var public_allstore_item_set_id = "";
var allstore_db_item_list = ["id", "a_pj_code", "a_kouji_kikan_code", "a_shiten", "a_kakudo", "a_pj_name", "a_kouji_kubun", "a_kouji_type", "a_ukeoikin", "a_youto1", "a_youto2", "a_sekou_basyo", "a_sekkei_state", "a_sekkei", "a_kouzou", "a_kaisuu", "a_tijo", "a_tika", "a_ph", "a_nobe_menseki", "a_tyakkou", "a_syunkou", "b_pj_state", "b_sekkei_state", "b_sekou_state", "b_jiyuu_kinyuu", "b_pj_name", "b_tmp_pj_name", "b_shiten", "b_kakudo", "b_hattyuusya", "b_sekkeisya1", "b_sekkeisya2", "b_sekou_basyo", "b_youto", "b_kouzou", "b_kaisuu", "b_tika", "b_tijo", "b_ph", "b_nobe_menseki", "b_tousuu", "b_syotyou", "b_kouji_jimusyo", "b_kouji_katyou", "b_kouji_buka", "b_eigyou_tantousya", "b_eigyou_tantoubu", "b_isyou_sekkei", "b_isyou_syozoku", "b_isyou_model", "b_isyou_model_syozoku", "b_kouzou_sekkei", "b_kouzou_syozoku", "b_kouzou_model", "b_kouzou_model_syozoku", "b_setubi_kuutyou_sekkei", "b_setubi_kuutyou_syozoku", "b_setubi_kuutyou_model", "b_setubi_kuutyou_model_syozoku", "b_setubi_eisei_sekkei", "b_setubi_eisei_syozoku", "b_setubi_eisei_model", "b_setubi_eisei_model_syozoku", "b_setubi_denki_sekkei", "b_setubi_denki_syozoku", "b_setubi_denki_model", "b_setubi_denki_model_syozoku", "b_ss_designer_name", "b_ss_designer_dept", "b_ss_modeler_name", "b_ss_modeler_dept", "b_sekou_tantou", "b_sekou_syozoku", "b_seisan_modeler_name", "b_seisan_modeler_dept", "b_seisan_gijutu_tantou", "b_seisan_gijutu_syozoku", "b_sekisan_mitumori_tantou", "b_sekisan_mitumori_syozoku", "b_bim_maneka_tantou", "b_bim_maneka_syozoku", "b_ipd_center_tantou", "b_ipd_center_syozoku", "b_partner_company", "b_partner_company_dept", "b_bim_m", "b_bim_manager_dept", "b_bim_coordinator_tantou", "b_bim_coordinator_syozoku", "b_hattyuu_keitai_kentiku", "b_hattyuu_keitai_setubi", "b_nyuusatu_jiki", "b_nyuusatu_kettei_jiki", "b_koutei_kihonsekkei_start", "b_koutei_kihonsekkei_end", "b_koutei_jissisekkei_start", "b_koutei_jissisekkei_end", "b_koutei_sekkei_model_start", "b_koutei_sekkei_model_end", "b_koutei_kakunin_sinsei_start", "b_koutei_kakunin_sinsei_end", "b_koutei_sekisan_model_tougou_start", "b_koutei_sekisan_model_tougou_end", "b_koutei_kouji_juujisya_kettei_start", "b_koutei_kouji_juujisya_kettei_end", "b_koutei_genba_koutei_kettei_start", "b_koutei_genba_koutei_kettei_end", "b_koutei_kouji_start", "b_koutei_kouji_end", "b_handover_start", "b_handover_end", "b_modeling_state", "b_bikou", "c_bikou", "c_order_status", "c_additional_item"];

var allstore_headers = ["No.", "iPDコード", "工事基幹コード", "支店", "確度(A)", "プロジェクト名称(A)", "工事区分", "工事区分名", "請負金", "建物用途1", "建物用途2", "施工場所", "設計State", "設計", "構造(A)", "階数(A)", "地上(A)", "地下(A)", "PH(A)", "延べ面積(A)", "着工", "竣工", "プロジェクト稼働状況", "設計段階", "施工段階", "自由記入欄", "プロジェクト名称(B)", "BIM360プロジェクト名称", "支店(B)", "確度(B)", "発注者", "設計者(B)", "設計者(B)支店", "施工場所(B)", "用途(B)", "構造(B)", "階数(B)", "地下(B)", "地上(B)", "PH(B)", "延べ面積(B)", "棟数", "工事事務所所長_氏名", "工事事務所_組織", "工事部担当者_氏名", "工事部担当者_組織", "営業担当者_氏名", "営業担当者_組織", "意匠設計担当者_氏名", "意匠設計担当者_組織", "意匠モデラー_氏名", "意匠モデラー_組織", "構造設計担当者_氏名", "構造設計担当者_組織", "構造モデラー_氏名", "構造モデラー_組織", "設備空調担当者_氏名", "設備空調担当者_組織", "設備空調モデラー_氏名", "設備空調モデラー_組織", "設備衛生担当者_氏名", "設備衛生担当者_組織", "設備衛生モデラー_氏名", "設備衛生モデラー_組織", "設備電気担当者_氏名", "設備電気担当者_組織", "設備電気モデラー_氏名", "設備電気モデラー_組織", "生産設計担当者_氏名", "生産設計担当者_組織", "生産設計モデラー_氏名", "生産設計モデラー_組織", "施工管理担当者_氏名", "施工管理担当者_組織", "生産モデラー_氏名", "生産モデラー_組織", "生産技術担当者_氏名", "生産技術担当者_組織", "積算担当者_氏名", "積算担当者_組織", "BIMマネ課担当者_氏名", "BIMマネ課担当者_組織", "iPDセンター担当者_氏名", "iPDセンター担当者_組織", "協力会社担当者_氏名", "協力会社担当者_組織", "BIMマネージャー_氏名", "BIMマネージャー_組織", "BIMコーディネーター_氏名", "BIMコーディネーター_組織", "建築工事発注形態", "設備工事発注形態", "入札start", "入札end", "基本設計start", "基本設計end", "実施設計start", "実施設計end", "設計モデル作成start", "設計モデル作成end", "確認申請start", "確認申請end", "積算見積モデル統合・追記修正start", "積算見積モデル統合・追記修正end", "工事従事者決定start", "工事従事者決定end", "現場工程決定start", "現場工程決定end", "工事start", "工事end", "引渡しstart", "引渡しend", "モデリング会社区分", "備考1(B)", "備考2(C)", "受注状況", "追加項目"];

$(document).ready(function() {

    var login_user_id = $("#hidLoginID").val();
    var img_src = "../public/image/JPG/鍵のクローズアイコン素材.jpeg";
    var url = "projectAccessSetting/index";
    var content_name = "閲覧権限設定";
    recordAccessHistory(login_user_id, img_src, url, content_name);

    allstoreDataGroupA = [];
    allstoreDataGroupB = [];
    //GetModelDataList();
    getAllstoreManagementInfo();


    // Example usage:

    $("#txtiPDCodeSearch,#txtProjectNameSearch,#txtBranchSearch,#txtiPDinChargeSearch,#txtKoujiTypeSearch").keyup(delay(function(e) {
        console.log('Time elapsed!', this.value);
        TableRowFilter();
    }, 500));


    //body click then close right clicked popup
    $("body").click(function() {
        $("#authority_set_content").hide(100);
    });

    //left click over right clicked popup still open control
    $('#authority_set_content,.select-set').click(function(event) {
        event.stopPropagation();
    })


    $(".select-set").click(function() {
        $("#authority_set_content").show("100").css({
            top: ($(this).offset().top - 53) + "px",
            left: ($(this).offset().left - 15) + "px"
        });

        var status = $(this).attr('id');
        current_set_content_id = status;
        var authoirtySetList = "";
        if (status == "allstore_info") {
            authoirtySetList = GetAllStoreSetList();
        }
        else if (status == "allstore_item") {
            authoirtySetList = GetAllStoreItemSetList();
        }
        else if (status == "model_data") {
            authoirtySetList = GetModelDataSetList();
        }

        if (authoirtySetList != "") {
            CreateAuthoritySetTable(authoirtySetList);
        }
        //return false;
    })


    $("#tblSetList ").on("click", "tr", function() {
        var setName = $(this).find('label').html();
        var setId = $(this).find('label').attr('id');
        if (setName != undefined && setName != "" && current_set_content_id != "") {
            var linkId = $("#" + current_set_content_id).siblings('.custom-body').attr('id');
            SaveAccessSetId(setId, current_set_content_id);
        }

    });


});

function GetModelDataList() {
    var user_id = $("#hidUserId").val();
    $.ajax({
        url: "/iPD/projectAccessSetting/getData",
        type: 'post',
        async: false,
        data: { _token: CSRF_TOKEN, message: "get_modeldata", access_user_id: user_id },
        success: function(data) {
            console.log(data);
            if (data != "") {
                CreateModelDataTable(data);
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function CreateModelDataTable(data) {
    var row = "";
    var accessable_count = 0;
    var header = "";
    header += "<tr>";
    header += "<th>モデル名</th>";
    if (data[0]["model_data_set_id"] == 0) {
        header += "<th>アクセス許可</th>";
    }
    header += "</tr>";

    $.each(data, function(key, item) {
        row += "<tr>";
        row += "<td>" + item["name"] + "</td>";
        // if(item["accessable"] == 1){
        //     accessable_count++;
        //     row += "<td><input type='checkbox' id='"+item['id']+"' checked='checked'></td>";
        // }else{
        if (item["model_data_set_id"] == 0) {
            var tempArr = [];
            if (accessable_model_id_list.length > 0)
                tempArr = accessable_model_id_list.split(',');
            if (tempArr != "" && tempArr.includes(item['id'].toString())) {
                accessable_count++;
                row += "<td><input type='checkbox' id='" + item['id'] + "' checked='checked'></td>";
            }
            else {
                row += "<td><input type='checkbox' id='" + item['id'] + "'></td>";
            }


        }
        else {
            accessable_count++;
        }

        //}

        row += "</tr>";
    });
    $("#total_model").html(accessable_count);
    $("#tblModelData tr").remove();
    $("#tblModelData thead").append(header);
    $("#tblModelData tbody").append(row);
}

function GoTo(str) {
    var user_id = $("#hidUserId").val();
    var user_name = $("#hidUserName").val();
    window.location = "/iPD/projectAccessSetting/" + str + "/" + user_id + "/" + user_name;
}

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


// function UpdateDisplayReportFlag(pjCode, flag) {
    //     $.ajax({
    //         url: "../allstore/updateFlag",
    //         type: 'post',
    //         data: { _token: CSRF_TOKEN, message: "update_report_flag", projectCode: pjCode, flag: flag },
    //         success: function(data) {
    //             if (data.includes("success")) {
    //                 console.log("succssfully updated!");

    //                 for (var i = 0; i < g_allstoreData.length; i++) {
    //                     var storeData = g_allstoreData[i];

    //                     if (storeData["a_pj_code"] === pjCode) {
    //                         g_allstoreData[i]["display_report_flag"] = flag;
    //                         break;
    //                     }
    //                 }
    //             }
    //         },
    //         error: function(err) {
    //             console.log(err);
    //         }
    //     });
    // }


function getAllstoreManagementInfo() {
    ShowLoading();
    console.log("getAllstoreManagementInfo start");
    var access_user_id = $("#hidUserId").val();
    if (access_user_id == undefined || access_user_id == "") return;
    $.ajax({
        url: "/iPD/projectAccessSetting/getData",
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "get_allstore_info", access_user_id: access_user_id },
        success: function(data) {
            console.log(data);
            if (data != null) {
                // 	console.log(data);

                g_allstoreData = data;
                GetAccessSetting();
                GetModelDataList(); //get data for model accessable setting table
                var access_user_id = $("#hidUserId").val();
                var accessable_info = GetAccessableProjects(access_user_id);

                if (public_allstore_item_set_id == null || public_allstore_item_set_id == 0) {
                    DisplayTableHeaderWithCheckBox(g_allstoreData, accessable_info);
                }
                else {
                    DisplayTableHeaderWithoutCheckBox();
                }

                if (public_allstore_set_id == null || public_allstore_set_id == 0) {
                    DisplayTableBodyWithCheckBox(g_allstoreData, accessable_info)
                }
                else {
                    //accessable_item_index_list = accessable_info[0]["accessable_items"];
                    DisplayTableBodyWithoutCheckBox(g_allstoreData);
                    $("#total_allstore").html(g_allstoreData.length);
                }

                HideLoading();

            }
        },
        error: function(err) {
            console.log(err);
            HideLoading();
        }
    });

}


function GetAccessSetting() {

    console.log("get access setting start");
    var access_user_id = $("#hidUserId").val();
    if (access_user_id == undefined || access_user_id == "") return;
    $.ajax({
        url: "/iPD/projectAccessSetting/getData",
        type: 'post',
        async: false,
        data: { _token: CSRF_TOKEN, message: "get_access_setting", access_user_id: access_user_id },
        success: function(data) {
            console.log(data);
            if (data != null) {
                SetSettingData(data);
            }
        },
        error: function(err) {
            console.log(err);
        }
    });

}

function DisplayTableHeaderWithoutCheckBox(allstoreData) {
    $("#tbUser tr td").remove();
    //$("#tbCheckBox tr td").remove();
    var newRow = "";
    var newChkRow = "";
    var newitemChkRow = "";
    var accessable_item_count = 0;
    newRow += "<thead>";
    newChkRow += "<thead>";

    newRow += "<tr>";
    //newitemChkRow +=   "<tr>";
    var accessable_item_array = [];
    if (accessable_item_index_list != null && accessable_item_index_list.length > 0)
        accessable_item_array = accessable_item_index_list.split(",");
    $.each(allstore_headers, function(key, value) {
        if (accessable_item_array.includes(key.toString())) {
            accessable_item_count++;
            newRow += "<th>" + value + "</th>";
            //newitemChkRow += "<th><input type='checkbox' name='"+value+"' id='"+key+"'<th>";
        }

    });

    newRow += "</tr>";
    //newitemChkRow +=   "</tr>";
    newRow += "</thead>";

    //newChkRow +=    "<tr><th><th><tr>";//adjust header row with tbUser
    newChkRow += "<tr>";
    newChkRow += "<th style='word-wrap:break-word !important;'>";

    newChkRow += "物件情報\nアクセス権限";
    newChkRow += "</th>";

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

        //newChkRow += "</tbody>";
        newChkRow += "<tbody>";
    }

    $("#total_item").html(accessable_item_count);

    $("#tbUser").append(newRow);
    //$("#tbUser thead tr:first-child").before(newitemChkRow);//.prepend();
    if (public_allstore_set_id == 0) {
        $("#tbCheckBox").append(newChkRow);
    }
}

function DisplayTableBodyWithoutCheckBox(allstoreData) {

    var newRow = "";
    var newChkRow = "";
    var accessable_item_array = [];
    if (accessable_item_index_list != null && accessable_item_index_list.length > 0)
        accessable_item_array = accessable_item_index_list.split(",");

    for (var i = 0; i <= allstoreData.length - 1; i++) {

        newRow += "<tr>";

        var storeData = allstoreData[i];
        var pjCode = storeData["a_pj_code"];
        if (public_allstore_item_set_id !== 0) {
            $.each(accessable_item_array, function(key, index) {
                var itemName = allstore_db_item_list[index];
                newRow += "<td>" + storeData[itemName] + "</td>";
            });
        }
        else {
            $.each(allstore_db_item_list, function(key, item) {
                //var itemName = allstore_db_item_list[index];
                newRow += "<td>" + storeData[item] + "</td>";
            });
        }

        newRow += "</tr>";

    }


    $("#tbUser > tbody > tr").remove();
    $("#tbUser tbody").append(newRow);
    $("#tbCheckBox > thead > tr").remove();
    $("#tbCheckBox > tbody > tr").remove();
}

function GetAccessableProjects(access_user_id) {

    var project_list = "";
    $.ajax({
        url: "/iPD/projectAccessSetting/getData",
        type: 'post',
        async: false,
        data: { _token: CSRF_TOKEN, message: "get_accessable_project", access_user_id: access_user_id },
        success: function(data) {
            if (data != "") {
                project_list = data[0];
            }
        },
        error: function(err) {
            console.log(err);
            return project_list;
        }
    });
    return project_list;
}

function LoadProjectAccessSetting(access_user_id, access_user_name) {

    window.open("/iPD/projectAccessSetting/setting/" + access_user_id + "/" + access_user_name, "_blank");
}


function GetAllStoreSetList() {
    var result = "";
    $.ajax({
        url: "/iPD/projectAccessSetting/getData",
        type: 'post',
        async: false,
        data: { _token: CSRF_TOKEN, message: "get_allstore_set_list" },
        success: function(data) {
            console.log(data);
            if (!data.includes("empty")) {
                result = (data);
            }
        },
        error: function(err) {
            console.log(err);
            return result;
        }
    });
    return result;
}

function GetAllStoreItemSetList() {
    var result = "";
    $.ajax({
        url: "/iPD/projectAccessSetting/getData",
        type: 'post',
        async: false,
        data: { _token: CSRF_TOKEN, message: "get_allstore_item_set_list" },
        success: function(data) {
            console.log(data);
            if (!data.includes("empty")) {
                result = (data);
            }
        },
        error: function(err) {
            console.log(err);
            return result;
        }
    });
    return result;
}

function GetModelDataSetList() {
    var result = "";
    $.ajax({
        url: "/iPD/projectAccessSetting/getData",
        type: 'post',
        async: false,
        data: { _token: CSRF_TOKEN, message: "get_model_data_set_list" },
        success: function(data) {
            console.log(data);
            if (!data.includes("empty")) {
                result = (data);
            }
        },
        error: function(err) {
            console.log(err);
            return result;
        }
    });
    return result;
}

function CreateAuthoritySetTable(authoritySetList) {

    var row = "";
    $.each(authoritySetList, function(key, item) {
        row += "<tr class='list-group-item'>";
        row += "<td><label id='" + item["id"] + "' class='txtNew' value =''>" + item["authority_set_name"] + "</label></td>";
        row += "</tr>";

    });

    $("#tblSetList tbody tr").remove();
    $("#tblSetList tbody").append(row);
}

function GoSettingDetailPage(setId, linkId) {
    var user_id = $("#hidUserId").val();
    var user_name = $("#hidUserName").val();
    if (linkId.trim() == "model_data_link") {
        window.open("/iPD/projectAccessSetting/modelDataSet/" + user_id + "/" + user_name + "/" + setId, "blank");
    }
    else if (linkId.trim() == "allstore_info_link") {
        window.open("/iPD/projectAccessSetting/authoritySet/" + user_id + "/" + user_name + "/" + setId, "blank");
    }
    else if (linkId.trim() == "allstore_item_link") {
        window.open("/iPD/projectAccessSetting/authorityItemSet/" + user_id + "/" + user_name + "/" + setId, "blank");
    }

}

function SaveAccessSetId(access_set_id, status) {
    var access_user_id = $("#hidUserId").val();
    if (access_user_id == undefined || access_user_id == "") return;
    var message = "";
    if (status == "allstore_info") {
        message = "save_allstore_set_id";
    }
    else if (status == "allstore_item") {
        message = "save_allstore_item_set_id";
    }
    else if (status == "model_data") {
        message = "save_model_data_set_id";
    }
    $.ajax({
        url: "/iPD/projectAccessSetting/saveData",
        type: 'post',
        data: { _token: CSRF_TOKEN, message: message, access_set_id: access_set_id, access_user_id: access_user_id },
        success: function(data) {
            if (data.includes("success")) {
                console.log("succssfully save_access_set!");
                location.reload();
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function SetSettingData(data) {
    //data[0]//just one row
    $.each(data, function(key, item) {
        accessable_item_index_list = item["accessable_item"];
        accessable_pj_code_list = item["accessable_project"];
        accessable_model_id_list = item["accessable_model"];
        public_allstore_set_id = item["allstore_set_id"];
        public_allstore_item_set_id = item["allstore_item_set_id"];

        var setName = "";
        var setId = "";
        var linkId = "";
        var data_link = "";

        setName = item["model_data_set_name"];
        setId = item["model_data_set_id"];
        if (setName == "なし" || setId == 0) {
            linkId = "model_data_link";
            data_link = "<span>" + setName + "<span>";
            $("#" + linkId).append(data_link);
        }
        else {
            if (setId != null) {
                linkId = "model_data_link";
                data_link = "<a href='javascript:void(0)' onclick='GoSettingDetailPage(\"" + setId + "\",\" " + linkId + "\")'>" + setName + "</a>";
                $("#" + linkId).append(data_link);
            }
        }


        setName = item["allstore_set_name"];
        setId = item["allstore_set_id"];
        if (setName == "なし" || setId == 0) {
            linkId = "allstore_info_link";
            data_link = "<label>" + setName + "<label>";
            $("#" + linkId).append(data_link);
        }
        else {
            if (setId != null) {
                linkId = "allstore_info_link";
                data_link = "<a href='javascript:void(0)' onclick='GoSettingDetailPage(\"" + setId + "\",\" " + linkId + "\")'>" + setName + "</a>";
                $("#" + linkId).append(data_link);
            }
        }


        setName = item["allstore_item_set_name"];
        setId = item["allstore_item_set_id"];
        if (setName == "なし" || setId == 0) {
            linkId = "allstore_item_link";
            data_link = "<label>" + setName + "<label>";
            $("#" + linkId).append(data_link);
        }
        else {
            if (setId != null) {
                linkId = "allstore_item_link";
                data_link = "<a href='javascript:void(0)' onclick='GoSettingDetailPage(\"" + setId + "\",\" " + linkId + "\")'>" + setName + "</a>";
                $("#" + linkId).append(data_link);
            }
        }

    })
}

function TableRowFilter() {

    $("#tbUser tbody tr").each(function() {
        $(this).show();
    });
    $("#tbCheckBox tbody tr").each(function() {
        $(this).show();
    });

    var ipdCode_index = "";
    var pj_name_index = "";
    var pj_name_b_index = "";
    var pj_name_c_index = "";
    var branch_index = "";
    var incharge_index = "";
    var koujiType_index = "";
    $("#tbUser thead tr th").each(function() {
        var thText = $(this).text();
        if (thText.trim() == "iPDコード") {
            ipdCode_index = $(this).index();
        }
        if (thText.trim() == "プロジェクト名称(A)") {
            pj_name_index = $(this).index();
        }
        if (thText.trim() == "プロジェクト名称(B)") {
            pj_name_b_index = $(this).index();
        }
        if (thText.trim() == "BIM360プロジェクト名称") {
            pj_name_c_index = $(this).index();
        }
        if (thText.trim() == "支店") {
            branch_index = $(this).index();
        }
        if (thText.trim() == "iPDセンター担当者_氏名") {
            incharge_index = $(this).index();
        }
        if (thText.trim() == "工事区分名") {
            koujiType_index = $(this).index();
        }
    });

    var txtiPDCodeSearch = $("#txtiPDCodeSearch").val().trim();
    var txtProjectNameSearch = $("#txtProjectNameSearch").val().trim();
    var txtBranchSearch = $("#txtBranchSearch").val().trim();
    var txtiPDinChargeSearch = $("#txtiPDinChargeSearch").val().trim();
    var txtKoujiTypeSearch = $("#txtKoujiTypeSearch").val();
    if (txtiPDCodeSearch !== "" || txtProjectNameSearch !== "" || txtBranchSearch !== "" || txtiPDinChargeSearch !== "" || txtKoujiTypeSearch !== "") {

        $("#tbUser tbody tr").each(function(index) {

            var ipdCode = (ipdCode_index === "") ? "" : $(this).find("td:eq(" + ipdCode_index + ")").text();
            var projectName = (pj_name_index === "") ? "" : $(this).find("td:eq(" + pj_name_index + ")").text();
            var projectName_b = (pj_name_b_index === "") ? "" : $(this).find("td:eq(" + pj_name_b_index + ")").text();
            var projectName_c = (pj_name_c_index === "") ? "" : $(this).find("td:eq(" + pj_name_c_index + ")").text();
            var branch = (branch_index === "") ? "" : $(this).find("td:eq(" + branch_index + ")").text();;
            var inCharge = (incharge_index === "") ? "" : $(this).find("td:eq(" + incharge_index + ")").text();
            var koujiType = (koujiType_index === "") ? "" : $(this).find("td:eq(" + koujiType_index + ")").text();

            if ((!ipdCode.includes(txtiPDCodeSearch)) ||
                (!projectName.includes(txtProjectNameSearch) && !projectName_b.includes(txtProjectNameSearch) && !projectName_c.includes(txtProjectNameSearch)) ||
                !branch.includes(txtBranchSearch) ||
                !inCharge.includes(txtiPDinChargeSearch) ||
                !koujiType.includes(txtKoujiTypeSearch)) {

                $("#tbCheckBox tbody tr").eq(index).hide();
                $(this).hide();
            }
            else {
                $("#tbCheckBox tbody tr").eq(index).show();
                $(this).show();
            }
        });

    }
    else {
        $("#tbUser tbody tr").each(function() {
            $(this).show();
        });
        $("#tbCheckBox tbody tr").each(function() {
            $(this).show();
        });
    }

    $("#tbUser >tbody >tr:visible:odd").css("background-color", "#f2f2f2");
    $("#tbUser >tbody >tr:visible:even").css("background-color", "#fff");
}

function DisplayTableHeaderWithCheckBox(allstoreData, accessable_info) {

    $("#tbUser tr").remove();
    //$("#tbCheckBox tr td").remove();
    var newRow = "";
    var newChkRow = "";
    var newitemChkRow = "";
    var accessable_item_count = 0;
    newRow += "<thead>";
    newChkRow += "<thead>";

    newRow += "<tr>";
    newitemChkRow += "<tr>";

    $.each(allstore_headers, function(key, value) {
        newRow += "<th>" + value + "</th>";
        if (accessable_info != "" && accessable_info["accessable_items"].includes(key)) {
            accessable_item_count++;
            newitemChkRow += "<th class='chk_th'><input type='checkbox' name='" + value + "' id='" + key + "' checked><i class='fa fa-hand-point-right'></i></th>";
        }
        else {
            newitemChkRow += "<th class='chk_th'><input type='checkbox' name='" + value + "' id='" + key + "'><i class='far fa-hand-point-left'></i></th>";
        }

    });

    newRow += "</tr>";
    newitemChkRow += "</tr>";
    newRow += "</thead>";

    newChkRow += "<tr><th class='chk_th'><th></tr>"; //adjust header row with tbUser
    newChkRow += "<tr>";
    newChkRow += "<th style='word-wrap:break-word !important;'>";

    newChkRow += "物件情報\nアクセス権限";
    newChkRow += "</th>";

    newChkRow += "</tr>";
    newChkRow += "</thead>";

    // 	if(allstoreData.length == 0){

    //         newRow += "<tbody>";
    //         newRow +=   "<tr>";
    //         newRow +=     "<td></td>";

    //         var COLUMN_NUM = 52;
    //         for(var k=0; k < COLUMN_NUM; k++){

    //             if(k === 0){
    //                 newRow += "<td>No Data</td>";
    //             }else{
    //             newRow += "<td></td>";
    //             }
    //         }
    //         newRow +=   "</tr>";
    //         newRow += "</tbody>";

    // 	}else{

    newRow += "<tbody>";
    newRow += "</tbody>";

    newChkRow += "<tbody>";
    newChkRow += "</tbody>";
    //}

    $("#total_item").html(accessable_item_count);

    $("#tbUser").append(newRow);
    $("#tbUser thead tr:first-child").before(newitemChkRow); //.prepend();
    $("#tbCheckBox").append(newChkRow);

    //DisplayAllstoreSettingBodyWithCheckBox(allstoreData,accessable_info);
}

function DisplayTableBodyWithCheckBox(allstoreData, accessable_info) {
    var newRow = "";
    var newChkRow = "";
    var accessable_item_array = [];
    var accessable_count = 0;
    if (accessable_item_index_list != null && accessable_item_index_list.length > 0)
        accessable_item_array = accessable_item_index_list.split(",");

    for (var i = 0; i <= allstoreData.length - 1; i++) {
        newRow += "<tr>";
        var storeData = allstoreData[i];
        var pjCode = storeData["a_pj_code"];
        if (public_allstore_item_set_id !== 0) {
            $.each(accessable_item_array, function(key, index) {
                var itemName = allstore_db_item_list[index];
                newRow += "<td>" + storeData[itemName] + "</td>";
            });
        }
        else {
            $.each(allstore_db_item_list, function(key, item) {
                newRow += "<td>" + storeData[item] + "</td>";
            });
        }

        newRow += "</tr>";

        newChkRow += "<tr>";
        if (accessable_info != "" && accessable_info["accessable_projects"].includes(pjCode)) {
            accessable_count++;
            newChkRow += "<td><input type='hidden' value='" + storeData["a_pj_name"] + "$" + storeData["b_ipd_center_tantou"] + "'><input id='accessable_project' type='checkbox' value='" + storeData["a_pj_code"] + "' checked></td>";
        }
        else {
            newChkRow += "<td><input type='hidden' value='" + storeData["a_pj_name"] + "$" + storeData["b_ipd_center_tantou"] + "'><input id ='accessable_project' type='checkbox' value='" + storeData["a_pj_code"] + "'></td>";
        }
        newChkRow += "</tr>";

    }

    $("#total_allstore").html(accessable_count);
    $("#tbUser > tbody > tr").remove();
    $("#tbCheckBox > tbody > tr").remove();
    $("#tbUser tbody").append(newRow);
    $("#tbCheckBox tbody").append(newChkRow);
}


function SaveUserAccessableInfo() {
    var access_user_id = $("#hidUserId").val();
    if (access_user_id == undefined || access_user_id == "") return;
    var item_list = [];
    var project_list = [];
    var model_list = [];
    $("#tbUser thead tr:first-child th").each(function() {
        var chk = $(this).find('input[type="checkbox"]')
        if (chk.prop("checked") == true) {
            item_list.push(chk.attr('id'));
        }
    });
    $("#tbCheckBox tbody tr:visible td").each(function() {
        var chk = $(this).find('input[type="checkbox"]')
        if (chk.prop("checked") == true) {
            project_list.push(chk.val());
        }
    });

    $("#tblModelData tbody tr:visible td").each(function() {
        var chk = $(this).find('input[type="checkbox"]')
        if (chk.prop("checked") == true) {
            model_list.push(chk.attr('id'));
        }
    });
    //alert(project_list.length+"\n"+item_list.length);return;
    $.ajax({
        url: "/iPD/projectAccessSetting/saveData",
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "save_user_accessable_info", access_user_id: access_user_id, item_list: item_list, model_list: model_list, project_list: JSON.stringify(project_list) },
        success: function(data) {
            if (data.includes("success")) {
                console.log("succssfully save_access_set!");
                location.reload();
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function AllProjectCheck() {
    $("#tbCheckBox tbody tr:visible").each(function(index) {
        var chk = $(this).find('input[type="checkbox"]');
        chk.prop("checked", true);
    });
}

function AllProjectUncheck() {
    $("#tbCheckBox tbody tr:visible").each(function(index) {
        var chk = $(this).find('input[type="checkbox"]');
        chk.prop("checked", false);
    });
}

function AllItemCheck() {
    $("#tbUser thead tr:first-child th").each(function(index) {
        var chk = $(this).find('input[type="checkbox"]');
        chk.prop("checked", true);
    });
}

function AllItemUncheck() {
    $("#tbUser thead tr:first-child th").each(function(index) {
        var chk = $(this).find('input[type="checkbox"]');
        chk.prop("checked", false);
    });
}

function AllModelCheck() {
    $("#tblModelData tbody tr").each(function(index) {
        var chk = $(this).find('input[type="checkbox"]');
        chk.prop("checked", true);
    });
}

function AllModelUncheck() {
    $("#tblModelData tbody tr").each(function(index) {
        var chk = $(this).find('input[type="checkbox"]');
        chk.prop("checked", false);
    });
}
