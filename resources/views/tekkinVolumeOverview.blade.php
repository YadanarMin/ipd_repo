@extends('layouts.baselayout')

@section('head')
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>
<script type="text/javascript" src="../public/js/select2/select2.min.js"></script>
<script type="text/javascript" src="../public/js/select2/i18n/ja.js"></script>
<script type="text/javascript" src="../public/js/tekkinVolumeOverview.js"></script>
<script type="text/javascript" src="../public/js/xlsx.full.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.3/FileSaver.min.js"></script>
<link rel="stylesheet" href="../public/css/jquery.multiselect.css">
<link rel="stylesheet" href="../public/css/select2.min.css">
<link rel="stylesheet" href="../public/css/projectSearchConsole.css">
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
    z-index: -10;
}
#tblVersionData  td{
    padding-left:20px;
}
#inputTypeName {
    border-radius: 5px;
    width: 150px;
    height: 33px;
    border: solid #aaa 1px;
}
input, textarea {
  font-size: 13px;
}
</style>
@endsection

@section('content')
<div class="main-content">

    <h3>Tekkin Volume Overview</h3>
    <!--<div style="display:flex;flex-wrap:wrap;margin-bottom:10px;">-->
    <div style="margin-bottom:5px;">
        <select id="project" multiple="multiple">
        </select>&nbsp;&nbsp;&nbsp;
    </div>
    <div style="margin-bottom:5px;">
        <select id="item" multiple="multiple">
        </select>&nbsp;&nbsp;&nbsp;
    </div>
    <div style="margin-bottom:10px;">
        <select id="version" multiple="multiple">
        </select>&nbsp;&nbsp;&nbsp;
    </div>
    <div style="display:flex;flex-wrap:wrap;margin-bottom:10px;">
        <select id="category" multiple="multiple">
            <option value="column">構造柱</option>
            <option value="beam">梁</option>
            <option value="floor">床</option>
            <option value="wall">壁</option>
            <option value="foundation">構造基礎</option>
        </select>&nbsp;&nbsp;&nbsp;
        <input type="button" class="btn btn-primary" name="btnSearchConsoleData" id="btnSearchConsoleData" value="ShowData" onClick="ReportTekkinVolumeOvewview()"/>
        &nbsp;&nbsp;&nbsp;
        <input type="button" class="btn btn-primary" name="btnSearchConsoleDownloadData" id="btnSearchConsoleDownloadData" value="Download" data-ajax="false" onClick=""/>
    </div>
    <br>
    <div id="tblTekkinData">
    </div>

</div>
@endsection