@extends('layouts.baselayout')
@section('title', 'CCC - tekkkin volume search')

@section('head')
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>
<script type="text/javascript" src="../public/js/tekkintest.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/js/select2.min.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
<style>
.main-content{
    background-color: #edf0f2;
}
#mainarea{
    background-color: #edf0f2;
    margin: 0px -29px 0 -29px;
}
#maincontent{
    padding: 10px 29px 0 29px;
}
#chartDiv{
    height:60vh;
}
#formulaPopupSize{
    width:1050px;
    height:93vh;
    background:#4C9CA0;
}
#tekkinTotal{
    /*text-align: right;*/
    /*margin: 0 5px 0 0;*/
}
#tekkinData{
    min-height:75.5vh;
    margin:0 0 -1vh 0;
    display: flex;
    border-top: 1px solid #dae1e6;
    /*border:1px solid black;*/
    background-color: #edf0f2;
}
#levelDiv{
    margin: 0 5vh 0 2vh;
}
.main{
    width :calc(100vh + 550px);
}
.sidebar{
    width:550px;
    margin:0vh 0 -0.999vh 0;
    border-right: 1px solid #dae1e6;
    /*border:1px solid red;*/
    /*background-color: white;*/
}
.matrix-table{
    margin:1vh 3vh 1vh 1vh;
    /*border:1px solid green;*/
}
.mt-header{
    height: 10vh;
    /*background-color: white;*/
}
.mt-body{
    height: 73.77vh;
    /*background-color: white;*/
}
.levelElement{
    display: flex;
}
.levelElement:hover{
    background : lightgray;
}
.levelElement.active{
    color : dodgerblue;
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
.acd-check{
    display: none;
}
.acd-label{
    background: #0068b7;
    color: #fff;
    display: block;
    margin-bottom: 1px;
    padding: 10px;
    position: relative;
}
.acd-label:after{
    background: #00479d;
    box-sizing: border-box;
    content: '\f067';
    display: block;
    font-family: "Font Awesome 5 Free";
    height: 40px;
    padding: 10px 20px;
    position: absolute;
    right: 0;
    top: 0px;
}

.acd-content{
    /*border: 1px solid #333;*/
    display: block;
    height: 0;
    opacity: 0;
    padding: 0 10px;
    transition: .5s;
    visibility: hidden;
}
.acd-check:checked + .acd-label:after{
    content: '\f068';
}
.acd-check:checked + .acd-label + .acd-content{
    height: 100px;
    opacity: 1;
    padding: 10px;
    visibility: visible;
}
.acd-label p{
	margin:0 0 0 0;
}
#tblTekkinData {
    width: 80%;
    margin-bottom:9vh;
}
#tblTekkinData  td{
    padding-left:20px;
}

#tblField{
    /*width: 700px;*/
}
#tblField table{
    width: 100%;
    border-collapse: collapse;
    border: solid #CCC;
    border-width: 1px;
}
#tblField table tr th,
#tblField table tr td{
    padding: 0.5em;
    text-align: center;
    vertical-align: middle;
    border: solid #CCC;
    border-width: 1px;
}
#tblField table tr th{
    background: silver;
    color: black;
}
@media screen and (max-width:768px){
    #tblField{
    width: 100%;
    }
    #tblField table tbody,
    #tblField table tr thead,
    #tblField table tr,
    #tblField table tr th,
    #tblField table tr td{
    display: block;
}
#tblField table{
    width: 100%;
    border-width: 0 1px 1px 0;
}
#tblField table tr th,
#tblField table tr td{
    padding:0.5em;
    border-width: 1px 0 0 1px;
}
#tblField table thead{
    float: left;
    width: 30%;
}
#tblField table thead tr{
    width: 100%;
}
#tblField table tbody{
    float: left;
    width: 70%;
}
#tblField table tbody tr{
    width: 33.3%;
}
#tblField table tbody tr{
    float: left;
}
}
</style>
@endsection

@section('content')
<div class="main-content">

    <div id="mainarea">
        <div id="maincontent">
            <h3>鉄筋情報表示</h3>
            <div style="display:flex;">  
            
                <select id="item" multiple > 
                </select>&nbsp;&nbsp;&nbsp; 
                
               <input type="button" class="btn btn-primary" name="btnTekkin" id="btnTekkin" value="ShowData" onClick="DisplayTekkinData()"/>&nbsp;&nbsp;&nbsp;
              <!-- <input type="button" class="btn btn-primary" name="btnTekkinPopup" id="btnTekkinPopup" value="ShowPopup" onClick="DisplayTekkinPopup()"/>-->&nbsp;&nbsp;&nbsp;
               <input type="button" class="btn btn-primary" name="btnTekkinExcel" id="btnTekkinExcel" value="Download Excel" onClick="DownloadTekkinExcel()"/>
            </div>
            <br>

            <div id="tekkinData">
                <div class="sidebar">
                    <div id="tekkinTotal">
                        <h5>総重量 3.77(t)</h5>
                    </div>
                    <div id="levelDiv">
                        <ul class="levelList list1">
                            <li>
                                <div class="levelElement" onClick="">
                                    <div>1FL</div>
                                    <div style="margin-left:auto;">1.42[t]</div>
                                </div>
                            </li>
                            <li>
                                <div class="levelElement" onClick="">
                                    <div>2FL</div>
                                    <div style="margin-left:auto;">0.00[t]</div>
                                </div>
                            </li>
                            <li>
                                <div class="levelElement" onClick="">
                                    <div>10FL</div>
                                    <div style="margin-left:auto;">0.00[t]</div>
                                </div>
                            </li>
                            <li>
                                <div class="levelElement" onClick="">
                                    <div>RFL</div>
                                    <div style="margin-left:auto;">0.00[t]</div>
                                </div>
                            </li>
                            <li>
                                <div class="levelElement" onClick="">
                                    <div>RHFL</div>
                                    <div style="margin-left:auto;">0.00[t]</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="main">
                    <!--<div class="matrix-table mt-header">-->
                        
                    <!--</div>-->
                    <div class="matrix-table mt-body">
                        
                    	<div style="margin:0 auto 5vh auto;">
                    
                            <div style="display:flex;height:35px;">
                                <div style="width:87%;margin-left:10px;"><p style="margin:5px 0 0 0;">カテゴリ</p></div>
                                <div><p style="margin:5px 0 0 0;">重量[t]</p></div>
                            </div>
                            
                    		<input id="acd-check1" class="acd-check" type="checkbox">
                    		<label class="acd-label" for="acd-check1">
                    			<p>柱<span style="float:right;margin-right:52px;">3.77</span></p>
                    		</label>
                    		<div class="acd-content">
                    		    <div id="tblField">
                        		    <table id="tblTekkinData">
                        		        <thead>
                                            <tr>
                                            	<th>No.</th>
                                            	<th>element_id</th>
                                            	<th>start_weight(t)</th>
                                            	<th>center_weight(t)</th>
                                            	<th>end_weight(t)</th>
                                            	<th>total(t)</th>
                                            	<th>phase</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                            	<td>count</td>
                                            	<td>element_id</td>
                                            	<td>start_weight</td>
                                            	<td>center_weight</td>
                                            	<td>end_weight</td>
                                            	<td>total</td>
                                            	<td>phase</td>
                                            </tr>
                                        <tbody>
                        		    </table>
                    		    </div>
                    		</div>
                    		
                    		<input id="acd-check2" class="acd-check" type="checkbox">
                    		<label class="acd-label" for="acd-check2">
                    			<p>梁<span style="float:right;margin-right:52px;">3.77</span></p>
                    		</label>
                    		<div class="acd-content">
                    		    <p>hello.world2!</p>
                    		</div>
                    
                    	</div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
   
</div>
@endsection