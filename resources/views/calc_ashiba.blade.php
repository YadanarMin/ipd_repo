@extends('layouts.baselayout')
@section('title', 'CCC - calc Ashiba')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://developer.api.autodesk.com/modelderivative/v2/viewers/7.*/style.min.css" type="text/css">
<script src="https://developer.api.autodesk.com/modelderivative/v2/viewers/7.*/viewer3D.min.js"></script>
<script type="text/javascript" src="../public/js/library/toolbarExtension.js"></script>
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/select2/select2.min.js"></script>
<script type="text/javascript" src="../public/js/select2/i18n/ja.js"></script>
<script type="text/javascript" src="../public/js/slick.js"></script>
<!--ForAshibaCalculation-->
<script type="text/javascript" src="../public/js/calcAshiba.js"></script>  
<script type="text/javascript" src="../public/js/ashibaModelViewer.js"></script>
<!--ForAshibaCalculation-->
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
<!--ForAshibaCalculation-->
<script type="text/javascript" src="../public/js/ashibaSelection.js"></script>
<script type="text/javascript" src="../public/js/myCustomSelection2.js"></script>
<script type="text/javascript" src="../public/js/showAshibaDataTable.js"></script>
<!--ForAshibaCalculation-->

<style>
.page-title{
    margin-left:2%;
}
#modelViewer{
  width:60%;
  height: 82.5vh;
  position:relative;
  z-index:0;
}
.customizeExtension{
    background-color: red;
}

</style>


@endsection

@section('content')
@include('layouts.loading')

<div class="main-content">
    <input type="hidden" id="hidLoginID" name="hidLoginID" value="{{Session::get('login_user_id')}}"/>
    
    <h4 class="page-title">足場算出</h4>
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
                        <div style="margin-bottom:24px;">
                            <select id="version" style="height:150px !important;" class="select-1column-dtl" multiple="multiple">
                            </select>&nbsp;&nbsp;&nbsp;
                        </div>
                        
                        <label>①選択したモデルの3Dビューを表示します。</label><br>
                        <input type="button" class="btn btn-primary" name="btnSearchAshibaData" id="btnSearchAshibaData" value="ShowModel" onClick="ShowModel()"/>
                        <br>
                        <br>
                        
                        <label>②カウントする足場を3Dビュー上で選択してください！<br>
                              <b>(※右下の赤色のボタンで範囲選択可能)​</b> </label>
                        <br>
                        <br>
                        
                        <label>③3Dビュー上で選択した足場の数量を表示します。<br>
                        (画面を下スクロールしてください)</label>
                        <br>
                        <input type="button" class="btn btn-primary" name="btnShowAshibaData" id="btnShowAshibaData" value="LoadData" onClick="LoadData()"/>
                        
                        <!--<br>-->
                        <!--<br>-->
                        <!--<input type="button" class="btn btn-primary" name="btnInsertSegregation" id="btnInsertSegregation" value="SegregateData" onClick="SegregateData()"/>-->
                        <!--<input type="text"  id="segregateText" class="" >-->
                        <div style="margin-bottom: 20px;"></div>
                    
                    </div>
                    
                         <div id="modelViewer">
                        </div>
                    
          
                </div>
            </div>
            <div class="menu-title">
                <img id="menuImage" src="../public/image/menu.png" alt="メニュー" style="float: right; margin: 6px 15px 0 0;" onClick="toggleFilter()">
            </div>
        </div>
    </div>

    <div id="tblVersionData">
        <center>
           
            <div style="margin-bottom: 20px;"></div>
            <table id="sugikoTable" class="table table-bordered" style="margin-left: auto; margin-right: auto;">
                            <thead> 
                                <tr>
                                    <th>Sugiko FamilyName</th>
                                    <th>Sugiko TypeName</th>
                                    <th>Sugiko-ID</th>
                                    <th>Sugiko-Name</th>
                                    <th>Quantity</th>
                                    <th>Unit Weight</th>
                                    <th>Total Weight</th>
                                </tr>
                            </thead>
                            
                            <tbody id="showTable">
                                <!--<tr>-->
                                <!--    <td id="sugikoFamily"></td>-->
                                <!--    <td id="sugikoType"></td>-->
                                <!--    <td id="sugikoId"></td>-->
                                <!--    <td id="sugikoName"></td>-->
                                <!--    <td id="sugikoQuantity"></td>-->
                                <!--    <td id="sugikoWeight"></td>-->
                                <!--    <td id="sugikoTotalWeight"></td>-->
                                    
                                <!--</tr>-->
                                
                                
                            </tbody>
                        </table>
        </center>
        
        
    </div>
    <input type="hidden" id="hidAuthorityID" name="hidAuthorityID" value="{{Session::get('authority_id')}}"/>
</div>
<script>
    $( document ).ready(function() {
      if($("#showTable >tr").length == 0){
        var row = "<tr><td colspan=7 style='text-align: center'>There is no data in table.</td> </tr>";
        $("#showTable").append(row);
      }
    });
</script>
@endsection