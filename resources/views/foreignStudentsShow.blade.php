@extends('layouts.baselayout')
@section('title', 'CCC - 留学生確認')

<!--CSS and JS file-->
@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css" />
<link rel="stylesheet" href="../public/css/foreignStudentsShow.css" />
<script type="text/javascript" src="../public/js/foreignStudentsShow.js"></script>
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>

<style>
tr, td{
    font-size: 16px;
}

.checkboxDiv{
    display : flex;
    justify-content: space-between;
}
/*thead tr th {*/
/*    position: sticky;*/
/*    top: 0;*/
/*}*/
</style>
@endsection

@section('content')
@include('layouts.loading')
<div class="main-content">
    
    <div class="btn-group customBtn" role="group" aria-label="...">
      <button type="button" class="btn btn-default" id="insertBtn" onclick="InsertBtn();" ><h5>留学生入力</h5></button>
      <button type="button" class="btn btn-default" id="showBtn" onclick="ShowBtn();"><h5>留学生情報確認</h5></button>
      
    </div>
    
    <div id="studentInfo">
        <!--<h3 id="studentInfoTitle">留学生情報確認​</h3>-->
        <hr>
        
        
        <div class="input-group">
          <span class="input-group-addon"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></span>
          <input type="text" id="userSearch" class="form-control" placeholder="氏名・社員コード・分野・派遣元所属で検索">
        </div>
        
        <div class="divider"></div>
        
        <div class="checkboxDiv">
            <!--ForeignStudentFormCheck-->
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="all" checked>
                <label class="form-check-label">All &nbsp;</label>
                
                <input type="checkbox" class="form-check-input" id="notfinished">
                <label class="form-check-label">留学中 &nbsp;</label>
                
                <input type="checkbox" class="form-check-input" id="notyet">
                <label class="form-check-label">留学予定 &nbsp;</label>
                
                <input type="checkbox" class="form-check-input" id="finished">
                <label class="form-check-label">留学終了 &nbsp;</label>
            </div>
            
            <!--TimelineFormCheck-->
            <div class="form-check changeTimeline" style="margin-right : 200px">
                
                <label class="form-check-label" style="margin-right: 10px;">表示単位:</label>
                
                <input type="radio" class="form-check-input" value="3650" name="time" checked>
                <label class="form-check-label" style="margin-right: 10px;">10年 &nbsp;</label>
                
                <input type="radio" class="form-check-input" value="1825" name="time" checked>
                <label class="form-check-label" style="margin-right: 10px;">5年 &nbsp;</label>
                
                <input type="radio" class="form-check-input" value="1095" name="time" checked>
                <label class="form-check-label" style="margin-right: 10px;">3年 &nbsp;</label>
                
                <input type="radio" class="form-check-input" value="730" name="time" >
                <label class="form-check-label" style="margin-right: 10px;">2年 &nbsp;</label>
                
                <input type="radio" class="form-check-input" value="365" name="time" >
                <label class="form-check-label" style="margin-right: 10px;">1年 &nbsp;</label>
                
                <input type="radio" class="form-check-input" value="180" name="time">
                <label class="form-check-label" style="margin-right: 10px;">6ヶ月 &nbsp;</label>
                
                <input type="radio" class="form-check-input" value="90" name="time">
                <label class="form-check-label" style="margin-right: 10px;">3ヶ月 &nbsp;</label>
            </div>
        </div>
        
        
        
        
        <div id="studentNameList">
            <div id="studentName">
                <table class="table table-bordered table-hover" id="usernameList">
                    <thead>
                        <tr class="info">
                            <th>氏名</th>
                            <th style="display:none;">社員コード</th>
                            <th style="display:none;">分野</th>
                            <th style="display:none;">派遣</th>
                            <th style="display:none;">大林</th>
                            <th style="display:none;">種別</th>
                            <th style="display:none;">店</th>
                            <th style="display:none;">スキル</th>
                            
                        </tr>
                    </thead>
                    <tbody id="searchableUserList">
                        
                    </tbody>
                </table>
            </div>
            <div id="timeline">
                
                <table class="borderless" id="timelineList">
                    <thead>
                        <tr class="timelineHead">
                            <th>
                                <div style="display:block">
                                    <span id="today"><?php echo date("Y-m-d") ?></span>
                                    <span id="vline"></span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="searchableTimelineList">
                        
                    </tbody>
                </table>
            </div>
        </div>
        
        <hr>
        <div class="divider"></div>
        
        <div id="studentInfoTable">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr class="info">
                        <th></th>
                        <th>支店</th>
                        <th>大林組所属</th>
                        <th>社員コード</th>
                        <th>派遣元所属</th>
                        <th>スキル</th>
                        <th>分野</th>
                        <th>留学種別</th>
                        <th>留学開始</th>
                        <th>留学終了</th>
                        <th>プチ留学1</th>
                        <th>プチ留学2</th>
                        <th>プチ留学3</th>
                        <th>プチ留学4</th>
                    
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>現在</td>
                        <td id="e-place"></td>
                        <td id="e-obayashi"></td>
                        <td id="e-code"></td>
                        <td id="e-haken"></td>
                        <td id="e-skill"> </td>
                        <td id="e-field"></td>
                        <td id="e-type"></td>
                        <td id="e-startDate"></td>
                        <td id="e-endDate"></td>
                        <td id="e-puchi1"></td>
                        <td id="e-puchi2"></td>
                        <td id="e-puchi3"></td>
                        <td id="e-puchi4"></td>
                        
                    </tr>
                    <tr>
                        <td>留学時</td>
                        <td id="s-place"></td>
                        <td id="s-obayashi"></td>
                        <td id="s-code"></td>
                        <td id="s-haken"></td>
                        <td id="s-skill"> </td>
                        <td id="s-field"></td>
                        <td id="s-type"></td>
                        <td id="s-startDate"></td>
                        <td id="s-endDate"></td>
                        <td id="s-puchi1"></td>
                        <td id="s-puchi2"></td>
                        <td id="s-puchi3"></td>
                        <td id="s-puchi4"></td>
                        
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div id="compareStudent">
            <div class="compareTitle" style="padding : 10px">
                <p style="font-weight: bold">比較する留学生 &nbsp;<span id="numOfCompareStudent"></span>　人　</p>
            </div>
            <div class="compareWrap">
                <div class="compareWrapIn">
                    <div class="compareSlide">
                        <div class="compareSlideIn">
                            <div class="previous" style=""><span class="glyphicon glyphicon-backward"></span></div>
                            <div class="displayCompareStudentList">
                        
                            <!--Display compare student-->
                        
                            </div>
                            <div class="next"><span class="glyphicon glyphicon-forward"></span></div>
                        </div>
                        
                    </div>
                </div>
                <div class="compareWrapIn compareBtn">
                    <button class="btn btn-primary" id="compareBtn" onclick="CompareStudent()"; >比較する</button>
                </div>
            </div>  
            <h3 class="glyphicon glyphicon-remove-sign closeSign"></h3>
            
            
        </div>
    </div>
    
</div>
<div id="comparePage"></div>
<script>

$(document).ready(function(){
    $('#notfinished, #finished, #notyet').click(function(){
        $('#all').removeAttr('checked');
        var isNotFinished = $("#notfinished").is(":checked");
        var isFinished = $("#finished").is(":checked");
        var isNotYet = $("#notyet").is(":checked");
        
        console.log("Radio" + $(".changeTimeline input[type='radio']:checked").val());
        
        if(isNotFinished && isFinished && isNotYet){
            ClearAllStudents();
            LoadAllStudents($(".changeTimeline input[type='radio']:checked").val());
            
        }else if(isNotFinished && isNotYet){
            ClearAllStudents();
            LoadNotFinishedAndNotYetStudent($(".changeTimeline input[type='radio']:checked").val());
            
        }else if(isNotFinished && isFinished ){
            ClearAllStudents();
            LoadNotFinishedAndFinishedStudent($(".changeTimeline input[type='radio']:checked").val());
            
        }else if(isNotYet && isFinished ){
            ClearAllStudents();
            LoadNotYetAndFinishedStudent($(".changeTimeline input[type='radio']:checked").val());
            
        }else if(isNotFinished){
            ClearAllStudents();
            LoadNotFinishedStudent($(".changeTimeline input[type='radio']:checked").val());
            
        }else if(isNotYet){
            ClearAllStudents();
            LoadNotYetStudent($(".changeTimeline input[type='radio']:checked").val());
            
        }else if(isFinished){
            ClearAllStudents();
            LoadFinishedStudent($(".changeTimeline input[type='radio']:checked").val());
        }else{
            ClearAllStudents();
            ClearAllTimeline();
        }
        
      });
      
      $('#all').click(function(){
        window.location.reload();
      });
      
      
      
      
 }); 
  
    
</script>
@endsection