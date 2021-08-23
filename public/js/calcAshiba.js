/* ajax通信トークン定義 */
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

/* Placeholder名称定義 */
var PLACEHOLDER_NAME_FOLDER     = "Select Folder";
var PLACEHOLDER_NAME_PROJECT    = "Select Project";
var PLACEHOLDER_NAME_VERSION    = "Select Versions";
$(document).ready(function(){
    $.ajaxSetup({
        cache:false
    });
    
    var login_user_id = $("#hidLoginID").val();
    var img_src = "../public/image/JPG/クレーンアイコン.jpeg";
    var url = "addin/calcAshiba";
    var content_name = "足場算出";
    recordAccessHistory(login_user_id,img_src,url,content_name);
    
    $("#project").select2({
        placeholder:"Folders Loading..."
    });
    $("#item").select2({
        placeholder:"Project Loading..."
    });
    $("#version").select2({
        placeholder:"Version Loading..."
    });
    
    LoadComboData();
    //LoadImplementationDocInfo();


    $("#project").change(function() {
        ProjectChange();
    });
    $("#item").change(function() {
        ItemChange();
    });
    $("#version").change(function() {
       
    });

    if (location.hash !== '') $('a[href="' + location.hash + '"]').tab('show');
        return $('a[data-toggle="tab"]').on('shown', function(e) {
        return location.hash = $(e.target).attr('href').substr(1);
    });
    
});

/**
 * セレクトボックス内の項目をLoadする。
 * @param なし
 * @return なし
 */
function LoadComboData(){
    DEBUGLOG("LoadComboData", "start", 0);
    
    $.ajax({
        url: "../ashiba/getData",
        type: 'post',
        data:{_token: CSRF_TOKEN, message: 'getAshibaData'},
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

function ProjectChange(){
    DEBUGLOG("ProjectChange", "start", 0);

    var itemOption = "";
    var versionOption = "";
    var folderSelectedCount = $('#project option:selected').length;
    
    if(folderSelectedCount == 0) {
        
        LoadComboData();
        //flyToMap("");
        
    }else if(folderSelectedCount == 1) {
        var projectName = $('#project option:selected').text();
        console.log(projectName);
        
            
            $.ajax({
                url: "../ashiba/getData",
                type: 'post',
                data:{_token: CSRF_TOKEN,message:"getAshibaDataByProjectName",projectName:projectName,itemName:""},
                success :function(data) {
                    console.log("#########################Ashiba Data By ProjectName ########");
                    console.log(data);
                    if(data != null){
    
                        var chkResult = checkItemName(data["items"]);
                        console.log("chkResult");console.log(chkResult);
                        var authority_id = $("#hidAuthorityID").val();
                        console.log("hidAuthorityID");console.log(authority_id);
    
                        if(chkResult["isOneModel"] === false){
                            
                            if(authority_id !== "1"){
                                LoadComboData();
                                //flyToMap("");
                                alert("中央モデルが複数あります。\nモデルデータを参照できません。");
                                return;
                            }else{
                                BindComboData(data["items"],"item",PLACEHOLDER_NAME_PROJECT);
                                //BindComboData(data["versions"],"version",PLACEHOLDER_NAME_VERSION);
                                //BindComboData(data["levels"],"level",PLACEHOLDER_NAME_LEVEL);
                                //BindComboData(data["worksets"],"workset",PLACEHOLDER_NAME_WORKSET);
                                //BindComboData(data["materials"],"material",PLACEHOLDER_NAME_MATERIAL);
                                //BindComboData(data["familyNames"],"familyName",PLACEHOLDER_NAME_FAMILY);
                                //BindComboData(data["typeNames"],"typeName",PLACEHOLDER_NAME_TYPE);
                                
                                //$("#category").select2({placeholder:PLACEHOLDER_NAME_CATEGORY}).trigger('changed');
                            }
                        }else{
                            var items = chkResult["data"];
                            let long_name = items[0]["name"];
                            let element = $('#item');
                            let val = element.find("option:contains('"+long_name+"')").val();
                            element.val(val).trigger('change.select2');
            
                            //ItemChangeTmp(chkResult);
                        }
                        
                        //flyToMap(projectName);
                    }
                },
                error:function(err){
                    console.log(err);
                }
            });
    
    }else{
        //NOP
    }
}

function checkItemName(items){
    
    var result = {"isOneModel":false, "data":[]};
    var dataNum = items.length;

    if(dataNum === 0){
        result["isOneModel"] = false;
    }else if(dataNum === 1){
        var modelName = items[0]["name"];
        if(modelName.indexOf("#cen") === 0 || modelName.indexOf("cen") === 0 || modelName.indexOf("link") === 0){
            result["isOneModel"] = true;
            result["data"] = items;
        }
    }else{
        var modelNameList = [];
        var tmpItems = [];
        //get for all items that including "cen" or "link" in modelName
        for(var i=0; i < dataNum; i++){
            var modelName = items[i]["name"];
            if(modelName.indexOf("#cen") === 0 || modelName.indexOf("cen") === 0 || modelName.indexOf("link") === 0){
                modelNameList.push(items[i]);
            }
        }
        
        if(modelNameList.length > 0){ 
            result["isOneModel"] = true;
            result["data"] = modelNameList;
        }
    }
    
    return result;
}

function ItemChange(){
    DEBUGLOG("ItemChange", "start", 0);

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
            url: "../ashiba/getData",
            type: 'post',
            data:{_token: CSRF_TOKEN,message:"getAshibaDataVersionByProjectAndItem",projectName:projectName,itemName:itemName},
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

//Show Map
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