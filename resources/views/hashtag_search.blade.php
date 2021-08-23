@extends('layouts.baselayout')
@section('title', 'CCC - Hashtag Search')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script type="text/javascript" src="/iPD/public/js/hashtag_search.js"></script>
<script type="text/javascript" src="/iPD/public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="/iPD/public/js/jquery.multiselect.js"></script>
<link rel="stylesheet" href="/iPD/public/css/jquery.hashtags.search.css">
<script type="text/javascript" src="/iPD/public/js/select2/select2.min.js"></script>
<link rel="stylesheet" href="/iPD/public/css/select2.min.css">
<!--<script type="text/javascript" src="/iPD/public/js/library/jquery.hashtag.search.js"></script>-->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Caret.js/0.3.1/jquery.caret.js"></script>

<style>
#hashtag_popup {
    display: none;
    z-index:10;
    position: absolute;
    height:auto !important;
}
#hashtag_popup .list-group-item{
	display: block;
	padding:5px;
	color:#1a0d00;
	margin-bottom: 1px solid #ddd !important;
    background-color: #dce6f8;
    border: 1px solid #ddd;
　　
}
#hashtag_popup .list-group-item:hover{
	background:whitesmoke;
}
.main-content{
	margin: 0 auto;
	margin-bottom : 3%;
	width:80%;
	display : center;
}
.search_hashtag{
    margin-top : 2%;
    display : flex;
    justify-content: space-around;
}
.hashtag1, .hashtag2{
    border : 1px solid #ccc;
    border-radius : 0 4px 4px 0;
    height: 34px;
}
.closeSign1, .closeSign2{
    position: absolute;
    /* float: right; */
    top: 9px;
    right: 4px;
    color: #ccc;
}
.search_result{
    width: 100%;
    height: 500px;
    border-top: 1px solid #ccc;
}
.post{
    margin-top : 3%;
}
.tab{
    display : flex;
    justify-content: space-evenly;
}
.tab-item{
    border-bottom: 6px solid #3737d9;
    margin-bottom: 0;
    width: 100px;
    text-align: center;
    color: blue;
}

.scrollableTable{
    display: flex;
    overflow: auto;
    white-space: nowrap;
    max-height: 450px;
    width: 100%;
    justify-content: space-between;
}
#tblPosts{
    height :100%;
    position: sticky;
}

#tblPosts thead tr th{
    text-align : center;
    font-size: 16px;
    color: white;
    background: black;
}
thead th { position: sticky; top: 0; }

</style>
@endsection

@section('content')
@include('layouts.loading')
<div class="main-content">
    <input type="hidden" id="hidLoginID" name="hidLoginID" value="{{Session::get('login_user_id')}}"/>
    <h3>ハッシュタグ検索</h3>
    <div class="search_hashtag">
        <div class="input-group" style="width : 80%">
            <span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>
            <select class='hashtag1 form-control' multiple="multiple">
                
            </select>
            
            
        </div>
        
        <select class='form-control' id="search_logic" style="width : 10%">
            <option value='1'>AND</option>
            <option value='0'>OR</option>
        </select>
        <button class="btn btn-primary" onclick="SearchReport();">Search</button>
    </div>
    
    <hr>
    <div class='tab'>
        <h4 class="tab-item" id="tab1">報告</h4>
        <h4 id="tab2">画像</h4>
    </div>
    <div class="search_result">
        <div class="post">
            <div id='record' ><span id='display_record'></span> 件表示中 (<span id='total_record'></span> 件中)</div>
            <div class="scrollableTable">
                <table id="tblPosts" class="table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th width="15%">氏名</th>
                            <th width="25%">組織名</th>
                            <th width="50%">内容</th>
                            <th width="10%">日付</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                    
                </table>
            </div>
            
            <div style="text-align:center;margin-top:10px;">
    		    <botton type="button" id="readPartOfAllstore" onclick="getResultByRowIncrease()" class="btn btn-default" style="margin:0 auto;border: none;">
    		        <img src='../public/image/down_arrow_blue.png' style="" alt='' height='18' width='18' />
    		        <span>もっと読み込む</span>
    		    </botton>
    		    <botton type="button" id="readAllAllstore" onclick="getAllResult()" class="btn btn-default" style="margin:0 auto;border: none;">
    		        <img src='../public/image/down_arrow_blue.png' style="" alt='' height='18' width='18' />
    		        <span>すべて読み込む</span>
    		    </botton>
    		</div>
            
        </div>
        
		
        <div class = "images" style="display :none">
            <div class="alert alert-danger" style="margin-top : 3%">
              <strong>There is no images.</strong> 
            </div>
        </div>
    </div>
    
    
    <div id="hashtag_popup"></div>
    
</div>

    
    
    

@endsection