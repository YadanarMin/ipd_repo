@extends('layouts.baselayout')
@section('title', 'CCC - Document Management')

@section('head')
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>
<script src="../public/js/xlsx.full.min.js"></script>
<script>
</script>
<style>
.document-mng-content{  
    width:88%;
    margin:10vh 0% 4% 6%;
}
#document-mng-showcase{
    display: flex;
    justify-content:center;
    flex-wrap: wrap;
}
.document-mng-template{
	position: relative;
    width:200px;
    height:150px;
    margin:28px 15px 28px 15px;
    border-radius:5px;
    min-width:200px;
    max-width:300px;
    cursor: pointer;
    <!--border:solid thin navy;-->
    <!--background-color:#ECECEC;-->
    box-shadow: 0 12px 10px -6px rgba(0, 0, 0, .3);
    text-align: center;
    transition: all 0.3s ease 0s;
}
.template-excel{
    border:solid 2px #107C41;
    /*background-color:green;*/
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
	/*background-image:url(https://obayashi-ccc.net/iPD/public/image/excel.png);*/
}
.template-word{
    border:solid 2px #185ABD;
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
	/*background-image:url(https://obayashi-ccc.net/iPD/public/image/word.png);*/
}
.document-mng-template-title{
	display: inline;
	position: absolute;
	top: 50%;
	left: 50%;
	-webkit-transform : translate(-50%,-50%);
	transform : translate(-50%,-50%);
	width:100%;
    color: #424242;/*#f5f5f5;*/
    font: 400 17px 'arial';
}
.document-mng-template:hover {
    /*background: #337ab7;*/
    /*color: white;*/
    box-shadow: 0 3px 6px 0 rgba(0, 0, 0, 0.25);
    transform: translateY(-0.1875em);
}
</style>
@endsection

@section('content')
@include('layouts.loading')
<div class="document-mng-content main-content">

    <!--<h3>書類出力</h3>-->
    
    <div id="document-mng-showcase">
        <div class="document-mng-template template-excel" onclick="window.location='{{ url("document/templateConsole") }}'">
            <div class="document-mng-template-title" style="margin-top:-10px;">テンプレート</div>
            <div class="document-mng-template-title" style="margin-top:20px;">作成・編集</div>
        </div>
        <div class="document-mng-template template-excel" onclick="window.location='{{ url("document/downloadConsole") }}'">
            <div class="document-mng-template-title">書類出力</div>
        </div>
    </div>
    <div id="document-mng-showcase">
        <div class="document-mng-template template-word" onclick="window.location='{{ url("document/templateConsoleWord") }}'">
            <div class="document-mng-template-title" style="margin-top:-10px;">テンプレート</div>
            <div class="document-mng-template-title" style="margin-top:20px;">作成・編集</div>
        </div>
        <div class="document-mng-template template-word" onclick="window.location='{{ url("document/downloadConsoleWord") }}'">
            <div class="document-mng-template-title">書類出力</div>
        </div>
    </div>

</div>
@endsection