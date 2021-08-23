@extends('layouts.baselayout')
@section('title', 'CCC - Custom Document Creation')

@section('head')
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/customDocumentCreation.js"></script>
<!--<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">-->
<!--<script src="http://code.jquery.com/jquery-1.9.1.js"></script>-->
<!--<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>-->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
<script src='http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js' ></script>
<link  type='text/css' rel='stylesheet'  href='http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/ui-lightness/jquery-ui.css' />
<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css"/>
<!--<script src="http://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>-->

<style>
.outer-border{
    border:1px solid whitesmoke;
    min-height:70vh;
    height:auto;
    margin:0 30px 6vh 30px;
}
#drop-area label{
    border:1px solid black;
    padding:2px 5px 2px 5px;
}
#drop-area .drag{
  margin-bottom: 7px;
  position: absolute;
}
ul{
    padding:2px;
}
ul li{
 list-style:none;
 cursor: move;
 padding:0;
 margin-bottom:10px;
 text-align:left;
 position:relative;

}
label,input,textarea{
 cursor:move;
}
label{
    <!--position:relative;-->
}
.custom-lbl{
 width:125px;
 margin:0;
}
.custom-lbl-val{
 width:125px;
 margin:0;
 height: 26px;
 font-weight:normal;
}
.custom-panel{
    cursor:move;
    width:800px ;
    height:200px;
    border:1px solid whitesmoke;
}
.custom-table{
    cursor:move;
    width:800px ;
    height:200px;
    border:1px solid whitesmoke;
}
.custom-flex-lbl{
    display: flex;
}
.flex-row{
    flex-direction:row;
}
.flex-column{
    flex-direction:column;
}

.custom-menu {
    display: none;
    z-index:1000;
    position: absolute;
    background-color:#fff;
    border: 1px solid #ddd;
    overflow: hidden;
    width: 120px;
    white-space:nowrap;
    font-family: sans-serif;
    -webkit-box-shadow: 2px 2px 7px 0px rgba(50, 50, 50, 0.5);
    -moz-box-shadow:    2px 2px 7px 0px rgba(50, 50, 50, 0.5);
    box-shadow:         2px 2px 7px 0px rgba(50, 50, 50, 0.5);
}

.custom-menu li {
    padding: 5px 10px;
    margin-bottom:0;
}

.custom-menu li:hover {
    background-color: #4679BD;
    color: #fff;
    cursor: pointer;
}

.custom-header{
    background:#5bc0de;
    padding:5px;
    color:#fff;
    width:100%;
}
.tableDesign{
    width: 125px;
    text-align: center;
}
#table td,#table th {
    border-collapse: collapse;
    border:1px solid white;
}
.tblUser th {
    padding: 5px 0 5px 0;
    background-color: #1a0d00;
    color: white;
    border: 1px solid;
    text-align: center;
    min-height:40px;
}
.tblUser td {
    padding-left: 10px;
}
</style>

@endsection

@section('content')
<div align="center">
    <input type="hidden" id="hidLoginID" name="hidLoginID" value="{{Session::get('login_user_id')}}"/>
        
       <h3>カスタム書類作成</h3>
        <hr> 
        
        <div style="display:flex;width:5355px;">
            <div id="custom-items" class="col-md-2 panel outer-border" style="width:256px;margin: 0 0px 6vh 30px;">
                <!--container-->
                <ul>
                    <li><label class="custom-header">コンテナ</label></li>
                   
                    <li>
                        <ul><li>
                            <div class="ipd-panel drag " style="z-index: 0;">panel</div>
                            <div style="display:flex;justify-content:flex-end;">
                                <div class="ipd-table drag " style="z-index: 0;margin-right:auto;">table</div>
                                <div style="display:flex;">
                                    <select class="form-control input-sm" id="tableRowNumSelect" style="width:55px;height:22px;font-size:1px;">
                                        <option value="" selected>-</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        <option value="9">9</option>
                                        <option value="10">10</option>
                                    </select>&nbsp;行
                                    &nbsp;&nbsp;
                                    <select class="form-control input-sm" id="tableColumnNumSelect" style="width:55px;height:22px;font-size:1px;">
                                        <option value="" selected>-</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        <option value="9">9</option>
                                        <option value="10">10</option>
                                    </select>&nbsp;列
                                </div>
                            </div>
                            <ul id="table-type" style="justify-content:flex-end;">
                                <label class="custom-ul-label" style="margin:0 0 0 55px;">テーブル形式を選択</label>
                                <li style="margin:0 0 0 90px;"><input type="radio" name="tableType" value="1">&nbsp;&nbsp;PJコードが同行</li>
                                <li style="margin:0 0 0 90px;"><input type="radio" name="tableType" value="2">&nbsp;&nbsp;PJコードが同列</li>
                                <li style="margin:0 0 0 90px;"><input type="radio" name="tableType" value="3">&nbsp;&nbsp;PJコードなし</li>
                            </ul>
                            
                        </li></ul>
                        
                    </li>
                </ul>
    
                <hr>
                <ul>
                    <li><label class="custom-header">カスタム条件</label></li>
                    <li><ul style="width: 100%; height: 150px; overflow: auto">
                        <li><label id="custom_limit" class="drag">○年○月○日 ～ ○年○月○日</label></li>
                        <li><label id="custom_before" class="drag">○年○月○日以前</label></li>
                        <li><label id="custom_after" class="drag">○年○月○日以降</label></li>
                        <li><label id="custom_time" class="drag">年月日</label></li>
                        <li><label id="custom_year" class="drag">○年</label></li>
                        <li><label id="custom_month" class="drag">○月</label></li>
                        <li><label id="custom_day" class="drag">○日</label></li>
                        <li><label id="custom_nami" class="drag">～</label></li>
                        <li><label id="custom_pre" class="drag">以前</label></li>
                        <li><label id="custom_next" class="drag">以降</label></li>
                    </ul></li>
                </ul>
    
                <hr>
                <ul>
                    <li><label class="custom-header">全店物件項目</label></li>
                    <li><ul style="width: 100%; height: 315px; overflow: auto">
                        <li><label id="branch_list" class="drag">〇〇支店</label></li>
                        <li><label id="a_pj_code" class="drag">PJコード</label></li>
                        <li><label id="a_kouji_kikan_code" class="drag">工事基幹コード</label></li>
                        <li><label id="a_shiten" class="drag">支店</label></li>
                        <li><label id="a_kakudo" class="drag">確度(A)</label></li>
                        <li><label id="a_pj_name" class="drag">プロジェクト名称(A)</label></li>
                        <li><label id="a_kouji_kubun" class="drag">工事区分</label></li>
                        <li><label id="a_kouji_type" class="drag">工事区分名</label></li>
                        <li><label id="a_youto1" class="drag">建物用途1</label></li>
                        <li><label id="a_youto2" class="drag">建物用途2</label></li>
                        <li><label id="a_sekou_basyo" class="drag">施工場所</label></li>
                        <li><label id="a_sekkei_state" class="drag">設計State</label></li>
                        <li><label id="a_sekkei" class="drag">設計</label></li>
                        <li><label id="a_kouzou" class="drag">構造(A)</label></li>
                        <li><label id="a_kaisuu" class="drag">階数(A)</label></li>
                        <li><label id="a_tijo" class="drag">地上(A)</label></li>
                        <li><label id="a_tika" class="drag">地下(A)</label></li>
                        <li><label id="a_ph" class="drag">PH(A)</label></li>
                        <li><label id="a_nobe_menseki" class="drag">延べ面積(A)</label></li>
                        <li><label id="a_tyakkou" class="drag">着工</label></li>
                        <li><label id="a_syunkou" class="drag">竣工</label></li>
                        
                        <li><label id="b_pj_state" class="drag">プロジェクト稼働状況</label></li>
                        <li><label id="b_sekkei_state" class="drag">設計段階</label></li>
                        <li><label id="b_sekou_state" class="drag">施工段階</label></li>
                        <li><label id="b_jiyuu_kinyuu" class="drag">自由記入欄</label></li>
                        <li><label id="b_pj_name" class="drag">プロジェクト名称(B)</label></li>
                        <li><label id="b_tmp_pj_name" class="drag">BIM360プロジェクト名称</label></li>
                        <li><label id="b_shiten" class="drag">支店(B)</label></li>
                        <li><label id="b_kakudo" class="drag">確度(B)</label></li>
                        <li><label id="b_hattyuusya" class="drag">発注者</label></li>
                        <li><label id="b_sekkeisya1" class="drag">設計者(B)</label></li>
                        <li><label id="b_sekkeisya2" class="drag">設計者(B)支店</label></li>
                        <li><label id="b_sekou_basyo" class="drag">施工場所(B)</label></li>
                        <li><label id="b_youto" class="drag">用途(B)</label></li>
                        <li><label id="b_kouzou" class="drag">構造(B)</label></li>
                        <li><label id="b_kaisuu" class="drag">階数(B)</label></li>
                        <li><label id="b_tika" class="drag">地下(B)</label></li>
                        <li><label id="b_tijo" class="drag">地上(B)</label></li>
                        <li><label id="b_ph" class="drag">PH(B)</label></li>
                        <li><label id="b_nobe_menseki" class="drag">延べ面積(B)</label></li>
                        <li><label id="b_tousuu" class="drag">棟数</label></li>
                        
                        <li><label id="b_syotyou" class="drag">工事事務所所長_氏名</label></li>
                        <li><label id="b_kouji_jimusyo" class="drag">工事事務所_組織</label></li>
                        <li><label id="b_kouji_katyou" class="drag">工事部担当者_氏名</label></li>
                        <li><label id="b_kouji_buka" class="drag">工事部担当者_組織</label></li>
                        <li><label id="b_eigyou_tantousya" class="drag">営業担当者_氏名</label></li>
                        <li><label id="b_eigyou_tantoubu" class="drag">営業担当者_組織</label></li>
                        
                        <li><label id="b_isyou_sekkei" class="drag">意匠設計担当者_氏名</label></li>
                        <li><label id="b_isyou_syozoku" class="drag">意匠設計担当者_組織</label></li>
                        <li><label id="b_isyou_model" class="drag">意匠モデラー_氏名</label></li>
                        <li><label id="b_isyou_model_syozoku" class="drag">意匠モデラー_組織</label></li>
                        <li><label id="b_kouzou_sekkei" class="drag">構造設計担当者_氏名</label></li>
                        <li><label id="b_kouzou_syozoku" class="drag">構造設計担当者_組織</label></li>
                        <li><label id="b_kouzou_model" class="drag">構造モデラー_氏名</label></li>
                        <li><label id="b_kouzou_model_syozoku" class="drag">構造モデラー_組織</label></li>
                        <li><label id="b_setubi_kuutyou_sekkei" class="drag">設備空調担当者_氏名</label></li>
                        <li><label id="b_setubi_kuutyou_syozoku" class="drag">設備空調担当者_組織</label></li>
                        <li><label id="b_setubi_kuutyou_model" class="drag">設備空調モデラー_氏名</label></li>
                        <li><label id="b_setubi_kuutyou_model_syozoku" class="drag">設備空調モデラー_組織</label></li>
                        <li><label id="b_setubi_eisei_sekkei" class="drag">設備衛生担当者_氏名</label></li>
                        <li><label id="b_setubi_eisei_syozoku" class="drag">設備衛生担当者_組織</label></li>
                        <li><label id="b_setubi_eisei_model" class="drag">設備衛生モデラー_氏名</label></li>
                        <li><label id="b_setubi_eisei_model_syozoku" class="drag">設備衛生モデラー_組織</label></li>
                        <li><label id="b_setubi_denki_sekkei" class="drag">設備電気担当者_氏名</label></li>
                        <li><label id="b_setubi_denki_syozoku" class="drag">設備電気担当者_組織</label></li>
                        <li><label id="b_setubi_denki_model" class="drag">設備電気モデラー_氏名</label></li>
                        <li><label id="b_setubi_denki_model_syozoku" class="drag">設備電気モデラー_組織</label></li>
                        <li><label id="b_ss_designer_name" class="drag">生産設計担当者_氏名</label></li>
                        <li><label id="b_ss_designer_dept" class="drag">生産設計担当者_組織</label></li>
                        <li><label id="b_ss_modeler_name" class="drag">生産設計モデラー_氏名</label></li>
                        <li><label id="b_ss_modeler_dept" class="drag">生産設計モデラー_組織</label></li>
                        
                        <li><label id="b_sekou_tantou" class="drag">施工管理担当者_氏名</label></li>
                        <li><label id="b_sekou_syozoku" class="drag">施工管理担当者_組織</label></li>
                        <li><label id="b_seisan_modeler_name" class="drag">生産モデラー_氏名</label></li>
                        <li><label id="b_seisan_modeler_dept" class="drag">生産モデラー_組織</label></li>
                        <li><label id="b_seisan_gijutu_tantou" class="drag">生産技術担当者_氏名</label></li>
                        <li><label id="b_seisan_gijutu_syozoku" class="drag">生産技術担当者_組織</label></li>
                        <li><label id="b_sekisan_mitumori_tantou" class="drag">積算担当者_氏名</label></li>
                        <li><label id="b_sekisan_mitumori_syozoku" class="drag">積算担当者_組織</label></li>
                        <li><label id="b_bim_maneka_tantou" class="drag">BIMマネ課担当者_氏名</label></li>
                        <li><label id="b_bim_maneka_syozoku" class="drag">BIMマネ課担当者_組織</label></li>
                        <li><label id="b_ipd_center_tantou" class="drag">iPDセンター担当者_氏名</label></li>
                        <li><label id="b_ipd_center_syozoku" class="drag">iPDセンター担当者_組織</label></li>
                        <li><label id="b_partner_company" class="drag">協力会社担当者_氏名</label></li>
                        <li><label id="b_partner_company_dept" class="drag">協力会社担当者_組織</label></li>
                        <li><label id="b_bim_m" class="drag">BIMマネージャー_氏名</label></li>
                        <li><label id="b_bim_manager_dept" class="drag">BIMマネージャー_組織</label></li>
                        <li><label id="b_bim_coordinator_tantou" class="drag">BIMコーディネーター_氏名</label></li>
                        <li><label id="b_bim_coordinator_syozoku" class="drag">BIMコーディネーター_組織</label></li>
                        
                        <li><label id="b_hattyuu_keitai_kentiku" class="drag">建築工事発注形態</label></li>
                        <li><label id="b_hattyuu_keitai_setubi" class="drag">設備工事発注形態</label></li>
                        
                        <li><label id="b_nyuusatu_jiki" class="drag">入札start</label></li>
                        <li><label id="b_nyuusatu_kettei_jiki" class="drag">入札end</label></li>
                        <li><label id="b_koutei_kihonsekkei_start" class="drag">基本設計start</label></li>
                        <li><label id="b_koutei_kihonsekkei_end" class="drag">基本設計end</label></li>
                        <li><label id="b_koutei_jissisekkei_start" class="drag">実施設計start</label></li>
                        <li><label id="b_koutei_jissisekkei_end" class="drag">実施設計end</label></li>
                        <li><label id="b_koutei_sekkei_model_start" class="drag">設計モデル作成start</label></li>
                        <li><label id="b_koutei_sekkei_model_end" class="drag">設計モデル作成end</label></li>
                        <li><label id="b_koutei_kakunin_sinsei_start" class="drag">確認申請start</label></li>
                        <li><label id="b_koutei_kakunin_sinsei_end" class="drag">確認申請end</label></li>
                        <li><label id="b_koutei_sekisan_model_tougou_start" class="drag">積算見積モデル統合・追記修正start</label></li>
                        <li><label id="b_koutei_sekisan_model_tougou_end" class="drag">積算見積モデル統合・追記修正end</label></li>
                        <li><label id="b_koutei_kouji_juujisya_kettei_start" class="drag">工事従事者決定start</label></li>
                        <li><label id="b_koutei_kouji_juujisya_kettei_end" class="drag">工事従事者決定end</label></li>
                        <li><label id="b_koutei_genba_koutei_kettei_start" class="drag">現場工程決定start</label></li>
                        <li><label id="b_koutei_genba_koutei_kettei_end" class="drag">現場工程決定end</label></li>
                        <li><label id="b_koutei_kouji_start" class="drag">工事start</label></li>
                        <li><label id="b_koutei_kouji_end" class="drag">工事end</label></li>
                        <li><label id="b_handover_start" class="drag">引渡しstart</label></li>
                        <li><label id="b_handover_end" class="drag">引渡しend</label></li>
                        
                        <li><label id="b_modeling_state" class="drag">モデリング会社区分</label></li>
                        <li><label id="b_bikou" class="drag">備考1(B)</label></li>
                        <li><label id="c_bikou" class="drag">備考2(C)</label></li>
                        <li><label id="c_order_status" class="drag">受注状況</label></li>
                        <li><label id="c_additional_item" class="drag">追加項目</label></li>
                    </ul></li>
                    
                </ul>
    
            </div>  
        
        
            <div id="custom-icons" class="col-md-1" style="width:100px;">
                <!--<a href="{{ url('test/mail') }}" class="pop" >mail</a>-->
                <button type="button" class="btn btn-sm" style="width:70px;" onclick="LoadFormatData()">
                  <span class="glyphicon glyphicon-ok"></span> Load
                </button>
                <hr>
                <button type="button" class="btn btn-sm" style="width:70px;" onclick="SaveFormat()">
                  <span class="glyphicon glyphicon-save"></span> Save
                </button>
                <hr>
                <button type="button" class="btn btn-sm ui-droppable" style="width:70px;height:632px;" id="trash">
                    <span class="glyphicon glyphicon-trash" style="margin-right:5px;"></span>Trash
                </button>
            </div>
        
            <div id="drop-area" class="col-md-8 panel outer-border drop ui-droppable" style="width:5000px;height:5000px;margin:0;">
                
            </div>
        </div>
        
        <!--<div id="table"></div>-->
        <div class="addable-contents"></div>
        <!--<table id="tblUser" align="center">-->
        <!--    <thead>-->
        <!--    </thead>-->
        <!--    <tbody>-->
        <!--    </tbody>-->
        <!--</table>-->
        
        <!--right click anywhere-->
        <ul class='custom-menu'>
          <li data-action = "hide_one">Hide Label</li>
          <li data-action = "show_one">Show Label</li>
          <li data-action = "hide_all">Hide All Labels</li>
          <li data-action = "show_all">Show All Labels</li>
          <li data-action = "switch_one_style">Switch Style</li>
          <li data-action = "switch_all_style">Switch All Style</li>
        </ul>

    </div>


@endsection

