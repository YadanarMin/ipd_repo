@extends('layouts.baselayout')
@section('title', 'CCC - Top')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>
<script type="text/javascript" src="../public/js/accessLog.js"></script>

<style>
.h5-title{
    text-align: center;
    color:black;
    font-size: 18px;
    position:relative;
    text-transform:uppercase;
    z-index:100 ;
    margin: 50px 0px 50px 0;
}

.h5-title::before{
    /*left: 100px;*/
    content: "";
    display: block;
    width: 40px;
    height: 40px;
    background: #a5d1ff;
    position: absolute;
    left: 50%;
    margin-left: -20px;
    margin-top:-10px;
    transform: rotate(45deg);
    text-align: center;
    z-index:-100;
}

.service-content{
    justify-content: center;
    text-align: center;
    margin:0 auto;
    line-height: 15px;
    display: flex; 
    flex-wrap:wrap ;
    display: flex;
    max-width: 1200px;
    vertical-align: bottom;
    padding: 0px 15px 0px 15px;
}
ul li{
    margin: 10px 10px 60px 10px;  
    float:left;
    width:200px;
    height: 80px;
}
ul{
    list-style:none; 
}
.link-text{
    margin-top: 10px;
    position: absolute;
    text-align:center;
}
li a:link{
    text-decoration: none;
    color:black;
}

figure {
    display: inline-block;
}

figure img{
    width:30px;
}

figcaption {
    text-align: center;
    padding-top: 10px;
    color:#0d0d0d;
}
figcaption:hover {
    color:#ffa500;
}
.hyper{
    color:#a5d1ff;
    text-decoration:underline;
    cursor:pointer;
}
.popover-content {
    font-size: 11px;
    font-family:'メイリオ', 'Meiryo', sans-serif;
    /*background-color:#212529;
    color:#fff;*/
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
<main>

    <h5 class="h5-title" id="h5-title">Service</h5>
    <!--<input type="hidden" id="hidAuthorityID" name="hidAuthorityID" value="{{Session::get('authority_id')}}"/>-->
    <input type="hidden" id="hiddenLoginUser" name="hiddenLoginUser" value="{{ Session::get('userName')}}" />   
    <ul class="service-content" id="mainContent">
        
    @if($authority_data["show_model_storage"])
        <li>
            <a href="{{ url('forge/index') }}" onclick="saveAccessLog('モデルストレージ');" class="pop" data-toggle="popover" data-placement="bottom" data-content="モデルストレージ<a href='javascript:void(0)' class='hyper'>サービス説明</a>">
                <figure>
                    <img src="/iPD/public/image/JPG/jyamu.jpeg" alter="モデルストレージ"width="50px">
                    <figcaption>モデルストレージ</figcaption>
                </figure>
            </a>
        </li>
    @endif
    @if($authority_data["model_analysis"])
        <li>
            <a href="{{ url('dataPortal/projectSearchConsole') }}" onclick="saveAccessLog('モデル分析');" class="pop" data-toggle="popover" data-placement="bottom" data-content="モデル分析<a href='javascript:void(0)' class='hyper'>サービス説明</a>">
                <figure>
	                <img src="/iPD/public/image/JPG/原子力のフリーイラスト3.jpeg" alter="モデル分析"width="50px">
	                <figcaption>モデル分析</figcaption>
                </figure>
            </a>
        </li>
    @endif
    @if($authority_data["room_analysis"])
        <li>
            <a href="{{ url('dataPortal/roomInfoSearchConsole') }}" onclick="saveAccessLog('部屋データ分析');" class="pop" data-toggle="popover" data-placement="bottom" data-content="部屋データ分析<a href='javascript:void(0)' class='hyper'>サービス説明</a>">
                <figure>
                    <img src="/iPD/public/image/JPG/目的地アイコン3.jpeg" alter="部屋データ分析"width="50px">
                    <figcaption>部屋データ分析</figcaption>
                </figure>
            </a>
        </li>
    @endif
    @if($authority_data["project_management"])
        <li>
            <a href="{{ url('prjmgt/index') }}" onclick="saveAccessLog('プロジェクト管理');" class="pop" data-toggle="popover" data-placement="bottom" data-content="プロジェクト管理<a href='javascript:void(0)' class='hyper'>サービス説明</a>">
                <figure>
                    <img src="/iPD/public/image/JPG/作業員のアイコン.jpeg" alter="プロジェクト管理"width="50px">
                    <figcaption>プロジェクト管理</figcaption>
                </figure>
            </a>
        </li>
    @endif
    @if($authority_data["crane_save"])
        <li>
            <a href="{{ url('crane/save') }}" onclick="saveAccessLog('クレーン情報登録');" class="pop" data-toggle="popover" data-placement="bottom" data-content="クレーン情報登録<a href='javascript:void(0)' class='hyper'>サービス説明</a>">
                <figure>
                    <img src="/iPD/public/image/JPG/キャッシューカードのフリーアイコン.jpeg" alter="クレーン情報登録"width="50px">
                    <figcaption>クレーン情報登録</figcaption>
                </figure>
            </a>
        </li>
    @endif
    @if($authority_data["gantt_management"])
        <li>
            <a href="{{ url('gantt/processControll') }}" onclick="saveAccessLog('工程管理');" class="pop" data-toggle="popover" data-placement="bottom" data-content="工程管理<a href='javascript:void(0)' class='hyper'>サービス説明</a>">
                <figure>
                    <img src="/iPD/public/image/JPG/USBマークのアイコン素材.jpeg"art="工程管理"width="50px">
                    <figcaption>工程管理</figcaption>
                </figure>
            </a>
        </li>
    @endif
    @if($authority_data["all_store_info"])
        <li>
            <a href="{{ url('allstore/index') }}" onclick="saveAccessLog('全店物件データ');" class="pop" data-toggle="popover" data-placement="bottom" data-content="全店物件データ<a href='javascript:void(0)' class='hyper'>サービス説明</a>">
                <figure>
                    <img src="/iPD/public/image/JPG/分析アイコン.jpeg" alter="全店物件データ"width="50px">
                    <figcaption>全店物件データ</figcaption>
                </figure>
            </a>
        </li>
    @endif
    @if($authority_data["model_change_history"])
        <li>
            <a href="{{url('common/changedInfo') }}" onclick="saveAccessLog('モデル変更状況追跡');" class="pop" data-toggle="popover" data-placement="bottom" data-content="モデル変更状況追跡<a href='javascript:void(0)' class='hyper'>サービス説明</a>">
                <figure>
                    <img src="/iPD/public/image/JPG/ローディング中のアイコン1.jpeg" alter="モデル変更状況追跡"width="50px">
                    <figcaption>モデル変更状況追跡</figcaption>
                </figure>
            </a>
        </li>
    @endif
    @if($authority_data["addin_user_info"])
        <li>
            <a href="{{url('common/userInfo') }}" onclick="saveAccessLog('アドイン使用状況追跡');" class="pop" data-toggle="popover" data-placement="bottom" data-content="アドイン使用状況追跡<a href='javascript:void(0)' class='hyper'>サービス説明</a>">
                <figure>
                    <img src="/iPD/public/image/JPG/会員証のアイコン素材.jpeg" alter="アドイン使用状況追跡"width="50px">
                    <figcaption>アドイン使用状況追跡</figcaption>
                </figure>
            </a>
        </li>
    @endif   
    @if($authority_data["crane_search"])
        <li>
            <a href="{{ url('crane/search') }}" onclick="saveAccessLog('クレーン情報検索');" class="pop" data-toggle="popover" data-placement="bottom" data-content="クレーン情報検索<a href='javascript:void(0)' class='hyper'>サービス説明</a>">
                <figure>
                   <img src="/iPD/public/image/JPG/クレーンアイコン.jpeg" alter="クレーン情報検索"width="50px">
                   <figcaption>クレーン情報検索</figcaption>
                </figure>
            </a>
        </li>
    @endif
    @if($authority_data["tekkin_volume"])
        <li>
            <a href="{{ url('forge/tekkin') }}" onclick="saveAccessLog('鉄筋重量管理');" class="pop" data-toggle="popover" data-placement="bottom" data-content="鉄筋重量管理<a href='javascript:void(0)' class='hyper'>サービス説明</a>">
                <figure>
                    <img src="/iPD/public/image/JPG/グラフアイコン.jpeg"art="鉄筋重量管理"width="50px">
                    <figcaption>鉄筋重量管理</figcaption>
                </figure>
            </a>
        </li>
    @endif
    @if($authority_data["room_siage_management"])
        <li>
            <a href="{{ url('common/saveRoom') }}" onclick="saveAccessLog('部屋仕上データ管理');">
                <figure>
                    <img src="/iPD/public/image/JPG/位置バルーンのフリー素材.jpeg" alter="部屋仕上データ管理"width="50px">
                    <figcaption>部屋仕上データ管理<span style="color:red;">【準備中】</span></figcaption>
                </figure>
            </a>
        </li>
    @endif
    @if($authority_data["create_kozo_type"])
        <li>
            <a href="#">
                <figure>
                    <img src="/iPD/public/image/JPG/リンク削除ボタン.jpeg" alter="構造タイプ作成"width="50px">
                    <figcaption>構造タイプ作成<span style="color:red;">【準備中】</span></figcaption>
                </figure>
            </a>
        </li>
    @endif
    @if($authority_data["model_family_analysis"])
        <li>
            <a href="#">
                <figure>
                    <img src="/iPD/public/image/JPG/立方体の無料素材2.jpeg" alter="モデルファミリ分析"width="50px">
                    <figcaption>モデルファミリ分析<span style="color:red;">【準備中】</span></figcaption>
                </figure>
            </a>
        </li>
    @endif
    @if($authority_data["kiso_kouji_management"])
        <li>
            <a href="#">
                <figure>
                    <img src="/iPD/public/image/JPG/ダウンロードのアイコン素材 その3.jpeg" alter="基礎工事データ管理"width="50px">
                    <figcaption>基礎工事データ管理<span style="color:red;">【準備中】</span></figcaption>
                </figure>
            </a>
        </li>
    @endif
    @if($authority_data["user_settings"])
        <li>
            <a href="{{ url('user/index') }}" onclick="saveAccessLog('ユーザー設定');" class="pop" data-toggle="popover" data-placement="bottom" data-content="ユーザー設定<a href='javascript:void(0)' class='hyper'>サービス説明</a>">
                <figure>
                    <img src="/iPD/public/image/user_settings.png"art="ユーザー設定"width="50px">
                    <figcaption>ユーザー設定</figcaption>
                </figure>
            </a>
        </li>
    @endif
    @if($authority_data["authority_settings"])
        <li>
            <a href="{{ url('user/authoritySettings') }}" onclick="saveAccessLog('権限設定');" class="pop" data-toggle="popover" data-placement="bottom" data-content="権限設定<a href='javascript:void(0)' class='hyper'>サービス説明</a>">
                <figure>
                    <img src="/iPD/public/image/JPG/鍵のクローズアイコン素材.jpeg"art="権限設定"width="50px">
                    <figcaption>権限設定</figcaption>
                </figure>
            </a>
        </li>
    @endif
    @if($authority_data["model_backup_settings"])
        <li>
            <a href="{{ url('admin/backup') }}" onclick="saveAccessLog('モデルバックアップ設定');" class="pop" data-toggle="popover" data-placement="bottom" data-content="モデルバックアップ設定<a href='javascript:void(0)' class='hyper'>サービス説明</a>">
                <figure>
                    <img src="/iPD/public/image/JPG/葉っぱのエコアイコン.jpeg" alter="モデルバックアップ設定"width="50px">
                    <figcaption>モデルバックアップ設定</figcaption>
                </figure>
            </a>
        </li>
    @endif
    @if($authority_data["model_save_settings"])
        <li>
            <a href="{{ url('admin/index') }}" onclick="saveAccessLog('CCC取込設定');"  class="pop" data-toggle="popover" data-placement="bottom" data-content="モデル自動保存設定<a href='javascript:void(0)' class='hyper'>サービス説明</a>">
                <figure>
                    <img src="/iPD/public/image/JPG/鍵のクローズアイコン素材.jpeg" alter="モデル自動保存設定"width="50px">
                    <figcaption>CCC取込設定</figcaption>
                </figure>
            </a>
        </li>
    @endif
    @if($authority_data["bim360_authority_settings"])
        <li>
            <a href="{{ url('bim360/index') }}" onclick="saveAccessLog('BIM360権限設定');" class="pop" data-toggle="popover" data-placement="bottom" data-content="BIM360権限設定<a href='javascript:void(0)' class='hyper'>サービス説明</a>">
                <figure>
                    <img src="/iPD/public/image/JPG/鍵のクローズアイコン素材.jpeg" alter="BIM360権限設定"width="50px">
                    <figcaption>BIM360権限設定</figcaption>
                </figure>
            </a>
        </li>
    @endif
    @if($authority_data["object_file_conversion"])
        <li>
            <a href="{{ url('OBJ/index') }}" class="pop" data-toggle="popover" data-placement="bottom" data-content="OBJファイル変換<a href='javascript:void(0)' class='hyper'>サービス説明</a>">
                <figure>
                    <img src="/iPD/public/image/JPG/リツイートアイコン.jpeg" alter="OBJファイル変換"width="50px">
                    <figcaption>OBJファイル変換</figcaption>
                </figure>
            </a>
        </li>
    @endif
    @if($authority_data["process_id_settings"])
        <li>
            <a href="{{ url('processMapping/index') }}" onclick="saveAccessLog('工程ID対応表');" class="pop" data-toggle="popover" data-placement="bottom" data-content="<a href='javascript:void(0)' class='hyper'>サービス説明</a>">
                <figure>
                    <img src="/iPD/public/image/JPG/分析アイコン.jpeg" alter="OBJファイル変換"width="50px">
                    <figcaption>工程ID対応表</figcaption>
                </figure>
            </a>
        </li>
    @endif
    @if($authority_data["case_report_settings"])
        <li>
            <a href="{{ url('project/report') }}" onclick="saveAccessLog('案件報告');" class="pop" data-toggle="popover" data-placement="bottom" data-content="案件報告<a href='javascript:void(0)' class='hyper'>サービス説明</a>">
                <figure>
                    <img src="/iPD/public/image/JPG/report.png" alter="案件報告"width="50px">
                    <figcaption>案件報告</figcaption>
                </figure>
            </a>
        </li>
    @endif
    @if($authority_data["ashiba_settings"])
        <li>
            <a href="{{ url('addin/calcAshiba') }}" onclick="saveAccessLog('足場算出');" class="pop" data-toggle="popover" data-placement="bottom" data-content="足場算出<a href='javascript:void(0)' class='hyper'>サービス説明</a>">
                <figure>
                    <img src="/iPD/public/image/JPG/分析アイコン.jpeg" alter="足場算出"width="50px">
                    <figcaption>足場算出</figcaption>
                </figure>
            </a>
        </li>
    @endif
    @if($authority_data["document_management"])
        <li>
            <a href="{{ url('document/management') }}" onclick="saveAccessLog('書類出力');" class="pop" data-toggle="popover" data-placement="bottom" data-content="書類出力<a href='javascript:void(0)' class='hyper'>サービス説明</a>">
                <figure>
                    <img src="/iPD/public/image/JPG/report.png" alter="書類出力"width="50px">
                    <figcaption>書類出力</figcaption>
                </figure>
            </a>
        </li>
    @endif
    @if($authority_data["kasetsu_settings"])
        <li>
            <a href="{{ url('addin/calcKasetsu') }}" onclick="saveAccessLog('重仮設算出');" class="pop" data-toggle="popover" data-placement="bottom" data-content="重仮設算出<a href='javascript:void(0)' class='hyper'>サービス説明</a>">
                <figure>
                    <img src="/iPD/public/image/JPG/分析アイコン.jpeg" alter="重仮設算出"width="50px">
                    <figcaption>重仮設算出</figcaption>
                </figure>
            </a>
        </li>
    @endif
    @if($authority_data["foreign_students_info"])
        <li>
            <a href="{{ url('foreignStudents/index') }}" onclick="saveAccessLog('留学生情報');" class="pop" data-toggle="popover" data-placement="bottom" data-content="留学生情報<a href='javascript:void(0)' class='hyper'>サービス説明</a>">
                <figure>
                    <img src="/iPD/public/image/user_settings.png" alter="留学生情報"width="50px">
                    <figcaption>留学生情報</figcaption>
                </figure>
            </a>
        </li>
    @endif
    @if($authority_data["partner_company_info"])
        <li>
            <a href="{{ url('partnerCompany/index') }}" onclick="saveAccessLog('協力会社管理');" class="pop" data-toggle="popover" data-placement="bottom" data-content="協力会社管理<a href='javascript:void(0)' class='hyper'>サービス説明</a>">
                <figure>
                    <img src="/iPD/public/image/JPG/USBマークのアイコン素材.jpeg"　alter="協力会社管理"　width="50px">
                    <figcaption>協力会社管理</figcaption>
                </figure>
            </a>
        </li>
    @endif
    
    @if($authority_data["partner_company_contact"])
        <li>
            <a href="{{ url('partnerCompanyContact/index') }}" onclick="saveAccessLog('パートナー会社連絡先管理');" class="pop" data-toggle="popover" data-placement="bottom" data-content="パートナー会社連絡先管理<a href='javascript:void(0)' class='hyper'>サービス説明</a>">
                <figure>
                    <img src="/iPD/public/image/JPG/USBマークのアイコン素材.jpeg"　alter="パートナー会社連絡先管理"　width="50px">
                    <figcaption>パートナー会社連絡先管理</figcaption>
                </figure>
            </a>
        </li>
    @endif
    
    @if($authority_data["custom_document"])
        <li>
            <a href="{{ url('customDocument/index') }}" onclick="saveAccessLog('カスタム書類作成');" class="pop" data-toggle="popover" data-placement="bottom" data-content="カスタム書類作成<a href='javascript:void(0)' class='hyper'>サービス説明</a>">
                <figure>
                    <img src="/iPD/public/image/JPG/分析アイコン.jpeg" alter="カスタム書類作成"width="50px">
                    <figcaption>カスタム書類作成</figcaption>
                </figure>
            </a>
        </li>
    @endif
    @if($authority_data["access_log"])
        <li>
            <a  href="{{ url('common/accessLog') }}" onclick="saveAccessLog('アクセスログ');" class="pop" data-toggle="popover" data-placement="bottom" data-content="アクセスログ<a href='javascript:void(0)' class='hyper'>サービス説明</a>">
                <figure>
                    <img src="/iPD/public/image/JPG/会員証のアイコン素材.jpeg" alter="アクセスログ"width="50px">
                    <figcaption>アクセスログ</figcaption>
                </figure>
            </a>
        </li>
    @endif   
        <!--<li>-->
        <!--    <a href="{{ url('forge/volumeChart') }}">-->
        <!--        <figure>-->
        <!--            <img src="/iPD/public/image/JPG/���析アイコン.jpeg"art="データー容量"width="50px">-->
        <!--            <figcaption>マテリアル容積表示</figcaption>-->
        <!--        </figure>-->
        <!--    </a>-->
        <!--</li>-->
        <!--<li>-->
        <!--    <a href="{{ url('roomProp/index') }}">-->
        <!--        <figure>-->
        <!--            <img src="/iPD/public/image/JPG/位置バルーンのフリー素材.jpeg"art="データー容量"width="50px">-->
        <!--            <figcaption>部屋情報表示</figcaption>-->
        <!--        </figure>-->
        <!--    </a>-->
        <!--</li>-->
        <!--<li></li>-->
        <!--<li></li>-->
        <!--<li></li>-->
    </ul>

</main>
@endsection