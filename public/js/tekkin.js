$(document).ready(function(){
    
    var login_user_id = $("#hidLoginID").val();
    var img_src = "../public/image/JPG/クレーンアイコン.jpeg";
    var url = "forge/tekkin";
    var content_name = "鉄筋重量管理";
    recordAccessHistory(login_user_id,img_src,url,content_name);
    
    $('#item').select2({width: '30%',placeholder: "Select Project",dropdownAutoWidth : true, allowClear: true,});
    LoadComboData();
});

function LoadComboData()
{
    $.ajax({
        url: "../forge/getData",
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"getComboData"},
        success :function(data) {
            if(data != null){
               //BindComboData(data["projects"],"project");
               BindComboData(data["items"],"item");
               //BindComboData(data["versions"],"version");
            }                                 
        },
        error:function(err){
            console.log(err);
        }
    });  
}

function BindComboData(data,comboId){
    var appendText = "";
    $.each(data,function(key,value){      
        value["name"] = value["name"].trim();
        if(value["name"]== "")return;           
        appendText +="<option value='"+JSON.stringify(value)+"'>"+value["name"]+"</option>";      
    });
    $("select#"+comboId+" option").remove();
    $("#"+comboId).append(appendText).multiselect("reload");
}

function DisplayTekkinData(){
    ShowLoading();
    
    var itemSelectedCount = $('#item option:selected').length;
    if(itemSelectedCount != 1){
        alert("Please select just one project!");return;
    }
    $('#item option:selected').each(function(){           
        var valArr =JSON.parse($(this).val());                     
        var item_id = valArr.id;
        $.ajax({
            url: "../forge/getData",
            type: 'post',
            data:{_token: CSRF_TOKEN,message:"getTekkinData",item_id:item_id},
            success :function(data) {
                // console.log(data);
                if(data != null){               
                    // CreateColumnTekkinTable_old(data);
                    
                    var totalVolumeList = OrganizeLevelTekkinData(data);
                    org_total = {};
                    org_total = totalVolumeList;
                    CreateColumnTekkinTable(totalVolumeList);
                }
                
                HideLoading();
            },
            error:function(err){
                console.log(err);
                HideLoading();
            }
        });  
    });
}


function CreateColumnTekkinTable_old(data){
    var appendText = "";
    appendText += "<tr>";
    appendText += "<th>No.</th>";
    appendText += "<th>element_id</th>";
    appendText += "<th>level</th>";
    appendText += "<th>start_weight(t)</th>";
    appendText += "<th>center_weight(t)</th>";
    appendText += "<th>end_weight(t)</th>";
    appendText += "<th>total(t)</th>";
    appendText += "<th>category</th>"; 
    appendText += "<th>phase</th>";
    appendText += "</tr>";
    
    var count = 0;
   $.each(data,function(key,row){
       count++;
       appendText += "<tr>";
       appendText += "<td>"+count+".</td>";
       appendText += "<td>"+row["element_id"]+"</td>";
       appendText += "<td>"+row["level"]+"</td>";
       appendText += "<td>"+row["start_weight"]+"</td>";
       appendText += "<td>"+row["center_weight"]+"</td>";
       appendText += "<td>"+row["end_weight"]+"</td>";
       appendText += "<td>"+row["total"]+"</td>";
       appendText += "<td>"+row["category"]+"</td>";
       appendText += "<td>"+row["phase"]+"</td>";
       appendText += "</tr>";
   });
   $("#tblTekkinData tr").remove();
   $("#tblTekkinData").append(appendText);
}

function CreateColumnTekkinTable(totalVolumeList){
    var appendText = "";
    var total_volume = Math.round(totalVolume * 100) / 100;   //小数点第三位以下四捨五入

    appendText += "<div class='sidebar'>";

        appendText += "<div id='tekkinTotal'>";
            appendText += "<h5>総重量 "+total_volume.toString()+" [t]</h5>";
        appendText += "</div>";     //tekkinTotal
        
        appendText += "<div id='levelDiv'>";
            appendText += "<ul class='levelList list1'>";
            
            var cnt = 0;
            for(var key in totalVolumeList) {
                if(key == ""){ continue; }
                
                var total = 0.0;
                for(var keyOrg in organizeData) {
                    if(keyOrg === totalVolumeList[key]["level"]){
    
                        var organizeLevelData = organizeData[keyOrg];
                        for(var key1 in organizeLevelData) {
                            var tmp_total = organizeLevelData[key1]["total"];
                            tmp_total = parseFloat(tmp_total);
                            if(isNaN(tmp_total)){
                                tmp_total = 0.0;
                            }
                            total += tmp_total;
                        }
                        break;
                    }
                }
                
                total = Math.round(total * 100) / 100;
                cnt++;
                // var total1 = Math.round(totalVolumeList[key]["total"] * 100) / 100;   //小数点第三位以下四捨五入
                
                appendText += "<li>";
                    // appendText += "<div class='levelElement' onClick='createTekkinTable("+totalVolumeList[key]['level'].toString()+","+cnt.toString()+")'>";
                    appendText += "<div id='"+cnt.toString()+"_"+totalVolumeList[key]['level'].toString()+"' class='levelElement'>";
                        appendText += "<div>"+totalVolumeList[key]["level"]+"</div>";
                        appendText += "<div style='margin-left:auto;'>"+total.toString()+" [t]</div>";
                    appendText += "</div>";
                appendText += "</li>";
            }
            
            appendText += "</ul>";
        appendText += "</div>";     //levelDiv
        
    appendText += "</div>";     //sidebar
    
    appendText += "<div id='tekkinTbl' class='main'>";
    appendText += "</div>";     //main

    $("#tekkinData div").remove();
    $("#tekkinData").append(appendText);
    
    $('.levelElement').on('click', function(){
        $('.levelElement').removeClass('active');
        $(this).addClass('active');
        
        var tmpID = $(this)[0].getAttribute('id');
        tmpID = tmpID.split("_");
        
        createTekkinTable(tmpID[1], tmpID[0]);
    });
}

function createTekkinTable(levelName, id){
    var appendText = "";
    
    appendText += "<div class='matrix-table mt-body'>";
        appendText += "<div style='margin:0 auto 5vh auto;'>";

        var category_total = {};
        for(var keyLevel in org_total){
            if(keyLevel === levelName){
                category_total = org_total[keyLevel]["category_total"];
            }
        }
        
        var count = 0;
        for(var keyTotal in category_total){
            count++;
            var total = 0.0;
            if(!isNaN(category_total[keyTotal])){
                total = Math.round(category_total[keyTotal] * 100) / 100;   //小数点第三位以下四捨五入
            }
            
            var categoryTotal = 0.0;
            for(var keyOrg in organizeData) {
                
                if(keyOrg === levelName){
                    
                    var orgLevelData = organizeData[keyOrg];
                    
                    for(var k in orgLevelData) {
                        if(orgLevelData[k]["category"] === keyTotal){
                            var tmp_total = orgLevelData[k]["total"];
                            tmp_total = parseFloat(tmp_total);
                            if(isNaN(tmp_total)){
                                tmp_total = 0.0;
                            }
                            categoryTotal += tmp_total;
                        }
                    }
                    
                    break;
                }
            }
            categoryTotal = Math.round(categoryTotal * 100) / 100;
    
            appendText += "<input id='acd-check"+count+"' class='acd-check' type='checkbox'>";
            appendText += "<label class='acd-label' for='acd-check"+count+"'>";
                appendText += "<p>"+keyTotal+"<span style='float:right;margin-right:52px;'>"+categoryTotal+"</span></p>";
            appendText += "</label>";
            
            appendText += "<div class='acd-content'>";
                appendText += "<div class='tblField'>";
                
                    appendText += "<table class='tblTekkinData'>";
                    
                        appendText += "<thead>";
                            appendText += "<tr>";
                                appendText += "<th class='trNumber'>No.</th>";
                                appendText += "<th class='trElementID'>element_id</th>";
                                appendText += "<th class='trWeight'>start_weight[t]</th>";
                                appendText += "<th class='trWeight'>center_weight[t]</th>";
                                appendText += "<th class='trWeight'>end_weight[t]</th>";
                                appendText += "<th class='trTotal'>total[t]</th>";
                                appendText += "<th class='trPhase'>phase</th>";
                            appendText += "</tr>";
                        appendText += "</thead>";
    
                        appendText += "<body>";
    
            for(var keyOrg in organizeData) {
                if(keyOrg === levelName){

                    var cnt = 0;
                    var organizeLevelData = organizeData[keyOrg];
                    
                    for(var key in organizeLevelData) {
                        
                        if(organizeLevelData[key]["category"] === keyTotal){
                            cnt++;
                            appendText += "<tr>";
                                appendText += "<td class='trNumber'>"+cnt.toString()+"</td>";
                                appendText += "<td class='trElementID'>"+organizeLevelData[key]["element_id"]+"</td>";
                                appendText += "<td class='trWeight'>"+organizeLevelData[key]["start_weight"]+"</td>";
                                appendText += "<td class='trWeight'>"+organizeLevelData[key]["center_weight"]+"</td>";
                                appendText += "<td class='trWeight'>"+organizeLevelData[key]["end_weight"]+"</td>";
                                appendText += "<td class='trTotal'>"+organizeLevelData[key]["total"]+"</td>";
                                appendText += "<td class='trPhase'>"+organizeLevelData[key]["phase"]+"</td>";
                            appendText += "</tr>";
                        }
                    }
                }
            }
                        
                        appendText += "</body>";
                    appendText += "</table>";
                    
                appendText += "</div>";     //tblField
            appendText += "</div>";
            
        }   //for categoryList

        appendText += "</div>";
    appendText += "</div>";     //matrix-table mt-body
    
    $("#tekkinTbl div").remove();
    $("#tekkinTbl").append(appendText);
    
    // var tmpb = $("#tekkinTbl");
    // console.log("tmpb.height:"+tmpb.height());
    
    
    // $('.acd-check').css("display","none");
    // $('.acd-label').css({"background":"#0068b7", "color":"#fff", "display":"block","margin-bottom":"1px","padding":"10px","position":"relative"});
    // $('.acd-label:after').css({"background":"#00479d", "box-sizing":"border-box", "content":"'\f067'", "display":"block","font-family":"'Font Awesome 5 Free'","height":"40px","padding":"10px 20px","position":"absolute", "right":"0", "top":"0px"});
    // $('.acd-content').css({"display":"block","height":"0","opacity":"0","padding":"0 10px","transition":".5s","visibility":"hidden"});
    // $('.acd-check:checked + .acd-label:after').css("content","'\f068'");

    // $('.acd-check:checked + .acd-label + .acd-content').css({"height":"750px","opacity":"1","padding":"10px","visibility":"visible"});
    // $('.acd-check:checked + .acd-label + .acd-content').css("height","100px");
    // $('.acd-check:checked').css({"height":"500px","opacity":"1","padding":"10px","visibility":"visible"});
    // $('.acd-label').css({"height":"500px","opacity":"1","padding":"10px","visibility":"visible"});
    // $('.acd-content').css({"height":"500px","opacity":"1","padding":"10px","visibility":"visible"});
    
    // if($('.acd-check').is(':checked')){
    //     $('.acd-check:checked').css({"height":"500px","opacity":"1","padding":"10px","visibility":"visible"});
    //     $('.acd-label').css({"height":"500px","opacity":"1","padding":"10px","visibility":"visible"});
    //     $('.acd-content').css({"height":"500px","opacity":"1","padding":"10px","visibility":"visible"});
    // }

    // $('.acd-label p').css("margin","0 0 0 0");
}

var org_total = {};
var organizeData = [];
var totalVolume = 0.0;
function OrganizeLevelTekkinData(data){
    
    organizeData = [];
    var organizeTotalData = [];
    var organizeDataEachLevel = [];
    var levelList = [];
    totalVolume = 0.0;
    
    data.sort(function(a, b) {
        return (a.level < b.level) ? -1 : 1;
    });

    var index = 0;
    var idx = 0;
    var tmpTotal = 0.0;
    var preTotal = 0.0;
    var preLevel = "";
    var categoryList = [];
    var preCategory = "";
    var totalCategoryList = [];

    var cnt = 0;
    var datalen = Object.keys(data).length;
    $.each(data,function(key,row){
        
        var level = row["level"] == "" || row["level"] == undefined ? "empty" : row["level"];
        var element_id    = row["element_id"];
        var start_weight  = row["start_weight"]  == "" || row["start_weight"]  == undefined ? "0.00" : row["start_weight"];
        var center_weight = row["center_weight"] == "" || row["center_weight"] == undefined ? "0.00" : row["center_weight"];
        var end_weight    = row["end_weight"]    == "" || row["end_weight"]    == undefined ? "0.00" : row["end_weight"];
        var total         = row["total"]         == "" || row["total"]         == undefined ? "0.00" : row["total"];
        var fTotal = 0.0;
        if(fTotal !== "0"){
            fTotal = parseFloat(total);
        }
        if(isNaN(fTotal)){
            fTotal = 0.0;
        }
        var category = row["category"];
        var phase = row["phase"];
        
        var tmpObj = {};
        tmpObj["level"] = level;
        tmpObj["category"] = category;
        tmpObj["element_id"] = element_id;
        tmpObj["start_weight"] = start_weight;
        tmpObj["center_weight"] = center_weight;
        tmpObj["end_weight"] = end_weight;
        tmpObj["total"] = total;
        tmpObj["phase"] = phase;
        tmpTotal += fTotal;

        categoryList.push(category);
        
        if(cnt === 0){
            preLevel = level;
            preCategory = category;
            totalCategoryList[category] = 0.0;
        }else if(cnt === (datalen -1)){
            preTotal += fTotal;
            organizeDataEachLevel[idx] = tmpObj;
        }
        
        if(preLevel != level || cnt === (datalen -1)){
            organizeData[preLevel] = organizeDataEachLevel;
            var tmpCategoryList = Array.from(new Set(categoryList));
            var tmp = {};
            tmp["level"] = preLevel;
            tmp["total"] = preTotal;
            tmp["category"] = tmpCategoryList;
            tmp["category_total"] = totalCategoryList;
            organizeTotalData[preLevel] = tmp;
            
            index++;
            idx = 0;
            tmpTotal = 0.0;
            preTotal = 0.0;
            preLevel = level;
            organizeDataEachLevel = [];
            totalCategoryList = [];
        }
        
        if(preCategory !== category){
            totalCategoryList[category] = fTotal;
            preCategory = category;
        }else{
            totalCategoryList[category] += fTotal;    
        }

        preTotal += fTotal;
        organizeDataEachLevel[idx] = tmpObj;
        idx++;
        cnt++;
        totalVolume += fTotal;
        
    });

    return organizeTotalData;
}

function DisplayTekkinPopup(){
    $("#formulaPopup").css("z-index", "5000");
    $("#formulaPopup").css({ visibility: "visible",opacity: "1"});
    $("#formulaPopup").draggable();
}

function DownloadTekkinExcel()
{
    var item_id;
    var itemSelectedCount = $('#item option:selected').length;
    if(itemSelectedCount != 1){
        alert("Please select just one project!");return;
    }
     $('#item option:selected').each(function(){   
            var valArr =JSON.parse($(this).val());
             item_id = valArr.id;
        
     });

    window.location="/iPD/forge/excelDownload?item_id="+item_id;

}

function ClosePopup(){
    $("#formulaPopup").css({ visibility: "visible",opacity: "0"});
}