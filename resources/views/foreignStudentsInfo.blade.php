@extends('layouts.baselayout')
@section('title', 'CCC - foreign students info')

<!--CSS and JS file-->
@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>
<script>

    $(document).ready(function(){
        var login_user_id = $("#hidLoginID").val();
        var img_src = "../public/image/user_settings.png";
        var url = "foreignStudents/index";
        var content_name = "留学生情報";
        recordAccessHistory(login_user_id,img_src,url,content_name);
    });
</script>
<style>
.student-management-content{  
    width:88%;
    margin:10vh 0% 4% 6%;
}
#student-management-showcase{
    display: flex;
    justify-content:center;
    flex-wrap: wrap;
}
.student-management-btn{
	position: relative;
    width: 200px;
    height:230px;
    margin:28px 15px 28px 15px;
    border-radius:5px;
    min-width:200px;
    max-width:300px;
    cursor: pointer;
    border:solid thin navy;
    box-shadow: 0 12px 10px -6px rgba(0, 0, 0, .3);
    text-align: center;
    transition: all 0.3s ease 0s;
}
.student-management-btn-title{
	display: inline;
	position: absolute;
	top: 50%;
	left: 50%;
	-webkit-transform : translate(-50%,-50%);
	transform : translate(-50%,-50%);
	width:100%;
    color: #424242;
    font:  20px 'arial';
}
.student-management-btn:hover {
    box-shadow: 0 3px 6px 0 rgba(0, 0, 0, 0.25);
    transform: translateY(-0.1875em);
}
</style>
@endsection

@section('content')
@include('layouts.loading')
<div class="student-management-content main-content">

    <input type="hidden" id="hidLoginID" name="hidLoginID" value="{{Session::get('login_user_id')}}"/>
    
    <div id="student-management-showcase">
        <div class="student-management-btn" onclick="window.location='{{ url("/foreignStudents/insert") }}'">
            <div class="student-management-btn-title">留学生情報入力</div>
            
        </div>
        <div class="student-management-btn" onclick="window.location='{{ url("/foreignStudents/show") }}'">
            <div class="student-management-btn-title">留学生情報確認</div>
        </div>
    </div>

</div>
@endsection