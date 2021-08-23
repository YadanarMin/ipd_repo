@extends('layouts.baselayout')
@section('title', 'CCC - tekkkin volume search')

@section('head')
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>
<script type="text/javascript" src="../public/js/tekkin.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/js/select2.min.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<link rel="stylesheet" href="../public/css/tekkin.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
<style>
</style>
@endsection

@section('content')
@include('layouts.loading')
<div class="main-content">
    
    <input type="hidden" id="hidLoginID" name="hidLoginID" value="{{Session::get('login_user_id')}}"/>

    <div id="formulaPopup" class="popupOverlay popupOverlayBackground">
    	<div class="popup popupBackground" id="formulaPopupSize">
    		<div class="formulaHeader" style="width:100%;">
    			<h3 style="text-align:center;text-decoration:underline;">鉄筋計算式<span id="roomName"></span></h3>
    			<a class="close" href="javascript:void(0);" onClick ="ClosePopup()"  style="margin-top:5px;">&times;</a>
    		</div></br>
    		<div  style="padding-bottom:0px;">		
          <ul>
            <li>
              構造フレーム
              <ul>
                <li>
                  始端
                  <div style="float:right;">
                    <input type="text" id="txtBeamHead" size="5" readonly="readonly" >　ｘ　
                    <input type="text" id="txtBeamHeadMul" size="5" value="1.2">　＝　
                    <input type="text" name="txtBeamHeadTotal" id="txtBeamHeadTotal" size="10px"/>&nbsp; t
                  </div>
                  <p>            
                  [ (始端 上主筋 太径の面積 ｘ カット長 / 3)　ｘ (始端 上主筋 1段筋太筋本数 ＋ 始端 上主筋 2段筋太筋本数) ]<br>
                  ＋[ (始端 下主筋 太径の面積 ｘ カット長 / 3)　ｘ (始端 下主筋 1段筋太筋本数 ＋ 始端 下主筋 2段筋太筋本数) ]<br>
                  ＋[（始端 肋筋径の面積 ｘ　(B＋H)ｘ２)　ｘ　(カット長 / 始端 肋筋ピッチ / 3) ]              
                  </p>
                  
                </li>
                <li>
                  中央
                  <div style="float:right;">
                    <input type="text" name="txtBeamCenter" id="txtBeamCenter" size="10px"/>&nbsp; t
                  </div>
                  <p>            
                  [ (中央 上主筋 太径の面積 ｘ カット長 / 3)　ｘ (中央 上主筋 1段筋太筋本数 ＋ 中央 上主筋 2段筋太筋本数) ]<br>
                  ＋[ (中央 下主筋 太径の面積 ｘ カット長 / 3)　ｘ (中央 下主筋 1段筋太筋本数 ＋ 中央 下主筋 2段筋太筋本数) ]<br>
                  ＋[ (中央 肋筋径の面積 ｘ　(B＋H)ｘ２)　ｘ　(カット長 / 中央 肋筋ピッチ / 3) ]
                  </p>
                </li>
                <li>
                  終端
                  <div style="float:right;">
                    <input type="text" id="txtBeamBottom" size="5" readonly="readonly" >　ｘ　
                    <input type="text" id="txtBeamBottomMul" size="5" value="1.2">　＝　
                    <input type="text" name="txtBeamBottomTotal" id="txtBeamBottomTotal" size="10px"/>&nbsp; t
                  </div>
                  <p>            
                  [ (終端 上主筋 太径の面積 ｘ カット長 / 3)　ｘ (終端 上主筋 1段筋太筋本数 ＋ 終端 上主筋 2段筋太筋本数) ]<br>
                  ＋[ (終端 下主筋 太径の面積 ｘ カット長 / 3)　ｘ (終端 下主筋 1段筋太筋本数 ＋ 終端 下主筋 2段筋太筋本数) ]<br>
                  ＋[ (終端 肋筋径の面積 ｘ　(B＋H)ｘ２)　ｘ　(カット長 / 終端 肋筋ピッチ / 3) ]<br>   
                  <span style="float:right;color:darkblue;">構造フレーム合計重量：<input type="text" id="txtBeamTotalDisplay" size="10px"/>&nbsp; t </span>   
                  <br>&nbsp;    
                  </p>
                </li>
              </ul>
            </li>
            <li>
              構造柱
              <ul>
                <li>
                  柱頭 【 長さ　⇒　( 容積　/ (W ｘ　Ｄ) ) 】
                  <div style="float:right;">
                  　<input type="text" id="txtColumnHead" size="5" readonly="readonly" >　ｘ　
                  　<input type="text" id="txtColumnHeadMul" size="5" value="1">　＝　
                    <input type="text" name="txtColumnHeadTotal" id="txtColumnHeadTotal" size="10px"/>&nbsp; t
                  </div>
                  <p>
                  [ (柱頭 主筋太径の面積　ｘ　長さ / 2)　ｘ　( 柱頭 主筋X方向1段太筋本数　<br>
                    ＋　柱頭 主筋X方向2段太筋本数 ＋柱頭 主筋Y方向1段太筋本数　＋　柱頭 主筋Y方向2段太筋本数) ] <br>
                  ＋[ (柱頭 帯筋径の面積ｘ(W+D)ｘ２) x (長さ / 柱頭 帯筋ピッチ / 2) ]             
                  </p>
                </li>
                <li>
                  柱脚 【 長さ　⇒　( 容積　/ (W ｘ　Ｄ) ) 】
                  <div style="float:right;">
                    <input type="text" id="txtColumnBottom" size="5" readonly="readonly" >　ｘ　
                  　<input type="text" id="txtColumnBottomMul" size="5" value="1.1">　＝　
                    <input type="text" name="txtColumnBottomTotal" id="txtColumnBottomTotal" size="10px"/>&nbsp; t
                  </div>
                  <p>
                  [ (柱脚 主筋太径の面積　ｘ　長さ / 2)　ｘ　(柱脚 主筋X方向1段太筋本数　<br>
                    ＋　柱脚 主筋X方向2段太筋本数 ＋柱脚 主筋Y方向1段太筋本数　＋　柱脚 主筋Y方向2段太筋本数) ] <br>
                  ＋[ (柱脚 帯筋径の面積ｘ(W+D)ｘ２) x (長さ / 柱脚 帯筋ピッチ / 2) ]<br>
                  <span style="float:right;color:darkblue;">構造柱合計重量：<input type="text" id="txtColumnTotalDisplay" size="10px"/>&nbsp; t</span>   
                  <br>&nbsp;  
                  </p>
                </li>
              </ul>
            </li>
            <li>
              構造基礎
              <ul>
                <li>
                  上端筋
                  <div style="float:right;">
                    <input type="text" id="txtFoundationHead" size="5" readonly="readonly" >　ｘ　
                    <input type="text" id="txtFoundationHeadMul" size="5" value="1">　＝　
                    <input type="text" name="txtFoundationHeadTotal" id="txtFoundationHeadTotal" size="10px"/>&nbsp; t</sup>
                  </div>
                  <p>              
                  (上端筋_X方向_鉄筋径の面積 ｘ (W+H+H) ）ｘ　上端筋_X方向_鉄筋本数)<br>
                  　＋　(上端筋_Y方向_鉄筋径の面積 ｘ (D+H+H)ｘ　上端筋_Y方向_鉄筋本数)
                  </p>
                </li>
                <li>
                  下端筋
                  <div style="float:right;">
                    <input type="text" id="txtFoundationBottom" size="5" readonly="readonly" >　ｘ　
                    <input type="text" id="txtFoundationBottomMul" size="5" value="1">　＝　
                    <input type="text" name="txtFoundationBottomTotal" id="txtFoundationBottomTotal" size="10px"/>&nbsp; t
                  </div>
                  <p>
                  (下端筋_X方向_鉄筋径の面積 ｘ (W+H+H) ）ｘ　下端筋_X方向_鉄筋本数)<br>
                  　＋　(下端筋_Y方向_鉄筋径の面積 ｘ (D+H+H))ｘ　下端筋_Y方向_鉄筋本数)
                  <span style="float:right;color:darkblue;">構造基礎合計重量：<input type="text" id="txtFoundationTotalDisplay" size="10px"/>&nbsp; t</span>   
                  <br><br>&nbsp;  
                  </p>
                </li>
              </ul>
            </li>
            <p style="text-align:center;color:purple;font-weight: bold;">
            鉄筋合計重量　⇒　<span id="totalVolume"></span>　&nbsp; t </br>
           　　　　　　　
            </p>          　 
          </ul>
    
    		</div>
    	</div>
    </div> 

    <div id="mainarea">
        <div id="maincontent">
            <h3>鉄筋重量管理</h3>
            <div style="display:flex;">  
            
                <select id="item" multiple> 
                </select>&nbsp;&nbsp;&nbsp; 
                
               <input type="button" class="btn btn-primary" name="btnTekkin" id="btnTekkin" value="ShowData" onClick="DisplayTekkinData()"/>&nbsp;&nbsp;&nbsp;
              <!-- <input type="button" class="btn btn-primary" name="btnTekkinPopup" id="btnTekkinPopup" value="ShowPopup" onClick="DisplayTekkinPopup()"/>-->&nbsp;&nbsp;&nbsp;
               <input type="button" class="btn btn-primary" name="btnTekkinExcel" id="btnTekkinExcel" value="Download Excel" onClick="DownloadTekkinExcel()"/>
            </div>
            <br>

            <div id="tekkinData">
            </div>
        </div>
    </div>
   
</div>
@endsection