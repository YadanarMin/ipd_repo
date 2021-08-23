/* ajax通信トークン定義 */
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

$(document).ready(function() {
    $.ajaxSetup({
        cache: false
    });

    LoadTemplateList();

    $('#levelDiv').on('click', "ul li", function() {

        $("#levelDiv li").removeClass('selected');
        $(this).addClass('selected');
        var templateName = $(this).find("label").html();
        CreateDocDetails(templateName);
        SetTblTemplateVariable(templateName);
    });

    $("#displayTemplateFunc").addClass("tab_switch_on");
});

function switchDisplayExcelDownload() {
    window.location = "../document/templateConsoleWord";
}

function switchDisplayExcelMakeTemplate() {
    window.location = '../document/downloadConsoleWord';
}

function LoadTemplateList() {
    $.ajax({
        url: "../document/getWordData",
        async: true,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "get_template_list" },
        success: function(data) {
            console.log(data);
            if (data != null) {
                CreateTemplateList(data);
            }
        },
        error: function(err) {
            console.log(err);
            alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });
}

function CreateTemplateList(data) {
    var li = "";
    $.each(data, function(key, item) {
        li += "<li>";
        //li += "<div id='template"+key+"' class='levelElement'>";
        li += "<label>" + item["name"] + "</label>";
        //li += "</div>";
        li += "</li>";
    });
    $("#levelDiv ul li").remove();
    $("#levelDiv ul ").append(li);

}

function CreateDocDetails(templateName) {
    var appendText = "";

    appendText += "<div class='doc-details-header'>";

    appendText += "<div>";
    appendText += "<h3 style='display:inline-block;'>" + templateName + "</h3>";
    appendText += "</div>";
    appendText += "<div style='margin-left:auto;margin-top:17px;'>";
    appendText += "<form id ='allstoreForm' name ='allstoreForm' method='POST'>";
    appendText += "<div style='padding: 1% 0% 0% 0%;float:left;display:flex;'>";
    // appendText += "<input type = 'button' class='btn btn-primary' name = 'selectProject' id ='selectProject' value = 'プロジェクト選択' onclick ='getAllstoreManagementInfo()'/>";
    // appendText += "&nbsp;&nbsp;&nbsp;";
    appendText += "<input type = 'button' class='btn btn-primary' name = 'output' id ='output' value = '出力' onclick='outputDocument(\"" + templateName + "\")'/>";
    // appendText += "<input type = 'button' style ='margin-left:10px;' class='btn btn-primary' name = 'delete' id ='delete' value = '削除' onclick =''/>";
    appendText += "</div>";
    appendText += "</form>";
    appendText += "</div>";

    appendText += "</div>"; //<!--doc-details-header-->


    appendText += "<div class='doc-details-body'>";

    appendText += "<div class='doc-comment'>";
    appendText += "<h4 style='display:inline-block;'>テンプレートの説明</h4>";
    appendText += "<div id='docComment' style='height:90px;border:1px solid #a9a9a9;color:#a9a9a9;padding:10px 10px 10px 10px;'>";
    appendText += "</div>";
    appendText += "<div id='select_project_title'></div>";
    appendText += "<div id='combo_selectProject'></div>";
    appendText += "</div>";
    appendText += "<div class='doc-select-pj' id='docSelectProject' style='height:422px;margin:10px 0 0 0;'>";

    appendText += "<div class='centering' id='searchBox' style='display:flex;margin-top:10px;'>";
    appendText += "</div>";
    appendText += "<h4 style='display:inline-block;'>対応表</h4>";
    appendText += "<div class='scroll-table' align='center'><table id='tblTemplateVariable' width='100%'></table></div>";

    appendText += "</div>";

    appendText += "</div>"; //<!--doc-details-body-->

    $("#documentDetails div").remove();
    $("#documentDetails").append(appendText);
}

function SetTblTemplateVariable(templateName) {
    $.ajax({
        url: "../document/getWordData",
        async: true,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "get_template_byname", templateName: templateName },
        success: function(data) {
            console.log(data);
            if (data != null) {
                SetTblTemplateVariableProc(data[0]);
            }
        },
        error: function(err) {
            console.log(err);
            alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });
}

function SetTblTemplateVariableProc(data) {

    $("#tblTemplateVariable tr td").remove();
    // console.log("data");console.log(data);return;

    var template_id = data["id"];
    var template_name = data["name"];
    var file_name = data["file_name"];
    var description = data["description"];
    var item_key_array = data["item_key"].split(',');
    var item_val_array = data["item_val"].split(',');

    if (item_key_array.length !== item_val_array.length) {
        alert("template data load error.\n管理者に問い合わせください。");
        return;
    }

    var appendText = "";
    var descriptionText = "";

    appendText += "<thead>";
    appendText += "<tr>";
    appendText += "<th>日本語</th>";
    appendText += "<th>置換文字列</th>";
    appendText += "</tr>";
    appendText += "</thead>";

    appendText += "<tbody>";
    for (var i = 0; i < item_val_array.length; i++) {
        appendText += "<tr>";
        appendText += "<td>" + item_key_array[i] + "</td>";
        appendText += "<td>" + item_val_array[i] + "</td>";
        appendText += "</tr>";
    }
    appendText += "</tbody>";

    descriptionText += description;

    $("#tblTemplateVariable").append(appendText);
    $("#docComment").append(descriptionText);

    var projects = GetAllStoreProjectNamesAndPJID();
    console.log(projects);
    if (projects != "") {
        var cmb = "";
        cmb += "<select id='cmb_pj_name'>";
        $.each(projects, function(key, value) {
            cmb += "<option value='" + value['pj_code'] + "'>" + value['project_name'] + "</option>";
        });
        cmb += "</select>";
        $("#combo_selectProject select").remove();

        var titleAppend = "<h4 style='display:inline-block;'>プロジェクト選択</h4>";
        $("#select_project_title").append(titleAppend);
        $("#combo_selectProject").append(cmb);
        $("#cmb_pj_name").select2();
    }
}

function GetAllStoreProjectNamesAndPJID() {
    var result = "";
    $.ajax({
        url: "../document/getData",
        async: false,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "get_allstore_name_pjid" },
        success: function(data) {
            console.log(data);
            if (data != null && data.length > 0) {
                result = data;
            }
        },
        error: function(err) {
            console.log(err);
            return result;
            //alert("Failed in delete. Please try again!!");
        }
    });

    return result;
}

function getAllstoreManagementInfo() {

    console.log("getAllstoreManagementInfo start");

    var data = [{ "id": 1, "name": "テンプレートA", "item_key": "tyakkou,nobe_menseki", "item_val": "${tyakkou},${nobe_menseki}" },
        { "id": 2, "name": "テンプレートB", "item_key": "syunkou,youto", "item_val": "${syunkou},${youto}" }
    ];

    createTblSelectProject(data[0]);

    // $.ajax({
    //     url: "../allstore/getData",
    //     type: 'post',
    //     data:{_token: CSRF_TOKEN},
    //     success :function(data) {
    //         if(data != null){
    //         	console.log(data);

    //   createTblSelectProject(data);
    //         }
    //     },
    //     error:function(err){
    //         console.log(err);
    //     }
    // });

}

function createTblSelectProject(data) {
    $("#tbCheckBox tr td").remove();

    var template_id = data["id"];
    var template_name = data["name"];
    var item_key_array = data["item_key"].split(',');
    var item_val_array = data["item_val"].split(',');
    if (item_key_array.length !== item_val_array.length) {
        alert("template data error.\n管理者に問い合わせください。");
        return;
    }

    var newChkRow = "";
    var searchBox = "";

    searchBox += "<div class='form-group has-search' style='margin-bottom:0px;margin-left:-5px;'>";
    searchBox += "<span class='glyphicon glyphicon-search form-control-feedback'></span>";
    // searchBox += "<input type='text' class='form-control' id='txtSearch' placeholder='プロジェクトコード＆プロジェクト名称検索' style='width:900px;'>";
    searchBox += "</div>";


    newChkRow += "<thead>";
    newChkRow += "<tr>";
    newChkRow += "<th>No.</th>";
    newChkRow += "<th>テンプレート名</th>";

    for (var i = 0; i < item_key_array.length; i++) {
        newChkRow += "<th>" + item_key_array[i] + "</th>";
    }

    newChkRow += "</tr>";
    newChkRow += "</thead>";

    newChkRow += "<tbody>";

    newChkRow += "<tr>";

    newChkRow += "<td>" + template_id + "</td>";
    newChkRow += "<td>" + template_name + "</td>";

    for (var i = 0; i < item_val_array.length; i++) {
        newChkRow += "<td>" + item_val_array[i] + "</td>";
    }

    newChkRow += "</tr>";

    newChkRow += "</tbody>";

    $("#searchBox div").remove();
    $("#searchBox").append(searchBox);
    $("#tbCheckBox").append(newChkRow);


    $('#txtSearch').keyup(function() {
        var textboxValue = $('#txtSearch').val();
        // alert("keyup:"+textboxValue);
        $("#tbUser tbody >tr").each(function(index) {
            $row = $(this);
            var pj_code = $row.find("td:eq(1)").text();
            var pj_name = $row.find("td:eq(2)").text();
            //alert(name+"\n"+email);
            if (!pj_code.includes(textboxValue) && !pj_name.includes(textboxValue)) {
                //if(index != 0)//skip when body header row
                $row.hide();

            }
            else {
                $row.show();
            }
        });

        $("#tbCheckBox tbody >tr").each(function(index) {
            $row = $(this);
            var pj_name = $row.find('input[type=hidden]').val();
            var pj_code = $row.find('input[type=checkbox]').val();
            // console.log(pj_name+"\n"+pj_code);
            if (!pj_code.includes(textboxValue) && !pj_name.includes(textboxValue)) {
                //if(index != 0)//skip when body header row
                $row.hide();

            }
            else {
                $row.show();
            }
        });

    });

}

function outputDocument(templateName) {

    var select_pj_code = "";
    if ($("#cmb_pj_name").val() != undefined) {
        select_pj_code = $("#cmb_pj_name").val();
        select_pj_code = "," + select_pj_code;
    }
    console.log(templateName);
    console.log(select_pj_code);

    window.location = "/iPD/document/outputWordTemplate/" + templateName + select_pj_code;
}
