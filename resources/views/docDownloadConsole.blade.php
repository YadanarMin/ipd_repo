@extends('layouts.baselayout')
@section('title', 'CCC - Document Download Console')

@section('head')
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>
<script type="text/javascript" src="../public/js/docDownloadConsole.js"></script>
<script type="text/javascript" src="../public/js/select2/select2.min.js"></script>
<link rel="stylesheet" href="../public/css/select2.min.css">
<script src="../public/js/xlsx.full.min.js"></script>
<script>
</script>
<style>
.main-content-header{
    display:flex;
    margin: 10px 0 0 -10px;
}
.main-content-body {
    margin-top:1vh;
	display:flex;
	width:100%;
}
.sidebar{
    width:20%;
    margin:0vh 0 -0.999vh 0;
    border-right: 1px solid #dae1e6;
    /*border:1px solid red;*/
    /*background-color: white;*/
}
.doc-details {
    width:70%;
    margin:0 3vh 0 3vh;
}

#levelDiv{
    margin: 0 0.5vh 2vh 3vh;
}
.levelList
,.levelList li{
	padding:0px;
	margin:0px;
}
 
.levelList li{
	list-style-type:none !important;
	list-style-image:none !important;
	margin: 5px 0px 5px 0px !important;
}
 
.list1 li{
	position:relative;
	padding-left:20px;
}
 
.list1 li:before{
	content:''; 
	display:block; 
	position:absolute; 
	box-shadow: 0 0 2px 2px rgba(255,255,255,0.2) inset;
	top:3px; 
	left:2px; 
	height:0; 
	width:0; 
	border-top: 6px solid transparent;
	border-right: 7px solid transparent;
	border-bottom: 6px solid transparent;
	border-left: 9px solid #aaa;
}
.levelElement{
    display: flex;
}
.levelElement:hover{
    background : lightgray;
}
<!--.levelElement.active{-->
<!--    color : dodgerblue;-->
<!--}-->
.selected{
    color : dodgerblue;
}
.doc-details-header {
	display:flex;
}
.doc-details-body {
}
#tblTemplateVariable thead th{
  /* 縦スクロール時に固定する */
  position: -webkit-sticky;
  position: sticky;
  top: 0;
}
#tblTemplateVariable th{
    background-color:#1a0d00;/*#dff0d8*/
    padding: 8px 0 8px 0;
    color: white;/*#5f5f5f*/
    border: 1px solid #ffff;
    text-align: center;
}
#tblTemplateVariable td{
    background-color:#e6e6e6;/*#dff0d8*/
    padding: 4px;
    border: 1px solid #ffff;
}
.scroll-table {
	display: flex;
	overflow: auto;
	white-space: nowrap;
	height: 364px;
	width: 710px;
	/*margin: 0px auto 0px auto;*/
}
.tab_switch_on{
    background-color:silver;
}
</style>
@endsection

@section('content')
@include('layouts.loading')
<div class="main-content">

    <div class='main-content-header'>
        <a href="{{ url('document/management') }}" style="display:flex;color:#808080;text-decoration:none;">
            <img src="../public/image/arrow_left.png" alt="" style="width:15px;height:15px;">
            <div style="display:flex;">&nbsp
                <img src='../public/image/excel.png' alt='' height='16' width='16' />&nbsp
                <h5 style="margin:1px 0 0 0;">書類出力　　　　　　　</h5>
            </div>
            
        </a>
        
        <div class="btn-group customBtn" role="group" aria-label="..." style="margin-left:32px;">
            <button type="button" class="btn btn-default" id="editTemplateFunc" onclick="switchDisplayExcelDownload()" style="height:40px;">
                <img src='../public/image/pencil_hoso.png' alt='' height='20' width='20' />
                作成・編集
            </button>
            <button type="button" class="btn btn-default" id="displayTemplateFunc" onclick="switchDisplayExcelMakeTemplate()" style="height:40px;">
                <img src='../public/image/1070_dl_h.png' alt='' height='27' width='27' />
                出力
            </button>
        </div>
    </div>

    <div class='main-content-body'>
        
        <div class='sidebar'>
            <h4 style="margin:20px 0 20px 24px;">テンプレート一覧</h4>
            <div id='levelDiv'>
                <ul class='levelList list1'>

                </ul>
            </div>
        </div>  <!--sidebar-->
        
        
        <div class="doc-details" id="documentDetails">
        </div>  <!--doc-details-->
    
    
    </div>


</div>
@endsection