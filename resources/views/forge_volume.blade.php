@extends('layouts.baselayout')

@section('head')
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/select2/select2.min.js"></script>
<script type="text/javascript" src="../public/js/forge.js"></script>
<link rel="stylesheet" href="../public/css/select2.min.css">
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<style>
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

.spacing{margin-bottom:5px;}
</style>
@endsection

@section('content')
<div class="main-content">

    <h3>Forge Volume By Material</h3>
    <div class="row">  
        <div class="col-xs-4">
            <div class="spacing">
                <select id="project"  multiple="multiple" style="width:100%">
                </select>
                
            </div>
            <!-- -->
            <div class="spacing">
                <select id="item" multiple="multiple" style="width:100%"> 
                </select>
            </div>
            
            <div class="spacing">
                <select id="version" multiple="multiple" style="width:100%">
                </select>
            </div>
            
            <div class="spacing">
                <select id="category" multiple="multiple" style="width:100%">
                <option value="column">構造柱</option>
                <option value="beam">梁</option>
                <option value="floor">床</option>
                <option value="wall">壁</option>
                <option value="foundation">構造基礎</option>
                </select>
            </div>
            
            <div class="spacing">
                <select id="material" multiple="multiple" style="width:100%">
                </select>
            </div>
            
            <div class="spacing">
               <select id="workset" multiple="multiple" style="width:100%">
                </select> 
            </div>
        </div>
        <div class="col-xs-1">
            <input type="button" class="btn btn-primary" name="btnMaterialChartData" id="btnMaterialChartData" value="ShowData" onClick="DisplayVolumeData()"/>
        </div>
        
    </div>
    <br>
    <table id="tblVersionData">
    </table>
    
   
</div>
@endsection