@extends('layouts.baselayout')
@section('title', 'CCC - All store accessable setting')

@section('head')
<script type="text/javascript" src="/iPD/public/js/jquery-ui.min.js"></script>
<!--<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>-->
<script type="text/javascript" src="/iPD/public/js/projectAccessSetting.js"></script>
<!--<script src="../public/js/shim.js"></script>-->
<!--<script src="../public/js/xlsx.full.min.js"></script>-->
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
<script>
</script>
<style>

ul{
	float:left;
}
.main-content{
	/*background-color: #f2f3f3;*/
	margin: 0 0% 0% 0%;
	width:100%;
}
.centering{
	width: 1120px;
	margin: 0 auto;
}

.scroll-table {
	display: flex;
	overflow: auto;
	white-space: nowrap;
	max-height:350px;
	width: 1120px;
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

.chk_th{
    padding:0px !important;
	height:20px !important;
	background:#e5ecff !important;
}
.btn-info{
 background:#e5ecff !important;
 border :1px solid #e5ecff;
 color:darkblue !important;
}

#tbUser thead th{
  /* #5bc0de 縦スクロール時に固定する */
  /*position: -webkit-sticky;
  position: sticky;
  top: 0;*/
  /* tbody内のセルより手前に表示する */
  /*z-index: 0;*/
}
#tbUser thead tr th:first-child{
	<!--position:sticky;-->
	<!--top:0;-->
	<!--z-index:50;-->
}
#tbUser tbody tr td:first-child{
  <!--top: auto;-->
  <!--left: 0px;-->
  <!--position:sticky;-->
  <!--background:lightgray;-->
  /*position: absolute;
  display:inline-block;
  width: 6em;*/
}

.sticky-right {
  background-color: lightgray;
  opacity: 0.9;
  text-align: center;
  right: 0;
  height:100%;
  position: sticky;
}

.sticky-right th{
	z-index:500px;
	position:sticky;
}

td{
	height:25px;
}

.word_btn {
	display: block;
	position: relative;
	width: 80px;
	<!--padding: 0.8em;-->
	text-align: center;
	text-decoration: none;
	color: #818181;
}
.word_btn:hover {
	 color: #1B1B1B;
	 cursor: pointer;
	 text-decoration: none;
}
.sortOn{
  /*color:#ff0000;*/
  color:deepskyblue;
}

/*****dropdown start*****/
.dropdown {
  width: 380px;
  display: inline-block;
  background-color: #fff;
  border-radius: 2px;
  box-shadow: 0 0 2px rgb(204, 204, 204);
  transition: all .5s ease;
  position: relative;
  font-size: 14px;
  color: #474747;
  height: 100%;
  text-align: left
}
.dropdown .select {
    cursor: pointer;
    display: block;
    padding: 10px
}
.dropdown .select > i {
    font-size: 13px;
    color: #888;
    cursor: pointer;
    transition: all .3s ease-in-out;
    float: right;
    line-height: 20px
}
.dropdown:hover {
    box-shadow: 0 0 4px rgb(204, 204, 204)
}
.dropdown:active {
    background-color: #f8f8f8
}
.dropdown.active:hover,
.dropdown.active {
    box-shadow: 0 0 4px rgb(204, 204, 204);
    border-radius: 2px 2px 0 0;
    background-color: #f8f8f8
}
.dropdown.active .select > i {
    transform: rotate(-90deg)
}
.dropdown .dropdown-menu {
    position: absolute;
    background-color: #fff;
    width: 100%;
    left: 0;
    margin-top: 1px;
    box-shadow: 0 1px 2px rgb(204, 204, 204);
    border-radius: 0 1px 2px 2px;
    overflow: hidden;
    display: none;
    height: 432px;
    overflow-y: auto;
    z-index: 9
}
.dropdown .dropdown-menu li {
    padding: 10px;
    transition: all .2s ease-in-out;
    cursor: pointer
} 
.dropdown .dropdown-menu {
    padding: 0;
    list-style: none
}
.dropdown .dropdown-menu li:hover {
    background-color: #f2f2f2
}
.dropdown .dropdown-menu li:active {
    background-color: #e2e2e2
}
/*****dropdown end*****/

.btn-custom{
    margin-top:30px;
}
.btn-dark{
	background :#343a40;
	color:#fff !important;
}
.access-set-div{
	margin:5% 0 2% 1%;
	min-width:260px !important;
	max-width:260px !important;
}
.childDiv{
	border:1px solid #ccc;
}
.custom-header{
	background:#343a40;
	border:1px solid #ccc;
	color:#fff;
}
.custom-header > label{
	display: block;
	text-align: center;
	<!--color:red;-->
}
.custom-body{
	background:whitesmoke;<!--#d9edf7-->
	padding:5px;
	min-height:50px;
	text-align:center;
	font-weight:bold;
	font-size:15px;
	padding-top:15px;
}

#authority_set_content {
    display: none;
    z-index:10;
    position: absolute;
}

#right_click_content {
    display: none;
    z-index:10;
    position: absolute;
    background-color:#eee;
    border: 1px solid #ddd;
    border-radius:5px;
}
#tblAuthoritySet td{
	border:1px solid #d9edf7;
	width:100%;
	padding:3px;
	margin:3px;
	cursor:pointer;
}
#tblAuthoritySet input[type="text"]{
	cursor:pointer !important;
	width:100% !important;
}

#tblAuthoritySet input[type="text"]:hover{
	font-weight:bold;
	color:red;
}
.select-set{
	background:#343a40 ;
	border:1px solid;
	color:#fff;
	cursor:pointer;
}
ul li{
	list-style:none;
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
.disable-color > input[type="text"]{
	background:#f5f5f5;
	border-color:#f5f5f5;
	border:1px solid #f5f5f5;
	//color: rgb(169, 169, 169);
}
.custom-title-style{
	display:inline-block;
	margin-top:20px;
	//background:#f5dd67;
	background:#d9edf7;
	font-size:20px;
	padding:10px;
}
.custom_left_side{
	min-width:325px;
	min-height:700px;
	height:auto;
	background:whitesmoke;
	border:1px solid whitesmoke;
	margin-right:30px;
	padding:10px;
}

#tblSetList td label{
	cursor:pointer;
	padding:5px;
}
.list-group-item{
	padding:15px;
	display:table-row;
}

.list-group-item:hover{
	background:whitesmoke;
}
.btn-secondary{
	background:#6c757d;
	color:#fff !important;
	font-weight:bold;
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
#tblModelData th{
	background:#6c757d;
	height:35px;
	padding:5px;
	color:#fff;
	border:1px solid #fff;
	text-align:center;
}
#tblModelData td{
	border:1px solid #fff;
	padding:5px;
}
#tblModelData tr:nth-child(even){
    background-color:#F8FCFC;
}
#tblModelData tr:nth-child(odd){
    background-color:#f2f2f2;
}
#tblModelData td:nth(2)-child{
	text-align:center;
}
.model-color{
	background:#6c757d;
	border:1px solid whitesmoke;
	font-weight:bold;
}
</style>
@endsection

@section('content')
@include('layouts.loading')
<div class="main-content">
	<!--<input type="button" onclick="GetBim360ProjectUsers()" class="btn btn-primary" value="BIM360プロジェクトユーザー"/>-->
	
	<input type="hidden" id="hidLoginID" name="hidLoginID" value="{{Session::get('login_user_id')}}"/>
	
	<input type="hidden" id="hidUserId" name="hidUserId" value="{{ $access_user_id}}"/>
	<input type="hidden" id="hidUserName" name="hidUserName" value="{{ $access_user_name}}"/>
	<div style = "display:flex;">
		<div id="left_side" class="custom_left_side">
			<!--<a href="{{ url('projectAccessSetting/index') }}" style ="position:relative;margin-top:10px;margin-left:10px;">閲覧権限設定に戻る</a>-->
			<!--<hr>-->
			<div class="btn-group" role="group">
			　<button type="button" class="btn btn-default btn-sm disable_color" id="btn_setting" onclick="GoTo('setting');">詳細権限</button>
		      <button type="button" class="btn btn-default btn-sm " id="btn_authoritySet" onclick="GoTo('authoritySet');">物件セット</button>
		      <button type="button" class="btn btn-default btn-sm" id="btn_authorityItemSet" onclick="GoTo('authorityItemSet');">項目別セット</button>
		      <button type="button" class="btn btn-default btn-sm" id="btn_authorityItemSet" onclick="GoTo('modelDataSet');">モデルセット</button>
		    </div><hr>

			<div class="childDiv">
				<div class="custom-header"><label>物件情報<br>アクセス権限セット</label></div>
				<div class="custom-body" id="allstore_info_link"></div>
				<div style="height:20px" class="select-set" id="allstore_info">物件情報セット選択</div>
			</div><hr> 
			<div class="childDiv">
				<div class="custom-header"><label>項目別<br>アクセス権限セット</label></div>
				<div class="custom-body" id="allstore_item_link"></div>
				<div style="height:20px" class="select-set" id="allstore_item">項目別セット選択</div>
			</div><hr>
			<div class="childDiv">
				<div class="custom-header model-color"><label>モデルデータ<br>アクセス権限セット</label></div>
				<div class="custom-body" id="model_data_link"></div>
				<div style="height:20px" class="select-set  model-color" id="model_data">モデルデータセット選択</div>
			</div><hr>
		</div>
		
		<div style="">
			<div class="centering" style="display:flex;margin-bottom:10px;">
				<div style="flex-grow:21;">
					<h3 class="allstoreManagementHeader page-title custom-title-style"><span style="color:darkblue">{{ $access_user_name }}さん</span>の物件情報アクセス権限設定</h3>
				</div>
			</div>
			<div class="centering" style="display:flex;">
		        <div class="form-group has-feedback has-search" style="margin-right:10px;width:250px;">
		            <span class="glyphicon glyphicon-search form-control-feedback"></span>
		            <input type="text" class="form-control" id="txtiPDCodeSearch" placeholder="iPDコード">
		        </div>
		        <div class="form-group has-feedback has-search" style="margin-right:10px;width:250px;">
		            <span class="glyphicon glyphicon-search form-control-feedback"></span>
		            <input type="text" class="form-control" id="txtProjectNameSearch" placeholder="プロジェクト名称" >
		        </div>
		        <div class="form-group has-feedback has-search" style="margin-right:10px;width:250px;">
		            <span class="glyphicon glyphicon-search form-control-feedback"></span>
		            <input type="text" class="form-control" id="txtBranchSearch" placeholder="支店名" >
		        </div>
		        <div class="form-group has-feedback has-search" style="margin-right:10px;width:250px;">
		            <span class="glyphicon glyphicon-search form-control-feedback"></span>
		            <input type="text" class="form-control" id="txtKoujiTypeSearch" placeholder="工事区分名" >
		        </div>
		        <div class="form-group has-feedback has-search" style="margin-right:10px;width:250px;">
		            <span class="glyphicon glyphicon-search form-control-feedback"></span>
		            <input type="text" class="form-control" id="txtiPDinChargeSearch" placeholder="iPDセンター担当名">
		        </div>
		        <div>
		        	<input type="button" onClick=SaveUserAccessableInfo() value="確定" class="btn btn-primary btn-md"/>
		        </div>
		    </div>
		    <hr style="margin:7px;">
		    <div style="display:flex;">
		    	<div>
		    		<label style="margin-right:50px;">アクセス可能物件数　:　<span id="total_allstore"></span></label>
		    		<label>アクセス可能項目数　:　<span id="total_item"></span></label>
		    	</div>
		    	<div style="margin:0 0 0 350px;">
					<input type="button" onClick=AllProjectCheck() value="全店物件オン" class="btn btn-dark btn-sm"/>
		        	<input type="button" onClick=AllProjectUncheck() value="全店物件オフ" class="btn btn-dark btn-sm"/>
		        	<input type="button" onClick=AllItemCheck() value="全項目オン" class="btn btn-info btn-sm"/>
		        	<input type="button" onClick=AllItemUncheck() value="全項目オフ" class="btn btn-info btn-sm"/>
				</div>
		    </div>
			<div class="scroll-table" align="center">
				<table id="tbUser" width="400%" align="center"></table>
				<table id="tbCheckBox" class="sticky-right" width="5%"></table>
				
			</div>
			<hr>
			<div class="centering" style="display:flex;margin-bottom:10px;">
				<div style="flex-grow:21;">
					<h3 class="allstoreManagementHeader page-title custom-title-style"><span style="color:darkblue">{{ $access_user_name }}さん</span>のモデルデータアクセス権限設定</h3>
				</div>
			</div>
			
			<div style="display:flex;">
		    	<div>
		    		<label style="margin-right:50px;">アクセス可能モデル数　:　<span id="total_model"></span></label>
		    	</div>
		    	<div style="margin:0 0 10px 250px;">
		        	<input type="button" onClick=AllModelCheck() value="全モデルオン" class="btn btn-secondary btn-sm"/>
		        	<input type="button" onClick=AllModelUncheck() value="全モデルオフ" class="btn btn-secondary btn-sm"/>
		        	<!--<input type="button" onClick=SaveModelAccessableInfo() value="モデル確定" class="btn btn-primary btn-md"/>-->
				</div>
		    </div>
			<div class="" style="width:700px;" >
				<table id="tblModelData" width="100%" align="">
					<thead></thead>
					<tbody></tbody>
				</table>
				
			</div>
		
		</div>
	</div>

</div>

	<!--append table-->
	<div id ="authority_set_content" class="access-set-div">
		<table id="tblSetList" class="list-group" width="100%">
			<tbody></tbody>
		</table>
	</div>
	
@endsection