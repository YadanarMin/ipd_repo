/* ajax通信トークン定義 */
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
var allstore_headers = ["No.", "iPDコード", "工事基幹コード", "支店", "確度(A)", "プロジェクト名称(A)", "工事区分", "工事区分名", "請負金",
    "建物用途1", "建物用途2", "施工場所", "設計State", "設計", "構造(A)", "階数(A)", "地上(A)", "地下(A)", "PH(A)", "延べ面積(A)", "着工", "竣工",
    "プロジェクト稼働状況", "設計段階", "施工段階", "自由記入欄", "プロジェクト名称(B)", "BIM360プロジェクト名称", "支店(B)", "確度(B)", "発注者", "設計者(B)",
    "設計者(B)支店", "施工場所(B)", "用途(B)", "構造(B)", "階数(B)", "地下(B)", "地上(B)", "PH(B)", "延べ面積(B)", "棟数", "工事事務所所長_氏名", "工事事務所_組織",
    "工事部担当者_氏名", "工事部担当者_組織", "営業担当者_氏名", "営業担当者_組織", "意匠設計担当者_氏名", "意匠設計担当者_組織", "意匠モデラー_氏名", "意匠モデラー_組織",
    "構造設計担当者_氏名", "構造設計担当者_組織", "構造モデラー_氏名", "構造モデラー_組織", "設備空調担当者_氏名", "設備空調担当者_組織", "設備空調モデラー_氏名",
    "設備空調モデラー_組織", "設備衛生担当者_氏名", "設備衛生担当者_組織", "設備衛生モデラー_氏名", "設備衛生モデラー_組織", "設備電気担当者_氏名", "設備電気担当者_組織",
    "設備電気モデラー_氏名", "設備電気モデラー_組織", "生産設計担当者_氏名", "生産設計担当者_組織", "生産設計モデラー_氏名", "生産設計モデラー_組織", "施工管理担当者_氏名",
    "施工管理担当者_組織", "生産モデラー_氏名", "生産モデラー_組織", "生産技術担当者_氏名", "生産技術担当者_組織", "積算担当者_氏名", "積算担当者_組織", "BIMマネ課担当者_氏名",
    "BIMマネ課担当者_組織", "iPDセンター担当者_氏名", "iPDセンター担当者_組織", "協力会社担当者_氏名", "協力会社担当者_組織", "BIMマネージャー_氏名", "BIMマネージャー_組織",
    "BIMコーディネーター_氏名", "BIMコーディネーター_組織", "建築工事発注形態", "設備工事発注形態", "入札start", "入札end", "基本設計start", "基本設計end", "実施設計start", "実施設計end",
    "設計モデル作成start", "設計モデル作成end", "確認申請start", "確認申請end", "積算見積モデル統合・追記修正start", "積算見積モデル統合・追記修正end", "工事従事者決定start", "工事従事者決定end",
    "現場工程決定start", "現場工程決定end", "工事start", "工事end", "引渡しstart", "引渡しend", "モデリング会社区分", "備考1(B)", "備考2(C)", "受注状況", "追加項目"
];


$(document).ready(function() {

    GetAllStoreItemSetList();
    //GetAllStoreDataForAccessSet();

    SearchItem();

    $("#tblSetList").on('click', 'td', function() {
        var td_index = $(this).index();
        if (td_index > 0) return;
        var tr = $(this).closest('tr');
        var set_id = $(this).find('label').attr('id');
        var set_name = $(this).find('label').html();

        if (set_id == undefined) return; // skip last li
        $("#set_name").html("【" + set_name + "】");
        $("#hidAccessId").val(set_id);
        $("#tblSetList tr").removeClass("active");
        tr.addClass('active');
        if (set_id == 1) {
            //$("#btnSave").attr('disabled', 'disabled');
        }
        else {
            $("#btnSave").removeAttr('disabled');
        }
        GetAllStoreDataForAccessSet(set_id);
    });

    var hidAccessId = $("#hidAccessId").val();
    if (hidAccessId == null || hidAccessId == undefined || hidAccessId == "") {
        FirstItemClick();
    }
    else {
        SetItemClick(hidAccessId, null);
    }
});

function FirstItemClick() {
    if ($("#tblSetList tbody tr").length > 1) {
        $("#tblSetList tbody tr:first").find("td:eq(0)").click();
    }
}

function GetAllStoreItemSetList() {
    $.ajax({
        url: "/iPD/projectAccessSetting/getData",
        type: 'post',
        async: false,
        data: { _token: CSRF_TOKEN, message: "get_allstore_item_set_list" },
        success: function(data) {
            console.log(data);
            if (data != "") {
                //CreateAccessSetList(data);
                CreateAuthoritySetTable(data);
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function GetAllStoreDataForAccessSet(set_id) {
    //var authority_set_id = $("#hidAccessId").val();
    $.ajax({
        url: "/iPD/projectAccessSetting/getData",
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "get_allstore_item_set_byid", "set_id": set_id },
        success: function(data) {
            //console.log(data);return;
            if (data != "") {
                CreateAccessSetTable(data);
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function CreateAuthoritySetTable(authoritySetList) {

    var row = "";
    $.each(authoritySetList, function(key, item) {

        if (item["authority_set_name"] == "カスタム" || item["id"] == 0) {
            return;
        }
        else if (item["id"] == 1) {
            row += "<tr class='list-group-item'>";
            row += "<td colspan='3'><label id='" + item["id"] + "' class='txtNew'>" + item["authority_set_name"] + "</label> </td>";
            row += "</tr>";

        }
        else {
            row += "<tr class='list-group-item'>";
            row += "<td class='td-width'><label id='" + item["id"] + "' class='txtNew'>" + item["authority_set_name"] + "</label> </td>";
            row += '<td data-toggle="tooltip" data-placement="top" title="セット名編集"><a href="javascript:void(0)" onClick="EditAccessSetName(' + item["id"] + ',this)"><img class="appIconBig" src="/iPD/public/image/edit.png" alt="" height="20" width="15"></a></td>';
            // <i onClick="EditAccessSetName('+item["id"]+',this)" class="fa fa-refresh" aria-hidden="true"></i>
            row += '<td data-toggle="tooltip" data-placement="top" title="削除"><a href="javascript:void(0)" onClick="DeleteAccessSet(' + item["id"] + ',\'' + item["authority_set_name"] + '\')"><img class="appIconBig" src="/iPD/public/image/trash.png" alt="" height="20" width="15"></a></td>';
            row += "</tr>";
        }

    });

    $("#tblSetList tr:not(:last)").remove()
    $("#tblSetList tbody tr:last").before(row);
}

function CreateAccessSetTable(data) {
    var row = "";
    var setDetail = (data[0]["detail"] != "") ? (data[0]["detail"]).split(',') : data[0]["detail"];
    var accessable_count = 0;

    console.log(setDetail);
    $.each(allstore_headers, function(key, item) {
        row += "<tr>";
        row += "<td>" + item + "</td>";
        if (setDetail.includes("" + key + "")) {

            accessable_count++;
            row += "<td><input type='checkbox' name='" + item + "' id='" + key + "' checked='checked'></td>";
        }
        else {
            row += "<td><input type='checkbox' name='" + item + "' id='" + key + "'></td>";
        }

        row += "</tr>";
    });
    $("#total").html(accessable_count);
    $("#tblAuthorityItemSet tbody tr").remove();
    $("#tblAuthorityItemSet tbody").append(row);
}

function SaveAccessSetDetail() {
    var item_list = [];
    var authority_set_id = $("#hidAccessId").val();
    $("#tblAuthorityItemSet tbody tr:visible").each(function(index) {
        var chk = $(this).find('input[type="checkbox"]');
        var item_idex = chk.attr('id');
        if (chk.prop("checked") == true)
            item_list.push(item_idex);
    });


    $.ajax({
        url: "/iPD/projectAccessSetting/saveData",
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "save_item_set_detail", authority_set_id: authority_set_id, item_list: JSON.stringify(item_list) },
        success: function(data) {
            if (data.includes("success")) {
                console.log("succssfully updated!");
                var user_id = $("#hidUserId").val();
                var user_name = $("#hidUserName").val();
                window.location = "/iPD/projectAccessSetting/authorityItemSet/" + user_id + "/" + user_name + "/" + authority_set_id;
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function CheckAll() {
    $("#tblAuthorityItemSet tbody tr:visible").each(function(index) {
        var chk = $(this).find('input[type="checkbox"]');
        chk.prop("checked", true);
    });
}

function UnCheckAll() {
    $("#tblAuthorityItemSet tbody tr:visible").each(function(index) {
        var chk = $(this).find('input[type="checkbox"]');
        chk.prop("checked", false);
    });
}

function AddAccessSet() {

    var last_row_idex = $("#tblSetList tbody tr:last").index();
    //alert(last_row_idex);
    var index = last_row_idex;

    var td_row = "";
    td_row += "<tr class='list-group-item'>";
    td_row += "<td><input type='text' name='txtNew' class='txtNew'/></td>";
    td_row += '<td colspan="2" height="20px"><a href="javascript:void(0)" onClick="SaveNewAuthoritySet(this)">確定</a></td>';
    td_row += "</tr>";
    $("#tblSetList tbody tr:last").before(td_row);

}

function SaveNewAuthoritySet(ele) {
    var tr = $(ele).closest('tr');
    var newAuthoritySetName = tr.find('input[class="txtNew"]').val();
    if (newAuthoritySetName == "" || newAuthoritySetName == undefined) return;
    $.ajax({
        url: "/iPD/projectAccessSetting/saveData",
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "save_new_allstore_item_set", new_authority_set_name: newAuthoritySetName },
        success: function(data) {
            if (data.includes("success")) {
                GetAllStoreItemSetList();
                SetItemClick(null, newAuthoritySetName);
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function SearchItem() {
    $("#txtSearch").keyup(function() {
        var textSearch = $(this).val();
        $("#tblAuthorityItemSet tbody tr").each(function() {
            var itemName = $(this).find("td:nth-child(1)").text();
            if (!itemName.includes(textSearch)) {
                $(this).hide();
            }
            else {
                $(this).show();
            }
        });
    })
}

function SetItemClick(setId, setName) {
    $("#tblSetList tbody tr").each(function(index) {
        var id = $(this).find("td:eq(0)").find('label').attr('id');
        var name = $(this).find("td:eq(0)").find('label').html();
        if (setId != null) {
            if (id == undefined) return;
            if (id === setId) {
                $(this).find("td:eq(0)").click();
            }
        }
        else {

            if (name == undefined) return;
            if (name === setName) {
                $(this).find("td:eq(0)").click();
            }
        }

    });
}

function EditAccessSetName(id, ele) {
    var tr = $(ele).closest('tr');
    var lbl = tr.find('label');
    var setName = lbl.html();
    $("#updateName").val(setName);
    $("#updateName").attr('name', id); //set ele id to name property
    $("#update_popup").show("100").css({
        top: (tr.offset().top + 40) + "px",
        left: (tr.offset().left + 50) + "px"
    });
}

function UpdateSetName() {
    var id = $("#updateName").attr('name');
    var newSetName = $("#updateName").val();
    $.ajax({
        url: "/iPD/projectAccessSetting/saveData",
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "update_allstore_item_set_name", access_set_id: id, access_set_name: newSetName, status: "allstore_item" },
        success: function(data) {
            if (data.includes("success")) {
                GetAllStoreItemSetList();
                SetItemClick(id, null);

                $("#update_popup").hide(100);
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function DeleteAccessSet(id, setName) {
    var cfrm = confirm(setName + " を削除していいですか？");
    if (cfrm == false) return;
    $.ajax({
        url: "/iPD/projectAccessSetting/deleteData",
        type: 'post',
        async: false,
        data: { _token: CSRF_TOKEN, message: "delete_allstore_item_set", access_set_id: id },
        success: function(data) {
            if (data.includes("success")) {
                GetAllStoreItemSetList();
            }
            var user_id = $("#hidUserId").val();
            var user_name = $("#hidUserName").val();
            window.location = "/iPD/projectAccessSetting/authorityItemSet/" + user_id + "/" + user_name;
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function ClosePopUp() {
    $("#update_popup").hide(100);
}

function GoTo(str) {
    var user_id = $("#hidUserId").val();
    var user_name = $("#hidUserName").val();
    window.location = "/iPD/projectAccessSetting/" + str + "/" + user_id + "/" + user_name;
}
