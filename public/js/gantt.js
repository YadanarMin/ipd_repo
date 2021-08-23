/* ajax通信トークン定義 */
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
var contextPath = "";

var ge;
$(function() {
    ShowLoading();

    var canWrite=true; //this is the default for test purposes
    
    // here starts gantt initialization
    ge = new GanttMaster();
    ge.set100OnClose=true;
    
    ge.shrinkParent=true;
    
    ge.init($("#workSpace"));
    loadI18n(); //overwrite with localized ones
    
    //in order to force compute the best-fitting zoom level
    delete ge.gantt.zoom;
    var project=loadFromLocalStorage();
    
    if (!project.canWrite){
        $(".ganttButtonBar button.requireWrite").attr("disabled","true");
    }
    
    ge.loadProject(project);
    ge.checkpoint(); //empty the undo stack
    
    initializeHistoryManagement(ge.tasks[0].id);
    
    setTimeout('HideLoading();', 5000);
    
});

$(document).ready(function(){
    $.ajaxSetup({
        cache:false
    });
    
    loadModel();
    
    setTimeout('getLevelListForForgeModel();', 15000);
    
    $(document).on('click','.gdfCell',function(){

        var processTitle = $(this).find("input").val();
        
        getHighlightCondition(processTitle);
        
    });
});

function getHighlightCondition(processTitle){
    
    var conditionList = [];
    var ajaxCnt = 0;
    
    var idList = getProcessID(processTitle);
    if(idList.length === 0){
        return;
    }
    
    for (var i = 0; i < idList.length; i++) {
        var condition = {};
        
        if(idList[i].length === 11){
            condition["id_1"] = parseInt(idList[i].substring(0,2));
            condition["id_2"] = parseInt(idList[i].substring(3,5));
            condition["id_3"] = parseInt(idList[i].substring(6,8));
            condition["id_4"] = parseInt(idList[i].substring(9,11));
        }else{
            var levelStr = idList[i].substring(3,6);
            if(levelStr[0] !== "B"){
                continue;   // 工程IDのフォーマットエラーのためスキップ
            }
            condition["id_1"] = parseInt(idList[i].substring(0,2));
            condition["id_2"] = (-1) * parseInt(levelStr.substring(1));
            condition["id_3"] = parseInt(idList[i].substring(7,9));
            condition["id_4"] = parseInt(idList[i].substring(10,12));
        }
        console.log("condition");console.log(condition);

        $.ajax({
            url: "../processMapping/getData",
            type: 'post',
            data:{_token: CSRF_TOKEN,message:"getDataByID",condition:condition},
            success :function(data) {
                if(data != null){
                	console.log(data);
                	if(data !== []){
                        conditionList.push(data);
                	}
                    
                    ajaxCnt++;
                    if(ajaxCnt === idList.length){
                        console.log("target");console.log(conditionList);
                        ViewerProcessHighLight(conditionList);
                    }
                }
            },
            error:function(err){
                console.log(err);
            }
        });
    }
}

function getProcessID(processTitle){

    // processTitle = "【06_00_00_00】【05_02_01_02】【09_01_02_03】あいうえお";

    var idList = [];
    var NUM_OF_STRING_PROCESS_ID = 11;

    var count1 = processTitle.length === 0 ? 0 : (processTitle.match( /【/g ) || []).length;
    var count2 = processTitle.length === 0 ? 0 : (processTitle.match( /】/g ) || []).length;
    
    if(count1 !== count2){

        alert("工程ID取得エラー\nフォーマットを確認してください。");

    }else if(count1 === 0 && count2 === 0){

        alert("工程ID取得エラー\n工程IDが付与されていません。");

    }else{
        var tmpIdList = [];
        var tmpStr = processTitle;
        
        for (var i = 0; i < count1; i++) {
            var startIdx = tmpStr.indexOf("【");
            var endIdx = tmpStr.indexOf("】");
            tmpIdList.push(tmpStr.substring(startIdx+1,endIdx));
            tmpStr = tmpStr.slice(endIdx+1);
        }
        
        for (var i = 0; i < tmpIdList.length; i++) {
            if(tmpIdList[i].length === NUM_OF_STRING_PROCESS_ID || tmpIdList[i].length === NUM_OF_STRING_PROCESS_ID+1){
                idList.push(tmpIdList[i]);
            }
        }

        if(idList.length === 0){
            alert("工程ID取得エラー\nフォーマットを確認してください。");
        }
        
        console.log("tmpIdList");console.log(tmpIdList);
        console.log("idList");console.log(idList);
    }
    
    return idList;
}

function loadModel(){
    
    var projectName = $("#hidProjectName").val();

    $.ajax({
        url: "../forge/getData",
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"getVersionsDataByProject",projectName:projectName,itemName:""},
        success :function(data) {
            // console.log(data);
            if(data != null){
                var latestData = data["versions"][0];
                // console.log(latestData);
                
                BindComboData(latestData,"version","Select Versions");
                
                let long_name = latestData["name"]+"("+latestData["version_number"]+")";
                let element = $('#version');
                let val = element.find("option:contains('"+long_name+"')").val();
                element.val(val).trigger('change.select2');

                ShowModel();
            }
        },
        error:function(err){
            console.log(err);
        }
    });
}

function BindComboData(data,comboId,placeholder){

    var appendText = "";
    var fileName =  data["name"]+"("+data["version_number"]+")";
    appendText +="<option value='"+JSON.stringify(data)+"'>"+fileName+"</option>";
    
    $("select#"+comboId+" option").remove();
    $("#"+comboId).append(appendText).select2({placeholder:placeholder}).trigger('changed');
}

function getDemoProject(){
    //console.debug("getDemoProject")
    ret= {"tasks":    [
        {"id": -1, "name": "Gantt editor", "progress": 0, "progressByWorklog": false, "relevance": 0, "type": "", "typeId": "", "description": "", "code": "", "level": 0, "status": "STATUS_ACTIVE", "depends": "", "canWrite": true, "start": 1396994400000, "duration": 20, "end": 1399586399999, "startIsMilestone": false, "endIsMilestone": false, "collapsed": false, "assigs": [], "hasChild": true},
        {"id": -2, "name": "coding", "progress": 0, "progressByWorklog": false, "relevance": 0, "type": "", "typeId": "", "description": "", "code": "", "level": 1, "status": "STATUS_ACTIVE", "depends": "", "canWrite": true, "start": 1396994400000, "duration": 10, "end": 1398203999999, "startIsMilestone": false, "endIsMilestone": false, "collapsed": false, "assigs": [], "hasChild": true},
        {"id": -3, "name": "gantt part", "progress": 0, "progressByWorklog": false, "relevance": 0, "type": "", "typeId": "", "description": "", "code": "", "level": 2, "status": "STATUS_ACTIVE", "depends": "", "canWrite": true, "start": 1396994400000, "duration": 2, "end": 1397167199999, "startIsMilestone": false, "endIsMilestone": false, "collapsed": false, "assigs": [], "hasChild": false},
        {"id": -4, "name": "editor part", "progress": 0, "progressByWorklog": false, "relevance": 0, "type": "", "typeId": "", "description": "", "code": "", "level": 2, "status": "STATUS_SUSPENDED", "depends": "3", "canWrite": true, "start": 1397167200000, "duration": 4, "end": 1397685599999, "startIsMilestone": false, "endIsMilestone": false, "collapsed": false, "assigs": [], "hasChild": false},
        {"id": -5, "name": "testing", "progress": 0, "progressByWorklog": false, "relevance": 0, "type": "", "typeId": "", "description": "", "code": "", "level": 1, "status": "STATUS_SUSPENDED", "depends": "2:5", "canWrite": true, "start": 1398981600000, "duration": 5, "end": 1399586399999, "startIsMilestone": false, "endIsMilestone": false, "collapsed": false, "assigs": [], "hasChild": true},
        {"id": -6, "name": "test on safari", "progress": 0, "progressByWorklog": false, "relevance": 0, "type": "", "typeId": "", "description": "", "code": "", "level": 2, "status": "STATUS_SUSPENDED", "depends": "", "canWrite": true, "start": 1398981600000, "duration": 2, "end": 1399327199999, "startIsMilestone": false, "endIsMilestone": false, "collapsed": false, "assigs": [], "hasChild": false},
        {"id": -7, "name": "test on ie", "progress": 0, "progressByWorklog": false, "relevance": 0, "type": "", "typeId": "", "description": "", "code": "", "level": 2, "status": "STATUS_SUSPENDED", "depends": "6", "canWrite": true, "start": 1399327200000, "duration": 3, "end": 1399586399999, "startIsMilestone": false, "endIsMilestone": false, "collapsed": false, "assigs": [], "hasChild": false},
        {"id": -8, "name": "test on chrome", "progress": 0, "progressByWorklog": false, "relevance": 0, "type": "", "typeId": "", "description": "", "code": "", "level": 2, "status": "STATUS_SUSPENDED", "depends": "6", "canWrite": true, "start": 1399327200000, "duration": 2, "end": 1399499999999, "startIsMilestone": false, "endIsMilestone": false, "collapsed": false, "assigs": [], "hasChild": false}
    ], "selectedRow": 7, "deletedTaskIds": [],
        "resources": [
        {"id": "tmp_1", "name": "Resource 1"},
        {"id": "tmp_2", "name": "Resource 2"},
        {"id": "tmp_3", "name": "Resource 3"},
        {"id": "tmp_4", "name": "Resource 4"}
    ],
        "roles":       [
        {"id": "tmp_1", "name": "Project Manager"},
        {"id": "tmp_2", "name": "Worker"},
        {"id": "tmp_3", "name": "Stakeholder"},
        {"id": "tmp_4", "name": "Customer"}
    ], "canWrite":    true, "canDelete":true, "canWriteOnParent": true, canAdd:true}
    
    //actualize data
    // var offset=new Date().getTime()-ret.tasks[0].start;
    // for (var i=0;i<ret.tasks.length;i++) {
    //     ret.tasks[i].start = ret.tasks[i].start + offset;
    // }
    return ret;
}

function getProject(){

    var ret = {}
    var pj_name = $("#hidProjectName").val();
    var fileName = "/var/www/html/iPD/app/Exports/Template/GanttTemp.json";

    $.ajax({
        url: "../gantt/getData",
        async:false,
        type: 'post',
        data:{_token: CSRF_TOKEN,isTemp:0,pj_name:pj_name,fileName:fileName},
        success :function(data) {
            if(data != null){
                console.log(data);
                // console.log(data[0]["gantt_data"]);
                if(data[0]["gantt_data"]){
                    ret = JSON.parse(data[0]["gantt_data"]);
                }else{
                    alert("セッションの有効期限が切れました。\n再読込してください。");
                }
                // ret = JSON.parse(data);

                //actualize data
                // var offset=new Date().getTime()-ret.tasks[0].start;
                // for (var i=0;i<ret.tasks.length;i++) {
                //     ret.tasks[i].start = ret.tasks[i].start + offset;
                // }
            }
        },
        error:function(err){
            console.log(err);
            setTimeout('HideLoading();', 5000);
            alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });

    return ret;
}



function loadGanttFromServer(taskId, callback) {

    //this is a simulation: load data from the local storage if you have already played with the demo or a textarea with starting demo data
    var ret=loadFromLocalStorage();

    //this is the real implementation
    /*
    //var taskId = $("#taskSelector").val();
    var prof = new Profiler("loadServerSide");
    prof.reset();

    $.getJSON("ganttAjaxController.jsp", {CM:"LOADPROJECT",taskId:taskId}, function(response) {
        //console.debug(response);
        if (response.ok) {
            prof.stop();
            
            ge.loadProject(response.project);
            ge.checkpoint(); //empty the undo stack
            
            if (typeof(callback)=="function") {
                callback(response);
            }
        } else {
            jsonErrorHandling(response);
        }
    });
    */

    return ret;
}

function uploadJSON(uploadedJSONFile) {
    var fileread = new FileReader();
    
    fileread.onload = function(e) {
        var content = e.target.result;
        var intern = JSON.parse(content); // Array of Objects.
        // console.log("intern");
        // console.log(intern);
        //console.log(intern); // You can index every object
        
        ge.loadProject(intern);
        ge.checkpoint(); //empty the undo stack

    };

    fileread.readAsText(uploadedJSONFile);
}

function uploadCSV(fileread, uploadedFile) {
    // console.log("uploadCSV() start");
    
    const INDEX_PROC_NAME  = 3;   // 工程名Index定数
    const INDEX_START_DATE = 5;   // 開始日Index定数
    const INDEX_END_DATE   = 7;   // 終了日Index定数
    const INDEX_ACTUAL_DATE= 9;   // 実働日数Index定数
    var procNameList  = [];
    var startDateList = [];
    var endDateList   = [];
    var actualDateList= [];
    
    // console.log("FileReader start");

    fileread.onload = function(e) {
        // console.log("fileread.onload start");
        var content = e.target.result;
        // var intern = JSON.parse(content); // Array of Objects.
        // console.log(content); // You can index every object

        // var text  = content.replace(/\r\n|\r/g, "\n");
        var lines = content.split('\n');

        // console.log(content);
        // console.log(lines);
        // console.log("lines.length:"+lines.length);

        var startPreIndex = 1000000;
        var tmpProcName = "";
        var isEntry = true;
        var isFirst = false;
        var isEnd = false;

        for(var i = 0; i < lines.length; i++){

            var value = splitLineString(lines[i]);
            var value_procName   = value[INDEX_PROC_NAME];
            var value_startDate  = value[INDEX_START_DATE];
            var value_endDate    = value[INDEX_END_DATE];
            var value_actualDate = value[INDEX_ACTUAL_DATE];
            
            if(isEnd){
                tmpProcName = "";
                isEnd = false;
            }

            if(value[0].trim() == '</工程>'){
                break;
            }

            if(i >= (startPreIndex + 2)){
                
                // ["]の文字数取得
                var dqStrCnt = ( lines[i].match(/"/g) || [] ).length;

                if(dqStrCnt == 1){

                    if(isEntry){
                        /* ["]の始まり検出 */
                        //行内に["]が一つしかないため、["]と["]の間の文字列内に改行コードが入っている
                        //リスト登録フラグを登録なしに変更
                        isEntry = false;
                        isFirst = true;
                    }else{
                        /* ["]の終わり検出 */
                        //行内に["]が一つしかない且つ、前回までに["]の始まり検出済み
                        //終わりの場合は、必ず配列の先頭内に["]が存在する
                        var offset = 3;
                        value_procName   = tmpProcName + value[INDEX_PROC_NAME - offset];
                        value_startDate  = value[INDEX_START_DATE - offset];
                        value_endDate    = value[INDEX_END_DATE - offset];
                        value_actualDate = value[INDEX_ACTUAL_DATE - offset];
                        // console.log("[LAST]value_procName:"+value_procName);
                        
                        //リスト登録フラグを登録ありに変更    
                        isEntry = true;
                        isEnd = true;
                    }
                }else{
                    isFirst = false;
                }
                
                if(isEntry){
                    /* 通常ルート(改行コードで文字列分割ミスなし) */
                    if(value_procName.slice(0, 1) === "\""){
                        console.log(value_procName);
                        value_procName = value_procName.slice(1) ;
                        value_procName = value_procName.slice(0,-1);
                        value_procName = value_procName.replace(/\n/g, "&[改行]");
                        console.log(value_procName);
                    }
                    procNameList.push(value_procName);
                    startDateList.push(value_startDate);
                    endDateList.push(value_endDate);
                    actualDateList.push(value_actualDate);
                }else{
                    //一つのセル内に改行コード使用あり
                    //["]と["]の間の文字列は必ず配列長さが1になる
                    if(!isFirst){
                        value_procName = value[0];
                    }
                    tmpProcName = tmpProcName == "" ? tmpProcName : tmpProcName + "\n";    //初回以外は改行コードで連結
                    tmpProcName += value_procName;
                    // console.log("value_procName:"+value_procName);
                    // console.log("tmpProcName:"+tmpProcName);
                }
            }
            
            // if(i == 547){
            //     console.log("target proc name:"+value[INDEX_PROC_NAME]);
            //     console.log("target start date:"+value[INDEX_START_DATE]);
            //     console.log("target end date:"+value[INDEX_END_DATE]);
            //     console.log("dqStrCnt:"+dqStrCnt);
            //     console.log("line:"+lines[i]);
            // }
            
            if(value[0].trim() == "<工程>"){
                startPreIndex = i;
            }
        }

        console.log(procNameList);
        console.log(startDateList);
        console.log(endDateList);
        console.log(actualDateList);
        
        // 一時JSONファイルに値を挿入
        // ret= {"tasks": [
        //                 {"id": -1, "name": "Gantt editor", "progress": 0, "progressByWorklog": false, "relevance": 0, "type": "", "typeId": "", "description": "", "code": "", "level": 0, "status": "STATUS_ACTIVE", "depends": "", "canWrite": true, "start": 1396994400000, "duration": 20, "end": 1399586399999, "startIsMilestone": false, "endIsMilestone": false, "collapsed": false, "assigs": [], "hasChild": true},
        //                 {"id": -2, "name": "coding", "progress": 0, "progressByWorklog": false, "relevance": 0, "type": "", "typeId": "", "description": "", "code": "", "level": 1, "status": "STATUS_ACTIVE", "depends": "", "canWrite": true, "start": 1396994400000, "duration": 10, "end": 1398203999999, "startIsMilestone": false, "endIsMilestone": false, "collapsed": false, "assigs": [], "hasChild": true},
        //                 {"id": -3, "name": "gantt part", "progress": 0, "progressByWorklog": false, "relevance": 0, "type": "", "typeId": "", "description": "", "code": "", "level": 2, "status": "STATUS_ACTIVE", "depends": "", "canWrite": true, "start": 1396994400000, "duration": 2, "end": 1397167199999, "startIsMilestone": false, "endIsMilestone": false, "collapsed": false, "assigs": [], "hasChild": false},
        //                 {"id": -4, "name": "editor part", "progress": 0, "progressByWorklog": false, "relevance": 0, "type": "", "typeId": "", "description": "", "code": "", "level": 2, "status": "STATUS_SUSPENDED", "depends": "3", "canWrite": true, "start": 1397167200000, "duration": 4, "end": 1397685599999, "startIsMilestone": false, "endIsMilestone": false, "collapsed": false, "assigs": [], "hasChild": false},
        //                 {"id": -5, "name": "testing", "progress": 0, "progressByWorklog": false, "relevance": 0, "type": "", "typeId": "", "description": "", "code": "", "level": 1, "status": "STATUS_SUSPENDED", "depends": "2:5", "canWrite": true, "start": 1398981600000, "duration": 5, "end": 1399586399999, "startIsMilestone": false, "endIsMilestone": false, "collapsed": false, "assigs": [], "hasChild": true},
        //                 {"id": -6, "name": "test on safari", "progress": 0, "progressByWorklog": false, "relevance": 0, "type": "", "typeId": "", "description": "", "code": "", "level": 2, "status": "STATUS_SUSPENDED", "depends": "", "canWrite": true, "start": 1398981600000, "duration": 2, "end": 1399327199999, "startIsMilestone": false, "endIsMilestone": false, "collapsed": false, "assigs": [], "hasChild": false},
        //                 {"id": -7, "name": "test on ie", "progress": 0, "progressByWorklog": false, "relevance": 0, "type": "", "typeId": "", "description": "", "code": "", "level": 2, "status": "STATUS_SUSPENDED", "depends": "6", "canWrite": true, "start": 1399327200000, "duration": 3, "end": 1399586399999, "startIsMilestone": false, "endIsMilestone": false, "collapsed": false, "assigs": [], "hasChild": false},
        //                 {"id": -8, "name": "test on chrome", "progress": 0, "progressByWorklog": false, "relevance": 0, "type": "", "typeId": "", "description": "", "code": "", "level": 2, "status": "STATUS_SUSPENDED", "depends": "6", "canWrite": true, "start": 1399327200000, "duration": 2, "end": 1399499999999, "startIsMilestone": false, "endIsMilestone": false, "collapsed": false, "assigs": [], "hasChild": false}
        //               ],
        var jsonData= {
            "deletedTaskIds": [],
            "resources": [
                            // {"id": "tmp_1", "name": "Resource 1"},
                            // {"id": "tmp_2", "name": "Resource 2"},
                            // {"id": "tmp_3", "name": "Resource 3"},
                            // {"id": "tmp_4", "name": "Resource 4"}
                         ],
            "roles":       [
                            // {"id": "tmp_1", "name": "Project Manager"},
                            // {"id": "tmp_2", "name": "Worker"},
                            // {"id": "tmp_3", "name": "Stakeholder"},
                            // {"id": "tmp_4", "name": "Customer"}
                          ],
            "canWrite": true,
            "canDelete":true,
            "canWriteOnParent": true,
            canAdd:true
        }
        
        var taskList = [];
        for(var i = 0; i < procNameList.length; i++){
            var task = {};      //{"id": -1, "name": "Gantt editor", "progress": 0, "progressByWorklog": false, "relevance": 0, "type": "",
                                // "typeId": "", "description": "", "code": "", "level": 0, "status": "STATUS_ACTIVE", "depends": "",
                                // "canWrite": true, "start": 1396994400000, "duration": 20, "end": 1399586399999, "startIsMilestone": false,
                                // "endIsMilestone": false, "collapsed": false, "assigs": [], "hasChild": true}

            task["id"]                  = (-1)*(i+1);
            task["name"]                = procNameList[i];
            task["progress"]            = 0;
            task["progressByWorklog"]   = false;
            task["relevance"]           = 0;
            task["type"]                = "";
            task["typeId"]              = "";
            task["description"]         = "";
            task["code"]                = "";
            task["level"]               = 1;
            task["status"]              = "STATUS_ACTIVE";
            task["depends"]             = "";
            var startUnixtime = convertString2Unixtime(startDateList[i], (i+1), procNameList[i]);
            task["start"]               = startUnixtime;
            task["duration"]            = actualDateList[i];
            var endUnixtime = convertString2Unixtime(endDateList[i], (i+1), procNameList[i]);
            task["end"]                 = endUnixtime;
            task["startIsMilestone"]    = false;
            task["endIsMilestone"]      = false;
            task["canWrite"]            = true;
            task["canAdd"]              = true;
            task["canDelete"]           = true;
            task["canAddIssue"]         = true;
            task["assigs"]              = [];
            
            taskList.push(task);
        }
        
        jsonData["tasks"] = taskList;
        jsonData["selectedRow"] = procNameList.length - 1;

        // var ganttData = ge.saveProject();
        // var _ganttJSON = JSON.stringify(ganttData, null, '\t');
        var gantt_data = JSON.stringify(jsonData, null, '\t');
        var pj_code = "";
        var pj_name = "";
        var fileName = "/var/www/html/iPD/app/Exports/Template/GanttTemp.json";

        $.ajax({
            url: "../gantt/putData",
            async:false,
            type: 'post',
            data:{_token: CSRF_TOKEN,isTemp:1,pj_code:pj_code,pj_name:pj_name,gantt_data:gantt_data,fileName:fileName},
            success :function(response) {
                // console.log(response);
                if(response !== false){

                    var ret = {}
                    $.ajax({
                        url: "../gantt/getData",
                        async:false,
                        type: 'post',
                        data:{_token: CSRF_TOKEN,isTemp:1,pj_name:"",fileName:fileName},
                        success :function(data) {
                            if(data != null){
                                console.log(data);
                                ret = JSON.parse(data);
                
                                //actualize data
                                // var sec = new Date().getTime();
                                // var offset=sec-ret.tasks[0].start;
                                // for (var i=0;i<ret.tasks.length;i++) {
                                //     ret.tasks[i].start = ret.tasks[i].start + offset;
                                // }
                                
                                if (!ret.canWrite){
                                    $(".ganttButtonBar button.requireWrite").attr("disabled","true");
                                }
                                
                                ge.loadProject(ret);
                                ge.checkpoint(); //empty the undo stack
                                
                                initializeHistoryManagement(ge.tasks[0].id);
                            }
                        },
                        error:function(err){
                            console.log(err);
                        }
                    });
                    
                    // ge.loadProject(intern);
                    // ge.checkpoint(); //empty the undo stack
    
                    // fileread.readAsText(fileName, 'shift-jis');
                }
            },
            error:function(err){
                console.log(err);
            }
        });


    };

}

function splitLineString(line){
    
    var ret = [];
    
    // 文字数カウント:ダブルクォーテーション
    var array = line.match(/\"/g);
    var matchCnt = array == null ? 0: array.length;
    
    // CSV形式のセルごとの情報を抽出
    if(matchCnt <= 1){
        ret = line.split(',');
    }else{
        var arr = line.split(',"');
        for(var i=0; i < arr.length; i++){
            
            if(arr[i].indexOf('"') == -1){
                var tmpArr = arr[i].split(',');
                for(var k=0; k < tmpArr.length; k++){
                    ret.push(tmpArr[k]);
                }
            }else{
                var lastStr = arr[i].slice(-1);
                if(lastStr == '"'){
                    ret.push(arr[i].replace('"',''));
                }else{
                    var tempArr = arr[i].split('",');
                    ret.push(tempArr[0]);
    
                    var tempArr2nd = tempArr[1].split(',');
                    for(var k=0; k < tempArr2nd.length; k++){
                        ret.push(tempArr2nd[k]);
                    }
                }
            }
        }
    }
    
    return ret;
}

function convertString2Unixtime(timeStr, id, procName){
    var ret = 0;
    // console.log("["+id.toString()+"]["+procName+"]timeStr:"+timeStr);
    
    if(timeStr.includes("/")){
        var prefixStr = "";
        // var prefix2 = timeStr.slice(0,2);
        // if(prefix2 != "20"){
        //     prefixStr = "20";
        // }
        prefixStr = "20";
        
        var formattedStr = prefixStr + timeStr.replace('/', '-');
        formattedStr = formattedStr.replace('/', '-');
        formattedStr = formattedStr + " 0:0:0";
        ret = Date.parse(formattedStr);
    }
    
    // console.log("ret:"+ret.toString());
    return ret;
}

function saveGanttOnServer() {
    ShowLoading();
  
    var ganttData = ge.saveProject();
    var gantt_data = JSON.stringify(ganttData, null, '\t');
    var pj_code = $("#hidProjectCode").val();
    var pj_name = $("#hidProjectName").val();
    var fileName = "";
    // console.log(gantt_data);

    $.ajax({
        url: "../gantt/putData",
        async:true,
        type: 'post',
        data:{_token: CSRF_TOKEN,isTemp:0,pj_code:pj_code,pj_name:pj_name,gantt_data:gantt_data,fileName:fileName},
        success :function(response) {
            // console.log(response);
            if(response !== false){
                setTimeout('HideLoading();', 2000);
                location.reload();
                alert("保存完了しました。");
            }
        },
        error:function(err){
            HideLoading();
            console.log(err);
        }
    });
}

function exportCSVGanttOnServer() {

    var csvFormat = "";
    var csvHeader = "Navi_Koutei,201607\n"+
                    "<種目>\n"+
                        "種目,行,列,ﾚｲﾔｰNo\n"+
                        "施工計画書・要領書,1,1,管理欄\n"+
                        "行事等,1,1,0\n"+
                        "安全当番,1,1,管理欄\n"+
                        "週間安全衛生目標,1,1,管理欄\n"+
                        "SMW工事,1,1,0\n"+
                        "杭工事,1,1,0\n"+
                        "地下躯体工事,1,1,0\n"+
                    "</種目>\n"+
                    "<書式>\n"+
                        "【形式】,カレンダー\n"+
                        "【最早日】,,\n"+
                    "</書式>\n"+
                    "<工程>\n"+
                    "No,ﾚｲﾔｰNo,休稼働･連結･ﾀﾞﾐｰ,工程名,最早日からの実日数,開始日,開始時間,終了日,終了時間,実働日数,実働時間,工程行,開始行,終了行,工程種,線種,線色,線太さ,ﾍﾟｲﾝﾄ色,ﾍﾟｲﾝﾄｽﾀｲﾙ,文字方向,文字横,文字縦,メモ1,メモ2,メモ3,メモ4,メモ5,分類1,分類2,分類3,分類4,日数算出対象No,数量名称1,工程数量1,数量単位1,歩掛1,歩掛単位1,日投入量1,投入単位1,算出日数1,数量名称2,工程数量2,数量単位2,歩掛2,歩掛単位2,日投入量2,投入単位2,算出日数2,数量名称3,工程数量3,数量単位3,歩掛3,歩掛単位3,日投入量3,投入単位3,算出日数3,数量名称4,工程数量4,数量単位4,歩掛4,歩掛単位4,日投入量4,投入単位4,算出日数4,数量名称5,工程数量5,数量単位5,歩掛5,歩掛単位5,日投入量5,投入単位5,算出日数5,数量名称6,工程数量6,数量単位6,歩掛6,歩掛単位6,日投入量6,投入単位6,算出日数6,数量名称7,工程数量7,数量単位7,歩掛7,歩掛単位7,日投入量7,投入単位7,算出日数7,数量名称8,工程数量8,数量単位8,歩掛8,歩掛単位8,日投入量8,投入単位8,算出日数8,数量名称9,工程数量9,数量単位9,歩掛9,歩掛単位9,日投入量9,投入単位9,算出日数9,数量名称10,工程数量10,数量単位10,歩掛10,歩掛単位10,日投入量10,投入単位10,算出日数10\n";
    var csvFooter = "</工程>\n"+
                    "<イベント>\n"+
                    "No,ﾚｲﾔｰNo,日,時間,イベント,表示行,行位置,文字方向,文字横,文字縦\n"+
                    "</イベント>\n"+
                    "<工事概要>\n"+
                        "【工事名１】\n"+
                        "【工事名２】\n"+
                        "【工事場所】\n"+
                        "【発注者】\n"+
                        "【設計者】\n"+
                        "【監理者】\n"+
                        "【主構造】\n"+
                        "【建築面積】\n"+
                        "【延床面積】\n"+
                        "【敷地面積】\n"+
                        "【地下階】\n"+
                        "【地上階】\n"+
                        "【棟屋階】\n"+
                        "【着工日】\n"+
                        "【竣工日】\n"+
                        "【備考１】\n"+
                        "【備考２】\n"+
                        "【備考３】\n"+
                    "</工事概要>\n";

    var prj = ge.saveProject();
    var tasks = prj["tasks"];
    var csvBody = "";
    for(var i=0; i < tasks.length; i++){
        var startTime = new Date(tasks[i]["start"]);
        var endTime = new Date(tasks[i]["end"]);
        var curNum = (i+1).toString();
        var taskName = tasks[i]["name"].replace("　", "");
        taskName = taskName.replace("\"", "");
        taskName = taskName.replace(/,/g, "");
        
        csvBody = csvBody + curNum +            // No
                            ",0,0," +           // ﾚｲﾔｰNo,休稼働･連結･ﾀﾞﾐｰ
                            taskName +          // 工程名
                            ",0," +             // 最早日からの実日数
                            startTime.toLocaleDateString() +    // 開始日
                            ",0," +             // 開始時間
                            endTime.toLocaleDateString() +      // 終了日
                            ",0," +             // 終了時間
                            tasks[i]["duration"] +  // 実働日数
                            ",0,"+curNum+","+curNum+","+curNum+ // 実働時間,工程行,開始行,終了行
                            ",0,0,57,1,16777215,0,FALSE,2,0,,,,,,,,,,0\n";       // 工程種,線種,線色,線太さ,ペイント色,ペイントスタイル,文字方向,文字横,文字縦,文字縦(以降空欄)
    }
    
    csvFormat = csvHeader + csvBody + csvFooter;
    downloadCSV(csvFormat, "MyProject.CSV", "application/vnd.ms-excel");
}

function exportJSONGanttOnServer() {

    //this is a simulation: save data to the local storage or to the textarea
    //saveInLocalStorage();
    
    var prj = ge.saveProject();
    
    download(JSON.stringify(prj, null, '\t'), "MyProject.json", "application/json");
    
    /*

    delete prj.resources;
    delete prj.roles;
    
    var prof = new Profiler("saveServerSide");
    prof.reset();

    if (ge.deletedTaskIds.length>0) {
        if (!confirm("TASK_THAT_WILL_BE_REMOVED\n"+ge.deletedTaskIds.length)) {
            return;
        }
    }

    $.ajax("ganttAjaxController.jsp", {
        dataType:"json",
        data: {CM:"SVPROJECT",prj:JSON.stringify(prj)},
        type:"POST",
        
        success: function(response) {
            if (response.ok) {
                prof.stop();
                if (response.project) {
                    ge.loadProject(response.project); //must reload as "tmp_" ids are now the good ones
                } else {
                    ge.reset();
                }
            } else {
                var errMsg="Errors saving project\n";
                if (response.message) {
                    errMsg=errMsg+response.message+"\n";
                }
                
                if (response.errorMessages.length) {
                    errMsg += response.errorMessages.join("\n");
                }
                
                alert(errMsg);
            }
        }
    
    });
    */
}

// Function to download data to a file
function download(data, filename, type) {
    var file = new Blob([data], {type: type});
    if (window.navigator.msSaveOrOpenBlob) // IE10+
        window.navigator.msSaveOrOpenBlob(file, filename);
    else { // Others
        var a = document.createElement("a"),
        url = URL.createObjectURL(file);
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        setTimeout(function() {
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);  
        }, 0); 
    }
}

function downloadCSV(data, filename, type) {
    var bom = new Uint8Array([0xEF, 0xBB, 0xBF]);
    var file = new Blob([bom, data], {type: type});
    if (window.navigator.msSaveOrOpenBlob) // IE10+
        window.navigator.msSaveOrOpenBlob(file, filename);
    else { // Others
        var a = document.createElement("a"),
        url = URL.createObjectURL(file);
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        setTimeout(function() {
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);  
        }, 0); 
    }
}

function newProject(){
    clearGantt();
}


function clearGantt() {
    ge.reset();
}

//-------------------------------------------  Get project file as JSON (used for migrate project from gantt to Teamwork) ------------------------------------------------------
function getFile() {
    $("#gimBaPrj").val(JSON.stringify(ge.saveProject()));
    $("#gimmeBack").submit();
    $("#gimBaPrj").val("");
    
    /*  var uriContent = "data:text/html;charset=utf-8," + encodeURIComponent(JSON.stringify(prj));
    neww=window.open(uriContent,"dl");*/
}


function loadFromLocalStorage() {
    var ret;
    var data;
    if (localStorage) {
        if (localStorage.getObject("teamworkGantDemo")) {
            ret = localStorage.getObject("teamworkGantDemo");
        }
    }

    //if not found create a new example task
    if (!ret || !ret.tasks || ret.tasks.length == 0){
        // ret=getDemoProject();
        ret=getProject();
    }
    return ret;
}


// function saveInLocalStorage() {
//     var prj = ge.saveProject();
    
//     if (localStorage) {
//         localStorage.setObject("teamworkGantDemo", prj);
//     }
// }


//-------------------------------------------  Open a black popup for managing resources. This is only an axample of implementation (usually resources come from server) ------------------------------------------------------
function editResources(){

    //make resource editor
    var resourceEditor = $.JST.createFromTemplate({}, "RESOURCE_EDITOR");
    var resTbl=resourceEditor.find("#resourcesTable");

    for (var i=0;i<ge.resources.length;i++){
        var res=ge.resources[i];
        resTbl.append($.JST.createFromTemplate(res, "RESOURCE_ROW"))
    }


    //bind add resource
    resourceEditor.find("#addResource").click(function(){
        resTbl.append($.JST.createFromTemplate({id:"new",name:"resource"}, "RESOURCE_ROW"))
    });

    //bind save event
    resourceEditor.find("#resSaveButton").click(function(){
    var newRes=[];
    //find for deleted res
    for (var i=0;i<ge.resources.length;i++){
        var res=ge.resources[i];
        var row = resourceEditor.find("[resId="+res.id+"]");
        if (row.length>0){
            //if still there save it
            var name = row.find("input[name]").val();
            if (name && name!="")
                res.name=name;
            newRes.push(res);
      } else {
        //remove assignments
        for (var j=0;j<ge.tasks.length;j++){
            var task=ge.tasks[j];
            var newAss=[];
            for (var k=0;k<task.assigs.length;k++){
                var ass=task.assigs[k];
                if (ass.resourceId!=res.id)
                newAss.push(ass);
            }
            task.assigs=newAss;
        }
      }
    }

    //loop on new rows
    var cnt=0
    resourceEditor.find("[resId=new]").each(function(){
        cnt++;
        var row = $(this);
        var name = row.find("input[name]").val();
        if (name && name!="")
            newRes.push (new Resource("tmp_"+new Date().getTime()+"_"+cnt,name));
    });

    ge.resources=newRes;

    closeBlackPopup();
    ge.redraw();
  });


    var ndo = createModalPopup(400, 500).append(resourceEditor);
}

function initializeHistoryManagement(taskId){

    //retrieve from server the list of history points in millisecond that represent the instant when the data has been recorded
    //response: {ok:true, historyPoints: [1498168800000, 1498600800000, 1498687200000, 1501538400000, …]}
    $.getJSON(contextPath+"/applications/teamwork/task/taskAjaxController.jsp", {CM: "GETGANTTHISTPOINTS", OBJID:taskId}, function (response) {

        //if there are history points
        if (response.ok == true && response.historyPoints && response.historyPoints.length>0) {
    
            //add show slider button on button bar
            var histBtn = $("<button>").addClass("button textual icon lreq30 lreqLabel").attr("title", "SHOW_HISTORY").append("<span class=\"teamworkIcon\">&#x60;</span>");
    
            //clicking it
            histBtn .click(function () {
                var el = $(this);
                var ganttButtons = $(".ganttButtonBar .buttons");
    
                //is it already on?
                if (!ge.element.is(".historyOn")) {
                    ge.element.addClass("historyOn");
                    ganttButtons.find(".requireCanWrite").hide();
        
                    //load the history points from server again
                    showSavingMessage();
                    $.getJSON(contextPath + "/applications/teamwork/task/taskAjaxController.jsp", {CM: "GETGANTTHISTPOINTS", OBJID: ge.tasks[0].id}, function (response) {
                        jsonResponseHandling(response);
                        hideSavingMessage();
                        if (response.ok == true) {
                            var dh = response.historyPoints;
                            if (dh && dh.length > 0) {
                                //si crea il div per lo slider
                                var sliderDiv = $("<div>").prop("id", "slider").addClass("lreq30 lreqHide").css({"display":"inline-block","width":"500px"});
                                ganttButtons.append(sliderDiv);
                                
                                var minVal = 0;
                                var maxVal = dh.length-1 ;
                                
                                $("#slider").show().mbSlider({
                                    rangeColor : '#2f97c6',
                                    minVal     : minVal,
                                    maxVal     : maxVal,
                                    startAt    : maxVal,
                                    showVal    : false,
                                    grid       :1,
                                    formatValue: function (val) {
                                        return new Date(dh[val]).format();
                                    },
                                    onSlideLoad: function (obj) {
                                        this.onStop(obj);
                                    },
                                    onStart    : function (obj) {},
                                    onStop     : function (obj) {
                                        var val = $(obj).mbgetVal();
                                        showSavingMessage();
                                        /**
                                         * load the data history for that milliseconf from server
                                         * response in this format {ok: true, baselines: {...}}
                                         *
                                         * baselines: {61707: {duration:1,endDate:1550271599998,id:61707,progress:40,startDate:1550185200000,status:"STATUS_WAITING",taskId:"3055"},
                                         *            {taskId:{duration:in days,endDate:in millis,id:history record id,progress:in percent,startDate:in millis,status:task status,taskId:"3055"}....}}                     */
                                        
                                        $.getJSON(contextPath + "/applications/teamwork/task/taskAjaxController.jsp", {CM: "GETGANTTHISTORYAT", OBJID: ge.tasks[0].id, millis:dh[val]}, function (response) {
                                            jsonResponseHandling(response);
                                            hideSavingMessage();
                                            if (response.ok ) {
                                                ge.baselines=response.baselines;
                                                ge.showBaselines=true;
                                                ge.baselineMillis=dh[val];
                                                ge.redraw();
                                            }
                                        })
                                    
                                    },
                                    onSlide    : function (obj) {
                                        clearTimeout(obj.renderHistory);
                                        var self = this;
                                        obj.renderHistory = setTimeout(function(){
                                            self.onStop(obj);
                                        }, 200)
                                    }
                                });
                            }
                        }
                  });
        
                // closing the history
                } else {
                    //remove the slider
                    $("#slider").remove();
                    ge.element.removeClass("historyOn");
                    if (ge.permissions.canWrite)
                        ganttButtons.find(".requireCanWrite").show();
                    
                    ge.showBaselines=false;
                    ge.baselineMillis=undefined;
                    ge.redraw();
                }
        
            });
            $("#saveGanttButton").before(histBtn);
        }
    })
}

function showBaselineInfo (event,element){
    //alert(element.attr("data-label"));
    $(element).showBalloon(event, $(element).attr("data-label"));
    ge.splitter.secondBox.one("scroll",function(){
        $(element).hideBalloon();
    })
}

    $.JST.loadDecorator("RESOURCE_ROW", function(resTr, res){
        resTr.find(".delRes").click(function(){$(this).closest("tr").remove()});
    });

    $.JST.loadDecorator("ASSIGNMENT_ROW", function(assigTr, taskAssig){
        var resEl = assigTr.find("[name=resourceId]");
        var opt = $("<option>");
        resEl.append(opt);
        for(var i=0; i< taskAssig.task.master.resources.length;i++){
            var res = taskAssig.task.master.resources[i];
            opt = $("<option>");
            opt.val(res.id).html(res.name);
            if(taskAssig.assig.resourceId == res.id)
            opt.attr("selected", "true");
            resEl.append(opt);
        }
        var roleEl = assigTr.find("[name=roleId]");
        for(var i=0; i< taskAssig.task.master.roles.length;i++){
            var role = taskAssig.task.master.roles[i];
            var optr = $("<option>");
            optr.val(role.id).html(role.name);
            if(taskAssig.assig.roleId == role.id)
            optr.attr("selected", "true");
            roleEl.append(optr);
        }
        
        if(taskAssig.task.master.permissions.canWrite && taskAssig.task.canWrite){
            assigTr.find(".delAssig").click(function(){
                var tr = $(this).closest("[assId]").fadeOut(200, function(){$(this).remove()});
            });
        }
    
    });


function loadI18n(){
    GanttMaster.messages = {
        "CANNOT_WRITE":"No permission to change the following task:",
        "CHANGE_OUT_OF_SCOPE":"Project update not possible as you lack rights for updating a parent project.",
        "START_IS_MILESTONE":"Start date is a milestone.",
        "END_IS_MILESTONE":"End date is a milestone.",
        "TASK_HAS_CONSTRAINTS":"Task has constraints.",
        "GANTT_ERROR_DEPENDS_ON_OPEN_TASK":"Error: there is a dependency on an open task.",
        "GANTT_ERROR_DESCENDANT_OF_CLOSED_TASK":"Error: due to a descendant of a closed task.",
        "TASK_HAS_EXTERNAL_DEPS":"This task has external dependencies.",
        "GANNT_ERROR_LOADING_DATA_TASK_REMOVED":"GANNT_ERROR_LOADING_DATA_TASK_REMOVED",
        "CIRCULAR_REFERENCE":"Circular reference.",
        "CANNOT_DEPENDS_ON_ANCESTORS":"Cannot depend on ancestors.",
        "INVALID_DATE_FORMAT":"The data inserted are invalid for the field format.",
        "GANTT_ERROR_LOADING_DATA_TASK_REMOVED":"An error has occurred while loading the data. A task has been trashed.",
        "CANNOT_CLOSE_TASK_IF_OPEN_ISSUE":"Cannot close a task with open issues",
        "TASK_MOVE_INCONSISTENT_LEVEL":"You cannot exchange tasks of different depth.",
        "CANNOT_MOVE_TASK":"CANNOT_MOVE_TASK",
        "PLEASE_SAVE_PROJECT":"PLEASE_SAVE_PROJECT",
        "GANTT_SEMESTER":"Semester",
        "GANTT_SEMESTER_SHORT":"s.",
        "GANTT_QUARTER":"Quarter",
        "GANTT_QUARTER_SHORT":"q.",
        "GANTT_WEEK":"Week",
        "GANTT_WEEK_SHORT":"w."
    };
}



function createNewResource(el) {
    var row = el.closest("tr[taskid]");
    var name = row.find("[name=resourceId_txt]").val();
    var url = contextPath + "/applications/teamwork/resource/resourceNew.jsp?CM=ADD&name=" + encodeURI(name);
    
    openBlackPopup(url, 700, 320, function (response) {
        //fillare lo smart combo
        if (response && response.resId && response.resName) {
            //fillare lo smart combo e chiudere l'editor
            row.find("[name=resourceId]").val(response.resId);
            row.find("[name=resourceId_txt]").val(response.resName).focus().blur();
        }
    
    });
}

$(document).on("change", "#load-file", function() {
    ShowLoading();
    
    var uploadedFile = $("#load-file").prop("files")[0];
    // console.log(uploadedFile);
    
    if(uploadedFile["type"] == "application/json"){
        uploadJSON(uploadedFile);
    }else if((uploadedFile["type"] == "application/vnd.ms-excel") ||
             (uploadedFile["type"] == "vnd.openxmlformats-officedocument.spreadsheetml.sheet") ||
             (uploadedFile["type"] == "text/csv")){
        var fileReader = new FileReader();
        fileReader.readAsText(uploadedFile, 'shift-jis');
        uploadCSV(fileReader, uploadedFile);
    }else{
        var fileReader = new FileReader();
        fileReader.readAsText(uploadedFile, 'shift-jis');
        uploadCSV(fileReader, uploadedFile);
    }
    
    setTimeout('HideLoading();', 5000);
});

