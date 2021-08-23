@extends('layouts.baselayout')

@section('head')
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<!--<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>-->
<script type="text/javascript" src="../public/js/roomProp.js"></script>
<!--<link rel="stylesheet" href="../public/css/jquery.multiselect.css">-->
<script type="text/javascript" src="../public/js/select2/select2.min.js"></script>
<script type="text/javascript" src="../public/js/select2/i18n/ja.js"></script>
<link rel="stylesheet" href="../public/css/roomProp.css">
<link rel="stylesheet" href="../public/css/select2.min.css">
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<style>
#roomPieChartDiv{
    margin-bottom:9vh;
}
.comboStyle{
    width:80%;
   
}
select{
     margin-bottom: 10px;
}
</style>
@endsection

@section('content')
@include('layouts.loading')
<div class="roomProp-panel">

    <h3>Forge Room Properties Chart</h3>
    <div class="col-xs-5" >  <!--style="display:flex;"-->
        <div style="margin-bottom:5px;">
            <select id="project"  multiple="multiple" class="comboStyle">
            </select>&nbsp;&nbsp;&nbsp;
            <input type="button" name="btnChartDisplay" id="btnChartDisplay" value="チャート"onClick="GetRoomProperties()"/>
        </div>
        <div style="margin-bottom:5px;">
            <select id="item" multiple="multiple" class="comboStyle"> 
            </select>&nbsp;&nbsp;&nbsp;
        </div>
        <div style="margin-bottom:5px;">
            <select id="version" multiple="multiple" class="comboStyle">
            </select>&nbsp;&nbsp;&nbsp;
        </div>
        
    </div>
    <div id="roomPieChartDiv">
    </div>
    
</div>
@endsection