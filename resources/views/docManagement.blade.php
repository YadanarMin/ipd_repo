@extends('layouts.baselayout')
@section('title', 'CCC - Document Management')

@section('head')
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>
<script src="../public/js/xlsx.full.min.js"></script>
<script>
    $(document).ready(function(){
        
        var login_user_id = $("#hidLoginID").val();
        var img_src = "../public/image/JPG/会員証のアイコン素材.jpeg";
        var url = "document/management";
        var content_name = "書類出力";
        recordAccessHistory(login_user_id,img_src,url,content_name);
    });
</script>
<style>
.document-mng-content{  
    width:88%;
    margin:5vh 0% 0% 6%;
}
#showcase-excel{
    display: flex;
    justify-content:center;
    flex-wrap: wrap;
    border-radius:5px;
    
    position: relative;
    margin: 2em auto;
    padding: 1.2em;
    color: #555555;
    background-color: #fff;
    border: 2px solid #107C41;
    width: 58%;
}
#showcase-word{
    display: flex;
    justify-content:center;
    flex-wrap: wrap;
    border-radius:5px;
    
    position: relative;
    margin: 2em auto;
    padding: 1.2em;
    color: #555555;
    background-color: #fff;
    border: 2px solid #185ABD;
    width: 58%;
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
    border:solid 2px #5555;
    /*background-color:green;*/
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
	/*background-image:url(https://obayashi-ccc.net/iPD/public/image/excel.png);*/
}
.template-word{
    border:solid 2px #5555;
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
.title-box11 {
 position: absolute;
 padding: 0 .5em;
 left: 20px;
 top: -15px;
 font-weight: bold;
 background-color: #fff; /* タイトル背景色 */
 color: #555555; /* タイトル文字色 */
}
.icon_make_template{
    margin-top:-59px;
    margin-left:-80px;
    font-size:11px;
}
.icon_dl_template{
    margin-top:-56px;
    margin-left:-76px;
    font-size:11px;
}
</style>
@endsection

@section('content')
@include('layouts.loading')
<div class="document-mng-content main-content">

    <input type="hidden" id="hidLoginID" name="hidLoginID" value="{{Session::get('login_user_id')}}"/>
    <!--<h3>書類出力</h3>-->
    
    <div id="showcase-excel">
        <span class="title-box11">
            <img src='../public/image/excel.png' alt='' height='20' width='20' />
        </span>
        <div class="document-mng-template template-excel" onclick="window.location='{{ url("document/templateConsole") }}'">
            <div class="document-mng-template-title icon_make_template">
                <img src='../public/image/pencil_hoso.png' alt='' height='20' width='20' />
            </div>
            <div class="document-mng-template-title" style="margin-top:-10px;">テンプレート</div>
            <div class="document-mng-template-title" style="margin-top:20px;">作成・編集</div>
        </div>
        <div class="document-mng-template template-excel" onclick="window.location='{{ url("document/downloadConsole") }}'">
            <div class="document-mng-template-title icon_dl_template">
                <img src='../public/image/1070_dl_h.png' alt='' height='27' width='27' />
            </div>
            <div class="document-mng-template-title">書類出力</div>
        </div>
    </div>

	<div id="DivTab" class="centering" style="margin-bottom:30px;">
		<ul class="nav nav-tabs" id="tab_header" style="width:100%;"></ul>
		<div class="tab-content" id="tab_body"></div>
	</div>

    <div id="showcase-word">
        <span class="title-box11">
            <img src='../public/image/word.png' alt='' height='20' width='20' />
        </span>
        <div class="document-mng-template template-word" onclick="window.location='{{ url("document/templateConsoleWord") }}'">
            <div class="document-mng-template-title icon_make_template">
                <img src='../public/image/pencil_hoso.png' alt='' height='20' width='20' />
            </div>
            <div class="document-mng-template-title" style="margin-top:-10px;">テンプレート</div>
            <div class="document-mng-template-title" style="margin-top:20px;">作成・編集</div>
        </div>
        <div class="document-mng-template template-word" onclick="window.location='{{ url("document/downloadConsoleWord") }}'">
            <div class="document-mng-template-title icon_dl_template">
                <img src='../public/image/1070_dl_h.png' alt='' height='27' width='27' />
            </div>
            <div class="document-mng-template-title">書類出力</div>
        </div>
    </div>

</div>
@endsection