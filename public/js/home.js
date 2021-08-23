var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
var g_recentlyUsedData = [];
var current_icon_name = "";

$(document).ready(function() {
    $.ajaxSetup({
        cache: false
    });

    $(".cccgnav-content").click(function() {
        location.href = $(this).attr("data-url");
    });

    LoadContentsList();

    //Right click event
    $(document).on("contextmenu", ".right_click_icon", function(e) {
        current_icon_name = $(this).html();
        var current_id = $(this).attr('id');
        $("#hidDeleteContentsID").val(current_id);
        if (current_icon_name != "")
            $("#icon_header").html("【" + current_icon_name + "】");
        $(".right-click-content").toggle("100").css({
            top: event.pageY + "px",
            left: event.pageX + "px"
        });
        return false;
    });

    //body click then close right clicked popup
    $("body").click(function() {
        $(".right-click-content").hide(100);
    });

    //left click over right clicked popup still open control
    $('.right-click-content').click(function(event) {
        event.stopPropagation();
    })
});

function SetPageNameToSession(pageName) {
    $.ajax({
        url: "../admin/setToSession",
        type: 'post',
        data: { _token: CSRF_TOKEN, pageName: pageName },
        success: function(data) {

            if (data.includes("success")) {
                window.open('/iPD/admin/pageDescription', "pageDescription");
            }
            // window.location = '';

        },
        error: function(err) {
            console.log(err);
        }
    });
}

function LoadContentsList() {

    var appendStr = "";

    $.ajax({
        url: "../user/getContents",
        type: 'post',
        data: { _token: CSRF_TOKEN },
        success: function(data) {
            if (data != null) {
                // console.log(data);
                createContentsList(data);
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function LoadRecentlyUsed(contentsData) {

    var personal_id = $("#hidLoginID").val();

    $.ajax({
        url: "../user/getAccessHistory",
        type: 'post',
        data: { _token: CSRF_TOKEN, personal_id: personal_id },
        success: function(data) {
            if (data != null) {
                // 	console.log(data);
                if (data.length !== 0) {
                    g_recentlyUsedData = data[0];
                    createRecentlyUsed(data[0], contentsData);
                }
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function createContentsList(contentsData) {

    var appendStr = "";
    var tmp_authority = $("#hidAuthorityData").val();
    var authority = JSON.parse(tmp_authority);

    var contentList = adjustAuthorityMapping(contentsData, authority);
    var data = adjustContentsData(contentsData);

    var keyList = Object.keys(data);
    for (var i = 0; i < keyList.length; i++) {

        var currentCategory = keyList[i];
        var currentData = data[currentCategory];
        var contents_cnt = 0;

        for (var j = 0; j < currentData.length; j++) {
            if (currentData[j]["state"] === "1") {
                contents_cnt++;
            }
        }

        if (contents_cnt === 0) {
            continue;
        }

        appendStr += "<ul class='cccgnav-service-area'>";

        appendStr += "<li class='cccgnav-service'>";
        appendStr += "<a style='margin-top:5px;' href='javascript:void(0)'>";
        appendStr += "<img class='appIconBig' src='" + currentData[0]["img_src"] + "' onclick='createContents(\"" + currentCategory + "\",\"" + currentData[0]["img_src"] + "\")' alt='' height='20' width='20' />";
        appendStr += "</a>&nbsp";
        appendStr += "<h4 style='font-weight:700;'>" + currentCategory + "</h4>";
        appendStr += "</li>";

        for (var k = 0; k < currentData.length; k++) {

            if (currentData[k]["state"] === "0") {
                continue;
            }

            appendStr += "<li>";
            if (currentData[k]["name"] === "部屋仕上ﾃﾞｰﾀ" ||
                currentData[k]["name"] === "構造ﾀｲﾌﾟ作成" ||
                currentData[k]["name"] === "ﾓﾃﾞﾙﾌｧﾐﾘ分析" ||
                currentData[k]["name"] === "基礎工事ﾃﾞｰﾀ") {

                if (currentData[k]["data_url"] === "" || currentData[k]["data_url"] === "#") {
                    appendStr += "<a class='cccgnav-content right_click_icon' id='" + currentData[k]["id"].toString() + "' style='color:#a9a9a9;' >" + currentData[k]["name"] + "<span style='color:red;'>[準備中]</span></a>";
                }
                else {
                    appendStr += "<a class='cccgnav-content' onclick='openContent(\"" + currentData[k]["data_url"] + "\", \"" + currentData[k]["name"] + "\")' >" + currentData[k]["name"] + "<span style='color:red;'>[準備中]</span></a>";
                }
            }
            else {
                if (currentData[k]["data_url"] === "" || currentData[k]["data_url"] === "#") {
                    appendStr += "<a class='cccgnav-content right_click_icon' id='" + currentData[k]["id"].toString() + "' style='color:#a9a9a9;'>" + currentData[k]["name"] + "</a>";
                }
                else {
                    appendStr += "<a class='cccgnav-content' onclick='openContent(\"" + currentData[k]["data_url"] + "\", \"" + currentData[k]["name"] + "\")' >" + currentData[k]["name"] + "</a>";
                }
            }
            appendStr += "</li>";
        }

        appendStr += "</ul>";
    }

    $("#contents-field ul").remove();
    $("#contents-field").append(appendStr);

    LoadRecentlyUsed(data);
}

function createContents(category, img_src) {

    var authority_id = $("#hidAuthorityID").val();
    if (authority_id != 1) { return; }

    var content_name = window.prompt("追加したいコンテンツ名称を入力してください。", "");
    if (content_name == null) {
        //cancel
    }
    else {
        $.ajax({
            url: "../user/setContents",
            type: 'post',
            data: { _token: CSRF_TOKEN, content_name: content_name, category: category, img_src: img_src },
            success: function(result) {
                if (result.includes("success")) {
                    // console.log(result);
                    alert("追加完了しました。");
                    location.reload();
                }
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

}

function createRecentlyUsed(recentlyUsedData, data) {
    var appendStr = "";
    var img_src_list = recentlyUsedData["img_src"];
    var url_list = recentlyUsedData["url"];
    var name_list = recentlyUsedData["content_name"];

    var tmpArraySrc = img_src_list.split(',');
    var tmpArrayName = name_list.split(',');
    var tmpArrayUrl = url_list.split(',');

    gnav_service_loop:
        for (var i = 0; i < tmpArraySrc.length; i++) {

            var isDisplay = true;
            var keyList = Object.keys(data);
            key_loop:
                for (var k = 0; k < keyList.length; k++) {

                    var currentCategory = keyList[k];
                    var currentData = data[currentCategory];
                    for (var j = 0; j < currentData.length; j++) {
                        if (currentData[j]["name"] === tmpArrayName[i]) {
                            if (currentData[j]["state"] === "0") {
                                isDisplay = false;
                                break key_loop;
                            }
                        }
                    }
                }

            if (!isDisplay) {
                continue gnav_service_loop;
            }

            appendStr += "<ul class='cccgnav-service-area'>";
            appendStr += "<li class='cccgnav-service'>";

            appendStr += "<a style='margin-top:5px;' href='javascript:void(0)'>";
            appendStr += "<img class='appIconBig' src='" + tmpArraySrc[i] + "' alt='' height='20' width='20' />";
            appendStr += "</a>&nbsp";
            appendStr += "<a class='cccgnav-content' style='margin:0.7rem 0 0 0;' onclick='openContent(\"" + tmpArrayUrl[i] + "\", \"" + tmpArrayName[i] + "\")' >" + tmpArrayName[i] + "</a>";

            appendStr += "</li>";
            appendStr += "</ul>";
        }

    $("#recently-used-field div").remove();
    $("#recently-used-field").append(appendStr);
}

function openContent(data_url, funName) {

    var authortiy_id = $("#hidAuthorityID").val();
    var developer_id = "5";

    // var user_id = $("#hidLoginID").val();
    // if(data_url === "personal/index" && user_id !== "12"){
    //     return;
    // }

    if (authortiy_id === developer_id) {
        window.location.href = "/iPD/" + data_url;
    }
    else {

        var currentDateTime = getCurrentDateAndTime();
        var loginUserName = $("#hiddenLoginUser").val();

        $.ajax({
            type: "post",
            url: "../common/saveAccessLog",
            data: { _token: CSRF_TOKEN, message: "saveAccessLog", functionName: funName, loginUserName: loginUserName, currentDateTime: currentDateTime },
            success: function(data) {
                window.location.href = "/iPD/" + data_url;
            },
            error: function(err) {
                console.log(err);
            }
        });

    }
}

function getCurrentDateAndTime() {
    var current = new Date();
    var year = current.getFullYear();
    var month = current.getMonth() + 1;
    var day = current.getDate();
    var hour = current.getHours();
    var minute = current.getMinutes();
    var second = current.getSeconds();
    var result = year + "." + month + "." + day + " " + hour + ":" + minute + ":" + second;
    return result;
}

function adjustAuthorityMapping(contentsData, authority) {

    var authority_string = authority["authority"];
    var tmpArrayAuthority = authority_string.split(',');
    var result = [];
    for (var i = 0; i < tmpArrayAuthority.length; i++) {
        var tmp = tmpArrayAuthority[i].split(':');
        var content_id = tmp[0];
        var authority_state = tmp[1];

        var aryResult = {};
        aryResult["id"] = content_id;
        aryResult["state"] = authority_state;

        result.push(aryResult);
    }

    for (var i = 0; i < contentsData.length; i++) {
        var tmpId = contentsData[i]["id"];
        var tmpIdStr = tmpId.toString();

        var curState = 0;
        for (var k = 0; k < result.length; k++) {
            var curId = result[k]["id"];
            if (curId === tmpIdStr) {
                curState = result[k]["state"];
                break;
            }
        }

        contentsData[i]["state"] = curState;
    }

    return contentsData;
}

function adjustContentsData(contentsData) {

    var curCategory = "";
    var curCategoryData = [];
    var retData = [];
    var categoryList = {};

    for (var i = 0; i < contentsData.length; i++) {
        var curCategory = contentsData[i]["category"];

        if (categoryList[curCategory]) {
            //存在する
            var tmp = categoryList[curCategory];
            tmp.push(contentsData[i]);
            categoryList[curCategory] = tmp;
        }
        else {
            //存在しない
            var tmp = [];
            tmp.push(contentsData[i])
            categoryList[curCategory] = tmp;
        }
    }

    return categoryList;
}

function DeleteContent() {
    var contents_id_str = $("#hidDeleteContentsID").val();
    var contents_id = parseInt($("#hidDeleteContentsID").val());

    var result = confirm("本当に削除しますか？");
    if (result === true) {
        $.ajax({
            url: "../user/deleteContents",
            type: 'post',
            data: { _token: CSRF_TOKEN, contentsID: contents_id },
            success: function(message) {
                if (message.includes("success")) {
                    DeleteAuthority(contents_id_str);
                }
            },
            error: function(err) {
                alert(JSON.stringify(err));
                console.log(err);
            }
        });
    }
}

function DeleteAuthority(contents_id_str) {

    var tmp_all_authority = $("#hidAllAuthorityData").val();
    var all_authority = JSON.parse(tmp_all_authority);
    var authorityAry = [];

    for (var i = 0; i < all_authority.length; i++) {
        var cur_authority_id = all_authority[i]["id"];
        var cur_authority_name = all_authority[i]["name"];
        var cur_authority_string = all_authority[i]["authority"];

        var authority_result = deleteAuthorityString(contents_id_str, cur_authority_string);

        var tmp_authority = {};
        tmp_authority["id"] = cur_authority_id;
        tmp_authority["name"] = cur_authority_name;
        tmp_authority["authority"] = authority_result;
        authorityAry.push(tmp_authority);
    }

    $.ajax({
        url: "../user/updateAllAuthority",
        type: 'post',
        data: { _token: CSRF_TOKEN, allAuthorityData: authorityAry },
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

function deleteAuthorityString(contents_id, cur_authority_string) {
    var authority_array = cur_authority_string.split(",");
    var result = "";

    // console.log(cur_authority_string);

    for (var i = 0; i < authority_array.length; i++) {
        var tmp = authority_array[i].split(":");
        var cur_contents_id = tmp[0];
        var cur_contents_state = tmp[1];

        if (cur_contents_id !== contents_id) {
            result += authority_array[i] + ",";
        }
    }

    result = result.slice(0, -1);
    // console.log(result);
    return result;
}
