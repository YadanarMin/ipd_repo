var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
$(document).ready(function(){
    $.ajaxSetup({
        cache:false
    });
    
    $("#project").select2({
        dropdownAutoWidth: true,
        width: 500,
        placeholder:'Select Folders',
        selectedIndex:-1
    });
    $("#item").select2({
        dropdownAutoWidth: true,
        width: 500,
        maxPlaceholderWidth:150,
        maxWidth:300,
        placeholder:'Select Projects',
        selectAll : true
    });
    $("#version").select2({
        dropdownAutoWidth: true,
        width: 500,
        maxPlaceholderWidth:150,
        maxWidth:300,
        placeholder:'Select Versions',
        selectAll : true
    });
    
    $("#category").select2({
        dropdownAutoWidth: true,
        width: 150,
        maxPlaceholderWidth:150,
        maxWidth:150,
        placeholder:'Select Category',
        selectAll : true
    });
    
    LoadComboData();

    $("#project").change(function() {
        ProjectChange();
    });

    $("#item").change(function() {
        //ItemChange();
    });

    if (location.hash !== '') $('a[href="' + location.hash + '"]').tab('show');
        return $('a[data-toggle="tab"]').on('shown', function(e) {
        return location.hash = $(e.target).attr('href').substr(1);
    });

});

function LoadComboData()
{
    $.ajax({
        url: "../forge/getData",
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"getComboData"},
        success :function(data) {
            if(data != null){
               BindComboData(data["projects"],"project");
               BindComboData(data["items"],"item");
               BindComboData(data["versions"],"version");
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
        if(comboId == "version"){
            var fileName = value["name"]+"("+value["version_number"]+")";
            appendText +="<option value='"+JSON.stringify(value)+"'>"+fileName+"</option>";
        }else{
            appendText +="<option value='"+JSON.stringify(value)+"'>"+value["name"]+"</option>";
        }
        
    });
    $("select#"+comboId+" option").remove();
    $("#"+comboId).append(appendText).multiselect("reload");
}

function ProjectChange(){
    console.log("ProjectChange start");
    var folderSelectedCount = $('#project option:selected').length;
    var itemOption = "";
    var versionOption = "";
    
    if(folderSelectedCount == 1){
        var projectName = $('#project option:selected').text();
        $.ajax({
            url: "../forge/getData",
            type: 'post',
            data:{_token: CSRF_TOKEN,message:"getComboDataByProject",projectName:projectName,itemName:""},
            success :function(data) {
                // console.log(data);
                if(data != null){
                    BindComboData(data["items"],"item");
                    BindComboData(data["versions"],"version");
                    BindComboData(data["levels"],"level");
                    BindComboData(data["worksets"],"workset");
                    BindComboData(data["materials"],"material");
                    BindComboData(data["familyNames"],"familyName");
                    BindComboData(data["typeNames"],"typeName");
                }
            },
            error:function(err){
                console.log(err);
            }
        });

    }else if (folderSelectedCount > 1){
        //[TODO]複数バージョン選択時の挙動
        LoadComboData();
    }else{
        LoadComboData();
    }       
}

function ItemChange(){
    //NOP
}

function ReportTekkinVolumeOvewview(){

    var versionSelectedCount = $('#version option:selected').length;
    var level_list = [];
    var selected_categories=[];
    var workset_list=[];
    var material_list = [];
    var familyName_list = [];
    var typeName_list = [];
    var overviewData = {"Elements":0,"Volume":0,"Materials":0,"TypeName":0,"FamilyName":0};
    var chartData = {};
    var totalData = {};

    $("#category option:selected").each(function(){
        selected_categories.push($(this).val());
    });
    $('#version option:selected').each(function(){
        var valArr =JSON.parse($(this).val());                     
        var item_id = valArr.item_id;
        $.ajax({
            url: "../forge/getData",
            type: 'post',
            data:{_token: CSRF_TOKEN,message:"getTekkinData",item_id:item_id,category_list:selected_categories},
            success :function(data) {
                //alert(data);
               console.log(data);
                if(data != null){               
                 CreateColumnTekkinTable(data);
                }                                 
            },
            error:function(err){
                console.log("error");
                console.log(err);
            }
        }); 
    });
    
    //
}


 function CreateColumnTekkinTable(data){
    var appendText = "";
     appendText += "<tr>";
     appendText += "<th>No.</th>";
     appendText += "<th>element_id</th>";
     appendText += "<th>start_diameter</th>";
     appendText += "<th>start_X_firstRowCount</th>";
     appendText += "<th>start_X_secondRowCount</th>";
     appendText += "<th>start_Y_firstRowCount</th>";
     appendText += "<th>start_Y_secondRowCount</th>";
     appendText += "<th>start_rib_diameter</th>";
     appendText += "<th>start_rib_pitch</th>";
     appendText += "<th>end_diameter</th>";
     appendText += "<th>end_X_firstRowCount</th>";
     appendText += "<th>end_X_secondRowCount</th>";
     appendText += "<th>end_Y_firstRowCount</th>";
     appendText += "<th>end_Y_secondRowCount</th>";
     appendText += "<th>end_rib_diameter</th>";
     appendText += "<th>end_rib_pitch</th>";
     appendText += "</tr>";
     
     var count = 0;
    $.each(data,function(key,row){
        count++;
        appendText += "<tr>";
        appendText += "<td>"+count+".</td>";
        appendText += "<td>"+row["element_id"]+"</td>";
        appendText += "<td>"+row["start_diameter"]+"</td>";
        appendText += "<td>"+row["start_X_firstRowCount"]+"</td>";
        appendText += "<td>"+row["start_X_secondRowCount"]+"</td>";
        appendText += "<td>"+row["start_Y_firstRowCount"]+"</td>";
        appendText += "<td>"+row["start_Y_secondRowCount"]+"</td>";
        appendText += "<td>"+row["start_rib_diameter"]+"</td>";
        appendText += "<td>"+row["start_rib_pitch"]+"</td>";
        appendText += "<td>"+row["end_diameter"]+"</td>";
        appendText += "<td>"+row["end_X_firstRowCount"]+"</td>";
        appendText += "<td>"+row["end_X_secondRowCount"]+"</td>";
        appendText += "<td>"+row["end_Y_firstRowCount"]+"</td>";
        appendText += "<td>"+row["end_Y_secondRowCount"]+"</td>";
        appendText += "<td>"+row["end_rib_diameter"]+"</td>";
        appendText += "<td>"+row["end_rib_pitch"]+"</td>";
        appendText += "</tr>";
    });
    $("#tblTekkinData tr").remove();
    $("#tblTekkinData").append(appendText);
 }