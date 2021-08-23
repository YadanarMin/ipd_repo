@extends('layouts.baselayout')
@section('title', '見積物件設定')

<!--CSS and JS file-->
@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script type="text/javascript" src="/iPD/public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="/iPD/public/js/jquery.multiselect.js"></script>
<script type="text/javascript" src="/iPD/public/js/estimateSetting.js"></script>


<style>
.main-content{
	margin: 0 auto;
	width:90%;
	display : center;
} 
.projectListView{
    display: flex;
    overflow: auto;
    white-space: nowrap;
    max-height: 650px;
    width: 100%;
    justify-content: space-between;
    
}
#tbProjectList{ 
    width : 90%;
    height :100%;
    position: sticky;
}
#tblCheckbox{
    width : 10%;
    height :100%;
    
}
#tbProjectList th, #tblCheckbox th{
    padding: 10px 0 10px 0;
    background-color: #1a0d00;
    color: white;
    border: 1px solid;
    text-align: center;
}
.sticky-right{
    background-color: lightgray;
    opacity: 0.9;
    text-align: center;
    right: 0;
    height: 100%;
    position: sticky;
}
thead th { position: sticky; top: 0; }


</style>

@endsection
@section('content')
@include('layouts.loading')
<div class="main-content">
    <input type="hidden" id="hidLoginID" name="hidLoginID" value="{{Session::get('login_user_id')}}"/>
    
    <h3>見積物件設定</h3>
    <hr>
    <div  style="display:flex;margin-top:10px;">
	   <div class="form-group has-feedback has-search" style="margin-right:10px;width:260px;">
	            <span class="glyphicon glyphicon-search form-control-feedback"></span>
	            <input type="text" class="form-control" id="txtiPDCodeSearch" placeholder="iPDコード">
	        </div>
	   <div class="form-group has-feedback has-search" style="margin-right:10px;width:260px;">
	            <span class="glyphicon glyphicon-search form-control-feedback"></span>
	            <input type="text" class="form-control" id="txtProjectNameSearch" placeholder="プロジェクト名称">
	        </div>
	   <div class="form-group has-feedback has-search" style="margin-right:10px;width:260px;">
	            <span class="glyphicon glyphicon-search form-control-feedback"></span>
	            <input type="text" class="form-control" id="txtBranchSearch" placeholder="支店名">
	        </div>
	   <div class="form-group has-feedback has-search" style="margin-right:10px;width:260px;">
	            <span class="glyphicon glyphicon-search form-control-feedback"></span>
	            <input type="text" class="form-control" id="txtKoujiTypeSearch" placeholder="工事区分名">
	        </div>
	   <div class="form-group has-feedback has-search" style="margin-right:10px;width:260px;">
	            <span class="glyphicon glyphicon-search form-control-feedback"></span>
	            <input type="text" class="form-control" id="txtiPDinChargeSearch" placeholder="iPDセンター担当名">
	        </div>
	 </div>
	 <div class="projectListView">
	     <table  id="tbProjectList">
	         <thead>
	             <tr>
	                 <th width="5%">iPDコード</th>
	                 <th width="40%">プロジェクト名称</th>
	                 <th width="5%">支店名</th>
	                 <th width="10%">工事区分名</th>
	                 <th width="10%">iPDセンター担当名</th>
	             </tr>
	         </thead>
	         <tbody id="searchableProjectList">
	             
	         </tbody>
	     </table>
	     <table id="tblCheckbox" class="sticky-right" >
	         <thead>
	             <tr>
	                 <th>見積設定</th>
	             </tr>
	         </thead>
	         <tbody id="searchableCheckBoxList">
	             
	         </tbody>
	         
	     </table>
	 </div>
	
	  
    
    
</div>
<script>
    $(document).ready(function(){
        var login_user_id = $("#hidLoginID").val();
        var img_src = "../public/image/JPG/ローディング中のアイコン1.jpeg";
        var url = "estimateSetting/index";
        var content_name = "見積物件設定";
        recordAccessHistory(login_user_id,img_src,url,content_name);
    });
</script>
@endsection