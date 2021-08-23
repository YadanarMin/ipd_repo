@extends('layouts.baselayout')
@section('title', 'CCC - Crane info search')

@section('head')
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>

<script src="../public/js/shim.js"></script>
<script src="../public/js/xlsx.full.min.js"></script>
<script>
    $(document).ready(function(){
    
        var login_user_id = $("#hidLoginID").val();
        var img_src = "../public/image/JPG/クレーンアイコン.jpeg";
        var url = "crane/search";
        var content_name = "ｸﾚｰﾝ情報検索";
        recordAccessHistory(login_user_id,img_src,url,content_name);
    
        $('#selBranch').change(function() {

            var branchID = $("#selBranch :selected").val();
            $.ajax({
                type:"POST",
                url:"/RevitWebSystem/Crane/searchInfo.php",			   
                data:{message:"get_partner",branchID:branchID},
                success:function(data)
                {
                    var comboData = JSON.parse(data);
                    $("#selPartner").empty();//clear selectbox
                    var option = "";
                    comboData.forEach(function(element) {
                        option += "<option value="+ element.pid+">"+element.name+"</option>";
                    });
                $("#selPartner").append(option);

                //$("#selBox3 option:last").attr("selected", "selected");              
                }
            });

        });

        $("#selCrane").change(function(){
            GetDetailAndDisplay();
        });
    });


    function searchCraneInfo()
    {
        var branchID = $("#selBranch :selected").val();
        var partnerID = $("#selPartner :selected").val();
        var craneType = "クレーン";
   
        if($('#chkRafuta').is(":checked") && !$('#chkKurora').is(":checked")){
        craneType = "ラフター";
        }else if($('#chkKurora').is(":checked") && !$('#chkRafuta').is(":checked")){
            craneType = "クローラー";
        }

        $.ajax({
          url:"/RevitWebSystem/Crane/searchInfo.php",
		   method:"POST",
		   //dataType: "json",
		   data:{message:"get_craneFilter",branchID:branchID,partnerID:partnerID,craneType:craneType},
		   success:function(data)
		   {	
               var craneCombo = JSON.parse(data);         
               if(craneCombo.length > 0)
               {
                    var option = "";
                    craneCombo.forEach(function(element) {                   
                        option += "<option value='"+element['craneID']+"'>"+element['craneName']+"</option>";
                    });
                    $("#selCrane").empty();
                    $("#selCrane").append(option);
                    GetDetailAndDisplay();
               }else{
                   ClearSearchCraneForm();
                   alert("検索情報がDBに存在しません！");                 
               }             	     
           }
        });   	   	
    }
    
    function ClearSearchCraneForm(){
        $('#tbCraneInfo').empty();
        $("input[type='text']").val("");
        $('select#selCrane option').remove();
    }

    function GetDetailAndDisplay()
    {
        $('#tbCraneInfo').empty();
        var craneID = $("#selCrane").val();
		var craneInfoArray ;
	    var tableData ;
	    //var valuesArray ;
        //var twoDimenArray = [];

        $.ajax({
        url:"/RevitWebSystem/Crane/searchInfo.php",
        method:"POST",
        dataType: "json",
        data:{craneID:craneID},
        success:function(data)
        {		     
            craneInfo = data.craneInfo;
            tableData = data.tableInfo;

            $("#craneType").val(craneInfo[0]['craneType']);	
            $("#branchName").val(craneInfo[0]['craneType']);	
            $("#partnerName").val(craneInfo[0]['craneType']);				
            $("#craneName").val(craneInfo[0]['craneName']);
            
            $("#totalHeight").val(craneInfo[0]['totalHeight']);
            $("#totalWidth").val(craneInfo[0]['totalWidth']);
            $("#frontHeight").val(craneInfo[0]['frontHeight']);
            $("#backHeight").val(craneInfo[0]['backHeight']); 

            if(craneInfo[0]['craneType'].includes("クローラー"))
            {
                $("#driverSeatWidth").prop('disabled', false);
                $("#turnFront").prop('disabled', false);
                $("#turnBack").prop('disabled', false);
                $("#maxOverHang").prop('disabled', true);
                $("#maxOverHang").val('');

                $("#driverSeatWidth").val(craneInfo[0]['driverSeatWidth']);
                $("#turnFront").val(craneInfo[0]['turnFront']);
                $("#turnBack").val(craneInfo[0]['turnBack']);
                

            }else{
                $("#maxOverHang").prop('disabled', false);
                $("#driverSeatWidth").prop('disabled', true);
                $("#turnFront").prop('disabled', true);
                $("#turnBack").prop('disabled', true);
                $("#driverSeatWidth").val('');
                $("#turnFront").val('');
                $("#turnBack").val('');

                $("#maxOverHang").val(craneInfo[0]['maxOverHang']);
                
            }          
            
            var colCount = 0;
            for(var i = 0; i< tableData.length; i++)
            {
                var newRow = $("<tr></tr>");
                var items = tableData[i];               
                var itemArray = items.split(",");
                for(var col = 0; col < itemArray.length; col++)
                {	
                    newRow.append("<td>"+ itemArray[col] +"</td>");                                						                      
                }          
                $('#tbCraneInfo').append(newRow);
            }
        },
        error:function(data){
            alert(JSON.stringify(data));
        }
    });
    }
    
    function deleteCraneInfo()
    {
         var craneID = $("#selCrane").val();
         var tableLength = $("#tbCraneInfo tr").length;

         if(isNaN(craneID) || tableLength <= 0) return;
         var result =  confirm("全ての協力会社が使えないようになります。よろしいですか!!");
         if(result == false)return;
	   	  $.ajax({
	    	   url:"/RevitWebSystem/Crane/deleteInfo.php",
			   method:"POST",
			   dataType: "json",
			   data:{craneID:craneID},
			   success:function(data)
			   {		
			   		if(data == "success")
			   		{
			   			alert("クレーン情報を削除しました。") ;
			   		}else
			   		{
			   			alert("削除に失敗しました。");
			   		}
			   		$("#hidcraneID").val('');
			   		location.reload();
			   }
		 });
    }

</script>
<style>

#tbCraneInfo {
	width: 85%;
	background-color: #ddd;
	border: 1px solid #fff;
	border-collapse: collapse;
	margin-bottom: 5vh;
}
#tbCraneInfo  td {
	border : 1px solid #fff;
	border-collapse: collapse;
	width : 70px;
	text-align : center;
}

#tbCraneInfo tr:first-child td {
	background-color: #fff3cc;
	color : black;
}
#tbCraneInfo td:first-child  {
	background-color: #fff3cc;
	color : black;
}
#craneSearch td{
    padding:0px 10px 5px 10px;
}
 select{
	width:175px;
	padding:3px;
}

</style>
@endsection

@section('content')
<div class="main-content" align="center">

        <input type="hidden" id="hidLoginID" name="hidLoginID" value="{{Session::get('login_user_id')}}"/>
        
         <h4 class="page-title">クレーン情報検索</h4></br>
         支社名：<select name='selBranch' id = 'selBranch'>
          <!--<option>-- Select Item --</option>-->
            
          @foreach ($branch as $data)
          <option value="{{$data['bid']}}">{{$data["name"]}}</option>
          @endforeach 
          </select>&nbsp;&nbsp;&nbsp;                 
          
          
          協力会社名：<select name='selPartner' id = 'selPartner'>
         <!-- <option>-- Select Item --</option>-->
          
          @foreach ($partner as $data) 
          <option value="{{$data['pid']}}">{{$data["name"]}}</option>
          @endforeach 
          </select>&nbsp;&nbsp;&nbsp; 
          
        <input type="checkbox" name="chkRafuta" id="chkRafuta" >ラフター&nbsp;&nbsp;&nbsp; 
        <input type="checkbox" name="chkKurora" id="chkKurora" >クローラー
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type = "button" class = "btn btn-primary" name = "save" id ="save" value = "検索" onclick ="searchCraneInfo()"/>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type = "button" class = "btn btn-primary" name = "delete" id ="delete" value = "削除" onclick ="deleteCraneInfo()"/>
                            


    <div class="">
        <!--<div style="float:right;">
            <img src="../public/image/crane.png" alt="crane">
        </div>-->
        <table id="craneSearch" align="center">
            <tr>
            　  <td>クレーン種類: </td>
                <td><input type="text" name ="craneType" id="craneType" class="cranetxt" ></td>
                <td>アウトリガ最大張出 :</td>
                <td> <input type="text" name ="maxOverHang" id="maxOverHang" class="cranetxt" /></td>          
            </tr>
            <tr>
                <td>クレーンの名称 :</td>
                <td>
                    <select name='selCrane' id = 'selCrane'>                     
                    </select> 
                </td>
                <td>運転席幅 :</td>
                <td> <input type="text" name ="driverSeatWidth" id="driverSeatWidth" class="cranetxt" /></td>
    
            </tr>
        
            <tr> 
                <td>全　　　　　幅 : </td>
                <td><input type="text" name ="totalWidth" id="totalWidth"  class="cranetxt"/></td>
                <td>全　　　　　　　高 : </td>
                <td><input type="text" name ="totalHeight" id="totalHeight"  class="cranetxt"/></td>
            </tr>
            <tr> 
                <td>車　体　長・前 : </td>
                <td><input type="text" name ="fronttHeight" id="frontHeight" class="cranetxt" /></td>
                <td>車　体　長　・　後 : </td>
                <td><input type="text" name ="backHeight" id="backHeight" class="cranetxt"/></td>
            </tr>
            <tr> 
                <td>旋回台　・　前 : </td>
                <td><input type="text" name ="turnFront" id="turnFront" class="cranetxt" /></td>
                <td>旋回台　・　後 : </td>
                <td><input type="text" name ="turnBack" id="turnBack" class="cranetxt"/></td>
            </tr>
            </table></br>
            
        <input type ="hidden" name ="hidcraneID" id="hidcraneID" />
        <table id = "tbCraneInfo" align="center">
    
        </table> 
             
    </div> 
   
</div>
@endsection