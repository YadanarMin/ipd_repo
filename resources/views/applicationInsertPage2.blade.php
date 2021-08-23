@extends('layouts.baselayout')
@section('title', 'BIM速習コース入力')

<!--CSS and JS file-->
@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script type="text/javascript" src="/iPD/public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="/iPD/public/js/jquery.multiselect.js"></script>
<script type="text/javascript" src="/iPD/public/js/applicationInsert2.js"></script>

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
.chainLink {
    margin-top: 30px;
    position: absolute;
    margin-left: -20px;
}
.customForm{
    width: 90%;
    border: 1px solid #eee;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 10px;
    box-shadow: 0 14px 28px rgb(249 246 246 / 25%), 0 10px 10px rgb(0 0 0 / 22%);
}
.customBtn{
    width: 30%;
    font-size: 16px;
}
.btnGroup{
    display : flex;
    margin-bottom : 3%;
}
.Btn {
    display: flex;
    position: relative;
    width: 100%;
    padding-right: 35px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background: #fcf8e3;
    font-size: 1.8rem;
    font-weight: bold;
    color: #777!important;
    text-align: center;
    text-decoration: none !important;
    line-height: 1.2;
    justify-content: center;
    align-items: center;
    transition: all .3s ease;
}
.Btn--type01 {
    margin-top:10px;
    width: 208px;
    height: 33px;
    padding: 0 40px 0 10px;
    font-weight: normal;
}


.Btn::before {
    border-radius: 50%;
    background: #777;
}
.Btn::after {
    width: 0;
    height: 0;
    border-style: solid;
    border-color: transparent transparent transparent #f1f3ee;
}
.Btn::before, .Btn::after {
    display: block;
    position: absolute;
    top: 50%;
    left: 100%;
    content: '';
}
.Btn--type01::before {
    width: 24px;
    height: 24px;
    margin: -12px 0 0 -38px;
}
.Btn--type01::after {
    margin: -6px 0 0 -29px;
    border-width: 6px 0 6px 9px;
}

</style>
@endsection
@section('content')
@include('layouts.loading')
<div class="main-content">
    <input type="hidden" id="hidNumOfApplicants" name="hidNumOfApplicants" value="{{Session::get('numOfApplicants')}}"/>
    <input type="hidden" id="hidUserInfoList" name="hidUserInfoList" value="{{json_encode(Session::get('userInfoList'))}}"/>
    
    <h3>BIM速習コース入力</h3>
    <hr>
    <div class="insertView">
        <div class="activeView">
            <a href="{{ url('application/insert') }}" ><div class="page" style="margin-top: 50%;">希望日・参加人数の選択</div></a>
            <a href="{{ url('application/insert/page2') }}" ><div class="page activePage">受講者情報入力</div></a>
            <a href="{{ url('application/insert/page3') }}" ><div class="page">入力内容の最終確認</div></a>
        </div>
        <div class="formView">
            <label style="padding: 10px;" id="insertLabel">受講者情報を入力してください​。</label>
            
            <!--Error Message-->
            
            <!--Error Message-->
            
            @if(Session::has('numOfApplicants'))
                @php
                  $num = session()->get('numOfApplicants');
                @endphp
                <input type="hidden" name="applicants" id="applicants" value="{{session()->get('numOfApplicants') }}" />
                
                    @if(Session::has('userInfoList'))
                        @foreach(Session::get('userInfoList') as $userInfo)
                        <div class="customForm">
                             <div class="row">
                                <span class="badge" style="float: right;margin-right: 10px;margin-bottom: -18px;background: black;">{{$userInfo['id']}}</span>
                                <input type="hidden" name="id" id="{{$userInfo['id']}}" value="{{$userInfo['id']}}" />
                                <div class="form-group col-xs-10 col-sm-6 col-md-6 col-lg-6　ui-widget">
                                    <label>氏名：姓</label>
                                    <input type="text" class="form-control"  name="username" id="username{{$userInfo['id']}}" value="{{$userInfo['name']}}" required>
                                </div>
                                <div class="form-group col-xs-10 col-sm-6 col-md-6 col-lg-6　ui-widget">
                                    <label>名</label>
                                    <input type="text" class="form-control"  name="lastname" id="lastname{{$userInfo['id']}}" value="{{$userInfo['lastname']}}" required>
                                </div>
                                <div class="form-group col-xs-10 col-sm-12 col-md-12 col-lg-12">
                                    <label>メールアドレス</label>
                                    <input type="email" class="form-control"  name="email" id="email{{$userInfo['id']}}" value="{{$userInfo['email']}}" required>
                                    <botton type="button" id="automaticInput" onclick="getUserAttribute('{{$userInfo['id']}}')" class="btn btn-default" style="margin-top: 10px;">
                                        メールアドレスから受講者情報を取得<span class="glyphicon glyphicon-play-circle"></span>
                                    </botton>
                                </div>
                                
                                <div class="form-group col-xs-10 col-sm-12 col-md-12 col-lg-12">
                                    <label>企業種別</label>
                                    <select name="companyTypeSelect" class="companyTypeSelect" id="companyType{{$userInfo['id']}}" required disabled>
                                    </select>
                                    <input type="hidden" name="hidCompanyTypeID" id="hidCompanyTypeID{{$userInfo['id']}}" value="{{$userInfo['companyTypeId']}}"/>
                                </div>

                                <div class="form-group col-xs-10 col-sm-12 col-md-12 col-lg-12">
                                    <label>会社名 ※企業種別が「大林組(派遣)」の場合は、派遣元会社名を入力してください。</label>
                                    <input type="text" class="form-control" name="companyName" id="companyName{{$userInfo['id']}}" value="{{$userInfo['companyName']}}" disabled>
                                    <input type="hidden" name="hidCompanyID" id="hidCompanyID{{$userInfo['id']}}" value="{{$userInfo['companyId']}}"/>
                                </div>

                                <div class="form-group col-xs-10 col-sm-6 col-md-6 col-lg-6　ui-widget">
                                    <label>支店名</label>
                                    <select class="form-control" name="branch" id="branch{{$userInfo['id']}}" disabled></select>
                                    <input type="hidden" name="hidBranchID" id="hidBranchID{{$userInfo['id']}}" value="{{$userInfo['branchId']}}"/>
                                    <!--<input type="text" class="form-control"  name="branch" id="branch{{$userInfo['id']}}" value="{{$userInfo['branch']}}">-->
                                </div>

                                <div class="form-group col-xs-10 col-sm-6 col-md-6 col-lg-6　ui-widget">
                                    <label>組織名</label>
                                    <input type="text" class="form-control" name="dept" id="dept{{$userInfo['id']}}" value="{{$userInfo['dept']}}" disabled>
                                    <input type="hidden" name="hidDeptID" id="hidDeptID{{$userInfo['id']}}" value="{{$userInfo['deptId']}}"/>
                                </div>

                                <div class="form-group col-xs-10 col-sm-12 col-md-12 col-lg-12">
                                    <label>社員コード(Uから入力してください)</label>
                                    <input type="hidden" name="hidContractType" id="hidContractType{{$userInfo['id']}}" value="{{$userInfo['contractType']}}"/>
                                    <input type="text" class="form-control"  name="code" id="code{{$userInfo['id']}}" value="{{$userInfo['code']}}" disabled>
                                </div>

                                <div class="form-group col-xs-10 col-sm-12 col-md-12 col-lg-12">
                                    <label>役職</label>
                                    <input type="text" class="form-control"  name="position" id="position{{$userInfo['id']}}" value="{{$userInfo['position']}}" disabled>
                                </div>
                                
                                <input type="hidden" id="isStudyAbroad{{$userInfo['id']}}" value="{{ $userInfo['isStudyAbroad'] }}">
                                <input type="hidden" id="isC3User{{$userInfo['id']}}" value="{{ $userInfo['isC3User'] }}" >
                                <input type="hidden" id="isAdditionalPost{{$userInfo['id']}}" value="{{ $userInfo['isAdditionalPost'] }}" >
                                
                            </div>
                        </div>
                        @endforeach
                    @else
                        @for($i =0; $i< $num ; $i++)
                            <div class="customForm">
                                 <div class="row">
                                    <span class="badge" style="float: right;margin-right: 10px;margin-bottom: -18px;background: black;">{{$i+1}}</span>
                                    <input type="hidden" name="id" id="{{$i+1}}" value="{{$i+1}}" />
                                    <div class="form-group col-xs-10 col-sm-6 col-md-6 col-lg-6　ui-widget">
                                        <label>氏名：姓</label>
                                        <input type="text" class="form-control"  name="username" id="username{{$i+1}}" required>
                                    </div>
                                    <div class="form-group col-xs-10 col-sm-6 col-md-6 col-lg-6　ui-widget">
                                        <label>名</label>
                                        <input type="text" class="form-control"  name="lastname" id="lastname{{$i+1}}" required>
                                    </div>
                                    <div class="form-group col-xs-10 col-sm-12 col-md-12 col-lg-12">
                                        <label>メールアドレス</label>
                                        <input type="email" class="form-control"  name="email" id="email{{$i+1}}" required>
                                        <botton type="button" id="automaticInput" onclick="getUserAttribute('{{$i+1}}')" class="btn btn-default" style="margin-top: 10px;">
                                            メールアドレスから受講者情報を取得<span class="glyphicon glyphicon-play-circle"></span>
                                        </botton>
                                    </div>
                                    
                                    <div class="form-group col-xs-10 col-sm-12 col-md-12 col-lg-12">
                                        <label>企業種別</label>
                                        <select name="companyTypeSelect" class="companyTypeSelect" id="companyType{{$i+1}}" required disabled></select>
                                        <input type="hidden" name="hidCompanyTypeID" id="hidCompanyTypeID{{$i+1}}"/>
                                    </div>

                                    <div class="form-group col-xs-10 col-sm-12 col-md-12 col-lg-12">
                                        <label>会社名 ※企業種別が「大林組(派遣)」の場合は、派遣元会社名を入力してください。</label>
                                        <input type="text" class="form-control" name="companyName" id="companyName{{$i+1}}" disabled>
                                        <input type="hidden" name="hidCompanyID" id="hidCompanyID{{$i+1}}"/>
                                    </div>

                                    <div class="form-group col-xs-10 col-sm-6 col-md-6 col-lg-6　ui-widget">
                                        <label>支店名</label>
                                        <select class="form-control" name="branch" id="branch{{$i+1}}" disabled></select>
                                        <input type="hidden" name="hidBranchID" id="hidBranchID{{$i+1}}"/>
                                        <!--<input type="text" class="form-control"  name="branch" id="branch{{$i+1}}">-->
                                    </div>
                                    
                                    <div class="form-group col-xs-10 col-sm-6 col-md-6 col-lg-6　ui-widget">
                                        <label>組織名</label>
                                        <input type="text" class="form-control" name="dept" id="dept{{$i+1}}" disabled>
                                        <input type="hidden" name="hidDeptID" id="hidDeptID{{$i+1}}"/>
                                    </div>

                                    <div class="form-group col-xs-10 col-sm-12 col-md-12 col-lg-12">
                                        <label>社員コード(Uから入力してください)</label>
                                        <input type="hidden" name="hidContractType" id="hidContractType{{$i+1}}" value="{{$i+1}}"/>
                                        <input type="text" class="form-control"  name="code" id="code{{$i+1}}" disabled>
                                    </div>
                                    
                                    <div class="form-group col-xs-10 col-sm-12 col-md-12 col-lg-12">
                                        <label>役職</label>
                                        <input type="text" class="form-control"  name="position" id="position{{$i+1}}" disabled>
                                    </div>
                                    
                                    <input type="hidden" id="isStudyAbroad{{$i+1}}" >
                                    <input type="hidden" id="isC3User{{$i+1}}" >
                                    <input type="hidden" id="isAdditionalPost{{$i+1}}" >
                                    <input type="hidden" id="isExistingUser{{$i+1}}" >
                                    
                                </div>
                            </div>
                        @endfor
                    @endif
            @endif
            
            
            <div class="btnGroup">
                <a href="{{ url('application/insert') }}" onclick=""  class="btn btn-info" style="width :30%;"　>
                    
                    前へ
                   
                </a>
                
                <a  onclick="SaveSessionAndGoToPage3()"  class="btn btn-info" style="width :30%; margin-left:10px"　>
                    
                    次へ
                   
                </a>
                
                
            </div>
        </div>
    </div>
</div>
<script>
<!--$(document).ready(function(){-->
<!--     $('.chainLink').on('click',function(){-->
<!--        var that = this;-->
<!--        var prevText = $(that).parent().prev().find('input').val();-->
<!--        var nextText = $(that).nextAll().last().val();-->
        
<!--        if(prevText){-->
<!--            $(that).nextAll().last().val('');-->
<!--            $(that).nextAll().last().val(prevText);-->
<!--        }else{-->
<!--            $(that).parent().prev().find('input').val('');-->
<!--            $(that).parent().prev().find('input').val(nextText);-->
<!--        }-->
    
<!--    });-->
<!--})-->
</script>
@endsection