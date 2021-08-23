@extends('layouts.baselayout')

@section('head')
<style>
.main-content{  
    margin:10vh 2vh -4vh 6vh;
}

ul{
    list-style: square;      
}
ul li{
    margin-top:12px;       
}
ul h4{
   font-weight:bold;
   color:#002b80;
}
ul ul{
    list-style:none;    
}
ul li a{
    color:black;
}
#prjTitle{
    color:#002b80;
    font-weight:bold;
    padding: 3% 0 0 0;
}
.spanTextDesign{
    display:none;
    color:red;
}
</style>
<script>
    function showText(id,userName){
        var message = "※"  +userName +" はこのページにアクセス権限がありません。";
        $("#"+id).text(message);
        $("#"+id).css('display', 'block');
    }
</script>
@endsection

@section('content')
<div class="main-content">
    <div class="row">
        <div class ="col-xs-4 outerBorder">
            <h4 id="prjTitle">プロジェクト一覧</h4>
            @if (count($projects) > 0)<!-- && Session::has('authCode')-->
            <ul>
            @foreach($projects as $project)
            <li>{{ $project["name"] }}</li>
            @endforeach
            </ul>       
            @endif
        </div>
        <div class ="col-xs-7 outerBorder" style="margin-left:3%;padding-top:3%;">
           <ul class="col-xs-3">
            <li><h4>分析</h4></li>
             <ul>
                <li><a href="{{ url('forge/index') }}" class="btn popovers">データ容量表示</a></li>
                <!--<li><a href="{{ url('dataPortal/index') }}">データポータル</a></li>-->
                <li><a href="{{ url('dataPortal/projectSearchConsole') }}">モデル管理</a></li>
                <li><a href="{{ url('dataPortal/roomInfoSearchConsole') }}">部屋情報管理</a></li>
                <li><a href="{{ url('OBJ/index') }}">OBJファイル変換</a></li>
             </ul>
           </ul>

           <ul class="col-xs-4">
            <li><h4>データベース</h4></li>
             <ul>
                <li><a href="{{ url('crane/search') }}">クレーン情報検索</a></li>
                <li><a href="{{ url('crane/save') }}">クレーン情報登録</a></li>
                <li><a href="{{ url('prjmgt/index') }}">プロジェクト管理</a></li>
                <li><a href="{{ url('common/saveRoom') }}">部屋仕上情報登録<span style="color:red;">【準備中】</span></a></li>
                <li><a href="#">構造タイプ登録<span style="color:red;">【準備中】</span></a></li>
                <li><a href="#">ファミリブラウザ<span style="color:red;">【準備中】</span></a></li>
                <li><a href="#">データ読み込み<span style="color:red;">【準備中】</span></a></li>
             </ul>          
           </ul>

           <ul class="col-xs-4">
            <li><h4>ユーザー情報</h4></li> 
             <ul>
                @if((Session::get('authority')) == 1)
                    <li><a href="{{url('common/changedInfo') }}">モデル変更履歴</a></li> 
                    <li><a href="{{url('common/userInfo') }}">ユーザー情報</a></li> 
                @else
                    <li><a href="javascript:void(0)" onclick="showText('err1','{{Session::get('userName')}}')">モデル変更履歴</a></li> 
                    <span id="err1" class="spanTextDesign"></span>
                    <li><a href="javascript:void(0)" onclick="showText('err2','{{Session::get('userName')}}');">ユーザー情報</a></li> 
                    <span id="err2" class="spanTextDesign"></span>
                @endif
               
             </ul>          
           </ul>

           <ul class="col-xs-4">
            <li><h4>ストレージ</h4></li> 
             <ul>
                @if((Session::get('authority')) == 1)
                    <li><a href="{{ url('admin/backup') }}">バックアップ設定</a></li> 
                @else
                    <li><a href="javascript:void(0)" onclick="showText('err3','{{Session::get('userName')}}')">バックアップ設定</a></li> 
                    <span id="err3" class="spanTextDesign"></span>
                 
                @endif
                             
             </ul>          
           </ul>

           <ul class="col-xs-4">
            <li><h4>セキュリティ</h4></li>
             <ul>
                @if((Session::get('authority')) == 1)
                    <li><a href="{{ url('admin/index') }}">プロジェクトデータ登録設定</a></li>
                    <li><a href="{{ url('bim360/index') }}">BIM360権限設定</a></li>
                    <li><a href="{{ url('user/index') }}">ユーザー作成</a></li>
                    
                @else
                    <li><a href="javascript:void(0)" onclick="showText('err4','{{Session::get('userName')}}')">権限設定</a></li> 
                    <span id="err4" class="spanTextDesign"></span>
                    <li><a href="javascript:void(0)" onclick="showText('err5','{{Session::get('userName')}}');">ユーザー作成</a></li> 
                    <span id="err5" class="spanTextDesign"></span>
                @endif
                
             </ul>          
           </ul>
           
           <ul class="col-xs-4">
            <li><h4>その他</h4></li>
             <ul>
                <li><a href="{{ url('forge/volumeChart') }}">マテリアル容積表示</a></li>
                <li><a href="{{ url('roomProp/index') }}">部屋情報表示</a></li>
                <li><a href="{{ url('forge/tekkin') }}">鉄筋重量表示</a></li>
                <li><a href="{{ url('allstore/index') }}">全店物件情報</a></li>
             </ul>
           </ul>


        <div>
    </div>
</div>
</div>
</div>
@endsection