/* ajax通信トークン定義 */
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

/* Placeholder名称定義 */
var PLACEHOLDER_NAME_FOLDER     = "Select Folder";
var PLACEHOLDER_NAME_PROJECT    = "Select Project";
var PLACEHOLDER_NAME_VERSION    = "Select Versions";
var PLACEHOLDER_NAME_LEVEL      = "Select Level";
var PLACEHOLDER_NAME_WORKSET    = "Select Workset";
var PLACEHOLDER_NAME_CATEGORY   = "Select Category";
var PLACEHOLDER_NAME_MATERIAL   = "Select Material";
var PLACEHOLDER_NAME_FAMILY     = "Select Family";
var PLACEHOLDER_NAME_TYPE       = "Select Type";
var PLACEHOLDER_NAME_ENTER_TYPE = "タイプ名を入力";
var PLACEHOLDER_NAME_ADDRESS    = "住所";
var PLACEHOLDER_NAME_BRANCHSTORE= "支店";
var PLACEHOLDER_NAME_CONTYPE    = "工事区分";
var PLACEHOLDER_NAME_BUILDINGUSE= "建築用途";
var PLACEHOLDER_NAME_CON        = "構造";
var PLACEHOLDER_NAME_ORDERER    = "発注者";
var PLACEHOLDER_NAME_DESIGNER   = "設計 組織";
var PLACEHOLDER_NAME_RELATEDCOMP= "関係会社";

$(document).ready(function(){
    $.ajaxSetup({
        cache:false
    });
    
    var login_user_id = $("#hidLoginID").val();
    var img_src = "../public/image/JPG/原子力のフリーイラスト3.jpeg";
    var url = "dataPortal/projectSearchConsole";
    var content_name = "ﾓﾃﾞﾙ分析";
    recordAccessHistory(login_user_id,img_src,url,content_name);
    
    $("#prjAddress").select2({
        placeholder:"住所"
    });
    $("#branchStore").select2({
        placeholder:"支店"
    });
    $("#constructionType").select2({
        placeholder:"工事区分"
    });
    $("#buildingUse").select2({
        placeholder:"建築用途"
    });
    $("#construction").select2({
        placeholder:"構造"
    });
    $("#orderer").select2({
        placeholder:"発注者"
    });
    $("#designer").select2({
        placeholder:"設計 組織"
    });
    $("#relatedCompanies").select2({
        placeholder:"関係会社"
    });
    $("#project").select2({
        placeholder:"Folders Loading..."
    });
    $("#item").select2({
        placeholder:"Project Loading..."
    });
    $("#version").select2({
        placeholder:"Version Loading..."
    });

    $("#category").select2({
        placeholder:"Select Category"
    });

    $("#level").select2({
        placeholder:"Select Level"
    });
    $("#workset").select2({
        placeholder:"Select Workset"
    });
    $("#material").select2({
        placeholder:"Select Material"
    });
    $("#familyName").select2({
        placeholder:"Select Family"
    });
    $("#typeName").select2({
        placeholder:"Select Type"
    });
    
    LoadComboData();
    LoadImplementationDocInfo("ccc_project");//status for map region

    $("#prjAddress").change(function() {
        PrjSpecConditionsChange();
    });
    $("#branchStore").change(function() {
        PrjSpecConditionsChange();
    });
    $("#constructionType").change(function() {
        PrjSpecConditionsChange();
    });
    $("#buildingUse").change(function() {
        PrjSpecConditionsChange();
    });
    $("#construction").change(function() {
        PrjSpecConditionsChange();
    });
    $("#orderer").change(function() {
        PrjSpecConditionsChange();
    });
    $("#designer").change(function() {
        PrjSpecConditionsChange();
    });
    $("#relatedCompanies").change(function() {
        PrjSpecConditionsChange();
    });
    $("#project").change(function() {
        ProjectChange();
    });
    $("#item").change(function() {
        ItemChange();
    });
    $("#version").change(function() {
        VersionChange();
    });
    $("#level").change(function() {
        LevelChange();
    });
    $("#workset").change(function() {
        WorksetChange();
    });
    $("#material").change(function() {
        MaterialChange();
    });
    $("#familyName").change(function() {
        FamilyNameChange();
    });
    $("#typeName").change(function() {
        TypeNameChange();
    });
    
    RadioCheckedChange();
    
    // $("#project").parent().on('click', function () {
    //     console.log($("#project").next());
    // });
    
    
    // var target = $("#project").next();
    // console.log(target);
    // if(target.hasClass("select2-container--open")){
    //     console.log("project open");
        
    //     var tmpul = $("#select2-project-results").children();
    //     $.each(tmpul,function(key, value) {
    //         var current = $(this).closest("li");
    //         console.log(current);
    //         if(current.hasClass("select2-results__option--highlighted")){
    //             console.log("ssss");
    //         }
    //     })
    // }

    if (location.hash !== '') $('a[href="' + location.hash + '"]').tab('show');
        return $('a[data-toggle="tab"]').on('shown', function(e) {
        return location.hash = $(e.target).attr('href').substr(1);
    });
    
});

/**
 * Radio button checked change event。
 * @param なし
 * @return なし
 */
 function RadioCheckedChange(){
     $('input[name="rdo"]').change(function () {

        // value値取得
        var val = $(this).val();
        if(val == "rdoLimit"){
            LoadImplementationDocInfo("ccc_project");
        }else{
            LoadImplementationDocInfo("all_project");
        }
        

    });
 }
 

/**
 * セレクトボックス内の項目をLoadする。
 * @param なし
 * @return なし
 */
function LoadComboData(){
    DEBUGLOG("LoadComboData", "start", 0);
    
    $.ajax({
        url: "../forge/getData",
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"getComboData"},
        success :function(data) {
            if(data != null){
                BindComboData(data["projects"],"project",PLACEHOLDER_NAME_FOLDER);
                BindComboData(data["items"],"item",PLACEHOLDER_NAME_PROJECT);
                BindComboData(data["versions"],"version",PLACEHOLDER_NAME_VERSION);
            }
        },
        error:function(err){
            console.log(err);
        }
    });
}

/**
 * セレクトボックス内の項目をLoadする。
 * @param なし
 * @return なし
 */
function LoadImplementationDocInfo(status){
    DEBUGLOG("LoadImplementationDocInfo", "start", 0);
    //console.log("Khaing Test");
    
    var tmpProjectName = $('#project option:selected').text();
    var projectName = "";
    if(tmpProjectName !== ""){
        projectName = tmpProjectName.replace("【大阪】", "");
    }
    
    $.ajax({
		url:"../prjmgt/getData",
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"getImplementationDocComboData",name:"",status:status},
        success :function(data) {
            if(data == null){
                console.log("data is null");
                return;
            }
            
    		var results = JSON.parse(data);
            
    		var prjAddressList = [];
    		var project_info = [];
    		var buildingUseList = [];
    		var ordererList = [];
	   		var relatedCompanyList = [];
	   		
            var branchStoreList = [];
            var constructionTypeList = [];
            var constructionList = [];
            var designerList = [];

            $.each(results,function(key,value){
                if(value["address"] != "" && value["address"] != "undefined" && value["address"] != undefined){//a_sekou_basyo
                    prjAddressList.push(value["address"]);
                    project_info.push({"project_name":value["project_name"],"address":value["address"]});//a,b,c
                }
                if(value["building_use"] != "" && value["building_use"] != "undefined" && value["building_use"] != undefined){ buildingUseList.push(value["building_use"]); }//b_youto,a_youto1
                if(value["orderer"] != "" && value["orderer"] != "undefined" && value["orderer"] != undefined){ ordererList.push(value["orderer"]); }//b_hattyuusya
                
                if(value["branch_store"] != "" && value["branch_store"] != "undefined" && value["branch_store"] != undefined){ branchStoreList.push(value["branch_store"]); }
                if(value["construction_type"] != "" && value["construction_type"] != "undefined" && value["construction_type"] != undefined){ constructionTypeList.push(value["construction_type"]); }//a_kouji_kubun
                if(value["kouzou"] != "" && value["kouzou"] != "undefined" && value["kouzou"] != undefined){ constructionList.push(value["kouzou"]); }//b_kouzou,a_kouzou
                if(value["sekkeisya"] != "" && value["sekkeisya"] != "undefined" && value["sekkeisya"] != undefined){ designerList.push(value["sekkeisya"]); }//b_sekkeisya1,a_sekkei
                
                if(value["ken_org"] != "" && value["ken_org"] != "undefined" && value["ken_org"] != undefined){ relatedCompanyList.push(value["ken_org"]); }
                if(value["kou_org"] != "" && value["kou_org"] != "undefined" && value["kou_org"] != undefined){ relatedCompanyList.push(value["kou_org"]); }
                if(value["kou_org"] != "" && value["kou_org"] != "undefined" && value["sku_org"] != undefined){ relatedCompanyList.push(value["sku_org"]); }
                if(value["sde_org"] != "" && value["sde_org"] != "undefined" && value["sde_org"] != undefined){ relatedCompanyList.push(value["sde_org"]); }
                if(value["sek_org"] != "" && value["sek_org"] != "undefined" && value["sek_org"] != undefined){ relatedCompanyList.push(value["sek_org"]); }
                if(value["sei_org"] != "" && value["sei_org"] != "undefined" && value["sei_org"] != undefined){ relatedCompanyList.push(value["sei_org"]); }
                if(value["koj_org"] != "" && value["koj_org"] != "undefined" && value["koj_org"] != undefined){ relatedCompanyList.push(value["koj_org"]); }
                if(value["sgi_org"] != "" && value["sgi_org"] != "undefined" && value["sgi_org"] != undefined){ relatedCompanyList.push(value["sgi_org"]); }
                if(value["smi_org"] != "" && value["smi_org"] != "undefined" && value["smi_org"] != undefined){ relatedCompanyList.push(value["smi_org"]); }
                if(value["bmn_org"] != "" && value["bmn_org"] != "undefined" && value["bmn_org"] != undefined){ relatedCompanyList.push(value["bmn_org"]); }
                if(value["pds_org"] != "" && value["pds_org"] != "undefined" && value["pds_org"] != undefined){ relatedCompanyList.push(value["pds_org"]); }
                if(value["mdl_org"] != "" && value["mdl_org"] != "undefined" && value["mdl_org"] != undefined){ relatedCompanyList.push(value["mdl_org"]); }
                if(value["sbk_org"] != "" && value["sbk_org"] != "undefined" && value["sbk_org"] != undefined){ relatedCompanyList.push(value["sbk_org"]); }
                if(value["sbd_org"] != "" && value["sbd_org"] != "undefined" && value["sbd_org"] != undefined){ relatedCompanyList.push(value["sbd_org"]); }
                if(value["fsa_org"] != "" && value["fsa_org"] != "undefined" && value["fsa_org"] != undefined){ relatedCompanyList.push(value["fsa_org"]); }
                if(value["fse_org"] != "" && value["fse_org"] != "undefined" && value["fse_org"] != undefined){ relatedCompanyList.push(value["fse_org"]); }
            });
            var prjAddresss      = Array.from(new Set(prjAddressList));
            var branchStores     = Array.from(new Set(branchStoreList));
            var constructionTypes= Array.from(new Set(constructionTypeList));
            var buildingUses     = Array.from(new Set(buildingUseList));
            var constructions    = Array.from(new Set(constructionList));
            var orderers         = Array.from(new Set(ordererList));
            var designers        = Array.from(new Set(designerList));
            var relatedCompanies = Array.from(new Set(relatedCompanyList));

            BindComboData(prjAddresss,"prjAddress",PLACEHOLDER_NAME_ADDRESS);
            BindComboData(branchStores,"branchStore",PLACEHOLDER_NAME_BRANCHSTORE);
            BindComboData(constructionTypes,"constructionType",PLACEHOLDER_NAME_CONTYPE);
            BindComboData(buildingUses,"buildingUse",PLACEHOLDER_NAME_BUILDINGUSE);
            BindComboData(constructions,"construction",PLACEHOLDER_NAME_CON);
            BindComboData(orderers,"orderer",PLACEHOLDER_NAME_ORDERER);
            BindComboData(designers,"designer",PLACEHOLDER_NAME_DESIGNER);
            BindComboData(relatedCompanies,"relatedCompanies",PLACEHOLDER_NAME_RELATEDCOMP);
            
            LoadProjectRegions(project_info);
        },
        error:function(err){
            console.log(err);
        }
    });
}

function initializingMap() // call this method before you initialize your map.
{
    var container = L.DomUtil.get('project_regions');
    if(container != null){
        container._leaflet_id = null;
    }
}


var map = {};
function LoadProjectRegions(project_info){
    var isUseGeoChart = false;
    if(isUseGeoChart){
        google.charts.load('current', {'packages':['geochart'],
        // Note: you will need to get a mapsApiKey for your project.
        // See: https://developers.google.com/chart/interactive/docs/basic_load_libs#load-settings
        'mapsApiKey': 'AIzaSyD-9tSrke72PouQMnMX-a7eZSW0jkFMBWY'
        });
        google.charts.setOnLoadCallback(drawRegionsMap);
    }else{
        // console.log("project_info");
        // console.log(JSON.stringify(project_info));return;

        // var prjAddressList = [  {"project_name":"ダイビル本館","address":"大阪府大阪市北区中之島３丁目６"},
        //                         {"project_name":"キングパーツ","address":"広島県福山市御幸町８７９−１"}];
       //$("#project_regions").empty();
       initializingMap();
        map = L.map('project_regions').setView([34.140708, 133.942096], 7);  // 関西から九州バージョン
        // var map = L.map('project_regions').setView([34.713583, 135.371203], 10);    // 関西バージョン
        L.tileLayer('http://tile.openstreetmap.org/{z}/{x}/{y}.png',                                // OpenStreetMap:Standard
        // L.tileLayer('http://tile.thunderforest.com/cycle/{z}/{x}/{y}.png',                       // OpenStreetMap:CycleMap
        // L.tileLayer('http://tiles.wmflabs.org/bw-mapnik/{z}/{x}/{y}.png',                        // OpenStreetMap:Black and White
        // L.tileLayer('https://cyberjapandata.gsi.go.jp/xyz/std/{z}/{x}/{y}.png',                  // 国土地理院:標準地図
        // L.tileLayer('https://cyberjapandata.gsi.go.jp/xyz/blank/{z}/{x}/{y}.png',                // 国土地理院:白地図
        // L.tileLayer('https://cyberjapandata.gsi.go.jp/xyz/lum4bl_kinki2008/{z}/{x}/{y}.png',     // 国土地理院:数値地図5000（土地利用）（近畿圏 2008年）
        // L.tileLayer('https://cyberjapandata.gsi.go.jp/xyz/lum4bl_capital2005/{z}/{x}/{y}.png',   // 国土地理院:数値地図5000（土地利用）（首都圏 2005年）
        // L.tileLayer('https://cyberjapandata.gsi.go.jp/xyz/ccm1/{z}/{x}/{y}.png',                 // 国土地理院:沿岸海域土地条件図
        // L.tileLayer('https://cyberjapandata.gsi.go.jp/xyz/lcm25k/{z}/{x}/{y}.png',               // 国土地理院:土地条件図（初期整備版）
        // L.tileLayer('https://cyberjapandata.gsi.go.jp/xyz/lcm25k_2012/{z}/{x}/{y}.png',          // 国土地理院:数値地図25000（土地条件）
        // L.tileLayer('https://cyberjapandata.gsi.go.jp/xyz/pale/{z}/{x}/{y}.png',                 // 国土地理院:淡色地図
        // L.tileLayer('https://cyberjapandata.gsi.go.jp/xyz/seamlessphoto/{z}/{x}/{y}.jpg',        // 国土地理院:航空写真
        // L.tileLayer('https://cyberjapandata.gsi.go.jp/xyz/english/{z}/{x}/{y}.png',              // 国土地理院:標準地図（英語）
        // L.tileLayer('https://{s}.google.com/vt/lyrs=r&x={x}&y={y}&z={z}',                        // Google:GoogleStreet(表示できるが有料)
        // L.tileLayer('http://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png',                // CartoDB:12か月まで無料、それ以降有料
        {
            maxZoom: 20,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            subdomains:['mt0','mt1','mt2','mt3']
        }).addTo(map);
//setTimeout(function(){ map.invalidateSize()}, 400);
        console.log(project_info);
        map.options.singleClickTimeout = 250;
        for (var i=0; i<project_info.length; i++) {

            getLatLng(project_info[i].address,  settingLatlng(map, project_info[i].project_name));   // community-geocoder

            if((project_info[i].address).indexOf("大阪府泉南郡田尻町") !== -1){
                var popupStr = project_info[i].project_name;
                var marker = L.marker([34.4359642,135.2411557]).addTo(map).on('dblclick', function(e) { marker_dblclickEvt(e,popupStr); });
                var pdfName = GetPDF(popupStr);
                var jsonName = GetJSON(popupStr);
                marker.bindPopup(popupStr+'<br><a href="http://54.92.96.44/iPD/prjmgt/index" target="_blank" rel="noopener noreferrer" class="speciallink" onclick="viewGanttChart()">【BIM実行計画書】</a>'
                +'<a target="_blank" rel="noopener noreferrer" class="speciallink" onClick="setGanttSession(\''+popupStr+'\',\''+jsonName+'\')">【工程】</a>'
                +'<a href="javascript:void(0)"  rel="noopener noreferrer" class="speciallink" onclick="ShowPDF(\''+pdfName+'\')">【案件報告】</a>');//target="_blank" 
            }
        }
    }
}

function flyToMap(projectName){

    if(projectName === ""){
        // L.marker({icon: L.spriteIcon('red')}).addTo(map);
        map.flyTo([34.140708, 133.942096], 7, {duration:0.9});
    }else{
        //projectNameから住所を取得
    	$.ajax({
    		type:"POST",
    		url:"../prjmgt/getData",
    		data:{_token: CSRF_TOKEN,message:"getImplementationDocByProject",name:projectName},
    		success:function(data)
    		{
    			var results = JSON.parse(data);
    			var result = results[0];
    			var address = result["address"];
    
                //住所を緯度経度に変換
                if(address.indexOf("大阪府泉南郡田尻町") !== -1){
                    map.flyTo([34.4359642,135.2411557], 15, {duration:1.5});
                }else{
                    getLatLng(address,  flyToLatlng(map));
                }
    		},
            error:function(err){
                console.log(err);
            }
    	});
    }
}

function flyToLatlng(map){
    return function(latlng) {
        // L.marker({icon: L.spriteIcon('red')}).addTo(map);
        map.flyTo([latlng.lat,latlng.lng], 15, {duration:1.5});
    }
}

function settingLatlng(map, popupStr) {
    return function(latlng) {
        // console.log(latlng);
        // console.log(popupStr);
        var marker = L.marker([latlng.lat,latlng.lng]).addTo(map).on('dblclick', function(e) { marker_dblclickEvt(e,popupStr); });
        // marker.bindPopup("<p>"+popupStr+"</p>");
        // marker.bindPopup($('<a href="#" class="speciallink">TestLink</a>').click(function() {
        //     alert("test");
        // })[0]);
        // marker.bindPopup($("<a href='#'>"+popupStr+"</a><a href="#" href='#'>OK</a>").click(function() {;
        //     alert("test");
        // })[0]);
        var pdfName = GetPDF(popupStr);
        var jsonName = GetJSON(popupStr);
        if(jsonName === ""){
            marker.bindPopup(popupStr+'<br><a href="http://54.92.96.44/iPD/prjmgt/index" target="_blank" rel="noopener noreferrer" class="speciallink">【BIM実行計画書】</a>'
            +'<a target="_blank" rel="noopener noreferrer" class="speciallink" style="color:gray;" onClick="setGanttSession(\''+popupStr+'\',\''+jsonName+'\')">【工程】</a>'
            +'<a href="javascript:void(0)"  rel="noopener noreferrer" class="speciallink" onclick="ShowPDF(\''+pdfName+'\')">【案件報告】</a>');//target="_blank"
        }else{
            marker.bindPopup(popupStr+'<br><a href="http://54.92.96.44/iPD/prjmgt/index" target="_blank" rel="noopener noreferrer" class="speciallink">【BIM実行計画書】</a>'
            +'<a target="_blank" rel="noopener noreferrer" class="speciallink" onClick="setGanttSession(\''+popupStr+'\',\''+jsonName+'\')">【工程】</a>'
            +'<a href="javascript:void(0)"   rel="noopener noreferrer" class="speciallink" onclick="ShowPDF(\''+pdfName+'\')">【案件報告】</a>');//target="_blank"
        }
    }
}

function ShowPDF(pdfName){
    //window.location.href = '/iPD/pdf/'+pdfName;
    window.open('/iPD/pdf/'+pdfName,'pdfWindow');
}

function setGanttSession(projectName, jsonName){
    console.log("setGanttSession start");
    
    if(jsonName == ""){
        return;
    }
    
    projectName = projectName.trim();
    console.log("projectName:"+projectName);
    $.ajax({
          url: "../gantt/setProjectIdToSession",
          type: 'post',
          data:{_token: CSRF_TOKEN,projectId:"",projectName:projectName,projectCode:""},
          success :function(data) {
            if(data.includes("success")){
                window.open('/iPD/gantt/index',"_blank");
            }
          },
          error : function(err){
            console.log(err);
          }
    });
}

function GetPDF(projectName){

    if(projectName.includes("クレメントイン今治")){
        return "【四国】20200902_クレメント.pdf";
    }else if(projectName.includes("博多駅")){
        return "【九州】20200629_博多駅.pdf";
    }else if(projectName.includes("資生堂")){
        return "【九州】20200629_資生堂.pdf";
    }else if(projectName.includes("京都駅")){
        return "【大阪】20200630_京都駅.pdf";
    }else if(projectName.includes("宮原NK")){
        return "【大阪】20200630_宮原NK.pdf";
    }else if(projectName.includes("平野町")){
        return "【大阪】20200630_平野町.pdf";
    }else if(projectName.includes("関西国際空港")){
        return "【大阪】20200630_関空.pdf";
    }else if(projectName.includes("ミツトヨ")){
        return "NO DATAの画像.pdf";
    }else if(projectName.includes("うめきた")){
        return "【大阪】20201029_うめきた.pdf";
    }else if(projectName.includes("クボタ")){
        return "【大阪】20201029_クボタ.pdf";
    }else if(projectName.includes("新淀屋橋")){
        return "【大阪】20201029_日生淀屋橋.pdf";
    }else if(projectName.includes("淀屋橋駅西地区")){
        return "【大阪】20201029_淀屋橋日地区.pdf";
    }else if(projectName.includes("大阪駅西北")){
        return "【大阪】20201029_西北.pdf";
    }
    
    return"NO DATAの画像.pdf"
}

function GetJSON(projectName){
    var result = "";
    
    $.ajax({
        url: "../gantt/getData",
        async:false,
        type: 'post',
        data:{_token: CSRF_TOKEN,isTemp:0,pj_name:projectName,fileName:""},
        success :function(data) {
            if(data != null && data.length === 1){
                result = "index";
            }
        },
        error:function(err){
            console.log(err);
            alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });
    
    return result;
}

function viewGanttChart(){
    // alert("viewGanttChart");
}
function viewBimActionPlan(){
    // alert("viewBimActionPlan");
}


function marker_dblclickEvt(e,name){
    // プロジェクト名変換(.rvtファイル名になっている場合に"#cen_"や",rvt"をdeleteする。プロジェクト名一致率を上げるため)
    var prjName = name.replace("#cen_", "");
    prjName = prjName.replace("_2019", "");
    prjName = prjName.replace(".rvt", "");
    // alert(prjName);
    
    $.ajax({
        url: "../forge/getData",
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"getComboDataByProjectName",projectName:prjName},
        success :function(data) {
            if(data != null){
                var projects = data["projects"];

                let long_name = projects[0]["name"];
                let element = $('#project');
                let val = element.find("option:contains('"+long_name+"')").val();
                element.val(val).trigger('change.select2');

                ProjectChange();
            }
        },
        error:function(err){
            console.log(err);
        }
    });
}

function drawRegionsMap(){
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
    
    var options = { region: 'JP',
    				resolution: 'provinces',
    			  };
    
    var chart = new google.visualization.GeoChart(document.getElementById('project_regions'));
    chart.draw(data, options);
}

/**
 * セレクトボックス内の項目をバインドする。
 * @param  {object}  [in]data        元データ
 * @param  {string}  [in]comboId     セレクトボックスの識別ID
 * @param  {string}  [in]placeholder セレクトボックス内のplaceholder
 * @return なし
 */
function BindComboData(data,comboId,placeholder){
    DEBUGLOG("BindComboData", "start", 0);
    
    var appendText = "";
    $.each(data,function(key,value){
        
        if(comboId == "version"){
            value["name"] = value["name"].trim();
            var fileName = value["name"]+"("+value["version_number"]+")";
            appendText +="<option value='"+JSON.stringify(value)+"'>"+fileName+"</option>";
        }else if((comboId == "prjAddress")
                || (comboId == "branchStore")
                || (comboId == "constructionType")
                || (comboId == "buildingUse")
                || (comboId == "construction")
                || (comboId == "orderer")
                || (comboId == "designer")
                || (comboId == "relatedCompanies")
                ){
            appendText +="<option value='"+JSON.stringify(value)+"'>"+value+"</option>";
        }else{
            value["name"] = value["name"].trim();
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
 * プロジェクト指定条件(prjAddress/buildingUse/orderer/branchStore/constructionType/construction/designer)選択状態変更時に実行
 * セレクトボックス内の項目を絞り込む(選択したPrjAddressに対応するプロジェクトのみ)
 * 絞り込むセレクトボックス->#project,#item,#version,#buildingUse,#orderer,#branchStore,#constructionType,#construction,#designer
 * @param なし
 * @return なし
 */
function PrjSpecConditionsChange(){
    DEBUGLOG("PrjSpecConditionsChange", "start", 0);
    
    var totalBindProjectData = [];
    var totalBindItemData = [];
    var totalBindVersionData = [];
    var prjAddressList = [];
    var buildingUseList = [];
    var ordererList = [];
    // var relatedCompanyList = [];
    
    var branchStoreList = [];
    var constructionTypeList = [];
    var constructionList = [];
    var designerList = [];

    $('#prjAddress option:selected').each(function(){
        var tmpArrPrjAddress = JSON.parse($(this).val());
        prjAddressList.push(tmpArrPrjAddress);
    });

    $('#buildingUse option:selected').each(function(){
        var tmpArrBuildingUse = JSON.parse($(this).val());
        buildingUseList.push(tmpArrBuildingUse);
    });

    $('#orderer option:selected').each(function(){
        var tmpArrOrderer = JSON.parse($(this).val());
        ordererList.push(tmpArrOrderer);
    });

    // $('#relatedCompanies option:selected').each(function(){
    //     var tmpArrRelatedCompanies = JSON.parse($(this).val());
    //     relatedCompanyList.push(tmpArrRelatedCompanies);
    // });
    
    $('#branchStore option:selected').each(function(){
        var tmpArrBranchStore = JSON.parse($(this).val());
        branchStoreList.push(tmpArrBranchStore);
    });

    $('#constructionType option:selected').each(function(){
        var tmpArrConType = JSON.parse($(this).val());
        constructionTypeList.push(tmpArrConType);
    });

    $('#construction option:selected').each(function(){
        var tmpArrCon = JSON.parse($(this).val());
        constructionList.push(tmpArrCon);
    });

    $('#designer option:selected').each(function(){
        var tmpArrDesigner = JSON.parse($(this).val());
        designerList.push(tmpArrDesigner);
    });

    $.ajax({
        url:"../prjmgt/getData",
        async:true,
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"getProjectNameByImplementationDoc",prjAddressList:prjAddressList,
                buildingUseList:buildingUseList,ordererList:ordererList,
                branchStoreList:branchStoreList,constructionTypeList:constructionTypeList,
                constructionList:constructionList,designerList:designerList
        },
        success :function(data) {
            
            var projectNameList = [];
            
            if(data !== null){
                var results = JSON.parse(data);
                $.each(results,function(key,value){
                    if(value["project_name"] != ""){ projectNameList.push(value["project_name"]); }
                });
            }
          
            $.ajax({
                url: "../forge/getData",
                async:false,
                type: 'post',
                data:{_token: CSRF_TOKEN,message:"getComboDataByImplementationDocInfo",projectNameList:projectNameList
                },
                success :function(data) {
                    
                    if(data != null){
                        BindComboData(data["projects"],"project",PLACEHOLDER_NAME_FOLDER);
                        BindComboData(data["items"],"item",PLACEHOLDER_NAME_PROJECT);
                        BindComboData(data["versions"],"version",PLACEHOLDER_NAME_VERSION);
                    }
                },
                error:function(err){
                    console.log(err);
                }
            });
        },
        error:function(err){
            console.log(err);
        }
    });
}

/**
 * BuildingUse選択状態変更時に実行
 * セレクトボックス内の項目を絞り込む(選択したBuildingUseに対応するプロジェクトのみ)
 * 絞り込むセレクトボックス->#project,#item,#version,#prjAddress,#orderer,#relatedCompanies
 * @param なし
 * @return なし
 */
function BuildingUseChange(){
    DEBUGLOG("BuildingUseChange", "start", 0);
}

/**
 * Orderer選択状態変更時に実行
 * セレクトボックス内の項目を絞り込む(選択したOrdererに対応するプロジェクトのみ)
 * 絞り込むセレクトボックス->#project,#item,#version,#prjAddress,#buildingUse,#relatedCompanies
 * @param なし
 * @return なし
 */
function OrdererChange(){
    DEBUGLOG("OrdererChange", "start", 0);
}

/**
 * RelatedCompanies選択状態変更時に実行
 * セレクトボックス内の項目を絞り込む(選択したRelatedCompaniesに対応するプロジェクトのみ)
 * 絞り込むセレクトボックス->#project,#item,#version,#prjAddress,#buildingUse,#orderer
 * @param なし
 * @return なし
 */
function RelatedCompaniesChange(){
    DEBUGLOG("RelatedCompaniesChange", "start", 0);
}

/**
 * Project選択状態変更時に実行
 * セレクトボックス内の項目を絞り込む(選択した#projectに対応するもののみ)
 * 絞り込むセレクトボックス->#item,#version,#level,#workset,#material,#familyName,#typeName
 * @param なし
 * @return なし
 */
function ProjectChange(){
    DEBUGLOG("ProjectChange", "start", 0);

    var itemOption = "";
    var versionOption = "";
    var folderSelectedCount = $('#project option:selected').length;
    
    if(folderSelectedCount == 0) {
        
        LoadComboData();
        flyToMap("");
        
    }else if(folderSelectedCount == 1) {
        var projectName = $('#project option:selected').text();
        console.log(projectName);
        
        var projectCode = getPJCodeForProjectName(projectName);
       
        if(projectCode !== ""){
        // if(0){
            
            $.ajax({
                url: "../forge/getData",
                type: 'post',
                data:{_token: CSRF_TOKEN,message:"getComboDataByPjCode",projectCode:projectCode,itemName:""},
                success :function(data) {
                    // console.log("##PJコードあり#######################");
                    console.log(data);
                    if(data != null || data.length !== 0){
    
                        // console.log("##PJコード該当あり#######################");
                        var chkResult = checkItemName(data["items"]);
                        // console.log("chkResult");console.log(chkResult);
                        var authority_id = $("#hidAuthorityID").val();
                        // console.log("hidAuthorityID");console.log(authority_id);
    
                        if(chkResult["isOneModel"] === false){
                            // console.log("isOneModel is false");
                            
                            if(authority_id !== "1"){//if not admin
                                LoadComboData();
                                flyToMap("");
                                alert("中央モデルが複数あります。\nモデルデータを参照できません。");
                                return;
                            }else{
                                BindComboData(data["items"],"item",PLACEHOLDER_NAME_PROJECT);
                                BindComboData(data["versions"],"version",PLACEHOLDER_NAME_VERSION);
                                BindComboData(data["levels"],"level",PLACEHOLDER_NAME_LEVEL);
                                BindComboData(data["worksets"],"workset",PLACEHOLDER_NAME_WORKSET);
                                BindComboData(data["materials"],"material",PLACEHOLDER_NAME_MATERIAL);
                                BindComboData(data["familyNames"],"familyName",PLACEHOLDER_NAME_FAMILY);
                                BindComboData(data["typeNames"],"typeName",PLACEHOLDER_NAME_TYPE);
                                
                                $("#category").select2({placeholder:PLACEHOLDER_NAME_CATEGORY}).trigger('changed');
                            }
                        }else{
                            // console.log("isOneModel is not false");
                            
                            var items = chkResult["data"];
                            let long_name = items[0]["name"];
                            let element = $('#item');
                            let val = element.find("option:contains('"+long_name+"')").val();
                            element.val(val).trigger('change.select2');
            
                            ItemChangeTmp(chkResult);
                        }
                        
                        flyToMap(projectName);

    
                    }else{
                        
                        $.ajax({
                            url: "../forge/getData",
                            type: 'post',
                            data:{_token: CSRF_TOKEN,message:"getComboDataByProject",projectName:projectName,itemName:""},
                            success :function(data) {
                                // console.log("##PJコード該当なし->PJ名称取得##############");
                                console.log(data);
                                if(data != null){
                
                                    var chkResult = checkItemName(data["items"]);
                                    // console.log("chkResult");console.log(chkResult);
                                    var authority_id = $("#hidAuthorityID").val();
                                    // console.log("hidAuthorityID");console.log(authority_id);
                
                                    if(chkResult["isOneModel"] === false){
                                        
                                        if(authority_id !== "1"){
                                            LoadComboData();
                                            flyToMap("");
                                            alert("中央モデルが複数あります。\nモデルデータを参照できません。");
                                            return;
                                        }else{
                                            BindComboData(data["items"],"item",PLACEHOLDER_NAME_PROJECT);
                                            BindComboData(data["versions"],"version",PLACEHOLDER_NAME_VERSION);
                                            BindComboData(data["levels"],"level",PLACEHOLDER_NAME_LEVEL);
                                            BindComboData(data["worksets"],"workset",PLACEHOLDER_NAME_WORKSET);
                                            BindComboData(data["materials"],"material",PLACEHOLDER_NAME_MATERIAL);
                                            BindComboData(data["familyNames"],"familyName",PLACEHOLDER_NAME_FAMILY);
                                            BindComboData(data["typeNames"],"typeName",PLACEHOLDER_NAME_TYPE);
                                            
                                            $("#category").select2({placeholder:PLACEHOLDER_NAME_CATEGORY}).trigger('changed');
                                        }
                                    }else{
                                        var items = chkResult["data"];
                                        let long_name = items[0]["name"];
                                        let element = $('#item');
                                        let val = element.find("option:contains('"+long_name+"')").val();
                                        element.val(val).trigger('change.select2');
                        
                                        ItemChangeTmp(chkResult);
                                    }
                                    
                                    flyToMap(projectName);
                                    
                                }
                            },
                            error:function(err){
                                console.log(err);
                            }
                        });
                        
                    }
                },
                error:function(err){
                    console.log(err);
                }
            });


        }else{
            
            $.ajax({
                url: "../forge/getData",
                type: 'post',
                data:{_token: CSRF_TOKEN,message:"getComboDataByProject",projectName:projectName,itemName:""},
                success :function(data) {
                    console.log("#########################");
                    console.log(data);
                    if(data != null){
    
                        var chkResult = checkItemName(data["items"]);
                        // console.log("chkResult");console.log(chkResult);
                        var authority_id = $("#hidAuthorityID").val();
                        // console.log("hidAuthorityID");console.log(authority_id);
    
                        if(chkResult["isOneModel"] === false){
                            
                            if(authority_id !== "1"){
                                LoadComboData();
                                flyToMap("");
                                alert("中央モデルが複数あります。\nモデルデータを参照できません。");
                                return;
                            }else{
                                BindComboData(data["items"],"item",PLACEHOLDER_NAME_PROJECT);
                                BindComboData(data["versions"],"version",PLACEHOLDER_NAME_VERSION);
                                BindComboData(data["levels"],"level",PLACEHOLDER_NAME_LEVEL);
                                BindComboData(data["worksets"],"workset",PLACEHOLDER_NAME_WORKSET);
                                BindComboData(data["materials"],"material",PLACEHOLDER_NAME_MATERIAL);
                                BindComboData(data["familyNames"],"familyName",PLACEHOLDER_NAME_FAMILY);
                                BindComboData(data["typeNames"],"typeName",PLACEHOLDER_NAME_TYPE);
                                
                                $("#category").select2({placeholder:PLACEHOLDER_NAME_CATEGORY}).trigger('changed');
                            }
                        }else{
                            var items = chkResult["data"];
                            let long_name = items[0]["name"];
                            let element = $('#item');
                            let val = element.find("option:contains('"+long_name+"')").val();
                            element.val(val).trigger('change.select2');
            
                            ItemChangeTmp(chkResult);
                        }
                        
                        flyToMap(projectName);
                    }
                },
                error:function(err){
                    console.log(err);
                }
            });
        }
        
    }else if(folderSelectedCount > 1){
        
        var totalBindItemData = [];
        var totalBindVersionData = [];
    	var totalBindLevelData = [];
        var totalBindWorkSetData = [];
        var totalBindMaterialData = [];
        var totalBindFamilyNameData = [];
        var totalBindTypeNameData = [];
        var tmpPrePrjName = "";
        var ajaxCnt = 0;
        
        flyToMap("");
        
        $('#project option:selected').each(function(){
            var tmpStr = (tmpPrePrjName != "") ? tmpPrePrjName + " " : "";
            var projectName = $(this).text();
            projectName.replace(tmpStr, "");
    
            $.ajax({
                url: "../forge/getData",
                type: 'post',
                data:{_token: CSRF_TOKEN,message:"getComboDataByProject",projectName:projectName,itemName:""},
                success :function(data) {
                    // console.log(data);
                    if(data != null){
                        totalBindItemData.push(data["items"]);
                        totalBindVersionData.push(data["versions"]);
                        totalBindLevelData.push(data["levels"]);
                        totalBindWorkSetData.push(data["worksets"]);
                        totalBindMaterialData.push(data["materials"]);
                        totalBindFamilyNameData.push(data["familyNames"]);
                        totalBindTypeNameData.push(data["typeNames"]);

                        ajaxCnt++;
                        if(ajaxCnt == folderSelectedCount){
                            // 重複排除
                            var totalBindItems       = Array.from(new Set(totalBindItemData));
                            var totalBindVersions    = Array.from(new Set(totalBindVersionData));
                            var totalBindLevels      = Array.from(new Set(totalBindLevelData));
                            var totalBindWorkSets    = Array.from(new Set(totalBindWorkSetData));
                            var totalBindMaterials   = Array.from(new Set(totalBindMaterialData));
                            var totalBindFamilyNames = Array.from(new Set(totalBindFamilyNameData));
                            var totalBindTypeNames   = Array.from(new Set(totalBindTypeNameData));
                            
                            BindComboMultiData(totalBindItems,"item",PLACEHOLDER_NAME_PROJECT);
                            BindComboMultiData(totalBindVersions,"version",PLACEHOLDER_NAME_VERSION);
                            BindComboMultiData(totalBindLevels,"level",PLACEHOLDER_NAME_LEVEL);
                            BindComboMultiData(totalBindWorkSets,"workset",PLACEHOLDER_NAME_WORKSET);
                            BindComboMultiData(totalBindMaterials,"material",PLACEHOLDER_NAME_MATERIAL);
                            BindComboMultiData(totalBindFamilyNames,"familyName",PLACEHOLDER_NAME_FAMILY);
                            BindComboMultiData(totalBindTypeNames,"typeName",PLACEHOLDER_NAME_TYPE);
                        }
                    }
                },
                error:function(err){
                    console.log(err);
                }
            });
            
            tmpPrePrjName = $(this).text();
        });
        
    }else{
        //NOP
    }
}

function getPJCodeForProjectName(projectName){
    
    var ret = "";
    
    if(projectName.indexOf("PJ") === 0){
        var tmpAry = projectName.split("_");
        ret = tmpAry[0];
    }
    
    return ret;
}

function checkItemName(items){
    
    var result = {"isOneModel":false, "data":[]};
    var dataNum = items.length;
    
    if(dataNum === 0){
        result["isOneModel"] = false;
    }else if(dataNum === 1){
        var modelName = items[0]["name"];
        if(modelName.indexOf("#cen") === 0 || modelName.indexOf("cen") === 0){
            result["isOneModel"] = true;
            result["data"] = items;
        }
    }else{
        var modelNameList = [];
        var tmpItems = [];
        for(var i=0; i < dataNum; i++){
            var modelName = items[i]["name"];
            if(modelName.indexOf("#cen") === 0 || modelName.indexOf("cen") === 0){
                modelNameList.push(items[i]);
            }
        }
        
        if(modelNameList.length === 1){
            result["isOneModel"] = true;
            result["data"] = modelNameList;
        }
    }
    
    return result;
}

/**
 * Item選択状態変更時に実施する処理
 * セレクトボックス内の項目を絞り込む(選択した#itemに対応するもののみ)
 * 絞り込むセレクトボックス->#version
 * @param なし
 * @return なし
 */
function ItemChange(){
    DEBUGLOG("ItemChange", "start", 0);

    var totalBindVersionData = [];
    var projectName = "";
    var ajaxCnt = 0;
    var prjSelectedCount = $('#item option:selected').length;
    if(prjSelectedCount == 1){//my added source code for lated version selecting
        $('#item option:selected').each(function(){
            var tmpArrItem = JSON.parse($(this).val());
            var item_proj_id = tmpArrItem.project_id;
            var itemName = tmpArrItem.name;
    
            $('#project option:selected').each(function(){
                var tmpArrProj = JSON.parse($(this).val());
                if(item_proj_id == tmpArrProj.id){
                    projectName = tmpArrProj.name;
                }
            });
    
            $.ajax({
                url: "../forge/getData",
                type: 'post',
                data:{_token: CSRF_TOKEN,message:"getComboDataByProject",projectName:projectName,itemName:itemName},
                success :function(data) {
                    if(data != null){
                        totalBindVersionData.push(data["versions"]);
                        
                        ajaxCnt++;
                        if(ajaxCnt == prjSelectedCount){
                            BindComboMultiData(totalBindVersionData,"version",PLACEHOLDER_NAME_VERSION);
                            if($("#chkLatedVersion").prop("checked") == true){
                                $('#version').val($('#version option:eq(0)').val()).trigger('change');
                                //console.log("===================my log==============");
                                //console.log(totalBindVersionData);
                            }
                            
                        }
                    }
                    },
                    error:function(err){
                        console.log(err);
                    }
                });
        });
    }else{//original source code
        $('#item option:selected').each(function(){
            var tmpArrItem = JSON.parse($(this).val());
            var item_proj_id = tmpArrItem.project_id;
            var itemName = tmpArrItem.name;
    
            $('#project option:selected').each(function(){
                var tmpArrProj = JSON.parse($(this).val());
                if(item_proj_id == tmpArrProj.id){
                    projectName = tmpArrProj.name;
                }
            });
    
            $.ajax({
                url: "../forge/getData",
                type: 'post',
                data:{_token: CSRF_TOKEN,message:"getComboDataByProject",projectName:projectName,itemName:itemName},
                success :function(data) {
                    if(data != null){
                        totalBindVersionData.push(data["versions"]);
                        
                        ajaxCnt++;
                        if(ajaxCnt == prjSelectedCount){
                            BindComboMultiData(totalBindVersionData,"version",PLACEHOLDER_NAME_VERSION);
                        }
                    }
                    },
                    error:function(err){
                        console.log(err);
                    }
                });
        });
    }
    
    
    
}

function ItemChangeTmp(chkResult){
    DEBUGLOG("ItemChangeTmp", "start", 0);

    var totalBindVersionData = [];
    var projectName = "";
    var ajaxCnt = 0;
    var prjSelectedCount = $('#item option:selected').length;
    
    $('#item option:selected').each(function(){
        var tmpArrItem = JSON.parse($(this).val());
        var item_proj_id = tmpArrItem.project_id;
        var itemName = tmpArrItem.name;

        $('#project option:selected').each(function(){
            var tmpArrProj = JSON.parse($(this).val());
            if(item_proj_id == tmpArrProj.id){
                projectName = tmpArrProj.name;
            }
        });

        $.ajax({
            url: "../forge/getData",
            type: 'post',
            data:{_token: CSRF_TOKEN,message:"getComboDataByProject",projectName:projectName,itemName:itemName},
            success :function(data) {
                if(data != null){
                    totalBindVersionData.push(data["versions"]);
                    
                    ajaxCnt++;
                    if(ajaxCnt == prjSelectedCount){
                        BindComboMultiData(totalBindVersionData,"version",PLACEHOLDER_NAME_VERSION);
                        
                        //最新バージョン取得
                        //バージョンのコンボボックスを最新バージョン選択状態に変更
                        var items = chkResult["data"];
                        var long_name = items[0]["name"];
                        var element = $('#version');
                        var val = element.find("option:first").val();
                        element.val(val).trigger('change.select2');
                        
                        VersionChange();

                    }
                }
            },
            error:function(err){
                console.log(err);
            }
        });
    });
    
}


/**
 * Version選択状態変更時に実施する処理
 * セレクトボックス内の項目を絞り込む(選択した#versionに対応するもののみ)
 * 絞り込むセレクトボックス->#level,#workset,#material,#familyName,#typeName
 * @param なし
 * @return なし
 */
function VersionChange(){
    DEBUGLOG("VersionChange", "start", 0);

	var totalBindLevelData = [];
    var totalBindWorkSetData = [];
    var totalBindMaterialData = [];
    var totalBindFamilyNameData = [];
    var totalBindTypeNameData = [];
    var projectName = "";
    var ajaxCnt= 0;
    var versionSelectedCount = $('#version option:selected').length;
    
    $('#version option:selected').each(function(){
        var item_proj_id = 0;
        var tmpArrVersion = JSON.parse($(this).val());
        var version_item_id = tmpArrVersion.item_id;
        var versionNum = tmpArrVersion.version_number;
        
        $('#item option:selected').each(function(){
            var tmpArrItem = JSON.parse($(this).val());
            if(version_item_id == tmpArrItem.id){
                item_proj_id = tmpArrItem.project_id;
            }
        });
        $('#project option:selected').each(function(){
            var tmpArrProj = JSON.parse($(this).val());
            if(item_proj_id == tmpArrProj.id){
                projectName = tmpArrProj.name;
            }
        });
        
        $.ajax({
            url: "../forge/getData",
            type: 'post',
            data:{_token: CSRF_TOKEN,message:"getComboDataByVersion",projectName:projectName,versionNum:versionNum},
            success :function(data) {
                if(data != null){
	                totalBindLevelData.push(data["levels"]);
                    totalBindWorkSetData.push(data["worksets"]);
                    totalBindMaterialData.push(data["materials"]);
                    totalBindFamilyNameData.push(data["familyNames"]);
                    totalBindTypeNameData.push(data["typeNames"]);
                    
                    ajaxCnt++;
                    if(ajaxCnt == versionSelectedCount){
                        // console.log(JSON.stringify(totalBindLevelData));
                        // console.log(JSON.stringify(totalBindWorkSetData));
                        // console.log(JSON.stringify(totalBindMaterialData));
                        // console.log(JSON.stringify(totalBindFamilyNameData));
                        // console.log(JSON.stringify(totalBindTypeNameData));
                        BindComboMultiData(totalBindLevelData,"level",PLACEHOLDER_NAME_LEVEL);
                        BindComboMultiData(totalBindWorkSetData,"workset",PLACEHOLDER_NAME_WORKSET);
                        BindComboMultiData(totalBindMaterialData,"material",PLACEHOLDER_NAME_MATERIAL);
                        BindComboMultiData(totalBindFamilyNameData,"familyName",PLACEHOLDER_NAME_FAMILY);
                        BindComboMultiData(totalBindTypeNameData,"typeName",PLACEHOLDER_NAME_TYPE);
                    }
                }
            },
            error:function(err){
                console.log(err);
            }
        });
    });
    
}

/**
 * Level選択状態変更時に実施する処理
 * セレクトボックス内の項目を絞り込む(選択した#levelに対応するもののみ)
 * 絞り込むセレクトボックス->#workset,#material,#familyName,#typeName
 * @param なし
 * @return なし
 */
function LevelChange(){
    DEBUGLOG("LevelChange", "start", 0);

    var totalBindWorkSetData = [];
    var totalBindMaterialData = [];
    var totalBindFamilyNameData = [];
    var totalBindTypeNameData = [];
    var projectName = "";
    var levelList = [];
    var worksetList = [];
    var materialList = [];
    var familyNameList = [];
    var typeNameList = [];
    var ajaxCnt = 0;
    var versionSelectedCount = $('#version option:selected').length;
    
    $('#version option:selected').each(function(){
        var item_proj_id = 0;
        var tmpArrVersion = JSON.parse($(this).val());
        var version_item_id = tmpArrVersion.item_id;
        var versionNum = tmpArrVersion.version_number;

        $('#item option:selected').each(function(){
            var tmpArrItem = JSON.parse($(this).val());
            if(version_item_id == tmpArrItem.id){
                item_proj_id = tmpArrItem.project_id;
            }
        });
        $('#project option:selected').each(function(){
            var tmpArrProj = JSON.parse($(this).val());
            if(item_proj_id == tmpArrProj.id){
                projectName = tmpArrProj.name;
            }
        });
        $('#level option:selected').each(function(){
            var tmpArrLevel = JSON.parse($(this).val());
            levelList.push(tmpArrLevel.name);
        });
        $('#workset option:selected').each(function(){
            var tmpArrWorkset = JSON.parse($(this).val());
            worksetList.push(tmpArrWorkset.name);
        });
        $('#material option:selected').each(function(){
            var tmpArrMaterial = JSON.parse($(this).val());
            materialList.push(tmpArrMaterial.name);
        });
        $('#familyName option:selected').each(function(){
            var tmpArrFamilyName = JSON.parse($(this).val());
            familyNameList.push(tmpArrFamilyName.name);
        });
        $('#typeName option:selected').each(function(){
            var tmpArrTypeName = JSON.parse($(this).val());
            typeNameList.push(tmpArrTypeName.name);
        });
        
        $.ajax({
            url: "../forge/getData",
            type: 'post',
            data:{_token: CSRF_TOKEN,message:"getComboDataByLevel",projectName:projectName,versionNum:versionNum,
                    levelList:levelList,worksetList:worksetList,materialList:materialList,familyNameList:familyNameList,typeNameList:typeNameList,
            },
            success :function(data) {
                // console.log(data);
                if(data != null){
                    totalBindWorkSetData.push(data["worksets"]);
                    totalBindMaterialData.push(data["materials"]);
                    totalBindFamilyNameData.push(data["familyNames"]);
                    totalBindTypeNameData.push(data["typeNames"]);
                    
                    ajaxCnt++;
                    if(ajaxCnt == versionSelectedCount){
                        // console.log("totalBindWorkSetData");console.log(JSON.stringify(totalBindWorkSetData));
                        // console.log("totalBindMaterialData");console.log(JSON.stringify(totalBindMaterialData));
                        // console.log("totalBindFamilyNameData");console.log(JSON.stringify(totalBindFamilyNameData));
                        // console.log("totalBindTypeNameData");console.log(JSON.stringify(totalBindTypeNameData));
                        if (worksetList.length === 0)	{ BindComboMultiData(totalBindWorkSetData,"workset",PLACEHOLDER_NAME_WORKSET); }
                        if (materialList.length === 0)	{ BindComboMultiData(totalBindMaterialData,"material",PLACEHOLDER_NAME_MATERIAL); }
                        if (familyNameList.length === 0){ BindComboMultiData(totalBindFamilyNameData,"familyName",PLACEHOLDER_NAME_FAMILY); }
                        if (typeNameList.length === 0)	{ BindComboMultiData(totalBindTypeNameData,"typeName",PLACEHOLDER_NAME_TYPE); }
                    }
                }
            },
            error:function(err){
                console.log(err);
            }
        });
    });
    
}

/**
 * Workset選択状態変更時に実施する処理
 * セレクトボックス内の項目を絞り込む(選択した#worksetに対応するもののみ)
 * 絞り込むセレクトボックス->#level,#material,#familyName,#typeName
 * @param なし
 * @return なし
 */
function WorksetChange(){
    DEBUGLOG("WorksetChange", "start", 0);

    var totalBindLevelData = [];
    var totalBindMaterialData = [];
    var totalBindFamilyNameData = [];
    var totalBindTypeNameData = [];
    var projectName = "";
    var levelList = [];
    var worksetList = [];
    var materialList = [];
    var familyNameList = [];
    var typeNameList = [];
    var ajaxCnt = 0;
    var versionSelectedCount = $('#version option:selected').length;
    
    $('#version option:selected').each(function(){
        var item_proj_id = 0;
        var tmpArrVersion = JSON.parse($(this).val());
        var version_item_id = tmpArrVersion.item_id;
        var versionNum = tmpArrVersion.version_number;

        $('#item option:selected').each(function(){
            var tmpArrItem = JSON.parse($(this).val());
            if(version_item_id == tmpArrItem.id){
                item_proj_id = tmpArrItem.project_id;
            }
        });
        $('#project option:selected').each(function(){
            var tmpArrProj = JSON.parse($(this).val());
            if(item_proj_id == tmpArrProj.id){
                projectName = tmpArrProj.name;
            }
        });
        $('#level option:selected').each(function(){
            var tmpArrLevel = JSON.parse($(this).val());
            levelList.push(tmpArrLevel.name);
        });
        $('#workset option:selected').each(function(){
            var tmpArrWorkset = JSON.parse($(this).val());
            worksetList.push(tmpArrWorkset.name);
        });
        $('#material option:selected').each(function(){
            var tmpArrMaterial = JSON.parse($(this).val());
            materialList.push(tmpArrMaterial.name);
        });
        $('#familyName option:selected').each(function(){
            var tmpArrFamilyName = JSON.parse($(this).val());
            familyNameList.push(tmpArrFamilyName.name);
        });
        $('#typeName option:selected').each(function(){
            var tmpArrTypeName = JSON.parse($(this).val());
            typeNameList.push(tmpArrTypeName.name);
        });
        
        $.ajax({
            url: "../forge/getData",
            type: 'post',
            data:{_token: CSRF_TOKEN,message:"getComboDataByWorkset",projectName:projectName,versionNum:versionNum,
                    levelList:levelList,worksetList:worksetList,materialList:materialList,familyNameList:familyNameList,typeNameList:typeNameList,
            },
            success :function(data) {
                // console.log(data);
                if(data != null){
                    totalBindLevelData.push(data["levels"]);
                    totalBindMaterialData.push(data["materials"]);
                    totalBindFamilyNameData.push(data["familyNames"]);
                    totalBindTypeNameData.push(data["typeNames"]);
                    
                    ajaxCnt++;
                    if(ajaxCnt == versionSelectedCount){
                        // console.log("totalBindLevelData");console.log(JSON.stringify(totalBindLevelData));
                        // console.log("totalBindMaterialData");console.log(JSON.stringify(totalBindMaterialData));
                        // console.log("totalBindFamilyNameData");console.log(JSON.stringify(totalBindFamilyNameData));
                        // console.log("totalBindTypeNameData");console.log(JSON.stringify(totalBindTypeNameData));
                        if (levelList.length === 0)     { BindComboMultiData(totalBindLevelData,"level",PLACEHOLDER_NAME_LEVEL); }
                        if (materialList.length === 0)  { BindComboMultiData(totalBindMaterialData,"material",PLACEHOLDER_NAME_MATERIAL); }
                        if (familyNameList.length === 0){ BindComboMultiData(totalBindFamilyNameData,"familyName",PLACEHOLDER_NAME_FAMILY); }
                        if (typeNameList.length === 0)  { BindComboMultiData(totalBindTypeNameData,"typeName",PLACEHOLDER_NAME_TYPE); }
                    }
                }
            },
            error:function(err){
                console.log(err);
            }
        });
    });
    
}

/**
 * Material選択状態変更時に実施する処理
 * セレクトボックス内の項目を絞り込む(選択した#materialに対応するもののみ)
 * 絞り込むセレクトボックス->#level,#workset,#familyName,#typeName
 * @param なし
 * @return なし
 */
function MaterialChange(){
    DEBUGLOG("MaterialChange", "start", 0);

    var totalBindLevelData = [];
    var totalBindWorkSetData = [];
    var totalBindFamilyNameData = [];
    var totalBindTypeNameData = [];
    var projectName = "";
    var levelList = [];
    var worksetList = [];
    var materialList = [];
    var familyNameList = [];
    var typeNameList = [];
    var ajaxCnt = 0;
    var versionSelectedCount = $('#version option:selected').length;
    
    $('#version option:selected').each(function(){
        var item_proj_id = 0;
        var tmpArrVersion = JSON.parse($(this).val());
        var version_item_id = tmpArrVersion.item_id;
        var versionNum = tmpArrVersion.version_number;

        $('#item option:selected').each(function(){
            var tmpArrItem = JSON.parse($(this).val());
            if(version_item_id == tmpArrItem.id){
                item_proj_id = tmpArrItem.project_id;
            }
        });
        $('#project option:selected').each(function(){
            var tmpArrProj = JSON.parse($(this).val());
            if(item_proj_id == tmpArrProj.id){
                projectName = tmpArrProj.name;
            }
        });
        $('#level option:selected').each(function(){
            var tmpArrLevel = JSON.parse($(this).val());
            levelList.push(tmpArrLevel.name);
        });
        $('#workset option:selected').each(function(){
            var tmpArrWorkset = JSON.parse($(this).val());
            worksetList.push(tmpArrWorkset.name);
        });
        $('#material option:selected').each(function(){
            var tmpArrMaterial = JSON.parse($(this).val());
            materialList.push(tmpArrMaterial.name);
        });
        $('#familyName option:selected').each(function(){
            var tmpArrFamilyName = JSON.parse($(this).val());
            familyNameList.push(tmpArrFamilyName.name);
        });
        $('#typeName option:selected').each(function(){
            var tmpArrTypeName = JSON.parse($(this).val());
            typeNameList.push(tmpArrTypeName.name);
        });
        
        $.ajax({
            url: "../forge/getData",
            type: 'post',
            data:{_token: CSRF_TOKEN,message:"getComboDataByMaterial",projectName:projectName,versionNum:versionNum,
                    levelList:levelList,worksetList:worksetList,materialList:materialList,familyNameList:familyNameList,typeNameList:typeNameList,
            },
            success :function(data) {
                if(data != null){
                    totalBindLevelData.push(data["levels"]);
                    totalBindWorkSetData.push(data["worksets"]);
                    totalBindFamilyNameData.push(data["familyNames"]);
                    totalBindTypeNameData.push(data["typeNames"]);
                    
                    ajaxCnt++;
                    if(ajaxCnt == versionSelectedCount){
                        // console.log("totalBindLevelData");console.log(JSON.stringify(totalBindLevelData));
                        // console.log("totalBindWorkSetData");console.log(JSON.stringify(totalBindWorkSetData));
                        // console.log("totalBindFamilyNameData");console.log(JSON.stringify(totalBindFamilyNameData));
                        // console.log("totalBindTypeNameData");console.log(JSON.stringify(totalBindTypeNameData));
                        if (levelList.length === 0)     { BindComboMultiData(totalBindLevelData,"level",PLACEHOLDER_NAME_LEVEL); }
                        if (worksetList.length === 0)   { BindComboMultiData(totalBindWorkSetData,"workset",PLACEHOLDER_NAME_WORKSET); }
                        if (familyNameList.length === 0){ BindComboMultiData(totalBindFamilyNameData,"familyName",PLACEHOLDER_NAME_FAMILY); }
                        if (typeNameList.length === 0)  { BindComboMultiData(totalBindTypeNameData,"typeName",PLACEHOLDER_NAME_TYPE); }
                    }
                }
            },
            error:function(err){
                console.log(err);
            }
        });
    });
    
}

/**
 * FamilyName選択状態変更時に実施する処理
 * セレクトボックス内の項目を絞り込む(選択した#familyNameに対応するもののみ)
 * 絞り込むセレクトボックス->#level,#workset,#material,##typeName
 * @param なし
 * @return なし
 */
function FamilyNameChange(){
    DEBUGLOG("FamilyNameChange", "start", 0);

    var totalBindLevelData = [];
    var totalBindWorkSetData = [];
    var totalBindMaterialData = [];
    var totalBindTypeNameData = [];
    var projectName = "";
    var levelList = [];
    var worksetList = [];
    var materialList = [];
    var familyNameList = [];
    var typeNameList = [];
    var ajaxCnt = 0;
    var versionSelectedCount = $('#version option:selected').length;
    
    $('#version option:selected').each(function(){
        var item_proj_id = 0;
        var tmpArrVersion = JSON.parse($(this).val());
        var version_item_id = tmpArrVersion.item_id;
        var versionNum = tmpArrVersion.version_number;

        $('#item option:selected').each(function(){
            var tmpArrItem = JSON.parse($(this).val());
            if(version_item_id == tmpArrItem.id){
                item_proj_id = tmpArrItem.project_id;
            }
        });
        $('#project option:selected').each(function(){
            var tmpArrProj = JSON.parse($(this).val());
            if(item_proj_id == tmpArrProj.id){
                projectName = tmpArrProj.name;
            }
        });
        $('#level option:selected').each(function(){
            var tmpArrLevel = JSON.parse($(this).val());
            levelList.push(tmpArrLevel.name);
        });
        $('#workset option:selected').each(function(){
            var tmpArrWorkset = JSON.parse($(this).val());
            worksetList.push(tmpArrWorkset.name);
        });
        $('#material option:selected').each(function(){
            var tmpArrMaterial = JSON.parse($(this).val());
            materialList.push(tmpArrMaterial.name);
        });
        $('#familyName option:selected').each(function(){
            var tmpArrFamilyName = JSON.parse($(this).val());
            familyNameList.push(tmpArrFamilyName.name);
        });
        $('#typeName option:selected').each(function(){
            var tmpArrTypeName = JSON.parse($(this).val());
            typeNameList.push(tmpArrTypeName.name);
        });
        
        $.ajax({
            url: "../forge/getData",
            type: 'post',
            data:{_token: CSRF_TOKEN,message:"getComboDataByFamilyName",projectName:projectName,versionNum:versionNum,
                    levelList:levelList,worksetList:worksetList,materialList:materialList,familyNameList:familyNameList,typeNameList:typeNameList,
            },
            success :function(data) {
                if(data != null){
                    totalBindLevelData.push(data["levels"]);
                    totalBindWorkSetData.push(data["worksets"]);
                    totalBindMaterialData.push(data["materials"]);
                    totalBindTypeNameData.push(data["typeNames"]);
                    
                    ajaxCnt++;
                    if(ajaxCnt == versionSelectedCount){
                        // console.log("totalBindLevelData");console.log(JSON.stringify(totalBindLevelData));
                        // console.log("totalBindWorkSetData");console.log(JSON.stringify(totalBindWorkSetData));
                        // console.log("totalBindMaterialData");console.log(JSON.stringify(totalBindMaterialData));
                        // console.log("totalBindTypeNameData");console.log(JSON.stringify(totalBindTypeNameData));
                        if (levelList.length === 0)		{ BindComboMultiData(totalBindLevelData,"level",PLACEHOLDER_NAME_LEVEL); }
                        if (worksetList.length === 0)	{ BindComboMultiData(totalBindWorkSetData,"workset",PLACEHOLDER_NAME_WORKSET); }
                        if (materialList.length === 0)  { BindComboMultiData(totalBindMaterialData,"material",PLACEHOLDER_NAME_MATERIAL); }
                        if (typeNameList.length === 0)	{ BindComboMultiData(totalBindTypeNameData,"typeName",PLACEHOLDER_NAME_TYPE); }
                    }
                }
            },
            error:function(err){
                console.log(err);
            }
        });
    });
    
}

/**
 * TypeName選択状態変更時に実施する処理
 * セレクトボックス内の項目を絞り込む(選択した#typeNameに対応するもののみ)
 * 絞り込むセレクトボックス->#level,#workset,#material,#familyName
 * @param なし
 * @return なし
 */
function TypeNameChange(){
    DEBUGLOG("TypeNameChange", "start", 0);

    var totalBindLevelData = [];
    var totalBindWorkSetData = [];
    var totalBindMaterialData = [];
    var totalBindFamilyNameData = [];
    var projectName = "";
    var levelList = [];
    var worksetList = [];
    var materialList = [];
    var familyNameList = [];
    var typeNameList = [];
    var ajaxCnt = 0;
    var versionSelectedCount = $('#version option:selected').length;
    
    $('#version option:selected').each(function(){
        var item_proj_id = 0;
        var tmpArrVersion = JSON.parse($(this).val());
        var version_item_id = tmpArrVersion.item_id;
        var versionNum = tmpArrVersion.version_number;

        $('#item option:selected').each(function(){
            var tmpArrItem = JSON.parse($(this).val());
            if(version_item_id == tmpArrItem.id){
                item_proj_id = tmpArrItem.project_id;
            }
        });
        $('#project option:selected').each(function(){
            var tmpArrProj = JSON.parse($(this).val());
            if(item_proj_id == tmpArrProj.id){
                projectName = tmpArrProj.name;
            }
        });
        $('#level option:selected').each(function(){
            var tmpArrLevel = JSON.parse($(this).val());
            levelList.push(tmpArrLevel.name);
        });
        $('#workset option:selected').each(function(){
            var tmpArrWorkset = JSON.parse($(this).val());
            worksetList.push(tmpArrWorkset.name);
        });
        $('#material option:selected').each(function(){
            var tmpArrMaterial = JSON.parse($(this).val());
            materialList.push(tmpArrMaterial.name);
        });
        $('#familyName option:selected').each(function(){
            var tmpArrFamilyName = JSON.parse($(this).val());
            familyNameList.push(tmpArrFamilyName.name);
        });
        $('#typeName option:selected').each(function(){
            var tmpArrTypeName = JSON.parse($(this).val());
            typeNameList.push(tmpArrTypeName.name);
        });
        
        $.ajax({
            url: "../forge/getData",
            type: 'post',
            data:{_token: CSRF_TOKEN,message:"getComboDataByTypeName",projectName:projectName,versionNum:versionNum,
                    levelList:levelList,worksetList:worksetList,materialList:materialList,familyNameList:familyNameList,typeNameList:typeNameList,
            },
            success :function(data) {
                if(data != null){
                    totalBindLevelData.push(data["levels"]);
                    totalBindWorkSetData.push(data["worksets"]);
                    totalBindMaterialData.push(data["materials"]);
                    totalBindFamilyNameData.push(data["familyNames"]);
                    
                    ajaxCnt++;
                    if(ajaxCnt == versionSelectedCount){
                        // console.log("totalBindLevelData");console.log(JSON.stringify(totalBindLevelData));
                        // console.log("totalBindWorkSetData");console.log(JSON.stringify(totalBindWorkSetData));
                        // console.log("totalBindMaterialData");console.log(JSON.stringify(totalBindMaterialData));
                        // console.log("totalBindFamilyNameData");console.log(JSON.stringify(totalBindFamilyNameData));
                        if (levelList.length === 0)		{ BindComboMultiData(totalBindLevelData,"level",PLACEHOLDER_NAME_LEVEL); }
                        if (worksetList.length === 0)	{ BindComboMultiData(totalBindWorkSetData,"workset",PLACEHOLDER_NAME_WORKSET); }
                        if (materialList.length === 0)  { BindComboMultiData(totalBindMaterialData,"material",PLACEHOLDER_NAME_MATERIAL); }
                        if (familyNameList.length === 0){ BindComboMultiData(totalBindFamilyNameData,"familyName",PLACEHOLDER_NAME_FAMILY); }
                    }
                }
            },
            error:function(err){
                console.log(err);
            }
        });
    });
    
}

/**
 * 検索条件に一致するデータのレポート、Forgeモデル(3DView)を出力する。
 * @param  {string} [in]token    3Dビュー表示用トークン
 * @return なし
 */
function ReportForgeData(token){
    DEBUGLOG("ReportForgeData", "start", 0);
    ShowLoading();
    ResetCount();
    var level_list = [];
    var selected_categories=[];
    var workset_list=[];
    var material_list = [];
    var familyName_list = [];
    var typeName_list = [];
    var totalData = {};
    var inputType = document.getElementById("inputTypeName").value;
    var typeName_filter = inputType.replace(/_/g, '\\_');
    var projectSelectedType = [];
    var isMultipleProject = false;
    
    $("#level option:selected").each(function(){
        level_list.push($(this).text());
    });
    $("#category option:selected").each(function(){
        selected_categories.push($(this).val());
    });
    $("#workset option:selected").each(function(){
        workset_list.push($(this).text());
    });
    $("#material option:selected").each(function(){
        material_list.push($(this).text());
    });
    $("#familyName option:selected").each(function(){
        familyName_list.push($(this).text());
    });
    $("#typeName option:selected").each(function(){
        typeName_list.push($(this).text());
    });
    console.log("=======================");
    console.log(selected_categories);
    var ajaxCount = 0;
    var versionSelectedCount = $('#version option:selected').length;
    $('#version option:selected').each(function(){
        var valArr =JSON.parse($(this).val());
        var version_number = valArr.version_number;
        var item_id = valArr.item_id;
        if ($.inArray(valArr.name, projectSelectedType) == -1){
            projectSelectedType.push(valArr.name);
        }
        else{
            isMultipleProject = true;
        }
        
        return $.ajax({
            url: "../forge/getData",
            type: 'post',
            data:{_token: CSRF_TOKEN,message:"getDataByVersion",version_number:version_number,
                    item_id:item_id,category_list:selected_categories,material_list:material_list,workset_list:workset_list,
                    level_list:level_list,familyName_list:familyName_list,typeName_list:typeName_list,typeName_filter:typeName_filter},
            success :function(data) {
                console.log("===========data============");
                console.log(data);
                var tmpStr = valArr.name + "(" + version_number + ")";
                totalData[tmpStr] = data;
                ajaxCount++;
                if(ajaxCount == versionSelectedCount){
                    DisplayforgeData(token, totalData, projectSelectedType.length, isMultipleProject);
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

/**
 * データをエクセル形式でダウンロードする。
 * @param なし
 * @return なし
 */
function DownloadForgeData(){
    DEBUGLOG("DownloadForgeData", "start", 0);
    ShowLoading();

    var level_list = [];
    var selected_categories=[];
    var workset_list=[];
    var material_list = [];
    var familyName_list = [];
    var typeName_list = [];
    var ajaxCnt = 0;
    var versionSelectedCount = $('#version option:selected').length;
    
    var overviewData = {"Elements":0,"Volume":0,"Materials":0,"TypeName":0,"FamilyName":0};
    var chartData = {};
    var totalData = {};
    var inputType = document.getElementById("inputTypeName").value;
    var typeName_filter = inputType.replace(/_/g, '\\_');

    $("#level option:selected").each(function(){
        level_list.push($(this).text());
    });
    $("#category option:selected").each(function(){
        selected_categories.push($(this).val());
    });
    $("#workset option:selected").each(function(){
        workset_list.push($(this).text());
    });
    $("#material option:selected").each(function(){
        material_list.push($(this).text());
    });
    $("#familyName option:selected").each(function(){
        familyName_list.push($(this).text());
    });
    $("#typeName option:selected").each(function(){
        typeName_list.push($(this).text());
    });
    $('#version option:selected').each(function(){
        var valArr =JSON.parse($(this).val());
        var db_version_id = valArr.id;
        var version_number = valArr.version_number;
        var item_id = valArr.item_id;
        
        return $.ajax({
            url: "../forge/getData",
            type: 'post',
            data:{_token: CSRF_TOKEN,message:"getDataByVersion",version_number:version_number,
                    item_id:item_id,category_list:selected_categories,material_list:material_list,workset_list:workset_list,
                    level_list:level_list,familyName_list:familyName_list,typeName_list:typeName_list,typeName_filter:typeName_filter},
            success :function(data) {
                // console.log(data);

                if ((data == "") || (data == null)) {
                    HideLoading();
                    alert("not exist in the database.");
                    return;
                }

                if (versionSelectedCount == 1) {
                    OrganizeDataForEachVersion(data, overviewData, chartData);
                    DownloadProcForgeData(overviewData, chartData, 'Forge');
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
                HideLoading();
                console.log(err);
            }
        });
    });
}

/**
 * データを出力する。
 * @param  {object}  [in]data   元データ
 * @param  {number}  [in]prjNum 選択プロジェクト数(同一プロジェクト内で複数バージョン選択されていても1つとカウント)
 * @param  {boolean} [in]isMultipleVersion 同一プロジェクト内で複数バージョン選択したかどうかのフラグ
 *                                            (true:multiple version, false:single version)
 * @return true:成功,false:失敗
 */
function DisplayforgeData(token, data, projectSelectedNum, isMultipleVersion){
    DEBUGLOG("DisplayforgeData", "start", 0);
    
    var overviewData = {};
    var chartData = {};
    var diffList = {};

    if ((data == "") || (data == null)) {
        alert("not exist in the database.");
        return false;
    }

    if (Object.keys(data).length == 1){
        /* single project, single version */
        toggleFilter();
        OrganizeDataForEachVersion(data[Object.keys(data)], overviewData, chartData);
        DisplayCurrentVersionData(overviewData, chartData);
        // 3DView出力
        //if(token !== undefined || token != ""){
            ShowModel();
        //}
    }else if(projectSelectedNum == 1){
        /* single project, multiple version */
        toggleFilter();
        OrganizeDataSpecifiedVersion(data, overviewData, chartData, diffList);
        DisplayMultipleVersionData(overviewData, chartData, diffList);
    }else{
        if (isMultipleVersion){
            /* multiple project, multiple version */
            alert("error : unsupported.\n一つのプロジェクト内で複数バージョン選択するか、\n複数プロジェクトを選択する場合、一つのプロジェクト内で選択するバージョンは一つにしてください。");
        }else{
            /* multiple project, single version */
            toggleFilter();
            OrganizeDataSpecifiedProject(data, overviewData, chartData);
            DisplaySpecifiedProjectData(overviewData, chartData);
            
            // [TODO]3DView出力(複数)
        }
    }
    
    return true;
}

/**
 * 単数バージョンデータ出力用(概要/チャート)データに整理する。
 * @param  {object} [in]data          元データ
 * @param  {object} [out]overviewData 概要データ
 * @param  {object} [out]chartData    チャートデータ
 * @return なし
 */
function OrganizeDataForEachVersion(data, overviewData, chartData){
    DEBUGLOG("OrganizeDataForEachVersion", "start", 0);
    
    CreateOverviewData(data, overviewData);
    CreateChartData(data, chartData);
}

/**
 * 複数バージョンデータを出力用(概要/チャート/差分)データに整理する。
 * @param  {object} [in]data          元データ
 * @param  {object} [out]overviewData 概要データ
 * @param  {object} [out]chartData    チャートデータ
 * @param  {object} [out]diffList     差分データ
 * @return なし
 */
function OrganizeDataSpecifiedVersion(data, overviewData, chartData, diffList){
    DEBUGLOG("OrganizeDataSpecifiedVersion", "start", 0);
    console.log(data);
    var i = 0;
    var preVersion = "";
    var preVersionNumber = 0;
    var sortedKeys = SortObjectKeys(Object.keys(data));
    console.log(sortedKeys);
    sortedKeys.forEach(function(version){//Object.keys(data).sort()
        i++;
        var tmpOverviewData = {};
        var tmpChartData = {};
        var tmpDiffList = {"mod":[],"add":[],"del":[]};
        
        CreateOverviewData(data[version], tmpOverviewData);
        CreateChartData(data[version], tmpChartData);
        
        var tmpVersion = getSubstringVersionNumber(version);
        overviewData[tmpVersion] = tmpOverviewData;
        chartData[tmpVersion] = tmpChartData;
        diffList[tmpVersion] = tmpDiffList;

        if(i > 1){
            if(preVersionNumber > tmpVersion){
                var tmp = preVersion;
                preVersion = version;
                version = tmp;
                
            }
            CreateDiffData(data[preVersion], data[version], tmpDiffList);
            diffList[tmpVersion] = tmpDiffList;
             if(preVersionNumber > tmpVersion){
                var tmp = diffList[tmpVersion];
                diffList[tmpVersion] = diffList[preVersionNumber];
                diffList[preVersionNumber] = tmp;
                
            }
        }
        
        
        //console.log(diffList);
        preVersionNumber = tmpVersion;
        preVersion = version;
        
        
    });
}

/**
 * @param  {object} [in]keyList          元データ
 
 * @return sorted key array
 */
function SortObjectKeys(keyList){
    var sortedList = {};//object;
    keyList.forEach(function(keyString) {
        var keyNumber = getSubstringVersionNumber(keyString);
        //console.log(keyNumber);
        sortedList[keyNumber] = keyString;
    });
    
    Object.keys(sortedList).sort();
    
    return Object.values(sortedList);
    //console.log((sortedList));
}
/**
 * 複数プロジェクトデータを出力用(概要/チャート)データに整理する。
 * @param  {object} [in]data          元データ
 * @param  {object} [out]overviewData 概要データ
 * @param  {object} [out]chartData    チャートデータ
 * @return なし
 */
function OrganizeDataSpecifiedProject(data, overviewData, chartData){
    DEBUGLOG("OrganizeDataSpecifiedProject", "start", 0);
    
    Object.keys(data).forEach(function(project){
        var tmpOverviewData = {};
        var tmpChartData = {};
        // var tmpProject = getSubstringVersionName(project);

        CreateOverviewData(data[project], tmpOverviewData);
        CreateChartData(data[project], tmpChartData);
        
        overviewData[project] = tmpOverviewData;
        chartData[project] = tmpChartData;
    });
}

/**
 * 概要データを生成する。
 * @param  {object} [in]data         元データ
 * @param  {object} [in]overviewData 概要データ
 * @return なし
 */
function CreateOverviewData(data, overviewData){
    DEBUGLOG("CreateOverviewData", "start", 0);
    
    var metrial_num = 0;
    var type_num = 0;
    var family_num = 0;
    var tmpVolume = 0;
    var tmpMeterial_list = [];
    var tmpTypeName_list = [];
    var tmpFamilyName_list = [];
    
    Object.keys(data).forEach(function(key) {
        
        tmpVolume += data[key]["volume"];
        
        if (tmpMeterial_list.indexOf(data[key]["material_name"]) == -1) {
            tmpMeterial_list.push(data[key]["material_name"]);
            ++metrial_num;
        }
        if (tmpTypeName_list.indexOf(data[key]["type_name"]) == -1) {
            tmpTypeName_list.push(data[key]["type_name"]);
            ++type_num;
        }
        if (tmpFamilyName_list.indexOf(data[key]["family_name"]) == -1) {
            tmpFamilyName_list.push(data[key]["family_name"]);
            ++family_num;
        }
    });

    overviewData["Elements"] = data.length;
    overviewData["Volume"] = tmpVolume.toFixed(2);
    overviewData["Materials"] = metrial_num;
    overviewData["TypeName"] = type_num;
    overviewData["FamilyName"] = family_num;
}

/**
 * チャートデータを生成する。
 * @param  {object} [in]data      元データ
 * @param  {object} [in]chartData チャートデータ
 * @return なし
 */
function CreateChartData(data, chartData){
    DEBUGLOG("CreateChartData", "start", 0);
    
    var volumeChartDataForEachLevel = {};   // { level_name_a:容積, level_name_b:容積, }
    var materialChartData = {};             // { material_name_a:個数, material_name_b:個数, }
    var typeNameChartData = {};             // { type_name_a:個数, type_name_b:個数, }
    var familyNameChartData = {};           // { family_name_a:個数, family_name_b:個数, }
    var priceChartData = {};           // { price_a:count, price_b:個数, }
    var typePanelChartData = {};           // { type_panel_a:個数, type_panel_b:個数, }
    var furnitureChartData = {};
    
    var hidVolumeChartDataForEachLevel = {};
    var hidMaterialChartData = {};
    var hidFamilyNameChartData = {}; 
    var hidTypeNameChartData = {};
    var hidPriceChartData = {};
    var hidTypePanelChartData = {};
    var hidFurnitureChartData = {};
    
    Object.keys(data).forEach(function(key) {
        var tmpLevel            = data[key]["level"];
        var tmpMaterial_name    = data[key]["material_name"];
        var tmpType_name        = data[key]["type_name"];
        var tmpFamily_name      = data[key]["family_name"];
        var tmpPrice            = data[key]["price"];
        var tmpType_panel       = data[key]["type_panel"];
        var element_db_id       = data[key]["element_db_id"];

        //Volumeチャート用データ作成
        volumeChartDataForEachLevel[tmpLevel] = volumeChartDataForEachLevel[tmpLevel] ? volumeChartDataForEachLevel[tmpLevel]+data[key]["volume"] : data[key]["volume"];
        hidVolumeChartDataForEachLevel[tmpLevel] = hidVolumeChartDataForEachLevel[tmpLevel] ? hidVolumeChartDataForEachLevel[tmpLevel]+','+element_db_id : element_db_id;

        //個数チャート用データ作成 (マテリアル/タイプ/ファミリ)
        materialChartData[tmpMaterial_name] = materialChartData[tmpMaterial_name] ? materialChartData[tmpMaterial_name]+1 : 1;
        hidMaterialChartData[tmpMaterial_name] = hidMaterialChartData[tmpMaterial_name] ? hidMaterialChartData[tmpMaterial_name]+','+element_db_id : element_db_id;
        
        familyNameChartData[tmpFamily_name] = familyNameChartData[tmpFamily_name] ? familyNameChartData[tmpFamily_name]+1 : 1;
        hidFamilyNameChartData[tmpFamily_name] = hidFamilyNameChartData[tmpFamily_name] ? hidFamilyNameChartData[tmpFamily_name]+','+element_db_id : element_db_id;
        
        typeNameChartData[tmpType_name] = typeNameChartData[tmpType_name] ? typeNameChartData[tmpType_name]+1 : 1;
        hidTypeNameChartData[tmpType_name] = hidTypeNameChartData[tmpType_name] ? hidTypeNameChartData[tmpType_name]+','+element_db_id : element_db_id;
        
        //filter just door and window 
        if(tmpType_panel != null){
            priceChartData[tmpPrice] =  priceChartData[tmpPrice] ?  priceChartData[tmpPrice]+1 : 1;
            hidPriceChartData[tmpPrice] = hidPriceChartData[tmpPrice] ? hidPriceChartData[tmpPrice]+','+element_db_id : element_db_id;
            
            typePanelChartData[tmpType_panel] = typePanelChartData[tmpType_panel] ? typePanelChartData[tmpType_panel]+1 : 1;
            hidTypePanelChartData[tmpType_panel] =  hidTypePanelChartData[tmpType_panel] ?  hidTypePanelChartData[tmpType_panel]+','+element_db_id : element_db_id;
            
            furnitureChartData[tmpLevel] = furnitureChartData[tmpLevel] ? furnitureChartData[tmpLevel]+1 : 1;
            hidFurnitureChartData[tmpLevel] = hidFurnitureChartData[tmpLevel] ? hidFurnitureChartData[tmpLevel]+','+element_db_id : element_db_id;
        }
    });

    chartData["Volume"]     = sortObjectValue(volumeChartDataForEachLevel, false);
    chartData["Materials"]  = sortObjectValue(materialChartData, false);
    chartData["TypeName"]   = sortObjectValue(typeNameChartData, false);
    chartData["FamilyName"] = sortObjectValue(familyNameChartData, false);
    chartData["Price"] = priceChartData;
    chartData["TypePanel"] = typePanelChartData;
    chartData["Furniture"] = furnitureChartData;
    chartData["hidVolumeElementIds"] = hidVolumeChartDataForEachLevel;
    chartData["hidMaterialElementIds"] = hidMaterialChartData;
    chartData["hidFamilyElementIds"] = hidFamilyNameChartData;
    chartData["hidTypeElementIds"] = hidTypeNameChartData;
    chartData["hidPriceElementIds"] = hidPriceChartData;
    chartData["hidTypePanelElementIds"] = hidTypePanelChartData;
    chartData["hidFurnitureElementIds"] = hidFurnitureChartData;
}

/**
 * オブジェクトを昇順または降順に並び替える。
 * @param  {object}  [in]data 並び替え前のオブジェクト
 * @param  {boolean} [in]isAscendingOrder 並び替え順序(true:昇順,false:降順)
 * @return {object}  並び替え後のオブジェクト
 */
function sortObjectValue(data, isAscendingOrder){
    DEBUGLOG("sortObjectValue", "start", 0);
    
    var tmpArrays = [];
    var retSortedData = {};
    
    // 一旦配列変換
    Object.keys(data).forEach(function(key){
        var tmpArray = [key, data[key]];
        tmpArrays.push(tmpArray);
    });
    
    // ソートを実行
    if (isAscendingOrder) {
        tmpArrays.sort(function(a,b){ return(a[1]-b[1]); });    //昇順ソート
    }
    else{
        tmpArrays.sort(function(a,b){ return(b[1]-a[1]); });    //降順ソート
    }
    
    // Objectに戻す
    for(let i = 0; i < tmpArrays.length; i++) {
        retSortedData[tmpArrays[i][0]] = tmpArrays[i][1];
    }
    
    return retSortedData;
}

/**
 * 差分データの作成
 * @param  {object}  [in]preData  前回データ
 * @param  {object}  [in]curData  今回データ
 * @param  {object}  [inout]diffList 差分リスト(オジェクトデータ形式 >> {"mod":[],"add":[],"del":[]})
 * @return なし
 */
function CreateDiffData(preData, curData, diffList){
    DEBUGLOG("CreateDiffData", "start", 0);
    console.log(preData);
    console.log(curData);
    var delTable = [];
    var addTable = [];
    var modTable = [];
    
    //if(preData === curData){ return; }

    $.each(preData,function(key,row){
        // 削除データを検索
        var tmpCurData = curData.find((v) => v.element_id === row["element_id"]);
        if (tmpCurData == undefined) {
            // 削除確定
            delTable.push(preData[key]);
        }
    })

    $.each(curData,function(key,row){
        // 変更・追加データを検索
        var tmpPreData = preData.find((v) => v.element_id === row["element_id"]);
        //console.log(tmpPreData);
        if (tmpPreData == undefined) {
            
            // 追加確定
            addTable.push(curData[key]);
        }
        else {
            // 変更判定
            if (  (row["type_name"] != tmpPreData["type_name"])
                ||(row["element_id"] != tmpPreData["element_id"])
                ||(row["material_name"] != tmpPreData["material_name"])
                ||(row["level"] != tmpPreData["level"])
                ||(row["volume"] != tmpPreData["volume"])
                ||(row["family_name"] != tmpPreData["family_name"])
                ||(row["workset"] != tmpPreData["workset"])
                ||(row["version_number"] != tmpPreData["version_number"])
               ) {
                // 変更確定
                //row["pre_level"] = tmpPreData["level"];
                row["pre_type_name"] = tmpPreData["type_name"];
                row["pre_material_name"] = tmpPreData["material_name"]
                row["pre_volume"] = tmpPreData["volume"];
               
                modTable.push(row);
            }
        }
    })

    diffList["mod"] = modTable;
    diffList["add"] = addTable;
    diffList["del"] = delTable;
    console.log(diffList);
}

/**
 * １つのバージョンのモデルデータを出力するためのHTMLを記述し、出力用タグに適用する。
 * @param  {object}  [in]overviewData 出力データ(1)概要データ
 * @param  {object}  [in]chartData    出力データ(2)チャートデータ
 * @return なし
 */
function DisplayCurrentVersionData(overviewData, chartData){
    DEBUGLOG("DisplayCurrentVersionData", "start", 0);

    var appendText = "";
    if ( (isEmpty(overviewData)) || (isEmpty(chartData)) ) { return; }
    
    $("#hidVolumePieChartContainer").val(JSON.stringify(chartData["hidVolumeElementIds"]));
    $("#hidMaterialsPieChartContainer").val(JSON.stringify(chartData["hidMaterialElementIds"]));
    $("#hidFamilyNamePieChartContainer").val(JSON.stringify(chartData["hidFamilyElementIds"]));
    $("#hidTypeNamePieChartContainer").val(JSON.stringify(chartData["hidTypeElementIds"]));
    $("#hidPricePieChartContainer").val(JSON.stringify(chartData["hidPriceElementIds"]));
    $("#hidTypePanelPieChartContainer").val(JSON.stringify(chartData["hidTypePanelElementIds"]));
    $("#hidFurniturePieChartContainer").val(JSON.stringify(chartData["hidFurnitureElementIds"]));


	appendText += "<div id='AnalysisView' style='display:flex;'>";
// appendText += "<div id='AnalysisView' >";

	//##############################################################################################
	// チャートデータ出力用HTML記述
	//##############################################################################################
	appendText += "<div style='width:55%;'>";

	//##############################################################################################
	// 概要データ出力用HTML記述
	//##############################################################################################
    appendText += "<div class='row'>";
    Object.keys(overviewData).forEach(function(key) {
        appendText += "<div class='stats-small-area'>";
        appendText += "<div class='stats-small stats-small--1 card card-small'>";
        appendText += "<div class='card-body 0-1 d-flex'>";
        appendText += "<div class='d-flex flex-column m-auto'>";
        appendText += "<div class='stats-small__data text-center'>";
        appendText += "<span class='stats-small__label text-uppercase'>"+key+"</span>";
        appendText += "<h2 class='stats-small__value count my-3'>"+overviewData[key]+"</h2>";
        appendText += "</div></div></div></div></div>";
    });
    appendText += "</div>"; //row

    appendText += "<div class='mv-loading hide'>";
    	appendText += "<div class='lds-spinner'>";
    	appendText += "<div></div>";
    	appendText += "<div></div>";
    	appendText += "<div></div>";
    	appendText += "<div></div>";
    	appendText += "<div></div>";
    	appendText += "<div></div>";
    	appendText += "<div></div>";
    	appendText += "<div></div>";
    	appendText += "<div></div>";
    	appendText += "<div></div>";
    	appendText += "<div></div>";
    	appendText += "<div></div>";
    	appendText += "</div>";
    appendText += "</div>";
    appendText += "<div class='tab-wrap'>";
    
        //##############################################################################################
        
        appendText += "<input id='tab01' type='radio' name='tab' class='tab-switch' checked='checked'><label class='tab-label' for='tab01'>Pie Chart</label>";
        appendText += "<div class='tab-content'>";
            /* Pie Chart */
            appendText += "<div style='height:63vh;display:flex;flex-wrap:wrap;'>";
            // appendText += "<div style='height:65vh;display:flex;flex-wrap:no-wrap;'>";
            /*appendText += "<input type='hidden' value="+JSON.stringify(chartData['hidVolumeElementIds'])+"id='hidVolumePieChartContainer'/>";*/
                appendText += "<div class='tab-content-chart-area'>";
                    appendText += "<div id=VolumePieChartContainer style='width:100%;height:100%;'></div>";
                    if(Object.values(chartData["Volume"]).length > 0 && Object.values(chartData["Volume"]).reduce(function(a,b){  return a+b }) == 0)
                        DrawPieChart(chartData["Furniture"], "Volume", "個数【窓、ドア】");
                    else
                        DrawPieChart(chartData["Volume"], "Volume", "Volume");
                appendText += "</div>";
                appendText += "<div class='tab-content-chart-area'>";
                    appendText += "<div id=MaterialsPieChartContainer style='width:100%;height:100%;'></div>";
                    DrawPieChart(chartData["Materials"], "Materials", "Materials");
                appendText += "</div>";
                appendText += "<div class='tab-content-chart-area'>";
                    appendText += "<div id=TypeNamePieChartContainer style='width:100%;height:100%;'></div>";
                    DrawPieChart(chartData["TypeName"], "TypeName", "TypeName");
                appendText += "</div>";
                appendText += "<div class='tab-content-chart-area'>";
                    appendText += "<div id=FamilyNamePieChartContainer style='width:100%;height:100%;'></div>";
                    DrawPieChart(chartData["FamilyName"], "FamilyName", "FamilyName");
                appendText += "</div>";
                appendText += "<div class='tab-content-chart-area'>";
                    appendText += "<div id=PricePieChartContainer style='width:100%;height:100%;'></div>";
                    DrawPieChart(chartData["Price"], "Price", "Price【窓、ドア】");
                appendText += "</div>";
                appendText += "<div class='tab-content-chart-area'>";
                    appendText += "<div id=TypePanelPieChartContainer style='width:100%;height:100%;'></div>";
                    DrawPieChart(chartData["TypePanel"], "TypePanel", "TypePanel【窓、ドア】");
                appendText += "</div>";
            appendText += "</div>";
            /* End Pie Chart */
        appendText += "</div>"; //tab-content
        
        //##############################################################################################
        
        appendText += "<input id='tab02' type='radio' name='tab' class='tab-switch'><label class='tab-label' for='tab02'>Column Chart</label>";
        appendText += "<div class='tab-content'>";
            /* Column Chart */
            appendText += "<div style='height:80vh;'>";
                appendText += "<div class='tab-content-chart-area' style='background-color:lightblue;'>";
                    appendText += "<div id=VolumeColumnChartContainer style='width:100%;height:100%;'></div>";
                    if(Object.values(chartData["Volume"]).length > 0 && Object.values(chartData["Volume"]).reduce(function(a,b){  return a+b }) == 0)
                        DrawColumnChart(chartData["Furniture"], "Volume", "Level", "(個数)");
                    else
                        DrawColumnChart(chartData["Volume"], "Volume", "Volume", "(m^3)");
                appendText += "</div>";
                appendText += "<div class='tab-content-chart-area'>";
                    appendText += "<div id=MaterialsColumnChartContainer style='width:100%;height:100%;'></div>";
                    DrawColumnChart(chartData["Materials"], "Materials", "Materials", "(個数)");
                appendText += "</div>";
                appendText += "<div class='tab-content-chart-area'style='background-color:lightblue;'>";
                    appendText += "<div id=TypeNameColumnChartContainer style='width:100%;height:100%;'></div>";
                    DrawColumnChart(chartData["TypeName"], "TypeName", "TypeName", "(個数)");
                appendText += "</div>";
                appendText += "<div class='tab-content-chart-area'>";
                    appendText += "<div id=FamilyNameColumnChartContainer style='width:100%;height:100%;'></div>";
                    DrawColumnChart(chartData["FamilyName"], "FamilyName", "FamilyName", "(個数)");
                appendText += "</div>";
                appendText += "<div class='tab-content-chart-area'>";
                    appendText += "<div id=PriceColumnChartContainer style='width:100%;height:100%;'></div>";
                    DrawColumnChart(chartData["Price"], "Price", "Price", "(個数)");
                appendText += "</div>";
                appendText += "<div class='tab-content-chart-area'>";
                    appendText += "<div id=TypePanelColumnChartContainer style='width:100%;height:100%;'></div>";
                    DrawColumnChart(chartData["TypePanel"], "TypePanel", "TypePanel", "(個数)");
                appendText += "</div>";
            appendText += "</div>";
            /* End Column Chart */
        appendText += "</div>"; //tab-content
        
        //##############################################################################################
    
    
        appendText += "<input id='tab03' type='radio' name='tab' class='tab-switch'><label class='tab-label' for='tab03'>Detail Info【Door,Window】</label>";
        appendText += "<div class='tab-content'>";
            /* Detail Info */
            appendText += "<div id='data' style='height:80vh;'>";
               appendText += "<ul>";
                appendText += "<li id='Overall'>";
                    appendText += "<div>一般情報<span class='icon glyphicon glyphicon-plus'></span>";
                    appendText += "</div>";
                appendText += "</li>";
                appendText += "<div class='table-responsive'><table class='table' id='tblOverall'></table></div>";
                appendText += "<li id='Sunpou'>";
                    appendText += "<div>寸法<span class='icon glyphicon glyphicon-plus'></span>";
                    appendText += "</div>";
                appendText += "</li>";
                appendText += "<div class='table-responsive'><table class='table' id='tblSunpou'></table></div>";
                appendText += "<li id='Material'>";
                    appendText += "<div>マテリアル / 仕上<span class='icon glyphicon glyphicon-plus'></span>";
                    appendText += "</div>";
                appendText += "</li>";
                appendText += "<div class='table-responsive'><table class='table' id = 'tblMaterial'></table></div>";
                appendText += "<li id='Fire'>";
                    appendText += "<div>防火<span class='icon glyphicon glyphicon-plus'></span>";
                    appendText += "</div>";
                appendText += "</li>";
                appendText += "<div class='table-responsive'><table class='table' id = 'tblFire'></table></div>";
                appendText += "<li id='Moji'>";
                    appendText += "<div>文字<span class='icon glyphicon glyphicon-plus'></span>";
                    appendText += "</div>";
                appendText += "</li>";
                appendText += "<div class='table-responsive'><table class='table' id = 'tblMoji'></table></div>";
                appendText += "<li id = 'IdenInfo'>";
                    appendText += "<div>識別情報<span class='icon glyphicon glyphicon-plus'></span>";
                    appendText += "</div>";
                appendText += "</li>";
                appendText += "<div class='table-responsive'><table class='table' id='tblIdenInfo'></table></div>";
            appendText += "</ul>";
            appendText += "</div>";
            /* End Detail Info */
        appendText += "</div>"; //tab-content
        
        //##############################################################################################
    appendText += "</div>"; // tab-wrap
    appendText += "</div>&nbsp;&nbsp;";

	//##############################################################################################
	// 3DView出力用HTML記述
	//##############################################################################################
    appendText += "<div id='modelViewer'><span id='spanText'></span>";
    appendText += "</div>"; // modelViewer

    appendText += "</div>"; // AnalysisView

	//##############################################################################################
	// タグにHTMLを適用
	//##############################################################################################
    $("#tblVersionData div").remove();
    $("#tblVersionData").append(appendText);
}

/**
 * 複数バージョンのモデルデータを出力するためのHTMLを記述し、出力用タグに適用する。
 * @param  {object}  [in]overviewData 出力データ(1)概要データ
 * @param  {object}  [in]chartData    出力データ(2)チャートデータ
 * @param  {object}  [in]diffList     出力データ(3)差分IDリストデータ
 * @return なし
 */
function DisplayMultipleVersionData(overviewData, chartData, diffList){
    DEBUGLOG("DisplayMultipleVersionData", "start", 0);
    
    var appendText = "";

    if ( (isEmpty(overviewData)) || (isEmpty(chartData)) ) { return; }

	//##############################################################################################
	// 概要データ出力用HTML記述
	//##############################################################################################
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

// 	appendText += "<div id='AnalysisView' style='display:flex;'>";

	//##############################################################################################
	// チャートデータ出力用HTML記述
	//##############################################################################################
// 	appendText += "<div style='width:50%;'>";
    appendText += "<div class='tab-wrap'>";
    
        //##############################################################################################
        
        appendText += "<input id='tab01' type='radio' name='tab' class='tab-switch' checked='checked'><label class='tab-label' for='tab01'>バージョン間推移</label>";
        appendText += "<div class='tab-content'>";
            /* Line Chart */
            appendText += "<div style='height:80vh;display:flex;flex-wrap:wrap;'>";
            
                appendText += "<div class='tab-content-chart-area'>";
                    appendText += "<div id=ElementsLineChartContainer style='width:100%;height:100%;'></div>";
                    DrawLineChart(overviewData, "Elements");
                appendText += "</div>";
                appendText += "<div class='tab-content-chart-area'>";
                    appendText += "<div id=VolumeLineChartContainer style='width:100%;height:100%;'></div>";
                    DrawLineChart(overviewData, "Volume");
                appendText += "</div>";
                appendText += "<div class='tab-content-chart-area'>";
                    appendText += "<div id=MaterialsLineChartContainer style='width:100%;height:100%;'></div>";
                    DrawLineChart(overviewData, "Materials");
                appendText += "</div>";
                appendText += "<div class='tab-content-chart-area'>";
                    appendText += "<div id=TypeNameLineChartContainer style='width:100%;height:100%;'></div>";
                    DrawLineChart(overviewData, "TypeName");
                appendText += "</div>";
                appendText += "<div class='tab-content-chart-area'>";
                    appendText += "<div id=FamilyNameLineChartContainer style='width:100%;height:100%;'></div>";
                    DrawLineChart(overviewData, "FamilyName");
                appendText += "</div>";
                
            appendText += "</div>";
            /* End Line Chart */
        appendText += "</div>"; // tab-content
        
        //##############################################################################################
    
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
        
            appendText += "</div>"; //content-mod//
            
        appendText += "</div>"; // tab-content
    
        //##############################################################################################
        
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
    
            appendText += "</div>"; //content-add
            
        appendText += "</div>"; // tab-content
    
        //##############################################################################################

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
    
            appendText += "</div>"; //content-del
            
        appendText += "</div>"; // tab-content
        
        //##############################################################################################

    appendText += "</div>"; // tab-wrap
    // appendText += "</div>&nbsp;&nbsp;";

	//##############################################################################################
	// 3DView出力用HTML記述
	//##############################################################################################
    // appendText += "<div id='modelViewer'><span id='spanText'></span>";
    // appendText += "</div>"; // modelViewer

    // appendText += "</div>"; // AnalysisView

	//##############################################################################################
	// タグにHTMLを適用
	//##############################################################################################
    $("#tblVersionData div").remove();
    $("#tblVersionData").append(appendText);
    
	//##############################################################################################
	// 差分データ出力用HTML記述
	//##############################################################################################
    i = 0;
    var preVer="";
    var sortedKeys = SortObjectKeys(Object.keys(overviewData));
    Object.keys(overviewData).forEach(function(version) {//Object.keys(overviewData).sort()

        if (i > 0) {
            DisplayModifiedTable(diffList[version]["mod"], "tblModData"+version,preVer,version);
            //DisplayTable(diffList[version]["mod"], "tblModData"+version);
            DisplayTable(diffList[version]["add"], "tblAddData"+version);
            DisplayTable(diffList[version]["del"], "tblDelData"+version);
        }
        preVer = version;
        i++;
    });
    
	//##############################################################################################
	// スライド用slick適用
	//##############################################################################################
    $('.slideshow').slick({
        arrows: false,
        autoplay: false,
        autoplaySpeed: 5000, // [ms]
        slidesToShow: 6,
        slidesToScroll: 6,
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
        prevArrow:'<img src="../public/image/arrow_left.png" style="height:25px;" class="slide-arrow prev-arrow">',
        nextArrow:'<img src="../public/image/arrow_right.png" style="height:25px;" class="slide-arrow next-arrow">',
    });
    $('.content-add').slick({
        arrows: true,
        autoplay: false,
        infinite: false,
        slidesToShow: 1,
        slidesToScroll: 1,
        appendDots: true,
        prevArrow:'<img src="../public/image/arrow_left.png" style="height:25px;" class="slide-arrow prev-arrow">',
        nextArrow:'<img src="../public/image/arrow_right.png" style="height:25px;" class="slide-arrow next-arrow">',
    });
    $('.content-del').slick({
        arrows: true,
        autoplay: false,
        infinite: false,
        slidesToShow: 1,
        slidesToScroll: 1,
        appendDots: true,
        prevArrow:'<img src="../public/image/arrow_left.png" style="height:25px;" class="slide-arrow prev-arrow">',
        nextArrow:'<img src="../public/image/arrow_right.png" style="height:25px;" class="slide-arrow next-arrow">',
    });
}

/**
 * 複数プロジェクトのモデルデータを出力するためのHTMLを記述し、出力用タグに適用する。
 * @param  {object}  [in]overviewData 出力データ(1)概要データ
 * @param  {object}  [in]chartData    出力データ(2)チャートデータ
 * @return なし
 */
function DisplaySpecifiedProjectData(overviewData, chartData){
    DEBUGLOG("DisplaySpecifiedProjectData", "start", 0);
    
    var appendText = "";

    if ( (isEmpty(overviewData)) || (isEmpty(chartData)) ) { return; }

	//##############################################################################################
	// 概要データ出力用HTML
	//##############################################################################################
    Object.keys(overviewData).forEach(function(project) {
        // appendText += "<div class='slideshow' style='display:flex;'>";
        appendText += "<div class='row'>";
        appendText += "<div class='stats-small-area' style='flex:0 0 16.6%'>";
        appendText += "<div class='stats-small stats-small--1 card card-small'>";
        appendText += "<div class='card-body 0-1 d-flex'>";
        appendText += "<div class='d-flex flex-column m-auto'>";
        appendText += "<div class='stats-small__data text-center'>";
        appendText += "<span class='stats-small__label text-uppercase'>Project</span>";
        appendText += "<h6 class='stats-small__value count my-3'>"+project+"</h6>";
        appendText += "</div></div></div></div></div>";
        
        Object.keys(overviewData[project]).forEach(function(key) {
            appendText += "<div class='stats-small-area' style='flex:0 0 16.6%'>";
            appendText += "<div class='stats-small stats-small--1 card card-small'>";
            appendText += "<div class='card-body 0-1 d-flex'>";
            appendText += "<div class='d-flex flex-column m-auto'>";
            appendText += "<div class='stats-small__data text-center'>";
            appendText += "<span class='stats-small__label text-uppercase'>"+key+"</span>";
            appendText += "<h2 class='stats-small__value count my-3'>"+overviewData[project][key]+"</h2>";
            appendText += "</div></div></div></div></div>";
        });
        // appendText += "</div>";		//slideshow
        appendText += "</div>";		//row
    });

	appendText += "<div id='AnalysisView'>";

	//##############################################################################################
	// チャートデータ出力用HTML
	//##############################################################################################
// 	appendText += "<div style='width:50%;'>";
    appendText += "<div class='tab-wrap'>";
	
		//##############################################################################################

		appendText += "<input id='tab01' type='radio' name='tab' class='tab-switch' checked='checked'><label class='tab-label' for='tab01'>Pie Chart</label>";
		appendText += "<div class='tab-content'>";

    		/* Pie Chart */
		    var cnt = 0;
            Object.keys(chartData).forEach(function(project) {
                appendText += "<h4 style='margin-left:40px;font-family:Arial;font-weight:bold;text-decoration:underline;'>"+project+"</h4>";
                
        // 		appendText += "<div style='height:65vh;display:flex;flex-wrap:wrap;'>";
        		appendText += "<div style='height:300px;display:flex;'>";
        			// appendText += "<div style='height:65vh;display:flex;flex-wrap:no-wrap;'>";
        			/*appendText += "<input type='hidden' value="+JSON.stringify(chartData['hidVolumeElementIds'])+"id='hidVolumePieChartContainer'/>";*/
    				appendText += "<div class='tab-content-chart-area' style='height:100%;'>";
    					appendText += "<div id=Volume"+cnt.toString()+"PieChartContainer style='width:100%;height:100%;'></div>";
    					DrawPieChart(chartData[project]["Volume"], "Volume"+cnt.toString(), "Volume");
    				appendText += "</div>";
    				appendText += "<div class='tab-content-chart-area' style='height:100%;'>";
    					appendText += "<div id=Materials"+cnt+"PieChartContainer style='width:100%;height:100%;'></div>";
    					DrawPieChart(chartData[project]["Materials"], "Materials"+cnt.toString(), "Materials");
    				appendText += "</div>";
    				appendText += "<div class='tab-content-chart-area' style='height:100%;'>";
    					appendText += "<div id=TypeName"+cnt+"PieChartContainer style='width:100%;height:100%;'></div>";
    					DrawPieChart(chartData[project]["TypeName"], "TypeName"+cnt.toString(), "TypeName");
    				appendText += "</div>";
    				appendText += "<div class='tab-content-chart-area' style='height:100%;'>";
    					appendText += "<div id=FamilyName"+cnt+"PieChartContainer style='width:100%;height:100%;'></div>";
    					DrawPieChart(chartData[project]["FamilyName"], "FamilyName"+cnt.toString(), "FamilyName");
    				appendText += "</div>";
        		appendText += "</div>";
				cnt++;
            });
    		/* End Pie Chart */

		appendText += "</div>"; // tab-content
		
		//##############################################################################################

		appendText += "<input id='tab02' type='radio' name='tab' class='tab-switch'><label class='tab-label' for='tab02'>Column Chart</label>";
		appendText += "<div class='tab-content'>";
		
			/* Column Chart */
			appendText += "<div style='height:80vh;'>";
			
		    var projectList = Object.keys(chartData);
		    var points = {};
			CreateStackedColumeChartData(chartData, points);

            //合計グラフ
			// appendText += "<div class='tab-content-chart-area'>";
			// 	appendText += "<div id=Total"+cnt.toString()+"ColumnChartContainer style='width:100%;height:100%;'></div>";
			// 	DrawStackedColumnChart(chartData, "Total"+cnt.toString(), "Total", "(個数)");
			// appendText += "</div>";
			
            appendText += "<div class='tab_wrap1'>";
                appendText += "<input id='tab1' type='radio' name='tab_btn' checked>";
                appendText += "<input id='tab2' type='radio' name='tab_btn'>";
                appendText += "<div class='tab_area'>";
                    appendText += "<label class='tab1_label' for='tab1'>Stacked</label>";
                    appendText += "<label class='tab2_label' for='tab2'>No Stacked</label>";
                appendText += "</div>";
                appendText += "<div class='panel_area'>";
                    appendText += "<div id='panel1' class='tab_panel'>";
            
                        // Stacked Column
        				appendText += "<div class='tab-content-chart-area'>";
        					appendText += "<div id=VolumeColumnChartContainer></div>";
        					DrawStackedColumnChart(projectList, points['Volume'], "Volume", "Volume", "(m^3)", true);
        				appendText += "</div>";
        				appendText += "<div class='tab-content-chart-area'>";
        					appendText += "<div id=MaterialsColumnChartContainer></div>";
        					DrawStackedColumnChart(projectList, points['Materials'], "Materials", "Materials", "(個数)", true);
        				appendText += "</div>";
        				appendText += "<div class='tab-content-chart-area'>";
        					appendText += "<div id=TypeNameColumnChartContainer></div>";
        					DrawStackedColumnChart(projectList, points['TypeName'], "TypeName", "TypeName", "(個数)", true);
        				appendText += "</div>";
        				appendText += "<div class='tab-content-chart-area'>";
        					appendText += "<div id=FamilyNameColumnChartContainer></div>";
        					DrawStackedColumnChart(projectList, points['FamilyName'], "FamilyName", "FamilyName", "(個数)", true);
        				appendText += "</div>";

                    appendText += "</div>";
                    appendText += "<div id='panel2' class='tab_panel'>";

                        // No Stacked Column
        				appendText += "<div class='tab-content-chart-area'>";
        					appendText += "<div id=tmpVolumeColumnChartContainer></div>";
        					DrawStackedColumnChart(projectList, points['Volume'], "tmpVolume", "Volume", "(m^3)", false);
        				appendText += "</div>";
        				appendText += "<div class='tab-content-chart-area'>";
        					appendText += "<div id=tmpMaterialsColumnChartContainer></div>";
        					DrawStackedColumnChart(projectList, points['Materials'], "tmpMaterials", "Materials", "(個数)", false);
        				appendText += "</div>";
        				appendText += "<div class='tab-content-chart-area'>";
        					appendText += "<div id=tmpTypeNameColumnChartContainer></div>";
        					DrawStackedColumnChart(projectList, points['TypeName'], "tmpTypeName", "TypeName", "(個数)", false);
        				appendText += "</div>";
        				appendText += "<div class='tab-content-chart-area'>";
        					appendText += "<div id=tmpFamilyNameColumnChartContainer></div>";
        					DrawStackedColumnChart(projectList, points['FamilyName'], "tmpFamilyName", "FamilyName", "(個数)", false);
        				appendText += "</div>";

                    appendText += "</div>";
                appendText += "</div>";
            appendText += "</div>";
    		
    		appendText += "</div>";
    		/* End Column Chart */
			
		appendText += "</div>"; // tab-content

		//##############################################################################################

    appendText += "</div>"; // tab-wrap
    // appendText += "</div>&nbsp;&nbsp;";

	//##############################################################################################
	// 3DView出力用HTML記述
	//##############################################################################################
// 	$.each(projectList, function(index, prjName) {
//     	appendText += "<br><br><h4>"+prjName+"</h4>";
//         appendText += "<div id='modelViewer'><span id='spanText'></span>";
//         appendText += "</div>"; // modelViewer
//         return false;   // == break
//     });
//     appendText += "<br>";
    // appendText += "<div id='modelViewer1'><span id='spanText1'></span>";
    // appendText += "</div>"; // modelViewer1

    appendText += "</div>"; // AnalysisView
	
	//##############################################################################################
	// タグにHTMLを適用
	//##############################################################################################
    $("#tblVersionData div").remove();
    $("#tblVersionData").append(appendText);
    
	//##############################################################################################
	// スライド用slick適用
	//##############################################################################################
    $('.slideshow').slick({
        arrows: false,
        autoplay: false,
        // autoplaySpeed: 5000, // [ms]
        slidesToShow: 6,
        slidesToScroll: 6,
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
}

/**
 * テーブルデータをHTML形式で生成しIDタグに適用する。
 * @param  {object}  [in]data テーブル生成用元データ
 * @param  {string}  [in]id   idタグ
 * @return なし
 */
function DisplayTable(data, id){
    DEBUGLOG("DisplayTable", "start", 0);
    
    var appendText = "";
    appendText += "<tr>";
    appendText += "<th>No.</th>";
    appendText += "<th>type_name</th>";
    appendText += "<th>element_id</th>";
    appendText += "<th>material_name</th>";
    appendText += "<th>level</th>";
    appendText += "<th>volume</th>";
    appendText += "<th>family_name</th>";
    appendText += "<th>workset</th>";
    appendText += "<th>version_number</th>";
    appendText += "</tr>";

    var count = 0;
    $.each(data,function(key,row){
        count++;
        appendText += "<tr>";
        appendText += "<td>"+count+".</td>";
        appendText += "<td>"+row["type_name"]+"</td>";
        appendText += "<td>"+row["element_id"]+"</td>";
        appendText += "<td>"+row["material_name"]+"</td>";
        appendText += "<td>"+row["level"]+"</td>";
        appendText += "<td>"+row["volume"]+"</td>";
        appendText += "<td>"+row["family_name"]+"</td>";
        appendText += "<td>"+row["workset"]+"</td>";
        appendText += "<td>"+row["version_number"]+"</td>";
        appendText += "</tr>";
    })
    $("#"+id+" tr").remove();
    $("#"+id).append(appendText);
}


/**
 * テーブルデータをHTML形式で生成しIDタグに適用する。
 * @param  {object}  [in]data テーブル生成用元データ
 * @param  {string}  [in]id   idタグ
 * @return なし
 */
function DisplayModifiedTable(data, id,preVer,version){
    DEBUGLOG("DisplayModifiedTable", "start", 0);

    var appendText = "";
    appendText += "<tr>";
    appendText += "<th rowspan='2'>No.</th>";
    appendText += "<th rowspan='2'>element_id</th>";
    appendText += "<th rowspan='2'>level</th>";
    appendText += "<th rowspan='2'>family_name</th>";
    appendText += "<th rowspan='2'>workset</th>";
    appendText += "<th colspan='3'>version"+preVer+"</th>";
    appendText += "<th colspan='3'>version"+version+"</th>";
    appendText += "</tr>";
    appendText += "<tr>";
    appendText += "<th>type_name</th>";
    appendText += "<th>material_name</th>";
    appendText += "<th>volume</th>";

    appendText += "<th>type_name</th>";
    appendText += "<th>material_name</th>";
    appendText += "<th>volume</th>";
    appendText += "</tr>";

    var count = 0;
    $.each(data,function(key,row){
        
        count++;
        appendText += "<tr>";
        appendText += "<td>"+count+".</td>";
        appendText += "<td>"+row["element_id"]+"</td>";
        appendText += "<td>"+row["level"]+"</td>";
        appendText += "<td>"+row["family_name"]+"</td>";
        appendText += "<td>"+row["workset"]+"</td>";
        appendText += "<td>"+row["pre_type_name"]+"</td>";
        appendText += "<td>"+row["pre_material_name"]+"</td>";
        appendText += "<td>"+row["pre_volume"]+"</td>";
        appendText += "<td>"+row["type_name"]+"</td>";
        appendText += "<td>"+row["material_name"]+"</td>";
        appendText += "<td>"+row["volume"]+"</td>";
        
        
        
        appendText += "</tr>";
    })
    $("#"+id+" tr").remove();
    $("#"+id).append(appendText);
}


/**
 * 円グラフを描画する。
 * @param  {object}  [in]chartData チャートデータ
 * @param  {string}  [in]id        タグID
 * @param  {string}  [in]title     タイトル
 * @return なし
 */
function DrawPieChart(chartData, id, title){
    DEBUGLOG("DrawPieChart", "start", 0);
    
    var points =  [];
    var total= 0;
    
    Object.keys(chartData).forEach(function(key) {
        var intArea = parseFloat(chartData[key]);
        points.push([key,intArea]);
    });
    
    google.charts.load('current', {packages: ['corechart']});
    google.charts.setOnLoadCallback(function(){pieChart(points,id,title)});
}

/**
 * 円グラフを描画処理を実行する。
 * @param  {object}  [in]chartData チャートデータ
 * @param  {string}  [in]id        タグID
 * @param  {string}  [in]title     タイトル
 * @return なし
 */
function pieChart(chartData,id,title){
    DEBUGLOG("pieChart", "start", 0);

    var data = new google.visualization.DataTable();
    data.addColumn('string', 'item');
    data.addColumn('number', 'area');
    data.addRows(chartData);

    var tmpTitle = title;
    if (isFinite(tmpTitle.slice(-1)) == true){    //末尾の文字が数字である場合
        tmpTitle = title.slice(0, -1);
    }
    
    var sWidth = 0;
    var sHeight = 0;
    var screenWidth = window.screen.width;
    if(screenWidth < 1300){
        sWidth = 360;
        sHeight = 250;
    }else{
        sWidth = 480;
        sHeight = 330;
        // sWidth = 360;
        // sHeight = 250;
    }
    
    var options = {
        title:tmpTitle,
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
        },
        width:sWidth,
        height:sHeight,
      };

    var chart = new google.visualization.PieChart(document.getElementById(id+"PieChartContainer"));

    //start chart select event
     function selectHandler() 
     {
       var selectedItem = chart.getSelection()[0];
       if (selectedItem) 
       {
         var selectedLevel = data.getValue(selectedItem.row, 0);
         var hidData = $("#hid"+id+"PieChartContainer").val();
 
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

/**
 * 棒グラフを描画する。
 * @param  {object}  [in]chartData チャートデータ
 * @param  {string}  [in]id        タグID
 * @param  {string}  [in]title     タイトル
 * @param  {string}  [in]scale     単位
 * @return なし
 */
function DrawColumnChart(chartData, id, title, scale){
    DEBUGLOG("DrawColumnChart", "start", 0);
    
    var points =  [];
    var total= 0;
    
    Object.keys(chartData).forEach(function(key) {
        var intArea = parseFloat(chartData[key]);
        points.push([key,intArea]);
    });
    
    google.charts.load('current', {packages: ['corechart', 'bar']});
    google.charts.setOnLoadCallback(function(){columnChart(points, id, title, scale)});
}

/**
 * 棒グラフを描画処理を実行する。
 * @param  {object}  [in]chartData チャートデータ
 * @param  {string}  [in]id        タグID
 * @param  {string}  [in]title     タイトル
 * @param  {string}  [in]scale     単位
 * @return なし
 */
function columnChart(chartData, id, title, scale) {
    DEBUGLOG("columnChart", "start", 0);
    
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

/**
 * 積み上げ棒グラフ用のデータを生成する。
 * @param  {object}         [in]chartData         チャートデータ
 * @param  {array[string]}  [out]volumePoints     タグID
 * @param  {array[string]}  [out]materialsPoints  タイトル
 * @param  {array[string]}  [out]typeNamePoints   単位
 * @param  {array[string]}  [out]familyNamePoints 単位
 * @return なし
 */
function CreateStackedColumeChartData(chartData, points){
    DEBUGLOG("CreateStackedColumeChartData", "start", 0);
    
    var volumeKeyList = [];
    var materialKeyList = [];
    var typeNameKeyList = [];
    var familyNameKeyList = [];
    
    //オブジェクト内の全てのキー取得
    Object.keys(chartData).forEach(function(project) {
        volumeKeyList.push(Object.keys(chartData[project]['Volume']));
        materialKeyList.push(Object.keys(chartData[project]['Materials']));
        typeNameKeyList.push(Object.keys(chartData[project]['TypeName']));
        familyNameKeyList.push(Object.keys(chartData[project]['FamilyName']));
    });

    points['Volume']        = chkProjectDataByKeyList(chartData, 'Volume', arrayUnique(volumeKeyList));
    points['Materials']     = chkProjectDataByKeyList(chartData, 'Materials', arrayUnique(materialKeyList));
    points['TypeName']      = chkProjectDataByKeyList(chartData, 'TypeName', arrayUnique(typeNameKeyList));
    points['FamilyName']    = chkProjectDataByKeyList(chartData, 'FamilyName', arrayUnique(familyNameKeyList));
}

/**
 * 積み上げ棒グラフを描画する。
 * @param  {object}  [in]prjList プロジェクトリスト
 * @param  {object}  [in]points  描画用データ配列
 * @param  {string}  [in]id      タグID
 * @param  {string}  [in]title   タイトル
 * @param  {string}  [in]scale   単位
 * @param  {boolean} [in]isStacked グラフが積み上げ形式かどうか(true:積み上げ有効, false:積み上げ無効)
 * @return なし
 */
function DrawStackedColumnChart(prjList, points, id, title, scale, isStacked){
    DEBUGLOG("DrawStackedColumnChart", "start", 0);

    google.charts.load('current', {packages: ['corechart', 'bar']});
    google.charts.setOnLoadCallback(function(){stackedColumnChart(prjList, points, id, title, scale, isStacked)});
}

/**
 * chartDataからキーリストと一致するデータを抽出し、複数プロジェクトのデータをひとつのデータにまとめる。
 * @param  {object}        [in]chartData チャートデータ
 * @param  {string}        [in]chartKey  chartData���キー
 * @param  {array[string]} [in]keyList   キーリスト
 * @return 整理後のデータ
 */
function chkProjectDataByKeyList(chartData, chartKey, keyList){
    var retPoints = [];
    
    Object.keys(keyList).forEach(function(key) {
        var tmpPoints = [];
        tmpPoints.push(keyList[key]);

        Object.keys(chartData).forEach(function(project) {
            var chartKeyList = Object.keys(chartData[project][chartKey]);

            if (chartKeyList.indexOf(keyList[key]) == -1){
                tmpPoints.push(0);
            }
            else{
                var intArea = parseFloat(chartData[project][chartKey][keyList[key]]);
                tmpPoints.push(intArea);
            }
        });
        
        retPoints.push(tmpPoints);
    });
    
    return retPoints;
}

/**
 * 配列内の重複した文字列を削除し、ユニークな配列とする。
 * @param  {array[string]}  [in]array 入力配列
 * @return ユニーク配列
 */
function arrayUnique(array) {
    var retArray = [];
    var isOverlap = false;
    var i = 0;
    
    array.forEach(function(str, index) {
        if (i == 0){
            for (let i = 0; i < str.length; ++i) {
                retArray.push(str[i]);
            }
        }
        else{
            str.forEach(function(pStr, pIdx) {
                for (let i = 0; i < retArray.length; ++i) {
                    if (pStr == retArray[i]){
                        isOverlap = true;
                    }
                }
                if (isOverlap == false){
                    retArray.push(pStr);
                }
                isOverlap = false;
            });
        }
        
        i++;
    });
    
    return retArray;
}

/**
 * 積み上げ棒グラフを描画処理を実行する。
 * @param  {object}  [in]chartData チャートデータ
 * @param  {string}  [in]id        タグID
 * @param  {string}  [in]title     タイトル
 * @param  {string}  [in]scale     単位
 * @param  {boolean} [in]isStacked グラフが積み上げ形式かどうか(true:積み上げ有効, false:積み上げ無効)
 * @return なし
 */
function stackedColumnChart(prjList, chartData, id, title, scale, isStacked) {
    DEBUGLOG("stackedColumnChart", "start", 0);
    // console.log("prjList.length:"+prjList.length);
    // console.log("prjList:"+prjList);

    var data = new google.visualization.DataTable();
    data.addColumn('string', title);
    for(var i = 0; i < prjList.length; i++){
        data.addColumn('number', prjList[i]);
    }
    data.addRows(chartData);

    var options = {
        title: title,
        titleTextStyle: {fontSize:20},
        animation:{ duration: 1000,easing: 'out',startup: true },
        // hAxis: {title: title, titleTextStyle:{italic:true}, textStyle:{fontSize:10}},
        vAxis: { minValue: 0, title: scale, titleTextStyle:{italic:true} },
        // series: [{ visibleInLegend: false }],
        bar: { groupWidth: 20 },
        // legend: { position: 'top' },
        // chartArea:{width:'1000px', height:'300px'},
        isStacked: isStacked,    //true:積み上げ���効, false:積み上げ無効
        width:1500,
        height:300,
    };

    var chart = new google.visualization.ColumnChart(document.getElementById(id+"ColumnChartContainer"));
    // start chart select event
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
    // end chart select event
    
    // $("#"+id+"ColumnChartContainer").css({
    //   "width": "1500px",
    //   "height": "400px",
    // });
    chart.draw(data, options);
}

/**
 * 折れ線グラフを描画する。
 * @param  {object}  [in]overviewData チャートデータ
 * @param  {string}  [in]title        タイトル
 * @return なし
 */
function DrawLineChart(overviewData, title){
    DEBUGLOG("DrawLineChart", "start", 0);
    
    var points =  [];
    var total= 0;
    
    Object.keys(overviewData).forEach(function(version) {
        var intArea = parseFloat(overviewData[version][title]);
        points.push([version, intArea]);
    });
    
    google.charts.load('current', {packages: ['corechart', 'line']});
    google.charts.setOnLoadCallback(function(){lineChart(points, title)});
}

/**
 * 折れ線グラフを描画処理を実行する。
 * @param  {object}  [in]chartData チャートデータ
 * @param  {string}  [in]title     タイトル
 * @return なし
 */
function lineChart(chartData, title) {
    DEBUGLOG("lineChart", "start", 0);
    
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

    var chart = new google.visualization.LineChart(document.getElementById(title+"LineChartContainer"));
    chart.draw(data, options);
}

/**
 * オブジェクトEmpty判定
 * @param  {object}  [in]obj 前回データ
 * @return {boolean} 判定結果(true:Emptyである,false:Emptyでない)
 */
function isEmpty(obj) {
    return !Object.keys(obj).length;
}

/**
 * バージョン名文字列をバージョン数を表す文字列のみに抽出し返す。
 * 引数のバージョン名文字列は、"*****.rvt(12)"のような形式であること
 * @param  {string}  [in]version_name バージョン名文字列
 * @return {string} verison_number    バージョンナンバー文字列
 */
function getSubstringVersionNumber(version_name) {
    DEBUGLOG("convertVersionString", "start", 0);
    
    var index = version_name.indexOf(".rvt(");
    var ret = version_name.substr(index+5);
    return ret.replace(")", "");
}

/**
 * バージョン名文字列からバージョン数情報を削除した文字列のみに抽出し返す。
 * 引数のバージョン名文字列は、"*****.rvt(12)"のような形式であること。
 * @param  {string}  [in]version_name バージョン名文字列
 * @return {string} verison_number    バージョンナンバー文字列
 */
function getSubstringVersionName(version_name) {
    DEBUGLOG("convertVersionString", "start", 0);
    
    var index = version_name.indexOf(".rvt(");
    var ret = version_name.substring(0, index+5);
    return ret.replace(")", "");
}

/**
 * モデルデータのエクセルダウンロードを実行する。
 * @param  {object}  [in]overviewData 概要データ
 * @param  {object}  [in]chartData    チャートデータ
 * @param  {object}  [in]fileName     ファイル名
 * @return なし
 */
function DownloadProcForgeData(overviewData, chartData, fileName){
    
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
