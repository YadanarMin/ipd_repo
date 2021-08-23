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
    var url = "addin/calcKasetsu";
    var content_name = "重仮設算出";
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
    
    $("#project").change(function() {
        ProjectChange();
    });
});


function LoadComboData(){
    DEBUGLOG("LoadComboData", "start", 0);
    
    $.ajax({
        url: "../kasetsu/getData",
        type: 'post',
        data: {_token: CSRF_TOKEN, message: 'getKasetsuData'},
        success :function(data) {
            if(data != null){
                console.log("Project" + JSON.stringify(data["projects"],null,4));
                console.log("Item" + JSON.stringify(data["items"],null,4));
                BindComboData(data["projects"],"project",PLACEHOLDER_NAME_FOLDER);
                BindComboData(data["items"],"item",PLACEHOLDER_NAME_PROJECT);
                BindComboData(data["versions"],"version",PLACEHOLDER_NAME_VERSION);
            }
        },
        error : function(err){
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
    
    if(folderSelectedCount == 0){
        LoadComboData();
    }else if(folderSelectedCount == 1) {
        var projectName = $('#project option:selected').text();
        console.log(projectName);
            $.ajax({
                url: "../kasetsu/getData",
                type: 'post',
                data:{_token: CSRF_TOKEN,message:"getKasetsuDataByProjectName",projectName:projectName,itemName:""},
                success :function(data) {
                    console.log("#########################Kasetsu Data By ProjectName ########");
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