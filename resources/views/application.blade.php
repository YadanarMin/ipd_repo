@extends('layouts.baselayout')
@section('title', '各種申し込み')

<!--CSS and JS file-->
@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>

<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/redmond/jquery-ui.css" >
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/i18n/jquery.ui.datepicker-ja.min.js"></script>
<style>
.main-content{
	/*background-color: #f2f3f3;*/
	margin: 0 auto;
	width:90%;
	display : center;
} 
#application-view{
    
    height: 300px;
    display: flex;
    justify-content : center;
    margin-top: 5%;
}
#application-bim-insert{
    border: 1px solid #eee;
    width: 30%;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 14px 28px rgb(0 0 0 / 25%), 0 10px 10px rgb(0 0 0 / 22%);
}
</style>
<script>
    $(document).ready(function(){
        var login_user_id = $("#hidLoginID").val();
        var img_src = "../public/image/JPG/ローディング中のアイコン1.jpeg";
        var url = "application/index";
        var content_name = "各種申し込み";
        recordAccessHistory(login_user_id,img_src,url,content_name);
    });
</script>
@endsection
@section('content')
@include('layouts.loading')
<div class="main-content">
    <input type="hidden" id="hidLoginID" name="hidLoginID" value="{{Session::get('login_user_id')}}"/>
    
    <h3>各種申し込み</h3>
    <hr>
    <div id="application-view">
        <div id="application-bim-insert">
            <h4>BIM速習コース</h4>
            <div style=" margin-top: 15%;">
                <button class="btn btn-primary form-control" onclick="window.location='{{ url("/application/insert") }}'" style="margin-bottom: 30px; font-size :16px">
                    新規入力
                </button>
                <button class="btn btn-primary form-control" onclick="window.location='{{ url("/application/edit") }}'" style="margin-bottom: 30px; font-size :16px">
                    内容確認・変更
                </button>
            </div>
        </div>
        <div id="application-bim-request">
            
        </div>
    </div>
</div>
@endsection