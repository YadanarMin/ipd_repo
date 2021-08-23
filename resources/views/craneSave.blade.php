@extends('layouts.baselayout')
@section('title', 'CCC - Crane info save')

@section('head')
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>

<script src="../public/js/shim.js"></script>
<script src="../public/js/xlsx.full.min.js"></script>

<script>
		var sheetCount = 0;
		var craneList ={};
	$(document).ready(function(){
	
	 var login_user_id = $("#hidLoginID").val();
     var img_src = "../public/image/JPG/クレーンアイコン.jpeg";
     var url = "crane/save";
     var content_name = "ｸﾚｰﾝ情報登録";
     recordAccessHistory(login_user_id,img_src,url,content_name);
	 
	 $('#input-excel').change(function(e){
		
			 var reader = new FileReader();						           			 
			 reader.readAsArrayBuffer(e.target.files[0]);
			 reader.onload = function(e) {
					var data = new Uint8Array(reader.result);
					var wb = XLSX.read(data,{type:'array'});					 					 					
					var row ;
					var rowNum;
					var colNum;
					var sheet;				
					var sheetNames= wb.SheetNames;
					sheetCount = sheetNames.length;

					emptyItems(sheetNames.length); 
					var index = 0; 
				 for(var k = 0; k< sheetNames.length; k++)
				 {
					var arrayData = [];
					var rowArray = [];
					 var colArray = [];
					 var twoDimenArray = [] ;
					//alert(k);
					var sheetName = sheetNames[k];
					
					 if(isJapanese(sheetName))continue;
					 sheet = wb.Sheets[sheetName];

					var type = sheet[XLSX.utils.encode_cell({r: 1, c: 0})];
					var branch = sheet[XLSX.utils.encode_cell({r: 2, c: 1})];
					var partner = sheet[XLSX.utils.encode_cell({r: 3, c: 1})];
					var craneName = sheet[XLSX.utils.encode_cell({r: 4, c: 0})];
					var driverSeatOroverHang = sheet[XLSX.utils.encode_cell({r: 5, c: 1})];
					var totalWidth = sheet[XLSX.utils.encode_cell({r: 6, c: 1})];
					var totalHeight = sheet[XLSX.utils.encode_cell({r: 7, c: 1})];
					var frontHeight = sheet[XLSX.utils.encode_cell({r: 8, c: 1})];
					var backHeight = sheet[XLSX.utils.encode_cell({r: 9, c: 1})];
					var turnFront = sheet[XLSX.utils.encode_cell({r: 10, c: 1})];
					var turnBack = sheet[XLSX.utils.encode_cell({r: 11, c: 1})];

					var maxOverhang = "";
					var driverSeatWidth = "";
					var turnToFront = "";
					var turnToBack = "";

				    if(type.v.includes("クローラー"))
					{
						driverSeatWidth = driverSeatOroverHang.v;
						turnToFront = turnFront.v;
						turnToBack = turnBack.v;
					}else{
						maxOverhang = driverSeatOroverHang.v;
					}
					var craneInfo = {"craneType":type.v,"branchName":branch.v,"partnerName":partner.v,"craneName":craneName.v,
									"maxOverHang":maxOverhang,"totalWidth":totalWidth.v,"totalHeight":totalHeight.v,
									"frontHeight":frontHeight.v,"backHeight":backHeight.v
									,"driverSeatWidth":driverSeatWidth,"turnFront":turnToFront,"turnBack":turnToBack};								
				   
					var range = XLSX.utils.decode_range(sheet['!ref']);
					range.s.r = type.v.includes("クローラー") ? 13 : 11;
				   // alert(range.e.c);
					for(rowNum = range.s.r; rowNum <= range.e.r; rowNum++){
					   row = [];

						for(colNum=range.s.c; colNum<=range.e.c; colNum++){
						   var nextCell = sheet[
							  XLSX.utils.encode_cell({r: rowNum, c: colNum})
						   ];
						   var headerCell = sheet[
							  XLSX.utils.encode_cell({r: range.s.r, c: colNum})
						   ];
						   if( typeof nextCell !== 'undefined'){
							 // row.push(void 0);
							  row.push(nextCell.w);
						   } else if(typeof headerCell !== 'undefined')row.push(void 0);
						}						
						arrayData.push(row);												
					}
					craneList[index] = {arrayData,craneInfo}; 
					index++;	
					 //var arrayData = XLSX.utils.sheet_to_json(wb.Sheets.Sheet1, {header:1});
					// alert(arrayData.length);
					  colArray = arrayData[0];
					  
					 var arrayLength = arrayData.length;
					 for(var i = 1 ; i < arrayLength; i++)
					 {
						 rowArray.push(arrayData[i][0]);						 
					 }
																   					 
					 for(var i = 1; i< arrayLength; i++)
					 {
						 var valueArray = [];
						 var bodyValueArray = arrayData[i].length;
						 for(var j = 1; j < bodyValueArray; j++)
						 {
							 valueArray.push(arrayData[i][j]);
						 }
						 twoDimenArray.push(valueArray);
					 }
					 var name = craneName.v;

					generate_table(colArray,rowArray,twoDimenArray,k+1,craneInfo);
			  }//end for			  
			 }
	 	});
 	}); 

function isJapanese(word) {
	var result  = word.match(/[\u3400-\u9FBF]/);
	if(result == null)
	return false;
	else 
	return true;
}	
	
function generate_table(colArray,rowArray,twoDimenArray,k,craneInfo)
{
	//alert(rowArray.length+"rowwwww");
	
		var tab_header = "";
		var tab_body="";
		if(k == 1){
			tab_header = "<li class='active'><a href=#"+k+" data-toggle='tab'>"+craneInfo['craneName']+"</a></li>";
			 tab_body = "<div class='tab-pane active' id="+k+"></div>";
		}else{
			tab_header = "<li><a href=#"+k+" data-toggle='tab'>"+craneInfo['craneName']+"</a></li>";
			 tab_body = "<div class='tab-pane' id="+k+"></div>";
		}
		$("#tab_header").append(tab_header);
		$("#tab_body").append(tab_body);
		
	var newRow = "";
			newRow +="<div class='info' style='margin-top:5vh;'>";
			newRow +="<div>";
			newRow +="　クレーン種類: <input type='text' name ='craneType"+k+"' id='craneType"+k+"' value='"+craneInfo['craneType']+"' class='cranetxt'  disabled='disabled'/>";
			newRow +="</div>";
			newRow +="<div>"; 
			newRow+="　　　　支社名: <input type='text' name ='branchName"+k+"' id='branchName"+k+"'  value='"+craneInfo['branchName']+"' class='cranetxt'  disabled='disabled'/>";
			newRow+="　　　 　協力会社名: <input type='text' name ='partnerName"+k+"' id='partnerName"+k+"'  value='"+craneInfo['partnerName']+"' class='cranetxt'  disabled='disabled'/>";
			newRow+="</div>";
			newRow+="<div>";					
			newRow+="クレーンの名称: <input type='text' name ='craneName"+k+"' id='craneName"+k+"'  value='"+craneInfo['craneName']+"' class='cranetxt'  disabled='disabled'/>";
			if(craneInfo['craneType'].includes("クローラー")){
				newRow+="　　　　　運転席幅 : <input type='text' name ='driverSeatWidth"+k+"' id='driverSeatWidth"+k+"'  value='"+craneInfo['driverSeatWidth']+"'  class='cranetxt'  disabled='disabled' />";
			}else{
				newRow+="アウトリガ最大張出 : <input type='text' name ='maxOverHang"+k+"' id='maxOverHang"+k+"'  value='"+craneInfo['maxOverHang']+"'  class='cranetxt'  disabled='disabled' />";
			}
			//newRow+="アウトリガ最大張出 : <input type='text' name ='maxOverHang"+k+"' id='maxOverHang"+k+"'  value='"+craneInfo['maxOverHang']+"'  class='cranetxt'  disabled='disabled' />";
			newRow+="</div>";

			newRow+="<div>"; 
			newRow+="全　　　　　幅: <input type='text' name ='totalWidth"+k+"' id='totalWidth"+k+"'  value='"+craneInfo['totalWidth']+"'  class='cranetxt'  disabled='disabled' />";
			newRow+="全　　　　　　　高 : <input type='text' name ='totalHeight"+k+"' id='totalHeight"+k+"'  value='"+craneInfo['totalHeight']+"'  class='cranetxt'  disabled='disabled'/>";
			newRow+="</div>";

			newRow+="<div>"; 
			newRow+="車　体　長・前: <input type='text' name ='frontHeight"+k+"' id='frontHeight"+k+"'  value='"+craneInfo['frontHeight']+"'  class='cranetxt'  disabled='disabled'/>";
			newRow+="車　体　長　・　後 : <input type='text' name ='backHeight"+k+"' id='backHeight"+k+"'  value='"+craneInfo['backHeight']+"'  class='cranetxt'  disabled='disabled'/>";
			newRow+="</div>";

			if(craneInfo['craneType'].includes("クローラー")){
				newRow+="<div>"; 
				newRow+="　　旋回台・前 : <input type='text' name ='turnFront"+k+"' id='turnFront"+k+"'  value='"+craneInfo['turnFront']+"'  class='cranetxt'  disabled='disabled'/>";
				newRow+="　　　　旋回台・後 : <input type='text' name ='turnBack"+k+"' id='turnBack"+k+"'  value='"+craneInfo['turnBack']+"'  class='cranetxt'  disabled='disabled'/>";
				newRow+="</div>";
			}
			newRow+="</br></div>";

			//$("#tableDisplay table").remove();
			//$("#tableDisplay").append(newRow);
			var newTable = "<table id='tbCraneInfo"+k+"' class='tbCrane'>";
				newTable += "<tr>";
				for(var col = 0; col < colArray.length; col++)
				{
					newTable +="<td>"+ colArray[col] +"</td>";
				}
				newTable += "</tr>";
			newTable += "</table>";
			//$("#tbCraneInfo"+k+"tbody tr").remove();
			//$('#tableDisplay').append(newTable);

			$("#"+k).append(newRow);
			$("#"+k).append(newTable);
			
			$("#tbCraneInfo"+k+"tbody tr").remove();//alert($('#tbCraneInfo'+k+' tbody tr').length);
			for(var row = 0; row < rowArray.length; row++)
			{  
				if(typeof rowArray[row] === "undefined")rowArray[row] = "";
				$('#tbCraneInfo'+k).append("<tr><td>"+ rowArray[row] +"</td></tr>");
			}

			var tbID = "tbCraneInfo"+k;

			var tr = $("#"+tbID+" tr");
			//alert(JSON.stringify(rows));
			for(var i = 0; i< twoDimenArray.length; i++)
			{
				var arrLength = twoDimenArray[i].length;		
				
				for(var j = 0; j < arrLength; j++)
				{ 
					if( typeof twoDimenArray[i][j] === 'undefined' )
					{
						twoDimenArray[i][j] = " ";  
					}
										
					tr.eq(i+1).find('td:eq('+j+')').after("<td>"+ twoDimenArray[i][j] +"</td>");
				}
			}
			
			
			//$("#tableDisplay").append("<span class='diffLine' style='padding:3% 5% 3% 5%;color:blue'>===================================================</span>");
	}

	function emptyItems(tableCount)
	{
		$("#craneType").val('');
		$("#branchName").val('');
		$("#partnerName").val('');
		$("#craneName").val('');
		$("#maxOverHang").val('');
		$("#totalHeight").val('');
		$("#totalWidth").val('');
		$("#frontHeight").val('');
		$("#backHeight").val('');
		$("#tableDisplay table").remove();
		$(".info").remove();
		for(var i = 1; i<= tableCount; i++)
		{
			$('#tbCraneInfo'+i+' tbody tr').remove();
		}						
	}
       
    function saveCraneInfo()
    {
		if(!$.isEmptyObject(craneList))
		{
			$.ajax({
					type:"POST",
					url:"/RevitWebSystem/Crane/saveCraneInfo.php",			   
					data:{saveData:craneList},
					success:function(data)
					{	
						if(data.includes("success"))
						{								
							alert("クレーン情報を登録しました。") ;
						}else
						{
							alert(data);
						}
						//$("#hidcraneID").val('');
						location.reload();
					}			
				});
		}			
    }
</script>
<style>

ul{
	float:left;
}

[id*="tbCraneInfo"] {
	width: 85%;
	background-color: #ddd;
	border: 1px solid #fff;
	border-collapse: collapse;
	margin-bottom: 5vh;
}
[id*="tbCraneInfo"]  td {
	border : 1px solid #fff;
	border-collapse: collapse;
	width : 70px;
	text-align : center;
}

[id*="tbCraneInfo"] tr:first-child td {
	background-color: #fff3cc;
	color : black;
}
[id*="tbCraneInfo"] td:first-child  {
	background-color: #fff3cc;
	color : black;
}
.page-title{
	margin-left:20%;
}
</style>
@endsection

@section('content')
<div class="main-content">
	   <h4 class="page-title">クレーン情報登録</h4>

       <form id ="craneForm" name = "craneForm" method="POST">			
            <!--<h4 align="center">クレーン情報入力フォーム</h4>-->
            <div style="padding: 1% 0% 2% 0%;float:left;display:flex;margin-left:20%;">   
                <input type="file" id="input-excel" /> 
                <input type = "button" class="btn btn-primary" name = "save" id ="save" value = "保存" onclick ="saveCraneInfo()"/>
            </div>				           
        </form>    <br> <br> <br>                       

    	
	<div id="DivTab">	
		<ul class="nav nav-tabs" id="tab_header" style="width:100%;">
		</ul>

		<div class="tab-content" id="tab_body">
		</div>
	 </div>
     <!--<div id="tableDisplay"> </div>-->
	<input type="hidden" id="hidLoginID" name="hidLoginID" value="{{Session::get('login_user_id')}}"/>
   
</div>
@endsection