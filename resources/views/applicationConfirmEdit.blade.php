@extends('layouts.baselayout')
@section('title', '内容確認')

<!--CSS and JS file-->
@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>
<script type="text/javascript" src="../public/js/xls-export.js"></script>
<script type="text/javascript" src="../public/js/xlsx.full.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.3/FileSaver.min.js"></script>
<script type="text/javascript" src="../public/js/applicationConfirm.js"></script>



<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/redmond/jquery-ui.css" >
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1/i18n/jquery.ui.datepicker-ja.min.js"></script>
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js" />-->
<!--<script src="https://cdn.grapecity.com/wijmo/5.latest/controls/wijmo.min.js" />-->
<!--<script src="https://cdn.grapecity.com/wijmo/5.latest/controls/wijmo.input.min.js" />-->
<!--<script  src="https://cdn.grapecity.com/wijmo/5.latest/controls/wijmo.xlsx.min.js" />-->
<!--<script src="https://cdn.grapecity.com/wijmo/5.latest/controls/wijmo.grid.xlsx.min.js" />-->
<link rel="stylesheet" href="../public/css/jquery-ui.multidatePicker.css">
<script type="text/javascript" src="../public/js/jquery-ui.multidatePicker.js"></script>

<style>
.main-content{
	/*background-color: #f2f3f3;*/
	margin: 0 auto;
	width:95%;
	display : center;
} 
.editView{
    display : flex;
    justify-content: space-around;
    padding : 10px;
    border : 1px solid #eee;
}
.calendarView{
    width : 20%;
}
.tableView{
    width : 70%;
}


<!--custom UI Datepicker-->
.ui-state-default a {
    background: black none;
    color: white;
}
.myhighlight a{
	background: yellow none !important;
    color: black !important;
	
	/*color: white !important;*/
}
.ui-datepicker td.ui-state-disabled>span{
    background:green;
    color : white;}
.ui-datepicker td.ui-state-disabled{
    opacity:100;
}
.myhighlightDecided a{
	background: green none !important;
    color: white !important;
	
	/*color: white !important;*/
}
.myhighlightDisable a{
	background: red none !important;
    color: white !important;
	
	/*color: white !important;*/
}

.today a{
    background: #87b6da none !important;
    color: white !important;
}
ul{
    list-style-type : none;

}
.ui-widget-header .ui-icon {
    /* background-image: url(images/ui-icons_d8e7f3_256x240.png); */
}
.ui-datepicker td.ui-state-disabled>span{
    background:red;
    color: white !important;
}
.ui-datepicker td.ui-state-disabled{opacity:50;}
.selectedUserTable{
    display: flex;
    overflow: auto;
    white-space: nowrap;
    /* height: 500px; */
    width: 100%;
    border: 1px solid #eee;
    margin-bottom : 3%;
}
.searchDiv{
    border : 1px solid #eee;
    padding : 10px;
}
.searchResult{
    width : 40%;
    margin-top : 2%;
    margin-left : 5%;
}
#warningDiv{
    margin-top: 5px;
    background: #dedeac;
    padding: 10px;
}
#AM, #PM{
    position: absolute;
    margin-left: 20%;
    margin-top: -5px;
    /* margin-top: 3%; */
    /* writing-mode: vertical-lr; */
    background: #d9edf7;
    padding: 5px;
    }
.updateDateBtnAM,.updateDateBtnPM{
    position: absolute;
    margin-left: -7%;
    margin-top :-10px;
    
}
</style>
@endsection
@section('content')
@include('layouts.loading')
<div class="main-content">
    <h3>BIM速習コース管理</h3>
    <hr>
    <div class="editView">
        <div class="calendarView">
            <div id="editCalendar">
                
            </div>
            
            <input type="hidden" id="hiddenSelectedMonth" value="" />
            
            <!--<div id="dialog1">-->
            <!--    <button class="btn btn-success" onClick ="DecideDate()">決定</button>-->
            <!--    <button class="btn btn-danger" onClick="ClearDesireDate()">クリア</button>-->
            <!--</div>-->
            <!--<div id="dialog2">-->
            <!--    <button class="btn btn-danger" onClick="DisableDate()">NG日に設定する</button>-->
                
            <!--</div>-->
            <div id="colorLabel" style="margin-top: 5%;">
                
                    <div style="margin-bottom : 5px"><span style="background:green; display: inline-block; width: 20px; height: 20px;">&nbsp;</span>&nbsp;決定日</div>
                    <div  style="margin-bottom : 5px"><span style="background:yellow; display: inline-block; width: 20px; height: 20px;">&nbsp;</span>&nbsp;希望日​</div>
                    <div  style="margin-bottom : 5px"><span style="background:red; display: inline-block; width: 20px; height: 20px;">&nbsp;</span>&nbsp;NG日</div>
                
                
            </div>
            <hr>
            <div id="btnDiv" style="display : flex">
                <button class="btn btn-danger" onClick ="CloseApplicationForMonth()" style="margin-right: 5px">受付を締め切る</button>
                <button class="btn btn-info" onClick="ReopenApplicationForMonth()">受付を締め切り解除</button>
            </div>
            <div id="warningDiv">
                <p id="warning">この月の受付を締め切りました。</p>
            </div>
            
        </div>
        <input type="hidden" id="hiddenSelectedDate" value="" />
        <input type="hidden" id="hiddenDisableDateList" value="" />
        <input type="hidden" id="hiddenDecidedDateList" value="" />
        
        <div class="tableView">
            <!--Start of AM-->
            <div class="AM" style="margin-left: 10%">
                <h4 id="AM">午前</h4>
                <table width="100%">
                    <tr>
                        <td width="10%"><h5>日程    :</h5></td>
                        <td><h5 id="selectedDateAM"></h5></td>
                    </tr>
                    <tr>
                        <td width="10%"><h5>受講人数:</h5></td>
                        <td><h5><span id="numOfApplicantsAM"></span>人 </h5></td>
                    </tr>
                </table>
                <div class="updateDateBtnAM">
                    <!--<button class="btn btn-success">決定</button>-->
                    <!--<button class="btn btn-info">クリア</button>-->
                </div>
                <div class="selectedUserTable">
                    <table class="table table-bordered" id="selectedUserInfoTableAM">
                        <thead>
                            <tr class="info">
                                <td>氏名</td>
                                <td>企業</td>
                                <td>会社名</td>
                                <td>所属</td>
                                <td>支店</td>
                                <td>社員コード</td>
                                <td>役職</td>
                                <td>メールアドレス</td>
                                <td>招待者</td>
                                <td>講習形態</td>
                                <td>希望日</td>
                                
                                
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!--Start of PM-->
            <div class="PM" style="margin-left: 10%">
                <h4 id="PM">午後</h4>
                <table width="100%">
                    <tr>
                        <td width="10%"><h5>日程    :</h5></td>
                        <td><h5 id="selectedDatePM"></h5></td>
                    </tr>
                    <tr>
                        <td width="10%"><h5>受講人数:</h5></td>
                        <td><h5><span id="numOfApplicantsPM"></span>人 </h5></td>
                    </tr>
                </table>
                <div class="updateDateBtnPM">
                    <!--<button class="btn btn-success">決定</button>-->
                    <!--<button class="btn btn-info">クリア</button>-->
                </div>
                <div class="selectedUserTable">
                    <table class="table table-bordered" id="selectedUserInfoTablePM">
                        <thead>
                            <tr class="info">
                                <td>氏名</td>
                                <td>企業</td>
                                <td>会社名</td>
                                <td>所属</td>
                                <td>支店</td>
                                <td>社員コード</td>
                                <td>役職</td>
                                <td>メールアドレス</td>
                                <td>招待者</td>
                                <td>講習形態</td>
                                <td>希望日</td>
                                
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
            
            
            
            
            <div class="searchDiv" style="margin-top :5%; margin-left:10%">
                <div class="form-inline">
                    <div class="form-group">
                        <label>日付：</label>
                        <input type="text" class="form-control" name="startDate" id="startDate" placeholder="　　年　－月　ー日　" autocomplete="off"　/>
                        
                        <label>～</label>
                        <input type="text" class="form-control" name="endDate" id="endDate" placeholder="　　年　－月　ー日　" autocomplete="off"　/>
                    </div>    
                    <button class="btn btn-primary" style="margin-left :2%" onclick="searchPlace()">検索</button>
                    <button class="btn btn-primary" style="margin-left :2%" onclick="excelExport()">出力</button>
                </div>
                <div class="searchResult">
                    
                    <div class="panel panel-info">
                         <div class="panel-heading" style="color : black">開催数</div>
                         <div class="panel-body">
                             <ul class="list-group" id="listOfMise">
                              
                            </ul>
                         </div>
                    </div>
                    
                </div>
                
            </div>
            
            <div id="theGrid" style="display : none">
                <!--<table class="table table-bordered" id="exportDataTable">-->
                <!--    <thead>-->
                <!--        <tr>-->
                <!--            <th>日付</th>-->
                <!--            <th>氏名</th>-->
                <!--            <th>企業名</th>-->
                <!--            <th>会社名</th>-->
                <!--            <th>所属</th>-->
                <!--            <th>支店</th>-->
                <!--            <th>社員コード</th>-->
                <!--            <th>役職</th>-->
                <!--            <th>メールアドレス</th>-->
                <!--        </tr>-->
                <!--    </thead>-->
                <!--    <tbody>-->
                        
                <!--    </tbody>-->
                <!--</table>-->
            </div>
            
        </div>
    </div>
    
    
</div>
<script>
$(document).ready(function(){

  $('#startDate').datepicker({
    dateFormat: 'yy-mm-dd'
  });    
  $('#endDate').datepicker({
    dateFormat: 'yy-mm-dd'
  });
  
  
   
});
</script>
@endsection