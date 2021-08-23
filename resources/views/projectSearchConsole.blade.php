@extends('layouts.baselayout')
@section('title', 'CCC - Project search')

@section('head')

<link rel="stylesheet" href="https://developer.api.autodesk.com/modelderivative/v2/viewers/7.*/style.min.css" type="text/css">
<script src="https://developer.api.autodesk.com/modelderivative/v2/viewers/7.*/viewer3D.min.js"></script>
<script type="text/javascript" src="../public/js/library/toolbarExtension.js"></script>
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/select2/select2.min.js"></script>
<script type="text/javascript" src="../public/js/select2/i18n/ja.js"></script>
<script type="text/javascript" src="../public/js/slick.js"></script>
<script type="text/javascript" src="../public/js/projectSearchConsole.js"></script>
<script type="text/javascript" src="../public/js/modelViewer.js"></script>
<script type="text/javascript" src="../public/js/doorWindowDetail.js"></script>
<script type="text/javascript" src="../public/js/xlsx.full.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.3/FileSaver.min.js"></script>
<script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"></script>
<script src="https://cdn.geolonia.com/community-geocoder.js"></script>
<link rel="stylesheet" href="../public/css/select2.min.css">
<link rel="stylesheet" href="../public/css/slick.css">
<link rel="stylesheet" href="../public/css/slick-theme.css">
<link rel="stylesheet" href="../public/css/projectSearchConsole.css">
<link rel="stylesheet" href="../public/css/doorWindow.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" />
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<style>
.page-title{
    margin-left:2%;
}
input[name="rdo"]{
    -webkit-appearance: radio;
     display: inline;
}
</style>

@endsection

@section('content')
@include('layouts.loading')

<div class="main-content">

    <input type="hidden" id="hidLoginID" name="hidLoginID" value="{{Session::get('login_user_id')}}"/>

    <h4 class="page-title">モデル分析</h4>
    <div class="dropdown">
        <div class="menu">
            <div class="menu-content">
                <div class="searchFilter">
                    
                    <div class="versionFilter">
                        <div class="v-filter-vertical-interval">
                            <select id="project" class="select-1column-dtl" multiple="multiple">
                            </select>&nbsp;&nbsp;&nbsp;
                        </div>
                        <div class="v-filter-vertical-interval">
                            <select id="item" class="select-1column-dtl" multiple="multiple">
                            </select>&nbsp;&nbsp;&nbsp;
                            
                        </div>
                        <div class="v-filter-vertical-interval">
                            <input type="checkbox" name="chkLatedVersion" id="chkLatedVersion" checked>&nbsp;&nbsp;最新版選択
                        </div>
                        
                        <div style="margin-bottom:24px;">
                            
                            <select id="version" style="height:150px !important;" class="select-1column-dtl" multiple="multiple">
                            </select>&nbsp;&nbsp;&nbsp;
                        </div>
                        <div class="v-filter-vertical-interval" style="display:flex;flex-wrap:nowrap;">
                            <select class="select-3column-dtl" id="category" multiple="multiple">
                                <option value="column">構造柱</option>
                                <option value="beam">梁</option>
                                <option value="floor">床</option>
                                <option value="wall">壁</option>
                                <option value="foundation">構造基礎</option>
                                <option value="window">窓</option>
                                <option value="door">ドア</option>
                            </select>&nbsp;&nbsp;&nbsp;
                            
                            <select class="select-3column-dtl" id="level" multiple="multiple">
                            </select>&nbsp;&nbsp;&nbsp;
                    
                            <select class="select-3column-dtl" id="workset" multiple="multiple">
                            </select>&nbsp;&nbsp;&nbsp;
                        </div>
                        <div class="v-filter-vertical-interval" style="display:flex;flex-wrap:wrap;">
                            <select class="select-1column-dtl" id="material" multiple="multiple">
                            </select>
                        </div>
                        <div class="v-filter-vertical-interval" style="display:flex;flex-wrap:wrap;">
                            <select class="select-1column-dtl" id="familyName" multiple="multiple">
                            </select>
                        </div>
                        <div class="v-filter-vertical-interval" style="display:flex;flex-wrap:wrap;">
                            <select class="select-1column-dtl" id="typeName" multiple="multiple">
                            </select>
                        </div>
                        <div class="v-filter-vertical-interval" style="display:flex;flex-wrap:wrap;">
                            <input type="text" class="select-1column-dtl" id="inputTypeName" placeholder="タイプ名を入力">
                        </div>


                        <div style="margin: 1.5vh 0 1.5vh 0;border-bottom: 1px solid #dae1e6;">
                        </div>
                        <div class="p-filter-vertical-interval" style="display:flex;flex-wrap:nowrap;">
                            <label>マップ表示設定　:</label>&nbsp;&nbsp;&nbsp;
                            <input type="radio" name="rdo" value="rdoLimit" checked="checked">&nbsp;CCC取込物件&nbsp;&nbsp;&nbsp;
                            <input type="radio" name="rdo" value="rdoAll">&nbsp;全店物件
                        </div>
                        <div class="p-filter-vertical-interval" style="display:flex;flex-wrap:nowrap;">
                            <select type="hidden" style="width:424.5px;" id="branchStore" multiple="multiple">
                            </select>&nbsp;&nbsp;&nbsp;
                        </div>
                        <div class="p-filter-vertical-interval" style="display:flex;flex-wrap:nowrap;">
                            <select type="hidden" style="width:424.5px;" id="constructionType" multiple="multiple">
                            </select>&nbsp;&nbsp;&nbsp;
                        </div>
                        <div class="p-filter-vertical-interval" style="display:flex;flex-wrap:nowrap;">
                            <select type="hidden" style="width:424.5px;" id="buildingUse" multiple="multiple">
                            </select>&nbsp;&nbsp;&nbsp;
                        </div>
                        <div class="p-filter-vertical-interval" style="display:flex;flex-wrap:nowrap;">
                            <select type="hidden" style="width:424.5px;" id="construction" multiple="multiple">
                            </select>&nbsp;&nbsp;&nbsp;
                        </div>
                        <div class="p-filter-vertical-interval" style="display:flex;flex-wrap:nowrap;">
                            <select type="hidden" style="width:424.5px;" id="orderer" multiple="multiple">
                            </select>
                        </div>
                        <div class="p-filter-vertical-interval" style="display:flex;flex-wrap:nowrap;">
                            <select type="hidden" style="width:424.5px;" id="designer" multiple="multiple">
                            </select>
                        </div>
                        <div class="p-filter-vertical-interval" style="display:flex;flex-wrap:nowrap;">
                            <select type="hidden" style="width:424.5px;" id="prjAddress" multiple="multiple">
                            </select>&nbsp;&nbsp;&nbsp;
                        </div>

                        <!--<div style="display:flex;flex-wrap:nowrap;margin-bottom:23px;">-->
                        <!--    <select type="hidden" style="width:424.5px;" id="relatedCompanies" multiple="multiple">-->
                        <!--    </select>-->
                        <!--</div>-->

                        
                    </div>
                    <div class="regionFilter">
                        <div id="project_regions"></div>
                        <!--<div class="v-filter-vertical-interval">-->
                        <!--</div>-->
                        <div class="p-filter-vertical-interval" style="display:flex;flex-wrap:wrap;justify-content:flex-end;margin-left:233px;">
                            <input type="button" class="btn btn-primary" name="btnSearchConsoleData" id="btnSearchConsoleData" value="ShowData" onClick="ReportForgeData('{{Session::get('token')}}')"/>
                            &nbsp;&nbsp;&nbsp;
                            <input type="button" class="btn btn-primary" name="btnSearchConsoleDownloadData" id="btnSearchConsoleDownloadData" value="Download" data-ajax="false" onClick="DownloadForgeData()"/>
                            &nbsp;&nbsp;

                            <input type="hidden" id="hidVolumePieChartContainer" name = "hidVolumePieChartContainer"/>
                            <input type="hidden" id="hidMaterialsPieChartContainer" name = "hidMaterialsPieChartContainer"/>
                            <input type="hidden" id="hidFamilyNamePieChartContainer" name = "hidFamilyNamePieChartContainer"/>
                            <input type="hidden" id="hidTypeNamePieChartContainer" name = "hidTypeNamePieChartContainer"/>
                            <input type="hidden" id="hidPricePieChartContainer" name = "hidPricePieChartContainer"/>
                            <input type="hidden" id="hidTypePanelPieChartContainer" name = "hidTypePanelPieChartContainer"/>
                            <input type="hidden" id="hidFurniturePieChartContainer" name = "hidFurniturePieChartContainer"/>
                        </div>
                    </div>
                    <div class="projectFilter">
                    </div>

                </div>
            </div>
            <div class="menu-title">
                <img id="menuImage" src="../public/image/menu.png" alt="メニュー" style="float: right; margin: 6px 15px 0 0;" onClick="toggleFilter()">
            </div>
        </div>
    </div>

    <div id="tblVersionData">
    </div>
    <input type="hidden" id="hidAuthorityID" name="hidAuthorityID" value="{{Session::get('authority_id')}}"/>
</div>
@endsection