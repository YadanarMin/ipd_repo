@extends('layouts.baselayout')
@section('title', '見積管理')

<!--CSS and JS file-->
@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>
<script type="text/javascript" src="../public/js/estimate.js"></script>


<style>
.main-content{
	margin: 0 auto;
	width:90%;
	display : center;
} 
.estimateView{
    
}
.projectView{
    width : 90%;
}
.companyView{
    width : 60%;
}
.heading{
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.btnHeading{
    display: flex;
    margin: 0 0 0 3%;
    justify-content: space-between;
    align-items: flex-end;
}
#tblProjectList{
    width : 100%;
}
#tblProjectList th, #tblDuringProjectList th, #tblFinishedProjectList th{
    padding: 10px 0 10px 0;
    background-color: #1a0d00;
    color: white;
    border: 1px solid;
    text-align: center;
}
.disabledBtn{
    background-color: #cccccc;
}
.btn-toggle{
    margin : 1% 0 0 0;
    }

</style>

@endsection
@section('content')
@include('layouts.loading')
<div class="main-content">
    <input type="hidden" id="hidLoginID" name="hidLoginID" value="{{Session::get('login_user_id')}}"/>
    @if (Session::has('access_token'))
        <input type="hidden" id="access_token_value" value="{{Session::get('access_token')}}"/>
    @endif
    
    
    <div class="btn-group btn-toggle">
        <button type="button" class="btn btn-default disabledBtn" id="mainPage" ><h5>見積メインページ</h5></button>
        <button type="button" class="btn btn-default" id="selectProject" onclick="EstimateProjectSelect();"><h5>見積プロジェクト</h5></button>
        
    </div>
    <hr>
    <div class="estimateView">
        <label id="box-login-check" style="color : red"></label>
       <!--見積前-->
        <div id="beforeEstimate">
            <div class="heading">
                <h3><span class="glyphicon glyphicon-cog"></span> 見積前プロジェクト</h3>
                <div>
                    <button type="button" class="btn btn-primary" onclick="ShowBoxFolder();">BOX状態表示</button>
                    <button type="button" class="btn btn-primary" onclick="MoveToEstimate();">見積中に移動</button>
                    <button type="button" class="btn btn-primary" onclick="GoToProjectSetting();">プロジェクト取り込み</button>
                </div>
                
            </div>
                
            <div class="projectList" style="padding: 20px">
                <table class="table table-bordered" id="tblProjectList">
                    <thead>
                        <tr>
                            <th width="5%">設定</th>
                            <th>PJコード</th>
                            <th width="35%">プロジェクト名</th>
                            <th>見積フォルダ</th>
                            <th>着工日</th>
                            <!--<th>BOX構造図</th>-->
                            <!--<th>BOX意匠図</th>-->
                            <!--<th>BOX工程表</th>-->
                            <th>BOXアクセス者</th>
                            <th>BOXアクセス年月日</th>
                            <th>秘密保持</th>
                            <th>期限</th>
                            <th>見積提出件数</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <!--@if(empty($projectList))-->
                        <!--    <tr><td colspan="10" style="text-align: center">見積物件設定画面より物件を選択してください。</td></tr>-->
                        <!--@else-->
                        <!--    @foreach($projectList as $project)-->
                        <!--    <tr id="{{ $project['a_pj_code'] }}">-->
                        <!--        <td>-->
                        <!--            <input type="checkbox" class="estimateProjectSelect" id="{{ $project['a_pj_code'] }}"  value="{{ $project['a_pj_name'] }}">-->
                        <!--        </td>-->
                        <!--        <td>{{ $project['a_pj_name'] }}</td>-->
                        <!--        <td></td>-->
                        <!--        <td>{{ $project['a_tyakkou'] }}</td>-->
                        <!--        <td></td>-->
                        <!--        <td></td>-->
                        <!--        <td></td>-->
                        <!--        <td></td>-->
                        <!--        <td></td>-->
                        <!--        <td></td>-->
                        <!--    </tr>-->
                            
                        <!--    @endforeach-->
                        <!--@endif-->
                        
                    </tbody>
                </table>
            </div>
        </div>
            
        <hr>
        <!--見積中-->
        <div id="duringEstimate">
            <div class="heading">
                <h3><span class="glyphicon glyphicon-cog"></span> 見積中プロジェクト</h3>
                <div>
                    <button type="button" class="btn btn-primary" onclick="MoveToEstimateFinished()">見積完了に移動</button>
                </div>
            </div>
            
            <div class="projectList" style="padding: 20px">
                <table class="table table-bordered" id="tblDuringProjectList">
                    <thead>
                        <tr>
                            <th width="5%">設定</th>
                            <th>PJコード</th>
                            <th width="35%">プロジェクト名</th>
                            <th>見積フォルダ</th>
                            <th>着工日</th>
                            <!--<th>BOX構造図</th>-->
                            <!--<th>BOX意匠図</th>-->
                            <!--<th>BOX工程表</th>-->
                            <th>BOXアクセス者</th>
                            <th>BOXアクセス年月日</th>
                            <th>秘密保持</th>
                            <th>期限</th>
                            <th>見積提出件数</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        
                        
                    </tbody>
                </table>
            </div>
        </div>
            
        <hr>
        <!--見積終了-->
        <div id="afterEstimate">
            <div class="heading">
                <h3><span class="glyphicon glyphicon-cog"></span> 見積完了プロジェクト</h3>
                <div>
                    
                </div>
            </div>
            
            <div class="projectList" style="padding: 20px">
                <table class="table table-bordered" id="tblFinishedProjectList">
                    <thead>
                        <tr>
                            <th width="5%">設定</th>
                            <th>PJコード</th>
                            <th width="35%">プロジェクト名</th>
                            <th>見積フォルダ</th>
                            <th>着工日</th>
                            <!--<th>BOX構造図</th>-->
                            <!--<th>BOX意匠図</th>-->
                            <!--<th>BOX工程表</th>-->
                            <th>BOXアクセス者</th>
                            <th>BOXアクセス年月日</th>
                            <th>秘密保持</th>
                            <th>期限</th>
                            <th>見積提出件数</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        
                        
                    </tbody>
                </table>
            </div>
        </div> 
    </div>
</div>
<script>
    $(document).ready(function(){
        var login_user_id = $("#hidLoginID").val();
        var img_src = "../public/image/JPG/ローディング中のアイコン1.jpeg";
        var url = "estimate/index";
        var content_name = "見積管理";
        recordAccessHistory(login_user_id,img_src,url,content_name);
    });
</script>
@endsection