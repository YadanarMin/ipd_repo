@extends('layouts.baselayout')
@section('title', 'CCC - Top')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script type="text/javascript" src="../public/js/home.js"></script>
<script type="text/javascript" src="../public/js/bimcourse_confirm_message.js"></script>
<script type="text/javascript" src="../public/js/library/jquery.vibr.js"></script>


<style>
#ccc-util-container-header{
    font-size:1.8rem;
    line-height:2rem;
    padding: 1.9rem 9rem 0rem 9rem;
    font-weight:700;
}
.container-margin{
    margin-top:2rem;
    margin-left:3%;
    width:64%;
    min-width:1000px;
    box-shadow: 0px 0px 4px 0px #232f3e;
    display: block;
}
#cccgnav-recently-used{
    padding:1.9rem 9rem;
    font-weight:400;
}
#cccgnav{
    padding: 0rem 9rem 1.9rem 9rem;
    font-weight:400;
}
.cccgnav-title{
    display:flex;
}
.cccgnav-field{
    display:flex;
    flex-wrap:wrap;
    width:850px;
}
.cccgnav-service{
    display:flex;
    padding:1rem 0 0 5rem;
}
.cccgnav-content{
    margin:0 0 1rem 7.6rem;
    color:#000000;
    cursor: pointer;
    font-size: 12pt;
}
.cccgnav-content:hover {
	color:#0073bb;
	text-decoration: underline;
}
.cccgnav-service-area{
    width:280px;
}
ul {
  list-style: none;
}

.right-click-content {
    display: none;
    z-index:1000;
    width:100px;
    height:32px;
    padding:5px;
    position: absolute;
    background-color:#eee;
    border: 1px solid #ddd;
    border-radius:5px;
}
.alert{
 margin:20px;
 width:100%;
 position:relative;
 box-shadow: 0px 0px 2px 0px #ddd;
 animation: shake 3s infinite ease-in-out 2s;

}
.alert-info:after{
    bottom: 100%;
    left: 7%;
    border: solid transparent;
    content: " ";
    height: 0;
    width: 0;
    position: absolute;
    pointer-events: none;
    border-bottom-color: #d9edf7;
    border-width: 13px;
    margin-left: -10px;
}

.alert-success:after{
    bottom: 100%;
    left: 7%;
    border: solid transparent;
    content: " ";
    height: 0;
    width: 0;
    position: absolute;
    pointer-events: none;
    border-bottom-color: #dff0d8;
    border-width: 13px;
    margin-left: -10px;
}
.cus-link-color{
    color:darkblue !important;
}
.alert:hover {
    animation-play-state: paused;
}
@keyframes shake {
  0% { transform: rotate(0deg); }
  10% { transform:rotate(-7deg); }
  20% { transform: rotate(1deg); }
  30% { transform: rotate(0deg); }
  90% { transform: rotate(0deg); }
  100% { transform: rotate(7deg); }
}
</style>
<script>

</script>

@endsection

@section('content')
<main>
    
    <input type="hidden" id="hidLoginID" name="hidLoginID" value="{{Session::get('login_user_id')}}"/>
    <input type="hidden" id="hidAuthorityID" name="hidAuthorityID" value="{{Session::get('authority_id')}}"/>
    <input type="hidden" id="hidAuthorityData" name="hidAuthorityData" value="{{json_encode($authority_data)}}"/>
    <input type="hidden" id="hidAllAuthorityData" name="hidAllAuthorityData" value="{{json_encode($ccc_all_authority_data)}}"/>
    <input type="hidden" id="hiddenLoginUser" name="hiddenLoginUser" value="{{ Session::get('userName')}}" />
    <div style="display:flex;">
        <div class="noti-area" style="width:15%;">
        @foreach($chief_admin_noti as $chief_noti)    
        <div class="alert alert-info">
              <strong class="cus-link-color">{{$chief_noti["name"]}}さん</strong>が<strong>承認</strong>お待ちしております。
              <a href="/iPD/login/approve/step1/{{{$chief_noti["personal_id"]}}}" class="alert-link cus-link-color">承認します</a>か？
        </div>
        @endforeach
        @foreach($ccc_master_noti as $master_noti)    
        <div class="alert alert-success">
              <strong class="cus-link-color">{{$master_noti["name"]}}さん</strong>が<strong>承認</strong>お待ちしております。</br>
              <a href="/iPD/login/approve/step2/{{{$master_noti["personal_id"]}}}/{{{$master_noti["chief_admin_id"]}}}" class="alert-link cus-link-color">承認します</a>か？
        </div>
        @endforeach
        </div>
        <div class="container-margin">
            <div id="ccc-util-container-header">
                <h3 style="font-weight:700;">CCCのサービス</h3>
            </div>
            <div id="cccgnav-recently-used">
                
                <div class="cccgnav-title">
                    <a style="margin-top:5px;" href='javascript:void(0)'>
                        <img class='appIconBig' src='../public/image/down_arrow2.png' alt='' height='11' width='13' />
                    </a>&nbsp
                    <h4 style="font-weight:700;">最近アクセスしたサービス</h4>
                </div>
                
                <div class="cccgnav-field" id="recently-used-field">
                </div>
                
            </div>
            
        	<div id="DivTab" class="centering" style="margin-bottom:30px;padding:0 9rem;">
        		<ul class="nav nav-tabs" id="tab_header" style="width:100%;"></ul>
        		<div class="tab-content" id="tab_body"></div>
        	</div>
            
            <div id="cccgnav">
                
                <div class="cccgnav-title">
                    <a style="margin-top:5px;" href='javascript:void(0)'>
                        <img class='appIconBig' src='../public/image/down_arrow2.png' alt='' height='11' width='13' />
                    </a>&nbsp
                    <h4 style="font-weight:700;">利用可能なサービス</h4>
                </div>
                
                <div class="cccgnav-field" id="contents-field">
                </div>
                
            </div>
        </div> 
    </div>
    
    
    <div class="right-click-content">
        <ul style="padding-left:15px;">
            <li><a href="#" onclick="DeleteContent()">削除</a></li>
        </ul>
        <input type="hidden" id="hidDeleteContentsID" name="hidDeleteContentsID" value="{{Session::get('login_user_id')}}"/>
    </div>
    
</main>
@endsection