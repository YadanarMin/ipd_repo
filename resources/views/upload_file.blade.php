@extends('layouts.baselayout')
@section('title', 'CCC - UploadFiles')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script type="text/javascript" src="../public/js/uploadFile.js"></script>
<style>


.header-part,.body-part{
    display:flex;
    justify-content: center;
}

#dragandrophandler
{
    border:2px dotted #0B85A1;
    width:400px;
    color:#92AAB0;
    padding:15px;
    margin-bottom:10px;
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
.saveBtn{
	float:right;
    margin-right:0%;
}

.page-title{
 margin-right:0%;
 display:flex;
 justify-content:left;
}
.page-title h4 {
	padding-top: 50px;
}

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
}
#tblUpload td:first-child{text-align:left;}
#tblUpload tr:nth-child(even){background-color:#d9d9d9;}
#tblUpload tr:nth-child(odd){background-color:#f2f2f2;}

.div-size{
    width:600px;
    /*border:1px solid green;*/
    margin-left: auto;
    margin-right: auto;
}
</style>
<script>
   
</script>

@endsection

@section('content')
<main>
    <div class=" div-size">
       <form action="{{ url('admin/UploadImages') }}" method="POST" enctype="multipart/form-data">
            {{csrf_field()}}
            <h4 class="page-title">ダウンロードファイルアップロード</h4>
            <div class="" style="width:100%;display:flex;margin-top:2vh;">
             
             <div id="dragandrophandler">ここにドロップしてください。</div>
             <div class="saveBtn">
                 <input class="btn btn-primary" type="button" value="アップロード"id="btnSaveImage" onClick="UploadFiles()"/> 
                 
             </div>
              
           </div>
        </form> 
    </div>
    <div class="div-size">
        @php
             $count = 0;
        @endphp
        <div class="table-responsive" style="width:100%;">
            <table id="tblUpload">
                <thead>
                    <tr>
                        <th width="5%">No.</th>
                        <th>ファイル名</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($files as $file)
                    <tr>
                        <td width="5%">{{++$count}}.</td>
                        <td>
                            {{$file}}
                            <button type="button" class="close" aria-label="Close" onClick="DeleteFile('{{$file}}')">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                
            </table>
        </div>
        
    </div>
</main>
@endsection