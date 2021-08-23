@extends('layouts.baselayout')
@section('title', '内容確認・変更')

<!--CSS and JS file-->
@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script type="text/javascript" src="../../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../../public/js/jquery.multiselect.js"></script>
<script type="text/javascript" src="../../public/js/applicationEditById.js"></script>


<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/redmond/jquery-ui.css" >
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1/i18n/jquery.ui.datepicker-ja.min.js"></script>
<link rel="stylesheet" href="../../public/css/jquery-ui.multidatePicker.css">
<script type="text/javascript" src="../../public/js/jquery-ui.multidatePicker.js"></script>
<style>
.main-content{
	/*background-color: #f2f3f3;*/
	margin: 0 auto;
	width:90%;
	display : center;
}  
.editView{
   width: 80%;
   margin: 0 auto; 
}
label{
    font-size : 16px;
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
/*.ui-datepicker td.ui-state-disabled>span{*/
/*    background:red;*/
/*    color: white !important;*/
/*}*/
/*.ui-datepicker td.ui-state-disabled{opacity:50;}*/

.ui-datepicker .ui-datepicker-calendar .ui-state-highlight a {
    background: yellow none !important;
    color: black !important;
}
.today a{
    background: #87b6da none !important;
    color: white !important;
}
#disableMonthWarning {
    margin-left: 10%;
    margin-top: 3%;
    padding: 20px;
    border: 1px solid #eee;
    background: #d6d4d3;
    height: 100px;
}
</style>
@endsection
@section('content')
@include('layouts.loading')
<div class="main-content">
    <h3>内容確認・変更</h3>
    <hr>
    <div class="editView">
        <input type="hidden" name="hiddenLoginUser" id="hiddenLoginUser" value="{{  Session::get('userName')}}" />
        <input type="hidden" name="hiddenUserId" id="hiddenUserId" value="{{ $userInfo['id']}}" />
        <input type="hidden" name="hiddenDecidedDateList" id="hiddenDecidedDateList" />
        <div class="form-horizontal">
            <div class="form-group">
                <label class="control-label col-sm-2">氏名：姓</label>
                <div class="col-sm-10">
                  <div class="col-sm-5">
                      <input type="text" class="form-control" name="firstName" id="firstName" value="{{ $userInfo['firstName']}}" disabled />
                  </div>
                  <label class="control-label col-sm-1">名</label>
                  <div class="col-sm-6">
                      
                      <input type="text" class="form-control" name="lastName" id="lastName" value="{{ $userInfo['lastName']}}" disabled />
                  </div>
                  
                </div>
            </div>
            
            <div class="form-group">
                <label class="control-label col-sm-2">企業名</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="companyType" id="companyType" value="{{ $userInfo['companyType']}}" disabled />
                </div>
            </div>
            
            <div class="form-group">
                <label class="control-label col-sm-2">会社名</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="company" id="company" value="{{ $userInfo['company']}}" disabled />
                </div>
            </div>
            
            <div class="form-group">
                <label class="control-label col-sm-2">所属</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="dept" id="dept" value="{{ $userInfo['dept']}}" disabled />
                </div>
            </div>
            
            <div class="form-group">
                <label class="control-label col-sm-2">支店</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="branch" id="branch" value="{{ $userInfo['branch']}}" disabled />
                </div>
            </div>
            
            <div class="form-group">
                <label class="control-label col-sm-2">社員コード</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="code" id="code" value="{{ $userInfo['code']}}" disabled />
                </div>
            </div>
            
            <div class="form-group">
                <label class="control-label col-sm-2">役職</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="position" id="position" value="{{ $userInfo['position']}}" disabled />
                </div>
            </div>
            
            <div class="form-group">
                <label class="control-label col-sm-2">メールアドレス</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="mail" id="mail" value="{{ $userInfo['mail']}}" disabled/>
                </div>
            </div>
            
            <div class="form-group">
                <label class="control-label col-sm-2">招待者</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="inviter" id="inviter" value="{{ $userInfo['inviter']}}" disabled />
                </div>
            </div>
            
            <div class="form-group">
                <label class="control-label col-sm-2">講習の形態</label>
                    @php
                        $classType = $userInfo['classType'];
                    @endphp
                <div class="col-sm-10">
                        @if( $classType == '1')
                            <div class="radio">
                                 <label><input type="radio" name="classType" value="1" checked >オンライン講習（Teams会議）</label>
                            </div>
                            <div class="radio">
                                 <label><input type="radio" name="classType" value="2" >対面講習(大阪本店１４階ICT研修室)</label>
                            </div>
                            <div class="radio">
                                 <label><input type="radio" name="classType" value="3"  >対面講習（その他会場、出張希望）※要相談</label>
                            </div>
                        @elseif ( $classType == '2')
                            <div class="radio">
                                 <label><input type="radio" name="classType" value="1">オンライン講習（Teams会議）</label>
                            </div>
                            <div class="radio">
                                 <label><input type="radio" name="classType" value="2" checked>対面講習(大阪本店１４階ICT研修室)</label>
                            </div>
                            <div class="radio">
                                 <label><input type="radio" name="classType" value="3"  >対面講習（その他会場、出張希望）※要相談</label>
                            </div>
                        @elseif ( $classType == '3')
                            <div class="radio">
                                 <label><input type="radio" name="classType" value="1">オンライン講習（Teams会議）</label>
                            </div>
                            <div class="radio">
                                 <label><input type="radio" name="classType" value="2" >対面講習(大阪本店１４階ICT研修室)</label>
                            </div>
                            <div class="radio">
                                 <label><input type="radio" name="classType" value="3" checked >対面講習（その他会場、出張希望）※要相談</label>
                            </div>
                        @else
                            <div class="radio">
                                 <label><input type="radio" name="classType" value="1" checked>オンライン講習（Teams会議）</label>
                            </div>
                            <div class="radio">
                                 <label><input type="radio" name="classType" value="2" >対面講習(大阪本店１４階ICT研修室)</label>
                            </div>
                            <div class="radio">
                                 <label><input type="radio" name="classType" value="3"  >対面講習（その他会場、出張希望）※要相談</label>
                            </div>
                        @endif

                    
                
                    
                </div>
                
            </div>
            
            <div class="form-group">
                <label class="control-label col-sm-2">希望日</label>
                <input type="hidden" id="hiddenDate" value="{{ $userInfo['desireDate'] }}" />
                <input type="hidden" id="hiddenSelectedDate" value="" />
                
                <div class="col-sm-10" >
                    <p>選択した日付：<span id="selectedDesireDate">{{ $userInfo['desireDate'] }}</span></p>
                    <div class="calendarAndWarining" style="display : flex">
                       
                        <div id="calendar">
                        
                        </div> 
                        <div id="disableMonthWarning">
                            <p>この月の受付は終了したので、</p>
                            <p>来月以降の希望日を選択してください</p>
                        </div>
                        
                    </div>
                    
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2"></label>
                <div class="col-sm-10">
                    <button class="form-control btn-primary" onclick="UpdateUserInfo()">変更</button>
                </div>
        </div>
        </div>
        
    </div>
</div>
<script>
    $(document).ready(function(){
        
    })
</script>
@endsection