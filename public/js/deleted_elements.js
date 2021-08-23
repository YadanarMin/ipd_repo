var dataArray = [];
$( document ).ready(function() {
    
    var login_user_id = $("#hidLoginID").val();
    var img_src = "../public/image/JPG/原子力のフリーイラスト3.jpeg";
    var url = "common/changedInfo";
    var content_name = "ﾓﾃﾞﾙ変更状況追跡";
    recordAccessHistory(login_user_id,img_src,url,content_name);
    
    $("#loader").addClass("bgNone");
    $("#pieChartDiv").css({"height":"0vh", "padding-top":"0px"});
    $.datepicker.setDefaults( $.datepicker.regional[ "ja" ] );
        $('#txtDate').datepicker({
            //minDate: '-70y', //今日から70年前
            changeYear: true, //表示年の指定が可
            onSelect: function(dateText){ //西暦→和暦に変換して表示
                var date = dateText.split('/');
                dateText =   date[0] +'-'+ date[1] +'-'+ date[2] ;
                //dateText =  date[0] +'-'+ date[1] +'-'+ date[2];
        $(this).val(dateText);
    // DisplayDeletedElements();
        }
    });

   // DisplayDeletedElements();
    GetProjectNames();
    $('#cmbProject').select2({placeholder: "Select Project",dropdownAutoWidth : true, allowClear: true});
    $('#cmbUser').select2({placeholder: "Select User",dropdownAutoWidth : true, allowClear: true});
    $('#cmbLevel').select2({placeholder: "Select Level",dropdownAutoWidth : true, allowClear: true});
    $('#cmbStatus').select2({placeholder: "Select Status",dropdownAutoWidth : true, allowClear: true});
    $('#cmbDisplayType').select2({placeholder: "Display Type",dropdownAutoWidth : true, allowClear: true});
    $("#cmbProject").change(function() {
        var projectName = $("#cmbProject option:selected").text();
        if(projectName != ""){
            GetLevels(projectName);
            GetUsers(projectName)
        }
    });

});


function DisplayDeletedElements()
{   
    if( $('#cmbDisplayType option:selected').val() === 'chartDisplay'){ 
        var projectName = $('#cmbProject option:selected').text();
        var userName = $('#cmbUser option:selected').text();
        var status = $("#cmbStatus option:selected").text();
        var level = $("#cmbLevel option:selected").text();
        var date = $("#txtDate").val();
        if(projectName === ""){
            alert("プロジェクトを選択してください。");
            return;
        }

        DisplayPieChart(projectName,userName,status,level,date);  
        DisplayChart(projectName,userName,status,level,date)     
        return;
    }
    
    var prjName = $("#cmbProject option:selected").text();   
    var userName = $("#cmbUser option:selected").text();
    var status = $("#cmbStatus option:selected").text();
    var level = $("#cmbLevel option:selected").text();
    $("#loader").removeClass("bgNone");
    $.ajax({
        type:"POST",
        url:"/RevitWebSystem/DeletedElements/SearchDeletedElements.php",			   
        data:{message:"searchData",project:prjName,level:level,userName:userName,status:status},
        success:function(data)
        {   $("#loader").addClass("bgNone");     
            var results = JSON.parse(data);
            
            if(results.includes("empty") || results === undefined || results.length == 0){
                alert("検索情報はすでにありません！");
                location.reload();              
            }
            var newRow = "";
            if(results.length > 0)
            {               
                $("#tbDeletedElements tr").remove();
                $("#chartDiv").empty();
                $("#pieChartDiv").empty();
                $("#chartDiv").css({"height":"0vh", "padding-top":"0px"});
                $("#pieChartDiv").css({"height":"0vh", "padding-top":"0px"});
                var count = 1;
                newRow +="<tr>";
				newRow +="<th width='5%'>No.</th>";
				newRow +="<th>Document_Name</th>"
                newRow +="<th>User_Name</th>";	
                newRow +="<th>Element_ID</th>";
                newRow +="<th>Element_Name</th>";			
				newRow +="<th>Category</th>";
				newRow +="<th>Level</th>";
                newRow +="<th>Status</th>";
                newRow +="<th>Date</th>";
                newRow +="<th>Time</th>";		
                newRow +="</tr>";
                results.forEach(function(ele) {
                    newRow += "<tr>";
                    newRow += "<td height='20px'>"+count+".</td>";
                    newRow += "<td>"+ele["documentName"]+"</td>";
                    newRow += "<td>"+ele["userName"]+"</td>";
                    newRow += "<td>"+ele["elementId"]+"</td>";    
                    newRow += "<td>"+ele["elementName"]+"</td>";                  
                    newRow += "<td>"+ele["category"]+"</td>";
                    newRow += "<td>"+ele["level"]+"</td>";
                    newRow += "<td>"+ele["transactionName"]+"</td>";
                    newRow += "<td>"+ele["date"]+"</td>";
                    newRow += "<td>"+ele["time"]+"</td>";
                    newRow += "</tr>";
                    count++;
                });

                $("#tbDeletedElements").append(newRow);             
            }
        }
    });
} 

function GetProjectNames(){
    $.ajax({
        type:"POST",
        url:"/RevitWebSystem/DeletedElements/SearchDeletedElements.php",			   
        data:{message:"getProjects"},
        success:function(data)
        {          
            var results = JSON.parse(data);
            var newRow = "";
            if(results.length > 0)
            {
                $("#cmbProject option").remove();
                newRow +="<option></option>";
                results.forEach(function(project) {
                    newRow += "<option>"+project["documentName"]+"</option>";
                });
                $("#cmbProject").append(newRow);             
            }
        }
    });
}

function GetLevels(projectName){
    $.ajax({
        type:"POST",
        url:"/RevitWebSystem/DeletedElements/SearchDeletedElements.php",			   
        data:{message:"getLevels",projectName:projectName},
        success:function(data)
        {         
            var results = JSON.parse(data);
            var newRow = "";
            if(results.length > 0)
            {
                $("#cmbLevel option").remove();
                newRow +="<option></option>";
                results.forEach(function(level) {
                    newRow += "<option>"+level["level"]+"</option>";
                });
                $("#cmbLevel").append(newRow);             
            }
        }
    });
}

function GetUsers(projectName){
    $.ajax({
        type:"POST",
        url:"/RevitWebSystem/DeletedElements/SearchDeletedElements.php",			   
        data:{message:"getUsers",projectName:projectName},
        success:function(data)
        {         
            var results = JSON.parse(data);
            var newRow = "";
            if(results.length > 0)
            {
                $("#cmbUser option").remove();
                newRow +="<option></option>";
                results.forEach(function(level) {
                    newRow += "<option>"+level["userName"]+"</option>";
                });
                $("#cmbUser").append(newRow);             
            }
        }
    });
}

function DisplayChart(projectName,userName,status,level,date){
   
    $.ajax({
        type:"POST",
        url:"/RevitWebSystem/DeletedElements/SearchDeletedElements.php",			   
        data:{message:"getChartData",project:projectName,userName:userName,status:status,level:level,date:date},
        success:function(data)
        {           
            $("#tbDeletedElements tr").remove();
            var chartData = JSON.parse(data);
            if(chartData != "empty"){
                DrawChart(chartData,projectName);
            }
        }
    });    
}

function DisplayPieChart(projectName,userName,status,level,date){
$.ajax({
    type:"POST",
    url:"/RevitWebSystem/DeletedElements/SearchDeletedElements.php",			   
    data:{message:"getPieChartData",project:projectName,userName:userName,status:status,level:level,date:date},
    success:function(data)
    {
        $("#tbDeletedElements tr").remove();
        var chartData = JSON.parse(data);
        if(chartData != "empty"){
            if(userName != ""){
                DrawPieChart(chartData,userName);
            }else{
                var divCount=0;
                $("#cmbUser option").each(function(index,ele){
                    if($(this).text() == "")return;
                    var user = $(this).text();
                    divCount++;
                    var pieChartData = chartData.filter(f=>f["userName"] == user);
                    DrawPieChart(pieChartData,user,divCount);
                });
            }
            
        }
    }
});    
}

function DrawChart(chartData,projectName){
var points = [];

chartData.forEach(function(row) {
    var str = row["time"];
    var t = str.split(":");
    var hours = parseInt(t[0]);
    var minutes = parseInt(t[1]);
    var seconds = parseInt(t[2]);
    var count = parseInt(row["count"]);
    //points.push({x:new Date(2020, 0, 23, hours, minutes, seconds) ,y:count,label:hours+":"+minutes+":"+seconds});
    points.push([[hours, minutes, seconds] ,count]);
});
//need to prepare google charts data
$("#chartDiv").css({"height":"40vh", "padding-top":"40px"});
google.charts.load('current', {packages: ['corechart', 'line']});
google.charts.setOnLoadCallback(function(){drawLineChart(points,projectName)});

}

function drawLineChart(chartData,projectName){
var data = new google.visualization.DataTable();
data.addColumn('timeofday', 'Time of Day');
data.addColumn('number', 'changed counts');
data.addRows(chartData);
var options = {
    title:projectName,
    hAxis: {
      title: 'Hours',
      format: 'hh:mm a'
    },
    vAxis: {
      title: 'Changed Element Counts'
    },
    animation:{
        duration: 1000,
        easing: 'out',
        startup: true
    },
    backgroundColor: '#f1f8e9',
    series: [{visibleInLegend: false}],
    curveType: 'function',
    pointSize: 5,
  };

  var chart = new google.visualization.LineChart(document.getElementById('chartDiv'));
  chart.draw(data, options);
}

function DrawPieChart(chartData,userName,divCount){
if(divCount == undefined || divCount == 1)$("#pieChartDiv").empty();
$("#pieChartDiv").append("<div id=pieChartDiv"+divCount+" style='width:40%;'></div>");
var points = [];
var total= 0;
chartData.forEach(function(row) {
    var status = row["transactionName"];
    var count = parseInt(row["count"]);
    total = parseInt(total) + parseInt(count);
    //points.push({y:count,indexLabel:status});
    points.push([status,count]);
});
$("#pieChartDiv").css({"height":"40vh", "padding-top":"40px"});
google.charts.load('current', {packages: ['corechart']});
google.charts.setOnLoadCallback(function(){pieChart(points,userName,divCount)});
  
}

function pieChart(chartData,userName,divCount){
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'transaction status');
    data.addColumn('number', '');
    data.addRows(chartData);
    var total = 0;
    $.each(chartData,function(index,item){
        total = parseInt(item[1]) + parseInt(total);
    });
    var options = {
        title:userName+"【"+total+"】",
        animation:{
            duration: 1000,
            easing: 'out',
            startup: true
        },
        //legend: {position: 'labeled'}
      };

      var chart = new google.visualization.PieChart(document.getElementById('pieChartDiv'+divCount));
      chart.draw(data, options);
 }
