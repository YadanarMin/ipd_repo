@extends('layouts.baselayout')
@section('title', 'CCC - Model changed history')

@section('head')
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>

<script type="text/javascript" src="../public/js/deleted_elements.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/js/select2.min.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/redmond/jquery-ui.css" >
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/i18n/jquery.ui.datepicker-ja.min.js"></script>

<style>
select{
  width: 150px;
}
#tbDeletedElements{
	border-collapse: collapse;
	width: 98%;	
	margin-top: 2%;
}
#tbDeletedElements tbody{
	height: 200em;
	overflow: scroll;
}

#tbDeletedElements td,th{
	border: 1px solid paleturquoise;
	font-size: 0.9em;
}

#tbDeletedElements th{
	background-color: #ccc;
  height:40px;
  text-align: center;
}
#tbDeletedElements td{
  text-align:left;
  padding-left:5px;
}
#txtDate{
  height:27px;
  border-radius:5px;
  border:1px solid #ccc;
  padding:3px;
}
#btnDisplay{
  padding:5px;
  border-radius:5px;
  width:60px;
}
#loader {
  position: absolute;
  left: 50%;
  top: 50%;
  z-index: 1;
  width: 150px;
  height: 150px;
  margin: -75px 0 0 -75px;
  border: 16px solid #f3f3f3;
  border-radius: 50%;
  border-top: 16px solid #3498db;
  width: 120px;
  height: 120px;
  -webkit-animation: spin 2s linear infinite;
  animation: spin 2s linear infinite;
}
@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Add animation to "page content" */
.animate-bottom {
  position: relative;
  -webkit-animation-name: animatebottom;
  -webkit-animation-duration: 1s;
  animation-name: animatebottom;
  animation-duration: 1s
}

@-webkit-keyframes animatebottom {
  from { bottom:-100px; opacity:0 } 
  to { bottom:0px; opacity:1 }
}

@keyframes animatebottom { 
  from{ bottom:-100px; opacity:0 } 
  to{ bottom:0; opacity:1 }
}
.bgNone{
  background: rgba(0, 0, 0, 0.7);
  transition: opacity 500ms;
  display:none
}
.deletedElementOuterBorder{
    border:1px solid #ccc;
    overflow:auto;
    height:80vh;
    width: 75%;
  padding-bottom:0px;
}
#tbDeletedElements{
    margin-bottom:9vh;
}

</style>
@endsection

@section('content')
<div class="main-content">
  <input type="hidden" id="hidLoginID" name="hidLoginID" value="{{Session::get('login_user_id')}}"/>

	<div class="col-8 centerDesign">
		<h4 class="page-title">モデル変更状況追跡</h4>	                
    </div>
    <div class="col-10  centerDesign">
    <select id="cmbProject"><option></option></select>&nbsp;
    <select id="cmbUser"><option></option></select>&nbsp;
    <select id="cmbLevel"><option></option></select>&nbsp;
    <select id="cmbStatus">
      <option></option>
      <option>Added</option>
      <option>Modified</option>
      <option>Deleted</option>
    </select>&nbsp;

    <input type="text" id="txtDate" name="txtDate" placeholder="Select Date"/>
    <select id="cmbDisplayType">
      <option value="dataDisplay">データ表示</option>
      <option value="chartDisplay">チャート表示</option>
    </select>&nbsp;
    <input type="button" class="btn btn-primary" name="btnDisplay" id="btnDisplay" value="表示" onClick="DisplayDeletedElements()"/>
    <div id="loader" class="back"></div><!--loader class-->
    <div id="chartDiv"></div>
    <div id="pieChartDiv" style="height:40vh;padding-top:40px;display:flex;flex-direction:row;"></div>
    <table id="tbDeletedElements" align="center">	
    </table>
    </div>
   
</div>
@endsection