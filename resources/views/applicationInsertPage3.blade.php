@extends('layouts.baselayout')
@section('title', 'BIM速習コース入力')

<!--CSS and JS file-->
@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script type="text/javascript" src="/iPD/public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="/iPD/public/js/jquery.multiselect.js"></script>
<script type="text/javascript" src="/iPD/public/js/applicationInsert3.js"></script>

<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/redmond/jquery-ui.css" >
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1/i18n/jquery.ui.datepicker-ja.min.js"></script>
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
#confirmDiv{
    margin-top : 1%;
    margin-bottom : 2%; 
}
#confirmInfo{
    border: 1px solid #eee;
    padding: 10px;
    width: 70%;
}
#confirmTable{
    border: 1px solid #eee;
    padding : 8px;
    margin-bottom : 5%;
}
table tbody tr:nth-child(even) {
    background: #dfe7ec;
    font-size : 14px;
}
table tbody tr:nth-child(odd) {
    background: #FFF;
    font-size : 14px;
}
.btnGroup{
    display : flex;
    margin-bottom : 3%;
}


</style>
@endsection
@section('content')
@include('layouts.loading')
<div class="main-content">
    <h3>BIM速習コース入力</h3>
    <input type="hidden" name="hidUserInfoList" id="hidUserInfoList" value="{{json_encode(Session::get('userInfoList'))}}" />
    <hr>
    <div class="insertView">
        <div class="activeView">
            <a href="{{ url('application/insert') }}"><div class="page" style="margin-top: 50%;">希望日・参加人数の選択</div></a>
            <a href="{{ url('application/insert/page2') }}"><div class="page">受講者情報入力</div></a>
            <a href="{{ url('application/insert/page3') }}"><div class="page activePage">入力内容の最終確認</div></a>
        </div>
        <div class="formView">
            <div id="confirmDiv">
                <label>入力内容をご確認ください​。</label>
                <div id="confirmInfo">
                    <label>希望日</label>
                    <input type="hidden" name="desireDate" id="desireDate" value="{{ Session::get('desireDate')}}" />
                    <input type="hidden" name="inviter" id="inviter" value="{{ Session::get('userName')}}" />
                    <input type="hidden" name="classType" id="classType" value="{{ Session::get('classType')}}" />
                    @if(Session::has("desireDate"))
                        @php
                            $dates = session()->get("desireDate");
                            $dateList = explode(",", $dates);
                        @endphp
                        <ul>
                            @for($i=0; $i<count($dateList); $i++)
                                 @php 
                                    $dateStr = $dateList[$i];
                                    $index = strpos($dateStr,"(");
                                    $d = substr($dateStr, 0,$index);
                                    $amOrpm = substr($dateStr, $index);
                                    $date = strtotime($d);
                                    
                                @endphp
                                <li> {{ date("Y年m月d日(D)" , $date) }}{{ $amOrpm}} </li>
                            @endfor
                        </ul>
                    @else
                        <ul>
                            <li></li>
                        </ul>
                    @endif
                    <label>参加人数</label>
                    <ul>
                        <li>{{ Session::get('numOfApplicants') }}人</li>
                    </ul>
                    
                    <label>招待者</label>
                    <ul>
                        <li>{{ Session::get('userName') }}</li>
                    </ul>
                </div>
            </div>
            
            
            <div id="confirmTable">
                <label>受講者情報​</label><br>
                <table class="table table-bordered">
                    <thead>
                        <tr class="info">
                            <th>姓</th>
                            <th>名</th>
                            <th>メールアドレス</th>
                            <th>会社種別</th>
                            <th>会社名</th>
                            <th>所属</th>
                            <th>支店</th>
                            <th>社員コード</th>
                            <th>役職</th>
                        </tr>
                    </thead>
                    <tbody id="userInfoTableBody">
                        @if(Session::has('userInfoList'))
                            @foreach(Session::get('userInfoList') as $userInfo)
                            <tr>
                                <td id="{{$userInfo['id']}}" style="display :none">{{$userInfo['id']}}</td>
                                <td id="username{{$userInfo['id']}}">{{$userInfo['name']}}</td>
                                <td id="lastname{{$userInfo['id']}}">{{$userInfo['lastname']}}</td>
                                <td id="email{{$userInfo['id']}}">{{$userInfo['email']}}</td>
                                <td id="companyType{{$userInfo['id']}}">{{$userInfo['companyType']}}</td>
                                <td id="companyTypeId{{$userInfo['id']}}" style="display :none">{{$userInfo['companyTypeId']}}</td>
                                <td id="companyName{{$userInfo['id']}}">{{$userInfo['companyName']}}</td>
                                <td id="companyId{{$userInfo['id']}}" style="display :none">{{$userInfo['companyId']}}</td>
                                <td id="dept{{$userInfo['id']}}">{{$userInfo['dept']}}</td>
                                <td id="deptId{{$userInfo['id']}}" style="display :none">{{$userInfo['deptId']}}</td>
                                <td id="branch{{$userInfo['id']}}">{{$userInfo['branch']}}</td>
                                <td id="branchId{{$userInfo['id']}}" style="display :none">{{$userInfo['branchId']}}</td>
                                <td id="code{{$userInfo['id']}}">{{$userInfo['code']}}</td>
                                <td id="position{{$userInfo['id']}}">{{$userInfo['position']}}</td>
                                <td id="isStudyAbroad{{$userInfo['id']}}" style="display :none">{{$userInfo['isStudyAbroad']}}</td>
                                <td id="isC3User{{$userInfo['id']}}" style="display :none">{{$userInfo['isC3User']}}</td>
                                <td id="isAdditionalPost{{$userInfo['id']}}" style="display :none">{{$userInfo['isAdditionalPost']}}</td>
                                <td id="hidContractType{{$userInfo['id']}}" style="display :none">{{$userInfo['contractType']}}</td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="btnGroup">
                <a href="{{ url('application/insert/page2') }}" onclick=""  class="btn btn-info" style="width :30%;"　>
                    
                    前へ
                   
                </a>
                
                <a  onclick="SaveAppliedUserInfo()"  class="btn btn-info" style="width :30%; margin-left:10px"　>
                    
                    決定（希望日を提出する）
                   
                </a>
                
                
            </div>
            
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        
    });
    
    
    
</script>
@endsection