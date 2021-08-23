@extends('layouts.baselayout')
@section('title', 'CCC - 閲覧権限設定')

@section('head')
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="/iPD/public/js/projectAccessSetting.js"></script>

<script src='https://kit.fontawesome.com/a076d05399.js'></script>
<style>
.marginAdjust{
    width:1300px;
    margin: 2vh auto;
}
.td-sm{
    width:100px;
}
.td-md{
    width:200px;
}
.td-lg{
    width:250px;
}
#tblUser{
    width:100%;
}
#tblUser td:last-child{
    text-align:center;
}
input[type="checkbox"]{
    width:10%;
    height:20px;
}
.outerBorder{
    border:none;
    width:100%;
    min-height:30vh;
    height:auto;
}
.appIconBig{
 display: block;
 margin-left: auto;
 margin-right: auto;
}
#tblUser td{
    padding-bottom: 5px;
    text-align: left;
    border : 1px solid #fff;
}
#btnCreateUserPopu{
    margin: 6px 0 6px 0;
    font-size: 12px;
    border: solid 1px lightgray;
}
#searchDiv{
    display: flex;
    justify-content: space-between;
}
#btnGroup{
    margin-left: 220px;
}
#btnAutoPassword{
    font-size:0.3em;
    float:right;
}
.popup{
    height:auto;
}
th,td{
/*border:1px solid;*/
}
.warning_text{
    color:red;
    font-size:0.5em;
}
.popupSize{
    width:500px;
}
.popupHeader{
    padding:0px;
    margin:0px;
}
</style>
@endsection

@section('content')
<div class="main-content marginAdjust">  
    <input type="hidden" id="hidLoginID" name="hidLoginID" value="{{Session::get('login_user_id')}}"/>

    <div id="searchDiv">
        <!--<h4 class="titleDesign pageTitle">ユーザー設定 </h4>-->
        <h4 class="page-title">閲覧権限設定</h4>
       

    </div>  
    <div class="outerBorder">
        <table id="tblUser">
            <tr>
                <th class="td-md">ユーザー名</th>
                <th class="td-md">メールアドレス</th>
                <th class="td-md">支店名</th>
                <th class="td-lg">組織</th>
                <th class="td-md">物件情報<br>アクセスセット名</th>
                <th class="td-md">項目別<br>アクセスセット名</th>
                <th class="td-md">モデルデータ<br>アクセスセット名</th>
                <th class="td-sm">詳細権限</th>
            </tr> 
            @if (count($users) > 0)
            @foreach($users as $user)
            <tr>
                <td>{{ $user["name"] }}</td>
                <td>{{ $user["email"] }}</td>
                <td>{{ $user["branch"] }}</td>
                <td>{{ $user["dept"] }}</td>
                <td>{{ $user["allstore_set_name"] }}</td>
                <td>{{ $user["allstore_item_set_name"] }}</td>
                <td>{{ $user["model_data_set_name"] }}</td>
                <td><a href="javascript:void(0)" onClick="LoadProjectAccessSetting({{$user['personal_id']}},'{{ $user["name"] }}');">設定</a></td>
                <!--<i class="fas fa-cogs"></i>-->
            </tr>  
            @endforeach  
            @endif           
        </table> 
    </div>
    

</div>


@endsection