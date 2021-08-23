@extends('layouts.baselayout')
@section('title', 'BIM速習コース入力')

<!--CSS and JS file-->
@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script type="text/javascript" src="/iPD/public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="/iPD/public/js/jquery.multiselect.js"></script>
<script type="text/javascript" src="/iPD/public/js/applicationInsert.js"></script>

<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/redmond/jquery-ui.css" >
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1/i18n/jquery.ui.datepicker-ja.min.js"></script>
<link rel="stylesheet" href="/iPD/public/css/jquery-ui.multidatePicker.css">
<script type="text/javascript" src="/iPD/public/js/jquery-ui.multidatePicker.js"></script>

<style>
.main-content{
	/*background-color: #f2f3f3;*/
	margin: 0 auto;
	width:90%;
	display : center;
} 
.insertView{
    display : flex;
    justify-content: space-around;
    height : 650px;
    border : 1px solid #eee;
}

.activeView{
    border-right : 2px solid #68a4e4;
    width : 20%;
}
.activePage{
    background: #a3a3ff;
    color: white;
}
.page{
    padding: 10px;
}
.formView{
    width : 80%;
    margin-left : 3%;
}
.customBtn{
    width: 30%;
    font-size: 16px;
}
<!--custom UI Datepicker-->
.ui-datepicker .ui-datepicker-calendar .ui-state-highlight a {
    background: yellow none;
    color: white;
}
.myhighlight a{
	background: #743620 none !important;
    color: white !important;
	
	/*color: white !important;*/
}
.today a{
    background: #87b6da none !important;
    color: white !important;
}

#disableMonthWarning {
    margin-left: 30%;
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
    <h3>BIM速習コース入力</h3>
    <hr>
    <div class="insertView">
        <div class="activeView">
            <a href="{{ url('application/insert') }}" ><div class="page activePage" style="margin-top: 50%;">希望日・参加人数の選択</div></a>
            <a href="{{ url('application/insert/page2') }}" ><div class="page">受講者情報入力</div></a>
            <a href="{{ url('application/insert/page3') }}"><div class="page">入力内容の最終確認</div></a>
        </div>
        <div class="formView">
            <!--Number of Person-->
            <div class="form-group" style="padding : 10px">
                <label>※参加人数を入力してください​</label><br>
                @if(Session::has('numOfApplicants'))
                    <input type="number" id="applicants" name="applicants" value="{{session()->get('numOfApplicants') }}" min="1" /><span>&nbsp; 人</span>
                @else
                    <input type="number" id="applicants" name="applicants" min="1" /><span>&nbsp; 人</span>
                @endif
            </div>
            
            <!--Calendar-->
            <div class="form-group" style="padding : 10px">
                <label>※講習会は2日間の日程となります。​</label> <br>
                <label>※希望日を選択してください(2～4日)</label><br>
                <input type="hidden" name="hiddenSelectedDate" id="hiddenSelectedDate" value="{{ Session::get('desireDate') }}"
                @if(empty($selectedDate))
                    <p>選択した日付：<span name="hiddenSelectedDate" id="hiddenSelectedDate1"></span></p>
                @else
                    <p>選択した日付：<span name="hiddenSelectedDate" id="hiddenSelectedDate1">{{ $selectedDate }}</span></p>
                @endif
                
                <div class="calendarAndWarining" style="display :flex">
                    
                    <div id="calendar1">
                        
                    </div>
                    
                    <div id="disableMonthWarning">
                        <p>この月の受付は終了したので、</p>
                        <p>来月以降の希望日を選択してください</p>
                    </div>
                </div>
            </div>
            
            <!--Radio Group-->
            <div class="form-group" style="padding : 10px">
                <label>※講習の形態を選択してください​</label><br>
                @if(Session::has("classType"))
                    @if(Session::get("classType") == "1")
                        <div class="radio">
                          <label><input type="radio" name="classType" value="1" checked>オンライン講習（Teams会議）</label>
                        </div>
                        <div class="radio">
                          <label><input type="radio" name="classType" value="2" >対面講習(大阪本店１４階ICT研修室)</label>
                        </div>
                        <div class="radio">
                          <label><input type="radio" name="classType" value="3" >対面講習（その他会場、出張希望）※要相談</label>
                        </div>
                    @elseif(Session::get("classType") == "2")
                        <div class="radio">
                          <label><input type="radio" name="classType" value="1" >オンライン講習（Teams会議）</label>
                        </div>
                        <div class="radio">
                          <label><input type="radio" name="classType" value="2" checked >対面講習(大阪本店１４階ICT研修室)</label>
                        </div>
                        <div class="radio">
                          <label><input type="radio" name="classType" value="3"  >対面講習（その他会場、出張希望）※要相談</label>
                        </div>
                    @elseif(Session::get("classType") == "3")
                        <div class="radio">
                          <label><input type="radio" name="classType" value="1" >オンライン講習（Teams会議）</label>
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
            
            <!--TO Page2-->
            <div class="form-group"　style="padding : 10px">
                <a  class="btn btn-info customBtn" onclick="SaveSessionAndGoToPage2()"　>次へ</a>
            </div>
        </div>
    </div>
</div>
<script>
  
</script>
@endsection