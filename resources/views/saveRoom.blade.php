@extends('layouts.baselayout')

@section('head')
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/shiageHome.js"></script>
<script src="../public/js/shim.js"></script>

<script lang="javascript" src="../public/js/xlsx.full.min.js"></script>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.9.10/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.3/FileSaver.min.js"></script>


<!-- External files for exporting -->
<script src="https://jp.igniteui.com/js/external/FileSaver.js"></script>
<script src="https://jp.igniteui.com/js/external/Blob.js"></script>

<script src="http://ajax.aspnetcdn.com/ajax/modernizr/modernizr-2.8.3.js"></script>
<script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="http://code.jquery.com/ui/1.11.1/jquery-ui.min.js"></script>

<!-- Ignite UI Required Combined JavaScript Files -->
<script src="../public/js/infragistics.core.js"></script>
<script src="../public/js/infragistics.lob.js"></script>
<script src="../public/js/infragistics.excel-bundled.js"></script>
<script src="../public/js/infragistics.spreadsheet-bundled.js"></script>
<script>
	function ClosePopup()
	{		
		$("#overlay").css({ visibility: "hidden",opacity: "0"});
	}
	function ClosePopupProject()
	{		
		$("#overlayProjectForm").css({ visibility: "hidden",opacity: "0"});
	}
	function ClosePopupVersionDisplay()
	{		
		$("#overlayVersionDisplay").css({ visibility: "hidden",opacity: "0"});
	}
	function CloseDeletePopup()
	{		
		$("#overlayDelete").css({ visibility: "hidden",opacity: "0"});
	}

	

</script>
<style>
fieldset{
    float: left;
    width: 50%;
    margin-left: 20px;
    padding: 10px 0px 10px 10px;
    box-sizing: border-box;
}
</style>
@endsection

@section('content')
<div class="main-content">

<input type="hidden" id="hidLoginID" name="hidLoginID" value="{{Session::get('login_user_id')}}"/>

<div id="overlay" class="popupOverlay">
	<div class="popup">
		<h4 align="center">変化情報</h4>
		<input type="button" name="douki" value="更新" onClick = "UpdateData()"/></br></br>		
		<a class="close" href="javascript:void(0);" onClick ="ClosePopup()" style="color:blue">&times;</a>
		<div class="content">
			<table id="popupTable">
				<thead>
				<tr>
					<th>部屋ID</th>
					<th>Property</th>
					<th>From</th>
					<th>～</th>
					<th>To</th>	
				</tr>
				</thead>
				<tbody>
				</tbody>			
			</table>
				
		</div>
	</div>
</div> 

<div id="overlayVersionDisplay" class="popupOverlay">
	<div class="popup" id="tbVersionPopupSize">
		<h4 align="center">変化情報</h4></br>
		<select name='select' class='selectBox' id='selBox2' onChange='ChangeVersion()'>
		</select>
		～
		<select name='select' class='selectBox' id='selBox3' onChange='ChangeVersion()'>
		</select>			
		<a class="close" href="javascript:void(0);" onClick ="ClosePopupVersionDisplay()" style="color:blue">&times;</a>
		</br>
		<div class="content">
			<table id="tbVersion">
				<thead>
				<tr>
					<th width="60px">Ver</th>
					<th width="50px">部屋ID</th>
					<th width="120px">Property</th>
					<th width="190">From</th>
					<th width="10px">～</th>
					<th width="190">To</th>	
					<th width="100px"s>更新日時</th>	
				</tr>
				</thead>
				<tbody>
				</tbody>			
			</table>
				
		</div>
	</div>
</div> 

<div id="overlayDelete" class="popupOverlay">
	<div class="popup" id="deletePopupSize">
		<h4 align="center">部屋仕上削除</h4>
		<input type="button" name="deleteRoom" value="削除" onClick ="DeleteRoom()"/></br></br>		
		<a class="close" href="javascript:void(0);" onClick ="CloseDeletePopup()"><span style="color:black">&times;</span></a>
		<div class="content">
			<table id="deleteRoom">	
				<thead>
					<tr>
						<th></th>
						<th>部屋ID</th>
						<th>部屋名</th>
					</tr>	
				</thead>
				<tbody>
				</tbody>											
			</table>
				
		</div>
	</div>
</div> 



	<h4 align="center">部屋仕上情報</h4></br>
	<div style="padding-top:10px;">
		プロジェクト名：	
		<select name='select' class='selectBox' id='selBox1' onChange='ChangeProject()' >
		</select>&nbsp&nbsp&nbsp
		<input type="button" class="btn btn-primary" name="btnExcel" id="btnExcel" value="エクセル作成"/>&nbsp&nbsp&nbsp
		<input type="button" class="btn btn-primary" name="btnDelete" id="btnDelete" value="削除" onClick="DisplayShiageDeletePopup()"/>&nbsp&nbsp&nbsp		
	</div>						
	<!--	<div style="padding-top:15px">-->
		<form name="ShiageExcelCreateForm" method="post">&nbsp&nbsp&nbsp	&nbsp&nbsp&nbsp	&nbsp&nbsp&nbsp	&nbsp&nbsp&nbsp	&nbsp&nbsp&nbsp	&nbsp&nbsp&nbsp	
		
			</form>
		<!--</div>-->


		<fieldset style="width:400px;">
		<legend>部屋仕上登録</legend>
			<input type="file" name="file_upload" id ="file_upload"/>	
			<input type="button"  class="btn btn-primary" value=" 保存 "  onClick = "SaveData()"/>&nbsp&nbsp&nbsp	 
			<input type="button" class="btn btn-primary" value="追加" onclick="AddNewShiage()"/> 
		</fieldset>			

		<fieldset style="width:550px;">
			<legend>部屋仕上検索</legend>
			<input type="radio" name="rdoSearch" value="id" checked="checked">部屋ID
			<input type="radio" name="rdoSearch" value="name">部屋名
			<input type="text" name="searchText" id="searchText" size="15" placeholder="部屋ID・部屋名"/>&nbsp&nbsp&nbsp
			<input type="button" class="btn btn-primary" name="btnSearch" id="btnSearch" value="検索" onClick="SearchShiage()"/>&nbsp&nbsp&nbsp
			<input type="button"  class="btn btn-primary" value=" バージョンごと表示 "  onClick = "DisplayByVersion()"/>   
		</fieldset>
 

    <div class="col-10  centerDesign" id="inner" style="height:71vh;">

	<table id="tbShiage" align="center">
    @php($count=0)
		@foreach ($data as $row)
		@php($count++)
		<tr id={{$count}}>
			<td>
				<table id='tbInner'>
				<tr>
						<th>ID:&nbsp<span name='roomid'>{{$row[roomid]}}</span></th>
						<th colspan="2">部　位</th>
						<th>下地</th>
						<th>仕上</th>
				</tr>
					@for ($i = 1; $i <= 18; $i++)
					<tr>
						@php($val1 = "")
						@php($val2 = "")
						@php($val3 = "")
						@php($name1 ="")
						@php($name2 = "")
						@php($name3 = "")
						@if( $i == 2) 
						<td width='10%' rowspan='12'><input type='text' name='roomname' size='10' value= ".{{$row['roomname']}}."></td>
						@elseif($i == 1)
							<td width="10%">室名</td>
						@elseif($i == 14 )
							<td width = "10%" >内装制限</td>
						@elseif($i == 15)
						  <td width = '10%' rowspan='2'><input type='text' class='resizedTextbox' size='10' name='naisoseigen1' id='naisoseigen1' value = {{$row[naisoseigen1]}}></td>
						@elseif($i == 17)
							<td width = '10%' rowspan='2'><input type='text' class='resizedTextbox'size='10' name='naisoseigen2' id='naisoseigen2' value = {{$row[naisoseigen2]}}></td>
						@endif
						
						@if($i== 1)
							<td width="10%" rowspan="2">床</td> 
					  @elseif($i== 3)
							<td width="10%">巾木</td> 
						@elseif($i == 4)
							<td width="10%" rowspan="2">柱・壁</td>
						@elseif($i == 6)
							<td width="10%" rowspan="3">天井</td>
						@else if($i== 9)
							<td width="10%" rowspan="10">他</td>
						@endif
						
						@switch($i)
							@case(1): $val1 = {{$row['yukatakasa1']}};$val2 = {{$row['yukashitaji1']}};$val3 = {{$row['yukashiage1']}}
									      $name1 = "yukatakasa1";$name2 = "yukashitaji1";$name3="yukashiage1"
									      @break
							@case(2) : $val1 = $row['yukatakasa2'];$val2 = $row['yukashitaji2'];$val3 = $row['yukashiage2'];
									$name1 = "yukatakasa2";$name2 = "yukashitaji2";$name3="yukashiage2";
									@break;
							@case(3) : $val1 = $row['habagitakasa1'];$val2 = $row['habagishitaji1'];$val3 = $row['habagishiage1'];
									$name1 = "habagitakasa1";$name2 = "habagishitaji1";$name3="habagishiage1";@break;
							@case(4) : $val1 = $row['kabe1'];$val2 = $row['kabeshitaji1'];$val3 = $row['kabeshiage1'];
									$name1 = "kabe1";$name2 = "kabeshitaji1";$name3="kabeshiage1";@break;
							@case(5) : $val1 = $row['kabe2'];$val2 = $row['kabeshitaji2'];$val3 = $row['kabeshiage2'];
									$name1 = "kabe2";$name2 = "kabeshitaji2";$name3="kabeshiage2";@break;
							@case(6) : $val1 = $row['tenjyotakasa1'];$val2 = $row['tenjyoshitaji1'];$val3 = $row['tenjyoshiage1'];
									$name1 = "tenjyotakasa1";$name2 = "tenjyoshitaji1";$name3="tenjyoshiage1";@break;
							@case(7) : $val1 = $row['tenjyotakasa2'];$val2 = $row['tenjyoshitaji2'];$val3 = $row['tenjyoshiage2'];
									$name1 = "tenjyotakasa2";$name2 = "tenjyoshitaji2";$name3="tenjyoshiage2";@break;
							@case(8) : $val2 = $row['mawarienshitaji1'];$val3 = $row['mawarienshiage1'];
									$name2 = "mawarienshitaji1";$name3="mawarienshiage1";@break;
							@case(9) : $val1 = $row['sonota1'];$val2 = $row['sonotashitaji1'];$val3 = $row['sonotashiage1'];
									$name1 = "sonota1";$name2 = "sonotashitaji1";$name3="sonotashiage1";@break;
							@case(10) :$val1 = $row['sonota2'];$val2 = $row['sonotashitaji2'];$val3 = $row['sonotashiage2'];
									$name1 = "sonota2";$name2 = "sonotashitaji2";$name3="sonotashiage2";@break;
							@case(11) : $val1 = $row['sonota3'];$val2 = $row['sonotashitaji3'];$val3 = $row['sonotashiage3'];
									$name1 = "sonota3";$name2 = "sonotashitaji3";$name3="sonotashiage3";@break;
							@case(12) : $val1 = $row['sonota4'];$val2 = $row['sonotashitaji4'];$val3 = $row['sonotashiage4'];
									$name1 = "sonota4";$name2 = "sonotashitaji4";$name3="sonotashiage4";@break;
							@case(13) : $val1 = $row['sonota5'];$val2 = $row['sonotashitaji5'];$val3 = $row['sonotashiage5'];
									$name1 = "sonota5";$name2 = "sonotashitaji5";$name3="sonotashiage5";@break;
							@case(14) : $val1 = $row['sonota6'];$val2 = $row['sonotashitaji6'];$val3 = $row['sonotashiage6'];
									$name1 = "sonota6";$name2 = "sonotashitaji6";$name3="sonotashiage6";@break;
							@case(15) : $val1 = $row['sonota7'];$val2 = $row['sonotashitaji7'];$val3 = $row['sonotashiage7'];
									$name1 = "sonota7";$name2 = "sonotashitaji7";$name3="sonotashiage7";@break;
							@case(16) : $val1 = $row['sonota8'];$val2 = $row['sonotashitaji8'];$val3 = $row['sonotashiage8'];
									$name1 = "sonota8";$name2 = "sonotashitaji8";$name3="sonotashiage8";@break;
							@case(17) : $val1 = $row['sonota9'];$val2 = $row['sonotashitaji9'];$val3 = $row['sonotashiage9'];
									$name1 = "sonota9";$name2 = "sonotashitaji9";$name3="sonotashiage9";@break;
							@case(18) : $val1 = $row['sonota10'];$val2 = $row['sonotashitaji10'];$val3 = $row['sonotashiage10'];
									$name1 = "sonota10";$name2 = "sonotashitaji10";$name3="sonotashiage10";@break;
						@endswitch
					<td width ='20%'><input type='text' name=$name1 id=$name1 value = $val1></td>
					<td width ='20%'><input type='text' name=$name2 id=$name2 value = $val2></td>
					<td width ='20%'><input type='text' name=$name3 id=$name3 value = $val3></td>

					</tr>
				@endfor
				
				</table>
			</td>
		</tr> 
	@endforeach	
	</table>
    </div>


</div>
   

@endsection