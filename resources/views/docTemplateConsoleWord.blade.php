@extends('layouts.baselayout')
@section('title', 'CCC - Document Template Console Word')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>
<script type="text/javascript" src="../public/js/docTemplateConsoleWord.js"></script>
<script type="text/javascript" src="../public/js/select2/select2.min.js"></script>
<link rel="stylesheet" href="../public/css/select2.min.css">
<script>
</script>
<style>

.main-content-header{
    display:flex;
    margin: 10px 0 0 -10px;
}
.main-content-body {
    margin-top:1vh;
	/*display:flex;*/
	/*width:100%;*/
}
.sidebar{
    /*width:20%;*/
    /*margin:1vh 0 -0.999vh 0;*/
    /*border-right: 1px solid #dae1e6;*/
    
    /*border:1px solid red;*/
    /*background-color: white;*/
}
.doc-details {
    margin-bottom:3vh;
    /*width:80%;
    margin:0 10vh 0 3vh;*/
}

#levelDiv{
    margin: 0 0vh 2vh 0vh;
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

/*.levelElement:active{
    color : dodgerblue;
}*/

.doc-details-header {
	display:flex;
}

.full-wd {
    width:100%;
    margin-bottom:10px;
}
textarea {
    min-height: 150px;
    display: block;
}
#template-type li{
    list-style:none;
    padding:3px;
}

.custom-label{
    background-color:#f5dd67;
    padding:6px;
    color:#1a0d00;
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
.custom-btn{
    padding:4px;
    width:90%;
    margin:10px 0 10px 5%!important;
}

.outer-border-design{
    padding-top:10px;
    border:1px solid #f2f2f2;
    border-radius:5px;
    height:auto;
    min-height:50vh;
    margin-bottom:20px;
}
.custom-ul-label{
    margin:15px 0 5px -30px;
}
#btnCreateNewTemplate,#btnDeleteTemplate{
    /*border:1px solid #ffff;
    border-radius:5px;
    height:30px;
    font-size:12px;*/
    /*float:right;
    margin-top:-50px;
    margin-right:10px;*/
}

.text-warning{
    color:red;
}
.custom-combo{
    height:25px;
    margin:0px;

}

.delete-icon{
    width:16px;
    cursor:pointer;
    margin:4px 0 0 5px;
    height:15px;
    text-align : center 
}
.hide-ele{
    display:none !important;
}
.show-ele{
    display:block !important;
}
.selected{
    color : dodgerblue;
}
#selected_file_name{
    color:#1a0d00;
    font-weight:bold;
}
.select2-container {
    /*width: auto !important;*/
    /*border:1px solid red;*/
}
.div-flex{
    display:flex;
}

.cus-padding{
    padding-right:5px !important;
    padding-left:0px !important;
}
.creator-info-group{
    margin:10px 0 15px 0;
}
#txtA_Description{
    min-height: 100px;
    display: block;
    position:relative;
}
.has-search .form-control-feedback {
    right: initial;
    left: 0;
    color: #ccc;
}

.has-search .form-control {
    padding-right: 12px;
    padding-left: 34px;
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
                <img src='../public/image/word.png' alt='' height='16' width='16' />&nbsp
                <h5 style="margin:1px 0 0 0;">テンプレート作成・編集</h5>
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

    <div class='main-content-body col-md-12'>
        <input type="hidden" id="hidAuthority_id" value="{{Session::get('authority_id')}}"/>
        <div class="row col-md-10" >
            <div class="pull-right" >
               <input type="button" name="btnCreateNewTemplate" id="btnCreateNewTemplate" class="btn btn-primary" value="新規作成" onclick="CreateNewTemplate()"/>&nbsp;&nbsp;
                <input type="button" name="btnSaveTemplate" class="btn btn-info" id="btnSaveTemplate" value="登録" onclick="SaveTemplate()"/>&nbsp;&nbsp;
                <input type="button" name="btnDeleteTemplate" id="btnDeleteTemplate" class="btn btn-danger" value="削除" onclick="DeleteTemplate()"/> 
            </div>
        </div>
        <div class='sidebar col-md-3 col-lg-3'>

            <div class="form-group has-feedback has-search">
                <span class="glyphicon glyphicon-search form-control-feedback"></span>
                <input type="text" id="txtSearch" class="form-control" placeholder="検索">
             </div>
            <h4 style="margin:10px 0 10px 10px;">テンプレート一覧</h4> 

            <div id='levelDiv'>
                <ul class='levelList list1'>
                    
                </ul>
            </div>
        </div>  <!--sidebar-->
        <div class="doc-details col-xs-9 col-lg-8" id="documentDetails" >
            
            <div class="row">
                <form id="template-form" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <span class="text-warning full-wd"></span>
                    <div id="template-info" class="col-md-11 outer-border-design" >
                        <label>テンプレート名</label>
                        <input type="text" name="txtTemplateName" id="txtTemplateName" class="form-control input-sm full-wd" placeholder="テンプレート名を入力"/>
                        <label>テンプレートの説明</label>　
                        <textarea name="txtA_Description" id="txtA_Description"class="full-wd" placeholder="説明"></textarea>
                        <div class="creator-info-group row">
                            <div class="col-md-6 div-flex">
                                <div class="col-md-4 cus-padding">
                                    <label>新規作成者</label>
                                    <input class="form-control input-sm" type="text" id="txtNewTemplateCreator" disabled/>
                                </div>
                                <div  class="col-md-5 cus-padding">
                                    <label>組織名</label>
                                    <input class ="form-control input-sm" type="text" id="txtOrganization" disabled/>
                                </div>
                                <div  class="col-md-3 cus-padding">
                                    <label>新規作成日</label>
                                    <input class="form-control input-sm" type="text" id="txtNewTemplateCreatedDate" disabled/>
                                </div>
                                
                            </div >
                                
                            <div class="col-md-6 div-flex">
                                <div  class="col-md-4 cus-padding">
                                    <label>最終更新者</label>
                                    <input class="form-control input-sm" type="text" id="txtlastTemplateCreator" disabled />
                                </div>
                                <div  class="col-md-5 cus-padding">
                                   <label>組織名</label>
                                   <input class="form-control input-sm" type="text" id="txtlastOrganization" disabled/> 
                                </div>
                                <div class="col-md-3 cus-padding">
                                    <label>最終更新日</label>
                                    <input class="form-control input-sm" type="text" id="txtlastTemplateCreatedDate" disabled/>
                                </div>
                                
                            </div>
                        </div>
                        <label class="custom-label">テンプレートの置換文字列は"${任意の文字列}"という形式で作成してください。</label>
                        <div class="" style="display:flex;"><input type="file" name="file" id="file"/><span id="selected_file_name"></span></div>

                        <!--<ul id="template-type">-->
                        <!--    <label class="custom-ul-label">テンプレート形式を選択</label>-->
                        <!--    <li><input type="radio" name="templateType" value="1">&nbsp;&nbsp;PJコードが同じ列に並んでいる</li>-->
                        <!--    <li><input type="radio" name="templateType" value="2">&nbsp;&nbsp;PJコードが同じ行に並んでいる</li>-->
                        <!--    <li><input type="radio" name="templateType" value="3">&nbsp;&nbsp;1つのシートに1プロジェクト</li>-->
                        <!--</ul>-->
                        
                        <!--<p class="custom-label" name="txtA_Caution" id="txtA_Caution" class="full-wd"></p>-->
                        <div class ="table-responsive" style="margin-top:15px;">
                            <label>対応表</label>
                            <table id="tblTemplateVariable" width="100%">
                                <thead>
                                   <tr>
                                    <th width="55%">日本語</th>
                                    <th width="45%">置換文字列</th>
                                </tr> 
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="2"><img src="../public/image/plus.png" alt="dropdown" align="dropdown" style="width:16px;cursor:pointer;" onclick="AddNewRow()"></td>
                                    </tr>
                                </tbody>
                                
                            </table>
                        </div>
                        
                        <input type="button" name="btnSaveTemplate" class="full-wd btn btn-info custom-btn" id="btnSaveTemplate" value="登録" onclick="SaveTemplate()"/>
                    </div>
                </form>
            </div>

        </div>  <!--doc-details-->
    
    </div>
    
</div>
@endsection