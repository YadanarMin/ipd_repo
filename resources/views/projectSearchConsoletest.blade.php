@extends('layouts.baselayout')

@section('head')

<link rel="stylesheet" href="https://developer.api.autodesk.com/modelderivative/v2/viewers/7.*/style.min.css" type="text/css">
<script src="https://developer.api.autodesk.com/modelderivative/v2/viewers/7.*/viewer3D.min.js"></script>
<script type="text/javascript" src="../public/js/library/toolbarExtension.js"></script>
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/select2/select2.min.js"></script>
<script type="text/javascript" src="../public/js/select2/i18n/ja.js"></script>
<script type="text/javascript" src="../public/js/slick.js"></script>
<!--<script type="text/javascript" src="../public/js/projectSearchConsole.js"></script>-->
<script type="text/javascript" src="../public/js/modelViewer.js"></script>
<script type="text/javascript" src="../public/js/xlsx.full.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.3/FileSaver.min.js"></script>
<script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"></script>
<script src="https://cdn.geolonia.com/community-geocoder.js"></script>
<link rel="stylesheet" href="../public/css/select2.min.css">
<link rel="stylesheet" href="../public/css/slick.css">
<link rel="stylesheet" href="../public/css/slick-theme.css">
<link rel="stylesheet" href="../public/css/projectSearchConsole.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" />
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<style>
.main-content{
    min-height:87.771vh;
    margin:0 0 -1vh 0;
    display: flex;
    /*border:1px solid black;*/
    background-color: #edf0f2;
}
.main{
    width :calc(100vh + 550px);
}
.sidebar{
    width:550px;
    margin:0vh 0 -0.999vh 0;
    border:1px solid red;
    background-color: #edf0f2;
}
.matrix-table{
    margin:1vh 3vh 1vh 1vh;
    /*border:1px solid green;*/
}
.mt-header{
    height: 10vh;
    background-color: white;
}
.mt-body{
    height: 73.77vh;
    background-color: white;
}
.bim-viewer{
    margin:7vh 0 0 0;
    /*border:1px solid blue;*/
}
</style>
@endsection

@section('content')
@include('layouts.loading')
<div class="main-content">
    <div class="sidebar">
        
    </div>
    <div class="main">
        <div class="matrix-table mt-header">
            
        </div>
        <div class="matrix-table mt-body">
            
        </div>
    </div>
    <!--<div class="main bim-viewer">-->
        
    <!--</div>-->
</div>
@endsection