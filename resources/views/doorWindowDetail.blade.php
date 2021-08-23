@extends('layouts.baselayout')
@section('title', 'CCC - Door,Window info search')

@section('head')
<link rel="stylesheet" href="https://developer.api.autodesk.com/modelderivative/v2/viewers/7.*/style.min.css" type="text/css">
<script src="https://developer.api.autodesk.com/modelderivative/v2/viewers/7.*/viewer3D.min.js"></script>
<script type="text/javascript" src="../public/js/library/toolbarExtension.js"></script>
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/select2/select2.min.js"></script>
<script type="text/javascript" src="../public/js/select2/i18n/ja.js"></script>
<script type="text/javascript" src="../public/js/slick.js"></script>
<script type="text/javascript" src="../public/js/doorWindowDetail.js"></script>
<script type="text/javascript" src="../public/js/xlsx.full.min.js"></script>
<script type="text/javascript" src="../public/js/modelViewer.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.3/FileSaver.min.js"></script>

<link rel="stylesheet" href="../public/css/select2.min.css">
<link rel="stylesheet" href="../public/css/slick.css">
<link rel="stylesheet" href="../public/css/slick-theme.css">
<link rel="stylesheet" href="../public/css/roomInfoSearchConsole.css">
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<style>
.col-6{
    /*border:1px solid red;*/
    width:50%;
    height:65vh;
    position:relative;
    height:auto;
}

#modelViewer{
width:100%;
z-index:0;
height:65vh;

}
#data ul{
margin-right:2%;
}
#data li{
   list-style:none;
   padding: 5px;
   margin-bottom:10px;
   background-color: #bfbfbf;/*#1a1a1a*/
   color: #0000b3;
   width:97%;
}
.icon{
 float:right;
}
thead th,tbody td{
height:25px;
}

table{
min-width:100%;
width:100%;
margin-top:0px;
display:block;

}
tbody{
    height:200px;
    overflow-y:hidden;
    width:850px;
    overflow-x:scroll;
    scrollbar-height: 5px;
    max-height:200px;
}
#tblOverall tbody{
 overflow-y:auto;
}

tbody::-webkit-scrollbar {
height:7px;
}
tbody::-webkit-scrollbar-track {
  background: #f1f1f1; 
}
/* Handle */
tbody::-webkit-scrollbar-thumb {
  background: #888; 
}

/* Handle on hover */
tbody::-webkit-scrollbar-thumb:hover {
  background: #555; 
}

thead{
width:850px;
    overflow-x:hidden;
}
thead,tbody{
    display:block;
    position:relative;
}
#tblVersionData th{
padding :0px;
background-color: #f2f2f2;
color:#000000;
border:none;
font-size:12px;
}
.col1{
width:150px;
}
.col2{
width:200px;
}
.col3{
width:250px;
}
.col1,.col2,.col3{
min-width:100px;
}
thead tr{
/*display:block;*/
}
tbody tr:nth-child(even) {/*background: #e6e6e6*/}
tbody tr:nth-child(odd) {/*background: #fff*/}
table tr.active {
background: #99bbff;
/*color:#ffff;*/
}
#tblOverall tr:hover{
background: #e6e6e6;
cursor:pointer;
}
.table-body
{
    position: relative;
    max-height: 550px;
    overflow: auto;
}
</style>
@endsection

@section('content')
@include('layouts.loading')
<div class="main-content">

    <div class="dropdown">
        <div class="menu">
            
            <div class="menu-content">

                <div class="searchFilter">
                    <div class="versionFilter">
                        <div class="v-filter-vertical-interval">
                            <select class="select-1column-dtl" id="project" multiple="multiple">
                            </select>&nbsp;&nbsp;&nbsp;
                        </div>
                        <div class="v-filter-vertical-interval">
                            <select class="select-1column-dtl" id="item" multiple="multiple">
                            </select>&nbsp;&nbsp;&nbsp;
                        </div>
                        <div class="v-filter-vertical-interval">
                            <select class="select-1column-dtl" id="version" multiple="multiple">
                            </select>&nbsp;&nbsp;&nbsp;
                        </div>
                    </div>
                    <div class="roomFilter">
                        <div class="r-filter-vertical-interval">
                            <select class="select-2column-dtl" id="category" multiple="multiple">
                                <option value="door">ドア</option>
                                <option value="window">窓</option>
                            </select>&nbsp;&nbsp;&nbsp;
                            
                            <select class="select-2column-dtl" id="typeName" multiple="multiple">
                            </select>&nbsp;&nbsp;&nbsp;
                        </div>
                        <div class="r-filter-vertical-interval">
                            <select class="select-2column-dtl" id="level" multiple="multiple">
                            </select>&nbsp;&nbsp;&nbsp;
                            
                            <select class="select-2column-dtl" id="typePanel" multiple="multiple">
                            </select>&nbsp;&nbsp;&nbsp;
                        </div>
                        <div class="r-filter-vertical-interval">
                            <select class="select-2column-dtl" id="workset" multiple="multiple">
                            </select>&nbsp;&nbsp;&nbsp;
                    
                            <!--<select class="select-2column-dtl" id="yukaShiage" multiple="multiple">
                            </select>&nbsp;&nbsp;&nbsp;-->
                        </div>
                    </div>
                    <div class="roomFilter">
                        <!--<div class="r-filter-vertical-interval">
                            <select class="select-2column-dtl" id="tenjoShitaji" multiple="multiple">
                            </select>&nbsp;&nbsp;&nbsp;
                    
                            <select class="select-2column-dtl" id="kabeShitaji" multiple="multiple">
                            </select>&nbsp;&nbsp;&nbsp;
                        </div>
                        <div class="r-filter-vertical-interval">
                            <select class="select-2column-dtl" id="yukaShitaji" multiple="multiple">
                            </select>&nbsp;&nbsp;&nbsp;
                    
                            <select class="select-2column-dtl" id="habaki" multiple="multiple">
                            </select>&nbsp;&nbsp;&nbsp;
                        </div>-->
                        <div class="r-filter-vertical-interval">
                            <!--<select class="select-2column-dtl" id="mawaribuchi" multiple="multiple">
                            </select>&nbsp;&nbsp;&nbsp;-->
                            
                            <div style="margin-left:11px;"></div>
                            <input type="button" class="btn btn-primary" name="btnSearchConsoleData" id="btnSearchConsoleData" value="ShowData" onClick="ShowData()"/>
                            &nbsp;&nbsp;&nbsp;
                            <!--<input type="button" class="btn btn-primary" name="btnSearchConsoleDownloadData" id="btnSearchConsoleDownloadData" value="Download" data-ajax="false" onClick="DownloadForgeRoomData()"/>
                            &nbsp;&nbsp;&nbsp;-->
                            
                            <input type="hidden" id="hidAreaChartContainer" name = "hidAreaChartContainer"/>
                            <input type="hidden" id="hidRoomChartContainer" name = "hidRoomChartContainer"/>
                            <input type="hidden" id="hidCalcHeightChartContainer" name = "hidCalcHeightChartContainer"/>
                            <input type="hidden" id="hidRoomHeightChartContainer" name = "hidRoomHeightChartContainer"/>
                            <input type="hidden" id="hidPerimeterChartContainer" name = "hidPerimeterChartContainer"/>
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="menu-title">
                <img id="menuImage" src="../public/image/menu.png" alt="メニュー" style="float: right; margin: 6px 15px 0 0;" onClick="toggleFilter()">
            </div>

            
        </div>
    </div>
    <br>
    <div class="row" id="tblVersionData">
        <div class="col-6" id="data">
            
            <ul>
                <li id="Overall">
                    <div class="">一般情報<span class="icon glyphicon glyphicon-plus"></span>
                    </div>
                </li>
                <table id="tblOverall"></table>
                <li id="Sunpou">
                    <div class="">寸法<span class="icon glyphicon glyphicon-plus"></span>
                    </div>
                </li>
                <table id="tblSunpou"></table>
                <li id="Material">
                    <div class="">マテリアル / 仕上<span class="icon glyphicon glyphicon-plus"></span>
                    </div>
                </li>
                <table id = "tblMaterial"></table>
                <li id="Fire">
                    <div class="">防火<span class="icon glyphicon glyphicon-plus"></span>
                    </div>
                </li>
                <table id = "tblFire"></table>
                <li id="Moji">
                    <div class="">文字<span class="icon glyphicon glyphicon-plus"></span>
                    </div>
                </li>
                <table id = "tblMoji"></table>
                <li id = "IdenInfo">
                    <div class="">識別情報<span class="icon glyphicon glyphicon-plus"></span>
                    </div>
                </li>
                <div class="table-body"></div><table id = "tblIdenInfo"></table></div>
            </ul>
        </div>
        <div class="col-6">
            <div id="elementCount"></div>
            <div id="modelViewer"></div>
        </div>
    </div>

</div>
@endsection