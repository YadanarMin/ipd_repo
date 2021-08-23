var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
var viewer;
var forge_token;
var current_item;
var previousVersion="";

function ShowModel(category=null){
    var valArray =JSON.parse($('#version option:selected').val());
    console.log("============previousVersion==================");
    console.log(valArray);
    if(category != null)
    if(previousVersion === valArray.forge_version_id)return;
   
    var urn = valArray.forge_version_id; 
     previousVersion = valArray.forge_version_id;
    
    token = GetTwoLeggedToken();
    //alert(token);
    forge_token = token;

    if(token == "" || token== null || token== undefined){
        var message = "Token is NULL.Please Try Again.";
        $("#spanText").text(message);
        return;
    }

    
    
    var options = {
        env: 'AutodeskProduction',       
        accessToken: token,
    };
    
    var documentId ="urn:"+base64encode(urn);
    Autodesk.Viewing.Initializer(options, function onInitialized(){
        Autodesk.Viewing.Document.load(documentId, onDocumentLoadSuccessV7, onDocumentLoadFailure);
    });
}

/*function onDocumentLoadSuccess(doc) {
    // A document contains references to 3D and 2D viewables.
    var viewables = Autodesk.Viewing.Document.getSubItemsWithProperties(
        doc.getRootItem(),
        {
          type: "geometry",
          role: "2d"
        },
        true
      );
    if (viewables.length === 0) {
        console.error('Document contains no viewables.');
        return;
    }else{
         viewerApp.selectItem(geometryItems[0], onItemLoadSuccess2, onItemLoadError);
         //add sheets to combo
         var option = "";
         $.each(viewables,function(index,item){
             var valueArr　= {"guid":item.guid,"role":item.role};
             option +="<option value="+JSON.stringify(item)+">"+item.name+"</option>";
         });
         $("#viewSheet").append(option);
    }

    // Choose any of the avialble viewables
    var initialViewable = viewables[0];
    var svfUrl = doc.getViewablePath(initialViewable);
    var modelOptions = {
        sharedPropertyDbPath: doc.getPropertyDbPath()
    };
    
    var viewerDiv = document.getElementById('modelViewer');
    viewer = new Autodesk.Viewing.Private.GuiViewer3D(viewerDiv);
    viewer.start(svfUrl, modelOptions, onLoadModelSuccess, onLoadModelError);
}*/

function GetTwoLeggedToken(){
    var result = "";
    $.ajax({
        url: "../bim360/getTwoLeggedToken",
        type: 'get',
        async:false,
        data:{_token: CSRF_TOKEN,message:"getTwoLeggedToken"},
        success :function(data) {
            if(data != null){
               result = data;
            }
        },
        error:function(err){
            console.log(err);
        }
    }); 
    return result;
}

function onDocumentLoadSuccessV7(doc) {
    var viewerDiv = document.getElementById('modelViewer');
    var config3d = {
         extensions: ["Autodesk.Viewing.MarkupsCore","KasetsuSelection"]
    };
    Autodesk.Viewing.theExtensionManager.registerExtension('ToolbarExtension', ToolbarExtension);
    Autodesk.Viewing.theExtensionManager.registerExtension('KasetsuSelection', KasetsuSelection);
    viewer = new Autodesk.Viewing.GuiViewer3D(viewerDiv,config3d);
    //viewer = new Autodesk.Viewing.Private.GuiViewer3D(viewerDiv,config3d);
     var startedCode = viewer.start();
    if (startedCode > 0) {
        console.error('Failed to create a Viewer: WebGL not supported.');
        return;
    }else{
        var viewables = doc.getRoot().search({'type':'geometry'});
        //var viewables = Autodesk.Viewing.Document.getSubItemsWithProperties(doc.getRootItem(), {'type':'geometry'}, true);
        console.log("=========viewables==============");
        console.log(viewables);
        var view = null;
        $.each(viewables,function(index, item) {
             console.log(item.data.name);
            if(item.data.name == "新しい建設"){
                view = item;
            }
        })
        if(view == null) view = viewables[0];
        viewer.loadDocumentNode(doc, view);
        if(viewer.isLoadDone()){

            //viewer.unload("ToolbarExtension");
            viewer.loadExtension('Autodesk.DocumentBrowser');
            viewer.loadExtension('Autodesk.VisualClusters');
            viewer.loadExtension("Autodesk.LayerManager");
            viewer.loadExtension("Autodesk.Viewing.MarkupsCore");
            viewer.loadExtension("ToolbarExtension");
            viewer.loadExtension('Autodesk.Fusion360.Animation');
            viewer.loadExtension('Autodesk.NPR');
            viewer.loadExtension('KasetsuSelection');
            viewer.addEventListener(Autodesk.Viewing.MODEL_ROOT_LOADED_EVENT, this.onLoadedBind);
            //viewer.loadExtension('Autodesk.VisualReports');
            
            
        }

            
    }
   

}

function onLoadedBind(){
    //alert("bind");
    console.log("loaded");
    //console.log(viewer.getLayerStates()());
    console.log("loaded extensions");
    console.log(viewer.getLoadedExtensions());
    viewer.loadExtension('Autodesk.VisualClusters');

    var fusion360 = viewer.getExtension("Autodesk.Fusion360.Animation");
    //var extNPR = viewer.getExtension("Autodesk.NPR");
    //extNPR.setParameter('style','pencil');
//markup.enterEditMode();
//markup.enterViewMode(); 
    //ShowModel(forge_token);
}
function onDocumentLoadSuccess(doc) {
    // A document contains references to 3D and 2D viewables.
    var viewables =doc.getRoot(); Autodesk.Viewing.Document.getSubItemsWithProperties(doc.getRoot(),{ type: "geometry"/*  role: "3d"*/}, true);
   
    
    /*if (viewables.length === 0 && current_item == undefined) {
        console.error('Document contains no viewables.');
        return;
    }else{
         //add sheets to combo
         var option = "";
         $.each(viewables,function(index,item){
             var tempArray = {"guid":item.guid,"role":item.role};
             option +="<option value="+JSON.stringify(tempArray)+">"+item.name+"</option>";
         });
         $("#viewSheet").append(option);
    }*/

    // Choose any of the avialble viewables
    var initialViewable;
    
    /*if(current_item == undefined || current_item == ""){
        initialViewable =  viewables[0];
    }else{
        $.each(viewables,function(index, view) {
            if(view.guid === current_item.guid){
                initialViewable = view;
                return;//break from loop
            }
        })
    } */
console.log(viewables);
    var svfUrl = doc.getViewablePath(viewables[0]);
    console.log(svfUrl);
    var config3d = {
      extensions: ['Autodesk.DocumentBrowser'],
    };
    
    var viewerDiv = document.getElementById('modelViewer');
    viewer = new Autodesk.Viewing.Private.GuiViewer3D(viewerDiv);

    viewer.start(svfUrl, onLoadModelSuccess, onLoadModelError);
   
}

function onDocumentLoadFailure(viewerErrorCode) {
    
    console.error('onDocumentLoadFailure() - errorCode:' + viewerErrorCode);
}

function onLoadModelSuccess(model) {
     //viewer.loadExtension('Autodesk.DocumentBrowser');
    console.log('onLoadModelSuccess()!');
    console.log('Validate model loaded: ' + (viewer.model === model));
    console.log(model);
}

function onLoadModelError(viewerErrorCode) {
    
    console.error('onLoadModelError() - errorCode:' + (viewerErrorCode));
    console.log(viewerErrorCode);
}

function base64encode(str) {
    var ret = "";
    if (window.btoa) {
        ret = window.btoa(str);
    } else {
        // IE9 support
        ret = window.Base64.encode(str);
    }
  
    // 終端の '=' 記号を削除するdataManagementHubs
    // 代わりに_を使用する/
    // +の代わりに-を使用する
    // Model Derivative APIで使用されている形式です
    // https://en.wikipedia.org/wiki/Base64#Variants_summary_table
    var ret2 = ret.replace(/=/g, '').replace(/[/]/g, '_').replace(/[+]/g, '-');
  
    console.log('base64encode result = ' + ret2);
  
    return ret2;
}

var instTree;
function ViewerHighLight(element_ids){
    //viewer.loadExtension('Autodesk.DocumentBrowser');
    var id_array = element_ids.split(',');//.map(Number);//string split to array and change to number
    //console.log(id_array);
    instTree = viewer.model.getData().instanceTree;
    //var ids = it.nodeAccess.dbIdToIndex();
    var idList = [];
    var count = 0;
    /* $.each(id_array,function(index, item) {
        //var id = viewer.search(item);
        // alert(id);
        viewer.search(item, function(dbIds){
            count++;
            $.each(dbIds,function(index, dbId) {
                idList.push(dbId);
            });
            //alert(idList);
            if(count == id_array.length){
               viewer.select(idList);
                viewer.isolate(idList); 
            }
            // idList.push(dbIds);
        //viewer.fitToView(dbIds);
        //viewer.select(dbIds);
       // viewer.isolate(dbIds);
          //callbackSearchIds(dbIds); // handle the results async
        }, function(error){
            // handle errors here...
        }, ['name'] // this array indicates the filter: search only on 'Name'
        )
     });*/

    
    var rootId = instTree.getRootId();
    var dbIds = getAlldbIds(rootId);
    $.each(dbIds,function(index, item) {
        var nodeFinalName = instTree.getNodeName(item);
        if(id_array.includes(nodeFinalName)){
            idList.push(item);
        }

    });
    //alert(idList);
    viewer.select(idList);
    viewer.isolate(idList);
    console.log("aaaaaa => "+idList.length);
    if(idList.length == 1){
        viewer.fitToView(idList);
    }
    //viewer.setGhosting(false);
    //viewer.clearSelection();
    //viewer.getObjectTree( propCallback, propErrorCallback);
    
    //viewer.search("8256824", searchCallback, searchErrorCallback);

    
}

var levelList = [];
function getLevelListForForgeModel(){
    
    console.log("getLevelListForForgeModel start");
    var totalDbIdList = [];
    levelList = [];

    instTreeProc = viewer.model.getData().instanceTree;
    var rootId = instTreeProc.getRootId();
    var dbIds = getAlldbIdsProcess(rootId);
    
    $.each(dbIds,function(index, item) {
        var nodeCategory = instTreeProc.getNodeName(item);
        var nodeNodeParentId = instTreeProc.getNodeParentId(item);

        if(index === 0 || !nodeCategory.includes('[')){
            return true;
        }

        totalDbIdList.push(item);
    });
    
    // forgeからレベルのリスト取得
    var filterList = ["基準レベル","参照レベル"];
    viewer.model.getBulkProperties(totalDbIdList, filterList, onBulkTotalIdPropSuccessCallback, onBulkTotalIdPropErrorCallback);
}

var conditionListCnt = 0;
var instTreeProc;
var dbIdList = [];
var procPropertyList = [];
var procProperties = {"sbs_category":"","sbs_family_name":"","sbs_type_name":"","sbs_kinou_name":"","level_title":"","level":0};
function ViewerProcessHighLight(conditionList){
    dbIdList = [];
    procPropertyList = [];

    instTreeProc = viewer.model.getData().instanceTree;
    var rootId = instTreeProc.getRootId();
    var dbIds = getAlldbIdsProcess(rootId);
    
    console.log("dbIds");console.log(dbIds);
    console.log("conditionList:"+conditionList.length);console.log(conditionList);
    
    var elementIdList = [];
    $.each(dbIds,function(index, item) {
        var nodeCategory = instTreeProc.getNodeName(item);
        var nodeNodeParentId = instTreeProc.getNodeParentId(item);

        if(index === 0 || !nodeCategory.includes('[')){
            return true;
        }

        var tmp = {"dbId":item,"parentId":nodeNodeParentId,"category":nodeCategory};
        elementIdList.push(tmp);
    });
    console.log("elementIdList");console.log(elementIdList);
    
    var parentIdList = [];
    conditionListCnt = 0;
    $.each(elementIdList,function(index, elementInfo) {
        var dbId = elementInfo['dbId'];
        var target_name = elementInfo['category'];
        var target_parentId = elementInfo['parentId'];

        var get_parentId = instTreeProc.getNodeParentId(target_parentId);
        var get_nodeType = instTreeProc.getNodeType(target_parentId);
        var get_nodeName = instTreeProc.getNodeName(get_parentId);
        var get_nodeType2 = instTreeProc.getNodeType(get_parentId);
        var get_childCount = instTreeProc.getChildCount(get_parentId);

        var get2_nodeParentId = -1;
        var get2_nodeName = "";
        if(get_parentId !== 1){
            get2_nodeParentId = instTreeProc.getNodeParentId(get_parentId);
            get2_nodeName = instTreeProc.getNodeName(get2_nodeParentId);
        }

        var tmp = { "tgt_parentId":target_parentId,"tgt_name":target_name,
                    "get_parentId":get_parentId,"get_nodeName":get_nodeName,
                    "get_nodeType(tgt_parentId)":get_nodeType,
                    "get_nodeType(get_parentId)":get_nodeType2,
                    "get2_nodeParentId":get2_nodeParentId,"get2_nodeName":get2_nodeName,
                    "get_childCount(get_parentId)":get_childCount,
        };
        parentIdList.push(tmp);
        
        var tgtCategory = get2_nodeName;
        var tgtFamily = get_nodeName;
        $.each(conditionList,function(index, conditions) {
            
            $.each(conditions["data"],function(index, condition) {
                
                procProperties = {"sbs_category":"","sbs_family_name":"","sbs_type_name":"","sbs_kinou_name":"","level_title":"","level":0};
                
                //category filter
                if(tgtCategory !== "" && tgtCategory.includes(condition["sbs_category"])){
                    
                    //family filter
                    if(tgtFamily.includes(condition["sbs_family_name"]) && condition["sbs_family_name"] !== "Model"){
                        // console.log("[CHECK]tgtFamily:"+tgtFamily+",condition['sbs_family_name']:"+condition["sbs_family_name"]);

                        if(condition["sbs_family_name"] !==  ""){
                            // console.log("[PASS]["+tmpCount+"]tgtFamily:"+tgtFamily+",condition['sbs_family_name']:"+condition["sbs_family_name"]);
                            
                            var levelCondition = getLevelForCondition(condition, conditions["level"]);
                            // console.log("levelCondition");console.log(levelCondition);
                            procProperties["dbId"] = dbId;
                            dbIdList.push(dbId);
                            procProperties["sbs_category"] = tgtCategory;
                            procProperties["sbs_family_name"] = tgtFamily;
                            procProperties["level_title"] = levelCondition["displayName"];
                            procProperties["level"] = levelCondition["displayValue"];
                            procProperties["sbs_type_name"] = condition["sbs_type_name"];
                            procProperties["sbs_kinou_name"] = condition["sbs_type_param"];
                            procPropertyList.push(procProperties);
                        }
    
                    }
                    
                }
                
            });
            
            conditionListCnt++;
        });
        
    });
    
    console.log("procPropertyList");console.log(procPropertyList);

    if(procPropertyList.length === 0){
        alert("該当する要素がありません。");
        return;
    }

    var filterList = ["タイプ名","基準レベル","参照レベル","機能"];
    viewer.model.getBulkProperties(dbIdList, filterList, onBulkPropSuccessCallback, onBulkPropErrorCallback);

}

function getLevelForCondition(condition, level){
    
    var result = {};
    var cond_id_1       = condition["process_code_1"];
    var cond_id_2;      // 未使用
    var cond_id_3       = condition["process_code_2"];
    var cond_id_4       = condition["process_code_3"];
    var cond_id_5       = condition["process_code_4"];
    
    result["displayName"] = "基準レベル";

    if(level === 0 || level === undefined){
        result["displayValue"] = 0;
        return result;
    }else{
        result["displayValue"] = level;
    }
    
    if( (cond_id_1 === 4 && cond_id_3 === 1 && cond_id_4 === 2)     //基礎梁
      ||(cond_id_1 === 5 && cond_id_3 === 1 && cond_id_4 === 1)     //構造鉄骨梁
      ||(cond_id_1 === 5 && cond_id_3 === 2 && cond_id_4 === 0 && cond_id_5 === 1)  //構造コンクリート1
      ||(cond_id_1 === 5 && cond_id_3 === 2 && cond_id_4 === 0 && cond_id_5 === 2)  //構造コンクリート2
      ||(cond_id_1 === 5 && cond_id_3 === 2 && cond_id_4 === 1)     //鉄筋コンクリート梁
      ){
        result["displayName"] = "参照レベル";
    }

    if( (cond_id_1 === 5 && cond_id_3 === 2 && cond_id_4 === 0 && cond_id_5 === 1)
      ||(cond_id_1 === 5 && cond_id_3 === 2 && cond_id_4 === 0 && cond_id_5 === 2)
      ||(cond_id_1 === 5 && cond_id_3 === 2 && cond_id_4 === 0 && cond_id_5 === 10)
      ||(cond_id_1 === 5 && cond_id_3 === 2 && cond_id_4 === 0 && cond_id_5 === 11)
      ||(cond_id_1 === 5 && cond_id_3 === 2 && cond_id_4 === 0 && cond_id_5 === 12)
      ||(cond_id_1 === 5 && cond_id_3 === 2 && cond_id_4 === 0 && cond_id_5 === 13)
      ||(cond_id_1 === 5 && cond_id_3 === 1 && cond_id_4 === 1)
      ||(cond_id_1 === 5 && cond_id_3 === 2 && cond_id_4 === 1)
      ||(cond_id_1 === 6)
      ){
        var levelNum = 0;
        var levelStr = level.toString() + "FL";
        var curIndex = levelList.indexOf(levelStr);
        
        if(curIndex !== -1){
            if(0 < levelList.length && curIndex+1 <= levelList.length){
                var isTika = false;
                var tmpStr = levelList[curIndex].replace("FL","");
                if(tmpStr.includes("B")){
                    isTika = true;
                    tmpStr = tmpStr.replace("B","");
                }
                levelNum = parseInt(tmpStr) + 1;
                levelNum = isTika ? levelNum * (-1): levelNum;
                
            }else{
                var isTika = false;
                var tmpStr = levelList[curIndex].replace("FL","");
                if(tmpStr.includes("B")){
                    isTika = true;
                    tmpStr = tmpStr.replace("B","");
                }
                levelNum = parseInt(tmpStr);
                levelNum = isTika ? levelNum * (-1): levelNum;
            }
        }
        
        if(levelNum === 0){
            result["displayValue"] = level;
        }else{
            result["displayValue"] = levelNum;
        }
        
        console.log("levelStr:"+levelStr+",curIndex:"+curIndex+",levelList.length:"+levelList.length+",levelNum:"+levelNum+",result['displayValue']:"+result["displayValue"]);
    }
    
    return result;
}

function onBulkTotalIdPropSuccessCallback(forgeTotalDbIdPropertyList){

    $.each(forgeTotalDbIdPropertyList,function(index, dbIdProperties) {
        var dbId = dbIdProperties["dbId"];
        var properties = dbIdProperties["properties"];
        
        $.each(properties,function(index, property) {
            if(property["displayName"] === "基準レベル" || property["displayName"] === "参照レベル"){
                if(levelList.indexOf(property["displayValue"]) === -1){
                    levelList.push(property["displayValue"]);
                }
            }
        });
    });
    
    levelList = sortLevelList(levelList);
    
    console.log("levelList");console.log(levelList);
}

function onBulkTotalIdPropErrorCallback(){}

function onBulkPropSuccessCallback(forgePropertyList){
    
    var idProcessList = [];
    console.log("forgePropertyList");console.log(forgePropertyList);
    
    $.each(forgePropertyList,function(index, dbIdProperties) {
        var dbId = dbIdProperties["dbId"];
        var properties = dbIdProperties["properties"];
        var levelName = "";
        var procTypeName = "";
        var procLevel = 0;
        var procKinou = "";
        var category = "";

        // 工程IDからデータの抽出(タイプ名,レベル,機能)
        $.each(procPropertyList,function(index, property) {
            if(property["dbId"] === dbId){

                levelName = property["level_title"];

                procTypeName = property["sbs_type_name"];
                procLevel = property["level"];
                procKinou = property["sbs_kinou_name"];
                
                category = property["sbs_category"];
            }
        });
        console.log("category:"+category+",procTypeName:"+procTypeName+",procLevel:"+procLevel+",levelName:"+levelName);

        var isMatchTypeName = {"isExists":false,"isMatch":false};
        var isMatchLevel = {"isExists":false,"isMatch":false};
        var isMatchKinou = {"isExists":false,"isMatch":false};
        $.each(properties,function(index, property) {
            var forgeTypeName = "";
            var forgeLevel = "";
            var forgeKinou = "";

            if(property["displayName"] === "タイプ名"){
                isMatchTypeName["isExists"] = true;
                forgeTypeName = property["displayValue"];

                if(procTypeName === "" || (forgeTypeName !== "" && forgeTypeName === procTypeName)){
                // if(forgeTypeName !== "" && forgeTypeName === procTypeName){
                    if(procTypeName !== ""){
                        // console.log("[forgeタイプ名]:"+forgeTypeName+" [procタイプ名]:"+procTypeName);
                    }
                    isMatchTypeName["isMatch"] = true;
                }
            }

            if(property["displayName"] === levelName){
                isMatchLevel["isExists"] = true;
                forgeLevel = property["displayValue"];
                // console.log("forgeLevel:"+forgeLevel);
                forgeLevel = forgeLevel.replace("FL","");
                var intForgelevel = 0;
                if(forgeLevel.includes("B")){
                    forgeLevel = forgeLevel.replace("B","");
                    intForgelevel = (-1) * parseInt(forgeLevel);
                }else{
                    intForgelevel = parseInt(forgeLevel);
                }
                // console.log("intForgelevel:"+intForgelevel+",procLevel:"+procLevel);

                // if(procLevel === 0 || (intForgelevel !== 0 && intForgelevel === procLevel)){
                if(intForgelevel !== 0 && intForgelevel === procLevel){
                    if(procLevel !== 0){
                        console.log("[forgeレベル]:"+intForgelevel+" [procレベル]:"+procLevel);
                    }
                    isMatchLevel["isMatch"] = true;
                }
            }

            if(property["displayName"] === "機能"){
                isMatchKinou["isExists"] = true;
                forgeKinou = property["displayValue"];

                if(procKinou === "" || (forgeKinou !== "" && forgeKinou === procKinou)){
                // if(forgeKinou !== "" && forgeKinou === procKinou){
                    if(procKinou !== ""){
                        // console.log("[forge機能]:"+forgeKinou+" [proc機能]:"+procKinou);
                    }
                    isMatchKinou["isMatch"] = true;
                }
            }

        });

        // console.log("isMatchTypeName");console.log(isMatchTypeName);
        // console.log("isMatchLevel");console.log(isMatchLevel);
        // console.log("isMatchKinou");console.log(isMatchKinou);
        
        // 比較結果判定
        if( (!isMatchTypeName["isExists"] || isMatchTypeName["isMatch"])
            && (!isMatchLevel["isExists"] || isMatchLevel["isMatch"])
            && (!isMatchKinou["isExists"] || isMatchKinou["isMatch"])
          ){
            idProcessList.push(dbId);
        }
    });
    
    console.log("conditionListCnt");console.log(conditionListCnt);
    
    console.log("idProcessList");console.log(idProcessList);
    if(idProcessList.length === 0){
        alert("該当する要素がありません。");
    }else{
        viewer.select(idProcessList);
        viewer.isolate(idProcessList);
        if(idProcessList.length == 1){
            viewer.fitToView(idProcessList);
        }
    }
}

function onBulkPropSuccessCallback_new(forgePropertyList){
    
    var idProcessList = [];
    console.log("forgePropertyList");console.log(forgePropertyList);
    
    $.each(procPropertyList,function(index, property) {
        var levelName = property["level_title"];
        var procTypeName = property["sbs_type_name"];
        var procLevel = property["level"];
        var procKinou = property["sbs_kinou_name"];
        var category = property["sbs_category"];
        
        
    });

    console.log("conditionListCnt");console.log(conditionListCnt);
    
    console.log("idProcessList");console.log(idProcessList);
    if(idProcessList.length === 0){
        alert("該当する要素がありません。");
    }else{
        viewer.select(idProcessList);
        viewer.isolate(idProcessList);
        if(idProcessList.length == 1){
            viewer.fitToView(idProcessList);
        }
    }
}

function onBulkPropErrorCallback(){}

function compareFunc(a, b) {
  return a - b;
}

function sortLevelList(levelList){
    
    if(levelList.length === 0){
        return levelList;
    }
    
    var result = [];
    var tmpOkujoList = [];
    var tmpTikaList = [];
    var tmpTijoList = [];
    var tmpOther = [];
    $.each(levelList,function(index, level) {
        if(level.indexOf("B") === 0){
            tmpTikaList.push(level);
        }else if(level.includes("RFL")){
            tmpOkujoList.push(level);
        }else if(level.includes("FL")){
            var tmp = level;
            tmp = tmp.replace("FL", "");
            tmpTijoList.push(parseInt(tmp));
        }else{
            tmpOther.push(level);
        }
    });

    $.each(tmpOther,function(index, other) {
        result.push(other);
    });
    $.each(tmpTikaList,function(index, tika) {
        result.push(tika);
    });
    tmpTijoList.sort(compareFunc);
    $.each(tmpTijoList,function(index, tijo) {
        result.push(tijo.toString() + "FL");
    });
    $.each(tmpOkujoList,function(index, okujo) {
        result.push(okujo);
    });

    return result;
}

function getAlldbIds (rootId) {
	var alldbId = [];
	if (!rootId) {
		return alldbId;
	}
	var queue = [];
	queue.push(rootId);
	while (queue.length > 0) {
		var node = queue.shift();
		alldbId.push(node);
		instTree.enumNodeChildren(node, function(childrenIds) {
			queue.push(childrenIds);
		});
	}
	return alldbId;
}

function getAlldbIdsProcess(rootId) {
	var alldbId = [];
	if (!rootId) {
		return alldbId;
	}
	var queue = [];
	queue.push(rootId);
	while (queue.length > 0) {
		var node = queue.shift();
		alldbId.push(node);
		instTreeProc.enumNodeChildren(node, function(childrenIds) {
			queue.push(childrenIds);
		});
	}
	return alldbId;
}

var idList;
function callbackSearchIds(dbIds){
    $.each(dbIds,function(index, item) {
        idList.push(item);
    })
}
// Callback for view.getProperties() on success.
function propCallback(data) {
    // Check if we got properties.
   console.log((data));
}
function propErrorCallback(data) {
    alert("error");
}

/**
 * 3D又は2Dの表示データが存在する場合返却する
 */
function getGeometryItems(rootItem) {
    var geometryItems = [];
    // 3Dデータを取得
    geometryItems = Autodesk.Viewing.Document.getSubItemsWithProperties(
        rootItem,
        {
            type: "geometry"
        },
        true
    );
    
    return geometryItems;
}


// Callback for _viewer.search() on success.
function searchCallback(ids) {
    if (ids.length > 0) {
        viewer.isolate(ids);
        viewer.select(ids);
        viewer.fitToView(ids);
        alert("id====>"+ids);
        // outputTextArea.value = ids;
    }else{
        console.log("nothing found.");
    }
}
// Callback for _viewer.search() on error.
function searchErrorCallback() {
    alert("error in search") ;//“error in search().”;
}

function SheetChange(sheet){
    current_item = JSON.parse(sheet.value);
    //alert(JSON.stringify(current_item));
    ShowModel(forge_token);
}


// //Testing external extensions by KWH
// function MyAwesomeExtension(viewer, options) {
//   Autodesk.Viewing.Extension.call(this, viewer, options);
// }

// MyAwesomeExtension.prototype = Object.create(Autodesk.Viewing.Extension.prototype);
// MyAwesomeExtension.prototype.constructor = MyAwesomeExtension;

// MyAwesomeExtension.prototype.load = function() {
//   alert('MyAwesomeExtension is loaded!');
//   return true;
// };

// MyAwesomeExtension.prototype.unload = function() {
//   alert('MyAwesomeExtension is now unloaded!');
//   return true;
// };

