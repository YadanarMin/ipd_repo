@extends('layouts.baselayout')
@section('title', '見積プロジェクト')

<!--CSS and JS file-->
@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script type="text/javascript" src="/iPD/public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="/iPD/public/js/jquery.multiselect.js"></script>
<script type="text/javascript" src="/iPD/public/js/estimate.js"></script>


<style>
.main-content{
	margin: 0 auto;
	width:90%;
	display : center;
} 
.companySelectMainView{
    
}
.companySelectView{
    display : flex;
    margin-top: 1%;
    padding: 20px;
    border: 1px solid #eee;
    box-shadow: 0 14px 28px rgb(0 0 0 / 25%), 0 10px 10px rgb(0 0 0 / 22%);
}
.disabledBtn{
    background-color: #cccccc;
}
.btn-toggle{
    margin : 1% 0 0 0;
}
#selectedCompanyList,#createFolder,#uploadData{
    margin-top : 2%;
    margin-bottom :1%;
    height :250px;
}
#scrollableSelectedCompanyList{
    padding :10px;
    
}
.selectModellingCompany{
    margin : 10px;
}
.selectedCompany{
    border :1px solid #eee;
    height :250px;
    overflow : auto;
}
#boxLoginWarning{
    margin: 10px;
    color: red;
}

</style>

@endsection
@section('content')
@include('layouts.loading')
<div class="main-content">
    <!--Check whether or not login in to BOX-->
    @if (Session::has('access_token'))
        <input type="hidden" id="access_token" value="{{Session::get('access_token')}}"/>
    @endif
    
    <div class="btn-group btn-toggle">
        <button type="button" class="btn btn-default" id="mainPage" onclick="EstimateMainPage();"><h5>見積メインページ</h5></button>
        <button type="button" class="btn btn-default disabledBtn" id="selectProject"><h5>見積プロジェクト</h5></button>
        
    </div>
    <hr>
    <div class="companySelectMainView">
        @if(count($projectNameList) > 0)
            @foreach($projectNameList as $projectName)
                <h4><li id='{{ $projectName['a_pj_code'] }}'>{{ $projectName['a_pj_name'] }}</li></h4>
            @endforeach
        @endif
        
        @if(!empty($ipdCodeList))
            <input type='hidden' name='hidIPDCode' id='hidIPDCode' value={{ $ipdCodeList }} />
        @endif
        
        <div class="companySelectView">
            <!--label and button-->
            <div style="width :30%">
                <div id="selectedCompanyList" style="height :400px">
                    <label>①見積を提出させる業者を選択してください。</label>
                    <div  id="scrollableSelectedCompanyList" >
                        <p>見積業者</p>
                        <div class="list-group selectedCompany">
                            <!--<a class="list-group-item">Modelling Company 1</a>-->
                            <!--<a class="list-group-item">Modelling Company 1</a>-->
                            <!--<a class="list-group-item">Modelling Company 1</a>-->
                           
                            
                        </div>
                        <input type='hidden' name='hidCompanyName' id='hidCompanyName' />
                    </div>
                </div>
                
                <div id="createFolder">
                    <label>②BOX内にフォルダを自動作成してください。</label>
                    <br>
                    <div style="display : flex; align-items: center;">
                        <button type="button" class="btn btn-primary btn-lg" onclick="CreateFolderInBox();">BOX内にフォルダ自動作成</button>
                        <div  style="margin-left :10px">
                            <div class="checkbox">
                              <label><input type="checkbox" value="概算B1" class="folder_flag">概算B1</label>
                            </div>
                            <div class="checkbox">
                              <label><input type="checkbox" value="概算B2" class="folder_flag">概算B2</label>
                            </div>
                            <div class="checkbox">
                              <label><input type="checkbox" value="概算B3" class="folder_flag">概算B3</label>
                            </div>
                            <div class="checkbox">
                              <label><input type="checkbox" value="精算" class="folder_flag">精算</label>
                            </div>
                        </div>
                        
                    </div>
                    
                    <label id="boxLoginWarning"></label>
                </div>
                <div id="uploadData">
                    <label>③BOXに所定の書類をアップロードしてください。</label>
                    <br>
                    <button type="button" class="btn btn-primary btn-lg" onclick="GoToUploadPage();">書類アップロード画面へ​</button>
                </div>
            </div>
            
            <!--choose and unchoose arrow-->
            <div style="width : 10%; margin-top :7%">
                <button type="button" class="btn btn-default" onclick="AddToList();"><span class="glyphicon glyphicon-arrow-left"></span> 追加</button>
                <div style="height :10px"></div>
                <button type="button" class="btn btn-default" onclick="RemoveFromList();">削除 <span class="glyphicon glyphicon-arrow-right"></span></button>
            </div>
            
            <!--modellingcompanylist-->
            <div style="width : 50%">
                <div class="selectModellingCompany">
                    <label>モデリング業者一覧</label>
                    <div class="listOfModellingCompany">
                        <table class="table table-bordered" id="tblModellingCompany">
                            <tbody>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
<script>
    
</script>
@endsection