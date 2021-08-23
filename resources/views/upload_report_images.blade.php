@extends('layouts.baselayout')
@section('title', 'CCC - UploadImages')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script type="text/javascript" src="../public/js/uploadImage.js"></script>
<script type="text/javascript" src="../public/js/box.js"></script>
<script type="text/javascript" src="../public/js/select2/select2.min.js"></script>
<link rel="stylesheet" href="../public/css/select2.min.css">

<style>

#tblUpload{
    width:100%;
    
}
#tblUpload> tbody {
    display:block;
    height:500px;
    overflow:auto;
}
#tblUpload> thead, tbody tr {
    display:table;
    width:100%;         
}
#tblUpload> thead {
    width:100%   
}
#tblUpload th{
    padding:10px 0 10px 0;/*TRBL*/
    background-color:#1a0d00;/*#002b80*/
    color:white;
    border:1px solid;
    text-align:center;
}
#tblUpload td{
    padding:5px;
    border:1px solid #fff;
}
#tblUpload td:first-child{text-align:left;}
#tblUpload tr:nth-child(even){background-color:#d9d9d9;}
#tblUpload tr:nth-child(odd){background-color:#f2f2f2;}

.custom-size{
    width:80%;
    min-width:700px;
    margin-left: 10% !important;
    <!--margin-right: 10% !important;-->
    margin-top:1%;
    /*border:1px solid green;*/

}
#tblRename{
    width:100%;
    margin-top:2%;
    margin-bottom:2%;
   
}
#tblRename td{
    background-color:#eee;
    padding:5px;
    border:1px solid #eee;
}
#tblRename th{
   background-color:#eee;
   color:#1a0d00;
   border:1px solid #eee;
   text-align:center;
   padding:5px;
}
.td-wd-0{
    width:2%;
}
.td-wd-1{
    width:10%;
}
.td-wd-2{
    width:13%;
}
.td-wd-3{
    width:20%;
}

.select2-container {
    width: 100% !important;
}
.txt-bold{
    font-weight:bold;
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
<script>
   
</script>

@endsection

@section('content')
@include('layouts.loading')
<main>

    @if (Session::has('access_token'))
        <input type="hidden" id="access_token" value="{{Session::get('access_token')}}"/>
    @endif 
    
    @if (Session::has('login_email'))
        <input type="hidden" id="login_email" value="{{Session::get('login_email')}}"/>
    @endif   
   
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12 custom-size" >
                <h4>BOX UPLOAD</h4><span id="box_login_warning" style="color:red;"></span>
                <hr>
                <div style="display:flex;">
                    <form id="box-upload-form" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="file" id="file" name="file[]" multiple="multiple">
                    </form>
                    <input type="button" name="btnBoxUpload" id="btnBoxUpload" class="btn btn-primary" value="BOXにアップロード" onClick="UploadFilesToBox()"/>
                </div>
                
            </div>
            
        </div>
        <div class="row">
             <div class="col-md-12 custom-size">
                <table id="tblRename"></table>
            </div>
        </div>
       
        <div class="row">
            <div class=" col-md-12 custom-size">
                <div class="form-group has-feedback has-search">
                    <span class="glyphicon glyphicon-search form-control-feedback"></span>
                    <input type="text" id="txtSearch" class="form-control" placeholder="ファイル名で検索">
                 </div>
                <!--<div class="table-responsive">-->
                <table id="tblUpload">
                    <thead>
                        <tr>
                            <th width="10%">No.</th>
                            <th>ファイル名</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <!--</div>-->
            </div>
        </div>
        
    </div>
  
</main>
@endsection