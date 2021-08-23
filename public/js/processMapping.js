/* ajax通信トークン定義 */
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

// 工程ID-SBSルール情報ファイルのセル番号定数
var TARGET_SHEETNAME = "Sheet1";
var TARGET_INDEX_ROW_START      = 6;    // 7行
var TARGET_INDEX_COLUMN_ID1     = 7;    // H列
var TARGET_INDEX_COLUMN_ID2     = 8;    // I列
var TARGET_INDEX_COLUMN_ID3     = 9;    // J列
var TARGET_INDEX_COLUMN_ID4     = 10;   // K列
var TARGET_INDEX_COLUMN_NAME    = 12;   // M列
var TARGET_INDEX_COLUMN_SATE    = 15;   // P列
var TARGET_INDEX_COLUMN_SBS_FAMILY  = 18;   // S列
var TARGET_INDEX_COLUMN_SBS_CATEGORY= 20;   // U列
var TARGET_INDEX_COLUMN_SBS_TYPENAME= 22;   // W列
var TARGET_INDEX_COLUMN_SBS_TYPEPRM = 26;   // AA列

// var TARGET_INDEX_COLUMN_PJCODE  = 1;  // B列 PJコード
// var TARGET_INDEX_COLUMN_KKCODE  = 2;  // C列 工事基幹コード
// var TARGET_INDEX_COLUMN_BRANCH  = 3;  // D列 支店
// var TARGET_INDEX_COLUMN_CONTYPE = 6;  // G列 工事区分
// var TARGET_INDEX_COLUMN_START   = 21; // V列
// var TARGET_INDEX_COLUMN_PRJNAME = 25; // Z列
// var TARGET_INDEX_COLUMN_SKIP_BB = 53; // BB列
// var TARGET_INDEX_COLUMN_SKIP_BC = 54; // BC列
// var TARGET_INDEX_COLUMN_SKIP_BD = 55; // BD列
// var TARGET_INDEX_COLUMN_END     = 71; // BT列 グループC(追加項目Cまで)

var allstoreData = [];
$(document).ready(function(){
    
    var login_user_id = $("#hidLoginID").val();
    var img_src = "../public/image/JPG/クレーンアイコン.jpeg";
    var url = "processMapping/index";
    var content_name = "工程ID対応表";
    recordAccessHistory(login_user_id,img_src,url,content_name);
    
	allstoreData = [];
	getProcessMappingInfo();

	$('#input-excel').change(function(e){
		
        var reader = new FileReader();						           			 
        reader.readAsArrayBuffer(e.target.files[0]);
        reader.onload = function(e) {
            var data = new Uint8Array(reader.result);
            var wb = XLSX.read(data,{type:'array'});
            var sheet;
            var sheetNames= wb.SheetNames;
            allstoreData = [];
            var isExist = false;

            for(var i=0; i< sheetNames.length; i++)
            {
                var sheetName = sheetNames[i];
                if(sheetName != TARGET_SHEETNAME){
                    continue;
                }

                sheet = wb.Sheets[sheetName];
                var range = sheet['!ref'];

                // セルの範囲だけ値取得
                var decodeRange = XLSX.utils.decode_range(range);
                // 行ループ
                for (var rowIdx = decodeRange.s.r; rowIdx <= decodeRange.e.r; rowIdx++) {

					if(rowIdx < TARGET_INDEX_ROW_START){
						continue;
					}

                    var storeData = [];
                    // 列ループ
                    for (var colIdx = decodeRange.s.c; colIdx <= decodeRange.e.c; colIdx++) {
						

                        // セルの値を取得
                        var address = XLSX.utils.encode_cell({ r: rowIdx, c:colIdx });
                        var cell = sheet[address];
                        
                        // [ID1-4],[名前],[サテライトモデルフラグ]の取得
                        if((colIdx == TARGET_INDEX_COLUMN_ID1) || (colIdx == TARGET_INDEX_COLUMN_ID2)
                        || (colIdx == TARGET_INDEX_COLUMN_ID3) || (colIdx == TARGET_INDEX_COLUMN_ID4)
                        || (colIdx == TARGET_INDEX_COLUMN_SATE)){
                            if (typeof cell !== "undefined" && typeof cell.v !== "undefined") {
                                var strID = cell.v
                                var intID = parseInt(strID, 10);
                                storeData.push(intID);
                            }else{
                                storeData.push(0);
                            }
                        }
                        // 工程名称の取得
                        if(colIdx == TARGET_INDEX_COLUMN_NAME){
                            if (typeof cell !== "undefined" && typeof cell.v !== "undefined" && cell.v !== "-") {
                                storeData.push(cell.v);
                            }else{
                                storeData.push("");
                            }
                        }
                        // ファミリ名の取得
                        if(colIdx == TARGET_INDEX_COLUMN_SBS_FAMILY){
                            if (typeof cell !== "undefined" && typeof cell.v !== "undefined" && cell.v !== "-") {
                                storeData.push(cell.v);
                            }else{
                                storeData.push("");
                            }
                        }
                        // カテゴリの取得
                        if(colIdx == TARGET_INDEX_COLUMN_SBS_CATEGORY){
                            if (typeof cell !== "undefined" && typeof cell.v !== "undefined" && cell.v !== "-") {
                                storeData.push(cell.v);
                            }else{
                                storeData.push("");
                            }
                        }
                        // タイプ名の取得
                        if(colIdx == TARGET_INDEX_COLUMN_SBS_TYPENAME){
                            if (typeof cell !== "undefined" && typeof cell.v !== "undefined" && cell.v !== "-") {
                                storeData.push(cell.v);
                            }else{
                                storeData.push("");
                            }
                        }
                        // タイプパラメータの取得
                        if(colIdx == TARGET_INDEX_COLUMN_SBS_TYPEPRM){
                            if (typeof cell !== "undefined" && typeof cell.v !== "undefined" && cell.v !== "-") {
                                storeData.push(cell.v);
                            }else{
                                storeData.push("");
                            }
                        }
                    }

                    if(storeData.length !== 0){
                        allstoreData.push(storeData);
                    }
                }
                
                isExist = true;
                break;
                
            }//end for
            
            if(isExist == false){
                alert("[Error] File format is invalid.");
            }
            
            console.log(allstoreData);
        }
    });
    
    // document.getElementById("editCell1").style.visibility ="hidden";
    // $("#editCell").hover(function(){
    //     $(this).css('visibility','visible');
    // })
    
});

function saveProcessMappingInfo(){
    ShowLoading();

    var doneCount = 0;

    if(allstoreData.length == 0){
    	alert("[ERROR] No update data.\nファイルを選択してから更新してください。");
    	HideLoading();
    	return;
    }

    $.ajax({
        url: "../processMapping/deleteData",
        type: 'post',
        data:{_token: CSRF_TOKEN},
        success :function() {

            for(let i = 0; i < allstoreData.length; i++) {
                $.ajax({
                    url: "../processMapping/saveData",
                    async:false,
                    type: 'post',
                    data:{_token: CSRF_TOKEN,storeData:allstoreData[i]},
                    success :function(data) {
                        // console.log(data);
                        
                        doneCount++;
                        if(doneCount === allstoreData.length) {
                            HideLoading();
                            alert("保存完了しました。");
                			location.reload();
                        }
                    },
                    error:function(err){
                        HideLoading();
                        console.log(err);
                    }
                });
            }

        },
        error:function(err){
            HideLoading();
            console.log(err);
        }
    });

}

function getProcessMappingInfo(){

    var condition = {};
    
    $.ajax({
        url: "../processMapping/getData",
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"getAllData",condition:condition},
        success :function(data) {
            if(data != null){
            // 	console.log(data);
            	
			    displayAllstoreManagementInfo(data);
			    
            }
        },
        error:function(err){
            console.log(err);
        }
    });

}

function displayAllstoreManagementInfo(allstoreData){

    $("#tbUser tr td").remove();
    var newRow = "";
    newRow += "<thead>";
    newRow +=   "<tr>";
    newRow +=       "<th class='short'>No.</th>";
    newRow +=       "<th class='short'>ID1</th>";
    newRow +=       "<th class='short'>ID3</th>";
    newRow +=       "<th class='short'>ID4</th>";
    newRow +=       "<th class='short' style='display:none;'>ID5</th>";
    newRow +=       "<th class='middle'>工程名称</th>";
    newRow +=       "<th class='short2'>サテライトモデル</th>";
    newRow +=       "<th class='middle'>ファミリ名</th>";
    newRow +=       "<th class='middle'>カテゴリ</th>";
    newRow +=       "<th class='middle'>タイプ名</th>";
    newRow +=       "<th class='middle'>タイプパラメータ</th>";
    newRow +=   "</tr>";
    newRow += "</thead>";
	
// 	console.log("allstoreData.length");console.log(allstoreData.length);
// 	console.log("allstoreData");console.log(allstoreData);
	
	if(allstoreData.length === 0){
	    
        newRow += "<tbody>";
        newRow +=   "<tr>";
        newRow +=     "<td></td>";
        
        var COLUMN_NUM = 11;
        for(var k=0; k < COLUMN_NUM; k++){
            
            if(k === 0){
                newRow += "<td>No Data</td>";
            }else{
            newRow += "<td></td>";
            }
        }
        newRow +=   "</tr>";
        newRow += "</tbody>";
        
	}else{
	    
        newRow += "<tbody>";
        
        for(var i=0; i< allstoreData.length; i++){
            newRow += "<tr>";
            
            var storeData = allstoreData[i];

            var updateButtton = "<button id='updateBtn"+storeData["id"]+"' class='clear-decoration-btn' style='margin:0 0 0 -10px;' onclick='UpdateRow("+storeData["id"]+")'><img style='opacity:0.3;' src='../public/image/update.png' alt='' height='13' width='13' /></button>";
            newRow += "<td class='short'>"+ updateButtton + storeData["id"] +"</td>";
            newRow += "<td class='short'>"+ "<div class='' contenteditable='false'>" + storeData["process_code_1"] + "</div></td>";
            newRow += "<td class='short'>"+ "<div class='' contenteditable='false'>" + storeData["process_code_2"] + "</div></td>";
            newRow += "<td class='short'>"+ "<div class='' contenteditable='false'>" + storeData["process_code_3"] + "</div></td>";
            newRow += "<td class='short' style='display:none;'>"+ "<div class='' contenteditable='false'>" + storeData["process_code_4"] + "</div></td>";
            newRow += "<td class='middle'>"+ "<div class='' contenteditable='true'>" + storeData["name"] + "</div></td>";
            var satellite = storeData["satellite_model"] === 1 ? "サテライト" : "";
            newRow += "<td class='short2'>"+ "<div class='' contenteditable='true'>" + satellite + "</div></td>";
            newRow += "<td class='middle'>"+ "<div class='' contenteditable='true'>" + storeData["sbs_family_name"] + "</div></td>";
            newRow += "<td class='middle'>"+ "<div class='' contenteditable='true'>" + storeData["sbs_category"] + "</div></td>";
            newRow += "<td class='middle'>"+ "<div class='' contenteditable='true'>" + storeData["sbs_type_name"] + "</div></td>";
            newRow += "<td class='middle'>"+ "<div class='' contenteditable='true'>" + storeData["sbs_type_param"] + "</div></td>";
            
            newRow += "</tr>";
        }
        
        newRow += "</tbody>";
	}
	
// 	console.log("newRow");console.log(newRow);
	$("#tbUser").append(newRow);

    // if(allstoreData.length !== 0){
    //     for(var i=0; i< allstoreData.length; i++){
    //         var storeData = allstoreData[i];
    //         var elementId = "editCell" + storeData["id"];

    //         document.getElementById(elementId).style.visibility ="hidden";

    //         $(elementId).hover(function(){
    //             $(this).css("visibility","visible");
    //         });
    //     }
    // }
    
    $("#tbUser tbody tr").hover(function(){
        // $(this).children(0).css("display","block");
    });
}

function deleteAllstoreManagementInfo(){

    $.ajax({
        url: "../allstore/deleteData",
        type: 'post',
        data:{_token: CSRF_TOKEN},
        success :function() {
            alert("全てのデータ削除が完了しました。");
            location.reload();
        },
        error:function(err){
            console.log(err);
        }
    });

}

function UpdateRow(id){
    // alert("UpdateRow("+id+") click");
    var columns = $("#tbUser").children("tbody").children("tr").eq(id - 1).children();
    
    var columnData = [];
    $.each(columns,function(key,column){
        columnData.push(column["innerText"]);
    });

    $.ajax({
        url: "../processMapping/updateData",
        type: 'post',
        data:{_token: CSRF_TOKEN,columnData:columnData},
        success :function(data) {
            console.log(data);
            alert("データの更新が完了しました。");
            // location.reload();
        },
        error:function(err){
            console.log(err);
        }
    });
}

function exportProcessMappingInfo(){
    
	window.location="/iPD/processMapping/excelDownloadProcess";
}

function getCurrentProcessData(){
    var result =[];
    
    var tr =  $("#tbUser").children("tbody").children("tr");
    for(var i=0; i<tr.length; i++){
    
        var columns = $("#tbUser").children("tbody").children("tr").eq(i).children();
        
        var k = 0;
        var columnData = [];
        $.each(columns,function(key,column){
            if(k !== 0){
                columnData.push(column["innerText"]);
            }
            k++;
        });
        
        result.push(columnData);
    }

    return result;
}