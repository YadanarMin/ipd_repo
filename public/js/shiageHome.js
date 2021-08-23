	 	
	$(document).ready(function(){
		
	    var login_user_id = $("#hidLoginID").val();
	    var img_src = "../public/image/JPG/ローディング中のアイコン1.jpeg";
	    var url = "common/saveRoom";
	    var content_name = "部屋仕上ﾃﾞｰﾀ";
	    recordAccessHistory(login_user_id,img_src,url,content_name);

		$("#btnExcel").click(function(e){
			e.preventDefault();//not refresh by button click
			$( function () {

				var workbook = null;
				var xhr = new XMLHttpRequest();
				xhr.open('GET', '../Libraries/Template/ShiageTemplateExcel.xlsm', true);
				xhr.responseType = 'arraybuffer';
	
				xhr.onload = function (e) {
					// response is unsigned 8 bit integer
					var responseArray = new Uint8Array(this.response);
					
					/* start Call XLSX */	
					var arr = new Array();
					for(var i = 0; i != responseArray.length; ++i) arr[i] = String.fromCharCode(responseArray[i]);
					var bstr = arr.join("");				
					var wb = XLSX.read(bstr, {bookVBA:true, type:"binary",raw:true});//bookVBA to keep macro
					var sheetName = wb.SheetNames[1];//get sheetName by xlsx because canot get sheet Name in igniteui library
					/* end Call XLSX */	

					$.ig.excel.Workbook.load(responseArray, function () {
						workbook = arguments[0];
						//console.log(workbook);
						//alert(arguments[0]);
						EditExcelFile(workbook,sheetName);						
					}, function () {
						console.log("fail");
					})
				};
				xhr.send();
		});
		
	});

	//set selectbox1 data and page initial display
	$.ajax({
		type:"POST",
		url:"/RevitWebSystem/Shiage/getShiageData.php",			   
		data:{message:"get_project"},
		success:function(data)
		{
			var comboData = JSON.parse(data);
			$("#selBox1").empty();//clear selectbox			
			var option = "";
			comboData.forEach(function(element) {
				option += "<option value="+ element.id+">"+element.name+"</option>";
			});
			$("#selBox1").append(option);
			$("#selBox1 option:last").attr("selected", "selected");
			//ChangeProject();
		}
	});
	//	$("#mydiv").hide();
	 $('#file_upload').change(function(e){
		//var filepath = this.files[0].mozFullPath;
		//alert(filepath);
			 var reader = new FileReader();              
			 reader.readAsArrayBuffer(e.target.files[0]);
			 reader.onload = function(e) {

					 var data = new Uint8Array(reader.result);					 
					 var wb = XLSX.read(data,{type:'array'});
				 
					var arrayData = [];
					var rowNum;
					var colNum;
					var sheet;
					var sheetNames= wb.SheetNames;
					
				 for(var i = 0; i< sheetNames.length; i++)
				 {
					 var sheetName = sheetNames[i];
					 if(!sheetName.includes('Sheet'))continue;
					 sheet = wb.Sheets[sheetName];;
					 var range = XLSX.utils.decode_range(sheet['!ref']);
					 range.s.r = 1;
							
					for(rowNum = range.s.r; rowNum <= range.e.r; rowNum++)
					{
						var row = [];
						var firstCell = sheet[XLS.utils.encode_cell({r: rowNum, c: 1})];							
						if(typeof firstCell === "undefined")continue;

						for(colNum=range.s.c; colNum<=range.e.c; colNum++)
						{
							var currentCell = sheet[XLSX.utils.encode_cell({r: rowNum, c: colNum})];
							//alert(currentCell);
							if( typeof currentCell !== 'undefined'){
								if(currentCell.v == 0){
									row.push(" ");
								}else{										
									row.push(currentCell.v);
								}
								
							} else if(typeof currentCell === 'undefined'){	
								row.push(void 0);
							}							
						}
						arrayData.push(row);							
					}
				 }

				 if(arrayData.length > 0)
				 {
					 displayData(arrayData);
				 }
					//("#hidData").val('');
					//("#hidData").val(JSON.stringify(arrayData));					   
			 }
	 	});
	 });

 function DisplayByVersion()
 {
	 var projName = $("#selBox1 :selected").text();
	 $.ajax({
		type:"POST",
		url:"/RevitWebSystem/Shiage/getShiageData.php",			   
		data:{message:"get_comboData",docName:projName},
		success:function(data)
		{
			var comboData = JSON.parse(data);
			$("#selBox2").empty();//clear selectbox
			$("#selBox3").empty();//clear selectbox
			var option = "";
			comboData.forEach(function(element) {
				option += "<option value="+ element.version+">"+element.name+"</option>";
			});
		 $("#selBox2").append(option);
		 $("#selBox3").append(option);
		 $("#selBox3 option:last").attr("selected", "selected");
		 ChangeVersion();
			
		}
	 });
	$("#overlayVersionDisplay").css({ visibility: "visible",opacity: "1"});
 } 

 function ChangeVersion()
 {
	  //get first ver data
	  var versionFrom = parseInt($("#selBox2").val());
	  var versionTo = parseInt($("#selBox3").val());
	  var projectName = $("#selBox1 :selected").text();

	  if(versionFrom > versionTo ) return true;
	  $.ajax({
		type:"POST",
		url:"/RevitWebSystem/Shiage/getShiageData.php",
		data: {message:"get_ByVersion_First",FirstVersion:1,Version:versionTo,docName :projectName},//get all version data from tb_shiage_log		   
		success:function(data)
		{
			var versionData = JSON.parse(data);
			changesArray = [];	
			for(var i = versionFrom; i < versionTo; i++)
			{
				var dataPreVersion = versionData.filter(x=>x.version == i);
				var dataNextVersion = versionData.filter(x=>x.version == i+1);
				if(dataNextVersion.length > 0)
				{		
					var notExistPrevious = false;				
					dataNextVersion.forEach(function(element) {													
						var result = dataPreVersion.find(x=>x.roomid == element.roomid);
						
						//compare next version data and previous version data
						if(!jQuery.isEmptyObject(result))
						{	var nextVer = i+1;
							var changeVersion = i+"～"+nextVer														
							var diffResultFlat  = objDiff(element,result,"version",changeVersion);																			
						}else{
							//if not exist in previous version,find previous of previous version
							var j = i-1;
							var found = false;
							while(j >= versionFrom){
								//alert(j);
								var dataVersion = versionData.filter(x=>x.version == j);
								var result = dataVersion.find(x=>x.roomid == element.roomid);
								if(!jQuery.isEmptyObject(result))
								{
									var nextVer = i+1;
									var changeVersion = i+"～"+nextVer														
									var diffResultFlat  = objDiff(element,result,"version",changeVersion);
									j = 0;//for break while loop
									found = true;	
								}else{
									j--;
								}											
							}
							
							//find previous version of versionFrom ,if versionFrom is 3, find from version 2,1 
							if(found == false && versionFrom > 1)
							{
								var k = versionFrom -1;											
								while(k < versionFrom && k > 0){

									var preFromVersion = versionData.filter(x=>x.version == k);
									//alert(preFromVersion.length);
									var data = preFromVersion.find(x=>x.roomid == element.roomid);

									if(!jQuery.isEmptyObject(data))
									{
										var nextVer = i+1;
										var changeVersion = i+"～"+nextVer														
										var diffResultFlat  = objDiff(element,data,"version",changeVersion);
										k = 0;//for break while loop
									}else{
										k--;
									}											
								}
							}
						}								
					});
				}else{
					/*var upadatedData = dbData.find( x => x.roomid == element.roomid);
					if(!jQuery.isEmptyObject(upadatedData))
					{
						var nextVer = i+1;
						var changeVersion = i+"～"+nextVer														
						var diffResultFlat  = objDiff(element,upadatedData,"version",changeVersion);			
					}*/
				}
			}
				$("#tbVersion tbody tr").remove();			
				changesArray.forEach(function(item){
					var newRow = "<tr>";
					for(var i = 0; i < item.length; i++)
					{
						newRow += "<td>"+item[i]+"</td>";
					}
					newRow += "</tr>";
					$("#tbVersion tbody").append(newRow);
				});	
				
		}
	 });  
 } 

 function ChangeProject()
 {
	 var projectName = $("#selBox1 :selected").text();
	 $.ajax({
			type:"POST",
			url:"/RevitWebSystem/Shiage/getShiageData.php",			   
			data:{message:"get_shiage",docName:projectName},
			success:function(data)
			{
				var projectData = JSON.parse(data);
				if(projectData.length > 0)
				{	var changePoject = [];
					projectData.forEach(function(e) {
						var data = Object.values(e);
						data.shift();//remove id
						data.shift();//remove docID
						changePoject.push(data);
					  });			
					displayData(Object.values(changePoject));
				}else{
					$("#tbShiage tr").remove();
				}
				
				
			}
	 });
 }

function displayData(arrayData)
{
	$("#tbShiage tr").remove();
	for(var i = 0; i < arrayData.length; i++)
	{	
		var index = i+1;	
		var data = arrayData[i];
		var newRow = "<tr id="+index+">"
					+"<td>"
					+"<table id='tbInner'>"
					+"<tr>"
							+"<th>ID:&nbsp<span name='roomid'>"+data[0]+"</span></th>"
							+"<th colspan='2'>部　位</th>"
							+"<th>下地</th>"
							+"<th>仕上</th>"
						+"</tr>";

			for(var j = 1 ; j<= 18; j++)
			{
				newRow += "<tr>";

					 //first colunm
					 if( j == 2) {
						newRow += "<td width='10%' rowspan='12'><input type='text' name='roomname' size='10' value="+data[1]+" ></td>";
					 }else if(j == 1){
						newRow += "<td width='10%'>室名</td>";
					 }else if(j == 14 ){
						newRow += "<td width = '10%' >内装制限</td>";
					 }else if(j == 15)
					 {
						newRow += "<td width = '10%' rowspan='2'><input type='text' size='10' name='naisoseigen1' id='naisoseigen1' value = "+data[2]+"></td>";
					 }else if(j == 17)
					 {
						newRow += "<td width = '10%' rowspan='2'><input type='text' size='10' name='naisoseigen2' id='naisoseigen1' value = "+data[3]+"></td>";
					 }

					//second column
					if(j== 1){
						newRow += "<td width='10%' rowspan='2'>床</td>" ;
					 }else if(j== 3){
						newRow += "<td width='10%'>巾木</td>" ;
					 }else if(j == 4){
						newRow += "<td width='10%' rowspan='2'>柱・壁</td>";
					 }else if(j == 6){
						newRow += "<td width='10%'' rowspan='3'>天井</td>";
					 }else if(j== 9){
						newRow += "<td width='10%' rowspan='10'>他</td>";
					 } 

					 var val1 = "";
					 var val2 = "";
					 var val3 = "";
					 var name1 ="";
					 var name2 = "";
					 var name3 = ""; 
					 switch(j)
					 {
						 case 1 : val1 = data[4];val2 = data[5];val3 = data[6];
						 		  name1 = "yukatakasa1";name2 = "yukashitaji1";name3="yukashiage1";break;
						 case 2 : val1 = data[7];val2 = data[8];val3 = data[9];
						 		  name1 = "yukatakasa2";name2 = "yukashitaji2";name3="yukashiage2";break;
						 case 3 : val1 = data[10];val2 = data[11];val3 = data[12];
						 		  name1 = "habagitakasa1";name2 = "habagishitaji1";name3="habagishiage1";break;
						 case 4 : val1 = data[13];val2 = data[14];val3 = data[15];
						 		  name1 = "kabe1";name2 = "kabeshitaji1";name3="kabeshiage1";break;
						 case 5 : val1 = data[16];val2 = data[17];val3 = data[18];
						 		  name1 = "kabe2";name2 = "kabeshitaji2";name3="kabeshiage2";break;
						 case 6 : val1 = data[19];val2 = data[20];val3 = data[21];
						 		  name1 = "tenjyotakasa1";name2 = "tenjyoshitaji1";name3="tenjyoshiage1";break;
						 case 7 : val1 = data[22];val2 = data[23];val3 = data[24];
						 		  name1 = "tenjyotakasa2";name2 = "tenjyoshitaji2";name3="tenjyoshiage2";break;
						 case 8 : val2 = data[25];val3 = data[26];
						 		  name2 = "mawarienshitaji1";name3="mawarienshiage1";break;
						 case 9 : val1 = data[27];val2 = data[28];val3 = data[29];
						 		  name1 = "sonota1";name2 = "sonotashitaji1";name3="sonotashiage1";break;
						 case 10 :val1 = data[30];val2 = data[31];val3 = data[32];
						 		name1 = "sonota2";name2 = "sonotashitaji2";name3="sonotashiage2";break;
						 case 11 : val1 = data[33];val2 = data[34];val3 = data[35];
						 		name1 = "sonota3";name2 = "sonotashitaji3";name3="sonotashiage3";break;
						 case 12 : val1 = data[36];val2 = data[37];val3 = data[38];
						 		name1 = "sonota4";name2 = "sonotashitaji4";name3="sonotashiage4";break;
						 case 13 : val1 = data[39];val2 = data[40];val3 = data[41];
						 		name1 = "sonota5";name2 = "sonotashitaji5";name3="sonotashiage5";break;
						 case 14 : val1 = data[42];val2 = data[43];val3 = data[44];
						 		name1 = "sonota6";name2 = "sonotashitaji6";name3="sonotashiage6";break;
						 case 15 : val1 = data[45];val2 = data[46];val3 = data[47];
						 		name1 = "sonota7";name2 = "sonotashitaji7";name3="sonotashiage7";break;
						 case 16 : val1 = data[48];val2 = data[49];val3 = data[50];
						 		name1 = "sonota8";name2 = "sonotashitaji8";name3="sonotashiage8";break;
						 case 17 : val1 = data[51];val2 = data[52];val3 = data[53];
						 		name1 = "sonota9";name2 = "sonotashitaji9";name3="sonotashiage9";break;
						 case 18 : val1 = data[54];val2 = data[55];val3 = data[56];
						 		name1 = "sonota10";name2 = "sonotashitaji10";name3="sonotashiage10";break;
					 }
	
					 newRow += "<td width ='20%'><input type='text' name= "+name1+" id="+name1+" value = "+val1+"></td>";				 
					 newRow += "<td width ='20%'><input type='text' name= "+name2+" id="+name2+" value = "+val2+"></td>";
					 newRow += "<td width ='20%'><input type='text' name= "+name3+" id="+name3+" value = "+val3+"></td>";
					 newRow += "</tr>";
			}//end for row 

				//alert(newRow);
		$('#tbShiage').append(newRow);
	}     

}

function SaveData(status)
{
  var dataArray =[];
  var RoomIDs = [];
  var errorFlat = false;
  $("#tbShiage tr").each(function(){
	var id = $(this).closest('tr').attr('id');
	
	if(id != '' &&  typeof id !== 'undefined'){
			
		   var roomid = $(this).find('span[name="roomid"]').text().trim();
		   if(roomid == '' || roomid == 'undefined'){
			   roomid = $(this).find('input[name="roomid"]').val();
		   }

		   if(roomid == '' || roomid == 'undefined')return true;

			if(RoomIDs.indexOf(roomid) > -1){
				alert("部屋ID が複数なっています。");
				errorFlat = true;
				return false;//break loop
			}else{
				if(RoomIDs.indexOf(roomid) <= -1){
					RoomIDs.push(roomid);
				}								
			}

			var roomname = $(this).find('input[name="roomname"]').val();
			var naisoseigen1= $(this).find('input[name="naisoseigen1"]').val();
			var naisoseigen2= $(this).find('input[name="naisoseigen2"]').val();

			var yukatakasa1 = $(this).find('input[name="yukatakasa1"]').val();
			var yukashitaji1 = $(this).find('input[name="yukashitaji1"]').val();
			var yukashiage1 = $(this).find('input[name="yukashiage1"]').val();
			var yukatakasa2 = $(this).find('input[name="yukatakasa2"]').val();
			var yukashitaji2 = $(this).find('input[name="yukashitaji2"]').val();
			var yukashiage2 = $(this).find('input[name="yukashiage2"]').val();

			var habagitakasa1 = $(this).find('input[name="habagitakasa1"]').val();
			var habagishitaji1 = $(this).find('input[name="habagishitaji1"]').val();
			var habagishiage1 = $(this).find('input[name="habagishiage1"]').val();

			var kabe1 = $(this).find('input[name="kabe1"]').val();
			var kabeshitaji1 = $(this).find('input[name="kabeshitaji1"]').val();
			var kabeshiage1 = $(this).find('input[name="kabeshiage1"]').val();
			var kabe2 = $(this).find('input[name="kabe2"]').val();
			var kabeshitaji2 = $(this).find('input[name="kabeshitaji2"]').val();
			var kabeshiage2 = $(this).find('input[name="kabeshiage2"]').val();

			var tenjyotakasa1 = $(this).find('input[name="tenjyotakasa1"]').val();
			var tenjyoshitaji1 = $(this).find('input[name="tenjyoshitaji1"]').val();
			var tenjyoshiage1 = $(this).find('input[name="tenjyoshiage1"]').val();
			var tenjyotakasa2 = $(this).find('input[name="tenjyotakasa2"]').val();
			var tenjyoshitaji2 = $(this).find('input[name="tenjyoshitaji2"]').val();
			var tenjyoshiage2 = $(this).find('input[name="tenjyoshiage2"]').val();

			var mawarienshitaji1 = $(this).find('input[name="mawarienshitaji1"]').val();
			var mawarienshiage1 = $(this).find('input[name="mawarienshiage1"]').val();

			var sonota1 = $(this).find('input[name="sonota1"]').val();
			var sonotashitaji1 = $(this).find('input[name="sonotashitaji1"]').val();
			var sonotashiage1 = $(this).find('input[name="sonotashiage1"]').val();

			var sonota2 = $(this).find('input[name="sonota2"]').val();
			var sonotashitaji2 = $(this).find('input[name="sonotashitaji2"]').val();
			var sonotashiage2 = $(this).find('input[name="sonotashiage2"]').val();
			var sonota3 = $(this).find('input[name="sonota3"]').val();
			var sonotashitaji3 = $(this).find('input[name="sonotashitaji3"]').val();
			var sonotashiage3 = $(this).find('input[name="sonotashiage3"]').val();

			var sonota4 = $(this).find('input[name="sonota4"]').val();
			var sonotashitaji4 = $(this).find('input[name="sonotashitaji4"]').val();
			var sonotashiage4= $(this).find('input[name="sonotashiage4"]').val();
			var sonota5 = $(this).find('input[name="sonota5"]').val();
			var sonotashitaji5 = $(this).find('input[name="sonotashitaji5"]').val();
			var sonotashiage5 = $(this).find('input[name="sonotashiage5"]').val();

			var sonota6 = $(this).find('input[name="sonota6"]').val();
			var sonotashitaji6 = $(this).find('input[name="sonotashitaji6"]').val();
			var sonotashiage6= $(this).find('input[name="sonotashiage6"]').val();
			var sonota7 = $(this).find('input[name="sonota7"]').val();
			var sonotashitaji7 = $(this).find('input[name="sonotashitaji7"]').val();
			var sonotashiage7 = $(this).find('input[name="sonotashiage7"]').val();

			var sonota8 = $(this).find('input[name="sonota8"]').val();
			var sonotashitaji8 = $(this).find('input[name="sonotashitaji8"]').val();
			var sonotashiage8= $(this).find('input[name="sonotashiage8"]').val();
			var sonota9 = $(this).find('input[name="sonota9"]').val();
			var sonotashitaji9 = $(this).find('input[name="sonotashitaji9"]').val();
			var sonotashiage9 = $(this).find('input[name="sonotashiage9"]').val();

			var sonota10 = $(this).find('input[name="sonota10"]').val();
			var sonotashitaji10 = $(this).find('input[name="sonotashitaji10"]').val();
			var sonotashiage10 = $(this).find('input[name="sonotashiage10"]').val();
			var data = {
				roomid:roomid,roomname:roomname,naisoseigen1:naisoseigen1,naisoseigen2:naisoseigen2
				,yukatakasa1:yukatakasa1,yukashitaji1:yukashitaji1,yukashiage1:yukashiage1,yukatakasa2:yukatakasa2,yukashitaji2:yukashitaji2,yukashiage2:yukashiage2
				,habagitakasa1:habagitakasa1,habagishitaji1:habagishitaji1,habagishiage1:habagishiage1
				,kabe1:kabe1,kabeshitaji1:kabeshitaji1,kabeshiage1:kabeshiage1,kabe2:kabe2,kabeshitaji2:kabeshitaji2,kabeshiage2:kabeshiage2
				,tenjyotakasa1:tenjyotakasa1,tenjyoshitaji1:tenjyoshitaji1,tenjyoshiage1:tenjyoshiage1,tenjyotakasa2:tenjyotakasa2,tenjyoshitaji2:tenjyoshitaji2,tenjyoshiage2:tenjyoshiage2
				,mawarienshitaji1:mawarienshitaji1,mawarienshiage1:mawarienshiage1
				,sonota1:sonota1,sonotashitaji1:sonotashitaji1,sonotashiage1:sonotashiage1
				,sonota2:sonota2,sonotashitaji2:sonotashitaji2,sonotashiage2:sonotashiage2
				,sonota3:sonota3,sonotashitaji3:sonotashitaji3,sonotashiage3:sonotashiage3
				,sonota4:sonota4,sonotashitaji4:sonotashitaji4,sonotashiage4:sonotashiage4
				,sonota5:sonota5,sonotashitaji5:sonotashitaji5,sonotashiage5:sonotashiage5
				,sonota6:sonota6,sonotashitaji6:sonotashitaji6,sonotashiage6:sonotashiage6
				,sonota7:sonota7,sonotashitaji7:sonotashitaji7,sonotashiage7:sonotashiage7
				,sonota8:sonota8,sonotashitaji8:sonotashitaji8,sonotashiage8:sonotashiage8
				,sonota9:sonota9,sonotashitaji9:sonotashitaji9,sonotashiage9:sonotashiage9
				,sonota10:sonota10,sonotashitaji10:sonotashitaji10,sonotashiage10:sonotashiage10};
			dataArray.push(data);
	}
                 
   });

   //get data for excel create button
	if(status == "Excel" && dataArray.length > 0){
		return dataArray;
	}

	if( dataArray.length > 0 && !errorFlat)
	{		
		FindChangesData(dataArray);	 
	}
}
var changesArray = [];
var updateList = [];
var deleteList = [];
function FindChangesData(dataArray)
{
	//alert(JSON.stringify(dataArray));
	changesArray=[];
	var projectName = $("#selBox1 :selected").text();
	if(projectName.trim() == "")
	{
		alert("プロジェクトを選択してください！");
		return false;
	}
	$.ajax({
		type:"POST",
			url:"/RevitWebSystem/Shiage/getShiageData.php",			   
			data:{message:"get_shiage",docName:projectName},
			success:function(data)
			{	
				var dbData = JSON.parse(data);		
				if(dbData.length > 0){

					//findDeletedData(dbData,dataArray);//if not exist in db,it is deleted

					dataArray.forEach(function(element) {	
					var result = dbData.filter(x=>x.roomid.trim() == element.roomid.trim());					
					if(result.length > 0)
					{						
						//var result = dbData.filter( x => x.roomid === 101);
						var sortResult = result.sort((a,b) => (a.docID < b.docID) ? 1 : ((b.docID < a.docID) ? -1 : 0)); 
						var updateRow = sortResult.find(x=>x.roomid.trim() == element.roomid.trim());		
						//alert(JSON.stringify(updateRow));				
						var diffResultFlat  = objDiff(element,updateRow,"update");
						//alert(diffResultFlat);										
						if(diffResultFlat == true)
						{
							updateList.push(element);
						}
					}else{
						AddedData(element);//not exist in db..so,it is the new data
					}					
					});
				}else{
					var result = confirm(projectName+" 情報を更新しますか。！！");
					if (result == true) {
						SaveToDatabase(dataArray);
						return false;
					}else{
						return false;
					}
					return false;
				}
				
				//alert(JSON.stringify(changesArray.length));	
				if(changesArray.length > 0)
				{	
					$("#popupTable tbody tr").remove();			
					changesArray.forEach(function(item){
						var newRow = "<tr>";
						for(var i = 0; i < item.length; i++)
						{
							newRow += "<td>"+item[i]+"</td>";
						}
						newRow += "</tr>";
						$("#popupTable tbody").append(newRow);
					});	  
					//$("#hidData").val(datas);
					$("#overlay").css({ visibility: "visible",opacity: "1"});
				}else{
					alert("変化データはありません！！");
					return false;					
				}

			}
	});
}

function findDeletedData(dbData,dataArray)
{
	dbData.forEach(function(element){
		var result = dataArray.filter( x => x.roomid == element.roomid);
		if(result.length <= 0)
		{
			deleteList.push({"roomid":element.roomid,"docID":element.docID});
			changesArray.push([element.roomid,"　","","⇒","削除"]);
		}
	});

	//alert(JSON.stringify(deleteList));
}

function AddedData(data)
{
	var roomid = data.roomid;
	$.each(data, function (key, val) {		
		if(key == "id" || key == "version" || key == "roomid"){
			return true;
		}
		var mapKey = Mapping(key);
		changesArray.push([roomid,mapKey,"追加","⇒",val]);
	});
	updateList.push(data);
}

function UpdateData()
{
	var projectName = $("#selBox1 :selected").text();
	if(updateList.length > 0 )
	{
		//both update and delete room
		var result = confirm(projectName+" 情報を更新しますか。！！");
		if (result == true) {
			if(deleteList.length > 0)
			{
				DeleteRoom("");
			}
			SaveToDatabase(updateList);
		}else{
			return false;
		}
		
	}else if(deleteList.length > 0){//delete only
		var result = confirm(projectName+" の部屋情報を削除します。！！");
		if (result == true) {
			DeleteRoom("DeleteOnly");
		}else{
			return false;
		}
	}else{
		alert("変化データがありません。！！");
		return false;
	}
	
}

function DeleteRoom(status)
{
		//delete Data by requirement
	var projectName = $("#selBox1 :selected").text();
	if(deleteList.length > 0)
	{
		$.ajax({
			type:"POST",
			url:"/RevitWebSystem/Shiage/DeleteShiageInfo.php",			   
			data:{deleteData:deleteList,message:"delete_room",projectName:projectName},
			success:function(data)
			{
				if(data.includes("success") && status == "DeleteOnly")
				{
					alert("部屋情報削除しました。");
					location.reload();
				}
			}
		});
	}
}

function SaveToDatabase(dataArray)
{
 	//filter value from array, key isnt need to save db
	 var datas = [];	  ;
	dataArray.forEach(function(item) {
		var data = [];		
		Object.keys(item).forEach(function(key) {
			if(item[key] == null)
			{				
				data.push(" ");
			}else{
				data.push(item[key]);
			}							
		});
	datas.push(data);
	});
	//alert(JSON.stringify(datas));
	var projectName = $("#selBox1 :selected").text();		
	
	$.ajax({
		type:"POST",
		url:"/RevitWebSystem/Shiage/saveShiageInfo.php",			   
		data:{hidData:JSON.stringify(datas),docName:projectName},
		success:function(data)
		{	
			if(data.includes("success"))
			{
				alert("部屋仕上情報を登録しました。") ;
				location.reload();
			}else
			{
				alert(JSON.stringify(data));
			}			
		}
	});
}

function objDiff(tableRow, dbRow,status,changeVersion) {
    
	var i = 0;
	var equal = false;
	$.each(dbRow, function (key, val) {
		var dateTime = tableRow["update_time"];
        if(key == "id" || key == "docID" || key == "version" || key == "update_time"){
			return true;
		}
		
		if($.trim(dbRow[key]) != $.trim(tableRow[key]))
		{
			if(equal == false) equal = true;
			var val1 = dbRow[key]; 
			var val2 = tableRow[key];
			var mapKey = Mapping(key);

			//changesArray.push([dbRow.roomid,mapKey,val1,"⇒",val2]);
			if(status != "version")
			{
				changesArray.push([dbRow.roomid,mapKey,val1,"⇒",val2]);
			}else{
				changesArray.push([changeVersion,dbRow.roomid,mapKey,val1,"⇒",val2,dateTime]);
			}
			
		}		
    });     
    return equal;
}

function Mapping(key)
{
	var returnValue = "";
			switch(key)
			{
				case "roomname" :returnValue = "部屋名";break;
				case "naisoseigen1" :returnValue = "内装制限１";break;
				case "naisoseigen2" :returnValue = "内装制限２";break;
				case "yukatakasa1" :returnValue = "床高さ１";break;
				case "yukashitaji1" :returnValue = "床下地１";break;
				case "yukashiage1" :returnValue = "床仕上１";break;
				case "yukatakasa2" :returnValue = "床高さ２";break;
				case "yukashitaji2" :returnValue = "床下地２";break;
				case "yukashiage2" :returnValue = "床仕上２";break;
				
				case "habagitakasa1" :returnValue = "巾木高さ１";break;
				case "habagishitaji1" :returnValue = "巾木下地１";break;
				case "habagishiage1" :returnValue = "巾木仕上１";break;
				case "kabe1" :returnValue = "壁１";break;
				case "kabeshitaji1" :returnValue = "壁下地１";break;
				case "kabeshiage1" :returnValue = "壁仕上１";break;
				case "kabe2" :returnValue = "壁２";break;
				case "kabeshitaji2" :returnValue = "壁下地２";break;
				case "kabeshiage2" :returnValue = "壁仕上２";break;
				
				case "tenjyotakasa1" :returnValue = "天井高さ１";break;
				case "tenjyoshitaji1" :returnValue = "天井下地１";break;
				case "tenjyoshiage1" :returnValue = "天井仕上１";break;
				case "tenjyotakasa2" :returnValue = "天井高さ２";break;
				case "tenjyoshitaji2" :returnValue = "天井下地２";break;
				case "tenjyoshiage2" :returnValue = "天井仕上２";break;
				case "mawarienshitaji1" :returnValue = "廻縁下地１";break;
				case "mawarienshiage1" :returnValue = "廻縁仕上１";break;
				
				case "sonota1" :returnValue = "その他１";break;
				case "sonotashitaji1" :returnValue = "その他下地１";break;
				case "sonotashiage1" :returnValue = "その他仕上１";break;
				case "sonota2" :returnValue = "その他２";break;
				case "sonotashitaji2" :returnValue = "その他下地２";break;
				case "sonotashiage2" :returnValue = "その他仕上２";break;
				case "sonota3" :returnValue = "その他３";break;
				case "sonotashitaji3" :returnValue = "その他下地３";break;
				case "sonotashiage3" :returnValue = "その他仕上３";break;
				
				case "sonota4" :returnValue = "その他４";break;
				case "sonotashitaji4" :returnValue = "その他下地４";break;
				case "sonotashiage4" :returnValue = "その他仕上４";break;
				case "sonota5" :returnValue = "その他５";break;
				case "sonotashitaji5" :returnValue = "その他下地５";break;
				case "sonotashiage5" :returnValue = "その他仕上５";break;
				case "sonota6" :returnValue = "その他６";break;
				case "sonotashitaji6" :returnValue = "その他下地６";break;
				case "sonotashiage6" :returnValue = "その他仕上６";break;
				
				case "sonota7" :returnValue = "その他７";break;
				case "sonotashitaji7" :returnValue = "その他下地７";break;
				case "sonotashiage7" :returnValue = "その他仕上７";break;
				case "sonota8" :returnValue = "その他８";break;
				case "sonotashitaji8" :returnValue = "その他下地８";break;
				case "sonotashiage8" :returnValue = "その他仕上８";break;
				case "sonota9" :returnValue = "その他９";break;
				case "sonotashitaji9" :returnValue = "その他下地９";break;
				case "sonotashiage9" :returnValue = "その他仕上９";break;
				
				case "sonota10" :returnValue = "その他１０";break;
				case "sonotashitaji10" :returnValue = "その他下地１０";break;
				case "sonotashiage10" :returnValue = "その他仕上１０";break;
			}
			
			return returnValue;
}

function AddNewShiage()
{
	var rowCount = ($('#tbShiage tr').length)/20;
	if(rowCount < 1) {
		alert("Revitから部屋IDを書き出す必要です。");
		return;
	}
	rowCount = rowCount+1;
	var newRow = "<tr id="+rowCount+">"
					+"<td>"
					+"<table id='tbInner'>"
					+"<tr>"
							+"<th>ID:&nbsp<input type='text' name='roomid' size='5'/></th>"
							+"<th colspan='2'>部　位</th>"
							+"<th>下地</th>"
							+"<th>仕上</th>"
						+"</tr>";

			for(var j = 1 ; j<= 18; j++)
			{
				newRow += "<tr>";
					 //first colunm
					 if( j == 2) {
						newRow += "<td width='10%' rowspan='12'><input type='text' name='roomname' size='10'/></td>";
					 }else if(j == 1){
						newRow += "<td width='10%'>室名</td>";
					 }else if(j == 14 ){
						newRow += "<td width = '10%' >内装制限</td>";
					 }else if(j == 15)
					 {
						newRow += "<td width = '10%' rowspan='2'><input type='text' size='10' name='naisoseigen1' id='naisoseigen1'></td>";
					 }else if(j == 17)
					 {
						newRow += "<td width = '10%' rowspan='2'><input type='text' size='10' name='naisoseigen2' id='naisoseigen1'></td>";
					 }

					//second column
					if(j== 1){
						newRow += "<td width='10%' rowspan='2'>床</td>" ;
					 }else if(j== 3){
						newRow += "<td width='10%'>巾木</td>" ;
					 }else if(j == 4){
						newRow += "<td width='10%' rowspan='2'>柱・壁</td>";
					 }else if(j == 6){
						newRow += "<td width='10%'' rowspan='3'>天井</td>";
					 }else if(j== 9){
						newRow += "<td width='10%' rowspan='10'>他</td>";
					 } 

					 var name1 ="";
					 var name2 = "";
					 var name3 = ""; 
					 switch(j)
					 {
						 case 1 :   name1 = "yukatakasa1";name2 = "yukashitaji1";name3="yukashiage1";break;
						 case 2 :   name1 = "yukatakasa2";name2 = "yukashitaji2";name3="yukashiage2";break;
						 case 3 :   name1 = "habagitakasa1";name2 = "habagishitaji1";name3="habagishiage1";break;
						 case 4 :   name1 = "kabe1";name2 = "kabeshitaji1";name3="kabeshiage1";break;
						 case 5 :   name1 = "kabe2";name2 = "kabeshitaji2";name3="kabeshiage2";break;
						 case 6 :   name1 = "tenjyotakasa1";name2 = "tenjyoshitaji1";name3="tenjyoshiage1";break;
						 case 7 :   name1 = "tenjyotakasa2";name2 = "tenjyoshitaji2";name3="tenjyoshiage2";break;
						 case 8 :   name2 = "mawarienshitaji1";name3="mawarienshiage1";break;
						 case 9 :   name1 = "sonota1";name2 = "sonotashitaji1";name3="sonotashiage1";break;
						 case 10 :	name1 = "sonota2";name2 = "sonotashitaji2";name3="sonotashiage2";break;
						 case 11 : 	name1 = "sonota3";name2 = "sonotashitaji3";name3="sonotashiage3";break;
						 case 12 : 	name1 = "sonota4";name2 = "sonotashitaji4";name3="sonotashiage4";break;
						 case 13 : 	name1 = "sonota5";name2 = "sonotashitaji5";name3="sonotashiage5";break;
						 case 14 : 	name1 = "sonota6";name2 = "sonotashitaji6";name3="sonotashiage6";break;
						 case 15 : 	name1 = "sonota7";name2 = "sonotashitaji7";name3="sonotashiage7";break;
						 case 16 : 	name1 = "sonota8";name2 = "sonotashitaji8";name3="sonotashiage8";break;
						 case 17 : 	name1 = "sonota9";name2 = "sonotashitaji9";name3="sonotashiage9";break;
						 case 18 : 	name1 = "sonota10";name2 = "sonotashitaji10";name3="sonotashiage10";break;
					 }
	
					 newRow += "<td width ='20%'><input type='text' name= "+name1+" id="+name1+"></td>";				 
					 newRow += "<td width ='20%'><input type='text' name= "+name2+" id="+name2+"></td>";
					 newRow += "<td width ='20%'><input type='text' name= "+name3+" id="+name3+"></td>";
					 newRow += "</tr>";
			}

			$("#tbShiage").append(newRow);
			$("#inner").scrollTop($("#inner")[0].scrollHeight);
}

function SearchShiage()
{
	var projectName = $("#selBox1 :selected").text();
	var rdo = $("input[name='rdoSearch']:checked").val();
	var searchText = $("#searchText").val();

	if(searchText == "" || searchText == "undefined")return;
	var message="";
	if(rdo == "id")
		message = "search_byID";
	else
		message = "search_byName";
		
	$.ajax({
		type:"POST",
		url:"/RevitWebSystem/Shiage/getShiageData.php",			   
		data:{message:message,docName:projectName,search:searchText},
		success:function(data)
		{
			var projectData = JSON.parse(data);
			if(projectData.length > 0)
			{	var changePoject = [];
				projectData.forEach(function(e) {
					var data = Object.values(e);
					data.shift();//remove id
					data.shift();//remove docID
					changePoject.push(data);
				});			
				displayData(Object.values(changePoject));
			}else{
				alert("検索情報が登録されていません。");
				//$("#tbShiage tr").remove();
			}
			
			
		},
		error:function(data){
			alert("data");
		}
	});
}

function DisplayShiageDeletePopup(){

	$("#overlayDelete").css({ visibility: "visible",opacity: "1"});
	var projectName = $("#selBox1 :selected").text();
	$("#deleteRoom tbody tr").remove();
	$.ajax({
		type:"POST",
			url:"/RevitWebSystem/Shiage/getShiageData.php",			   
			data:{message:"get_shiage",docName:projectName},
			success:function(data)
			{
				var result = JSON.parse(data);
				if(result.length > 0){
					var newRow = "";
					
					result.forEach(function(room){
						newRow += "<tr>";
						newRow += "<td width='50px'><input type='checkbox' name='chkDelete' id='chkDelete'/></td>";
						newRow += "<td>"+room['roomid']+"</td>";
						newRow += "<td>"+room['roomname']+"</td>";
						newRow += "</tr>";
						
					});
			    	
				$("#deleteRoom ").append(newRow);	
				}
			}
	})
}

function DeleteRoom(){
	var projectName = $("#selBox1 :selected").text();
	var deleteList = [];
	$('#deleteRoom tr').each(function() {
		var chk = $(this).find("input[type=checkbox]");
		if(chk.prop('checked')==true){			
			var roomid =	$(this).find("td:eq(1)").text();
			deleteList.push(roomid);
		}

	});

	if(deleteList.length > 0){
		$.ajax({
			type:"POST",
				url:"/RevitWebSystem/Shiage/DeleteShiageInfo.php",			   
				data:{message:"deleteFromButton",docName:projectName,data:JSON.stringify(deleteList)},
				success:function(data)
				{
					if(data.includes("success")){
						alert("削除しました。");
						$("#overlayDelete").css({ visibility: "hidden",opacity: "0"});
						ChangeProject();
					}else{
						alert(data);
					}
				}
		});
	}
	
}

/*function ShiageExcelCreate(){
	
	var datas = SaveData("Excel");
	//alert(datas.length);
	if(datas.length > 0){
		var url = '/RevitWebSystem/Shiage/ShiageExcel.php?data='+JSON.stringify(datas);
		document.ShiageExcelCreateForm.action = url;
		document.ShiageExcelCreateForm.submit();
	}
	
}*/



function EditExcelFile(workbook,sheetName){
	var datas = SaveData("Excel");
	if(datas.length > 0){
		var sheet1 = workbook.worksheets(0);
		var sheet2 = workbook.worksheets(1);

		var rowIndex = 2;
		var firstSheetRowIndex = 2;
		datas.forEach(function(data){
																
			//set cell header values
			var cellIndex=rowIndex+1;
			sheet2.getCell("A"+cellIndex).value("室名");	
			cellIndex=rowIndex;
			sheet2.getCell("B"+cellIndex).value("部　位");
			sheet2.getCell("B"+cellIndex).cellFormat().fill($.ig.excel.CellFill.createSolidFill("#ccc"));	
			sheet2.getCell("D"+cellIndex).value("下地");
			sheet2.getCell("D"+cellIndex).cellFormat().fill($.ig.excel.CellFill.createSolidFill("#ccc"));
			sheet2.getCell("E"+cellIndex).value("仕上");
			sheet2.getCell("E"+cellIndex).cellFormat().fill($.ig.excel.CellFill.createSolidFill("#ccc"));
			cellIndex=rowIndex+1;
			sheet2.getCell("B"+cellIndex).value("床");
			cellIndex=rowIndex+3;
			sheet2.getCell("B"+cellIndex).value("巾木");	
			cellIndex=rowIndex+4;
			sheet2.getCell("B"+cellIndex).value("柱・壁");
			cellIndex=rowIndex+6;
			sheet2.getCell("B"+cellIndex).value("天井");
			cellIndex=rowIndex+9;
			sheet2.getCell("B"+cellIndex).value("他");
			cellIndex=rowIndex+14;
			sheet2.getCell("A"+cellIndex).value("内装制限");

			SetCellBorder(sheet2,rowIndex);						
			MergeCell(sheet2,rowIndex);
			//setting sheet2 of values
			SetCellValue(sheet2,data,rowIndex);

			//set reference to sheet 1
			SetFirstSheetReference(sheet1,sheetName,firstSheetRowIndex,rowIndex);
			

			firstSheetRowIndex++;
			rowIndex = rowIndex+19;
		});
			
		var projectName = $("#selBox1 :selected").text();
		saveWorkbook(workbook,projectName+"_部屋仕上表.xlsm");
	}
	
}

function SetCellValue(sheet2,data,rowIndex){
	var roomidIndex = rowIndex;
	var index =rowIndex;
	sheet2.getCell("A"+index).value(data.roomid);
	sheet2.getCell("A"+index).cellFormat().fill($.ig.excel.CellFill.createSolidFill("#ccc"));
	index = rowIndex+2;
	sheet2.getCell("A"+index).value(data.roomname);

	index=rowIndex+1;
	sheet2.getCell("C"+index).value(data.yukatakasa1);
	sheet2.getCell("D"+index).value(data.yukashitaji1);
	sheet2.getCell("E"+index).value(data.yukashiage1);

	index=rowIndex+2;
	sheet2.getCell("C"+index).value(data.yukatakasa2);
	sheet2.getCell("D"+index).value(data.yukashitaji2);
	sheet2.getCell("E"+index).value(data.yukashiage2);

	index=rowIndex+3;
	sheet2.getCell("C"+index).value(data.habagitakasa1);
	sheet2.getCell("D"+index).value(data.habagishitaji1);
	sheet2.getCell("E"+index).value(data.habagishiage1);

	index=rowIndex+4;
	sheet2.getCell("C"+index).value(data.kabe1);
	sheet2.getCell("D"+index).value(data.kabeshitaji1);
	sheet2.getCell("E"+index).value(data.kabeshiage1);

	index=rowIndex+5;
	sheet2.getCell("C"+index).value(data.kabe2);
	sheet2.getCell("D"+index).value(data.kabeshitaji2);
	sheet2.getCell("E"+index).value(data.kabeshiage2);
	index=rowIndex+6;
	sheet2.getCell("C"+index).value(data.tenjyotakasa1);
	sheet2.getCell("D"+index).value(data.tenjyoshitaji1);
	sheet2.getCell("E"+index).value(data.tenjyoshiage1);

	index=rowIndex+7;
	sheet2.getCell("C"+index).value(data.tenjyotakasa2);
	sheet2.getCell("D"+index).value(data.tenjyoshitaji2);
	sheet2.getCell("E"+index).value(data.tenjyoshiage2);

	index=rowIndex+8;
	sheet2.getCell("C"+index).value("廻縁");
	sheet2.getCell("D"+index).value(data.mawarienshitaji1);
	sheet2.getCell("E"+index).value(data.mawarienshiage1);

	index=rowIndex+9;
	sheet2.getCell("C"+index).value(data.sonota1);
	sheet2.getCell("D"+index).value(data.sonotashitaji1);
	sheet2.getCell("E"+index).value(data.sonotashiage1);

	index=rowIndex+10;
	sheet2.getCell("C"+index).value(data.sonota2);
	sheet2.getCell("D"+index).value(data.sonotashitaji2);
	sheet2.getCell("E"+index).value(data.sonotashiage2);
	index=rowIndex+11;
	sheet2.getCell("C"+index).value(data.sonota3);
	sheet2.getCell("D"+index).value(data.sonotashitaji3);
	sheet2.getCell("E"+index).value(data.sonotashiage3);
	index=rowIndex+12;
	sheet2.getCell("C"+index).value(data.sonota4);
	sheet2.getCell("D"+index).value(data.sonotashitaji4);
	sheet2.getCell("E"+index).value(data.sonotashiage4);
	index=rowIndex+13;
	sheet2.getCell("C"+index).value(data.sonota5);
	sheet2.getCell("D"+index).value(data.sonotashitaji5);
	sheet2.getCell("E"+index).value(data.sonotashiage5);
	index=rowIndex+14;
	sheet2.getCell("C"+index).value(data.sonota6);
	sheet2.getCell("D"+index).value(data.sonotashitaji6);
	sheet2.getCell("E"+index).value(data.sonotashiage6);
	index=rowIndex+15;
	sheet2.getCell("C"+index).value(data.sonota7);
	sheet2.getCell("D"+index).value(data.sonotashitaji7);
	sheet2.getCell("E"+index).value(data.sonotashiage7);
	index=rowIndex+16;
	sheet2.getCell("C"+index).value(data.sonota8);
	sheet2.getCell("D"+index).value(data.sonotashitaji8);
	sheet2.getCell("E"+index).value(data.sonotashiage8);
	index=rowIndex+17;
	sheet2.getCell("C"+index).value(data.sonota9);
	sheet2.getCell("D"+index).value(data.sonotashitaji9);
	sheet2.getCell("E"+index).value(data.sonotashiage9);
	index=rowIndex+18;
	sheet2.getCell("C"+index).value(data.sonota10);
	sheet2.getCell("D"+index).value(data.sonotashitaji10);
	sheet2.getCell("E"+index).value(data.sonotashiage10);
	index=rowIndex+15;
	sheet2.getCell("C"+index).value(data.naisoseigen1);
	index=rowIndex+17;
	sheet2.getCell("E"+index).value(data.naisoseigen2);
}

function SetFirstSheetReference(sheet1,sheetName,firstSheetRowIndex,rowIndex){
	sheet1.getCell ("A"+firstSheetRowIndex).applyFormula("="+sheetName+"!A"+rowIndex);
	row = rowIndex+2;
	sheet1.getCell ("B"+firstSheetRowIndex).applyFormula("="+sheetName+"!A"+row);
    row = rowIndex+15;
	sheet1.getCell ("C"+firstSheetRowIndex).applyFormula("="+sheetName+"!A"+row);
	row = rowIndex+17;
	sheet1.getCell ("D"+firstSheetRowIndex).applyFormula("="+sheetName+"!A"+row);

	row = rowIndex+1;
	var count = 1;
	firstSheetRowIndex = firstSheetRowIndex-1;//row start zero
	for(var col = 4; col < 57; col++){

		if(count == 1){
			if(col != 25){//skip 廻り縁
				sheet1.rows(firstSheetRowIndex).cells(col).applyFormula("="+sheetName+"!C"+row);
			}else{
				col--;//not increae column 
			}
			
			count++;
		}else if(count == 2){
			sheet1.rows(firstSheetRowIndex).cells(col).applyFormula("="+sheetName+"!D"+row);
			count++;
		}else if(count == 3){
			sheet1.rows(firstSheetRowIndex).cells(col).applyFormula("="+sheetName+"!E"+row)
			count = 1;
			row = row+1;
		}			
	}
	
}

function MergeCell(sheet2,rowIndex){
	var mergeRowIndex = rowIndex-1;//count from zero 
	var mergedRegion = sheet2.mergedCellsRegions().add(mergeRowIndex,1,mergeRowIndex,2);//0 = "A",1="B",2="C"
	var start = rowIndex+1;
	var end = rowIndex+12;
	mergedRegion = sheet2.mergedCellsRegions().add(start,0,end,0);
	start=rowIndex+14;
	end=rowIndex+15;
	mergedRegion = sheet2.mergedCellsRegions().add(start,0,end,0);
	start=rowIndex+16;
	end=rowIndex+17;
	mergedRegion = sheet2.mergedCellsRegions().add(start,0,end,0);
	start=rowIndex;
	end=rowIndex+1;
	mergedRegion = sheet2.mergedCellsRegions().add(start,1,end,1);
	start=rowIndex+3;
	end=rowIndex+4;
	mergedRegion = sheet2.mergedCellsRegions().add(start,1,end,1);
	start=rowIndex+5;
	end=rowIndex+7;
	mergedRegion = sheet2.mergedCellsRegions().add(start,1,end,1);
	start=rowIndex+8;
	end=rowIndex+17;
	mergedRegion = sheet2.mergedCellsRegions().add(start,1,end,1);
}

function SetCellBorder(sheet2,rowIndex){

	for(var i = rowIndex ; i <= rowIndex+19; i++){//i=row
		for(var j = 0 ; j< 5; j++){//j=column
			sheet2.rows(i).cells(j).cellFormat().bottomBorderStyle($.ig.excel.CellBorderLineStyle.thin);			
			sheet2.rows(i).cells(j).cellFormat().leftBorderStyle($.ig.excel.CellBorderLineStyle.thin);
			sheet2.rows(i).cells(j).cellFormat().rightBorderStyle($.ig.excel.CellBorderLineStyle.thin);
			sheet2.rows(i).cells(j).cellFormat().topBorderStyle($.ig.excel.CellBorderLineStyle.thin);
		}
		
	}
	
}

function saveWorkbook(workbook, name) {
    workbook.save({ type: 'blob' }, function (data) {
        saveAs(data, name);
    }, function (error) {
        alert('エクスポート エラー: : ' + error);
    });
}







