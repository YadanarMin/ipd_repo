/* Debugログ出力設定 */
var DEBUG_LOG_LEVEL = 0;

/* ajax通信トークン定義 */
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

/* Placeholder名称定義 */
var PLACEHOLDER_NAME_FOLDER         = "Select Folder";
var PLACEHOLDER_NAME_PROJECT        = "Select Project";
var PLACEHOLDER_NAME_VERSION        = "Select Version";
var PLACEHOLDER_NAME_LEVEL          = "Select Level";
var PLACEHOLDER_NAME_WORKSET        = "Select Workset";
var PLACEHOLDER_NAME_ROOMNAME       = "Select '部屋名'";
var PLACEHOLDER_NAME_SHIAGE_TENJO   = "Select '天井仕上'";
var PLACEHOLDER_NAME_SHIAGE_KABE    = "Select '壁仕上'";
var PLACEHOLDER_NAME_SHIAGE_YUKA    = "Select '床仕上'";
var PLACEHOLDER_NAME_SHITAJI_TENJO  = "Select '天井下地'";
var PLACEHOLDER_NAME_SHITAJI_KABE   = "Select '壁下地'";
var PLACEHOLDER_NAME_SHITAJI_YUKA   = "Select '床下地'";
var PLACEHOLDER_NAME_HABAKI         = "Select '幅木'";
var PLACEHOLDER_NAME_MAWARIBUCHI    = "Select '廻縁'";

$(document).ready(function(){
    $.ajaxSetup({
        cache:false
    });
    
    var login_user_id = $("#hidLoginID").val();
    var img_src = "../public/image/JPG/原子力のフリーイラスト3.jpeg";
    var url = "dataPortal/roomInfoSearchConsole";
    var content_name = "部屋ﾃﾞｰﾀ分析";
    recordAccessHistory(login_user_id,img_src,url,content_name);
    
    $("#project").select2({
        placeholder:"Folder Loading..."
    });
    $("#item").select2({
        placeholder:"Project Loading..."
    });
    $("#version").select2({
        placeholder:"Version Loading..."
    });
    $("#level").select2({
        placeholder:"Select Level"
    });
    
    $("#workset").select2({
        placeholder:"Select Workset"
    });

    $("#roomName").select2({
        placeholder:"Select '部屋名'"
    });
    
    $("#tenjoShiage").select2({
        placeholder:"Select '天井仕上'"
    });

    $("#kabeShiage").select2({
        placeholder:"Select '壁仕上'"
    });

    $("#yukaShiage").select2({
        placeholder:"Select '床仕上'"
    });

    $("#tenjoShitaji").select2({
        placeholder:"Select '天井下地'"
    });

    $("#kabeShitaji").select2({
        placeholder:"Select '壁下地'"
    });

    $("#yukaShitaji").select2({
        placeholder:"Select '床下地'"
    });

    $("#habaki").select2({
        placeholder:"Select '幅木'"
    });

    $("#mawaribuchi").select2({
        placeholder:"Select '廻縁'"
    });

    LoadComboData();

    $("#project").change(function() {
        ProjectChange();
    });

    $("#item").change(function() {
        ItemChange();
    });

    if (location.hash !== '') $('a[href="' + location.hash + '"]').tab('show');
        return $('a[data-toggle="tab"]').on('shown', function(e) {
        return location.hash = $(e.target).attr('href').substr(1);
    });

});

function LoadComboData(){
    DEBUGLOG("LoadComboData", "start", DEBUG_LOG_LEVEL);
    
    $.ajax({
        url: "../forge/getData",
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"getComboData"},
        success :function(data) {
            if(data != null){
               BindComboData(data["projects"],"project",PLACEHOLDER_NAME_FOLDER);
               BindComboData(data["items"],   "item",   PLACEHOLDER_NAME_PROJECT);
               BindComboData(data["versions"],"version",PLACEHOLDER_NAME_VERSION);
            }                                 
        },
        error:function(err){
            console.log(err);
        }
    });  
}

function BindComboData(data,comboId,placeholder){
    DEBUGLOG("BindComboData", "start", DEBUG_LOG_LEVEL);
    
    var appendText = "<option value=''></option>";
    $.each(data,function(key,value){
        if ( (value["name"] != undefined) && (value["name"] != null) ) {
            value["name"] = value["name"].trim();
        }

        if(comboId == "version"){
            var fileName = value["name"]+"("+value["version_number"]+")";
            appendText +="<option value='"+JSON.stringify(value)+"'>"+fileName+"</option>";
        }else{
            appendText +="<option value='"+JSON.stringify(value)+"'>"+value["name"]+"</option>";
        }
        
    });
    $("select#"+comboId+" option").remove();
    $("#"+comboId).append(appendText).select2({placeholder:placeholder}).trigger('changed');
}

function BindComboRoomData(data,comboId,placeholder) {
    DEBUGLOG("BindComboRoomData", "start", DEBUG_LOG_LEVEL);
    
    var appendText = "";
    Object.keys(data).forEach(function(key){
        if ( (data[key] == undefined) || (data[key] == null) || (data[key] == "") ) {
            data[key] = "NoName";
        }
        appendText +="<option value='"+JSON.stringify(data[key])+"'>"+data[key]+"</option>";        
    });

    $("select#"+comboId+" option").remove();
    $("#"+comboId).append(appendText).select2({placeholder:placeholder}).trigger('changed');
}

function ProjectChange(){
    DEBUGLOG("ProjectChange", "start", DEBUG_LOG_LEVEL);
    
    var folderSelectedCount = $('#project option:selected').length;
    var itemOption = "";
    var versionOption = "";
    
    var projectName = $('#project option:selected').text();
    $.ajax({
        url: "../forge/getData",
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"getComboRoomDataByProject",projectName:projectName,itemName:""},
        success :function(data) {
            // console.log(data);
            if(data != null){
                BindComboData(data["items"],"item",PLACEHOLDER_NAME_PROJECT);
                BindComboData(data["versions"], "version",PLACEHOLDER_NAME_VERSION);
                
                BindComboRoomData(data["levels"],"level",PLACEHOLDER_NAME_LEVEL);
                BindComboRoomData(data["worksets"],"workset",PLACEHOLDER_NAME_WORKSET);
                BindComboRoomData(data["roomName"],"roomName",PLACEHOLDER_NAME_ROOMNAME);
                BindComboRoomData(data["tenjoShiage"],"tenjoShiage",PLACEHOLDER_NAME_SHIAGE_TENJO);
                BindComboRoomData(data["kabeShiage"],"kabeShiage",PLACEHOLDER_NAME_SHIAGE_KABE);
                BindComboRoomData(data["yukaShiage"],"yukaShiage",PLACEHOLDER_NAME_SHIAGE_YUKA);
                BindComboRoomData(data["tenjoShitaji"],"tenjoShitaji",PLACEHOLDER_NAME_SHITAJI_TENJO);
                BindComboRoomData(data["kabeShitaji"],"kabeShitaji",PLACEHOLDER_NAME_SHITAJI_KABE);
                BindComboRoomData(data["yukaShitaji"],"yukaShitaji",PLACEHOLDER_NAME_SHITAJI_YUKA);
                BindComboRoomData(data["habaki"],"habaki",PLACEHOLDER_NAME_HABAKI);
                BindComboRoomData(data["mawaribuchi"],"mawaribuchi",PLACEHOLDER_NAME_MAWARIBUCHI);
            }
        },
        error:function(err){
            console.log(err);
        }
    });
}

function ItemChange(){
    DEBUGLOG("ItemChange", "start", DEBUG_LOG_LEVEL);
    
    //NOP
}

function ReportForgeRoomData(){
    DEBUGLOG("ReportForgeRoomData", "start", DEBUG_LOG_LEVEL);
    ShowLoading();

    var level_list = [];
    var workset_list=[];
    var room_list = [];
    var tenjoShiage_list = [];
    var kabeShiage_list = [];
    var yukaShiage_list = [];
    var tenjoShitaji_list = [];
    var kabeShitaji_list = [];
    var yukaShitaji_list = [];
    var habaki_list = [];
    var mawaribuchi_list = [];
    var overviewData = {"Elements":0,"Area":0,"RoomName":0,"CalcHeight":0,"Shucho":0,"RoomHeight":0};
    var chartData = {};
    var totalData = {};
    var versionSelectedCount = $('#version option:selected').length;
    var ajaxCnt = 0;

    $("#level option:selected").each(function(){
        level_list.push($(this).text());
    });
    $("#workset option:selected").each(function(){
        workset_list.push($(this).text());
    });
    $("#roomName option:selected").each(function(){
        room_list.push($(this).text());
    });
    $("#tenjoShiage option:selected").each(function(){
        tenjoShiage_list.push($(this).text());
    });
    $("#kabeShiage option:selected").each(function(){
        kabeShiage_list.push($(this).text());
    });
    $("#yukaShiage option:selected").each(function(){
        yukaShiage_list.push($(this).text());
    });
    $("#tenjoShitaji option:selected").each(function(){
        tenjoShitaji_list.push($(this).text());
    });
    $("#kabeShitaji option:selected").each(function(){
        kabeShitaji_list.push($(this).text());
    });
    $("#yukaShitaji option:selected").each(function(){
        yukaShitaji_list.push($(this).text());
    });
    $("#habaki option:selected").each(function(){
        habaki_list.push($(this).text());
    });
    $("#mawaribuchi option:selected").each(function(){
        mawaribuchi_list.push($(this).text());
    });
    $('#version option:selected').each(function(){
        var valArr =JSON.parse($(this).val());
        var version_number = valArr.version_number;
        var item_id = valArr.item_id;
        
        return $.ajax({
            url: "../forge/getData",
            type: 'post',
            data:{_token: CSRF_TOKEN,message:"getRoomDataByVersion",version_number:version_number,
                    item_id:item_id,workset_list:workset_list,level_list:level_list,room_list:room_list,
                    tenjoShiage_list:tenjoShiage_list,kabeShiage_list:kabeShiage_list,yukaShiage_list:yukaShiage_list,
                    tenjoShitaji_list:tenjoShitaji_list,kabeShitaji_list:kabeShitaji_list,yukaShitaji_list:yukaShitaji_list,
                    habaki_list:habaki_list,mawaribuchi_list:mawaribuchi_list},
            success :function(data) {
                console.log(data);
                totalData[version_number] = data;
                
                ajaxCnt++;
                if(ajaxCnt == versionSelectedCount){
                    console.log("totalData length:"+Object.keys(totalData).length);
                    DisplayforgeData(totalData, Object.keys(totalData).length);
                    HideLoading();
                }
            },
            error:function(err){
                console.log(err);
                HideLoading();
            }
        });
    });
    
}

function DownloadForgeRoomData(){
    DEBUGLOG("DownloadForgeRoomData", "start", DEBUG_LOG_LEVEL);
    ShowLoading();

    var level_list = [];
    var workset_list=[];
    var room_list = [];
    var tenjoShiage_list = [];
    var kabeShiage_list = [];
    var yukaShiage_list = [];
    var tenjoShitaji_list = [];
    var kabeShitaji_list = [];
    var yukaShitaji_list = [];
    var habaki_list = [];
    var mawaribuchi_list = [];
    var overviewData = {"Elements":0,"Area":0,"RoomName":0,"CalcHeight":0,"Shucho":0,"RoomHeight":0};
    var chartData = {};
    var totalData = {};
    var versionSelectedCount = $('#version option:selected').length;
    var ajaxCnt = 0;

    $("#level option:selected").each(function(){
        level_list.push($(this).text());
    });
    $("#workset option:selected").each(function(){
        workset_list.push($(this).text());
    });
    $("#roomName option:selected").each(function(){
        room_list.push($(this).text());
    });
    $("#tenjoShiage option:selected").each(function(){
        tenjoShiage_list.push($(this).text());
    });
    $("#kabeShiage option:selected").each(function(){
        kabeShiage_list.push($(this).text());
    });
    $("#yukaShiage option:selected").each(function(){
        yukaShiage_list.push($(this).text());
    });
    $("#tenjoShitaji option:selected").each(function(){
        tenjoShitaji_list.push($(this).text());
    });
    $("#kabeShitaji option:selected").each(function(){
        kabeShitaji_list.push($(this).text());
    });
    $("#yukaShitaji option:selected").each(function(){
        yukaShitaji_list.push($(this).text());
    });
    $("#habaki option:selected").each(function(){
        habaki_list.push($(this).text());
    });
    $("#mawaribuchi option:selected").each(function(){
        mawaribuchi_list.push($(this).text());
    });
    $('#version option:selected').each(function(){
        var valArr =JSON.parse($(this).val());
        var version_number = valArr.version_number;
        var item_id = valArr.item_id;
        
        return $.ajax({
            url: "../forge/getData",
            async:false,
            type: 'post',
            data:{_token: CSRF_TOKEN,message:"getRoomDataByVersion",version_number:version_number,
                    item_id:item_id,workset_list:workset_list,level_list:level_list,room_list:room_list,
                    tenjoShiage_list:tenjoShiage_list,kabeShiage_list:kabeShiage_list,yukaShiage_list:yukaShiage_list,
                    tenjoShitaji_list:tenjoShitaji_list,kabeShitaji_list:kabeShitaji_list,yukaShitaji_list:yukaShitaji_list,
                    habaki_list:habaki_list,mawaribuchi_list:mawaribuchi_list},
            success :function(data) {
                console.log(data);

                if ((data == "") || (data == null)) {
                    HideLoading();
                    alert("not exist in the database.");
                    return;
                }

                if (versionSelectedCount == 1) {
                    OrganizeDataForEachVersion(data, overviewData, chartData);
                    DownloadProcForgeRoomData(overviewData, chartData, 'ForgeRoom');
                }
                else {
                    alert("開発中");
                }
                
                ajaxCnt++;
                if(ajaxCnt == versionSelectedCount){
                    HideLoading();
                }
            },
            error:function(err){
                console.log(err);
                HideLoading();
            }
        });
    });
}

function DisplayforgeData(data, dataLen){
    DEBUGLOG("DisplayforgeData", "start", DEBUG_LOG_LEVEL);
    
    var overview = {};
    var chartData = {};
    var diffList = {};

    if ((data == "") || (data == null)) {
        alert("not exist in the database.");
        return;
    }

    if (dataLen == 1){
        toggleFilter();
        OrganizeDataForEachVersion(data[Object.keys(data)], overview, chartData);
        DisplayCurrentVersionData(overview, chartData);
        // 3DView出力
        //if(token !== undefined || token != ""){
            ShowModel();
        //}
    }
    else{
        toggleFilter();
        OrganizeDataSpecifiedVersion(data, overview, chartData, diffList);

        var overviewList = Object.keys(overview);
        var chartList = Object.keys(chartData);
        var tmpOverview = {};
        var tmpChartData = {};
        tmpOverview[overviewList[0]] = overview[overviewList[0]];
        tmpChartData[chartList[0]] = chartData[chartList[0]];

        console.log("diffList:"+JSON.stringify(diffList));
        DisplayMultipleVersionData(overview, chartData, diffList, tmpOverview);
    }
}

function OrganizeDataForEachVersion(data, overviewData, chartData){
    DEBUGLOG("OrganizeDataForEachVersion", "start", DEBUG_LOG_LEVEL);
    
    CreateOverviewData(data, overviewData);
    CreateChartData(data, chartData);
}

function OrganizeDataSpecifiedVersion(data, overviewData, chartData, diffList){
    DEBUGLOG("OrganizeDataSpecifiedVersion", "start", DEBUG_LOG_LEVEL);
    
    var i = 0;
    var preVersion = "";

    Object.keys(data).forEach(function(version){
        i++;
        var tmpOverviewData = {};
        var tmpChartData = {};
        var tmpDiffList = {"mod":[],"add":[],"del":[]};
        
        CreateOverviewData(data[version], tmpOverviewData);
        CreateChartData(data[version], tmpChartData);
        
        if (i > 1) {
            CreateDiffData(data[preVersion], data[version], tmpDiffList);
        }
        preVersion = version;

        overviewData[version] = tmpOverviewData;
        chartData[version] = tmpChartData;
        diffList[version] = tmpDiffList;
    });
}

function CreateOverviewData(data, overviewData){
    DEBUGLOG("CreateOverviewData", "start", DEBUG_LOG_LEVEL);
    
    var room_num = 0;
    var tmpArea = 0;
    var tmpCalcHeight = 0;
    var tmpRoomHeight = 0;
    var tmpPerimeter = 0;
    var tmpRoom_list = [];
    
    Object.keys(data).forEach(function(key) {
        
        tmpArea += data[key]["menseki"];
        tmpCalcHeight += data[key]["santei_takasa"];
        tmpRoomHeight += data[key]["heya_takasa"];
        tmpPerimeter += data[key]["shucho"];
        
        if (tmpRoom_list.indexOf(data[key]["room_name"]) == -1) {
            tmpRoom_list.push(data[key]["room_name"]);
            ++room_num;
        }
    });

    overviewData["Elements"] = data.length;
    overviewData["面積"] = tmpArea.toFixed(2);
    overviewData["部屋名"] = room_num;
    overviewData["算定高さ"] = tmpCalcHeight.toFixed(2);
    overviewData["周長"] = tmpPerimeter.toFixed(2);
    overviewData["部屋高さ"] = tmpRoomHeight.toFixed(2);
}

function CreateChartData(data, chartData){
    DEBUGLOG("CreateChartData", "start", DEBUG_LOG_LEVEL);
    
    var areaChartDataForEachLevel = {}; // { 床仕上げ名A:面積, 床仕上げ名B:面積, }
    var roomChartData = {};             // { 部屋名A:個数, 部屋名B  :個数, }
    var calcHeightChartData = {};       // { 部屋名A:算定高さ, 部屋名B:算定高さ, }
    var roomHeightChartData = {};       // { 部屋名A:部屋高さ, 部屋名B:部屋高さ, }
    var perimeterChartData = {};        // { 部屋名A:周長, 部屋名B:周長, }
    
    
    var hidAreaChartDataForEachLevel = {}; 
    var hidRoomChartData = {};             
    var hidCalcHeightChartData = {}; 
    var hidRoomHeightChartData = {}; 
    var hidPerimeterChartData = {};  

    Object.keys(data).forEach(function(key) {
        var tmpYukaShiage = "NoName";
        var tmpRoom_name = "NoName";
        var element_db_id = data[key]["element_db_id"];

        if ( (data[key]["shiage_yuka"] != undefined) && (data[key]["shiage_yuka"] != null) && (data[key]["shiage_yuka"] != "") ) {
            tmpYukaShiage = data[key]["shiage_yuka"];
        }
        if ( (data[key]["room_name"] != undefined) && (data[key]["room_name"] != null) && (data[key]["room_name"] != "") ) {
            tmpRoom_name = data[key]["room_name"];   
        }
        
        //面積チャート用データ作成
        if (areaChartDataForEachLevel[tmpYukaShiage]) {
            if ( (data[key]["menseki"] != undefined) && (data[key]["menseki"] != null) ) {
                areaChartDataForEachLevel[tmpYukaShiage] += data[key]["menseki"];
                hidAreaChartDataForEachLevel[tmpYukaShiage] += ','+element_db_id;

            }
        }
        else {
            if ( (data[key]["menseki"] != undefined) && (data[key]["menseki"] != null) ) {
                areaChartDataForEachLevel[tmpYukaShiage] = data[key]["menseki"];
                hidAreaChartDataForEachLevel[tmpYukaShiage] = element_db_id;
            }
        }
        //部屋名チャート用データ作成
        if (roomChartData[tmpRoom_name]) {
            if ( (tmpRoom_name != undefined) && (tmpRoom_name != null) ) {
                ++roomChartData[tmpRoom_name];
                hidRoomChartData[tmpRoom_name] += ','+element_db_id;
            }
        }
        else{
            if ( (tmpRoom_name != undefined) && (tmpRoom_name != null) ) {
                roomChartData[tmpRoom_name] = 1;
                hidRoomChartData[tmpRoom_name] = element_db_id;
            }
        }
        //算定高さチャート用データ作成
        if (calcHeightChartData[tmpRoom_name]) {
            if ( (data[key]["santei_takasa"] != undefined) && (data[key]["santei_takasa"] != null) ) {
                calcHeightChartData[tmpRoom_name] += data[key]["santei_takasa"];
                hidCalcHeightChartData[tmpRoom_name] += ','+element_db_id;
            }
        }
        else {
            if ( (data[key]["santei_takasa"] != undefined) && (data[key]["santei_takasa"] != null) ) {
                calcHeightChartData[tmpRoom_name] = data[key]["santei_takasa"];
                hidCalcHeightChartData[tmpRoom_name] = element_db_id;
            }
        }
        //部屋高さチャート用データ作成
        if (roomHeightChartData[tmpRoom_name]) {
            if ( (data[key]["heya_takasa"] != undefined) && (data[key]["heya_takasa"] != null) ) {
                roomHeightChartData[tmpRoom_name] += data[key]["heya_takasa"];
                hidRoomHeightChartData[tmpRoom_name] += ','+element_db_id;
            }
        }
        else {
            if ( (data[key]["heya_takasa"] != undefined) && (data[key]["heya_takasa"] != null) ) {
                roomHeightChartData[tmpRoom_name] = data[key]["heya_takasa"];
                hidRoomHeightChartData[tmpRoom_name] = element_db_id;
            }
        }
        //周長チャート用データ作成
        if (perimeterChartData[tmpRoom_name]) {
            if ( (data[key]["shucho"] != undefined) && (data[key]["shucho"] != null) ) {
                perimeterChartData[tmpRoom_name] += data[key]["shucho"];
                hidPerimeterChartData[tmpRoom_name] += ','+element_db_id;
            }
        }
        else {
            if ( (data[key]["shucho"] != undefined) && (data[key]["shucho"] != null) ) {
                perimeterChartData[tmpRoom_name] = data[key]["shucho"];
                hidPerimeterChartData[tmpRoom_name] = element_db_id;
            }
        }
    });

    chartData["Area"]       = sortObjectValue(areaChartDataForEachLevel, false);
    chartData["Room"]       = sortObjectValue(roomChartData, false);
    chartData["CalcHeight"] = sortObjectValue(calcHeightChartData, false);
    chartData["Perimeter"]  = sortObjectValue(perimeterChartData, false);
    chartData["RoomHeight"] = sortObjectValue(roomHeightChartData, false);
    
    chartData["HidAreaIds"]       = hidAreaChartDataForEachLevel;
    chartData["HidRoomIds"]       = hidRoomChartData;
    chartData["HidCalcHeightIds"] = hidCalcHeightChartData;
    chartData["HidPerimeterIds"]  = hidPerimeterChartData;
    chartData["HidRoomHeightIds"] = hidRoomHeightChartData;
}

function sortObjectValue(sortData, isAscendingOrder){
    DEBUGLOG("sortObjectValue", "start", DEBUG_LOG_LEVEL);
    
    var tmpArrays = [];
    var retSortedData = {};
    
    // 一旦配列変換し並べ替え
    Object.keys(sortData).forEach(function(key){
        var tmpArray = [key, sortData[key]];
        tmpArrays.push(tmpArray);
    });
    if (isAscendingOrder) {
        tmpArrays.sort(function(a,b){ return(a[1]-b[1]); });
    }
    else{
        tmpArrays.sort(function(a,b){ return(b[1]-a[1]); });
    }
    
    // Objectに戻す
    for(let i = 0; i < tmpArrays.length; i++) {
        retSortedData[tmpArrays[i][0]] = tmpArrays[i][1];
    }
    
    return retSortedData;
}

function CreateDiffData(preData, curData, diffList){
    DEBUGLOG("CreateDiffData", "start", DEBUG_LOG_LEVEL);
    
    var delTable = [];
    var addTable = [];
    var modTable = [];
    
    if(preData === curData){ return; }

    $.each(preData,function(key,row){
        // 削除データを検索
        var tmpCurData = curData.find((v) => v.element_id === row["element_id"])
        if (tmpCurData == undefined) {
            // 削除確定
            delTable.push(preData[key]);
        }
    })
    
    $.each(curData,function(key,row){
        // 変更・追加データを検索
        var tmpPreData = preData.find((v) => v.element_id === row["element_id"])
        if (tmpPreData == undefined) {
            // 追加確定
            addTable.push(curData[key]);
        }
        else {
            // 変更判定
            if (  (row["element_id"] != tmpPreData["element_id"])
                ||(row["habaki"] != tmpPreData["habaki"])
                ||(row["heya_takasa"] != tmpPreData["heya_takasa"])
                ||(row["id"] != tmpPreData["id"])
                ||(row["kabe_shitaji"] != tmpPreData["kabe_shitaji"])
                ||(row["level"] != tmpPreData["level"])
                ||(row["mawaribuchi"] != tmpPreData["mawaribuchi"])
                ||(row["menseki"] != tmpPreData["menseki"])
                ||(row["menseki_kakikomi"] != tmpPreData["menseki_kakikomi"])
                ||(row["room_name"] != tmpPreData["room_name"])
                ||(row["santei_takasa"] != tmpPreData["santei_takasa"])
                ||(row["shiage_kabe"] != tmpPreData["shiage_kabe"])
                ||(row["shiage_tenjo"] != tmpPreData["shiage_tenjo"])
                ||(row["shiage_yuka"] != tmpPreData["shiage_yuka"])
                ||(row["shucho"] != tmpPreData["shucho"])
                ||(row["tenjo_shitaji"] != tmpPreData["tenjo_shitaji"])
                ||(row["version_number"] != tmpPreData["version_number"])
                ||(row["yuka_shitaji"] != tmpPreData["yuka_shitaji"])
               ) {
                // 変更確定
                modTable.push(curData[key]);
            }
        }
    })
    
    diffList["mod"] = modTable;
    diffList["add"] = addTable;
    diffList["del"] = delTable;
}

function DisplayCurrentVersionData(overviewData, chartData){
    DEBUGLOG("DisplayCurrentVersionData", "start", DEBUG_LOG_LEVEL);
    console.log(chartData);
    
    var appendText = "";

    if ( (isEmpty(overviewData)) || (isEmpty(chartData)) ) { return; }
    
    $("#hidAreaChartContainer").val(JSON.stringify(chartData["HidAreaIds"]));
    $("#hidRoomChartContainer").val(JSON.stringify(chartData["HidRoomIds"]));
    $("#hidCalcHeightChartContainer").val(JSON.stringify(chartData["HidCalcHeightIds"]));
    $("#hidPerimeterChartContainer").val(JSON.stringify(chartData["HidPerimeterIds"]));
    $("#hidRoomHeightChartContainer").val(JSON.stringify(chartData["HidRoomHeightIds"]));

    appendText += "<div id='AnalysisView' style='display:flex;'>";
    appendText += "<div style='width:55%;'>";
    /* Small Stats Block */
    appendText += "<div class='row'>";
    
    Object.keys(overviewData).forEach(function(key) {
        appendText += "<div class='stats-small-area' style='flex: 0 0 16%;'>";
        appendText += "<div class='stats-small stats-small--1 card card-small'>";
        appendText += "<div class='card-body 0-1 d-flex'>";
        appendText += "<div class='d-flex flex-column m-auto'>";
        appendText += "<div class='stats-small__data text-center'>";
        appendText += "<span class='stats-small__label text-uppercase'>"+key+"</span>";
        appendText += "<h2 class='stats-small__value count my-3'>"+overviewData[key]+"</h2>";
        appendText += "</div></div></div></div></div>";
    });
    
    appendText += "</div>";
    /* End Small Stats Block */

    /* Chart Stats Block */
    appendText += "<div class='tab-wrap'>";
    
    appendText += "<input id='tab01' type='radio' name='tab' class='tab-switch' checked='checked'><label class='tab-label' for='tab01'>円グラフ</label>";
    appendText += "<div class='tab-content'>";

        /* Pie Chart */
        appendText += "<div style='height:65vh;display:flex;flex-wrap:wrap;'>";
            appendText += "<div class='tab-content-chart-area'>";
                appendText += "<div id=AreaPieChartContainer style='width:100%;height:100%;'></div>";
                DrawPieChart(chartData["Area"], "Area", "面積");
            appendText += "</div>";
            appendText += "<div class='tab-content-chart-area'>";
                appendText += "<div id=RoomPieChartContainer style='width:100%;height:100%;'></div>";
                DrawPieChart(chartData["Room"], "Room", "部屋名");
            appendText += "</div>";
            appendText += "<div class='tab-content-chart-area'>";
                appendText += "<div id=CalcHeightPieChartContainer style='width:100%;height:100%;'></div>";
                DrawPieChart(chartData["CalcHeight"], "CalcHeight", "算定高さ");
            appendText += "</div>";
            appendText += "<div class='tab-content-chart-area'>";
                appendText += "<div id=PerimeterPieChartContainer style='width:100%;height:100%;'></div>";
                DrawPieChart(chartData["Perimeter"], "Perimeter", "周長");
            appendText += "</div>";
            appendText += "<div class='tab-content-chart-area'>";
                appendText += "<div id=RoomHeightPieChartContainer style='width:100%;height:100%;'></div>";
                DrawPieChart(chartData["RoomHeight"], "RoomHeight", "部屋高さ");
            appendText += "</div>";
        appendText += "</div>";
        /* End Pie Chart */
        
    appendText += "</div>";
    
    appendText += "<input id='tab02' type='radio' name='tab' class='tab-switch'><label class='tab-label' for='tab02'>棒グラフ</label>";
    appendText += "<div class='tab-content'>";
    
        /* Column Chart */
        appendText += "<div style='height:80vh;'>";
            appendText += "<div class='tab-content-chart-area' style='background-color:lightblue;'>";
                appendText += "<div id=AreaColumnChartContainer style='width:100%;height:100%;'></div>";
                DrawColumnChart(chartData["Area"], "Area", "面積", "(m^2)");
            appendText += "</div>";
            appendText += "<div class='tab-content-chart-area'>";
                appendText += "<div id=RoomColumnChartContainer style='width:100%;height:100%;'></div>";
                DrawColumnChart(chartData["Room"], "Room", "部屋名", "(部屋数)");
            appendText += "</div>";
            appendText += "<div class='tab-content-chart-area'style='background-color:lightblue;'>";
                appendText += "<div id=CalcHeightColumnChartContainer style='width:100%;height:100%;'></div>";
                DrawColumnChart(chartData["CalcHeight"], "CalcHeight", "算定高さ", "(mm)");
            appendText += "</div>";
            appendText += "<div class='tab-content-chart-area'>";
                appendText += "<div id=PerimeterColumnChartContainer style='width:100%;height:100%;'></div>";
                DrawColumnChart(chartData["Perimeter"], "Perimeter", "周長", "(mm)");
            appendText += "</div>";
            appendText += "<div class='tab-content-chart-area'>";
                appendText += "<div id=RoomHeightColumnChartContainer style='width:100%;height:100%;'></div>";
                DrawColumnChart(chartData["RoomHeight"], "RoomHeight", "部屋高さ", "(mm)");
            appendText += "</div>";
        appendText += "</div>";
        /* End Column Chart */
    
    appendText += "</div>";//tab-content
    
    appendText += "</div>";//tab-wrap
    appendText += "</div>&nbsp;&nbsp;";//end of width 55% div
    /* End Chart Stats Block */
    	//##############################################################################################
	// 3DView出力用HTML記述
	//##############################################################################################
    appendText += "<div id='modelViewer'><span id='spanText'></span>";
    appendText += "</div>"; // modelViewer

    appendText += "</div>"; // AnalysisView
    
    $("#tblVersionData div").remove();
    $("#tblVersionData").append(appendText);
}

function DisplayMultipleVersionData(overviewData, chartData, diffList, tmpOverview){
    DEBUGLOG("DisplayMultipleVersionData", "start", DEBUG_LOG_LEVEL);
    
    var appendText = "";

    if (isEmpty(overviewData)){
        DEBUGLOG("DisplayMultipleVersionData", "overviewData is empty.", DEBUG_LOG_LEVEL);
        return;
    }
    if(isEmpty(chartData)){
        DEBUGLOG("DisplayMultipleVersionData", "chartData is empty.", DEBUG_LOG_LEVEL);
        return;
    }

    /* Small Stats Block */
    appendText += "<div class='slideshow' style='display:flex;'>";

    Object.keys(overviewData).forEach(function(version) {
        appendText += "<div class='stats-small-area'>";
        appendText += "<div class='stats-small stats-small--1 card card-small'>";
        appendText += "<div class='card-body 0-1 d-flex'>";
        appendText += "<div class='d-flex flex-column m-auto'>";
        appendText += "<div class='stats-small__data text-center'>";
        appendText += "<span class='stats-small__label text-uppercase'>Version</span>";
        appendText += "<h2 class='stats-small__value count my-3'>"+version+"</h2>";
        appendText += "</div></div></div></div></div>";
        
        Object.keys(overviewData[version]).forEach(function(key) {
            appendText += "<div class='stats-small-area'>";
            appendText += "<div class='stats-small stats-small--1 card card-small'>";
            appendText += "<div class='card-body 0-1 d-flex'>";
            appendText += "<div class='d-flex flex-column m-auto'>";
            appendText += "<div class='stats-small__data text-center'>";
            appendText += "<span class='stats-small__label text-uppercase'>"+key+"</span>";
            appendText += "<h2 class='stats-small__value count my-3'>"+overviewData[version][key]+"</h2>";
            appendText += "</div></div></div></div></div>";
        });
        
    });
    
    appendText += "</div>"; //slideshow
    /* End Small Stats Block */

    /* Chart Stats Block */
    appendText += "<div class='tab-wrap'>";
    
    appendText += "<input id='tab01' type='radio' name='tab' class='tab-switch' checked='checked'><label class='tab-label' for='tab01'>バージョン間推移</label>";
    appendText += "<div class='tab-content'>";

        /* Line Chart */
        appendText += "<div style='height:80vh;display:flex;flex-wrap:wrap;'>";
        
        
            appendText += "<div class='tab-content-chart-area'>";
                appendText += "<div id=ElementsLineChartContainer style='width:100%;height:100%;'></div>";
                DrawLineChart(overviewData, "Elements", "要素数");
            appendText += "</div>";
            appendText += "<div class='tab-content-chart-area'>";
                appendText += "<div id=AreaLineChartContainer style='width:100%;height:100%;'></div>";
                DrawLineChart(overviewData, "Area", "面積");
            appendText += "</div>";
            appendText += "<div class='tab-content-chart-area'>";
                appendText += "<div id=RoomNameLineChartContainer style='width:100%;height:100%;'></div>";
                DrawLineChart(overviewData, "RoomName", "部屋名(種類)");
            appendText += "</div>";
            appendText += "<div class='tab-content-chart-area'>";
                appendText += "<div id=CalcHeightLineChartContainer style='width:100%;height:100%;'></div>";
                DrawLineChart(overviewData, "CalcHeight", "算定高さ");
            appendText += "</div>";
            appendText += "<div class='tab-content-chart-area'>";
                appendText += "<div id=RoomHeightLineChartContainer style='width:100%;height:100%;'></div>";
                DrawLineChart(overviewData, "RoomHeight", "部屋高さ");
            appendText += "</div>";
            appendText += "<div class='tab-content-chart-area'>";
                appendText += "<div id=ShuchoLineChartContainer style='width:100%;height:100%;'></div>";
                DrawLineChart(overviewData, "Shucho", "周長");
            appendText += "</div>";
            
            
        appendText += "</div>";
        /* End Line Chart */
        
    appendText += "</div>"; // tab-content
    
    var tmpVersion = Object.keys(diffList);

    appendText += "<input id='tab02' type='radio' name='tab' class='tab-switch'><label class='tab-label' for='tab02'>変更情報一覧</label>";
    appendText += "<div class='tab-content'>";
    
        appendText += "<div class='content-mod' style='display:flex;'>";
    
    var preVer = "";
    var i = 0;
    Object.keys(overviewData).forEach(function(version) {
        if (i > 0) {
            /* Table */
            appendText += "<div><h4 style='text-align:center;'>version"+preVer+"-version"+version+"</h4><table id='tblModData"+version+"'></table></div>";
            /* End Table */
        }
        preVer = version;
        i++;
    });
    
        appendText += "</div>";
        
    appendText += "</div>"; // tab-content

    appendText += "<input id='tab03' type='radio' name='tab' class='tab-switch'><label class='tab-label' for='tab03'>追加情報一覧</label>";
    appendText += "<div class='tab-content'>";
    
        appendText += "<div class='content-add' style='display:flex;'>";
        
    preVer = "";
    i = 0;
    Object.keys(overviewData).forEach(function(version) {
        if (i > 0) {
            /* Table */
            appendText += "<div><h4 style='text-align:center;'>version"+preVer+"-version"+version+"</h4><table id='tblAddData"+version+"'></table></div>";
            /* End Table */
        }
        preVer = version;
        i++;
    });

        appendText += "</div>";
        
    appendText += "</div>"; // tab-content

    appendText += "<input id='tab04' type='radio' name='tab' class='tab-switch'><label class='tab-label' for='tab04'>削除情報一覧</label>";
    appendText += "<div class='tab-content'>";
    
        appendText += "<div class='content-del' style='display:flex;'>";
        
    preVer = "";
    i = 0;
    Object.keys(overviewData).forEach(function(version) {
        if (i > 0) {
            /* Table */
            appendText += "<div><h4 style='text-align:center;'>version"+preVer+"-version"+version+"</h4><table id='tblDelData"+version+"'></table></div>";
            /* End Table */
        }
        preVer = version;
        i++;
    });

        appendText += "</div>";
        
    appendText += "</div>"; // tab-content

    appendText += "</div>"; // tab-wrap
    /* End Chart Stats Block */
    
    $("#tblVersionData div").remove();
    $("#tblVersionData").append(appendText);
    
    i = 0;
    Object.keys(overviewData).forEach(function(version) {
        if (i > 0) {
            DisplayTable(diffList[version]["mod"], "tblModData"+version);
            DisplayTable(diffList[version]["add"], "tblAddData"+version);
            DisplayTable(diffList[version]["del"], "tblDelData"+version);
        }
        
        i++;
    });
    
    $('.slideshow').slick({
        arrows: false,
        autoplay: false,
        autoplaySpeed: 5000, // [ms]
        slidesToShow: 7,
        slidesToScroll: 7,
        dots: true,
        // vertical: true,
        // verticalSwiping: true,
        // prevArrow:'<div class="arrow prev">PREV</div>',
        // nextArrow:'<div class="arrow next">NEXT</div>',
        // prevArrow:'<img src="../public/image/arrow_left.png" style="height:32px;" class="arrow prev">',
        // nextArrow:'<img src="../public/image/arrow_right.png" style="height:32px;" class="arrow next">',
        // prevArrow: '<div class="arrow prev"><img src="../public/image/arrow_left.png" style="margin-top:37px;"></div>',
        // nextArrow: '<div class="arrow next"><img src="../public/image/arrow_right.png" style="margin-top:37px;margin-right:15px;"></div>',
    });

    $('.content-mod').slick({
        arrows: true,
        autoplay: false,
        infinite: false,
        slidesToShow: 1,
        slidesToScroll: 1,
        appendDots: true,
        prevArrow:'<div class="arrow prev">←PREV</div>',
        nextArrow:'<div class="arrow next">NEXT→</div>',
    });
    $('.content-add').slick({
        arrows: true,
        autoplay: false,
        infinite: false,
        slidesToShow: 1,
        slidesToScroll: 1,
        appendDots: true,
        prevArrow:'<div class="arrow prev">←PREV</div>',
        nextArrow:'<div class="arrow next">NEXT→</div>',
    });
    $('.content-del').slick({
        arrows: true,
        autoplay: false,
        infinite: false,
        slidesToShow: 1,
        slidesToScroll: 1,
        appendDots: true,
        prevArrow:'<div class="arrow prev">←PREV</div>',
        nextArrow:'<div class="arrow next">NEXT→</div>',
    });
}

function DrawPieChart(chartData, id, title){
    DEBUGLOG("DrawPieChart", "start", DEBUG_LOG_LEVEL);
    
    var points =  [];
    var total= 0;
    
    Object.keys(chartData).forEach(function(key) {
        var intArea = parseFloat(chartData[key]);
        points.push([key,intArea]);
    });
    
    google.charts.load('current', {packages: ['corechart']});
    google.charts.setOnLoadCallback(function(){pieChart(points, id, title)});
}

function pieChart(chartData, id, title){
    DEBUGLOG("pieChart", "start", DEBUG_LOG_LEVEL);
    
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'item');
    data.addColumn('number', 'area');
    data.addRows(chartData);

    var options = {
        title:title,
        titleTextStyle:{fontSize:18},
        pieSliceText: 'value',
        animation:{
            duration: 1000,
            easing: 'out',
            startup: true
        },
        legend:{
            textStyle:{fontSize:10},
            position: 'labeled'
        }
      };

    var chart = new google.visualization.PieChart(document.getElementById(id+"PieChartContainer"));
    //start chart select event
     function selectHandler() 
     {
       var selectedItem = chart.getSelection()[0];
       if (selectedItem) 
       {
         var selectedLevel = data.getValue(selectedItem.row, 0);
         var hidData = $("#hid"+id+"ChartContainer").val();

         if(hidData != undefined){
                var hid_ids = JSON.parse(hidData); 
                var selected_ele_ids = hid_ids[selectedLevel];
                //alert(selected_ele_ids);
                ViewerHighLight(selected_ele_ids);
         }
       }
     } 
     
    google.visualization.events.addListener(chart, 'select', selectHandler);  
    //end chart select event
    
    chart.draw(data, options);
}

function DrawColumnChart(chartData, id, title, scale){
    DEBUGLOG("DrawColumnChart", "start", DEBUG_LOG_LEVEL);
    
    var points =  [];
    var total= 0;
    
    Object.keys(chartData).forEach(function(key) {
        var intArea = parseFloat(chartData[key]);
        points.push([key,intArea]);
    });
    
    google.charts.load('current', {packages: ['corechart', 'bar']});
    google.charts.setOnLoadCallback(function(){columnChart(points, id, title, scale)});
}

function columnChart(chartData, id, title, scale) {
    DEBUGLOG("columnChart", "start", DEBUG_LOG_LEVEL);
    
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'title');
    data.addColumn('number', '');
    data.addRows(chartData);
    // console.log("chartData"+JSON.stringify(chartData));
    // data.addRows([["aa",10],["bb",150]]);
    var options = {
        title: title,
        titleTextStyle: {fontSize:20},
        animation:{ duration: 1000,easing: 'out',startup: true },
        // hAxis: {title: title, titleTextStyle:{italic:true}, textStyle:{fontSize:10}},
        vAxis: { minValue: 0, title: scale, titleTextStyle:{italic:true} },
        series: [{ visibleInLegend: false }],
        bar: { groupWidth: 20 }
    };

    var chart = new google.visualization.ColumnChart(document.getElementById(id+"ColumnChartContainer"));
    //start chart select event
     function selectHandler() 
     {
       var selectedItem = chart.getSelection()[0];
       if (selectedItem) 
       {
         var selectedLevel = data.getValue(selectedItem.row, 0);
         var hidData = $("#hid"+id+"ChartContainer").val();

         if(hidData != undefined){
                var hid_ids = JSON.parse(hidData); 
                var selected_ele_ids = hid_ids[selectedLevel];
                //alert(selected_ele_ids);
                ViewerHighLight(selected_ele_ids);
         }
       }
     } 
     
    google.visualization.events.addListener(chart, 'select', selectHandler);  
    //end chart select event
    chart.draw(data, options);
}

function DrawLineChart(overviewData, id, title){
    DEBUGLOG("DrawLineChart", "start", DEBUG_LOG_LEVEL);

    var points =  [];
    var total= 0;
    
    Object.keys(overviewData).forEach(function(version) {
        var intArea = parseFloat(overviewData[version][id]);
        points.push([version, intArea]);
    });
    
    google.charts.load('current', {packages: ['corechart', 'line']});
    google.charts.setOnLoadCallback(function(){lineChart(points, id, title)});
}

function lineChart(chartData, id, title) {
    DEBUGLOG("lineChart", "start", DEBUG_LOG_LEVEL);
    
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'version');
    data.addColumn('number', title);
    data.addRows(chartData);

    var options = {
        title: title,
        titleTextStyle:{fontSize:18},
        // curveType: 'function',
        hAxis: { title: 'Version' },
        legend: { position: 'none' },
    };

    var chart = new google.visualization.LineChart(document.getElementById(id+"LineChartContainer"));
    chart.draw(data, options);
}

function isEmpty(obj) {
    return !Object.keys(obj).length;
}

function DisplayTable(data, id){
    DEBUGLOG("DisplayTable", "start", DEBUG_LOG_LEVEL);
    
     var appendText = "";
     appendText += "<tr>";
     appendText += "<th>No.</th>";
     appendText += "<th>element_id</th>";
     appendText += "<th>level</th>";
     appendText += "<th>shiage_tenjo</th>";
     appendText += "<th>tenjo_shitaji</th>";
     appendText += "<th>mawaribuchi</th>";
     appendText += "<th>shiage_kabe</th>";
     appendText += "<th>kabe_shitaji</th>";
     appendText += "<th>habaki</th>";
     appendText += "<th>shiage_yuka</th>";
     appendText += "<th>shucho</th>";
     appendText += "<th>menseki_kakikomi</th>";
     appendText += "<th>santei_takasa</th>";
     appendText += "<th>heya_takasa</th>";
     appendText += "<th>menseki</th>";
     appendText += "<th>workset</th>";
     appendText += "<th>version_number</th>";
     appendText += "</tr>";
     
     var count = 0;
    $.each(data,function(key,row){
        count++;
        appendText += "<tr>";
        appendText += "<td>"+count+".</td>";
        appendText += "<td>"+row["element_id"]+"</td>";
        appendText += "<td>"+row["level"]+"</td>";
        appendText += "<td>"+row["shiage_tenjo"]+"</td>";
        appendText += "<td>"+row["tenjo_shitaji"]+"</td>";
        appendText += "<td>"+row["mawaribuchi"]+"</td>";
        appendText += "<td>"+row["shiage_kabe"]+"</td>";
        appendText += "<td>"+row["kabe_shitaji"]+"</td>";
        appendText += "<td>"+row["habaki"]+"</td>";
        appendText += "<td>"+row["shiage_yuka"]+"</td>";
        appendText += "<td>"+row["shucho"]+"</td>";
        appendText += "<td>"+row["menseki_kakikomi"]+"</td>";
        appendText += "<td>"+row["santei_takasa"]+"</td>";
        appendText += "<td>"+row["heya_takasa"]+"</td>";
        appendText += "<td>"+row["menseki"]+"</td>";
        appendText += "<td>"+row["workset"]+"</td>";
        appendText += "<td>"+row["version_number"]+"</td>";
        appendText += "</tr>";
    })
    $("#"+id+" tr").remove();
    $("#"+id).append(appendText);
}

function DownloadProcForgeRoomData(overviewData, chartData, fileName){
    DEBUGLOG("DownloadProcForgeRoomData", "start", DEBUG_LOG_LEVEL);
    
    var arrayExportData = [];
    var arrayOverviewKey = [];
    var arrayOverviewValue = [];
    var now = new Date();
    var curDate = now.getFullYear().toString()+(now.getMonth()+1).toString()+now.getDate().toString()+
                    '_'+now.getHours().toString()+now.getMinutes().toString()+now.getSeconds().toString();
    var workbook = {SheetNames: [], Sheets: {}};
    var wopts = {   // 書込みオプション参照先 => https://github.com/SheetJS/js-xlsx/blob/master/README.md#writing-options
        bookType: 'xlsx',
        bookSST: false,
        type: 'binary'
    };
    
    Object.keys(overviewData).forEach(function(key){
        arrayOverviewKey.push(key);
        arrayOverviewValue.push(overviewData[key]);
    });
    arrayExportData.push(arrayOverviewKey);
    arrayExportData.push(arrayOverviewValue);
    workbook.SheetNames.push('OverviewData');
    workbook.Sheets['OverviewData'] = aoa_to_workbook(arrayExportData);

    Object.keys(chartData).forEach(function(sheetName){
        var arrayChartKey = [];
        var arrayChartValue = [];
        var sheetData = chartData[sheetName];
        
        Object.keys(sheetData).forEach(function(key){
            arrayChartKey.push(key);
            arrayChartValue.push(sheetData[key]);
        });

        arrayExportData.push(arrayChartKey);
        arrayExportData.push(arrayChartValue);
        console.log("arrayExportData"+JSON.stringify(arrayExportData));
        workbook.SheetNames.push(sheetName);
        workbook.Sheets[sheetName] = aoa_to_workbook(arrayExportData);
    });
    
    // ArrayをWorkbookに変換し、WorkbookからBlobオブジェクトを生成
    var wb = aoa_to_workbook(arrayExportData);
    var wb_out = XLSX.write(wb, wopts);
    var blob = new Blob([s2ab(wb_out)], { type: 'application/octet-stream' });

    // xlsxファイルダウンロード
    saveAs(blob, fileName+curDate+".xlsx");
}

function aoa_to_workbook(data/*:Array<Array<any> >*/, opts)/*:Workbook*/ {
    // SheetをWorkbookに追加する
    return sheet_to_workbook(XLSX.utils.aoa_to_sheet(data, opts), opts);
}

function s2ab(s) {
    var buf = new ArrayBuffer(s.length);
    var view = new Uint8Array(buf);
    for (var i = 0; i != s.length; ++i) view[i] = s.charCodeAt(i) & 0xFF;
    return buf;
}

function sheet_to_workbook(sheet/*:Worksheet*/, opts)/*:Workbook*/ {
    var n = opts && opts.sheet ? opts.sheet : "Sheet1";
    var sheets = {}; sheets[n] = sheet;
    return { SheetNames: [n], Sheets: sheets };
}

var isOpen = true;
function toggleFilter(){
    if(isOpen){
        $(".menu-content").css("display","none");
        isOpen = false;
    }else{
        $(".menu-content").css("display","block");
        isOpen = true;
    }
}
