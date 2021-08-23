@extends('layouts.baselayout')
@section('title', 'CCC - OBJ file converter')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://developer.api.autodesk.com/modelderivative/v2/viewers/7.*/style.min.css" type="text/css">
<script src="https://developer.api.autodesk.com/modelderivative/v2/viewers/7.*/viewer3D.min.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<!--<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>-->
<script type="text/javascript" src="../public/js/select2/select2.min.js"></script>
<!--<script type="text/javascript" src="../public/js/forge.js"></script>-->
<script type="text/javascript" src="../public/js/OBJ.js"></script>
<!--<link rel="stylesheet" href="../public/css/jquery.multiselect.css">-->
<link rel="stylesheet" href="../public/css/select2.min.css">
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<style>
#chartDiv{
    height:60vh;
}
.row{
    margin-left:5%;
}
.upload-part{
    height:10vh;
    height:auto;
    border:none;
}
.header-part,.body-part{
    display:flex;
    justify-content: center;
}

#dragandrophandler
    {
    border:2px dotted #0B85A1;
    width:100%;
    color:#92AAB0;
    text-align:left;vertical-align:middle;
    padding:15px;
    margin-bottom:10px;
    font-size:120%;
    }
    .progressBar {
    width: 200px;
    height: 22px;
    border: 1px solid #ddd;
    border-radius: 5px; 
    overflow: hidden;
    display:inline-block;
    margin:0px 10px 5px 5px;
    vertical-align:top;
}
  
.progressBar div {
    height: 100%;
    color: #fff;
    text-align: right;
    line-height: 22px; /* same as #progressBar height if we want text middle aligned */
    width: 0;
    background-color: #0ba1b5; border-radius: 3px; 
}
.statusbar
{
    border-top:1px solid #A9CCD1;
    min-height:25px;
    width:700px;
    padding:10px 10px 0px 10px;
    vertical-align:top;
}
.statusbar:nth-child(odd){
    background:#EBEFF0;
}
.filename
{
display:inline-block;
vertical-align:top;
width:250px;
}
.filesize
{
display:inline-block;
vertical-align:top;
color:#30693D;
width:100px;
margin-left:10px;
margin-right:5px;
}
.abort{
    background-color:#A8352F;
    -moz-border-radius:4px;
    -webkit-border-radius:4px;
    border-radius:4px;display:inline-block;
    color:#fff;
    font-family:arial;font-size:13px;font-weight:normal;
    padding:4px 15px;
    cursor:pointer;
    vertical-align:top
}
</style>
<script>
    $(document).ready(function(){
        var login_user_id = $("#hidLoginID").val();
        var img_src = "../public/image/JPG/ローディング中のアイコン1.jpeg";
        var url = "OBJ/index";
        var content_name = "OBJﾌｧｲﾙ変換";
        recordAccessHistory(login_user_id,img_src,url,content_name);
    });
</script>
@endsection

@section('content')
<div class="main-content">
    
    <input type="hidden" id="hidLoginID" name="hidLoginID" value="{{Session::get('login_user_id')}}"/>

    <div class="row upload-part">
         <h3>OBJファイル変換</h3></br>
         <form action="{{ url('OBJ/upload') }}" name ="objForm" method="POST" enctype="multipart/form-data">
            {{csrf_field()}}
           <div class="col-sm-3" style="padding-left:0px;">
              <div id="dragandrophandler">ここにドロップしてください。</div>
           </div>
        </form> 
        <div class="col-xs-2 saveBtn" style="display:flex;">
            <input class="btn btn-primary" type="button" value="OBJファイルに変換" id="btnSaveImage" onClick="ChangeOBJFile()"/> 
         </div> 

       <!-- <div style="margin-bottom:5px;">
            <select id="item" multiple="multiple" style="width:70%">
            </select>&nbsp;&nbsp;
        </div>
        <div style="margin-bottom:10px;">
            <select id="version" multiple="multiple" style="width:70%">
            </select>&nbsp;&nbsp;
        </div>-->
    </div>
   <!-- <div style="display:flex;">  
        <select id="project"  multiple>
        </select>&nbsp;&nbsp;&nbsp;

        <select id="item" multiple > 
        </select>&nbsp;&nbsp;&nbsp;

        <select id="version" multiple >
        </select>&nbsp;&nbsp;&nbsp;
      <input type="button" name="btnChartDisplay" id="btnChartDisplay" value="モデル表示"onClick="LoadModel()"/>&nbsp;&nbsp;&nbsp;
        <input type="submit" name="btnchangeObj" id="btnchangeObj" value="OBJファイルに変換"onClick="ChangeOBJFile()"/>
    </div>-->

 
</div>
@endsection