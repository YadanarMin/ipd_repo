var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

let CONTENTS_NUM = 31;
var toggleState = {};
$(document).ready(function() {

    var login_user_id = $("#hidLoginID").val();
    var img_src = "../public/image/JPG/鍵のクローズアイコン素材.jpeg";
    var url = "user/authoritySettings";
    var content_name = "権限設定";
    recordAccessHistory(login_user_id, img_src, url, content_name);

    initAuthorityData();
});

function initAuthorityData() {

    $.ajax({
        url: "../user/getAuthorityData",
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "getAllAuthority", authority_id: 0, authority_name: "" },
        success: function(result) {
            if (result.length > 0) {
                // console.log(result);
                $("#hidAuthority").val(JSON.stringify(result));
                getCCCContents(result);
            }
        },
        error: function(err) {
            console.log(err);
        }
    });

}

function getCCCContents(authorityData) {

    $.ajax({
        url: "../user/getContents",
        type: 'post',
        data: { _token: CSRF_TOKEN },
        success: function(data) {
            // console.log(data)
            if (data != null) {
                var contentsNum = data.length;
                $("#hidContents").val(JSON.stringify(data));

                setAuthorityData(authorityData, data);
            }
        },
        error: function(err) {
            console.log(err);
        }
    });

}

function toggleServiceList(authorityType) {

    // var login_user_id = $("#hidLoginID").val();
    // if(login_user_id != 12){ alert("メンテナンス中");return; }

    if (!toggleState[authorityType]) {
        alert("権限種別が存在しません。")
        return;
    }

    var preState = toggleState[authorityType];
    if (preState === "open") {
        // すべての要素を非表示に切替
        $('table#tblAuthorityList tbody tr').each(function() {
            // trAuthorityTypeクラスが付与されていなければ非表示に切替
            if (!$(this).hasClass("trAuthorityType")) {
                $(this).css("display", "none");
            }
        });

        toggleState[authorityType] = "close";
    }
    else {

        // すべての要素を非表示に切替
        $('table#tblAuthorityList tbody tr').each(function() {
            // trAuthorityTypeクラスが付与されていなければ非表示に切替
            if (!$(this).hasClass("trAuthorityType")) {
                $(this).css("display", "none");
            }
        });

        // 選択した権限種別のみ表示に切替
        var isToggle = false;
        var typeCnt = 0;
        $('table#tblAuthorityList tbody tr').each(function() {

            // trAuthorityTypeクラスが付与されていなければ表示に切替
            if (isToggle) {
                $(this).css("display", "block");
                typeCnt++;
            }

            // 表示切替開始判定
            if ($(this).hasClass("trAuthorityType") && $(this).find(".tdManager").find("div").first().text().trim() === authorityType) {
                isToggle = true;
            }

            // 表示切替終了判定
            var contents_num = $("#hidAuthorityNum").val();
            if (typeCnt == contents_num) {
                isToggle = false;
                return false; //break
            }
        });

        toggleState[authorityType] = "open";

        $("#tblAuthorityList tbody tr").each(function() {

            if ($(this).hasClass("trAuthorityType")) {
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

function ClosePopup() {
    $("#createUser").css({ visibility: "hidden", opacity: "0" });
}

function DisplayPopup() {
    $("#txtName").val("");
    $("#createUser").css({ visibility: "visible", opacity: "1" });
}

function DeleteUser(id) {
    var result = confirm('Are you sure!!');
    if (result === true) {
        $.ajax({
            url: "../user/deleteData",
            type: 'post',
            data: { _token: CSRF_TOKEN, userID: id },
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

function setAuthorityData(authorityData, contentsData) {
    $("table#tblAuthorityList tbody tr").remove();

    for (var i = 0; i < authorityData.length; i++) {
        var appendText = "";

        var id = authorityData[i]["id"].toString();
        var name = authorityData[i]["name"];
        var authority_string = authorityData[i]["authority"];
        var authority_array = authority_string.split(',');
        var box_access_authority = authorityData[i]["box_access"];

        if (i === 0) {
            $("#hidAuthorityNum").val(authority_array.length.toString());
        }

        toggleState[name] = "close";

        appendText += "<tr class='trAuthorityType' style='background:#b0c4de;border-top:1px white solid;'>";
        appendText += "<td class='tdManager'>";
        appendText += "<div style='display:flex;margin:0 5px 0 0;position:relative;'>";
        appendText += "<div>" + name + "</div>";
        appendText += "<div style='position: absolute;right: 0;'><img src='../public/image/drop_down.png' alt='dropdown' align='dropdown' style='width:16px;' onclick='toggleServiceList(\"" + name + "\")'></div>";
        appendText += "</div>";
        appendText += "</td>";
        appendText += "<td class='tdService'></td>";
        appendText += "<td class='tdChkbox'><input type='checkbox' name='isAuthorization' value='' id='allchkbox" + id + "'/></td>";
        if (box_access_authority == 1)
            appendText += "<td class='tdBoxAuthority'><input type='checkbox' name='isAuthorization' value='' id='box_access_authority" + id + "' checked='checked'/></td>";
        else
            appendText += "<td class='tdBoxAuthority'><input type='checkbox' name='isAuthorization' value='' id='box_access_authority" + id + "'/></td>";
        appendText += "<td class='tdUpdate'><a href='javascript:void(0)' onClick='UpdateAuthority(" + id + ", \"" + name + "\");'><img class='appIconBig' src='../public/image/update.png' alt='' height='17' width='17' /></a></td>";
        appendText += "<td class='tdDelete'><a href='javascript:void(0)' onClick='DeleteAuthority(" + id + ", \"" + name + "\");'><img class='appIconBig' src='../public/image/trash.png' alt='' height='17' width='12' /></a></td>";
        appendText += "</tr>";

        for (var k = 0; k < authority_array.length; k++) {
            var cur_string = authority_array[k];
            var tmp_string = cur_string.split(':');
            var cur_id = tmp_string[0];
            var cur_flg = tmp_string[1];
            var cur_name = "";
            for (var j = 0; j < contentsData.length; j++) {
                if (cur_id === contentsData[j]["id"].toString()) {
                    cur_name = contentsData[j]["name"];
                    break;
                }
            }

            appendText += "<tr class='trServiceType'>";
            appendText += "<td class='tdManager'></td>";
            appendText += "<td class='tdService'>" + cur_name + "</td>";
            appendText += "<td class='tdChkbox'><input type='checkbox' name='isAuthorization' value='" + cur_id + "' id='" + cur_id + "_" + id + "'/></td>";
            appendText += "<td class='tdUpdate'></td>";
            appendText += "<td class='tdDelete'></td>";
            appendText += "</tr>";
        }

        $("#tblAuthorityList tbody").append(appendText);
    }

    var appendFooterText = "";
    appendFooterText += "<tr class='trAuthorityType' style='background:#b0c4de;border-top:1px white solid;'>";
    appendFooterText += "<td class='tdManager'>";
    appendFooterText += "<div style='display:flex;justify-content: center;position: relative;'>";
    appendFooterText += "<div style='height:20px;'></div>";
    appendFooterText += "<div style='position:absolute;left:0;'><img src='../public/image/plus.png' alt='dropdown' align='dropdown' style='width:16px;' onclick='DisplayPopup()'></div>";
    appendFooterText += "</div>";
    appendFooterText += "</td>";
    appendFooterText += "<td class='tdService'></td>";
    appendFooterText += "<td class='tdChkbox'></td>";
    appendFooterText += "<td class='tdBoxAuthority'></td>";
    appendFooterText += "<td class='tdUpdate'></td>";
    appendFooterText += "<td class='tdDelete'></td>";
    appendFooterText += "</tr>";

    $("#tblAuthorityList tbody").append(appendFooterText);

    checkIsValidTblContent(authorityData, contentsData);
}

function checkIsValidTblContent(authorityData, contentsData) {

    for (var i = 0; i < authorityData.length; i++) {

        var id = authorityData[i]["id"].toString();

        var authority_string = authorityData[i]["authority"];
        var authority_array = authority_string.split(',');

        var chkcnt = 0;


        for (var k = 0; k < authority_array.length; k++) {

            var cur_string = authority_array[k];
            var tmp_string = cur_string.split(':');
            var cur_id = tmp_string[0];
            var cur_flg = tmp_string[1];
            var cur_name = "";
            // for (var j = 0; j < contentsData.length; j++) {
            //     if(cur_id === contentsData[j]["id"].toString()){
            //         cur_name = contentsData[j]["name"];
            //         break;
            //     }
            // }

            if (cur_flg === "1") {
                $("#" + cur_id + "_" + id).prop('checked', true);
                chkcnt++;
            }
        }

        var contents_num = $("#hidAuthorityNum").val();
        if (chkcnt.toString() === contents_num) {
            $("#allchkbox" + id).prop('checked', true);
        }
    }

    $('input[type=checkbox]').on('change', function() {

        var strAuthorityId = $(this).attr("id");
        if (strAuthorityId.indexOf("allchkbox") !== -1) {
            var id = strAuthorityId.replace("allchkbox", "");
            var isCheck = $("#" + strAuthorityId).prop("checked");
            changeAllCheckBoxContents(id, isCheck);
        }
    });
}

function changeAllCheckBoxContents(id, isCheck) {

    var authorityData = JSON.parse($("#hidAuthority").val());

    for (var i = 0; i < authorityData.length; i++) {

        var target_id = authorityData[i]["id"].toString();

        if (target_id === id) {
            var authority_string = authorityData[i]["authority"];
            var authority_array = authority_string.split(',');

            var chkcnt = 0;

            for (var k = 0; k < authority_array.length; k++) {

                var cur_string = authority_array[k];
                var tmp_string = cur_string.split(':');
                var cur_id = tmp_string[0];

                $("#" + cur_id + "_" + id).prop('checked', isCheck);
            }
        }
    }
}

function addAuthorityType() {

    var name = $("#txtName").val();
    var contentsData = JSON.parse($("#hidContents").val());
    var param_authority = "";
    for (var i = 0; i < contentsData.length; i++) {
        var cur_id = contentsData[i]["id"];
        param_authority += cur_id.toString() + ":0";

        if (i < contentsData.length - 1) {
            param_authority += ",";
        }
    }

    $.ajax({
        url: "../user/createAuthority",
        type: 'post',
        data: { _token: CSRF_TOKEN, name: name, param_authority: param_authority },
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

function UpdateAuthority(id, name) {

    var authorityData = getAuthorityListByTable(id);

    $.ajax({
        url: "../user/updateAuthority",
        type: 'post',
        data: { _token: CSRF_TOKEN, authorityData: authorityData },
        success: function(message) {
            if (message.includes("success")) {
                alert("successfully updated!");
                location.reload();
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function DeleteAuthority(id, name) {

    var isDelete = true;

    $.ajax({
        url: "../user/getAllUserData",
        type: 'post',
        data: { _token: CSRF_TOKEN },
        success: function(result) {
            if (result.length > 0) {
                // console.log(result);

                for (var i = 0; i < result.length; i++) {
                    if (id === result[i]["authority_id"]) {
                        isDelete = false;
                    }
                }

                if (isDelete) {
                    var result = confirm("本当に削除しますか？ 【権限:" + name + "】");
                    if (result === true) {
                        $.ajax({
                            url: "../user/deleteAuthorityData",
                            type: 'post',
                            data: { _token: CSRF_TOKEN, authorityID: id },
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
                else {
                    alert("削除できません。\n【権限:" + name + "】を持つユーザーがいます。");
                }
            }
        },
        error: function(err) {
            alert(JSON.stringify(err));
            console.log(err);
        }
    });

}

function getAuthorityListByTable(id) {

    var authorityData = {};
    authorityData["authority_string"] = "";
    var isToggle = false;
    var typeCnt = 0;
    var targetID = "allchkbox" + id;
    var box_access_authority_id = "box_access_authority" + id;
    var tmpAuthority = "";

    $('table#tblAuthorityList tbody tr').each(function() {

        if (isToggle) {
            var inputID = $(this).find(".tdChkbox").find("input").attr("id");
            var isCheck = $("#" + inputID).prop("checked");
            var contents_id = $("#" + inputID).val();
            inputID = inputID.slice(0, -1 * (id.toString().length));
            // authorityData[inputID] = isCheck;
            // authorityData[inputID+contents_id] = isCheck;

            var tmpString = "";
            if (isCheck) {
                tmpString = contents_id + ":1,";
            }
            else {
                tmpString = contents_id + ":0,";
            }
            tmpAuthority += tmpString;

            typeCnt++;
        }

        // 開始判定
        if ($(this).hasClass("trAuthorityType") && $(this).find(".tdChkbox").find("input").attr("id") === targetID) {
            isToggle = true;
            authorityData["name"] = $(this).find(".tdManager").find("div").first().text().trim();
        }

        if ($(this).hasClass("trAuthorityType") && $(this).find(".tdBoxAuthority").find("input").attr("id") === box_access_authority_id) {
            var isCheck = $("#" + box_access_authority_id).prop("checked");
            var tmpId = $("#" + box_access_authority_id).val();
            authorityData["box_access_authority"] = isCheck ? 1 : 0;
        }

        // 終了判定
        var contents_num = $("#hidAuthorityNum").val();
        if (typeCnt == contents_num) {
            isToggle = false;
            typeCnt = 0;
            return false;
        }
    });

    // var contentsData = JSON.parse($("#hidContents").val());
    tmpAuthority = tmpAuthority.slice(0, -1);
    authorityData["authority_string"] = tmpAuthority;

    return authorityData;
}
