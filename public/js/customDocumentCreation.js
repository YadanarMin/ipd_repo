var right_clicked_item = "";
var g_max_table_id = 0;

$(document).ready(function() {

    //アクセス履歴更新
    var login_user_id = $("#hidLoginID").val();
    var img_src = "../public/image/JPG/会員証のアイコン素材.jpeg";
    var url = "customDocument/index";
    var content_name = "ｶｽﾀﾑ書類作成";
    recordAccessHistory(login_user_id, img_src, url, content_name);

    // $("body").bind("mousedown", function () { $(".custom-menu").hide(100);});

    //保存した書類データを取得し画面に描画
    var format = GetFormatFromJSON();
    if (format != "") {
        LoadCustomFormat(format);
    }
    // $('table th').on("dragstart", function(event) {
    //     alert("drag td");
    // });
    $("#drop-area").resizable();
    $(".drag").draggable({ helper: "clone" });
    $("#drop-area > .ipd-panel").resizable();
    $("table").on('mouseenter', 'th', function() {
        // alert("a");
        // if (!$(this).data('draggable'))
        var div = $(this).find('div');
        $(div).draggable({});
    });

    IPDCodeKeyUpEventPanel();
    // IPDCodeKeyUpEventTable();
    DrawDropArea();
    DeleteCustomItem();

    $(document).on("contextmenu", ".custom-lbl", function(e) {
        right_clicked_item = $(this);
        return false;
    });

    //ItemRightClickEvent();
    //RightClickMenuEvent();

    $("#tableRowNumSelect").change(function() {});

    $("#tableColumnNumSelect").change(function() {});

});

function DrawDropArea() {

    $("#drop-area").droppable({
        accept: ".drag",
        // activeClass: "snaptarget-hover",
        drop: function(event, ui) {

            var ct = $(this);

            var item = $(ui.draggable);
            var itemName = item.html();
            var item_class = item.attr('class');
            var item_id = item.attr('id');
            var item_text = item.text();

            // console.log(itemName);
            // console.log(item_class);
            // console.log(item_id);
            // console.log(item_text);

            var origPos;
            var ctPos = ct.offset();
            var item_x_min = ui.offset.left;
            var item_x_max = ui.offset.left + item.width();
            var item_y_min = ui.offset.top;
            var item_y_max = ui.offset.top + item.height();

            var addPanel = FindPanel(item_x_max, item_x_min, item_y_max, item_y_min);
            var tmpTable = FindTable(item_x_max, item_x_min, item_y_max, item_y_min);
            var addTable = tmpTable["table"];

            if (item.is('.drop')) {
                origPos = ui.offset;
                if (addPanel != "" && !item.is('.ipd-panel') && !item.is('.ipd-table')) {
                    item.appendTo(addPanel);
                    console.log("aaa");
                }
                else if (addTable != "") {
                    var rabel_id = item.find(".custom-lbl").attr('id');
                    var rabel_name = item_text;
                    console.log("bbb");
                }
                else {
                    item.appendTo(ct);
                    console.log("ccc");
                }

            }
            else {

                origPos = ui.offset;
                var appendStr = "";
                if (itemName == "panel") {
                    //alert("panel");
                    item = item.clone();
                    console.log(item);
                }
                else if (itemName == "table") {
                    var rowNum = $("#tableRowNumSelect :selected").val();
                    var clmNum = $("#tableColumnNumSelect :selected").val();
                    appendStr += '<div draggable="true" class="ipd-table drag form-group ui-draggable ui-draggable-handle">';

                    var tmpStr = create_keywords_table(rowNum, clmNum);

                    appendStr += tmpStr;
                    appendStr += '</div>';
                    item = $(appendStr);

                    // create_keyword_list();
                    // bind_droppable();
                }
                else if (itemName == "iPDコード") {
                    appendStr += '<div draggable="true" class="drag form-group ui-draggable ui-draggable-handle">';
                    appendStr += '<div draggable="true" class="custom-flex-lbl flex-column" style="z-index: 1;">';
                    appendStr += '<label id="' + item_id + '" class="custom-lbl">iPDコード</label><input type="text" class="txtIPDCode" style="width: 180px;"></div>';
                    appendStr += '</div>';
                    item = $(appendStr);

                }
                else {
                    appendStr += '<div draggable="true" class="drag ui-draggable ui-draggable-handle" style="z-index: 2;">';
                    appendStr += '<div class="custom-flex-lbl flex-column">';
                    appendStr += '<label id="' + item_id + '" class="custom-lbl">' + itemName + '</label>';
                    appendStr += '<label class="custom-lbl-val"></label>';
                    appendStr += '</div>';
                    appendStr += '</div>';
                    item = $(appendStr);
                    //item = item.clone();
                }

                if (item_class.includes("ipd-panel")) {
                    item.html('');
                    ct.append(item);
                    item.addClass('custom-panel');
                    console.log("fff");
                }
                else if (addTable != "" && item_id !== undefined) {
                    console.log("ggg");
                    appendStr = '<div>';
                    appendStr += item_text;
                    appendStr += '</div>';
                    insertTableHeader(item_id, appendStr, tmpTable);

                    // var lbl_ele = "<label id='" + rabel_id + "'>" + rabel_name + "</label>";
                    // item = $(lbl_ele);
                }
                else {
                    console.log("hhh");
                    //var addPanel = FindPanel(item_x_max, item_x_min, item_y_max, item_y_min);
                    if (addPanel != "" && !item.is('.ipd-panel') && !item.is('.ipd-table')) {
                        item.appendTo(addPanel);
                    }
                    else {
                        item.appendTo(ct);
                    }
                    //item.css({zIndex:1});
                }

                item.removeClass("ui-draggable");
                item.addClass('drop');
                item.draggable();
                if (!item.is('.ipd-table'))
                    item.draggable();
                if (item.is('.ipd-panel')) {
                    item.resizable();
                }

            }

            if (addPanel != "" && !item.is('.ipd-panel') && !item.is('.ipd-table')) {
                item.css({ top: origPos.top - addPanel.offset().top - 1, left: origPos.left - addPanel.offset().left - 1 });
            }
            else {
                item.css({ top: origPos.top - ctPos.top - 1, left: origPos.left - ctPos.left - 1 });
            }

        }
    });

}

// function insertTableHeader(id, type, row, column) {
function insertTableHeader(id, text, tableInfo) {

    var row = tableInfo["row"];
    var column = tableInfo["column"];
    var table_id = tableInfo["id"];
    var type = tableInfo["type"];

    if (type === "1") {
        var column_idx = 1;
        $("#" + table_id + " tr").each(function() {
            if (column_idx === column) {
                $(this).children("th").first().attr("id", id);
                $(this).children("th").first().append(text);
                return false;
            }
            column_idx++;
        });
    }
    else if (type === "2") {
        var row_idx = 1;
        $("#" + table_id + " thead th").each(function() {
            if (row_idx === row) {
                $(this).attr("id", id);
                $(this).append(text);
                return false;
            }
            row_idx++;
        });
    }
    else {
        console.log("typenone");
        //NOP
    }

}

function ItemRightClickEvent() {
    // Trigger action when the contexmenu is about to be shown
    $("#drop-area").bind("contextmenu", function(event) {
        // Avoid the real one
        event.preventDefault();
        // Show contextmenu
        $(".custom-menu").toggle(100).
        // In the right position (the mouse)
        css({
            top: event.pageY + "px",
            left: event.pageX + "px"
        });
    });

}

function RightClickMenuEvent() {
    $(".custom-menu li").click(function() {
        // This is the triggered action name
        switch ($(this).attr("data-action")) {
            // A case for each action. Should personalize to your actions
            case "hide_one":
                HideCurrentItemTitle();
                break;
            case "show_one":
                ShowCurrentItemTitle();
                break;
            case "hide_all":
                HideAllItemTitle();
                break;
            case "show_all":
                ShowAllItemTitle();
                break;
            case "switch_one_style":
                SwitchCurrentItemStyle();
                break;
            case "switch_all_style":
                SwitchAllItemStyle();
                break;
        }
    });

}

function HideCurrentItemTitle() {

    if (right_clicked_item != "") {
        right_clicked_item.hide();
    }

    //$("#drop-area .custom-lbl").hide();
    $(".custom-menu").hide(100);
}

function ShowCurrentItemTitle() {
    if (right_clicked_item != "") {
        right_clicked_item.show();
    }
    $(".custom-menu").hide(100);
}

function HideAllItemTitle() {
    //alert(ele.find('.custom-lbl').html());
    $("#drop-area .custom-lbl").hide();
    $(".custom-menu").hide(100);
}

function ShowAllItemTitle() {
    $("#drop-area .custom-lbl").show();
    $(".custom-menu").hide(100);
}

function SwitchCurrentItemStyle() {

    if (right_clicked_item != "") {
        var flex = right_clicked_item.closest(".custom-flex-lbl");
        if (flex.hasClass('flex-column')) {
            flex.removeClass('flex-column');
            flex.addClass('flex-row')
        }
        else {
            flex.removeClass('flex-row');
            flex.addClass('flex-column')
        }
    }

    $(".custom-menu").hide(100);
}

function SwitchAllItemStyle() {
    var flex = $(".custom-flex-lbl");
    if (flex.hasClass('flex-column')) {
        flex.removeClass('flex-column');
        flex.addClass('flex-row')
    }
    else {
        flex.removeClass('flex-row');
        flex.addClass('flex-column')
    }
    $(".custom-menu").hide(100);
}

function IPDCodeKeyUpEventPanel() {
    $(".txtIPDCode").on('keyup', function(e) {
        e.preventDefault();
        var iPDCode = $(this).val();
        var ipd_panel = $(this).closest('.ipd-panel');
        var customDiv = (ipd_panel != undefined) ? ipd_panel.find('.custom-lbl') : $("#drop-area .custom-lbl");
        //alert(ipd_panel);
        if (iPDCode != "") {
            var projectInfo = GetProjectInfoByCode(iPDCode);
            console.log(projectInfo);
            console.log(customDiv);
            if (projectInfo != "") {
                customDiv.each(function() {

                    var itemID = $(this).attr('id');
                    $("#drop-area").append();
                    // alert(lblName);
                    if (!itemID.includes("a_pj_code"))
                        $(this).siblings().html(projectInfo[itemID]);
                });
            }
        }
    });
}

function IPDCodeKeyUpEventTable() {
    $(".tblCodeInput").on('keyup', function(e) {
        e.preventDefault();
        var iPDCode = $(this).val();
        var ipd_table = $(this).closest('.ipd-table');
        var tmpElem = $(this).parent().parent().parent().parent();
        var table_id = $(tmpElem).attr('id');
        var table_class = $(tmpElem).attr('class');
        var table_type = table_class.replace("tblUser tableType", "");
        var keyupIndex = 0;
        if (table_type === "1") {
            keyupIndex = $("#" + table_id + " thead tr th").index($(this).parent()) + 1;
        }
        else {
            keyupIndex = $("#" + table_id + " tbody tr").index($(this).parent().parent()) + 1;
        }

        console.log("iPDCode:" + iPDCode);
        console.log(table_id);
        console.log(table_type);
        console.log(keyupIndex);

        if (iPDCode != "") {
            var projectInfo = GetProjectInfoByCode(iPDCode);
            console.log(projectInfo);

            if (table_type === "1") {

                var cur_column = 1;
                $("#" + table_id + " tbody tr").each(function() {

                    var cur_row = 1;
                    var id = $(this).find('th').attr('id');
                    var name = $(this).find('th').find('div').text();

                    if (projectInfo != "") {

                        if (id !== undefined) {
                            $(this).find("td:nth-child(" + keyupIndex + ")").text(projectInfo[id]);
                        }

                        // $(this).find('td').each(function() {
                        //     console.log("[" + keyupIndex + "]" + "[" + id + "][" + name + "]" + cur_column + ":" + cur_row);
                        //     cur_row++;
                        // });
                    }
                    else {
                        $(this).find("td:nth-child(" + keyupIndex + ")").text("");
                    }
                    cur_column++;
                });
                $(this).attr("placeholder", iPDCode);
            }
            else {

                if (projectInfo != "") {

                    //theadの2番目以降のthループでidリスト取得
                    var idList = [];
                    $("#" + table_id + " thead tr th").each(function(index) {
                        if (index === 0) {
                            return true;
                        }

                        var cur_id = $(this).attr('id');
                        idList.push(cur_id);
                    });
                    console.log(idList);

                    $("#" + table_id + " tbody").find("tr:nth-child(" + keyupIndex + ") th").find('.tblCodeInput').attr("placeholder", iPDCode);
                    $("#" + table_id + " tbody").find("tr:nth-child(" + keyupIndex + ") td").each(function(index) {
                        console.log("aaa");
                        var pjInfo_index = idList[index];
                        $(this).text(projectInfo[pjInfo_index]);
                    });
                }

            }

        }

    });
}

function DeleteCustomItem() {
    $('#trash').droppable({

        drop: function(event, ui) {
            //alert("trash");

            var item = $(ui.draggable);
            var item_class = item.attr('class');

            //table削除のときは他のテーブルのID末尾の数字をデクリメント
            if (item_class.includes("ipd-table")) {
                var tbl = item.find(".tblUser");
                var tbl_id = tbl.attr('id');
                var tbl_id_num = parseInt(tbl_id.replace("table", ""));

                if (g_max_table_id !== tbl_id_num) {

                    $("#drop-area .ipd-table").each(function() {

                        var delete_item_id = item.find('.tblUser').attr('id');
                        var cur_item_id = $(this).find('.tblUser').attr('id');

                        if (delete_item_id === cur_item_id) {
                            return true;
                        }

                        //'.tblUser'のid末尾の数字をデクリメント(e.g.[table2]->[table1])
                        var tmpId = $(this).find('.tblUser').attr('id');
                        tmpId = tmpId.replace("table", "");
                        var decId = parseInt(tmpId) - 1;
                        $(this).find('.tblUser').attr('id', "table" + decId.toString());
                    });
                }

                g_max_table_id--;
            }

            ui.draggable.remove();
            //$('#drop-area').cleanWhitespace();
        }
    });
}

function FindPanel(item_x_max, item_x_min, item_y_max, item_y_min) {
    var panel = "";
    $("#drop-area .ipd-panel").each(function() {

        var panel_X_start = $(this).offset().left;
        var panel_X_end = panel_X_start + $(this).width();
        var panel_Y_start = $(this).offset().top;
        var panel_Y_end = panel_Y_start + $(this).height();
        // console.log("panel");
        // console.log(panel_X_start + "\n" + panel_X_end + "\n" + panel_Y_start + "\n" + panel_Y_end);

        if ((panel_X_start < item_x_max && item_x_max < panel_X_end) &&
            (panel_X_start < item_x_min && item_x_min < panel_X_end) &&
            (panel_Y_start < item_y_max && item_y_max < panel_Y_end) &&
            (panel_Y_start < item_y_min && item_y_min < panel_Y_end)
        ) {
            panel = $(this);
            return;
        }
    });

    return panel;
}

function FindTable(item_x_max, item_x_min, item_y_max, item_y_min) {
    var table = "";
    var id = "";
    var type = "";
    var row = 0;
    var column = 0;
    var isFound = false;

    $("#drop-area .ipd-table").each(function() {

        var table_X_start = $(this).offset().left;
        var table_Y_start = $(this).offset().top;
        var table_X_end = 0;
        var table_Y_end = 0;
        var tmp = $(this).children().attr('class');
        var tmpArray = tmp.split(" ");
        var table_type = tmpArray[1];
        table_type = table_type.replace("tableType", "");

        if (table_type === "1") {
            table_Y_start = table_Y_start + 40;
            table_X_end = table_X_start + $(this).find(".tbody_th1").width();
            table_Y_end = table_Y_start + $(this).height() - 40;
            // console.log("tableType=1");
        }
        else if (table_type === "2") {
            table_X_start = table_X_start + 180;
            table_X_end = table_X_start + $(this).width() - 180;
            table_Y_end = table_Y_start + $(this).find(".tbody_th1").height();
            // console.log("tableType=2");
        }
        else if (table_type == "3") {
            var tblId = $(this).find("table").attr('id');
            //alert(tblId);
            // var item_mid_x = (item_x_max + item_x_min) / 2;
            // var item_mid_y = (item_y_max + item_y_min) / 2;
            $("#" + tblId + " th").each(function(idx) {
                var th_start_x = $(this).offset().left;
                var th_end_x = $(this).offset().left + $(this).width();
                var th_start_y = $(this).offset().top;
                var th_end_y = $(this).offset().top + $(this).height();


                //console.log(th_start_x + "\n" + th_end_x + "\n" + item_x_min + "\n" + item_x_max);
                if ((th_start_x > item_x_min && item_x_max < th_end_x)) { //(th_start_x < item_x_min && item_x_min < th_end_x)
                    console.log("Yadanar===========");
                    var col_idx = $(this).index();
                    var row_idx = $(this).closest('tr').index();
                    //alert(row_idx + "\n" + col_idx);
                    table = tblId;
                    type = table_type;
                    row = row_idx + 1;
                    column = col_idx;
                    //return { "table": table, "id": tblId, "type": table_type, "row": row_idx, "column": col_idx };
                }

            });
        }
        else {

            table_X_end = table_X_start + $(this).width();
            table_Y_end = table_Y_start + $(this).height();
            // console.log("tableType=other");
        }

        // console.log("table");
        // console.log(table_X_start + " < " + item_x_max + " && " + item_x_max + " < " + table_X_end);
        // console.log(table_X_start + " < " + item_x_min + " && " + item_x_min + " < " + table_X_end);
        // console.log(table_Y_start + " < " + item_y_max + " && " + item_y_max + " < " + table_Y_end);
        // console.log(table_Y_start + " < " + item_y_min + " && " + item_x_max + " < " + table_Y_end);
        //if (table_type == "3") return;
        if (table_type != "3") {
            if ((table_X_start < item_x_max && item_x_max < table_X_end) &&
                (table_X_start < item_x_min && item_x_min < table_X_end)
                // (table_Y_start < item_y_max && item_y_max < table_Y_end) &&
                // (table_Y_start < item_y_min && item_y_min < table_Y_end)
            ) {
                id = $(this).children().attr('id');
                type = table_type;
                table = $(this);

                var target_x_len = item_x_min - table_X_start;
                var target_y_len = item_y_min - table_Y_start;
                // console.log("item_x_min:" + item_x_min);
                // console.log("table_X_start:" + table_X_start);
                // console.log("item_y_min:" + item_y_min);
                // console.log("table_Y_start:" + table_Y_start);
                // console.log("target_x_len:" + target_x_len);
                // console.log("target_y_len:" + target_y_len);
                if (table_type === "1") {
                    row = 1;
                    column = Math.floor(target_y_len / 40) + 2;
                }
                else if (table_type === "2") {
                    row = Math.floor(target_x_len / 180) + 2;
                    column = 1;
                }

                return;
            }
        }

    });

    // if (table == "") {
    //     console.log("NG!!");
    // }
    // else {
    //     console.log("OK!!");
    //     console.log(table);
    //     console.log("row:" + row);
    //     console.log("column:" + column);
    // }
    return { "table": table, "id": id, "type": type, "row": row, "column": column };
}

function GetProjectInfoByCode(iPDCode) {
    var result = "";
    $.ajax({
        url: "../customDocument/getData",
        type: 'post',
        async: false,
        data: { _token: CSRF_TOKEN, message: "get_allstore_bycode", iPDCode: iPDCode },
        success: function(data) {
            if (data.length > 0) {
                result = data[0];
            }

        },
        error: function(err) {
            console.log("=======error=========");
            console.log(err);
            return result;
        }
    });
    return result;
}

function beautifycode(htmlstr) {
    htmlstr = htmlstr.split(/\>[ ]?\</).join(">\n<");
    htmlstr = htmlstr.split(/([*]?\{|\}[*]?\{|\}[*]?)/).join("\n");
    htmlstr = htmlstr.split(/[*]?\;/).join("\;\n    ");
    return htmlstr;
}

function SaveFormat() {

    //Remove ui-resizable div e,s,es
    $("#drop-area .ipd-panel").children().each(function() {
        var childClass = $(this).attr('class');
        if (childClass.includes("ui-resizable-handle")) {
            $(this).remove();
        }
    });

    $("#drop-area .txtIPDCode").each(function() {
        $(this).attr("value", $(this).val());
    });

    var html = $('#drop-area').html(); //('#drop-area').get(0).outerHTML;


    //console.log(html);return;
    var pos = $("#drop-area").position();
    var x = pos.left;
    var y = pos.top
    var width = $("#drop-area").height();
    var height = $("#drop-are").width();

    console.log(html);
    var tempList = { divContents: html, "x": x, "y": y, "width": width, "height": height };
    // var dropContent = beautifycode($("#drop-area").html());
    // console.log(html);
    $.ajax({
        url: "../customDocument/save",
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "save_format", jsonData: JSON.stringify(tempList) },
        success: function(result) {
            if (result != "") {
                var data = JSON.parse(result);
                //\LoadCustomFormat(data);
            }

            //console.log((data["divContents"]));

            location.reload(true);

        },
        error: function(err) {
            console.log("=======error=========");
            console.log(err);
        }
    });
    //console.log(pageContent);
}


function LoadCustomFormat(data) {
    var divContents = data["divContents"];
    var x = data["x"];
    var y = data["y"];
    // var divEle = $.parseHTML(divContents);
    // console.log(divEle);
    $("#drop-area").append(divContents.trim());
    // console.log($("#drop-area").html());
    //$("body").append(divContents);
    //$("#drop-area").css({ left: x ,top:y });
}

function GetFormatFromJSON() {
    //var jsonPath = "/iPD/public/CustomFormat/test.json";
    var result = "";
    $.ajax({
        url: "../customDocument/getData",
        type: 'post',
        async: false,
        data: { _token: CSRF_TOKEN, message: "get_format_json" },
        success: function(data) {
            // console.log(data);
            if (data != "") {
                ///var data = json;
                LoadCustomFormat(JSON.parse(data));
                // console.log(data);

                $("#drop-area > .ipd-table").each(function() {
                    g_max_table_id++;
                });
                console.log("g_max_table_id:" + g_max_table_id);

            }

        },
        error: function(err) {
            console.log("=======error=========");
            console.log(err);
            return result;
        }

    });

    return result;

    // $.getJSON(jsonPath, function(json) {
    //   if(json != ""){
    //     LoadCustomFormat(json);
    //     }// this will show the info it in firebug console
    // });

}

function SendMail() {
    alert("mail");
}



//*********************************************************************************

const add_list = ["あ", "い", "う"];
var now_drag_object = null;
var titles = [];
var elements = [];

function create_keywords_table(rowNum, columnNum) {
    var table_html = '';
    var tableType = $("input[name='tableType']:checked").val();
    g_max_table_id++;
    table_html += '<table class="tblUser tableType' + tableType + '" id="table' + g_max_table_id + '" align="center">';
    table_html += '<thead>';
    table_html += '<tr class="head" style="background-color:lightgray;">';
    if (tableType == "3") {
        table_html += '<th style="width:180px;height:40px;text-align:center;">支店</th>';
    }
    else {
        table_html += '<th style="width:180px;height:40px;text-align:center;">PJ Code.</th>';
    }

    // table_html += '<th style="width:30px;text-align:center;">PJコード</th>';

    for (var j = 1; j <= columnNum; j++) {

        if (tableType === "1") {
            table_html += '<th class="tableDesign">';
            table_html += '<input type="text" class="tblCodeInput form-control input-sm" style="width:180px;">';
        }
        else {
            table_html += '<th class="tableDesign" style="background-color:darkgrey;width:180px;">';
        }

        // $.each(elements, function(index, element) {
        //     if (element["row"] != i || element["column"] != j) return true;
        //     table_html += '<div class="table-content" data-id="' + element["id"] + '">' + element["name"] + '</div>';
        // });

        table_html += '</th>';
    };
    table_html += '</tr>';
    table_html += '</thead>';
    table_html += '<tbody>';

    for (var i = 1; i <= rowNum; i++) {

        if (tableType === "1") {
            table_html += '<tr class="content" style="background-color:whitesmoke;">';
            table_html += '<th draggable="true" class="tbody_th' + i + '" style="width:180;height:40px;text-align:center;background-color:darkgrey;">';
            table_html += '</th>';
        }
        else if (tableType == "2") {
            table_html += '<tr class="content" style="background-color:whitesmoke;">';
            table_html += '<th class="tbody_th' + i + '" style="width:180;height:40px;text-align:center;">';
            table_html += '<input type="text" class="tblCodeInput form-control input-sm" style="width:180px;">';
            table_html += '</th>';
        }
        else if (tableType == "3") {
            table_html += '<tr class="content" style="background-color:whitesmoke;">';
            table_html += '<th class="tbody_th' + i + '" style="width:180;height:40px;text-align:center;">';
            table_html += '</th>';
        }

        // table_html += '<td class="tableDesign droppable" data-row="" data-column=""></td>';

        for (var j = 1; j <= columnNum; j++) {
            table_html += '<td class="tableDesign droppable" data-row="' + i + '" data-column="' + j + '">';
            $.each(elements, function(index, val) {
                if (val["row"] != i || val["column"] != j) return true;
                table_html += '<div class="table-content" data-id="' + val["id"] + '">' + val["name"] + '</div>';
            });
            table_html += '</td>';
        };
        table_html += '</tr>';
    };

    table_html += '</tbody>';
    table_html += '</table>';
    // $('#table').html(table_html);

    return table_html;
}

function create_keyword_list() {
    const keyword_list_div = add_list.map(function(element, index, array) { return '</p><div class="addable-content">' + element + '</div><p>' }).join('');
    $('.addable-contents').html(keyword_list_div);
}

function bind_droppable() {
    $('.table-content').draggable({
        start: function() {
            now_drag_object = $(this);
        },
        stop: function(event, ui) {
            create_contents();
            now_drag_object = null;
        }
    });

    $('.addable-content').draggable({
        start: function() {
            now_drag_object = $(this);
        },
        stop: function(event, ui) {
            create_contents();
            now_drag_object = null;
        }
    });

    $('.droppable').droppable({
        classes: {
            "ui-droppable-hover": "ui-state-hover"
        },
        drop: function(event, ui) {
            console.log("drop");
            const row = $(this).data("row");
            const column = $(this).data("column");
            const id = now_drag_object.data("id");
            const index = elements.map(function(element, index, array) { return element.id }).indexOf(id);
            if (now_drag_object.hasClass("addable-content")) {
                elements.push({ "id": elements.length + 1, "name": now_drag_object.text(), "row": row, "column": column });
            }
            else {
                elements[index]["row"] = row;
                elements[index]["column"] = column;
            }
        }
    });
};

function LoadFormatData() {
    console.log("LoadFormatData start");

    //ipd-panel loop
    loadPanelList();

    //ipd-table loop
    loadTableList();

}

function loadTableList() {

    console.log("ipd-table.each start");

    $("#drop-area .ipd-table").each(function() {
        var iPDCode = $(this).val();
        var tmpElem = $(this).children();
        var table_id = $(tmpElem).attr('id');
        var table_class = $(tmpElem).attr('class');
        var table_type = table_class.replace("tblUser tableType", "");
        var keyupIndex = 0;

        if (table_type === "1") {

            var thead_num = 0;
            var ipdCodeList = [];
            $("#" + table_id + " thead tr th").each(function() {
                thead_num++;

                if (thead_num === 1) {
                    return true;
                }

                var projectInfo = {};
                var pjCode = $(this).children().val();
                if (pjCode !== "" && pjCode.length === 10) {
                    projectInfo = GetProjectInfoByCode(pjCode);
                    if (projectInfo !== {}) {
                        $(this).children().attr("placeholder", pjCode);
                    }
                }
                ipdCodeList.push(projectInfo);
            });

            var cur_column = 1;
            $("#" + table_id + " tbody tr").each(function() {

                var cur_row = 1;
                var name = $(this).find('th').find('div').text();
                var id = $(this).find('th').attr('id');
                if (id !== undefined) {

                    $(this).find('td').each(function(index) {
                        var cur_pj_info = ipdCodeList[index];
                        if (cur_pj_info[id] !== undefined) {
                            $(this).text(cur_pj_info[id]);
                        }

                        cur_row++;
                    });
                }
                cur_column++;
            });
        }
        else if (table_type === "2") {

            var ipdCodeList = [];
            $("#" + table_id + " tbody tr").each(function() {
                var projectInfo = {};
                var pjCode = $(this).find('th').children().val();
                if (pjCode !== "" && pjCode.length === 10) {
                    projectInfo = GetProjectInfoByCode(pjCode);
                    if (projectInfo !== {}) {
                        $(this).find('th').children().attr("placeholder", pjCode);
                    }
                }
                ipdCodeList.push(projectInfo);
            });

            //theadの2番目以降のthループでidリスト取得
            var idList = [];
            $("#" + table_id + " thead tr th").each(function(index) {
                if (index === 0) {
                    return true;
                }

                var cur_id = $(this).attr('id');
                idList.push(cur_id);
            });

            $("#" + table_id + " tbody tr").each(function(row_index) {
                var cur_pj_info = ipdCodeList[row_index];

                $(this).find('td').each(function(column_index) {
                    var id = idList[column_index];
                    $(this).text(cur_pj_info[id]);
                });
            });

        }

    });
}

function loadPanelList() {

    $("#drop-area .ipd-panel").each(function() {

    });

}
