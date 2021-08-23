@extends('layouts.baselayout')
@section('title', 'Access Log')

<!--CSS and JS file-->
@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>
<script type="text/javascript" src="../public/js/accessLogView.js"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/redmond/jquery-ui.css" >
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1/i18n/jquery.ui.datepicker-ja.min.js"></script>
<style>
.main-content{
	/*background-color: #f2f3f3;*/
	margin: 0 auto;
	width:90%;
	display : center;
}   
label{
    font-size :16px;
}
#searchBar{
    display: flex;
    align-items: flex-end;
}
#tableView{
    
    overflow: auto;
    white-space: nowrap;
    height: 500px;
    width: 100%;
    border : 1px solid #eee;
}
thead tr th {
    position: sticky;
    top: 0;
    background: #2e6da4;;
}
table thead tr{
    background : #2e6da4;
    color      : white;
    font-size  : 16px;
}
table tbody tr:nth-child(even) {
    background: #c5d1da;
    font-size : 14px;
}
table tbody tr:nth-child(odd) {
    background: #FFF;
    font-size : 14px;
}

</style>
@endsection

@section('content')
@include('layouts.loading')
<div class="main-content">
    <input type="hidden" id="hidLoginID" name="hidLoginID" value="{{Session::get('login_user_id')}}"/>
    
    <h3>アクセスログ</h1>
    <hr>
    <div id="searchBar" class="form-inline" style="margin-bottom : 20px">
        <div class="form-group">
            <label>名前：</label>
            <input type="text" class="form-control" name="username" id="username" />
        </div>
        <div class="form-group" style="margin-left :2%">
            <label>日付：</label>
            <input type="text" class="form-control" name="startDate" id="startDate" placeholder="　　年　－月　ー日　" autocomplete="off"　/>
            
            <label>～</label>
            <input type="text" class="form-control" name="endDate" id="endDate" placeholder="　　年　－月　ー日　" autocomplete="off"　/>
        </div>    
        <button class="btn btn-primary" style="margin-left :2%" onclick="searchAccessLog()">検索</button>
        
    </div>
    <div id="tableView"　>
        <table class="table table-bordered" width="100%" align="center" id="accessLogTable">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th  width="30%">ユーザー名</th>
                    <th>機能</th>
                    <th>日付</th>
                </tr>
            </thead>
            <tbody id="searchableAccessLog">
                
            </tbody>
            
        </table>
    </div>
</div>
<script>
 $(document).ready(function(){
    $.datepicker.setDefaults( $.datepicker.regional[ "ja" ] );
    $('#startDate').datepicker();    
	$('#endDate').datepicker();
	
    var login_user_id = $("#hidLoginID").val();
    var img_src = "../public/image/JPG/ローディング中のアイコン1.jpeg";
    var url = "common/accessLog";
    var content_name = "ｱｸｾｽﾛｸﾞ";
    recordAccessHistory(login_user_id,img_src,url,content_name);
 })
    
</script>
@endsection