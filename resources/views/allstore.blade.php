@extends('layouts.baselayout')
@section('title', 'CCC - All store building info')

@section('head')
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>
<script type="text/javascript" src="../public/js/allstoreManagement.js"></script>
<script src="../public/js/shim.js"></script>
<script src="../public/js/xlsx.full.min.js"></script>
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
	width: 1189px;
	margin: 0 auto;
}

.scroll-table {
	display: flex;
	overflow: auto;
	white-space: nowrap;
	max-height: 650px;
	width: 1200px;
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
#tbUser th{
	/*background-color: #f5f7f7;*/
}

#tbUser thead th{
    <!--position: sticky;-->
    <!--top: 0;-->
    <!--z-index:49;-->
}
#tbUser thead tr th:first-child,#tbUser thead tr th:nth-child(2){
	position:sticky;
	top:0;
	z-index:50;
}
#tbUser tbody tr td:first-child, #tbUser tbody tr td:nth-child(2){
  top: auto;
  left: 0px;
  position:sticky;
  background:lightgray;
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

/*****dropdownp start*****/
.dropdownp {
  width: 100%;
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
.dropdownp .select {
    cursor: pointer;
    display: block;
    padding: 10px
}
.dropdownp .select > i {
    font-size: 13px;
    color: #888;
    cursor: pointer;
    transition: all .3s ease-in-out;
    float: right;
    line-height: 20px
}
.dropdownp:hover {
    box-shadow: 0 0 4px rgb(204, 204, 204)
}
.dropdownp:active {
    background-color: #f8f8f8
}
.dropdownp.active:hover,
.dropdownp.active {
    box-shadow: 0 0 4px rgb(204, 204, 204);
    border-radius: 2px 2px 0 0;
    background-color: #f8f8f8
}
.dropdownp.active .select > i {
    transform: rotate(-90deg)
}
.dropdownp .dropdownp-menu {
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
.dropdownp .dropdownp-menu li {
    padding: 10px;
    transition: all .2s ease-in-out;
    cursor: pointer
} 
.dropdownp .dropdownp-menu {
    padding: 0;
    list-style: none
}
.dropdownp .dropdownp-menu li:hover {
    background-color: #f2f2f2
}
.dropdownp .dropdownp-menu li:active {
    background-color: #e2e2e2
}
/*****dropdownp end*****/

.has-search .form-control-feedback {
    right: initial;
    left: 0;
    color: #ccc;
}
.has-search .form-control {
    padding-right: 12px;
    padding-left: 34px;
}
div.alert {
    background-color:#FFEFEF;
    <!--margin:0 0 1em 0; padding:10px;-->
    color:#C25338;
    border:1px solid #D4440D;
    line-height:1.5;
    clear:both;
    background-repeat:no-repeat;
    background-position:5px 5px;
    width: 590px;
    margin: 0 auto 10px auto;
    float: right;
}
div.alert span {
    width:48px; height:48px;
    position:relative;
    top:-5px; left:-5px;
    display:block;
    text-indent:-9999px;
    float:left;
}
#reportFlagHistoryPopup{
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.7);
    transition: opacity 500ms;
    visibility: hidden;
    opacity: 0;
    z-index:100;
}
#autoLoadInput{
    margin: 17vh 20vh;
    background: white;
    border-radius: 9px;
    min-height: 600px;
}
#koujiTypeSelect option {
	color: black;
}
#koujiTypeSelect option:first-child {
	color: darkgrey;
}
select#koujiTypeSelect {
	color: darkgrey;
}
#recordNum{
    width: 1193px;
    padding-top: 44px;
    margin: 0 auto;
}
</style>
@endsection

@section('content')
@include('layouts.loading')
<div class="main-content">
	<!--<input type="button" onclick="GetBim360ProjectUsers()" class="btn btn-primary" value="BIM360プロジェクトユーザー"/>-->
	
	<input type="hidden" id="hidLoginID" name="hidLoginID" value="{{Session::get('login_user_id')}}"/>

	<div style="margin: 0 3% 3% 3%;">
		<div class="centering" style="display:flex;">
			<div style="flex-grow:21;">
				<h3 class="allstoreManagementHeader page-title" style="display:inline-block;margin-top:35px;">全店物件情報</h3>
			</div>
			
			<!--<div style="flex-grow:1;margin-top:17px;">-->
			<div style="margin-top:17px;">
				<!--<form id ="allstoreForm" name ="allstoreForm" method="POST" action="{{ url('common/updateAllstore') }}" enctype="multipart/form-data" style="display:flex;margin-left:720px;">-->
				<!--	{{ csrf_field() }}-->
				<!--	<input type="file" id="file" name="file">-->
				<!--	<button type="submit" class="word_btn" style="width:45px;height:28px;">更新</button>-->
				<!--</form>-->
				
				<form id ="allstoreForm" name ="allstoreForm" method="POST">
				    <div style="padding: 1% 0% 0% 0%;float:left;display:flex;">
				        <!--<input type="file" id="input-excel" />-->
				        <div id="update_history" style="margin-top:9px;"></div>&nbsp;
				        <!--<input style="margin-top:8px;background-color:gray;color:lightgray;border:none;" type = "button" class="btn btn-primary" name = "update" id ="update" value = "BOX情報取込" onclick ="wrapSaveAllStore()"/>-->
				        <input style="margin-top:8px;" type = "button" class="btn btn-primary" name = "update" id ="update" value = "BOX情報取込" onclick ="saveAllstoreManagementInfo()"/>
				        <!--<input type = "button" style ="margin-left:10px;" class="btn btn-primary" name = "delete" id ="delete" value = "削除" onclick ="deleteAllstoreManagementInfo()"/>-->
				    </div>
				</form>

				<!--<form id ="allstoreForm" name ="allstoreForm" method="POST">-->
				<!--    <div style="padding: 1% 0% 0% 0%;float:left;display:flex;">-->
				<!--        <input type="file" id="input-excel" disabled />-->
				<!--        <input style="opacity:0.3;" type = "button" class="btn btn-primary" name = "update" id ="update" value = "更新" onclick =""/>-->
				<!--    </div>-->
				<!--</form>-->
			</div>
		</div>
	    
		<!--<div id="DivTab" class="centering">-->
		<!--	<ul class="nav nav-tabs" id="tab_header" style="width:100%;">-->
		<!--	</ul>-->
		<!--	<div class="tab-content" id="tab_body">-->
		<!--	</div>-->
		<!--</div>-->
		<div style="display:flex;width: 1189px;margin: 0 auto;">
			<div style="width: 82px;margin-top: 10px;margin-right: 9px;">
				<input type="button" class="btn btn-primary" name="filter" id="filter" value="Search" onclick="searchStoreInfo()"/>
			</div>
			<div style="display:flex;margin-top:10px;width: 1120px;">
		        <div class="form-group has-feedback has-search" style="margin-right:10px;width:260px;">
		            <span class="glyphicon glyphicon-search form-control-feedback"></span>
		            <input type="text" class="form-control" id="txtiPDCodeSearch" placeholder="iPDコード">
		        </div>
		        <div class="form-group has-feedback has-search" style="margin-right:10px;width:260px;">
		            <span class="glyphicon glyphicon-search form-control-feedback"></span>
		            <input type="text" class="form-control" id="txtKoujiKikanCodeSearch" placeholder="工事基幹コード">
		        </div>
		        <div class="form-group has-feedback has-search" style="margin-right:10px;width:260px;">
		            <span class="glyphicon glyphicon-search form-control-feedback"></span>
		            <input type="text" class="form-control" id="txtProjectNameSearch" placeholder="プロジェクト名称" >
		        </div>
		        <div class="form-group has-feedback has-search" style="margin-right:10px;width:260px;">
		            <span class="glyphicon glyphicon-search form-control-feedback"></span>
		            <input type="text" class="form-control" id="txtBranchSearch" placeholder="支店名" >
		        </div>
		        <div class="form-group has-feedback has-search" style="margin-right:10px;width:260px;">
		            <span class="glyphicon glyphicon-search form-control-feedback"></span>
                	<select class="form-control" id="koujiTypeSelect">
                		<option value="" selected>工事区分名</option>
            		    <option value="1">国内建築</option>
            		    <option value="2">国内土木</option>
            		    <option value="3">海外建築</option>
            		    <option value="4">海外土木</option>
                	</select>
                </div>

		        <div class="form-group has-feedback has-search" style="margin-right:10px;width:260px;">
		            <span class="glyphicon glyphicon-search form-control-feedback"></span>
		            <input type="text" class="form-control" id="txtiPDinChargeSearch" placeholder="iPDセンター担当名">
		        </div>
		        
		    </div>
	    </div>
	    
	    <div style="width: 1189px;margin:0 auto;">
			<div class="alert warning" style="display:flex;">
			    <img src='../public/image/warning_48.png' style="margin:3px 0 0 0;" alt='' height='35' width='35' />
			    <div>
				    案件報告設定のチェックボックスを外すと、他の人の案件報告にも影響があるため、</br>
				    すでにあるチェックは変更しないようにお願いします。
				</div>
			</div>
		</div>
	    
        <div id="recordNum"></div>
		<div class="scroll-table" align="center">
			<table id="tbUser" width="400%" align="center"></table>
			<table id="tbCheckBox" class="sticky-right" width="5%"></table>
			
		</div>
		
		<div style="text-align:center;margin-top:10px;">
		    <botton type="button" id="readPartOfAllstore" onclick="getAllstoreManagementInfo()" class="btn btn-default" style="margin:0 auto;border: none;">
		        <img src='../public/image/down_arrow_blue.png' style="" alt='' height='18' width='18' />
		        <span>もっと読み込む</span>
		    </botton>
		    <botton type="button" id="readAllAllstore" onclick="readAllAllstoreInfo()" class="btn btn-default" style="margin:0 auto;border: none;">
		        <img src='../public/image/down_arrow_blue.png' style="" alt='' height='18' width='18' />
		        <span>すべて読み込む</span>
		    </botton>
		</div>
	
		
	</div>

</div>

<!-- popup -->
<div id="reportFlagHistoryPopup">
    <div id="autoLoadInput">
        
        <div><a class="close" href="javascript:void(0);" onClick ="ClosePopup()" style="top:0px;padding-top:3vh;padding-right:3vh;">&times;</a><br></div>
    	<div class="align-center" style="margin-left: 2vh;text-align: center;">
    		<h4>案件報告設定 変更履歴</h4>
    		<span class="err_msg" id="auto_load_err_message"></span>
    	</div>
    	<hr style="margin-top:10px;margin-bottom:10px;">

        <div id="report_flag_history" style="margin:9px 15px 0 15px;"></div>
        
    </div>
</div>


@endsection