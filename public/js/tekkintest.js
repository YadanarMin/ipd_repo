$(document).ready(function(){
    $('#item').select2({width: '30%',placeholder: "Select Project",dropdownAutoWidth : true, allowClear: true,});
    LoadComboData();

    $('.levelElement').on('click', function(){
        $('.levelElement').removeClass('active');
        $(this).addClass('active');
    })
    
    
    var acd_check1 = document.getElementById("acd-check1");
    var acd_check2 = document.getElementById("acd-check2");
    console.log(acd_check1);
    if(acd_check1.checked == true){
        console.log("acd_check1.checked");
        acd_check2.checked = false;
    }
    if(acd_check2.checked == true){
        console.log("acd_check2.checked");
        acd_check1.checked = false;
    }
    
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
                //alert(data);
                console.log(data);
                if(data != null){               
                    CreateColumnTekkinTable(data);
                    
                    OrganizeLevelTekkinData(data);
                }                                 
            },
            error:function(err){
                console.log("error");
                console.log(err);
            }
        });  
    });
}


function CreateColumnTekkinTable(data){
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

function OrganizeLevelTekkinData(data){
    
    var organizeData = [];
    var organizeTotalData = [];
    var organizeDataEachLevel = [];
    var levelList = [];
    
    data.sort(function(a, b) {
        return (a.level < b.level) ? -1 : 1;
    });
    console.log(data);

    var index = 0;
    var idx = 0;
    var tmpTotal = 0.0;
    var preTotal = 0.0;
    var preLevel = "";
    $.each(data,function(key,row){
        
        var level = row["level"] == "" || row["level"] == undefined ? "empty" : row["level"];
        var element_id    = row["element_id"];
        var start_weight  = row["start_weight"]  == "" || row["start_weight"]  == undefined ? "0.00" : row["start_weight"];
        var center_weight = row["center_weight"] == "" || row["center_weight"] == undefined ? "0.00" : row["center_weight"];
        var end_weight    = row["end_weight"]    == "" || row["end_weight"]    == undefined ? "0.00" : row["end_weight"];
        var total         = row["total"]         == "" || row["total"]         == undefined ? "0.00" : row["total"];
        var fTotal = parseFloat(total);
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

        if(preLevel != level){
            organizeData[preLevel] = organizeDataEachLevel;
            var tmp = {};
            tmp["level"] = preLevel;
            tmp["total"] = preTotal;
            organizeTotalData[preLevel] = tmp;

            index++;
            idx = 0;
            tmpTotal = 0.0;
            preTotal = 0.0;
            preLevel = level;
            organizeDataEachLevel = [];
        }

        preTotal += fTotal;
        organizeDataEachLevel[idx] = tmpObj;
        idx++;
    });
   
    organizeTotalData.shift();
    organizeData.shift();
   
   console.log("organizeTotalData");
   console.log(organizeTotalData);
   console.log("organizeData");
   console.log(organizeData);
    
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