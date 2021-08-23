@extends('layouts.baselayout')
@section('title', 'CCC - Process Mapping info')

@section('head')
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>
<script type="text/javascript" src="../public/js/processMapping.js"></script>
<script src="../public/js/shim.js"></script>
<script src="../public/js/xlsx.full.min.js"></script>
<script>
</script>
<style>

ul{
	float:left;
}
.main-content{
	/*background-color: #f2f3f3;*/
	margin: 0 0 0 0;
	width:100%;
}
thead, tbody {
	display: block;
}

tbody {
	overflow-x: hidden;
	overflow-y: scroll;
	height: 690px;
	width: 1189px;
}
.main_content_header {
	display:flex;
	width: 1189px;
	margin:0 auto;
}
.short {
	width: 58.1px;
	text-align: center;
}
.short2 {
	width: 113px;
	text-align: center;
}
.middle {
	width: 164.1px;
}
.clear-decoration-btn{
    border: none;
    outline: none;
    background: transparent;
}
#tbUser tbody tr:hover {
	background : wheat;
}
</style>
@endsection

@section('content')
@include('layouts.loading')
<div class="main-content">
	<div>
		<input type="hidden" id="hidLoginID" name="hidLoginID" value="{{Session::get('login_user_id')}}"/>
		
		<div class="main_content_header">
			<div style="flex-grow:20;">
				<h2 class="allstoreManagementHeader" style="display:inline-block;">工程ID - SBSルール 対応一覧表</h2>
			</div>
			<div style="flex-grow:1;margin-top:17px;">
				<form id ="allstoreForm" name ="allstoreForm" method="POST">
				    <div style="padding: 1% 0% 0% 0%;float:left;display:flex;">
				        <input type="file" id="input-excel" />
				        <input type = "button" class="btn btn-primary" name = "update" id ="update" value = "更新" onclick ="saveProcessMappingInfo()"/>
				        &nbsp;&nbsp;&nbsp;
				        <input type = "button" class="btn btn-primary" name = "export" id ="export" value = "Export" onclick ="exportProcessMappingInfo()"/>
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
	    
		<!--<div id="DivTab">-->
		<!--	<ul class="nav nav-tabs" id="tab_header" style="width:100%;">-->
		<!--	</ul>-->
		<!--	<div class="tab-content" id="tab_body">-->
		<!--	</div>-->
		<!--</div>-->
		<!--<table id="tbUser" width="100%" align="center"></table>-->
		<table id="tbUser" align="center"></table>
	</div>
</div>
@endsection