@extends('layouts.baselayout')
@section('title', 'CCC - Storage search')

@section('head')
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<!--<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>-->
<script type="text/javascript" src="../public/js/select2/select2.min.js"></script>
<script type="text/javascript" src="../public/js/select2/i18n/ja.js"></script>
<script type="text/javascript" src="../public/js/forge.js"></script>
<!--<link rel="stylesheet" href="../public/css/jquery.multiselect.css">-->
<link rel="stylesheet" href="../public/css/select2.min.css">
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<style>
#chartDiv{
    height:70vh;
    margin-bottom: 9vh;
    width:100%;
}
.scroll{
     height: 500px;
    overflow-y: scroll;
}
.select2-container .select2-selection {
   
} 

</style>
<script>
    $(document).ready(function(){
        var login_user_id = $("#hidLoginID").val();
        var img_src = "../public/image/JPG/原子力のフリーイラスト3.jpeg";
        var url = "forge/index";
        var content_name = "ﾓﾃﾞﾙｽﾄﾚｰｼﾞ";
        recordAccessHistory(login_user_id,img_src,url,content_name);
    });
</script>
@endsection

@section('content')
@include('layouts.loading')
<div class="main-content">
    
    <input type="hidden" id="hidLoginID" name="hidLoginID" value="{{Session::get('login_user_id')}}"/>

    <h4 class="page-title">モデルストレージ</h4>
    <div class="row">
       <div class="col-xs-5">
         <div style="margin-bottom:5px;">
            <select id="project" multiple="multiple" style="width:80%">
            </select>&nbsp;&nbsp;&nbsp;<input type="button" class="btn btn-primary" name="btnChartDisplay" id="btnChartDisplay" value="チャート"onClick="DisplayVolumeChart()"/>
        </div>
        <div style="margin-bottom:5px;">
            <select id="item" multiple="multiple" style="width:80%">
            </select>&nbsp;&nbsp;<input type="checkbox" name="chkAllItemCheck" id="chkAllItemCheck">Select All 
        </div>
        <div style="margin-bottom:10px;">
            <select id="version" multiple="multiple" style="width:80%">
            </select>&nbsp;&nbsp;<input type="checkbox" name="chkAllCheck" id="chkAllCheck">Select All &nbsp;&nbsp;&nbsp;
             
        </div>
       </div>
       <div class="col-xs-7">
        <div id="chartDiv"> </div>
       </div>
    </div>
    
    <div style="display:flex;">  
    <!--<div style="margin-bottom:5px;">
        <select id="project" multiple="multiple" style="width:90%">
        </select>&nbsp;&nbsp;&nbsp;
    </div>
    <div style="margin-bottom:5px;">
        <select id="item" multiple="multiple" style="width:90%">
        </select>&nbsp;&nbsp;&nbsp;
    </div>
    <div style="margin-bottom:10px;">
        <select id="version" multiple="multiple" style="width:90%">
        </select>&nbsp;&nbsp;&nbsp;
    </div>
        <select id="project"  multiple>
        </select>&nbsp;&nbsp;&nbsp;

        <select id="item" multiple > 
        </select>&nbsp;&nbsp;&nbsp;

        <select id="version" multiple >
        </select>&nbsp;&nbsp;&nbsp;
        <input type="button" name="btnChartDisplay" id="btnChartDisplay" value="チャート"onClick="DisplayVolumeChart()"/>&nbsp;&nbsp;&nbsp;-->
    </div>
    
 
</div>
@endsection