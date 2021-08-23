@extends('layouts.baselayout')
@section('title', 'CCC - Addin Usage tracking')

@section('head')
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>
<script type="text/javascript" src="../public/js/viewUser.js"></script>
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/redmond/jquery-ui.css" >
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/i18n/jquery.ui.datepicker-ja.min.js"></script>
<script>

	$(function() {
		$.datepicker.setDefaults( $.datepicker.regional[ "ja" ] );
		$('#startDate').datepicker({
			//minDate: '-70y', //今日から70年前
			changeYear: true, //表示年の指定が可
			onSelect: function(dateText){ //西暦→和暦に変換して表示
				var date = dateText.split('/');
				//var wareki = date[0] - 1988;
				//dateText =   date[2] +'-'+ date[0] +'-'+ date[1] ;
				dateText =  date[0] +'-'+ date[1] +'-'+ date[2];
				$(this).val(dateText);
			}
		});
		$('#endDate').datepicker({
			//minDate: '-70y', //今日から70年前
			changeYear: true, //表示年の指定が可
			onSelect: function(dateText){ //西暦→和暦に変換して表示
				var date = dateText.split('/');	
				//dateText =   date[2] +'-'+ date[0] +'-'+ date[1] ;		
				dateText =   date[0] +'-'+ date[1] +'-'+ date[2] ;
				$(this).val(dateText);
			}
		});		
	});
	
	$(document).ready(function(){
	    var login_user_id = $("#hidLoginID").val();
	    var img_src = "../public/image/JPG/原子力のフリーイラスト3.jpeg";
	    var url = "common/userInfo";
	    var content_name = "ｱﾄﾞｲﾝ使用状況追跡";
	    recordAccessHistory(login_user_id,img_src,url,content_name);
	});

</script>
<style>

#tbUserControl td{
	border:1px solid gray;
	text-align:center;
}

#tbUserControl th{
	border: 1px solid gray;

}
#tbUserControl td:nth-child(3){
	text-align:center;
}
#tbUserControl td:nth-child(1),#tbUserControl td:nth-child(2){
	text-align:left;
}
.popupOverlay {
	position: fixed;
	top: 0;
	bottom: 0;
	left: 0;
	right: 0;
	background: rgba(0, 0, 0, 0.7);
	transition: opacity 500ms;
	visibility: hidden;
	opacity: 0;
  }
  .popupOverlayBackground{
	background:none; 
	transition: opacity 0ms;
  }
  popupOverlay:target {
	visibility: visible;
	opacity: 1;
  }
  
  .popup {
	margin: 70px auto;
	padding: 20px;
	background: #fff;
	border-radius: 5px;
	width: 30%;
	height:80%;
	position: relative;
	transition: all 0s ease-in-out;
  }
  .popupBackground {
	padding: 2px 10px 10px 2px;
	background:#ccc;
  }
  .popup h2 {
	margin-top: 0;
	color: blue;
	text-align:center;
	font-family: Tahoma, Arial, sans-serif;
  }
  .popup h3 {
	margin-top: 0;
	color: #fff;
	text-align:center;
	font-family: Tahoma, Arial, sans-serif;
  }
  .popup .close {
	position: absolute;
	top: 20px;
	right: 30px;
	transition: all 10ms;
	font-size: 30px;
	font-weight: bold;
	text-decoration: none;
	color: #fff;
  }
  .popup .close:hover {
	color: #06D85F;
  }
  .popup .content {
	max-height: 90%;
	overflow: auto;
  }
  #KisoPopupSize{
	width:752px;
	height:536px;
}
#tbUser{
    margin-bottom:9vh;
}
</style>
@endsection

@section('content')
<div id="UserControlPopup" class="popupOverlay popupOverlayBackground">
	<div class="popup popupBackground" id="KisoPopupSize">
		<div class="PopupHeader hashiraSize">
			<h3>ユーザーコントロール</h3>
			<a class="close" href="javascript:void(0);" onClick ="CloseUserControlPopup()" style="top:2px;">&times;</a>
		</div></br>
		<div class="content" style="max-height:400px;">				
			<table style="border-collapse: collapse;width:600px;overflow:auto;margin-left: 70px;" id="tbUserControl">
			<thead>
				<tr>
					<th rowspan="2" width="50px">No.</th>
					<th rowspan="2">ユーザー名</th>
					<th rowspan="2" width="70px">無許可</th>
					<th colspan="3">変更履歴登録</td>								
				</tr>
				<tr>
					<th >追加</th>
					<th>修正</th>
					<th>削除</th>		
				</tr>									
			</thead>
			<tbody>			
			</tbody>
						
			</table>				
		</div>
		<div class="PopupFooter hashiraSize" id="tbKozoMaterialFooter">
			<div style="float:right">
				<input type="button" name="btnSaveUserControl" id="btnSaveUserControl" onClick ="SaveUserControl()" value="保存"/>&nbsp&nbsp&nbsp
				<input type="button" name="btnCancel" id="btnCancel" onClick ="CloseUserControlPopup()" value="キャンセル"/>&nbsp&nbsp&nbsp
			</div>				
		</div>	
	</div>
</div> 

<input type="hidden" id="hidLoginID" name="hidLoginID" value="{{Session::get('login_user_id')}}"/>

<div class="main-content">
	<h4 class="page-title">アドイン使用状況追跡</h4>	    
      <span>名前 : </span><input type="text" name="projectName" id= "projectName"/>&nbsp&nbsp&nbsp
        日付：<input type="text" name="startDate" id="startDate" placeholder="　　年　-　月　-　日"/>
        <span > ～ &nbsp</span>
        <input type="text" name="endDate" id="endDate" placeholder="　　年　-　月　-　日"/>&nbsp&nbsp&nbsp
        <input type="button" class="btn btn-primary" name="saveproject" value="検索" onclick ="SearchUserData()"/> &nbsp&nbsp&nbsp
		<input type="button" class="btn btn-primary" name="userControl" value="ユーザーコントロール" onclick ="UserControlPopupDisplay()"/>
	<br><br>
   <table id="tbUser" width="100%" align="center">
		<thead>
			<tr>
				<th width="5%">No.</th>
				<th width = "25%">名前</th>	
                <th width="40%">プロジェクト名</th>
                <th>ボタン名</th>				
				<th>カウント</th>
				<th>日付</th>			
			</tr>
			</thead>
			<tbody>
			<tr>
				<td height= "20"></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			</tbody>	
		</table>
</div>
@endsection