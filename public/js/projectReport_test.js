/* ajax通信トークン定義 */
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
var PLACEHOLDER_NAME_BRANCHSTORE = "支店名";
var PLACEHOLDER_NAME_INCHARGE = "担当者名";
var PLACEHOLDER_NAME_KOUJIKUBUN = "工事区分名";
var active_project = "";
var projectCode = "";
var current_project_name = "";
var prevReportDate = "";
var ordered = false;
var tempInfo;
var image_list = [];
var Current_Domain_Image = [];
var toggleHeight = "300px";

var min_schedule_start_date = "";
var max_schedule_end_date = "";
var current_icon_name = "";
var map_project = [];
var hashtags = [];

$(document).ready(function() {

    var login_user_id = $("#hidLoginID").val();
    var img_src = "/iPD/public/image/JPG/会員証のアイコン素材.jpeg";
    var url = "project/report";
    var content_name = "案件報告";
    recordAccessHistory(login_user_id, img_src, url, content_name);


    LoadData();
    if ($("#hidProjectCode").val() != "") {
        $("#chkOpenNewTag").attr("checked", "checked");
    }
    //$(".reports").hashtags();

    //Testing
    $(document).on("keydown", ".reports", function() {
        //alert("click bound to document listening for #test-element");
        console.log($(this));
        console.log($(this)[0].innerText)
        var text = $(this)[0].innerText;
        MakeHighlight($(this), text);
        // var regex = "/#+([a-zA-Z0-9_]+)/";
        // text = text.replace(/#(([_a-zA-Z0-9ぁ-んァ-ン一-龯\(\)\（\）\.\・\-\ー]+)|()|([\u0600-\u06FF]+)|([ㄱ-ㅎㅏ-ㅣ가-힣]+)|([ぁ-んァ-ン]+)|([一-龯]+))/g, '<span class="hashtag">#$1</span>');
        
        // $(this).children($('.ql-editor')).html(text)
    });

    ReportTypeShowOrHide();

    //Right click event
    $(document).on("contextmenu", ".right_click_icon", function(e) {
        current_icon_name = $(this).html();
        var current_link = $(this).val();
        $("#fileURL").val(current_link);
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

    $.ajaxSetup({
        cache: false,

    });
    $("#bottom-content").hide();
    ClickCaptureImage();
    //TextSearch();

    $("#minimize-arrow").on("click", function(e) {

        if ($(this).hasClass("fa-toggle-left")) {
            $(this).removeClass("fa-toggle-left");
            $(this).addClass("fa-toggle-right");
            $("#search-content").addClass("col-md-2");
            $("#search-content").removeClass("col-md-5");
            $("#map-content,#report-content").addClass("col-md-10");
            $("#map-content,#report-content").removeClass("col-md-7");
            $(".tblScroll").removeClass("lg-scroll-wd");
            $(".tblScroll").addClass("sm-scroll-wd");

            //general info part
            if (!$("#general_report_info").hasClass('general_info_minimize')) {
                $("#report-item-group").removeClass("col-md-8 col-md-9 col-md-12");
                $("#general_report_info").removeClass("col-md-3 col-md-4");
                $("#report-item-group").addClass("col-md-9");
                $("#general_report_info").addClass("col-md-3");
            }

            //$('#tblProjectList-bar').animate({"width":"100%"});

        }
        else {

            $(this).removeClass("fa-toggle-right");
            $(this).addClass("fa-toggle-left");

            $("#search-content").removeClass("col-md-2");
            $("#search-content").addClass("col-md-5");
            $("#map-content,#report-content").removeClass("col-md-10");
            $("#map-content,#report-content").addClass("col-md-7");
            $(".tblScroll").addClass("lg-scroll-wd");
            $(".tblScroll").removeClass("sm-scroll-wd");
            // $('#tblProjectList-bar').animate({"width":"650px;"});

            //general info part
            if (!$("#general_report_info").hasClass('general_info_minimize')) {
                $("#report-item-group").removeClass("col-md-8 col-md-9 col-md-12");
                $("#general_report_info").removeClass("col-md-3 col-md-4");
                $("#report-item-group").addClass("col-md-8");
                $("#general_report_info").addClass("col-md-4");

            }
        }

        ReportHashtagHighlight();
        //reload box image
        var access_token = $("#access_token").val();
        var selectedType1 = $("#img1_type option:selected").val();
        var selectedType2 = $("#img2_type option:selected").val();
        var selectedType3 = $("#img3_type option:selected").val();
        //alert(selectedType1 + "\n" + selectedType2 + "\n" + selectedType3);
        if (image_list[selectedType1] && image_list[selectedType1].length > 0)
            CreateBoxFileContent(image_list[selectedType1], access_token, "#img1", null, selectedType1);
        if (image_list[selectedType2] && image_list[selectedType2].length > 0)
            CreateBoxFileContent(image_list[selectedType2], access_token, "#img2", null, selectedType2);
        if (image_list[selectedType3] && image_list[selectedType3].length > 0)
            CreateBoxFileContent(image_list[selectedType3], access_token, "#img3", null, selectedType3);

        GetXYPosition();

        //TextAreaAutoHeight();
    });

    $("#general_info_minimize_arrow").on("click", function(e) {

        if ($(this).hasClass("fa-toggle-left")) {
            $(this).removeClass("fa-toggle-left");
            $(this).addClass("fa-toggle-right");
            $("#general_report_info").removeClass("col-md-4");
            $("#general_report_info .box2,#general_report_info .box5").hide();
            //$("#general_report_info .box2,#general_report_info .box5").addClass("general_info_minimize");
            $("#general_report_info").addClass("general_info_minimize");
            $("#report-item-group").removeClass("col-md-8 col-md-9");
            $("#report-item-group").addClass("col-md-12");
        }
        else {

            $(this).removeClass("fa-toggle-right");
            $(this).addClass("fa-toggle-left");
            $("#general_report_info").removeClass("general_info_minimize");

            $("#general_report_info .box2,#general_report_info .box5").show();
            $("#general_report_info").removeClass("col-md-3 col-md-4 col-md-5");
            $("#report-item-group").removeClass("col-md-12 col-md-8 col-md-9");
            if ($("#minimize-arrow").hasClass("fa-toggle-left")) {
                $("#general_report_info").addClass("col-md-4");
                $("#report-item-group").addClass("col-md-8");
            }
            else {
                $("#general_report_info").addClass("col-md-3");
                $("#report-item-group").addClass("col-md-9");

            }

        }

        ReportHashtagHighlight();
        //reload box image
        var access_token = $("#access_token").val();
        var access_token = $("#access_token").val();
        var selectedType1 = $("#img1_type option:selected").val();
        var selectedType2 = $("#img2_type option:selected").val();
        var selectedType3 = $("#img3_type option:selected").val();
        if (image_list[selectedType1].length > 0)
            CreateBoxFileContent(image_list[selectedType1], access_token, "#img1", null, selectedType1);
        if (image_list[selectedType2].length > 0)
            CreateBoxFileContent(image_list[selectedType2], access_token, "#img2", null, selectedType2);
        if (image_list[selectedType3].length > 0)
            CreateBoxFileContent(image_list[selectedType3], access_token, "#img3", null, selectedType3);

        GetXYPosition();

        //TextAreaAutoHeight();
    });

    ImageBarShowAndHide();


    // $("#icon_toku").click(function(){
    //     $("#myModal").modal("show");
    // });

    $("#myModal").on('show.bs.modal', function() {
        var projectName = $("#projectName").html();
        $("#parent-prj").html("【" + projectName + "】")
        if (projectCode != "" && projectCode != undefined) {
            var special_feature_info = GetProjectSpecialFeature(projectCode);
            if (special_feature_info != "") {
                $("#txtASpecialFeature").val(special_feature_info[0]["special_feature_info"]);
            }
        }
    });
    //DisplayReportDate();


    $("#branchName").select2({
        placeholder: "支店名"
    });

    $("#inCharge").select2({
        placeholder: "担当者名"
    });

    $("#kouji_kubun").select2({
        placeholder: "工事区分名"
    });

    $("#report_type").select2({
        placeholder: "報告区分名"
    });

    $("#report_type").on("change", function(e) {
        ReportTypeShowOrHide();
    });

    $("#img1_type,#img2_type,#img3_type").on("change", function(e) {
        RebindImages($(this).attr('id'));
    });

    $("#branchName,#inCharge,#kouji_kubun").on("change", function(e) {
        TableRowFilter();
    });
    $('#txtSearch').keyup(function() {
        TableRowFilter();
    });

    $("#tblProjectList tbody").on("click", "tr td", function(e) {
        var idex = $(this).index();
        isOpen = false;
        if (idex > 3) return;
        var tr = $(this).closest('tr');
        current_project_name = tr.find("td:first").html();
        projectCode = tr.find('input[type="hidden"]').val();

        if ($("#chkOpenNewTag").prop("checked") == true) {
            //alert($("#projectName").html());
            if (current_project_name == $("#projectName").html().trim()) return;
            var search_states = {};
            //alert($("#branchName").val().toString());
            search_states["projectName"] = $("#txtSearch").val();
            search_states["branchName"] = $("#branchName").val().toString().replace(/"/g, '');
            search_states["inCharge"] = $("#inCharge").val().toString().replace(/"/g, '');
            search_states["kouji_kubun"] = $("#kouji_kubun").val().toString().replace(/"/g, '');
            search_states["report_type"] = $("#report_type").val().toString().replace(/"/g, '');
            //alert(search_states);
            if ($("#projectName").html() != "")
                window.open("/iPD/project/temp/" + projectCode + "/" + JSON.stringify(search_states), "_blank");
        }
        //perspective_images = [];
        //non_perspective_images = [];
        // if ($("#hidProjectCode").val() == "") return;
        ShowAndHideDiv(tr);
        var param_pj_code = $("#hidProjectCode").val();
        if ($("#projectName").html() == "" || $("#chkOpenNewTag").prop("checked") == false) {
            $("#tblProjectList tbody tr").removeClass("active");

            $(this).closest('tr').toggleClass("active");



            var reportInfo = GetProjectReportByName(current_project_name); //tb_project_report,tb_bimactionplan
            if (reportInfo != null || reportInfo != undefined || reportInfo != "") {
                console.log(reportInfo);
                SetProjectReportInfo(reportInfo["currentWeekData"], reportInfo["currentWeekReport"])
                projectCode = reportInfo["currentWeekData"][0]['a_pj_code'];

            }
            var arrange_flat = 0;
            //get images of project_code
            if (projectCode != "" && projectCode != undefined) {
                var isBoxlogin = CheckBoxAccessToken();
                if (isBoxlogin) {
                    GetAndSetBoxData(projectCode);
                }

                var captures_images = GetCaptureImages(projectCode);
                if (captures_images != "" || !captures_images.includes("error")) {
                    SetCaptureImages(captures_images["capture_files"]);

                }
                //var box_files = GetFilesFromBox(projectCode);
            }
            else {
                ClearImage();
            }

            //reverse div order
            $('#parent > div').each(function() {
                var first_div_id = $("#parent div:first-child").attr('id');
                //alert(first_div_id);
                if (first_div_id == "map-content") return;
                if ($(this).attr("id") != "report-content")
                    $(this).prependTo(this.parentNode);
            });

            $("#map-content").addClass("customize");
            $("#project_regions").css("display", "none");
            $("#report-content").css("display", "block");

            //if($("div").hasClass(".slider-horizontal"))
            if ((reportInfo != null || reportInfo != undefined || reportInfo != "")) {
                tempInfo = reportInfo["currentWeekData"];
                GetXYPosition();
            }
        }

        // Text-editor
        var containers = document.querySelectorAll('.reports');
        var toolbars = document.querySelectorAll('.reports_header');
        console.log(toolbars);
        containers.forEach(function(b, index) {
            var editor = new Quill(b, {
                modules: { toolbar: toolbars[index] },
                theme: 'snow'
            });
        });
        // //hashtag
        // const values = [
        //     { id: 1, value: 'Hashtag1' },
        //     { id: 2, value: 'Hashtag' }
        // ];

        // containers.forEach(function(b, index) {
        //     const quill = new Quill(editor, {
        //         modules: {
        //             mention: {
        //                 allowedChars: /^[A-Za-z\sÅÄÖåäö]*$/,
        //                 source: function(searchTerm) {
        //                     if (searchTerm.length === 0) {
        //                         this.renderList(values, searchTerm);
        //                     }
        //                     else {
        //                         const matches = [];
        //                         for (i = 0; i < values.length; i++)
        //                             if (~values[i].value.toLowerCase().indexOf(searchTerm)) matches.push(values[i]);
        //                         this.renderList(matches, searchTerm);
        //                     }
        //                 },
        //             },
        //         }
        //     });
        // });


    });

    var param_pj_code = $("#hidProjectCode").val();
    if (param_pj_code != null && param_pj_code != "") {
        //alert($("#hidSearchStates").val());
        var search_states = JSON.parse($("#hidSearchStates").val());
        if (search_states["projectName"]) {
            $("#txtSearch").val(search_states["projectName"]);
            TableRowFilter();

        }

        if (search_states["branchName"]) {
            var branchNames = search_states["branchName"];
            var branchList = branchNames.split(',');
            $("#branchName").val(branchList).trigger('change');
        }
        if (search_states["inCharge"]) {
            var inChargeNames = search_states["inCharge"];
            var inChargeList = inChargeNames.split(',');
            $("#inCharge").val(inChargeList).trigger('change');
        }
        if (search_states["kouji_kubun"]) {
            var kouji_kubunNames = search_states["kouji_kubun"];
            var kouji_kubunList = kouji_kubunNames.split(',');
            $("#kouji_kubun").val(kouji_kubunList).trigger('change');
        }
        if (search_states["report_type"]) {
            var report_typeNames = search_states["report_type"];
            var report_typeList = report_typeNames.split(',');
            $("#report_type").val(report_typeList).trigger('change');
        }
        projectCode = param_pj_code;
        var reportInfo = GetProjectReportByName(null); //tb_project_report,tb_bimactionplan
        if (reportInfo != null || reportInfo != undefined || reportInfo != "") {
            //console.log(reportInfo);
            SetProjectReportInfo(reportInfo["currentWeekData"], reportInfo["currentWeekReport"]);
            projectCode = reportInfo["currentWeekData"][0]['a_pj_code'];
            SetRowClick(projectCode);

        }
        var arrange_flat = 0;
        //get images of project_code
        if (projectCode != "" && projectCode != undefined) {
            var isBoxlogin = CheckBoxAccessToken();
            if (isBoxlogin) {
                GetAndSetBoxData(projectCode);
            }

            var captures_images = GetCaptureImages(projectCode);
            if (captures_images != "" || !captures_images.includes("error")) {
                SetCaptureImages(captures_images["capture_files"]);

            }
            //var box_files = GetFilesFromBox(projectCode);
        }
        else {
            ClearImage();
        }

        //reverse div order
        $('#parent > div').each(function() {
            var first_div_id = $("#parent div:first-child").attr('id');
            //alert(first_div_id);
            if (first_div_id == "map-content") return;
            if ($(this).attr("id") != "report-content")
                $(this).prependTo(this.parentNode);
        });

        $("#map-content").addClass("customize");
        $("#project_regions").css("display", "none");
        $("#report-content").css("display", "block");

        //if($("div").hasClass(".slider-horizontal"))
        if ((reportInfo != null || reportInfo != undefined || reportInfo != "")) {
            tempInfo = reportInfo["currentWeekData"];
            GetXYPosition();
        }

    }

    $("#cmbDisplayOrder").on("change", function(e) {
        var order = $("#cmbDisplayOrder option:selected").text();
        var tableData = GetAllProjectReport(order);
        if (tableData != "" && tableData != undefined) {
            CreateTableBody(tableData);
            TableRowFilter();
        }

    });

    //table sorting by th
    $('#tblProjectList thead').on('click', 'tr th', function() {

        var header = $(this).text();
        if (header.trim() !== "支店名") return;
        //sort by branch name
        var tableData = GetAllProjectReport("branch_order");
        if (tableData != "" && tableData != undefined) {
            CreateTableBody(tableData);
            TableRowFilter();
        }

    });

    //LoadImplementationDocInfo("ccc_project");


});


function ReportHashtagHighlight() {
    //alert("hl");
    $(".reports").each(function() {
        MakeHighlight($(this), $(this).html());
    });
}

function ReportTypeShowOrHide() {
    var reportTypes = $("#report_type").val(); //.toString();

    var selectedCount = $("#report_type option:selected").length;

    $("#report_type option").each(function() {
        var report_div_id = $(this).val();
        if (reportTypes.indexOf(report_div_id) > -1) {
            $("#" + report_div_id + "_li").show();
            if (selectedCount == 1) { //change to full width
                $("#" + report_div_id + "_li").removeClass("col-md-6");
                $("#" + report_div_id + "_li").addClass("col-md-12");
            }
            else { //change to half width
                $("#" + report_div_id + "_li").removeClass("col-md-12");
                $("#" + report_div_id + "_li").addClass("col-md-6");

            }
        }
        else {
            $("#" + report_div_id + "_li").hide();
        }
    });
    ReportHashtagHighlight();
}


function SetRowClick(pj_code) {
    isOpen = false;
    //alert($("#tblProjectList tbody tr").length);
    $("#tblProjectList tbody tr").each(function(index) {

        var hidProjectCode = $(this).find('input[type=hidden]').val();
        if (hidProjectCode == undefined) return;
        if (hidProjectCode === pj_code) {
            $("#tblProjectList tbody tr").removeClass("active");
            $(this).closest('tr').toggleClass("active");
            var ypos = $(this).offset().top;
            ypos = ypos - ($('#tblProjectList').height() / 2 + 200); //plus 200 for header
            //alert($('#search-content .tblScroll').scrollTop() + "\n" + ypos)
            $('#tblProjectList').animate({
                scrollTop: ypos
            }, 500);


            // $('.tblScroll').scrollTop(ypos);
        }
    });
}


function TableRowFilter() {


    $("#tblProjectList tbody tr").each(function() {
        $(this).show();
    });

    var textSearch = $("#txtSearch").val();
    var branchList = $("#branchName").val().toString();
    var inChargeList = $("#inCharge").val().toString();
    var kouji_kubunList = $("#kouji_kubun").val().toString();
    if (branchList.toString() !== "" || inChargeList.toString() !== "" || kouji_kubunList.toString() !== "" || textSearch !== "") {
        $("#tblProjectList tbody tr").each(function(index) {
            var projectName = $(this).find("td:nth-child(2)").text();
            var branch = $(this).find("td:nth-child(3)").text();
            var inCharge = $(this).find("td:nth-child(4)").text();
            var kouji_kubun = $(this).find("td:nth-child(5)").text();

            if (textSearch !== "") {

                if (!projectName.includes(textSearch) || projectName === "") {
                    //alert(textSearch);
                    $(this).hide();
                    return;
                }
            }
            if (branchList !== "") {
                if (!branchList.includes(branch) || branch === "") {
                    $(this).hide();
                    return;
                }
            }
            if (inChargeList !== "") {
                if (!inChargeList.includes(inCharge) || inCharge === "") {
                    $(this).hide();
                    return;
                }
            }
            if (kouji_kubunList !== "") {
                if (!kouji_kubunList.includes(kouji_kubun) || kouji_kubun === "") {
                    $(this).hide();
                    return;
                }
            }
        })

    }
    else {
        $("#tblProjectList tbody tr").each(function() {
            $(this).show();
        });
    }

    $("#tblProjectList >tbody >tr:visible:odd").css("background-color", "#f2f2f2");
    $("#tblProjectList >tbody >tr:visible:even").css("background-color", "#fff");

    // var CurrentVisibleProjects = [];
    // $("#tblProjectList >tbody >tr:visible").each(function(index) {
    //     var projectName = $(this).find("td:nth-child(2)").text();
    //     CurrentVisibleProjects.push(projectName.trim());
    // });

    // ChangeMapIcon(CurrentVisibleProjects, textSearch, branchList, inChargeList, kouji_kubunList);
}

function LoadMap() {
    $("#default_region_design").hide();
    LoadProjectRegions(map_project);

    var textSearch = $("#txtSearch").val();
    var branchList = $("#branchName").val().toString();
    var inChargeList = $("#inCharge").val().toString();
    var kouji_kubunList = $("#kouji_kubun").val().toString();

    var CurrentVisibleProjects = [];
    $("#tblProjectList >tbody >tr:visible").each(function(index) {
        var projectName = $(this).find("td:nth-child(2)").text();
        CurrentVisibleProjects.push(projectName.trim());
    });

    ChangeMapIcon(CurrentVisibleProjects, textSearch, branchList, inChargeList, kouji_kubunList);
}

function ChangeMapIcon(visibleProjects, textSearch, branchList, inChargeList, kouji_kubunList) {
    var myIconRed = L.Icon.extend({
        options: {
            iconUrl: "/iPD/public/image/marker-icon-2x-red.png",
            iconSize: [25, 41],
            shadowUrl: "https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png",
            // shadowAnchor: [8, 20],
            // shadowSize: [25, 18],
            // iconSize: [20, 25],
            // iconAnchor: [8, 30] // horizontal puis vertical
        }
    });
    var myIconBlue = L.Icon.extend({
        options: {
            iconUrl: "/iPD/public/image/marker-icon-2x-blue.png",
            iconSize: [25, 41],
            shadowUrl: "https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png",
            // shadowAnchor: [8, 20],
            // shadowSize: [25, 18],
            // iconSize: [20, 25],
            // iconAnchor: [8, 30] // horizontal puis vertical
        }
    });


    map.eachLayer(function(layer) {
        if (layer instanceof L.Marker) {
            var popup = layer.getPopup();
            var content = popup.getContent();
            var popupPrjName = content.split('<br>')[0];
            if (branchList.toString() !== "" || inChargeList.toString() !== "" || kouji_kubunList.toString() !== "" || textSearch !== "") {
                if (visibleProjects.includes(popupPrjName.trim())) {
                    layer.setIcon(new myIconRed);
                }
                else {
                    layer.setIcon(new myIconBlue);
                }
            }
            else {
                layer.setIcon(new myIconBlue);
            }

        }


    });
    console.log("============================");
}

function ShowAndHideDiv(tr) {
    $("#hashtag_popup").css({ display: "none" });

    $(".highlighter span").remove();

    $("#text1").html('');
    $("#text2").html('');
    $("#text3").html('');
    $("#img1_type").val('');
    $("#img2_type").val('');
    $("#img3_type").val('');

    $("#finished").html('');
    $(".reports").html('');
    $('#image-bar').animate({ height: toggleHeight });
    $("#minimize-bottom").addClass("fa-toggle-down");
    $("#minimize-bottom").removeClass("fa-toggle-up");

    var report_div_height = $("#report-content").height() + 2;
    $("#search-content").css({ height: report_div_height });
    $("#bottom-content").show();

    $("#capture_report_content").removeClass("hideDiv");
    //$("#capture_report_content").addClass("showDiv");

    $("#capture-img div").remove();
    $("#capture-img").removeClass("showDiv");
    $("#capture-img").addClass("hideDiv");

    var chk_ji = (tr.find('input[id="chk_ji"]').prop("checked") == true) ? 1 : 0;
    var chk_ki = (tr.find('input[id="chk_ki"]').prop("checked") == true) ? 1 : 0;
    var chk_hou = (tr.find('input[id="chk_hou"]').prop("checked") == true) ? 1 : 0;
    var chk_tou = (tr.find('input[id="chk_tou"]').prop("checked") == true) ? 1 : 0;
    var chk_special = (tr.find('input[id="chk_special"]').prop("checked") == true) ? 1 : 0;
    if (chk_ji == 1) {
        $("#icon_ji").removeClass("hideIcon");
        $("#icon_ji").addClass("showIcon");
    }
    else {
        $("#icon_ji").removeClass("showIcon");
        $("#icon_ji").addClass("hideIcon");
    }

    if (chk_ki == 1) {
        $("#icon_ki").removeClass("hideIcon");
        $("#icon_ki").addClass("showIcon");
    }
    else {
        $("#icon_ki").removeClass("showIcon");
        $("#icon_ki").addClass("hideIcon");
    }

    if (chk_hou == 1) {
        $("#icon_hou").removeClass("hideIcon");
        $("#icon_hou").addClass("showIcon");
    }
    else {
        $("#icon_hou").removeClass("showIcon");
        $("#icon_hou").addClass("hideIcon");
    }

    if (chk_tou == 1) {
        $("#icon_tou").removeClass("hideIcon");
        $("#icon_tou").addClass("showIcon");
    }
    else {
        $("#icon_tou").removeClass("showIcon");
        $("#icon_tou").addClass("hideIcon");
    }

    if (chk_special == 1) {
        $("#icon_toku").removeClass("hideIcon");
        $("#icon_toku").addClass("showIcon");
    }
    else {
        $("#icon_toku").removeClass("showIcon");
        $("#icon_toku").addClass("hideIcon");
    }
}

function TextAreaAutoHeight() {
    //alert($("#report").innerHeight());

    // var txtReport = $("#report");

    // txtReport.ready(function () {


    //     var scrollHeight = txtReport[0].scrollHeight;
    //     // var textHeight =txtReport.innerHeight();
    //     // txtReport.height(0).height(scrollHeight);
    //     if(txtReport.val().length > 250){
    //         txtReport.height(scrollHeight);
    //     }else{
    //         txtReport.height("217");
    //     }
    // })


    //expandTextarea('report');
    //expandTextarea('description1');
    //expandTextarea('description2');
    // // $("textarea").height( $("textarea")[0].scrollHeight ); 
    // $("#report").height(0).height(scrollHeight);
}

function ImageBarShowAndHide() {

    $("#minimize-bottom").on("click", function(e) {
        if ($(this).hasClass("fa-toggle-down")) {
            $('#image-bar').animate({ height: "30px" });
            $(this).removeClass("fa-toggle-down");
            $(this).addClass("fa-toggle-up");
            $("#bottom-content").hide();

        }
        else {
            $('#image-bar').animate({ height: toggleHeight });
            $(this).removeClass("fa-toggle-up");
            $(this).addClass("fa-toggle-down");
            $("#bottom-content").show();
        }
    });

}

function GetAndSetBoxData(projectCode) {

    GetFilesFromBox(projectCode).then((box_files) => {
        console.log("my result");
        console.log(box_files);
        if (box_files != "") {

            var images = box_files["description_images"];
            Current_Domain_Image = box_files["current_domain_images"];

            if (images != "" || images != undefined) {
                SetDescriptionImages(images);
            }
            else {
                $("#img1").empty();
                $("#img2").empty();
                $("#img3").empty();
            }

        }
    }).catch((error) => {
        console.log("=====promise=======");
        console.log(error);
        //var error_message = error.message;
        if (error == undefined || error.responseText.includes("401 Unauthorized")) {
            $("#box_login_warning").html("BOX TOKEN の有効期限が切れたため、BOX画像表示が出来ません。BOXに再ログインしてください。")
            return;
        }
        console.log(error.code);
        alert("データロードに失敗しました。\n管理者に問い合わせてください。");
    });

}

function SetCaptureImages(captures, status = "") {
    var item = "";
    var highlight_dates = {};
    var i = 0;

    if (captures.length > 0) {
        $('#carousel-tilenav .carousel-control').removeClass("hideDiv");
        $('#carousel-tilenav .carousel-control').addClass("showDiv");
    }
    else {
        $('#carousel-tilenav .carousel-control').addClass("hideDiv");
        $('#carousel-tilenav .carousel-control').removeClass("showDiv");
    }

    var access_token = $("#access_token").val();
    //$(".carousel-showmanymoveone .carousel-inner div").remove();
    var item_arr = {};
    $.each(captures, function(key, image) {
        if (!image.includes("_archive") || status == "all_display") {
            var imageDate = image.split('_')[1];
            imageDate = imageDate.split('.')[0];
            if (item_arr[imageDate] !== undefined) {
                var temp = item_arr[imageDate];
                item[imageDate] = temp.push(image);
            }
            else {
                item_arr[imageDate] = [image];
            }
        }

    });

    var sorted_array = sortObject(item_arr);
    console.log("sorted_array");
    console.log(Object.keys(sorted_array).length);
    $.each(sorted_array, function(imageDate, images) {
        i++;
        var cap_div = "cap_div" + i;
        var img_div_height = 220 / images.length;
        //var fileId = image.split('=')[1];
        //var fileUrl = image.split('=')[2]
        //var imageDate = image.split('_')[1];
        //imageDate = imageDate.split('.')[0];

        var d = imageDate.split('-');
        var dateStr = d[1] + "/" + d[2] + "/" + d[0]; //monthi/day/year
        var date = new Date(dateStr);
        highlight_dates[date] = date;
        // if(i <= 1)
        // item += "<div class='item active'>";
        // else
        item += "<div class='swiper-slide'>"; //item
        item += "<div class=' style='border:1px solid #fff;'>"; //col-md-3'
        item += "<label class='imageLabel'>" + imageDate + "</label>";
        item += "<div id='" + cap_div + "' >"; //'

        $.each(images, function(k, imageName) {
            var fileURL = "/iPD/public/capture/" + imageName;
            //item += "<img src= '"+fileUrl+"?access_token="+access_token+"' class='img-responsive'>";
            item += "<div style='display:flex'>"
            item += "<div style='max-height:" + img_div_height + "px'><img src= '" + fileURL + "' class='img-responsive'></div>";
            item += "<div>";
            item += "<i class='fa fa-minus cus-minus' aria-hidden='true' onClick='ArchiveCaptureImage(\" " + projectCode + "\",\" " + imageName + "\")'></i>";
            item += "<button type='button' class='close' aria-label='Close' onClick='DeleteCaptureFile(\" " + fileURL + "\")'><span aria-hidden='true'>&times;</span></button>";
            item += "</div>";
            item += "</div>";
        });
        item += "</div>";
        item += "</div>";
        item += "</div>";

    });


    $(".swiper-wrapper div").remove();
    $(".swiper-wrapper").append(item).ready(function() {
        var slidePerviewCount = i < 4 ? i : 4; //i= last index
        var swiper = new Swiper('.swiper-container', {
            loop: false,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            slidesPerView: 4,
            spaceBetween: 10,
        });
        swiper.slideTo(i);
    });


    //  $('#carousel-tilenav .carousel-inner div').remove();
    //  $("#carousel-tilenav .carousel-inner").append(item).ready(function(){
    //      ArrangeSlide();
    //      //if(captures.length > 0)checkitem();
    //  });

    HighLightDate(highlight_dates);

}

function ArchiveCaptureImage(projectCode, fileName) {
    var origin_name = fileName.split('.')[0];
    var archive_file_name = origin_name + "_archive.jpeg";
    $.ajax({
        url: "/iPD/report/renameCapture",
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "archive_capture", newFileName: archive_file_name, oldFileName: fileName, projectCode: projectCode },
        success: function(captures_images) {
            console.log(captures_images);
            if (captures_images != "" || !captures_images.includes("error")) {
                SetCaptureImages(captures_images["capture_files"]);

            }
        },
        error: function(err) {
            console.log("=======error=========");
            console.log(err);
        }
    });
}

function ArchiveDisplay() {
    var captures_images = GetCaptureImages(projectCode);
    if (captures_images != "" || !captures_images.includes("error")) {
        SetCaptureImages(captures_images["capture_files"], "all_display");

    }
}

function HideArchive() {
    var captures_images = GetCaptureImages(projectCode);
    if (captures_images != "" || !captures_images.includes("error")) {
        SetCaptureImages(captures_images["capture_files"]);

    }
}

function DeleteCaptureFile(filePath) {
    var res = confirm("Are you sure!");
    if (res === true) {
        $("#capture-img div").remove();
        $.ajax({
            url: "/iPD/report/deleteCapture",
            type: 'post',
            data: { _token: CSRF_TOKEN, message: "delete_capture", filePath: filePath, projectCode: projectCode },
            success: function(captures_images) {
                console.log(captures_images);
                if (captures_images != "" || !captures_images.includes("error")) {
                    SetCaptureImages(captures_images["capture_files"]);

                }
            },
            error: function(err) {
                console.log("=======error=========");
                console.log(err);
            }
        });
    }
}

function HighLightDate(highlight_dates) {

    $("#highlight_date").datepicker("destroy").ready(function() {
        $("#highlight_date").datepicker({
            beforeShowDay: function(date) {
                // check if date is in your array of dates
                var highlight = highlight_dates[date];
                if (highlight && highlight != '') {
                    return [true, "myhighlight", ""];
                }
                else {
                    return [true, 'my_unhighlight', ''];

                }
            },

            onSelect: function() {
                //e.preventDefault();
                var selectedDate = $.datepicker.formatDate('yy-mm-dd', new Date($(this).val()));
                //alert("z");
                SlideToSelectedDate(selectedDate);
            }

        });
    });


}

function SlideToSelectedDate(selectedDate) {

    $('.swiper-container .swiper-slide').css({ "background": "none" });
    $('.swiper-container .swiper-slide').each(function() {
        var dateLable = $(this).find(".imageLabel").html();
        if (dateLable.trim() === selectedDate.trim()) {
            //alert($(this).index());
            var swiper = new Swiper('.swiper-container', {
                loop: false,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                slidesPerView: 4,
                spaceBetween: 10,
            });
            swiper.slideTo($(this).index());
            $('.swiper-container .swiper-slide').css({ "background": "none" });
            $(this).css({ "background": "#787878" });
            //$('#carousel-tilenav .item').removeClass("active");
            //$(this).addClass("active");
            //$(this).find(".imageLabel").css({"background":"green"});
        }
    });
}

function BindCaputerImages(arr_byDate) {
    /*var access_token = $("#access_token").val();
    $.each(arr_byDate,function(div_id, fileId) {
        var tempArr = [];
        tempArr.push(fileId);
        //alert(tempArr);
        $.when(CreateBoxFileContent(tempArr,access_token,"#"+div_id)).then();
    });*/

}

function UpdateImageTypeComboSetting(eleId, selectedType) {
    $.ajax({
        url: "/iPD/report/save",
        async: false,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "update_image_type", "projectCode": projectCode, "elementId": eleId, "selectedType": selectedType },
        success: function(data) {
            if (data != null) {
                console.log("successfully updated image type!");
            }
        },
        error: function(err) {
            console.log(err);
            //alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });


}

function RebindImages(eleId) {

    var selectedType = $("#" + eleId + " option:selected").val();

    UpdateImageTypeComboSetting(eleId, selectedType);
    var image_id_list = [];
    switch (selectedType) {
        case 'per':
            image_id_list = image_list["per"];
            break;
        case 'ext':
            image_id_list = image_list["ext"];
            break;
        case 'int':
            image_id_list = image_list["int"];
            break;
        case 'sit':
            image_id_list = image_list["sit"];
            break;
        case 'vr':
            image_id_list = image_list["vr"];
            break;
        case 'mr':
            image_id_list = image_list["mr"];
            break;
        case 'otr':
            image_id_list = image_list["otr"];
            break;
        default:
            break;

    }
    var img_div_id = "";
    var freedomPart_id = "";
    // alert(eleId);
    if (eleId == "img1_type") {
        img_div_id = "#img1";
        freedomPart_id = "#text1";
    }
    else if (eleId == "img2_type") {
        img_div_id = "#img2";
        freedomPart_id = "#text2";
    }
    else {
        img_div_id = "#img3";
        freedomPart_id = "#text3";
    }
    var access_token = $("#access_token").val();
    $(img_div_id + "> div").remove();
    $(freedomPart_id).html('');
    CreateBoxFileContent(image_id_list, access_token, img_div_id, null, selectedType);
}

function SetDescriptionImages(images) {

    var perspective_images = [];
    var exterior_images = [];
    var interior_images = [];
    var site_images = [];
    var vr_images = [];
    var mr_images = [];
    var meeting_images = [];
    var other_images = [];

    //var box_files = $("#box_files").val();
    var access_token = $("#access_token").val();

    //var box_file_array = images.split(',');
    $.each(images, function(key, file_str) {
        var tempArr = [];
        if (file_str.includes("=")) {
            tempArr = file_str.split("=");
        }
        else {
            tempArr = file_str.split(":");
        }

        var fileName = tempArr[0];
        var fileID = tempArr[1];

        if (fileName.includes(projectCode)) {
            if (fileName.includes("perspective") || fileName.includes("_per"))
                perspective_images.push(fileID);
            else if (fileName.includes("_ext"))
                exterior_images.push(fileID);
            else if (fileName.includes("_int"))
                interior_images.push(fileID);
            else if (fileName.includes("_sit"))
                site_images.push(fileID);
            else if (fileName.includes("_vr") || fileName.includes("VR"))
                vr_images.push(fileID);
            else if (fileName.includes("_mr"))
                mr_images.push(fileID);
            else if (fileName.includes("_mtg"))
                meeting_images.push(fileID)
            else
                other_images.push(fileID)
        }
    });
    image_list = { "per": perspective_images, "ext": exterior_images, "int": interior_images, "sit": site_images, "vr": vr_images, "mr": mr_images, "mtg": meeting_images, "otr": other_images };
    //alert(other_images);
    ClearImage();
    var selectedType1 = $("#img1_type option:selected").val();
    var selectedType2 = $("#img2_type option:selected").val();
    var selectedType3 = $("#img3_type option:selected").val();
    //alert(image_list[selectedType2]);
    if (image_list[selectedType1].length > 0)
        CreateBoxFileContent(image_list[selectedType1], access_token, "#img1", null, selectedType1);
    if (image_list[selectedType2].length > 0)
        CreateBoxFileContent(image_list[selectedType2], access_token, "#img2", null, selectedType2);
    if (image_list[selectedType3].length > 0)
        CreateBoxFileContent(image_list[selectedType3], access_token, "#img3", null, selectedType3);



}

function ClickCaptureImage() {
    $("#image-bar").on("click", "img", function() {

        var imgSrc = $(this).attr('src');
        var appendStr = "";
        appendStr += "<div><img src='" + imgSrc + "' class='img-responsive'></div>";
        $("#capture-img div").remove();
        //var tmp = [];
        //tmp.push(fileId);

        $("#capture-img").append(appendStr);
        $("#capture-img").removeClass("hideDiv");
        $("#capture-img").addClass("showDiv");

        $("#capture_report_content").addClass("hideDiv");
        $("#capture_report_content").removeClass("showDiv");
        //alert('ID is: '+ idimg+ '\n SRC: '+ srcimg);
        // $.when(CreateBoxFileContent(tmp,access_token,"#capture-img")).then();
    });

}

function ArrangeSlide() {

    $('#carousel-tilenav .item').each(function() {

        var itemToClone = $(this);
        //var cap_div = $(this).find('div[class*=cap_div]').attr('class');
        var clonelength = ($('.carousel-showmanymoveone .item').length <= 4) ? $('.carousel-showmanymoveone .item').length : 4;
        for (var i = 1; i < clonelength; i++) {
            itemToClone = itemToClone.next();

            // wrap around if at end of item collection
            if (!itemToClone.length) {
                itemToClone = $(this).siblings(':first');
            }

            // grab item, clone, add marker class, add to collection
            itemToClone.children(':first-child').clone()
                .addClass("cloneditem-" + (i))
                .appendTo($(this));

            //CreateBoxFileContent(arr_byDate[cap_div],access_token,'')
        }
    });
    //$('#carousel-tilenav').carousel(5);
    $('#carousel-tilenav .carousel-inner >div').removeClass('active');
    $('#carousel-tilenav .carousel-inner > div:last-child').addClass('active');
}

function TextSearch() {
    $('#txtSearch').keyup(function() {
        var textboxValue = $('#txtSearch').val();
        $("#tblProjectList tbody tr").each(function() {
            var fileName = $(this).find("td:eq(0)").html();
            if (!fileName.includes(textboxValue)) {
                $(this).hide();
            }
            else {
                $(this).show();
            }
        });

    });
}

function ReplaceImageWithCurrentDomainImage() {
    //var src = $("#mytest").attr('src');
    var basic_path = "/iPD/public/Download/";
    var img1 = $("#img1");
    var img2 = $("#img2");
    var img3 = $("#img3");
    var img1_src = $("#img1 .bp-image").find('img').attr('src');
    var img2_src = $("#img2 .bp-image").find('img').attr('src');
    var img3_src = $("#img3 .bp-image").find('img').attr('src');

    var fileName = "";
    var imageId1 = "";
    var imageId2 = "";
    var imageId3 = "";

    if (img1_src != undefined) {
        console.log(img1_src);
        var imageId1 = img1_src.split('/')[6];
        $.each(Current_Domain_Image, function(k, imageName) {
            if (imageName.includes(imageId1)) {
                fileName = imageName;
                return;
            }
        });

        var width = $("#img1 .bp-image").find('img').width();
        var height = $("#img1 .bp-image").find('img').height();

        if (fileName != "") {
            var imgPath = basic_path + fileName;
            img1.find('div').remove();
            var appenImg = "<img id='copy1' src='" + imgPath + "' class='img-responsive' width='" + width + "'/>";
            img1.append(appenImg);
        }

    }
    if (img2_src != undefined) {
        var imageId2 = img2_src.split('/')[6];
        $.each(Current_Domain_Image, function(k, imageName) {
            if (imageName.includes(imageId2)) {
                fileName = imageName;
                return;
            }
        });
        var width = $("#img2 .bp-image").find('img').width();
        var height = $("#img2 .bp-image").find('img').height();

        if (fileName != "") {
            var imgPath = basic_path + fileName;
            img2.find('div').remove();
            var appenImg = "<img id='copy2' src='" + imgPath + "' class='img-responsive' width='" + width + "'/>";
            img2.append(appenImg);
        }

    }
    if (img3_src != undefined) {
        var imageId3 = img3_src.split('/')[6];
        $.each(Current_Domain_Image, function(k, imageName) {
            if (imageName.includes(imageId3)) {
                fileName = imageName;
                return;
            }
        });
        var width = $("#img3 .bp-image").find('img').width();
        var height = $("#img3 .bp-image").find('img').height();

        if (fileName != "") {
            var imgPath = basic_path + fileName;
            img3.find('div').remove();
            var appenImg = "<img id='copy3' src='" + imgPath + "' class='img-responsive' width='" + width + "'/>";
            img3.append(appenImg);
        }

    }
    return { "imgId1": imageId1, "imgId2": imageId2, "imgId3": imageId3 };
}

function ReplaceText() {
    var des1_div = $("#description1").val(); //$("#text1").find("textarea").html();
    var des2_div = $("#description2").val();
    var report_div = $("#report").val();
    //var highlighter_val = $("#testtextarea").val();

    var des1_rep = $("#description1").val().replace(/\r?\n/g, '<br />');
    var des2_rep = $("#description2").val().replace(/\r?\n/g, '<br />');
    var report_rep = $("#report").val().replace(/\r?\n/g, '<br />');
    //highlighter_val = highlighter_val.replace(/\r?\n/g, '<br />');
    //$("#testtextarea").val(highlighter_val);
    var w_des1 = $("#description1").width();
    var h_des1 = $("#description1").height();
    var w_des2 = $("#description2").width();
    var h_des2 = $("#description2").height();
    var w_rep = $("#report").width();
    var h_rep = $("#report").height();

    $("#description1").replaceWith("<div id='description1' contenteditable='true'>" + des1_rep + "</div>");
    $("#description2").replaceWith("<div id='description2' contenteditable='true'>" + des2_rep + "</div>");
    $("#report").replaceWith("<div id='report' contenteditable='true'>" + report_rep + "</div>");
    //$("#testtextarea").replaceWith("<div id='report' contenteditable='true'>"+highlighter_val+"</div>");

    $("#description1").css({ "width": w_des1, "height": h_des1, "border": "1px solid gray", "padding": "5" });
    $("#description2").css({ "width": w_des2, "height": h_des2, "border": "1px solid gray", "padding": "5" });
    $("#report").css({ "width": w_rep, "height": h_rep, "border": "1px solid gray", "padding": "5" });

    return { "description1": des1_div, "description2": des2_div, "report": report_div };
}

function MakeScreenShot() {
    //alert("ms");
    //var isBoxlogin = CheckBoxAccessToken();
    //if(isBoxlogin == false) return;
    var access_token = $("#access_token").val();
    var imgId_array = ReplaceImageWithCurrentDomainImage();
    //var replaceText = ReplaceText();
    //alert(access_token);
    window.scrollTo(0, 0);

    html2canvas(document.querySelector("#report-content"), { letterRendering: true }).then(canvas => {

        var image = canvas.toDataURL("image/jpeg", "0.9");


        $.ajax({
            url: "/iPD/report/uploadCapture",
            type: 'post',
            crossDomain: true,
            async: true,
            data: { _token: CSRF_TOKEN, message: "upload_capture", image: image, projectCode: projectCode },
            success: function(result) {
                console.log("=====yyy=======");
                console.log(result);
                HideLoading();
                if (result["capture_files"].includes("401failed")) {
                    $("#box_login_warning").html("BOX TOKEN の有効期限が切れたため、BOX画像表示が出来ません。BOXに再ログインしてください。")
                    return;
                }
                if ((result["capture_files"] != "" || result["capture_files"] != undefined)) {
                    SetCaptureImages(result["capture_files"]);
                }
                else {
                    $('#carousel-tilenav .carousel-control').addClass("hideDiv");
                    $('#carousel-tilenav .carousel-control').removeClass("showDiv");
                }
            },
            error: function(err) {
                HideLoading();
                //var error_message = err.message;
                console.log(err.message);


            }
        });
    }).then(function() {
        //alert(replaceText);

        // if(replaceText["description1"] !== undefined){

        //     $("#text1 div").remove();
        //     var original_ele = '<textarea id="description1" disabled="disabled"></textarea>';
        //     $("#text1").append(original_ele);
        //     if(replaceText["description1"] != "")$("#description1").val(replaceText["description1"]);
        // }


        // if(replaceText["description2"] !== undefined){
        //     $("#text2 div").remove();
        //     var original_ele = '<textarea id="description2" disabled="disabled"></textarea>';
        //     $("#text2").append(original_ele);
        //     if(replaceText["description2"] != "")$("#description2").val(replaceText["description2"]);
        // }


        // if(replaceText["report"] !== undefined){
        //      $("#explaination div").remove();
        //     var original_ele = '<textarea id="report" placeholder="ここに物件の報告内容を入力してください。"></textarea>';
        //     $("#explaination").append(original_ele);
        //     if(replaceText["report"] != "")$("#report").val(replaceText["report"]);
        // }

        //TextAreaAutoHeight();

        if (imgId_array["imgId1"] != "") {
            var selectedType1 = $("#img1_type option:selected").val();
            CreateBoxFileContent(image_list[selectedType1], access_token, "#img1", imgId_array["imgId1"]);
        }

        if (imgId_array["imgId2"] != "") {
            var selectedType2 = $("#img2_type option:selected").val();
            CreateBoxFileContent(image_list[selectedType2], access_token, "#img2", imgId_array["imgId2"]);
        }

        if (imgId_array["imgId3"] != "") {
            var selectedType3 = $("#img3_type option:selected").val();
            CreateBoxFileContent(image_list[selectedType3], access_token, "#img3", imgId_array["imgId3"]);
        }





    });
}

function comparer(index) {
    return function(a, b) {
        var valA = getCellValue(a, index),
            valB = getCellValue(b, index)
        return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.toString().localeCompare(valB)
    }
}

function getCellValue(row, index) {
    return $(row).children('td').eq(index).text();

}

function GetXYPosition() {

    if (min_schedule_start_date == "" || max_schedule_end_date == "") {
        $(".cur_date_group").css({ "display": "none" });
        return;
    }
    var duration = (new Date(max_schedule_end_date).getTime() - new Date(min_schedule_start_date).getTime()) / (1000 * 3600 * 24);
    //not yet starting project checkting
    if (new Date(min_schedule_start_date) > new Date()) {
        $(".cur_date_group").css({ "display": "none" });
        return;
    }

    //竣工 checking
    if (new Date(max_schedule_end_date) < new Date()) {
        $(".cur_date_group").css({ "display": "none" });
        $("#finished").html("竣工");
        return;
    }
    var d = new Date();
    var year = d.getFullYear();
    var month = d.getMonth() + 1;
    var day = d.getDate();
    var cur_date_str = year + "/" + month + "/" + day;
    var diffDate = (new Date(cur_date_str).getTime() - new Date(min_schedule_start_date).getTime()) / (1000 * 3600 * 24);

    //alert(diffDate);
    var x = $(".ui-slider").offset().left;
    var y = $(".ui-slider").offset().top;

    var width = $("#slider1").closest(".ui-slider").width();
    var height = $("#slider6").closest(".ui-slider").offset().top; //lastweek slider-horizontal 

    var v_line_x = (diffDate * width) / duration;
    $(".cur_date_group").css({ "display": "block" });
    $(".v-line").css({ "margin-left": v_line_x, "height": height - y + 40 });
    $("#cur_date").css({ "margin-left": v_line_x - 30 });
    $("#cur_date").html(cur_date_str);
    console.log(x + "\n" + y + "\n" + width + "\n" + height);
}

function expandTextarea(id) {

    document.getElementById(id).addEventListener('keyup', function() {
        this.style.overflow = 'hidden';
        this.style.height = 0;
        this.style.height = this.scrollHeight + 'px';
    }, false);

}

function DisplayReportDate() {

    var d = new Date();
    var year = d.getFullYear();
    var month = d.getMonth() + 1;
    var day = d.getDate();
    var currentDate = year + "年" + month + "月" + day + "日";
    $("#todayDate").html(currentDate);

}

function LoadHashtags() {
    var result = "";
    $.ajax({
        url: "/iPD/report/getData",
        async: false,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "get_hashtags" },
        success: function(data) {
            console.log(data);
            if (data != null) {
                result = data;
            }
        },
        error: function(err) {
            console.log(err);
            console.log("load hashtags ===> error");
            //alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });

    return result;
}


function LoadData() {
    var tableData = GetAllProjectReport(""); //get_all_report
    if (tableData != "" && tableData != undefined) {
        var branchList = [];
        var inChargeList = [];
        var construction_typeList = [];
        var project_info = [];
        CreateTableBody(tableData);
        //TableRowFilter();
        $.each(tableData, function(key, row) {
            if (branchList.indexOf(row["branch"]) <= -1) {
                branchList.push(row["branch"]);
            }

            if (inChargeList.indexOf(row["tantousha"]) <= -1) {
                inChargeList.push(row["tantousha"]);
            }

            if (construction_typeList.indexOf(row["a_kouji_type"]) <= -1) {
                construction_typeList.push(row["a_kouji_type"]);
            }

            if (row["address"] != "" && row["address"] != "undefined" && row["address"] != undefined) {
                project_info.push({ "project_name": row["pj_name"], "address": row["address"], "project_code": row["a_pj_code"] });
            }

        });

        // $.each(tableData,function(key, row) {
        //     if(inChargeList.indexOf(row["tantousha"]) <= -1)
        //         inChargeList.push(row["tantousha"]);
        // });
        // $.each(tableData,function(key,value){
        //     if(value["address"] != "" && value["address"] != "undefined" && value["address"] != undefined){
        //         project_info.push({"project_name":value["pj_name"],"address":value["address"],"project_code":value["a_pj_code"]});
        //     }
        // });

        map_project = project_info;
        //LoadProjectRegions(project_info);
        BindComboData(branchList, "branchName", PLACEHOLDER_NAME_BRANCHSTORE);
        BindComboData(inChargeList, "inCharge", PLACEHOLDER_NAME_INCHARGE);
        BindComboData(construction_typeList, "kouji_kubun", PLACEHOLDER_NAME_KOUJIKUBUN);
    }

    var projectName = $("#projectName").html();
    if (projectName !== undefined) {
        ShowProjectReport(projectName);
    }

    //Load Hashtags
    hashtags = LoadHashtags();
}

var map = {};

function LoadProjectRegions(project_info) {
    var isUseGeoChart = false;
    if (isUseGeoChart) { //it is for googel geochart library.Not using now
        google.charts.load('current', {
            'packages': ['geochart'],
            // Note: you will need to get a mapsApiKey for your project.
            // See: https://developers.google.com/chart/interactive/docs/basic_load_libs#load-settings
            'mapsApiKey': 'AIzaSyD-9tSrke72PouQMnMX-a7eZSW0jkFMBWY'
        });
        google.charts.setOnLoadCallback(drawRegionsMap);
    }
    else {
        // console.log("project_info");
        // console.log(JSON.stringify(project_info));return;

        // var prjAddressList = [  {"project_name":"ダイビル本館","address":"大阪府大阪市北区中之島３丁目６"},
        //                         {"project_name":"キングパーツ","address":"広島県福山市御幸町８７９−１"}];

        map = L.map('project_regions').setView([34.140708, 133.942096], 7); // 関西から九州バージョン
        // var map = L.map('project_regions').setView([34.713583, 135.371203], 10);    // 関西バージョン
        L.tileLayer('http://tile.openstreetmap.org/{z}/{x}/{y}.png', // OpenStreetMap:Standard

            {
                maxZoom: 20,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            }).addTo(map);

        console.log(project_info);
        map.options.singleClickTimeout = 250;
        for (var i = 0; i < project_info.length; i++) {

            getLatLng(project_info[i].address, settingLatlng(map, project_info[i].project_name)); // community-geocoder

            if ((project_info[i].address).indexOf("大阪府泉南郡田尻町") !== -1) {
                var popupStr = project_info[i].project_name;
                var marker = L.marker([34.4359642, 135.2411557]).addTo(map).on('dblclick', function(e) { marker_dblclickEvt(e, popupStr); });
                var pdfName = GetPDF(popupStr);
                var jsonName = GetJSON(popupStr);
                marker.bindPopup(popupStr + '<br><a href="/iPD/prjmgt/index" target="_blank" rel="noopener noreferrer" class="speciallink" onclick="viewGanttChart()">【BIM実行計画書】</a>' +
                    '<a target="_blank" rel="noopener noreferrer" class="speciallink" onClick="setGanttSession(\'' + popupStr + '\',\'' + jsonName + '\')">【工程】</a>' +
                    '<a href="javascript:void(0)"  rel="noopener noreferrer" class="speciallink" onclick="ShowPDF(\'' + pdfName + '\')">【案件報告】</a>'); //target="_blank" 
            }
        }
    }
}

function flyToMap(projectName) {

    if (projectName === "") {
        // L.marker({icon: L.spriteIcon('red')}).addTo(map);
        map.flyTo([34.140708, 133.942096], 7, { duration: 0.9 });
    }
    else {
        //projectNameから住所を取得
        $.ajax({
            type: "POST",
            url: "/iPD/prjmgt/getData",
            data: { _token: CSRF_TOKEN, message: "getImplementationDocByProject", name: projectName },
            success: function(data) {
                var results = JSON.parse(data);
                var result = results[0];
                var address = result["address"];

                //住所を緯度経度��変換
                if (address.indexOf("大阪府泉南郡田尻町") !== -1) {
                    map.flyTo([34.4359642, 135.2411557], 15, { duration: 1.5 });
                }
                else {
                    getLatLng(address, flyToLatlng(map));
                }
            },
            error: function(err) {
                console.log(err);
            }
        });
    }
}

function flyToLatlng(map) {
    return function(latlng) {
        // L.marker({icon: L.spriteIcon('red')}).addTo(map);
        map.flyTo([latlng.lat, latlng.lng], 15, { duration: 1.5 });
    }
}

function marker_dblclickEvt(e, name) {
    ShowProjectReport(name);
}

function RecallRowClick() {

    var projectName = $("#projectName").html();
    if (projectName != undefined) ShowProjectReport(projectName);
}

function drawRegionsMap() {
    var chartData = [
        ["大阪", 5],
        ["兵庫", 2],
        ["京都", 1],
        ["広島", 1],
        ["福岡", 2]
    ];
    var data = new google.visualization.DataTable();
    data.addColumn('string', '都道府県');
    data.addColumn('number', '物件数');
    data.addRows(chartData);

    var options = {
        region: 'JP',
        resolution: 'provinces',
    };

    var chart = new google.visualization.GeoChart(document.getElementById('project_regions'));
    chart.draw(data, options);
}

function settingLatlng(map, popupStr) {
    return function(latlng) {
        var marker = L.marker([latlng.lat, latlng.lng]).addTo(map).on('dblclick', function(e) { marker_dblclickEvt(e, popupStr); });
        var pdfName = GetPDF(popupStr);
        var jsonName = GetJSON(popupStr);
        if (jsonName === "") {
            marker.bindPopup(popupStr + '<br><a href="/iPD/prjmgt/index" target="_blank" rel="noopener noreferrer" class="speciallink">【BIM実行計画書】</a>' +
                '<a target="_blank" rel="noopener noreferrer" class="speciallink" style="color:gray;" onClick="setGanttSession(\'' + popupStr + '\',\'' + jsonName + '\')">【工程】</a>' +
                '<a href="javascript:void(0)"  rel="noopener noreferrer" class="speciallink" onclick="ShowPDF(\'' + pdfName + '\')">【案件報告】</a>'); //target="_blank"
        }
        else {
            marker.bindPopup(popupStr + '<br><a href="/iPD/prjmgt/index" target="_blank" rel="noopener noreferrer" class="speciallink">【BIM実行計画書】</a>' +
                '<a target="_blank" rel="noopener noreferrer" class="speciallink" onClick="setGanttSession(\'' + popupStr + '\',\'' + jsonName + '\')">【工程】</a>' +
                '<a href="javascript:void(0)"   rel="noopener noreferrer" class="speciallink" onclick="ShowPDF(\'' + pdfName + '\')">【案件報告】</a>'); //target="_blank"
        }
    }
}

function GetPDF(projectName) {

    if (projectName.includes("クレメントイン今治")) {
        return "【四国】20200902_クレメント.pdf";
    }
    else if (projectName.includes("博多駅")) {
        return "【九州】20200629_博多駅.pdf";
    }
    else if (projectName.includes("資生堂")) {
        return "【九州】20200629_資生堂.pdf";
    }
    else if (projectName.includes("京都駅")) {
        return "【大阪】20200630_京都駅.pdf";
    }
    else if (projectName.includes("宮原NK")) {
        return "【大阪】20200630_宮原NK.pdf";
    }
    else if (projectName.includes("平野町")) {
        return "【大阪】20200630_平野町.pdf";
    }
    else if (projectName.includes("関西国際空港")) {
        return "【大阪】20200630_関空.pdf";
    }
    else if (projectName.includes("ミツトヨ")) {
        return "NO DATAの画像.pdf";
    }
    else if (projectName.includes("うめきた")) {
        return "【大阪】20201029_うめきた.pdf";
    }
    else if (projectName.includes("クボタ")) {
        return "【大阪】20201029_クボタ.pdf";
    }
    else if (projectName.includes("新淀屋橋")) {
        return "【大阪】20201029_日生淀屋橋.pdf";
    }
    else if (projectName.includes("淀屋橋駅西地区")) {
        return "【大阪】20201029_淀屋橋日地区.pdf";
    }
    else if (projectName.includes("大阪駅西北")) {
        return "【大阪】20201029_西北.pdf";
    }

    return "NO DATAの画像.pdf"
}

function ShowProjectReport(projectName) {
    //get Project code by project name
    $("#tblProjectList tbody tr").each(function(index) {
        var pn = $(this).find("td:nth-child(2)").text();
        if (pn === projectName) {
            $(this).find("td:nth-child(2)").click();
            //var scrollTo = $(this).offsetTop;
            //$("#tblProjectList tbody").scrollTop = scrollTo;
        }

    });
}

function ShowPDF(pdfName) {
    //window.location.href = '/iPD/pdf/'+pdfName;
    window.open('/iPD/pdf/' + pdfName, 'pdfWindow');
}

function GetJSON(projectName) {
    var result = "";

    $.ajax({
        url: "/iPD/gantt/getData",
        async: false,
        type: 'post',
        data: { _token: CSRF_TOKEN, isTemp: 0, pj_name: projectName, fileName: "" },
        success: function(data) {
            if (data != null && data.length === 1) {
                result = "index";
            }
        },
        error: function(err) {
            console.log(err);
            alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });

    return result;
}

// function GetProjectInfo(projectName){
//     var result = "";

//     $.ajax({
//         url: "../prjmgt/getData",
//         async:false,
//         type: 'post',
//         data:{_token: CSRF_TOKEN,message:"getImplementationDocByProject",name:projectName},
//         success :function(data) {
//             //alert(JSON.stringify(data));
//             if(data != null){
//                 result=JSON.parse(data);
//             }
//         },
//         error:function(err){
//             console.log(err);
//             alert("データロードに失敗しました。\n管理者に問い合わせてください。");
//         }
//     });

//     return result;
// }

function GetProjectReportByName(projectName) {
    var result = "";

    $.ajax({
        url: "/iPD/report/getData",
        async: false,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "get_report_byname_temp", name: projectName, projectCode: projectCode },
        success: function(data) {
            //alert(JSON.stringify(data));
            console.log("================");
            console.log(data);
            if (data != null) {
                result = data;
            }
        },
        error: function(err) {
            console.log(err);
            alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });

    return result;
}

function GetProjectSpecialFeature(projectCode) {
    var result = "";

    $.ajax({
        url: "/iPD/report/getData",
        async: false,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "get_project_sepcial_feature_byPrjCode", projectCode: projectCode },
        success: function(data) {
            //alert(JSON.stringify(data));
            if (data != null) {
                result = data;
            }
        },
        error: function(err) {
            console.log(err);
            alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });

    return result;
}

function GetFilesFromBox(projectCode) {

    return new Promise((resolve, reject) => {
        $.ajax({
            url: "/iPD/report/getImages",
            async: true,
            type: 'post',
            data: { _token: CSRF_TOKEN, message: "get_images", projectCode: projectCode },
            dataType: "JSON",
            success: function(data) {
                HideLoading();
                resolve(data);
            },
            beforeSend: function() { ShowLoading(); },
            error: function(error) {
                console.log(error);
                HideLoading();
                reject(error);
            }
        });
    });
}

function GetCaptureImages(projectCode) {

    var result = "";
    $.ajax({
        url: "/iPD/report/getImages",
        async: false,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "get_capture_images", projectCode: projectCode },
        dataType: "JSON",
        success: function(data) {
            //HideLoading();
            result = data;
        },
        error: function(error) {
            //HideLoading();
            return "error when loading capture images";
        }
    });
    return result;
}

function GetAllProjectReport(status_order) {
    var result = "";
    $.ajax({
        url: "/iPD/report/getData",
        async: false,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "get_all_report", status_order: status_order },
        success: function(data) {
            console.log(data);
            if (data != null) {
                result = data;
            }
        },
        error: function(err) {
            console.log(err);
            alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });

    return result;
}

/**
 * セレクトボックス内の項目をバインドする。
 * @param  {object}  [in]data        元データ
 * @param  {string}  [in]comboId     セレクトボックスの識別ID
 * @param  {string}  [in]placeholder セレクトボックス内のplaceholder
 * @return なし
 */
function BindComboData(data, comboId, placeholder) {
    DEBUGLOG("BindComboData", "start", 0);

    var appendText = "";
    $.each(data, function(key, value) {
        appendText += "<option value='" + value + "'>" + value + "</option>";

    });
    $("select#" + comboId + " option").remove();
    $("#" + comboId).append(appendText).select2({ placeholder: placeholder }).trigger('changed');
}


function CreateTableBody(project_info) {
    //console.log(project_info);
    var appendStr = "";
    var pj_name = "";
    $.each(project_info, function(key, row) {
        var lated_report_date = row["save_date"] == null ? "" : row["save_date"];

        appendStr += "<tr>";
        appendStr += "<input type='hidden' name='projectCode' id='projectCode' value=" + row['a_pj_code'] + ">";
        appendStr += "<td class='col-wd-lg'>" + row["pj_name"] + "</td>"; //50%
        appendStr += "<td class='col-wd-md'>" + row["branch"] + "</td>"; //15%
        appendStr += "<td class='col-wd-md'>" + row["tantousha"] + "</td>"; //15%
        appendStr += "<td class='col-wd-md'>" + row["a_kouji_type"] + "</td>"; //15%
        //appendStr += "<td width=''>"+lated_report_date+"</td>";//20%
        if (row["execution_plan"] == 1)
            appendStr += "<td class='col-wd-sm'><input class='form-check-input' type='checkbox' id='chk_ji' checked='checked'></td>";
        else
            appendStr += "<td class='col-wd-sm'><input class='form-check-input' type='checkbox' id='chk_ji'></td>";
        if (row["held_meeting"] == 1)
            appendStr += "<td class='col-wd-sm'><input class='form-check-input' type='checkbox' id='chk_ki' checked='checked'></td>";
        else
            appendStr += "<td class='col-wd-sm'><input class='form-check-input' type='checkbox' id='chk_ki'></td>";
        if (row["report_avaliable"] == 1)
            appendStr += "<td class='col-wd-sm'><input class='form-check-input' type='checkbox' id='chk_hou' checked='checked'></td>";
        else
            appendStr += "<td class='col-wd-sm'><input class='form-check-input' type='checkbox' id='chk_hou'></td>";
        if (row["bim360_report"] == 1)
            appendStr += "<td class='col-wd-sm'><input class='form-check-input' type='checkbox' id='chk_tou' checked='checked'></td>";
        else
            appendStr += "<td class='col-wd-sm'><input class='form-check-input' type='checkbox' id='chk_tou'></td>";
        if (row["special_feature"] == 1)
            appendStr += "<td class='col-wd-sm'><input class='form-check-input' type='checkbox' id='chk_special' checked='checked'></td>";
        else
            appendStr += "<td class='col-wd-sm'><input class='form-check-input' type='checkbox' id='chk_special'></td>";

        var assigned_person = (row["assigned_person"] == null) ? "" : row["assigned_person"];
        appendStr += "<td class='col-wd-md1'><input class='form-control-sm col-xs-12' type='input' width='20'id='txtHai' value='" + assigned_person + "'></td>";
        appendStr += "<td class='col-wd-sm'><i id='update_row' onclick='UpdateReportProjectStates(this)'class='fa fa-refresh' aria-hidden='true'></i></td>";
        appendStr += "</tr>";
    });

    $("#tblProjectList tbody > tr").remove();
    $("#tblProjectList tbody").append(appendStr);

}

function findDiffString(str1, str2) {
    let add_str = [];
    let del_str = [];
    let diff = [];
    let del = [];
    let index = 0;
    str2.split('').forEach(function(val, i) {
        if (val != str1.charAt(i)) {
            var lastIndex = add_str.length - 1;

            if (lastIndex < 0 || add_str[lastIndex] == i - 1) {
                add_str.push(i);
                if (i + 1 == str2.length) {
                    diff[index] = add_str;
                }
            }
            else {
                diff[index] = add_str;
                index++;
                add_str = [];
                add_str.push(i);
            }
        } //diff += val;
    });

    str1.split('').forEach(function(val, i) {
        if (val != str2.charAt(i)) del_str.push(i); //del += val;
    });
    //return diff + "\n" + del;
    return { "add_str": diff, "del_str": del };
    // var str = "";
    // var str_user = "";
    // var j = 1;
    // $.each(reports, function(k, row) {
    //     var diff_str = [];
    //     if (j <= reports.length - 1) {
    //         diff_str = findDiffString(reports[j]["report"], row["report"]);
    //         j++;
    //     }
    //     var report_str = row["report"];
    //     var colored_str = "";
    //     if (diff_str != "") {
    //         var start = 0;
    //         var end = row["report"].length;
    //         var cur_start = 0;
    //         var cur_end = 0;
    //         colored_str = "";
    //         for (var i = 0; i < diff_str["add_str"].length; i++) {
    //             var data = diff_str["add_str"][i];

    //             if (data.length == 1) {
    //                 cur_start = data[0];
    //                 cur_end = data[0];
    //             }
    //             else {
    //                 cur_start = data[0];
    //                 cur_end = data[data.length - 1];
    //             }
    //             colored_str += report_str.substr(start, cur_start - 1) + "<span style='background-color:yellow;'>" + report_str.substr(cur_start, cur_end) + "</span>" + report_str.substr(cur_end + 1, end);
    //         }
    //     }
    //     if (colored_str == "") {
    //         colored_str = report_str;
    //     }
    //     //alert(report_str);
    //     str += "<tr>";
    //     str += "<td>" + colored_str + "</td>";
    //     str += "<td>" + row["saved_user_name"] + "　" + row["saved_date"] + "</td>";
    //     str += "</tr>";
    //     // if (row["saved_user_id"] != $("#hidLoginID").val()) {
    //     //     str += "<div id='" + row["saved_user_id"] + "' class='report-disabled'>" + row["report"] + "</div>";
    //     // }
    //     // else {
    //     //     str += "<div id='" + row["saved_user_id"] + "'>" + row["report"] + "</div>";
    //     // }

    //     // str_user += "<div>" + row["saved_user_name"] + "</div>";

    // });
    // $("#ipd_report").html(reports[0]["report"]);
    // $("#tbl_ipd_report tr").remove();
    // $("#tbl_ipd_report").append(str);


    // var nodes = $("#ipd_report")[0].childNodes;
    // $.each(nodes, function(k, node) {
    //     if (node.nodeType == Node.TEXT_NODE) {

    //         //node.sty;
    //         //node.caretTo(curPostion);
    //     }
    // })
}

function LoadConfirmHistoryPopup(el) {
    $("#confirm_history_popup").toggle("100").css({
        top: event.pageY + "px",
        left: event.pageX - 600 + "px"
    });
    var tr = $(el).parents('tr');
    var tblName = tr.closest('table').attr('id');
    var order_list = [];
    $("#" + tblName + " tr").each(function() {
        var tr_id = $(this).attr('id');
        order_list.push(tr_id);
    });
    tblName = tblName.replace("tbl", "tb") + "_history";
    //if (order_list.length > 0) order_list.reverse();

    GetCurrentWeekReportHistory(tblName, order_list);
}

function CloseConfirmPopup() {
    $("#confirm_history_popup").hide();
}

function AddReportCategory() {
    var newReportCate = $("#txtReportCategory").val();
    if (newReportCate == "") {
        alert("追加したい報告区分名を入力してください。");
        return;
    }
    else {
        var data = $("#report_type").text();
        var isDuplicate = false;
        $("#report_type option").each(function() {
            if ($(this).text() === newReportCate) {
                // alert(newReportCate);
                isDuplicate = true;
                return;
            }
        });
        if (isDuplicate) {
            alert("報告区分名が既にあります。\n別の報告区分名を使ってください。");
            return;
        }
    }

    var res = confirm(newReportCate + "を追加していいですか？");
    if (res == true) {
        $.ajax({
            url: "/iPD/report/save",
            type: 'post',
            data: { _token: CSRF_TOKEN, message: "add_report_category", new_report_category: newReportCate },
            success: function(data) {
                //alert(JSON.stringify(data));
                if (data.includes("success")) {
                    location.reload();
                }
            },
            error: function(err) {
                alert("報告区分追加が失敗しました。\n管理者に問い合わせてください。");
                console.log("add new report cateory ===> error");
                console.log(err);

            }
        });
    }

}

function LoadAddReportCategoryPopup(el) {
    $("#add_report_category_popup").toggle("100").css({
        top: event.pageY + "px",
        left: event.pageX + "px"
    });
}

function CloseReportCategoryPopup() {
    $("#add_report_category_popup").hide();
}

function UpdateReportisShowFlag(param_id, flag, tblName) {
    $.ajax({
        url: "/iPD/report/save",
        async: false,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "update_report_isshow_flag", flag: flag, param_id: param_id, tblName: tblName },
        success: function(data) {
            console.log(data);
        },
        error: function(err) {
            console.log("update_report_isshow_flag ===> error");
            console.log(err);

        }
    });
}

function GetCurrentWeekReportHistory(tblName, order_list) {
    $.ajax({
        url: "/iPD/report/getData",
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "get_current_week_report_history", projectCode: projectCode, tblName: tblName, order_list: order_list },
        success: function(data) {
            if (data != "") {
                CreateCurrentWeekReportHistory(data);
            }
        },
        error: function(err) {
            console.log("get_current_week_report_history ===> error");
            console.log(err);

        }
    });
}

function CreateCurrentWeekReportHistory(data) {
    var tab = "";
    var cur_name = "";
    var next_name = "";
    var tab_content = "";
    var tab_index = 0;
    var isNew = true;
    $.each(data, function(index, row) {
        cur_name = data[index]["name"];
        //alert(data[index]["name"] + "\n" + data[index + 1]["name"]);
        next_name = (index + 1 < data.length) ? data[index + 1]["name"] : data[index]["name"];
        if (isNew == true) {
            if (tab_index == 0) { //make first tab active
                tab += "<li class='nav-item active'   role='presentation'>";
                tab += "<a href='#" + row["name"] + "' aria-controls='" + row["name"] + "' role='tab' data-toggle='tab'>" + row["name"] + "</a>";
                tab_content += "<div id=" + row["name"] + " role='tabpanel' class='tab-pane active'><table class='tbl_confirm_history'>";
            }
            else {
                tab += "<li class='nav-item'   role='presentation'>";
                tab += "<a href='#" + row["name"] + "' aria-controls='" + row["name"] + "' role='tab' data-toggle='tab'>" + row["name"] + "</a>";
                tab_content += "<div id=" + row["name"] + " role='tabpanel' class='tab-pane'><table class='tbl_confirm_history'>";
            }
            isNew = false;
            //tab_index++;

        }
        tab_content += "<tr>";
        tab_content += "<td width='350px'>" + row["report"] + "</td>";
        tab_content += "<td width='150px'>" + row["name"] + "</td>";
        tab_content += "<td width='200px'>" + row["saved_date"] + "</td>";
        tab_content += "</tr>";
        if (cur_name != next_name) {
            tab_content += "</table></div>";
            tab += "</li>";
            tab_index++; //for start new li
            isNew = true;

        }
    });
    // $("#tbl_confirm_history tr").remove(); //tab_report_history
    // $("#tbl_confirm_history").append(str);
    $("#report_history_tabs li").remove();
    $("#report_history_tabs").append(tab);
    $("#report_history_tab_content > div").remove();
    $("#report_history_tab_content").append(tab_content);
}

function HideReportRow(db_id, tr_id) {

    var tr = $("#" + tr_id).closest('tr');
    if (tr.find('.reports').hasClass('hide-report-row')) {

        tr.find('.reports').removeClass('hide-report-row');
        tr.find('.highlighter').removeClass('hide-report-row');
        tr.find('.reports').addClass('show-report-row');
        tr.find('.highlighter').addClass('show-report-row');
        tr.find('.saved_date_span').show();
        if (tr.find('.confirm-history'))
            tr.find('.confirm-history').show();
    }
    else {
        tr.find('.reports').removeClass('show-report-row');
        tr.find('.highlighter').removeClass('show-report-row');
        tr.find('.reports').addClass('hide-report-row');
        tr.find('.highlighter').addClass('hide-report-row');
        tr.find('.saved_date_span').hide();
        if (tr.find('.confirm-history'))
            tr.find('.confirm-history').hide();
    }

    //update isshow flag
    if (db_id == "" || db_id != undefined) {
        var tblName = tr.closest('table').attr('id');
        var flag = tr.find('.reports').hasClass('hide-report-row') ? 0 : 1;
        tblName = tblName.replace("tbl", "tb") + "_history";
        UpdateReportisShowFlag(db_id, flag, tblName);
    }
}

function SetReportData(tbl_id, reports) {
    var str = "";
    console.log("setReportDAta ======>" + tbl_id);
    console.log(reports);
    var canUpdate = false;
    $.each(reports, function(k, row) {
        var row_id = tbl_id.replace("tbl_", "");
        var id = row_id + row["saved_user_id"];
        if (row["report"] == "") return;
        if (row["saved_user_id"] != $("#hidLoginID").val()) {

            str += "<tr id='" + row["saved_user_id"] + "'>";
            str += "<td>";
            if (row["isShow"] == 1)
                str += "<div class='reports report-disabled' id='" + id + "' contenteditable='false'>" + row["report"] + "</div>";
            else
                str += "<div class='reports report-disabled hide-report-row' id='" + id + "' contenteditable='false'>" + row["report"] + "</div>";

            str += "</td>";
            str += "<td width='20%' style='position: relative;'>";
            str += "<div class='hide-row-icon' onClick='HideReportRow(" + row['id'] + ",\"" + id + "\");'>";
            str += "<span class='glyphicon glyphicon-triangle-bottom'></span>";
            str += "</div>";
            str += "<span>" + row["saved_user_name"] + "<span>";
            if (row["isShow"] == 1)
                str += "<span class='saved_date_span'>" + "【" + row["saved_date"] + "】</span>";
            else
                str += "<span class='saved_date_span' style = 'display:none'>" + "【" + row["saved_date"] + "】</span>";
            str += "</td>";
            str += "</tr>";


        }
        else { //enable div
            canUpdate = true;
            str += "<tr id='" + row["saved_user_id"] + "'>";
            str += "<input type='hidden' name='hidPrvReport' id='hidPrvReport' value='" + row["report"] + "'>";
            if (reports.length == 1) {
                if (row["isShow"] == 1)
                    str += "<td><div class='reports default-size2' id='" + id + "' contenteditable='false'>" + row["report"] + "</div></td>";
                else
                    str += "<td><div class='reports default-size2 hide-report-row' id='" + id + "' contenteditable='false'>" + row["report"] + "</div></td>";
            }
            else {
                if (row["isShow"] == 1)
                    str += "<td><div class='reports' id='" + id + "' contenteditable='false'>" + row["report"] + "</div></td>";
                else
                    str += "<td><div class='reports hide-report-row' id='" + id + "' contenteditable='false'>" + row["report"] + "</div></td>";
            }

            str += "</td>";
            str += "<td width='20%' style='position: relative;'>";
            if (row["isShow"] == 1)
                str += "<a href='javascript:void(0)' class='confirm-history' onClick='LoadConfirmHistoryPopup(this)'>変更履歴確認</a>";
            else
                str += "<a href='javascript:void(0)' class='confirm-history' onClick='LoadConfirmHistoryPopup(this)' style = 'display:none'>変更履歴確認</a>";
            str += "<div class='hide-row-icon' onClick='HideReportRow(" + row['id'] + ",\"" + id + "\");'>";
            str += "<span class='glyphicon glyphicon-triangle-bottom'></span>";
            str += "</div>";
            str += "<span class='ebl-usr-color'>" + row["saved_user_name"] + "<span>";
            if (row["isShow"] == 1)
                str += "<span class='saved_date_span'>" + "【" + row["saved_date"] + "】</span>";
            else
                str += "<span class='saved_date_span' style = 'display:none'>" + "【" + row["saved_date"] + "】</span>";
            str += "</td>";
            str += "</tr>";
        }

    });
    if (str == "") {
        var id = "ipd_report" + $("#hidLoginID").val();
        var loginUser = $("#loginUser").html();
        str += "<tr id='" + $("#hidLoginID").val() + "'>";
        str += "<td><div class='reports default-size2' id='" + id + "' contenteditable='false' placeholder='ここに入力してください。'></div></td>"; //empty div
        // str += "<td width='20%'><span class='ebl-usr-color'>" + loginUser + "</td>"; //empty div
        str += "</tr>";

    }
    else if (canUpdate == false) {
        var id = "ipd_report" + $("#hidLoginID").val();
        var loginUser = $("#loginUser").html();
        str += "<tr id='" + $("#hidLoginID").val() + "'>";
        str += "<td><div class='reports default-size' id='" + id + "' contenteditable='false' placeholder='追加内容はここに入力してください。'></div></td>"; //empty div
        // str += "<td><div class='active-div-style'>ここに入力...</div><div class='reports default-size' id='" + id + "' contenteditable='true' placeholder='ここに入力...' style='display:none;'></div></td>"; //empty div
        // str += "<td width='20%'><span class='ebl-usr-color'>" + loginUser + "</td>"; //empty div
        // str += "<td style='background:whitesmoke;padding 3px;color:darkgray' onclick='AddText(this)'>ADD TEXT。。。</td>";
        str += "<td style='color:#ddd;'><div >" + loginUser + "</div></td>";
        // <span class='glyphicon glyphicon-triangle-bottom' onClick='SwitchDiv(this)'></span>
        str += "</tr>";
    }
    $("#" + tbl_id + " tr").remove();
    $("#" + tbl_id).html(str);



}

function SwitchDiv(ele) {
    var tr = $(ele).closest('tr');
    if (tr.find('.reports').hasClass('default-size')) {
        tr.find('.reports').removeClass('default-size');
        tr.find('.reports').addClass('default-size2');
    }
    else {
        tr.find('.reports').removeClass('default-size2');
        tr.find('.reports').addClass('default-size');
    }

}

function SetProjectReportInfo(info, reports) {
    //set current week values
    DisplayDateRangeSlider("current", info[0]);
    //DisplayReportDate();
    var data = info[0];
    var hattyuusya = data["hattyuusya"];
    var sekkeisha = data["sekkeisya"];
    var shiten = data["shiten"];
    var tijou = data["tijo"];
    var tika = data["tika"];
    var menseki = data["nobe_menseki"];
    tijou = "地上 " + tijou + " 階";
    tika = "地下 " + tika + " 階";
    var structure = data["kouzou"] + tika + tijou
    var builder = data["sekou_syozoku"]; //+"("+data[0]["b_sekou_syozoku"]+")";

    $("#projectName").html(data["pj_name"]);
    $("#orderer").html(hattyuusya);
    $("#designer").html(sekkeisha);
    $("#branch_store").html(shiten);
    $("#structure").html(structure);
    $("#builder").html(builder); //data[0]["construction_type"]
    $("#totalArea").html(menseki);
    //var report_type_list = [];
    //set report all types data
    $.each(reports, function(key, items) {
        var tbl_name = "tbl_" + key;
        SetReportData(tbl_name, items);
    });

    //alert(report_type_list);
    //$("#report_type").val(report_type_list).change();
    var img1_type = (data["img1_type"] == "") ? "per" : data["img1_type"];
    var img2_type = (data["img2_type"] == "") ? "ext" : data["img2_type"];
    var img3_type = (data["img3_type"] == "") ? "otr" : data["img3_type"];

    $("#img1_type").val(img1_type);
    $("#img2_type").val(img2_type);
    $("#img3_type").val(img3_type);

    $("#icon_ji").val(data["execution_plan_file_link"]);
    $("#icon_ki").val(data["held_meeting_file_link"]);
    $("#icon_hou").val(data["report_avaliable_file_link"]);
    $("#icon_tou").val(data["bim360_report_file_link"]);

    //$(".reports").hashtags();

    //hide higlighter when report row is hide setting
    $(".tbl_report tr").each(function() {
        if ($(this).find('.reports').hasClass('hide-report-row')) {
            $(this).find('.highlighter').removeClass('show-report-row');
            $(this).find('.highlighter').addClass('hide-report-row');
        }
        if ($(this).find('.reports').hasClass('report-disabled')) {
            //$(this).find('.highlighter').removeClass('show-report-row');
            $(this).find('.highlighter').addClass('report-disabled');
        }
        else {
            $(this).find('.highlighter').removeClass('report-disabled');
        }
    });

    DisplayReportDate();

    ReportHashtagHighlight();
    //TextAreaAutoHeight();

}

function GetContentEditableText(str) {
    //string replace <div></div> with <br>;
    //alert(str);
    if (str.includes("<div>"))
        str = str.replace(/<div>/gi, '<br>').replace(/<\/div>/gi, '');
    if (str.includes("&nbsp;"))
        str = str.replace(/&nbsp;/gi, ' ');
    return str;
}

function GetSaveReportList(tbl_id) {
    var result = [];

    $("#" + tbl_id + " tr").each(function(index) {
        var saved_user_id = $(this).attr('id');
        if ($(this).find('td:eq(0)').find('.reports').hasClass('report-disabled') == false) { //is enable
            var hidReport = ($(this).find('input[type=hidden]').val());

            var r = $(this).find('td:eq(0)').find('.reports').html();
            var res = GetContentEditableText(r);
            //alert(hidReport + "\n" + res);
            var hashtag_list = [];
            $(this).find('td:eq(0)').find('.highlighter span').each(function() {
                hashtag_list.push($(this).html());
            });
            if (res.trim() != "") {
                if (hidReport == undefined || hidReport.trim() != res.trim()) {
                    result.push({ "saved_user_id": saved_user_id, "report": res, "hashtags": hashtag_list })
                }

            }
        }
    });
    return result;
    // $(".highlighter span").each(function() {
    //     hashtag_list.push($(this).html());
    // });
}


function SaveData() {
    var projectName = $("#projectName").html();
    var report = ""; //GetContentEditableText($("#ipd_report").html());
    var selected_report_type = $("#report_type").val();
    var save_report_list = {};
    $.each(selected_report_type, function(k, rt) {
        var tblId = "tbl_" + rt;
        var report_list = GetSaveReportList(tblId);
        if (report_list.length > 0) {
            save_report_list[rt] = report_list;
        }
    });

    var save_info = {
        projectCode: projectCode,
        projectName: projectName,
        save_report_list: save_report_list
    };

    if ($.isEmptyObject(save_report_list)) return;

    if (projectCode != "" && projectCode != undefined) {
        $.ajax({
            url: "/iPD/report/save",
            type: 'post',
            data: { _token: CSRF_TOKEN, message: "save_info_temp", save_info: JSON.stringify(save_info) },
            success: function(data) {
                console.log(data);
                // return;
                if (data["message"] == "success") {
                    alert("Successfully Saved!");
                    //rebind updated table data
                    $.each(data["reload_data"], function(key, items) {
                        var tbl_name = "tbl_" + key;
                        SetReportData(tbl_name, items);
                    });
                    ReportHashtagHighlight();
                }

            },
            error: function(err) {
                console.log(err);
                alert("データ登録が失敗しました。\n管理者に問い合わせてください。");
            }
        });
    }
    else {
        alert("プロジェクトコードが記載されてないため登録できません。");
    }
}

function UpdateReportProjectStates(lastTd) {
    var tr = $(lastTd).closest('tr');
    var projectName = tr.find('td:first').html();

    var pj_code = tr.find('input[type="hidden"]').val();
    var chk_ji = (tr.find('input[id="chk_ji"]').prop("checked") == true) ? 1 : 0;
    var chk_ki = (tr.find('input[id="chk_ki"]').prop("checked") == true) ? 1 : 0;
    var chk_hou = (tr.find('input[id="chk_hou"]').prop("checked") == true) ? 1 : 0;
    var chk_tou = (tr.find('input[id="chk_tou"]').prop("checked") == true) ? 1 : 0;
    var chk_special = (tr.find('input[id="chk_special"]').prop("checked") == true) ? 1 : 0;
    var txtHai = tr.find('input[id="txtHai"]').val();
    if (pj_code != undefined) {
        $.ajax({
            url: "/iPD/report/save",
            type: 'post',
            data: {
                _token: CSRF_TOKEN,
                message: "update_project_status",
                projectCode: pj_code,
                projectName: projectName,
                chk_ji: chk_ji,
                chk_ki: chk_ki,
                chk_hou: chk_hou,
                chk_tou: chk_tou,
                chk_special: chk_special,
                txtHai: txtHai
            },
            success: function(data) {
                if (data.includes("success")) {
                    alert("更新しました。");
                }

            },
            error: function(err) {
                console.log(err);
                alert("更新に失敗しました。\n管理者に問い合わせてください。");
            }
        });
    }
}

function SaveSpecialFeature() {
    var projectName = $("#projectName").html();
    var special_feature = $("#txtASpecialFeature").val();
    //alert(projectCode+"\n"+description1+"\n"+description2);
    if (projectCode != "" && projectCode != undefined) {
        $.ajax({
            url: "/iPD/report/save",
            type: 'post',
            data: { _token: CSRF_TOKEN, message: "save_special_feature_info", projectCode: projectCode, projectName: projectName, special_feature: special_feature },
            success: function(data) {
                if (data.includes("success")) {
                    //hideModal();
                    alert("Successfully Saved!");

                }

            },
            error: function(err) {
                console.log(err);
                alert("データ登録が失敗しました。\n管理者に問い合わせてください。");
            }
        });
    }
    else {
        alert("プロジェクトコードが記載されてないため登録できません。");
    }
}

function hideModal() {
    $("#myModal").removeClass("in");
    $(".modal-backdrop").hide();
    $("#myModal").modal('hide');
}

function ConvertToDateFormat(paramStr) {

    if (paramStr.length == 8) {
        var isDigit = CheckDigitOnly(paramStr);
        if (isDigit) {

            var year = paramStr.substr(0, 4); //start index,substring number
            var month = paramStr.substr(4, 2);
            var day = paramStr.substr(6, 2);

            var date_format_str = year + "/" + month + "/" + day;
            return new Date(date_format_str);
        }
        return paramStr;
    }
    return paramStr;
}

function CheckDigitOnly(str) {
    return /^\d+$/.test(str);
}

function DisplayDateRangeSlider(param, data) {

    var d = new Date();
    var day = d.getDay();
    var design_start = ConvertToDateFormat(data["b_koutei_sekkei_model_start"]);
    var design_end = ConvertToDateFormat(data["b_koutei_sekkei_model_end"]);
    var confirm_start = ConvertToDateFormat(data["b_koutei_kakunin_sinsei_start"]);
    var confirm_end = ConvertToDateFormat(data["b_koutei_kakunin_sinsei_end"]);
    var model_integ_start = ConvertToDateFormat(data["b_koutei_sekisan_model_tougou_start"]);
    var model_integ_end = ConvertToDateFormat(data["b_koutei_sekisan_model_tougou_end"]);
    var worker_decision_start = ConvertToDateFormat(data["b_koutei_kouji_juujisya_kettei_start"]);
    var worker_decision_end = ConvertToDateFormat(data["b_koutei_kouji_juujisya_kettei_end"]);
    var engineer_decision_start = ConvertToDateFormat(data["b_koutei_genba_koutei_kettei_start"]);
    var engineer_decision_end = ConvertToDateFormat(data["b_koutei_genba_koutei_kettei_end"]);
    var release_start = ConvertToDateFormat(data["b_koutei_kouji_start"]);
    var release_end = ConvertToDateFormat(data["b_koutei_kouji_end"]);

    min_schedule_start_date = "";
    max_schedule_end_date = "";
    //alert(min_schedule_start_date+"\n"+max_schedule_end_date);

    if (design_start == "")
        design_start = design_end;
    if (design_end == "")
        design_end = design_start;
    if (confirm_start == "")
        confirm_start = confirm_end;
    if (confirm_end == "")
        confirm_end = confirm_start;
    if (model_integ_start == "")
        model_integ_start = model_integ_end;
    if (model_integ_end == "")
        model_integ_end = model_integ_start;
    if (worker_decision_start == "")
        worker_decision_start = worker_decision_end;
    if (worker_decision_end == "")
        worker_decision_end = worker_decision_start;
    if (engineer_decision_start == "")
        engineer_decision_start = engineer_decision_end;
    if (engineer_decision_end == "")
        engineer_decision_end = engineer_decision_start;
    if (release_start == "")
        release_start = release_end;
    if (release_end == "")
        release_end = release_start;

    //find minimum schedule start date    
    if (design_start !== "") {
        if (min_schedule_start_date == "")
            min_schedule_start_date = design_start;
        else if (design_start < min_schedule_start_date)
            min_schedule_start_date = design_start;
    }
    if (confirm_start !== "") {
        if (min_schedule_start_date == "")
            min_schedule_start_date = confirm_start;
        else if (confirm_start < min_schedule_start_date)
            min_schedule_start_date = confirm_start;
    }
    if (model_integ_start !== "") {
        if (min_schedule_start_date == "")
            min_schedule_start_date = model_integ_start;
        else if (model_integ_start < min_schedule_start_date)
            min_schedule_start_date = model_integ_start;
    }
    if (worker_decision_start !== "") {
        if (min_schedule_start_date == "")
            min_schedule_start_date = worker_decision_start
        else if (worker_decision_start < min_schedule_start_date)
            min_schedule_start_date = worker_decision_start
    }
    if (engineer_decision_start !== "") {
        if (min_schedule_start_date == "")
            min_schedule_start_date = engineer_decision_start;
        else if (engineer_decision_start < min_schedule_start_date)
            min_schedule_start_date = engineer_decision_start;
    }
    if (release_start !== "") {
        if (min_schedule_start_date == "")
            min_schedule_start_date = release_start;
        else if (release_start < min_schedule_start_date)
            min_schedule_start_date = release_start;
    }


    //find maximum schedule end date    
    if (design_end !== "") {
        if (max_schedule_end_date == "")
            max_schedule_end_date = design_end;
        else if (design_end > max_schedule_end_date)
            max_schedule_end_date = design_end;
    }
    if (confirm_end !== "") {
        if (max_schedule_end_date == "")
            max_schedule_end_date = confirm_end;
        else if (confirm_end > max_schedule_end_date)
            max_schedule_end_date = confirm_end;
    }
    if (model_integ_end !== "") {
        if (max_schedule_end_date == "")
            max_schedule_end_date = model_integ_end;
        else if (model_integ_end > max_schedule_end_date)
            max_schedule_end_date = model_integ_end;
    }
    if (worker_decision_end !== "") {
        if (max_schedule_end_date == "")
            max_schedule_end_date = worker_decision_end;
        else if (worker_decision_end > max_schedule_end_date)
            max_schedule_end_date = worker_decision_end;
    }
    if (engineer_decision_end !== "") {
        if (max_schedule_end_date == "")
            max_schedule_end_date = engineer_decision_end;
        else if (engineer_decision_end > max_schedule_end_date)
            max_schedule_end_date = engineer_decision_end;
    }
    if (release_end !== "") {
        if (max_schedule_end_date == "")
            max_schedule_end_date = release_end;
        else if (release_end > max_schedule_end_date)
            max_schedule_end_date = release_end;
    }

    //alert(min_schedule_start_date+"\n"+max_schedule_end_date);

    if (min_schedule_start_date == "" || max_schedule_end_date == "") {

        $(".ui-slider").hide();
        $("#schedule1,#schedule2,#schedule3,#schedule4,#schedule5,#schedule6").empty();
        return;
    }
    else {
        $(".ui-slider").show();

    }



    //   var design_start_date= new Date(design_start);
    //   var design_end_date = new Date(design_end);
    //   var confirm_start_date= new Date(confirm_start);
    //   var confirm_end_date= new Date(confirm_end);
    //   var model_integ_start_date= new Date(model_integ_start);
    //   var model_integ_end_date= new Date(model_integ_end);
    //   var worker_decision_start_date= new Date(worker_decision_start);
    //   var worker_decision_end_date= new Date(worker_decision_end);
    //   var engineer_decision_start_date= new Date(engineer_decision_start);
    //   var engineer_decision_end_date= new Date(engineer_decision_end);
    //   var release_start_date= new Date(release_start);
    //   var release_end_date= new Date(release_end);




    var duration = (max_schedule_end_date.getTime() - min_schedule_start_date.getTime()) / (1000 * 3600 * 24);


    var cur_day = (day * 100) / duration;
    //alert(day+"\n"+duration+"\n"+cur_day);
    //if(param == "current"){
    var start = 0;
    var end = 0;
    var start_label = "";
    var end_label = "";


    for (var i = 1; i <= 6; i++) {
        var start_end_same = false;
        if (i == 1 && design_start != "") {

            if (design_start.getTime() != design_end.getTime()) {
                start = GetScheduleDuration(design_start, min_schedule_start_date);
                end = GetScheduleDuration(design_end, design_start);
            }
            else {
                start = GetScheduleDuration(design_end, design_start);
                start_end_same = true;
            }

            //alert(start+"\n"+end);
            start_label = GetLabelDate(design_start);
            end_label = GetLabelDate(design_end);
        }
        else if (i == 2 && confirm_start != "") {
            if (confirm_start.getTime() != confirm_end.getTime()) {
                start = GetScheduleDuration(confirm_start, min_schedule_start_date);
                end = GetScheduleDuration(confirm_end, min_schedule_start_date);
            }
            else {
                start = GetScheduleDuration(confirm_start, min_schedule_start_date);
                start_end_same = true;
            }

            start_label = GetLabelDate(confirm_start);
            end_label = GetLabelDate(confirm_end);
        }
        else if (i == 3 && model_integ_end != "") {
            if (model_integ_start.getTime() != model_integ_end.getTime()) {
                //start = GetSchedulePercentage(model_integ_start_date,design_start_date,duration);
                //end = GetSchedulePercentage(model_integ_end_date,design_start_date,duration);

                start = GetScheduleDuration(model_integ_start, min_schedule_start_date);
                end = GetScheduleDuration(model_integ_end, min_schedule_start_date);
            }
            else {
                start = GetScheduleDuration(model_integ_start, min_schedule_start_date);
                start_end_same = true;
            }

            start_label = GetLabelDate(model_integ_start);
            end_label = GetLabelDate(model_integ_end);
        }
        else if (i == 4 && worker_decision_start != "") {
            if (worker_decision_start.getTime() !== worker_decision_end.getTime()) {
                //start = GetSchedulePercentage(worker_decision_start_date,design_start_date,duration);
                //end = GetSchedulePercentage(worker_decision_end_date,design_start_date,duration);

                start = GetScheduleDuration(worker_decision_start, min_schedule_start_date);
                end = GetScheduleDuration(worker_decision_end, min_schedule_start_date);
            }
            else {
                start = GetScheduleDuration(worker_decision_start, min_schedule_start_date);
                start_end_same = true;
            }

            start_label = GetLabelDate(worker_decision_start);
            end_label = GetLabelDate(worker_decision_end);
        }
        else if (i == 5 && engineer_decision_start != "") {
            if (engineer_decision_start.getTime() !== engineer_decision_end.getTime()) {
                //start = GetSchedulePercentage(engineer_decision_start_date,design_start_date,duration);
                //end = GetSchedulePercentage(engineer_decision_end_date,design_start_date,duration);

                start = GetScheduleDuration(engineer_decision_start, min_schedule_start_date);
                end = GetScheduleDuration(engineer_decision_end, min_schedule_start_date);
            }
            else {
                start = GetScheduleDuration(engineer_decision_start, min_schedule_start_date);
                start_end_same = true;
            }

            start_label = GetLabelDate(engineer_decision_start);
            end_label = GetLabelDate(engineer_decision_end);
        }
        else if (i == 6 && release_start != "") {
            if (release_start.getTime() !== release_end.getTime()) {
                //start = GetSchedulePercentage(release_start_date,design_start_date,duration);
                //end = GetSchedulePercentage(release_end_date,design_start_date,duration);

                start = GetScheduleDuration(release_start, min_schedule_start_date);
                end = GetScheduleDuration(release_end, min_schedule_start_date);
                //alert(start+"\n"+end+"\n"+duration);
            }
            else {
                start = GetScheduleDuration(release_start, min_schedule_start_date);
                start_end_same = true;
            }

            start_label = GetLabelDate(release_start);
            end_label = GetLabelDate(release_end);
            //alert(start+"\n"+end);
        }

        var tick_label = "";
        if (start_end_same == false) {
            if (start_label !== "" && end_label !== "")
                tick_label = "【" + [start_label + "～" + end_label] + "】";
        }
        else {
            tick_label = "【" + start_label + "】";
            //tick_label = [start_label,end_label];
        }

        if (start.toString().includes("NaN") || end.toString().includes("NaN")) {
            start = 0;
            end = 0;
        }

        $("#slider" + i).slider({

            values: [start, end],
            min: 0,
            max: duration,
            range: true,
        });
        if (start_end_same == true) {
            $("#slider" + i).find('.ui-slider-range').css({ "width": "2px", "margin-top": "0px" });
        }
        $("#schedule" + i).html(tick_label);
        $("#slider" + i).unbind('mousedown');

    }
}

function GetSchedulePercentage(paramDate, startDate, duration) {
    var current_duration = (paramDate.getTime() - startDate.getTime()) / (1000 * 3600 * 24);
    var result = (current_duration * 100) / duration;
    return result;
}

function GetScheduleDuration(paramDate, startDate) {
    var current_duration = (paramDate.getTime() - startDate.getTime()) / (1000 * 3600 * 24);
    /*if(current_duration.toString().includes("NaN"))
        return 0;
    else*/
    return current_duration;
}

function GetLabelDate(paramDate) {
    var result = paramDate.getFullYear() + "/" + (paramDate.getMonth() + 1);
    return result;
}
var isOpen = true;
var isDisplayMap = false;

function toggleFilter() {

    /*if(isOpen){
        $("#project_regions").css("display","none");
        isOpen = false;
    }else{
        $("#project_regions").css("display","block");
        isOpen = true;
    }*/


    if (isOpen) {
        //("isopen");
        if ($("#hidProjectCode") != "") {
            $('#parent > div').each(function() {
                if ($(this).attr("id") != "report-content")
                    $(this).prependTo(this.parentNode);
            });
        }

        $("#map-content").addClass("customize");
        /*$("#report-content").addClass("customize");*/
        $("#project_regions").css("display", "none");
        $("#report-content").css("display", "block");
        //$("#project_regions").css("display","none");
        isOpen = false;
    }
    else {
        if ($("#hidProjectCode") != "") {
            $('#parent > div').each(function() {
                if ($(this).attr("id") != "report-content")
                    $(this).prependTo(this.parentNode);
            });
        }
        /*$("#map-content").addClass("customize");*/
        $("#map-content").removeClass("customize");
        $("#project_regions").css("display", "block");
        $("#report-content").css("display", "none");


        isOpen = true;
    }
}

function UploadImages() {
    window.open("/iPD/report/uploadImagesIndex", "_blank");
}

function DisplayImagesAtView(images) {
    var pers_img = images["image1"];
    var normal_img = images["image2"];

    var appendStr = "";
    var appendDiv = "";
    if (pers_img != "" && pers_img != undefined) {
        var count = 0;
        $.each(pers_img, function(key, imageName) {

            if (count == 0) {
                if (imageName.includes(".pdf")) {
                    appendDiv += "<div class='mySlides' style='display:block;'><embed src=/iPD/public/ReportImages/" + imageName + " width='100%' height='270px'/></div>";
                }
                else {
                    appendDiv += "<div class='mySlides' style='display:block;'><img src=/iPD/public/ReportImages/" + imageName + "></div>";
                }

                //appendStr += "<li data-target='#img1' data-slide-to='"+count+"' class='active'></li>"; 
                //appendDiv +="<div class='item active'><img src=/iPD/public/ReportImages/"+imageName+"></div>";
            }
            else {
                if (imageName.includes(".pdf")) {
                    appendDiv += "<div class='mySlides'><embed src=/iPD/public/ReportImages/" + imageName + " width='100%' height='270px'/></div>";
                }
                else {
                    appendDiv += "<div class='mySlides'><img src=/iPD/public/ReportImages/" + imageName + "></div>";
                }

                //appendStr += "<li data-target='#img1' data-slide-to='"+count+"'></li>";
                //appendDiv +="<div class='item'><img src=/iPD/public/ReportImages/"+imageName+"></div>";
            }


            count++;
        });

        $("#currentWeek #img1 div").remove();
        $("#currentWeek #img1").prepend(appendDiv);

        //   $("#prevWeek1 #img3 div").remove();
        //   $("#prevWeek1 #img3").prepend(appendDiv);
    }

    var appendStr = "";
    var appendDiv = "";
    if (normal_img != "" && normal_img != undefined) {
        var count = 0;
        $.each(normal_img, function(key, imageName) {
            if (count == 0) {
                //appendStr += "<li data-target='#img2' data-slide-to='"+count+"' class='active' ></li>"; 
                //appendDiv +="<div class='item active'><img src=/iPD/public/ReportImages/"+imageName+"></div>";
                appendDiv += "<div class='mySlides' style='display:block;'><img src=/iPD/public/ReportImages/" + imageName + "></div>";
            }
            else {
                //appendStr += "<li data-target='#img2' data-slide-to='"+count+"'></li>";
                //appendDiv +="<div class='item'><img src=/iPD/public/ReportImages/"+imageName+"></div>";
                appendDiv += "<div class='mySlides' style='display:block;'><img src=/iPD/public/ReportImages/" + imageName + "></div>";
            }


            count++;
        });

        $("#currentWeek #img2 div").remove();
        $("#currentWeek #img2").prepend(appendDiv);

        //   $("#prevWeek1 #img4 div").remove();
        //   $("#prevWeek1 #img4").prepend(appendDiv);
    }

}

function ClearImage() {
    $("#img1 > div").remove();
    $("#img2 > div").remove();
    $("#img3 > div").remove();
    $("#text1").html('');
    $("#text2").html('');
    $("#text3").html('');

    //$("#img4 div").remove();

}


function GetProjectReportByWeekly(projectName, date) {
    var result = "";

    $.ajax({
        url: "/iPD/report/getData",
        async: false,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "get_report_byweekly", name: projectName, date: date, projectCode: projectCode },
        success: function(data) {
            //alert(JSON.stringify(data));
            if (data != null) {
                result = data;
            }
        },
        error: function(err) {
            console.log(err);
            alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });

    return result;
}


function GetPreviousWeekData(projectName) {
    var result = "";

    $.ajax({
        url: "/iPD/report/getData",
        async: false,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "get_report_prevReport", name: projectName, projectCode: projectCode },
        success: function(data) {
            //alert(JSON.stringify(data));
            if (data != null) {
                result = data;
            }
        },
        error: function(err) {
            console.log(err);
            alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });

    return result;
}

var slideIndex = 1;

function plusSlides(n, divID) {
    showSlides(slideIndex += n, divID);
}

function currentSlide(n, divID) {
    showSlides(slideIndex = n, divID);
}

function showSlides(n, divID) {
    var i;
    var slides = document.getElementById(divID).getElementsByClassName("mySlides");
    //var dots = document.getElementsByClassName("dot");
    if (n > slides.length) { slideIndex = 1 }
    if (n < 1) { slideIndex = slides.length }
    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }

    var index = slideIndex - 1;
    if (index < 0) index = 0;
    //alert(index);
    slides[index].style.display = "block";

}

function CheckBoxAccessToken() {

    var access_token = $("#access_token").val();
    var warning_str = "BOXにログインされていないため、BOX画像表示が出来ません。";
    if (access_token == "" || access_token == undefined) {
        $("#box_login_warning").html(warning_str);
        return false;
    }
    else {
        $("#box_login_warning").html("");
        return true;
    }
}

function sortObject(obj) {
    return Object.keys(obj).sort().reduce(function(result, key) { //reverse()
        result[key] = obj[key];
        return result;
    }, {});
}


function OpenFile(btn) {

    var file_link = $(btn).val();
    if (file_link == "") return;
    window.open(file_link, "_blank");

}

function SaveFileLink() {
    if (current_icon_name != "" && projectCode != "") {

        var file_link = $("#fileURL").val();
        //alert(file_link);
        $.ajax({
            url: "/iPD/report/save",
            type: 'post',
            data: { _token: CSRF_TOKEN, message: "save_file_link", projectCode: projectCode, projectName: current_project_name.trim(), icon_name: current_icon_name, file_link: file_link },
            success: function(data) {
                if (data.includes("success")) {
                    if (current_icon_name == "実") {
                        $("#icon_ji").val(file_link);
                    }
                    else if (current_icon_name == "キ") {
                        $("#icon_ki").val(file_link);
                    }
                    else if (current_icon_name == "報") {
                        $("#icon_hou").val(file_link);
                    }
                    else if (current_icon_name == "登") {
                        $("#icon_tou").val(file_link);
                    }
                    $(".right-click-content").hide(100);

                }

            },
            error: function(err) {
                console.log(err);
                alert("データ登録が失敗しました。\n管理者に問い合わせてください。");
            }
        });
    }
}


function MultiCapture() {

    // ShowLoading();
    var promises = [];
    $("#tblProjectList tbody tr:visible").each(function(index) {
        var tr = $(this).find("td:nth-child(2)");
        //promises.push(AsyncRowClick(tr));

        var promise1 = new Promise(function(resolve, reject) {
            tr.click();
            alert("click");
            resolve("1");
            //setTimeout(resolve, 1000, 1);
        });
        var promise2 = new Promise(function(resolve, reject) {


            MakeScreenShot();
            //resolve(promise2);
            //alert("ms");
            setTimeout(resolve, 1000, 2);
        });

        // var promise3 =  new Promise(function(resolve, reject) {
        //   setTimeout(resolve, 1000, 3);
        // });

        Promise.all([promise1, promise2]).then(function(values) {
            console.log(values);
        });
    });

    // Promise.all(promises)
    // .then((results)=>{
    //     console.log(results);
    // })
    // .catch((err)=>console.log(err));





}

// async function AsyncRowClick(tr){
//     let capture = "";
//     let clicked = await RowClick(tr);
//     clicked.then(async result => {
//          capture = await AsyncMakeScreenShot();
//     })

//     return capture;
// }

function RowClick(tr) {
    alert("click");
    tr.click();
    return "clicked"
    // return new Promise((resolve, reject) => {

    //     resolve("clicked");
    // });


}

// async function AsyncMakeScreenShot(){
//     alert("ms");
//     //var isBoxlogin = CheckBoxAccessToken();
//     //if(isBoxlogin == false) return;
//     var access_token = $("#access_token").val();
//     var imgId_array = ReplaceImageWithCurrentDomainImage();
//     var replaceText = ReplaceText();
//     //alert(access_token);
//     window.scrollTo(0, 0);

//     await html2canvas(document.querySelector("#report-body"),{letterRendering:true}).then(canvas => {

//       var image = canvas.toDataURL("image/jpeg","0.9");
//       alert(image);
//       return image;


//       $.ajax({
// 		url:".. /report/uploadCapture",
//         type: 'post',
//         crossDomain : true,
//         async:true,
//         data:{_token: CSRF_TOKEN,message:"upload_capture",image:image,projectCode:projectCode},
//         success:function(result){
//             console.log("=====yyy=======");
//             console.log(result);
//             HideLoading();
//             if(result["capture_files"].includes("401failed")){
//                 $("#box_login_warning").html("BOX TOKEN の有効期限が切れたため、BOX画像表示が出来ません。BOXに再ログインしてください。")
//                 return;
//             }
//             if((result["capture_files"] != "" || result["capture_files"] != undefined )) {
//                 SetCaptureImages(result["capture_files"]);
//             }else{
//                 $('#carousel-tilenav .carousel-control').addClass("hideDiv");
//                 $('#carousel-tilenav .carousel-control').removeClass("showDiv");
//             }
//         },
//         error:function(err){
//             HideLoading();
//             //var error_message = err.message;
//             console.log(err.message);


//         }
//       });
//     }).then(function(){
//       //alert(replaceText);

//         if(replaceText["description1"] !== undefined){

//             $("#text1 div").remove();
//             var original_ele = '<textarea id="description1" disabled="disabled"></textarea>';
//             $("#text1").append(original_ele);
//             if(replaceText["description1"] != "")$("#description1").val(replaceText["description1"]);
//         }


//         if(replaceText["description2"] !== undefined){
//             $("#text2 div").remove();
//             var original_ele = '<textarea id="description2" disabled="disabled"></textarea>';
//             $("#text2").append(original_ele);
//             if(replaceText["description2"] != "")$("#description2").val(replaceText["description2"]);
//         }


//         if(replaceText["report"] !== undefined){
//              $("#explaination div").remove();
//             var original_ele = '<textarea id="report" placeholder="ここに物件の報告内容を入力してください。"></textarea>';
//             $("#explaination").append(original_ele);
//             if(replaceText["report"] != "")$("#report").val(replaceText["report"]);
//         }

//         TextAreaAutoHeight();

//         if(imgId_array["imgId1"] != "")
//             CreateBoxFileContent(perspective_images,access_token,"#img1",imgId_array["imgId1"]);

//         if(imgId_array["imgId2"] != "")
//             CreateBoxFileContent(non_perspective_images,access_token,"#img2",imgId_array["imgId2"]);



//     });
// }
