/* ajax通信トークン定義 */
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

/* Placeholder名称定義 */
var PLACEHOLDER_NAME_FOLDER     = "Select Folder";
var PLACEHOLDER_NAME_PROJECT    = "Select Project";
var PLACEHOLDER_NAME_VERSION    = "Select Versions";
var PLACEHOLDER_NAME_CATEGORY   = "Select Category";
var PLACEHOLDER_NAME_TYPE       = "Select Type";

$(document).ready(function(){
    $.ajaxSetup({
        cache:false
    });

    $("#project").select2({
        placeholder:"Folders Loading..."
    });
    $("#item").select2({
        placeholder:"Project Loading..."
    });
    $("#category").select2({
        placeholder:"Select Category"
    });
    $("#typeName").select2({
        placeholder:"Select Type"
    });
    
    LoadComboData();
    
    $("#project").change(function() {
        ProjectChange();
    });

});

/**
 * セレクトボックス内の項目をLoadする。
 * @return なし
 */
function LoadComboData(){
    $.ajax({
        url: "../forge/getData",
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"getComboData"},
        success :function(data) {
            if(data != null){
                BindComboData(data["projects"],"project",PLACEHOLDER_NAME_FOLDER);
                BindComboData(data["items"],"item",PLACEHOLDER_NAME_PROJECT);
                // BindComboData(data["versions"],"version",PLACEHOLDER_NAME_VERSION);
            }                                 
        },
        error:function(err){
            console.log(err);
        }
    });
}

/**
 * セレクトボックス内の項目をバインドする。
 * @param  {object}  [in]data        元データ(単一バージョンのみ)
 * @param  {string}  [in]comboId     セレクトボックスの識別ID
 * @param  {string}  [in]placeholder セレクトボックス内のplaceholder
 * @return なし
 */
function BindComboData(data,comboId,placeholder){
    var appendText = "";
    $.each(data,function(key,value){      
        value["name"] = value["name"].trim();
        if(comboId == "version"){
            var fileName = value["name"]+"("+value["version_number"]+")";
            appendText +="<option value='"+JSON.stringify(value)+"'>"+fileName+"</option>";
        }else if(comboId == "items"){
            //TODO
        }else{
            appendText +="<option value='"+JSON.stringify(value)+"'>"+value["name"]+"</option>";
        }
        
    });
    $("select#"+comboId+" option").remove();
    $("#"+comboId).append(appendText).select2({placeholder:placeholder}).trigger('changed');
}

/**
 * セレクトボックス内の項目をバインドする。
 * @param  {object}  [in]multiData   元データリスト
 * @param  {string}  [in]comboId     セレクトボックスの識別ID
 * @param  {string}  [in]placeholder セレクトボックス内のplaceholder
 * @return なし
 */
function BindComboMultiData(multiData,comboId,placeholder){
    var appendText = "";
    
    for (var i = 0; i < multiData.length; ++i) {
        $.each(multiData[i],function(key,value){      
            value["name"] = value["name"].trim();
            if(comboId == "version"){
                var fileName = value["name"]+"("+value["version_number"]+")";
                appendText +="<option value='"+JSON.stringify(value)+"'>"+fileName+"</option>";
            }else{
                appendText +="<option value='"+JSON.stringify(value)+"'>"+value["name"]+"</option>";
            }
            
        });
    }
    $("select#"+comboId+" option").remove();
    $("#"+comboId).append(appendText).select2({placeholder:placeholder}).trigger('changed');
}

/**
 * Project選択状態変更時に実行
 * 選択したProjectのItemのみセレクトボックス内の項目を絞り込む
 * @return なし
 */
function ProjectChange(){
    console.log("[DebugLog]ProjectChange start");
    var folderSelectedCount = $('#project option:selected').length;
    var totalBindData = [];
    var tmpPrePrjName = "";

    $('#project option:selected').each(function(){
        var tmpStr = (tmpPrePrjName != "") ? tmpPrePrjName + " " : "";
        var projectName = $(this).text();
        projectName.replace(tmpStr, "");

        $.ajax({
            url: "../forge/getData",
            async:false,
            type: 'post',
            data:{_token: CSRF_TOKEN,message:"getComboDataByProject",projectName:projectName,itemName:""},
            success :function(data) {
                // console.log(data);
                if(data != null){
                    totalBindData.push(data["items"]);
                }
            },
            error:function(err){
                console.log(err);
            }
        });
        
        tmpPrePrjName = $(this).text();
    });
    
    BindComboMultiData(totalBindData,"item",PLACEHOLDER_NAME_PROJECT);
    $("#category").select2({placeholder:PLACEHOLDER_NAME_CATEGORY}).trigger('changed');
}

/**
 * Item選択状態変更時に実施する処理
 * @return なし
 */
function ItemChange(){
    //NOP
}

/**
 * 検索条件に一致するデータのレポート、Forgeモデル(3DView)を出力する。
 * @param なし
 * @return なし
 */
function ReportProjectsOverview(){
    
    var selected_categories=[];
    var totalData = {};

    $("#category option:selected").each(function(){
        selected_categories.push($(this).val());
    });
    $("#item option:selected").each(function(){
        var valArr = JSON.parse($(this).val());     
        // console.log("[DebugLog]valArr:"+JSON.stringify(valArr));
        var item_id = valArr.id;
        var item_name = valArr.name;

        $.ajax({
            url: "../forge/getData",
            async:false,
            type: 'post',
            data:{_token: CSRF_TOKEN,message:"getDataByVersion",version_number:"",
                    item_id:item_id,category_list:selected_categories,material_list:"",workset_list:"",
                    level_list:"",familyName_list:"",typeName_list:"",typeName_filter:""},
            success :function(data) {
                console.log("[DebugLog]data:"+JSON.stringify(data));
                totalData[item_name] = data;
            },
            error:function(err){
                console.log(err);
            }
        });
    });
    
    // console.log("[DebugLog]totalData length:"+Object.keys(totalData).length);
    // console.log("[DebugLog]totalData:"+JSON.stringify(totalData));

    // DisplayForgeData(totalData);
}

/**
 * データを出力する。
 * @param  {object} [in]data    元データ
 * @param  {number} [in]dataLen データ長
 * @return なし
 */
function DisplayForgeData(data) {
    var overviewData = {};
    
    if ((data == "") || (data == null)) {
        alert("not exist in the database.");
        return;
    }
    
    OrganizeDataMultiProject(data, overviewData);
    DisplayChartData(overviewData);
}

/**
 * 複数プロジェクトデータを出力用(概要)データに整理する。
 * @param  {object}  [in]data          元データ
 * @param  {object}  [out]overviewData 概要データ
 * @return なし
 */
function OrganizeDataMultiProject(data, overviewData){
    
    Object.keys(data).forEach(function(version){
        var tmpChartData = {};

        CreateChartData(data[version], tmpChartData);
        
        // overviewData[version] = tmpOverviewData;
        // chartData[version] = tmpChartData;
    });
}

/**
 * 複数プロジェクトの概要データを出力するためのHTMLを記述し、出力用タグに適用する。
 * @param  {object}  [in]overviewData 概要データ
 * @return なし
 */
function DisplayChartData(overviewData){
    
}

/**
 * チャートデータを生成する。
 * @param  {object} [in]data      元データ
 * @param  {object} [in]chartData チャートデータ
 * @return なし
 */
function CreateChartData(data, chartData){
    // DEBUGLOG("CreateChartData", "start");
    
    // var volumeChartDataForEachLevel = {};   // { level_name_a:容積, level_name_b:容積, }
    // var materialChartData = {};             // { material_name_a:個数, material_name_b:個数, }
    // var typeNameChartData = {};             // { type_name_a:個数, type_name_b:個数, }
    // var familyNameChartData = {};           // { family_name_a:個数, family_name_b:個数, }
    
    // var hidVolumeChartDataForEachLevel = {};
    // var hidMaterialChartData = {};
    // var hidFamilyNameChartData = {}; 
    // var hidTypeNameChartData = {};
    
    // Object.keys(data).forEach(function(key) {
    //     var tmpLevel            = data[key]["level"];
    //     var tmpMaterial_name    = data[key]["material_name"];
    //     var tmpType_name        = data[key]["type_name"];
    //     var tmpFamily_name      = data[key]["family_name"];
    //     var element_db_id = data[key]["element_db_id"];
    //     //Volumeチャート用データ作成
    //     volumeChartDataForEachLevel[tmpLevel] = volumeChartDataForEachLevel[tmpLevel] ? volumeChartDataForEachLevel[tmpLevel]+data[key]["volume"] : data[key]["volume"];
    //     hidVolumeChartDataForEachLevel[tmpLevel] = hidVolumeChartDataForEachLevel[tmpLevel] ? hidVolumeChartDataForEachLevel[tmpLevel]+','+element_db_id : element_db_id;
    //     //個数チャート用データ作成 (マテリアル/タイプ/ファミリ)
    //     materialChartData[tmpMaterial_name] = materialChartData[tmpMaterial_name] ? materialChartData[tmpMaterial_name]+1 : 1;
    //     hidMaterialChartData[tmpMaterial_name] = hidMaterialChartData[tmpMaterial_name] ? hidMaterialChartData[tmpMaterial_name]+','+element_db_id : element_db_id;
        
    //     familyNameChartData[tmpFamily_name] = familyNameChartData[tmpFamily_name] ? familyNameChartData[tmpFamily_name]+1 : 1;
    //     hidFamilyNameChartData[tmpFamily_name] = hidFamilyNameChartData[tmpFamily_name] ? hidFamilyNameChartData[tmpFamily_name]+','+element_db_id : element_db_id;
        
    //     typeNameChartData[tmpType_name] = typeNameChartData[tmpType_name] ? typeNameChartData[tmpType_name]+1 : 1;
    //     hidTypeNameChartData[tmpType_name] = hidTypeNameChartData[tmpType_name] ? hidTypeNameChartData[tmpType_name]+','+element_db_id : element_db_id;
    // });

    // chartData["Volume"]     = sortObjectValue(volumeChartDataForEachLevel, false);
    // chartData["Materials"]  = sortObjectValue(materialChartData, false);
    // chartData["TypeName"]   = sortObjectValue(typeNameChartData, false);
    // chartData["FamilyName"] = sortObjectValue(familyNameChartData, false);
    // chartData["hidVolumeElementIds"] = hidVolumeChartDataForEachLevel;
    // chartData["hidMaterialElementIds"] = hidMaterialChartData;
    // chartData["hidFamilyElementIds"] = hidFamilyNameChartData;
    // chartData["hidTypeElementIds"] = hidTypeNameChartData;
}

/**
 * 棒グラフを描画する。
 * @param  {object}  [in]chartData チャートデータ
 * @param  {string}  [in]title     タイトル
 * @param  {string}  [in]scale     単位
 * @return なし
 */
function DrawColumnChart(chartData, title, scale){
    console.log("DrawColumnChart start");
    
    var points =  [];
    var total= 0;
    
    Object.keys(chartData).forEach(function(key) {
        var intArea = parseFloat(chartData[key]);
        points.push([key,intArea]);
    });
    
    google.charts.load('current', {packages: ['corechart', 'bar']});
    google.charts.setOnLoadCallback(function(){columnChart(points, title, scale)});
}

/**
 * 棒グラフを描画処理を実行する。
 * @param  {object}  [in]chartData チャートデータ
 * @param  {string}  [in]title     タイトル
 * @param  {string}  [in]scale     単位
 * @return なし
 */
function columnChart(chartData, title, scale) {
    console.log("columnChart start");
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

    var chart = new google.visualization.ColumnChart(document.getElementById(title+"ColumnChartContainer"));
    //start chart select event
     function selectHandler() 
     {
       var selectedItem = chart.getSelection()[0];
       if (selectedItem) 
       {
         var selectedLevel = data.getValue(selectedItem.row, 0);
         var hidData = $("#hid"+title+"PieChartContainer").val();
 
         if(hidData != undefined){
                var hid_ids = JSON.parse(hidData); 
                var selected_ele_ids = hid_ids[selectedLevel];
                ViewerHighLight(selected_ele_ids);
         }
       }
     } 
     
    google.visualization.events.addListener(chart, 'select', selectHandler);  
    //end chart select event
    chart.draw(data, options);
}
