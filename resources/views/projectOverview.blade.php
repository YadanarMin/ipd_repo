@extends('layouts.baselayout')

@section('head')
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/select2/select2.min.js"></script>
<script type="text/javascript" src="../public/js/select2/i18n/ja.js"></script>
<script type="text/javascript" src="../public/js/projectOverview.js"></script>
<link rel="stylesheet" href="../public/css/select2.min.css">
<link rel="stylesheet" href="../public/css/projectOverview.css">
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<style>
body{
    background-color:rgb(245,246,248);
}
#chartDiv{
    height:60vh;
}

#tblVersionData {
    width: 80%;
    margin-bottom:9vh;
}
#tblVersionData  td{
    padding-left:20px;
}
</style>
@endsection

@section('content')
<div class="main-content">

    <h3>Project Overview</h3>
    <div style="margin-bottom:5px;">
        <select id="project" multiple="multiple" style="width:50%">
        </select>&nbsp;&nbsp;&nbsp;
    </div>
    <div style="margin-bottom:5px;">
        <select id="item" multiple="multiple" style="width:50%">
        </select>&nbsp;&nbsp;&nbsp;
    </div>
    <div style="display:flex;">

        <select id="category" multiple="multiple" style="width:150px;min-width:100px;">
            <option value="column">構造柱</option>
            <option value="beam">梁</option>
            <option value="floor">床</option>
            <option value="wall">壁</option>
            <option value="foundation">構造基礎</option>
        </select>&nbsp;&nbsp;&nbsp;
        
        <input type="button" class="btn btn-primary" name="btnMaterialChartData" id="btnMaterialChartData" value="ShowData" onClick="ReportProjectsOverview()"/>
    </div>
    <br>
    <table id="tblVersionData">
    </table>
    
   
</div>
@endsection