@extends('layouts.baselayout')
@section('title', 'CCC - Authority Item Set Setting')

@section('head')
<script type="text/javascript" src="/iPD/public/js/jquery-ui.min.js"></script>
<!--<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>-->
<script type="text/javascript" src="/iPD/public/js/projectAccessAuthorityItemSet.js"></script>
<!--<script src="../public/js/shim.js"></script>-->
<!--<script src="../public/js/xlsx.full.min.js"></script>-->
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
<script>
</script>
<style>


.main-content{
	/*background-color: #f2f3f3;*/
	margin: 0 0% 0% 0%;
	width:100%;
}
.centering{
	width: 1000px;
	margin: 0 auto;
}

.scroll-table {
	display: flex;
	overflow: auto;
	white-space: nowrap;
	max-height: 650px;
	width: 1000px;
	margin: 10px auto 0px auto;
}
.short{
	width:70px;
}
.middle{
	width:164px;
}
.long{
	width:350px;
}
#tblAuthorityItemSet th{
    padding:10px 0 10px 0;/*TRBL*/
    background-color:#1a0d00;/*#002b80*/
    color:white;
    border:1px solid;
    text-align:center;
}
#tblAuthorityItemSet td ,#tbCheckBox td{
    padding-left:10px;
}
#tblAuthorityItemSet tr:nth-child(even){
    background-color:#d9d9d9;
}
#tblAuthorityItemSet tr:nth-child(odd){
    background-color:#f2f2f2;
}
#tblAuthorityItemSet td:last-child{
	text-align:center;
}
.btn-custom{
    margin-top:30px;
}
.btn-dark{
	background :#343a40;
	color:#fff !important;
}
.has-search .form-control-feedback {
    right: initial;
    left: 0;
    color: #ccc;
}
.has-search .form-control {
    padding-right: 12px;
    padding-left: 34px;
}
.custom-title-style{
	display:inline-block;
	margin-top:20px;
	background:#d9edf7;
	font-size:20px;
	padding:10px;
}
.custom_left_side{
	width:325px;
	min-height:700px;
	height:auto;
	background:whitesmoke;
	border:1px solid whitesmoke;
	margin-right:70px;
	padding:10px;
}

#tblSetList td{
	height:35px;
	padding:5px;
	cursor:pointer;
}
.td-width{
	min-width:200px !important;
	max-width:250px !important;
	word-wrap: break-word;
}
#tblSetList td label{
	cursor:pointer;
}
.list-group-item{
	padding:5px;
	display:table-row;
}
.list-group-item:hover{
	background:whitesmoke;
}
.custom_popup{
	min-height:120px;
	display:block;
	background-color:#ddd;
	width:200px;
	height:auto;
}

#update_popup {
    display: none;
    z-index:10;
    position: absolute;
}
.popup-item{
	text-align:center;
}
.custom_left_side hr{
  background:#fff;
}
.disable_color{
	background:#cccccc;
}
.btn-sm{
	padding-left:4px;
	padding-right:4px;
	<!--font-weight:bold;-->
	color:#1a0d00;
}
</style>
@endsection

@section('content')
@include('layouts.loading')
<div class="main-content">
	<!--<input type="button" onclick="GetBim360ProjectUsers()" class="btn btn-primary" value="BIM360プロジェクトユーザー"/>-->
	
	<input type="hidden" id="hidLoginID" name="hidLoginID" value="{{Session::get('login_user_id')}}"/>
	
	<input type="hidden" id="hidAccessId" name="hidAccessId" value="{{ $set_id}}"/>
	<input type="hidden" id="hidUserId" name="hidUserId" value="{{ $access_user_id}}"/>
	<input type="hidden" id="hidUserName" name="hidUserName" value="{{ $access_user_name}}"/>
	<div style = "display:flex;">
		<div id="left_side" class="custom_left_side">
			<div class="btn-group" role="group">
			　<button type="button" class="btn btn-default btn-sm " id="btn_setting" onclick="GoTo('setting');">詳細権限</button>
		      <button type="button" class="btn btn-default btn-sm" id="btn_authoritySet" onclick="GoTo('authoritySet');">物件セット</button>
		      <button type="button" class="btn btn-default btn-sm disable_color" id="btn_authorityItemSet" onclick="GoTo('authorityItemSet');">項目別セット</button>
		      <button type="button" class="btn btn-default btn-sm" id="btn_authorityItemSet" onclick="GoTo('modelDataSet');">モデルセット</button>
		    </div><hr>
			<h4 style="font-weight:bold;">項目別セット一覧</h4>
			<hr style="margin:3px;">
			<table id="tblSetList" class="list-group" width="100%">
				<tbody>
					<tr class="list-group-item">
						<td colspan="3"><img src="/iPD/public/image/plus.png" alt="dropdown" align="dropdown" style="width:16px;" onclick="AddAccessSet()"></td>
					</tr>
				</tbody>
			</table>
		
		</div>
		<div style="">
			<div class="centering" style="display:flex;">
			<div style="flex-grow:21;">
				<h3 class="allstoreManagementHeader page-title custom-title-style"><span id="set_name" style="color:darkblue"></span>項目別アクセス設定</h3>
			</div>
			
		</div>
	
			<div class="centering" style="display:flex;margin-top:10px;">
		        <div class="form-group has-feedback has-search" style="margin-bottom:0px;margin-left:-5px;">
		            <span class="glyphicon glyphicon-search form-control-feedback"></span>
		            <input type="text" class="form-control" id="txtSearch" placeholder="項目別名" style="width:900px;">
		        </div>
		        <div><input type="button" class="btn btn-primary btn-md" id="btnSave" style="margin-left:10px;" value="確定" onClick="SaveAccessSetDetail({{ $set_id}})"/></div>
		    </div>
		    <hr style="margin:7px;">
		    <div style="display:flex;">
				<div>
					<label>アクセス可能項目数 :　<span id="total"></span></label>
				</div>
				<div style="margin-left:550px;">
				   <input type="button" class="btn btn-dark btn-sm " value="全項目オン" onClick="CheckAll()"/>
				   <input type="button" class="btn btn-dark btn-sm " value="全項目オフ" onClick="UnCheckAll()"/>
				</div>
			</div>
			<div class="scroll-table" align="center">
				<table id="tblAuthorityItemSet" width="100%" align="center">
					<thead>
						<tr>
							<th>全店物件項目名</th>
							<th>アクセス許可</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
				<!--<<table id="tbCheckBox" class="sticky-right" width="5%"></table>-->
				
			</div>
		
		</div>
	</div>

</div>

<!--Name update popup-->
<div id ="update_popup" class="custom_popup">
	<div style="display:flex;">
		<h5 class="popup-item" style="font-style:bold;color:darkblue;margin-left:10px;">セット名編集</h5>
		<div style="margin:5px 0 0 80px;"><a class="close" href="javascript:void(0);" onclick="ClosePopUp()" style="top:0px;">×</a><br></div>
	</div>
	
	<hr style="margin:3px;">
	<div class="popup-item"><input type="text" class="form-control" id="updateName" name="" /></div>
	<hr style="margin:3px;">
	<div class="popup-item" style="margin-top:10px;margin-bottom:10px;">
		<input type="button" class="btn btn-primary btn-sm" value="編集" onClick="UpdateSetName()"/>
		<input type="button" class="btn btn-dark btn-sm" value="キャンセル" onClick="ClosePopUp()"/>
	</div>
	
</div>


@endsection