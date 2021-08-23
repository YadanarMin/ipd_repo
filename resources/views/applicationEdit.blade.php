@extends('layouts.baselayout')
@section('title', '内容確認・変更')

<!--CSS and JS file-->
@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>
<script type="text/javascript" src="../public/js/applicationEdit.js"></script>

<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/redmond/jquery-ui.css" >
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1/i18n/jquery.ui.datepicker-ja.min.js"></script>
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
    width : 30%;
}
.tableView{
    width : 70%;
}


<!--custom UI Datepicker-->
.ui-datepicker .ui-datepicker-calendar .ui-state-highlight a {
    background: yellow none;
    color: white;
}
.myhighlight a{
	background: yellow  !important;
    color: black !important;
}
.myhighlightDecided a{
	background: green none !important;
    color: white !important;
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
.ui-datepicker td.ui-state-disabled>span{
    background:red;
    color: white !important;
}
.ui-datepicker td.ui-state-disabled{opacity:50;}
ul{
    list-style-type : none;

}
.selectedUserTable{
    display: flex;
    overflow: auto;
    white-space: nowrap;
    /* height: 500px; */
    width: 100%;
    border: 1px solid #eee;
    margin-bottom : 20px;
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
</style>
@endsection
@section('content')
@include('layouts.loading')
<div class="main-content">
    <h3>内容確認・変更</h3>
    <hr>
    <input type="hidden" name="hiddenLoginUser" id="hiddenLoginUser" value="{{  Session::get('userName')}}" />
    <input type="hidden" name="hiddenLoginId" id="hiddenLoginId" value="{{  Session::get('login_user_id')}}" />
    <div class="editView">
        <div class="calendarView">
            <input type="hidden" name="hiddenDecidedDateList" id="hiddenDecidedDateList"/>
            <div id="editCalendar">
                
            </div>
            <div id="colorLabel" style="margin-top: 5%;">
                
                    <div style="margin-bottom : 5px"><span style="background:green; display: inline-block; width: 20px; height: 20px;">&nbsp;</span>&nbsp;決定日(開講決定日です。受講者は当日受講してください。)</div>
                    <div  style="margin-bottom : 5px"><span style="background:yellow; display: inline-block; width: 20px; height: 20px;">&nbsp;</span>&nbsp;希望日(調整中です。開講日が決定するまでお待ちください。)​</div>
                    <div  style="margin-bottom : 5px"><span style="background:red; display: inline-block; width: 20px; height: 20px;">&nbsp;</span>&nbsp;NG日(開講できない日です)</div>
                
                
            </div>
        </div>
        <div class="tableView">
            <!--AM-->
            <div class="AM" style="margin-bottom :2%">
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
                <div  id="AMDiv">
                    <div class="selectedUserTable">
                    <table class="table table-bordered" id="selectedUserInfoTableAM">
                        <thead>
                            <tr class="info">
                                <td style="display:none"></td>
                                <td style="display:none"></td>
                                <td style="display:none"></td>
                                <td>クリア</td>
                                <td>変更</td>
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
                
            </div>
            
            <hr>
            <!--PM-->
            <div class="PM">
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
                <div  id="PMDiv">
                    <div class="selectedUserTable">
                        <table class="table table-bordered" id="selectedUserInfoTablePM">
                            <thead>
                                <tr class="info">
                                    <td style="display:none"></td>
                                    <td style="display:none"></td>
                                    <td style="display:none"></td>
                                    <td>クリア</td>
                                    <td>変更</td>
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
                
            </div>
        </div>
        
        
    </div>
    
    
</div>
<script>
//   $(document).ready(function(){
//       $("#editCalendar").multiDatesPicker({
//          dateFormat: 'yy-mm-dd',
//          onSelect  : function(){
//                         if($(this).val() == '2021-05-01'){
//                             $(this).mousedown(function(e){
//                                 if(e.which ==3){
                                    
//                                 }
//                             });
//                         }else{
//                             alert($(this).val());
//                         }
         
//                     }
//       });
//       <!--$("#editCalendar").mousedown(function(e){-->
//       <!--  alert($(this).val())-->
//       <!--  if($(this).val() == '2021-05-01'){-->
        
//       <!--      if(e.which == 3){-->
//       <!--          alert("h");-->
//       <!--      }-->
//       <!--  }-->
//       <!--});-->
//   });
</script>
@endsection