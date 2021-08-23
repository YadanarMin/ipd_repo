@extends('layouts.baselayout')
@section('title', 'CCC - Room info search')

@section('head')
<link rel="stylesheet" href="https://developer.api.autodesk.com/modelderivative/v2/viewers/7.*/style.min.css" type="text/css">
<script src="https://developer.api.autodesk.com/modelderivative/v2/viewers/7.*/viewer3D.min.js"></script>
<script type="text/javascript" src="../public/js/library/toolbarExtension.js"></script>
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/select2/select2.min.js"></script>
<script type="text/javascript" src="../public/js/select2/i18n/ja.js"></script>
<script type="text/javascript" src="../public/js/slick.js"></script>
<script type="text/javascript" src="../public/js/roomInfoSearchConsole.js"></script>
<script type="text/javascript" src="../public/js/xlsx.full.min.js"></script>
<script type="text/javascript" src="../public/js/modelViewer.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.3/FileSaver.min.js"></script>
<link rel="stylesheet" href="../public/css/select2.min.css">
<link rel="stylesheet" href="../public/css/slick.css">
<link rel="stylesheet" href="../public/css/slick-theme.css">
<link rel="stylesheet" href="../public/css/roomInfoSearchConsole.css">
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<style>
.page-title{
    margin-left:6.5%;
}
</style>
@endsection

@section('content')
@include('layouts.loading')
<div class="main-content">
    
    <input type="hidden" id="hidLoginID" name="hidLoginID" value="{{Session::get('login_user_id')}}"/>
    
    <h4 class="page-title">部屋データ分析</h4>
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
                            <select class="select-2column-dtl" id="level" multiple="multiple">
                            </select>&nbsp;&nbsp;&nbsp;
                            
                            <select class="select-2column-dtl" id="workset" multiple="multiple">
                            </select>&nbsp;&nbsp;&nbsp;
                        </div>
                        <div class="r-filter-vertical-interval">
                            <select class="select-2column-dtl" id="roomName" multiple="multiple">
                            </select>&nbsp;&nbsp;&nbsp;
                            
                            <select class="select-2column-dtl" id="tenjoShiage" multiple="multiple">
                            </select>&nbsp;&nbsp;&nbsp;
                        </div>
                        <div class="r-filter-vertical-interval">
                            <select class="select-2column-dtl" id="kabeShiage" multiple="multiple">
                            </select>&nbsp;&nbsp;&nbsp;
                    
                            <select class="select-2column-dtl" id="yukaShiage" multiple="multiple">
                            </select>&nbsp;&nbsp;&nbsp;
                        </div>
                    </div>
                    <div class="roomFilter">
                        <div class="r-filter-vertical-interval">
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
                        </div>
                        <div class="r-filter-vertical-interval">
                            <select class="select-2column-dtl" id="mawaribuchi" multiple="multiple">
                            </select>&nbsp;&nbsp;&nbsp;
                            
                            <div style="margin-left:11px;"></div>
                            <input type="button" class="btn btn-primary" name="btnSearchConsoleData" id="btnSearchConsoleData" value="ShowData" onClick="ReportForgeRoomData()"/>
                            &nbsp;&nbsp;&nbsp;
                            <input type="button" class="btn btn-primary" name="btnSearchConsoleDownloadData" id="btnSearchConsoleDownloadData" value="Download" data-ajax="false" onClick="DownloadForgeRoomData()"/>
                            &nbsp;&nbsp;&nbsp;
                            
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
    <div id="tblVersionData">
    </div>

</div>
@endsection