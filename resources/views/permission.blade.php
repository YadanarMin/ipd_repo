@extends('layouts.baselayout')

@section('head')
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>
<script type="text/javascript" src="../public/js/bim360.js"></script>
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
<script type="text/javascript" src="../public/js/tableHeadFixer.js"></script>


<!--<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/js/jquery.tablesorter.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/css/theme.default.min.css">-->
<style>
table {
    border-collapse: separate;
    border-spacing: 1px;
    overflow: auto;
}

.form-control{
    width:85%;
    background-color:none;
}
/* Bootstrap 3 text input with search icon */
.has-search .form-control-feedback {
    color: #ccc;
    margin-bottom:-34px;
}

.has-search .form-control {
    padding-right: 12px;
    padding-left: 34px;
}
.glyphicon{
    position:static;  
}

#txtSearchBim360User{
    width:60%;
    /*padding-left: 34px;
    margin-left: 5%;*/
}
.loader,
.loader:before,
.loader:after {
  border-radius: 50%;
  width: 2.5em;
  height: 2.5em;
  -webkit-animation-fill-mode: both;
  animation-fill-mode: both;
  -webkit-animation: load7 1.8s infinite ease-in-out;
  animation: load7 1.8s infinite ease-in-out;
}
.loader {
  color: #337ab7;
  font-size: 10px;
  margin: 80px auto;
  position: relative;
  text-indent: -9999em;
  -webkit-transform: translateZ(0);
  -ms-transform: translateZ(0);
  transform: translateZ(0);
  -webkit-animation-delay: -0.16s;
  animation-delay: -0.16s;
}
.loader:before,
.loader:after {
  content: '';
  position: absolute;
  top: 0;
}
.loader:before {
  left: -3.5em;
  -webkit-animation-delay: -0.32s;
  animation-delay: -0.32s;
}
.loader:after {
  left: 3.5em;
}

@keyframes load7 {
  0%,
  80%,
  100% {
    box-shadow: 0 2.5em 0 -1.3em;
  }
  40% {
    box-shadow: 0 2.5em 0 0;
  }
}

.modal{
    top:64px;

}
.modal-dialog {
  height: 90%; /* = 90% of the .modal-backdrop block = %90 of the screen */
}
.modal-content {
  height: 100%; /* = 100% of the .modal-dialog block */
}
.modal-body{
    height: 80%;
}
.table tbody{
    display: block;
    height:550px;
    overflow-y: scroll;
}

.table thead,.table tbody >tr{

    display: inline-block;
    width: 100%;
}
.modal-title{
    text-align: center;
    font-weight: bold;
    color:#337ab7;
    font-size: 1.2em;
}

.outer-container
{
    background-color: #0000;
    position: relative;
    top:3vh;
    left: 0;
    right: 300px;
    bottom: 40px;
    /*border:1px solid red;*/
}

.inner-container
{
    overflow: hidden;
}

.table-header
{
    position: relative;
}
.table-body
{
    position: relative;
    max-height: 550px;
    overflow: auto;
}

.head-row{
    background-color:#1a0d00;
    text-align:center;
    color:white;
    height: 40px;
}
.header-cell
{
    height: 40px;
    text-align:center;
    font-weight: bold;
}
.body-cell 
{
    height: 30px;
    text-align: center;
    padding-left: 5px;
}
.col1, .col3{width:150px;min-width: 150px;}
.col4{width:200px;min-width: 200px}
.col2{width:300px;min-width: 300px;}
.col5{width:70px;min-width: 70px;}
#bodytable tr:nth-child(even) {background: #f2f2f2}
#bodytable tr:nth-child(odd) {background: #d9d9d9}
#bodytable td:nth-child(1),#bodytable td:nth-child(2),#bodytable td:nth-child(3),#bodytable td:nth-child(4){
    text-align:left;
}
#btnRefreshBim360Users{
    border:none;
    background-color: #0000;
}

#bodytable td:nth-child(1){

}
.disable-color{
    color:gray;
}
.icon-stack {
  position: relative;
  display: inline-block;
  width: 2em;
  height: 2em;
  line-height: 4em;
  vertical-align: middle;
}

.icon-stack-3x {
  position: absolute;
  left: 0;
  width: 100%;
  text-align: right;
}

.icon-stack-3x {
  font-size: 1.5em;
}
</style>
@endsection

@section('content')
@include('layouts.loading')
    <div class="main-content">
        
    <!-- Bim360 Users Popup Start-->    
    <div class="modal" id ="myModal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">BIM360 USERS</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="popup_body">
            <div class="form-group has-search">
               <span class="glyphicon glyphicon-search form-control-feedback"></span>
               <input type="text" class="form-control" id="txtPopupBim360User" placeholder="ユーザー検索"> 
            </div>
            <div style="float:right;margin-top:-5vh;">
                <button id="btnRefreshBim360Users" onclick="GetAllUsers()">
                    <img src='../public/image/refresh.png' alt='' height='30' width='30' /> 
                </button>
            </div> 
            <table id="tblBim360Users" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
               <thead>
                   <tr>
                   <th width="31"></th>
                   <th width="210">name</th>
                   <th width="350">email</th>
                   </tr>
               </thead>
               <tbody>
                   
               </tbody>
               
           </table>
            
          </div>
          <div class="modal-footer">
             
            <button type="button" class="btn btn-primary" onClick="AddUsersToTable()">ADD</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Bim360 Users Popup End-->
    
    <div class="row">
        <div class ="col-xs-11">
            
           <h4 align="left">{{$bim360ProjectName}}</h4><br />
           <div class="form-group has-search">
           <span class="glyphicon glyphicon-search form-control-feedback"></span>
           <input type="text" class="form-control" id="txtSearchBim360User" placeholder="ユーザー検索"> 
           </div>
           <div style="float:right;margin-top:-5vh;margin-right:10%;">
             <input type="button" id="btnAllFolderPermission" class="btn btn-primary" value="現在の権限見る" onClick="DisplayAllFolderPermission()" >&nbsp;&nbsp;&nbsp;
             <input type="button" id="btnBim360Users" class="btn btn-primary" value="Bim360 Users" onClick="DisplayBim360UsersPopup()" >  
           </div>
           <!--<div class="loader">Loading。。。</div>-->
           <input type="hidden" name="hidProjectId" id="hidProjectId" value="{{$bim360ProjectId}}"/>
           <input type="hidden" name="hidProjectName" id="hidProjectName" value="{{$bim360ProjectName}}"/>

          <!-- <table id="tblUserPermission" class="">
               <thead></thead>
               <tbody></tbody>
               
            </table></br></br>-->
            <div class="outer-container">
                <div class="inner-container">
                    <div class="table-header">
                        <table id="headertable">
                            <thead>
                            </thead>
                        </table>
                    </div>
                   <div class="table-body">
                        <table id="bodytable">
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
           
        </div>
    </div>
    
</div>
@endsection
