@extends('layouts.baselayout')
@section('title', 'CCC - Project auto save settings')

@section('head')
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>
<script type="text/javascript" src="../public/js/script.js"></script>
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
<style>
#borderAdjust{
    min-height:80vh;
    border:none;
}
#tblSetting{
    width:60%;
    margin:1% 0 0 5%;
}
#tblSetting th{
    padding:10px 0 10px 0;/*TRBL*/
    background-color:#1a0d00;;
    color:white;
    border:1px solid;
    text-align:center;
}
#tblSetting td{
    padding-bottom:5px;
    text-align:center;
}
#tblSetting td:first-child{
    text-align:left;
    padding-left:10px;
}

#tblSetting tr:nth-child(even) {background: #f2f2f2}
#tblSetting tr:nth-child(odd) {background: #d9d9d9}
#searchDiv{
    width:60%;
    margin:0 0 0 5%;
    height: 8vh;
    display:inline-block;
}
.form-control{
    width:90%;
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
.alert{
width:60%;
margin-left:5%;
}
</style>
<script>
 $(document).ready(function(){

    var login_user_id = $("#hidLoginID").val();
    var img_src = "../public/image/JPG/ローディング中のアイコン1.jpeg";
    var url = "admin/index";
    var content_name = "CCC取込設定";
    recordAccessHistory(login_user_id,img_src,url,content_name);
 })
    
</script>
@endsection

@section('content')
<div class="main-content">
    
    <input type="hidden" id="hidLoginID" name="hidLoginID" value="{{Session::get('login_user_id')}}"/>

    <div id="searchDiv">
        <h4 class="page-title">CCC取込設定</h4>             
        <div class="form-group has-search">
            <span class="glyphicon glyphicon-search form-control-feedback"></span>
            <input type="text" class="form-control" id="txtSearch" placeholder="プロジェクト検索">
            <input type="button" class="btn btn-primary" name="btnSetting" value="保存" onClick="SaveAdminSetting();" style="float:right;margin-top:-33px;"/>
        </div>
        
    </div>       
        
    <!--ngar ballo tg eight ngite naytarlal -->

    <div class="outerBorder" id="borderAdjust"> 
        <table id="tblSetting">
            <tr>
                <th width="70%;">プロジェクト名</th>
                <th>CCC取込</th>
                <!--<th>自動バックアップ</th>-->
            </tr>
            @if(Session::has('authority') && Session::get('authority') == 1 )
                @foreach($projects as $project)
                <tr>
                    <td>{{ $project["name"] }}</td>
                    <td><input type="checkbox" name="chkAutoSave" value="{{$project['id']}}" @if ($project['auto_save_properties'] == 1) checked @endif /></td>
                    <!--<td><input type="checkbox" name="chkBackup" value="{{$project['id']}}" @if ($project['auto_backup'] == 1) checked @endif/></td>-->
                </tr> 
                @endforeach 
            @else
                @if (count($projects) > 0  && Session::has('authCode') && Session::has('loggedinUserProjects'))
                @foreach($projects as $project)
                    @if(in_array($project["name"], Session::get('loggedinUserProjects')))
                        <tr>
                            <td>{{ $project["name"] }}</td>
                            <td><input type="checkbox" name="chkAutoSave" value="{{$project['id']}}" @if ($project['auto_save_properties'] == 1) checked @endif /></td>
                            <!--<td><input type="checkbox" name="chkBackup" value="{{$project['id']}}" @if ($project['auto_backup'] == 1) checked @endif/></td>-->
                        </tr> 
                    @endif
                @endforeach 
                @else
                <div class="alert alert-success" role="alert">
                 <label>FORGE LOGINされてないため、プロジェクト一覧表示できません。FORGE LOGIN してください。</label> 
                </div>
                @endif  
            @endif
            
        </table>           
       
    </div>
</div>
@endsection