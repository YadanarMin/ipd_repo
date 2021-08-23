var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
var PLACEHOLDER_NAME_FOLDER = "Select Folder";
var PLACEHOLDER_NAME_PROJECT = "Select Project";
var PLACEHOLDER_NAME_VERSION = "Select Versions";
$(document).ready(function() {
    $.ajaxSetup({
        cache: false
    });

    $("#project").select2({
        placeholder: "Folders Loading..."
    });
    $("#item").select2({
        placeholder: "Project Loading..."
    });
    $("#version").select2({
        placeholder: "Version Loading...",
    });

    $("#category").select2({
        //placeholder:"Folders Loading..."
    });
    $("#material").select2({
        //placeholder:"Project Loading..."
    });
    $("#workset").select2({
        //placeholder:"Version Loading...",
    });



    $("#chkAllCheck").change(function() {
        if ($("#chkAllCheck").is(':checked')) {
            $("#version > option").prop("selected", "selected");
            $('#version').parent().find(".select2-container .select2-selection").addClass('scroll');
            $("#version").trigger("change");
        }
        else {
            $("#version > option").prop("selected", false);
            $('#version').parent().find(".select2-container .select2-selection").removeClass('scroll');
            $("#version").trigger("change");
        }
    });

    $("#chkAllItemCheck").change(function() {
        if ($("#chkAllItemCheck").is(':checked')) {
            $("#item > option").prop("selected", "selected");
            $("#item").trigger("change");
        }
        else {
            $("#item > option").prop("selected", false);
            $("#item").trigger("change");
        }
    });



    /* $("#project").multiselect({
         maxPlaceholderWidth:174,
         maxWidth:300,
         placeholder:'Select Folders'
     });
     $("#item").multiselect({
         maxPlaceholderWidth:174,
         maxWidth:300,
         placeholder:'Select Projects',
         selectAll : true
     });
     $("#version").multiselect({
         maxPlaceholderWidth:174,
         maxWidth:300,
         placeholder:'Select Versions',
         selectAll : true

     });
     
     $("#category").multiselect({
         maxPlaceholderWidth:174,
         maxWidth:150,
         placeholder:'Select Category',
         selectAll : true
     });
     
     $("#material").multiselect({
         maxPlaceholderWidth:174,
         maxWidth:250,
         placeholder:'Select material',
         selectAll : true
     });
     $("#workset").multiselect({
         maxPlaceholderWidth:174,
         maxWidth:150,
         placeholder:'Select workset',
         selectAll : true
     });*/

    //if(useableProjects != null)
    LoadComboData();

    $("#project").change(function() {
        if ($("#chkAllItemCheck").is(':checked')) {
            $("#item > option").prop("selected", false);
            $("#item").trigger("change");
            $("#chkAllItemCheck").prop("checked", false);
        }
        ProjectChange();
    });

    $("#item").change(function() {
        if ($("#chkAllCheck").is(':checked')) {
            $("#version > option").prop("selected", false);
            $('#version').parent().find(".select2-container .select2-selection").removeClass('scroll');
            $("#version").trigger("change");
            $("#chkAllCheck").prop("checked", false);
        }

        ItemChange();
    });

});

/*$(document).on(
    {
        mouseover: function(){
            
            console.log("aaaaaaaaa");
            
        }
        /*mouseout: function(){console.log("bbbbbbbbb");}
    }, 
    '#project'
);*/
function LoadComboData() {

    $.ajax({
        url: "../forge/getData",
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "getComboData" },
        success: function(data) {
            console.log(data);
            if (data != null) {
                BindComboData(data["projects"], "project", PLACEHOLDER_NAME_FOLDER);
                BindComboData(data["items"], "item", PLACEHOLDER_NAME_PROJECT);
                BindComboData(data["versions"], "version", PLACEHOLDER_NAME_VERSION);
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}

/**
 * セレクトボックス内の項目をバインドする。
 * @param  {object}  [in]data        元データ
 * @param  {string}  [in]comboId     セレクトボックスの識別ID
 * @param  {string}  [in]placeholder セレクトボックス内のplaceholder
 * @return なし
 */
function BindComboData(data, comboId, placeholder) {
    console.log("BindComboData", "start");

    var appendText = "";
    $.each(data, function(key, value) {

        if (comboId == "version") {
            value["name"] = value["name"].trim();
            var fileName = value["name"] + "(" + value["version_number"] + ")";
            appendText += "<option value='" + JSON.stringify(value) + "'>" + fileName + "</option>";
        }
        else {
            value["name"] = value["name"].trim();
            appendText += "<option value='" + JSON.stringify(value) + "' onmouseover='Hover()'>" + value["name"] + "</option>";
        }

    });
    $("select#" + comboId + " option").remove();
    $("#" + comboId).append(appendText).select2({ placeholder: placeholder }).trigger('changed');
}

function ProjectChange() {

    var folderSelectedCount = $('#project option:selected').length;
    var itemOption = "";
    var versionOption = "";

    if (folderSelectedCount == 1) {
        var projectName = $('#project option:selected').text();
        $.ajax({
            url: "../forge/getData",
            type: 'post',
            data: { _token: CSRF_TOKEN, message: "getComboDataByProject", projectName: projectName, itemName: "" },
            success: function(data) {
                console.log(data);
                if (data != null) {
                    BindComboData(data["items"], "item");
                    BindComboData(data["versions"], "version");
                    BindComboData(data["worksets"], "workset");
                    BindComboData(data["materials"], "material");
                }
            },
            error: function(err) {
                console.log(err);
            }
        });

        /*$('#project option:selected').each(function(){           
            var projectVal =JSON.parse($(this).val());
            var projectId = projectVal["id"];

            $('#item option').each(function(){ 
                var itemVal =JSON.parse($(this).val());
                var itemId = itemVal["id"];
                if(projectId == itemVal["project_id"]){
                    itemOption +="<option value="+JSON.stringify(projectVal)+">"+projectVal["name"]+"</option>";
                    $('#version option').each(function(){                        
                        var versionVal =JSON.parse($(this).val());
                        if(itemId == versionVal["item_id"]){
                            versionOption +="<option value="+JSON.stringify(versionVal)+">"+$(this).text()+"</option>";
                        }
                    });
                }
            });            
        });
        $('select#item option').remove();
        $('select#version option').remove();
        $("#item").append(itemOption).multiselect("reload");
        $("#version").append(versionOption).multiselect("reload");*/
    }
    else {
        // LoadComboData();
    }
}

function ItemChange() {
    var versionOption = "";
    var itemSelectedCount = $('#item option:selected').length;
    if (itemSelectedCount == 1) {
        var projectName = $('#project option:selected').text();
        var itemName = $('#item option:selected').text();
        $.ajax({
            url: "../forge/getData",
            type: 'post',
            data: { _token: CSRF_TOKEN, message: "getVersionsDataByProject", projectName: projectName, itemName: itemName },
            success: function(data) {
                console.log(data);
                if (data != null) {
                    BindComboData(data["versions"], "version");
                }
            },
            error: function(err) {
                console.log(err);
            }
        });
    }
    /*$('#item option:selected').each(function(){ 
        
        var itemVal =JSON.parse($(this).val());
        var itemId = itemVal["id"];
            //itemOption +="<option value="+JSON.stringify(projectVal)+">"+projectVal["name"]+"</option>";
            $('#version option').each(function(){                        
                var versionVal =JSON.parse($(this).val());
                if(itemId == versionVal["item_id"]){
                    versionOption +="<option value="+JSON.stringify(versionVal)+">"+$(this).text()+"</option>";
                }
            });
    });  
    $("select#version option").remove();
    $("#version").append(versionOption).select2().trigger('changed');*/
}

function DisplayVolumeChart() {
    google.charts.load('current', { packages: ['corechart', 'bar'] });
    google.charts.setOnLoadCallback(drawChart);
}

function GetLatedVersionsByItem(selectedItems) {

    var result;
    $.ajax({
        url: "../forge/getData",
        type: 'post',
        async: false,
        data: { _token: CSRF_TOKEN, message: "getLatedVersionsDataByItem", itemList: selectedItems },
        success: function(data) {
            //alert(JSON.stringify(data["latedVersion"]));
            result = data["latedVersion"];


        },
        error: function(err) {
            alert(JSON.stringify(err));
        }
    });
    return result;
}

function drawChart() {

    var chartData = [];
    var ylabels = [];
    var versionSelectedCount = $('#version option:selected').length;

    if (versionSelectedCount <= 0) {
        var selectedItems = [];
        $('#item option:selected').each(function() {
            var valArr = JSON.parse($(this).val());
            var name = $(this).text();
            selectedItems.push(valArr.id);
            //chartData.push([name,parseInt(size)*0.0000001]);  
        });

        if (selectedItems.length > 0) {
            //get Latedversion data 
            var latedVersions = GetLatedVersionsByItem(selectedItems);
            $.each(latedVersions, function(key, row) {
                chartData.push([row.name + "(" + row.version_number + ")", (row.storage_size) / 1048411.0794862894]);
            })
        }
    }
    else {
        var version = 0;
        $('#version option:selected').each(function() {
            var valArr = JSON.parse($(this).val());
            var updated_time = valArr.updated_time;
            //alert(updated_time);return;
            if (updated_time.includes("T")) {
                var temp = updated_time.split('T');
                updated_time = temp[0];
            }
            updated_time = updated_time.replace(/[-]/g, '/');
            var str = updated_time + "(" + valArr.version_number + ")";
            var size = valArr["storage_size"] / 1048411.0794862894;
            /*var date = new Date(str);
            var day =  (date.getDate().toString()).padStart(2,'0');
            var month = ((date.getMonth()+1).toString()).padStart(2,'0');
            var year = date.getFullYear();
            var data= (year+"/"+month+"/"+day);*/

            var name = $(this).text();
            chartData.push([str, size]);
        });
        chartData.reverse();
    }
    //alert(JSON.stringify(chartData));
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'project name');
    data.addColumn('number', '');
    data.addRows(chartData);
    var options = {
        title: '',
        hAxis: { title: 'Projects' },
        animation: { duration: 1000, easing: 'out', startup: true },
        vAxis: { title: 'Storage Size(MB) ' },
        chartArea: { width: 1100, left: 100, top: 50, height: 110, bottom: 200 },
        series: [{ visibleInLegend: false }],
        bar: { groupWidth: 20 }
    };

    var chart = new google.visualization.ColumnChart(document.getElementById('chartDiv'));
    chart.draw(data, options);
}

function DisplayVolumeData() {

    var versionSelectedCount = $('#version option:selected').length;
    var selected_categories = [];
    var material_list = [];
    var workset_list = [];
    $("#category option:selected").each(function() {
        selected_categories.push($(this).val());
    });

    $("#material option:selected").each(function() {
        material_list.push($(this).text());
    });
    $("#workset option:selected").each(function() {
        workset_list.push($(this).text());
    });

    $('#version option:selected').each(function() {
        var valArr = JSON.parse($(this).val());
        var db_version_id = valArr.id;
        var version_number = valArr.version_number;
        var item_id = valArr.item_id;
        $.ajax({
            url: "../forge/getData",
            type: 'post',
            data: {
                _token: CSRF_TOKEN,
                message: "getDataByVersion",
                version_number: version_number,
                id: db_version_id,
                item_id: item_id,
                category_list: selected_categories,
                material_list: material_list,
                workset_list: workset_list
            },
            success: function(data) {

                console.log(data);
                if (Object.keys(data).length > 0) {
                    DisplayTable(data);
                }
            },
            error: function(err) {
                console.log("error");
                console.log(err);
            }
        });
    });
}

function DisplayTable(data) {
    var kozoData = data; //data["kozo_data"];
    //var roomData = data["room_data"];
    console.log(kozoData);
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
    appendText += "<th width='150px'>phase</th>";
    appendText += "</tr>";

    var count = 0;
    $.each(kozoData, function(key, row) {
        count++;
        appendText += "<tr>";
        appendText += "<td>" + count + ".</td>";
        appendText += "<td>" + row["type_name"] + "</td>";
        appendText += "<td>" + row["element_id"] + "</td>";
        appendText += "<td>" + row["material_name"] + "</td>";
        appendText += "<td>" + row["level"] + "</td>";
        appendText += "<td>" + row["volume"] + "</td>";
        appendText += "<td>" + row["family_name"] + "</td>";
        appendText += "<td>" + row["workset"] + "</td>";
        appendText += "<td>" + row["version_number"] + "</td>";
        appendText += "<td>" + row["phase"] + "</td>";
        appendText += "</tr>";
    })
    $("#tblVersionData tr").remove();
    $("#tblVersionData").append(appendText);
}


function DisplayTekkinData() {
    var versionSelectedCount = $('#version option:selected').length;
    var selected_categories = [];
    $("#category option:selected").each(function() {
        selected_categories.push($(this).val());
    });

    $('#version option:selected').each(function() {
        var valArr = JSON.parse($(this).val());
        var item_id = valArr.item_id;
        $.ajax({
            url: "../forge/getData",
            type: 'post',
            data: { _token: CSRF_TOKEN, message: "getTekkinData", item_id: item_id, category_list: selected_categories },
            success: function(data) {
                //alert(data);
                console.log(data);
                if (data != null) {
                    CreateColumnTekkinTable(data);
                }
            },
            error: function(err) {
                console.log("error");
                console.log(err);
            }
        });
    });
}

function CreateColumnTekkinTable(data) {
    var appendText = "";
    appendText += "<tr>";
    appendText += "<th>No.</th>";
    appendText += "<th>element_id</th>";
    appendText += "<th>level</th>";
    appendText += "<th>start_weight</th>";
    appendText += "<th>center_weight</th>";
    appendText += "<th>end_weight</th>";
    appendText += "<th>total</th>";
    appendText += "<th>category</th>";
    appendText += "</tr>";

    var count = 0;
    $.each(data, function(key, row) {
        count++;
        appendText += "<tr>";
        appendText += "<td>" + count + ".</td>";
        appendText += "<td>" + row["element_id"] + "</td>";
        appendText += "<td>" + row["level"] + "</td>";
        appendText += "<td>" + row["start_weight"] + "</td>";
        appendText += "<td>" + row["center_weight"] + "</td>";
        appendText += "<td>" + row["end_weight"] + "</td>";
        appendText += "<td>" + row["total"] + "</td>";
        appendText += "<td>" + row["category"] + "</td>";
        appendText += "</tr>";
    });
    $("#tblTekkinData tr").remove();
    $("#tblTekkinData").append(appendText);
}
