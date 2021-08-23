@extends('layouts.baselayout')
@section('title', 'CCC - Project management')

@section('head')
<script src="../public/js/shim.js"></script>
<script src="../public/js/xlsx.full.min.js"></script>
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/project.js"></script>
<script type="text/javascript" src="../public/js/slick.js"></script>
<link rel="stylesheet" href="../public/css/slick.css">
<link rel="stylesheet" href="../public/css/slick-theme.css">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
<style>
.koujiPopupsize{
	width:800px;
	height:710px;
	margin-top:5px;
	margin-bottom:5px;
	padding-top:5px;
	padding-bottom:5px;
}

.koujiFooterSize{
	width:1000px;
	height:40px;
	margin-top:1px;
}
.inputGroup {
	height:25px;
	border-color:#f5f5f5;
}
.inputGroupTmp {
	height:23px;
	border-color:#f5f5f5;
}
.h4 {
	margin-top:6px;	
}
/* Bootstrap 3 text input with search icon */
.has-search .form-control-feedback {
    color: #ccc;
    margin-bottom:-34px;
}
.has-search .form-control {
    padding-right: 12px;
    padding-left: 34px;
    min-width:100%;
    width:auto;
}

.glyphicon{
    position:static;  
}

#searchDiv{
    width:100%;
    margin:0 0 1% 5%;
    height: 8vh;
    margin: auto;
}
.clear-decoration-btn{
    border: none;
    outline: none;
    background: transparent;
}
</style>
<script>
	$(function() {
		$.datepicker.setDefaults( $.datepicker.regional[ "ja" ] );
		$('#startTime').datepicker({
			minDate: '-70y', //今日から70年前
			changeYear: true, //表示年の指定が可
			onSelect: function(dateText){ //西暦→和暦に変換して表示
				var date = dateText.split('/');
				//var wareki = date[0] - 1988;
				dateText =  date[0] +'年　'+ date[1] +'月　'+ date[2] +'日';
				$(this).val(dateText);
			}
		});
		$('#endTime').datepicker({
			minDate: '-70y', //今日から70年前
			changeYear: true, //表示年の指定が可
			onSelect: function(dateText){ //西暦→和暦に変換して表示
				var date = dateText.split('/');			
				dateText =   date[0] +'年　'+ date[1] +'月　'+ date[2] +'日';
				$(this).val(dateText);
			}
		});
		$('#timeInterval').datepicker({
			minDate: '-70y', //今日から70年前
			changeYear: true, //表示年の指定が可
			onSelect: function(dateText){ //西暦→和暦に変換して表示
				var date = dateText.split('/');
				//var wareki = date[0] - 1988;
				dateText =   date[0] +'年　'+ date[1] +'月　'+ date[2] +'日';
				$(this).val(dateText);
			}
		});
		$('#box_date1').datepicker({
			minDate: '-70y', //今日から70年前
			changeYear: true, //表示年の指定が可
			onSelect: function(dateText){ //西暦→和暦に変換して表示
				var date = dateText.split('/');
				//var wareki = date[0] - 1988;
				dateText =  date[0] +'年　'+ date[1] +'月　'+ date[2] +'日';
				$(this).val(dateText);
			}
		});
		$('#box_date2').datepicker({
			minDate: '-70y', //今日から70年前
			changeYear: true, //表示年の指定が可
			onSelect: function(dateText){ //西暦→和暦に変換して表示
				var date = dateText.split('/');
				//var wareki = date[0] - 1988;
				dateText =  date[0] +'年　'+ date[1] +'月　'+ date[2] +'日';
				$(this).val(dateText);
			}
		});
		$('#box_date3').datepicker({
			minDate: '-70y', //今日から70年前
			changeYear: true, //表示年の指定が可
			onSelect: function(dateText){ //西暦→和暦に変換して表示
				var date = dateText.split('/');
				//var wareki = date[0] - 1988;
				dateText =  date[0] +'年　'+ date[1] +'月　'+ date[2] +'日';
				$(this).val(dateText);
			}
		});
		$('#make_model_start').datepicker({
			minDate: '-70y', //今日から70年前
			changeYear: true, //表示年の指定が可
			onSelect: function(dateText){ //西暦→和暦に変換して表示
				var date = dateText.split('/');
				//var wareki = date[0] - 1988;
				dateText =  date[0] +'年　'+ date[1] +'月　'+ date[2] +'日';
				$(this).val(dateText);
			}
		});
		$('#make_model_end').datepicker({
			minDate: '-70y', //今日から70年前
			changeYear: true, //表示年の指定が可
			onSelect: function(dateText){ //西暦→和暦に変換して表示
				var date = dateText.split('/');
				//var wareki = date[0] - 1988;
				dateText =  date[0] +'年　'+ date[1] +'月　'+ date[2] +'日';
				$(this).val(dateText);
			}
		});
		$('#sinsei_start').datepicker({
			minDate: '-70y', //今日から70年前
			changeYear: true, //表示年の指定が可
			onSelect: function(dateText){ //西暦→和暦に変換して表示
				var date = dateText.split('/');
				//var wareki = date[0] - 1988;
				dateText =  date[0] +'年　'+ date[1] +'月　'+ date[2] +'日';
				$(this).val(dateText);
			}
		});
		$('#sinsei_end').datepicker({
			minDate: '-70y', //今日から70年前
			changeYear: true, //表示年の指定が可
			onSelect: function(dateText){ //西暦→和暦に変換して表示
				var date = dateText.split('/');
				//var wareki = date[0] - 1988;
				dateText =  date[0] +'年　'+ date[1] +'月　'+ date[2] +'日';
				$(this).val(dateText);
			}
		});
		$('#seisan_start').datepicker({
			minDate: '-70y', //今日から70年前
			changeYear: true, //表示年の指定が可
			onSelect: function(dateText){ //西暦→和暦に変換して表示
				var date = dateText.split('/');
				//var wareki = date[0] - 1988;
				dateText =  date[0] +'年　'+ date[1] +'月　'+ date[2] +'日';
				$(this).val(dateText);
			}
		});
		$('#seisan_end').datepicker({
			minDate: '-70y', //今日から70年前
			changeYear: true, //表示年の指定が可
			onSelect: function(dateText){ //西暦→和暦に変換して表示
				var date = dateText.split('/');
				//var wareki = date[0] - 1988;
				dateText =  date[0] +'年　'+ date[1] +'月　'+ date[2] +'日';
				$(this).val(dateText);
			}
		});
		$('#kouji_start').datepicker({
			minDate: '-70y', //今日から70年前
			changeYear: true, //表示年の指定が可
			onSelect: function(dateText){ //西暦→和暦に変換して表示
				var date = dateText.split('/');
				//var wareki = date[0] - 1988;
				dateText =  date[0] +'年　'+ date[1] +'月　'+ date[2] +'日';
				$(this).val(dateText);
			}
		});
		$('#kouji_end').datepicker({
			minDate: '-70y', //今日から70年前
			changeYear: true, //表示年の指定が可
			onSelect: function(dateText){ //西暦→和暦に変換して表示
				var date = dateText.split('/');
				//var wareki = date[0] - 1988;
				dateText =  date[0] +'年　'+ date[1] +'月　'+ date[2] +'日';
				$(this).val(dateText);
			}
		});
		$('#genba_start').datepicker({
			minDate: '-70y', //今日から70年前
			changeYear: true, //表示年の指定が可
			onSelect: function(dateText){ //西暦→和暦に変換して表示
				var date = dateText.split('/');
				//var wareki = date[0] - 1988;
				dateText =  date[0] +'年　'+ date[1] +'月　'+ date[2] +'日';
				$(this).val(dateText);
			}
		});
		$('#genba_end').datepicker({
			minDate: '-70y', //今日から70年前
			changeYear: true, //表示年の指定が可
			onSelect: function(dateText){ //西暦→和暦に変換して表示
				var date = dateText.split('/');
				//var wareki = date[0] - 1988;
				dateText =  date[0] +'年　'+ date[1] +'月　'+ date[2] +'日';
				$(this).val(dateText);
			}
		});
		$('#sekou_start').datepicker({
			minDate: '-70y', //今日から70年前
			changeYear: true, //表示年の指定が可
			onSelect: function(dateText){ //西暦→和暦に変換して表示
				var date = dateText.split('/');
				//var wareki = date[0] - 1988;
				dateText =  date[0] +'年　'+ date[1] +'月　'+ date[2] +'日';
				$(this).val(dateText);
			}
		});
		$('#sekou_end').datepicker({
			minDate: '-70y', //今日から70年前
			changeYear: true, //表示年の指定が可
			onSelect: function(dateText){ //西暦→和暦に変換して表示
				var date = dateText.split('/');
				//var wareki = date[0] - 1988;
				dateText =  date[0] +'年　'+ date[1] +'月　'+ date[2] +'日';
				$(this).val(dateText);
			}
		});
		$('#hiki_start').datepicker({
			minDate: '-70y', //今日から70年前
			changeYear: true, //表示年の指定が可
			onSelect: function(dateText){ //西暦→和暦に変換して表示
				var date = dateText.split('/');
				//var wareki = date[0] - 1988;
				dateText =  date[0] +'年　'+ date[1] +'月　'+ date[2] +'日';
				$(this).val(dateText);
			}
		});
		$('#hiki_end').datepicker({
			minDate: '-70y', //今日から70年前
			changeYear: true, //表示年の指定が可
			onSelect: function(dateText){ //西暦→和暦に変換して表示
				var date = dateText.split('/');
				//var wareki = date[0] - 1988;
				dateText =  date[0] +'年　'+ date[1] +'月　'+ date[2] +'日';
				$(this).val(dateText);
			}
		});
		$('#rev_date1').datepicker({
			minDate: '-70y', //今日から70年前
			changeYear: true, //表示年の指定が可
			onSelect: function(dateText){ //西暦→和暦に変換して表示
				var date = dateText.split('/');
				//var wareki = date[0] - 1988;
				dateText =  date[0] +'年　'+ date[1] +'月　'+ date[2] +'日';
				$(this).val(dateText);
			}
		});
		$('#rev_date2').datepicker({
			minDate: '-70y', //今日から70年前
			changeYear: true, //表示年の指定が可
			onSelect: function(dateText){ //西暦→和暦に変換して表示
				var date = dateText.split('/');
				//var wareki = date[0] - 1988;
				dateText =  date[0] +'年　'+ date[1] +'月　'+ date[2] +'日';
				$(this).val(dateText);
			}
		});
		$('#rev_date3').datepicker({
			minDate: '-70y', //今日から70年前
			changeYear: true, //表示年の指定が可
			onSelect: function(dateText){ //西暦→和暦に変換して表示
				var date = dateText.split('/');
				//var wareki = date[0] - 1988;
				dateText =  date[0] +'年　'+ date[1] +'月　'+ date[2] +'日';
				$(this).val(dateText);
			}
		});
		$('#tyakkou').datepicker({
			minDate: '-70y', //今日から70年前
			changeYear: true, //表示年の指定が可
			onSelect: function(dateText){ //西暦→和暦に変換して表示
				var date = dateText.split('/');
				//var wareki = date[0] - 1988;
				dateText =  date[0] +'年　'+ date[1] +'月　'+ date[2] +'日';
				$(this).val(dateText);
			}
		});
		$('#syunkou').datepicker({
			minDate: '-70y', //今日から70年前
			changeYear: true, //表示年の指定が可
			onSelect: function(dateText){ //西暦→和暦に変換して表示
				var date = dateText.split('/');
				//var wareki = date[0] - 1988;
				dateText =  date[0] +'年　'+ date[1] +'月　'+ date[2] +'日';
				$(this).val(dateText);
			}
		});
	});
	// function ClosePopup()
	// {		
	// 	$("#overlayEditProject").css({ visibility: "hidden",opacity: "0"});
	// }
	
</script>
@endsection

@section('content')
<div class="main-content">
	
<input type="hidden" id="hidLoginID" name="hidLoginID" value="{{Session::get('login_user_id')}}"/>

<div id="overlayEditProject" class="popupOverlay">
	<div class="popup popupBackground koujiPopupsize">
		<div class="PopupFooter koujiFooterSize" style="display:flex;justify-content:space-between;padding-top:13px;">
			<input type="button" name="douki" value="更新" onClick = "UpdateProjectName()"/>
			<h3 style="margin-top:5px;">プロジェクト情報修正</h3>
			<input type="button" name="RCHariClear" id="RCHariClear" onClick ="CloseCurrentEditProjectPopup()" value="閉じる"/>&nbsp&nbsp&nbsp
		</div>

		<div class="content" style="padding-top:10px;">	
			
			<table id="tbKouji">
				<tr>
					<td></td>
					<td>プロジェクト名</td>
					<td ><input type="text" class="inputGroupTmp" name="prjName" id="prjName" size="48"/></td>
				</tr>
				<tr style="border-top: 4px solid transparent;">
					<td>①</td>
					<td>工事名称</td>
					<td ><input type="text" class="inputGroupTmp" name="koujimeisho" id="koujimeisho" size="70"/></td>
				</tr>
				<tr style="border-top: 4px solid transparent;">
					<td>②</td>
					<td>施工場所</td>
					<td ><input type="text" class="inputGroupTmp" name="sekoubasho" id="sekoubasho" size="70"/></td>
				</tr>
				<tr style="border-top: 4px solid transparent;">
					<td>③</td>
					<td>発注者</td>
					<td ><input type="text" class="inputGroupTmp" name="hachuusha" id="hachuusha" size="70"/></td>
				</tr>
				<tr style="border-top: 4px solid transparent;">
					<td>④</td>
					<td>設計者</td>
					<td><input type="text" class="inputGroupTmp" name="sekkeisha" id="sekkeisha" size="48"/></td>
				</tr>
				<tr style="border-top: 4px solid transparent;">
					<td></td>
					<td>工事監理者</td>
					<td ><input type="text" class="inputGroupTmp" name="koujikanrisha" id="koujikanrisha" size="48"/></td>
				</tr>
				<tr style="border-top: 4px solid transparent;">
					<td></td>
					<td>施工者</td>
					<td ><input type="text" class="inputGroupTmp" name="sekousha" id="sekousha" size="48"/>(JVの場合、その構成比率を書く)</td>
				</tr>
				<tr style="border-top: 4px solid transparent;">
					<td>⑤</td>
					<td>工事事務所</td>
					<td>
						<table>
							<tr>
								<td>名称</td>
								<td><input type="text" class="inputGroupTmp" name="meisho" id="meisho" size="30"/></td>
							</tr>
							<tr style="border-top: 3px solid transparent;">
								<td>所在地</td>
								<td><input type="text" class="inputGroupTmp" name="shozonchi" id="shozonchi" size="30"/></td>
							</tr>	
							<tr style="border-top: 3px solid transparent;">
								<td>電話</td>
								<td><input type="text" class="inputGroupTmp" name="denwa" id="denwa" size="30"/></td>
								<td>ファックス</td>
								<td><input type="text" class="inputGroupTmp" name="fax" id="fax"/></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr style="border-top: 4px solid transparent;">
					<td>⑥</td>
					<td>所轄労基署</td>
					<td><input type="text" class="inputGroupTmp" name="shokatsurokisho" id="shokatsurokisho" size="30"/><span class="align-text">労働基準監督署</span></td>
				</tr>	
				<tr style="border-top: 4px solid transparent;">
					<td>⑦</td>
					<td>工期</td>
					<td>
						<input type="text" class="inputGroupTmp" name="startTime" id="startTime" placeholder="　　年　　月　　日"/>
						<span class="align-text"> ～ &nbsp</span>
						<input type="text" class="inputGroupTmp" name="endTime" id="endTime" placeholder="　　年　　月　　日"/>
					</td>
				</tr>
				<tr style="border-top: 3px solid transparent;">
					<td></td>
					<td></td>
					<td><span class="align-text" style="padding:0px 5px 0px 145px;">中間工期</span><input type="text" class="inputGroupTmp" name="timeInterval" id="timeInterval" placeholder="　　年　　月　　日"/></td>
				</tr>	
				<tr style="border-top: 4px solid transparent;">
					<td>⑧</td>
					<td>建築物用途</td>
					<td><input type="text" class="inputGroupTmp" name="kenchikuyoto" id="kenchikuyoto" size="70"/></td>
				</tr>
				<tr style="border-top: 4px solid transparent;">
					<td>⑨</td>
					<td>構造、規模</td>
					<td>
						<table>
							<tr>
								<td><input type="text" class="inputGroupTmp" name="kozo" id="kozo" size="7" />造</td>
								<td>
									<span class="align-text">地上&nbsp</span>
									<input type="text" class="inputGroupTmp" name="zouchijyo" id="zouchijyo" size="7"/>&nbsp階 
								</td>
								<td>
									<span class="align-text">地下&nbsp</span>
									<input type="text" class="inputGroupTmp" name="kaichika" id="kaichika" size="7"/>&nbsp階 
								</td>
								<td>
									<span class="align-text">塔屋&nbsp</span>
									<input type="text" class="inputGroupTmp" name="kaitouya" id="kaitouya" size="7"/>&nbsp階
								</td>
							</tr>
							<tr style="border-top: 3px solid transparent;">
								<td>最高高さ</td>
								<td ><input type="text" class="inputGroupTmp" name="glPlus" id="glPlus" size="10" value="GL＋"/> <span class="align-text">m</span></td>
								<td>軒高</td>
								<td ><input type="text" class="inputGroupTmp" name="glMinus" id="glMinus" size="10" value="GL－"/><span class="align-text"> m</span></td>	
							</tr>
							<tr style="border-top: 3px solid transparent;">
								<td>掘削深さ</td>
								<td colspan="3"><input type="text" class="inputGroupTmp" name="kussakufukasa" id="kussakufukasa" value="GL－" size="10"/><span class="align-text"> m</span></td>
							</tr>
							<tr style="border-top: 3px solid transparent;">
								<td>屋上．屋根</td>
								<td colspan="3"><input type="text" class="inputGroupTmp" name="okujyou" id="okujyou" size="40"/></td>
							</tr>
							<tr style="border-top: 3px solid transparent;">
								<td>外装</td>
								<td colspan="3"><input type="text" class="inputGroupTmp" name="gaisou" id="gaisou" size="40"/></td>
							</tr>
						</table>	
					</td>						
				</tr>				
				<tr style="border-top: 4px solid transparent;">
					<td>⑩</td>
					<td>敷地面積</td>
					<td><input type="text" class="inputGroupTmp" name="shikichimenseki" id="shikichimenseki" size="53"/><span class="align-text">㎡</span></td>
				</tr>	
				<tr style="border-top: 4px solid transparent;">
					<td>⑪</td>
					<td>建築面積</td>
					<td><input type="text" class="inputGroupTmp" name="kenchikumenseki" id="kenchikumenseki" size="53"/><span class="align-text">㎡</span></td>
				</tr>
				<tr style="border-top: 4px solid transparent;">
					<td>⑫</td>
					<td>延床面積</td>
					<td><input type="text" class="inputGroupTmp" name="yukamenseki" id="yukamenseki" size="53"/><span class="align-text">㎡</span></td>
				</tr>	
				<tr style="border-top: 4px solid transparent;">
					<td>⑬</td>
					<td>請負金</td>
					<td ><span style="float:left;">￥</span><input type="text" class="inputGroupTmp" name="ukeoikin" id="ukeoikin" size="51"/>円　（設備　含・不含）</td>
				</tr>
				<tr style="border-top: 4px solid transparent;">
					<td></td>
					<td></td>
					<td><span style="float:left;">支給材</span><input type="text" class="inputGroupTmp" name="shikyuzai" id="shikyuzai" size="47"/></td>
				</tr>
			</table>	
		</div>

	</div>
</div>

<div id="overlayEditBIMImplementationDocument" class="popupOverlay">
	<div class="popup popupBackground koujiPopupsize">

		<!--<div style="display:flex;">-->
		<!--	<h3 style="margin-top:10px;margin-bottom:0;text-align:center;padding-left:280px;padding-right:125px;">プロジェクト情報</h3>-->
		<!--	<input type="button" style="height:30px;margin-top:10px;" name="buildingInfoImport" id="buildingInfoImport" onClick ="ImportBuildingInfo()" value="全店情報"/>-->
		<!--	<input type="button" style="height:30px;margin:10px 0 0 5px;opacity:0.3;" name="forgeImport" id="forgeImport" onClick ="ImportForgeInfo()" value="Forge情報"/>-->
		<!--</div>-->
		<div class="PopupFooter koujiFooterSize" style="display:flex;justify-content:space-between;padding-top:13px;">
			<input type="button" name="douki" value="更新" onClick = "UpdateImplementationDocument()"/>
			<h3 style="margin-top:5px;">プロジェクト情報</h3>
			<input type="button" name="RCHariClear" id="RCHariClear" onClick ="CloseCurrentEditImplementDocPopup()" value="閉じる"/>&nbsp&nbsp&nbsp
		</div>
		
		<div class="BIMImpDocContent" style="display:flex;">
			<div>
				<div class="PopupHeader koujiHeaderSize">
					<h4 style="margin-top:0;margin-bottom:0;text-align:center;">(1/3)</h4>
					<!--<a class="close" href="javascript:void(0);" onClick ="CloseCurrentPopup()" style="top:2px;">&times;</a>-->
				</div>

				<div id="tbBIMImplementationTable1">
					<h4 style="font-weight:bold;">プロジェクト概要</h4>
					<div style="display:flex;">
						<h6 style="padding-left:78px;">PJコード</h6>
						<div style="padding:0 10px 0 10px;">
							<input type="text" class="inputGroup" name="project_code" id="project_code" size="15" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:44px;">工事基幹コード</h6>
						<div style="padding:0 10px 0 10px;">
							<input type="text" class="inputGroup" name="kouji_kikan_code" id="kouji_kikan_code" size="15" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:104px;">支店</h6>
						<div style="padding:0 10px 0 10px;">
							<input type="text" class="inputGroup" name="branch_store" id="branch_store" size="15" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:44px;">プロジェクト名</h6>
						<div style="padding:0 10px 0 10px;">
							<input type="text" class="inputGroup" name="project_name" id="project_name" size="50" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:80px;">工事区分</h6>
						<div style="padding:0 10px 0 10px;">
							<input type="text" class="inputGroup" name="construction_type" id="construction_type" size="15" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:104px;">用途</h6>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="building_use" id="building_use" size="15" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:104px;">住所</h6>
						<div style="padding:0 10px 0 10px;">
							<input type="text" class="inputGroup" name="address" id="address" size="50" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:92px;">発注者</h6>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="hattyuusya" id="hattyuusya" size="50" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:92px;">設計者</h6>
						<div style="padding:0 10px 0 10px;">
							<input type="text" class="inputGroup" name="sekkeisya" id="sekkeisya" size="50" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
					</div></br>
					<div style="display:flex;">
						<h6 style="padding-left:104px;">着工</h6>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="tyakkou" id="tyakkou" size="15"  placeholder="　　年　　月　　日" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:104px;">竣工</h6>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="syunkou" id="syunkou" size="15" placeholder="　　年　　月　　日" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:104px;">構造</h6>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="kouzou" id="kouzou" size="15" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:104px;">階数</h6>
						<h6 style="padding-left:10px;">地下</h6>
						<div style="padding-left:3px;">
							<input type="text" class="inputGroup" name="tika" id="tika" size="2" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
						<h6 style="padding-left:3px;">階</h6>
						<h6 style="padding-left:10px;">地上</h6>
						<div style="padding-left:3px;">
							<input type="text" class="inputGroup" name="tijou" id="tijou" size="2" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
						<h6 style="padding-left:3px;">階</h6>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:61px;">延床面積[㎡]</h6>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="total_floor_area" id="total_floor_area" size="15" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:104px;">棟数</h6>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="building_num" id="building_num" size="15" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:68px;">工事事務所</h6>
						<div style="padding:0 10px 0 10px;">
							<input type="text" class="inputGroup" name="kouji_jimusyo" id="kouji_jimusyo" size="50" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
					</div></br>
					<div style="display:flex;">
						<h6 style="padding-left:20px;">プロジェクト基準点</h6>
						<div style="padding:0 5px 0 10px;">
							<input type="text" class="inputGroup" name="base_linex" id="base_linex" size="5"/>
						</div>
						<h6>通りと</h6>
						<div style="padding:0 5px 0 5px;">
							<input type="text" class="inputGroup" name="base_liney" id="base_liney" size="5"/>
						</div>
						<h6>通りの交点</h6>
					</div>
				</div>
				</br>
			</div>
			<div>
				<div class="PopupHeader koujiHeaderSize">
					<h4 style="margin-top:0;margin-bottom:0;text-align:center;">(2/3)</h4>
					<!--<a class="close" href="javascript:void(0);" onClick ="CloseCurrentPopup()" style="top:2px;">&times;</a>-->
				</div>

				<div id="tbBIMImplementationTable2">
					<h4 style="font-weight:bold;margin-bottom: 0px;">プロジェクト関係者</h4>
					<div style="display:flex;">
						<h6 style="padding-left:216px; margin-bottom:5px;">組織</h6>
						<h6 style="padding-left:200px; margin-bottom: 5px;">担当者</h6>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:70px; margin-bottom:3px;">建築設計</h6>
						<div style="padding:0 40px 0 20px;">
							<input type="text" class="inputGroup" name="ken_org" id="ken_org" size="20" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="ken_name" id="ken_name" size="20" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:70px; margin-bottom:3px;">構造設計</h6>
						<div style="padding:0 40px 0 20px;">
							<input type="text" class="inputGroup" name="kou_org" id="kou_org" size="20" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="kou_name" id="kou_name" size="20" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:70px; margin-bottom:3px;">設備空調</h6>
						<div style="padding:0 40px 0 20px;">
							<input type="text" class="inputGroup" name="sku_org" id="sku_org" size="20" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="sku_name" id="sku_name" size="20" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:70px; margin-bottom:3px;">設備電気</h6>
						<div style="padding:0 40px 0 20px;">
							<input type="text" class="inputGroup" name="sde_org" id="sde_org" size="20" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="sde_name" id="sde_name" size="20" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:94px; margin-bottom:3px;">施工</h6>
						<div style="padding:0 40px 0 20px;">
							<input type="text" class="inputGroup" name="sek_org" id="sek_org" size="20" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="sek_name" id="sek_name" size="20" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:70px; margin-bottom:3px;">生産設計</h6>
						<div style="padding:0 40px 0 20px;">
							<input type="text" class="inputGroup" name="sei_org" id="sei_org" size="20" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="sei_name" id="sei_name" size="20" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:82px; margin-bottom:3px;">工事部</h6>
						<div style="padding:0 40px 0 20px;">
							<input type="text" class="inputGroup" name="koj_org" id="koj_org" size="20" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="koj_name" id="koj_name" size="20" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:70px; margin-bottom:3px;">生産技術</h6>
						<div style="padding:0 40px 0 20px;">
							<input type="text" class="inputGroup" name="sgi_org" id="sgi_org" size="20" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="sgi_name" id="sgi_name" size="20" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:70px; margin-bottom:3px;">積算見積</h6>
						<div style="padding:0 40px 0 20px;">
							<input type="text" class="inputGroup" name="smi_org" id="smi_org" size="20" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="smi_name" id="smi_name" size="20" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:61px; margin-bottom:3px;">BIMマネ課</h6>
						<div style="padding:0 40px 0 20px;">
							<input type="text" class="inputGroup" name="bmn_org" id="bmn_org" size="20" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="bmn_name" id="bmn_name" size="20" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:54px; margin-bottom:3px;">PDセンター</h6>
						<div style="padding:0 40px 0 20px;">
							<input type="text" class="inputGroup" name="pds_org" id="pds_org" size="20" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="pds_name" id="pds_name" size="20" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:58px; margin-bottom:3px;">モデル作成</h6>
						<div style="padding:0 40px 0 20px;">
							<input type="text" class="inputGroup" name="mdl_org" id="mdl_org" size="20"/>
						</div>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="mdl_name" id="mdl_name" size="20"/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:46px; margin-bottom:3px;">サブコン空調</h6>
						<div style="padding:0 40px 0 20px;">
							<input type="text" class="inputGroup" name="sbk_org" id="sbk_org" size="20"/>
						</div>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="sbk_name" id="sbk_name" size="20"/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:46px; margin-bottom:3px;">サブコン電気</h6>
						<div style="padding:0 40px 0 20px;">
							<input type="text" class="inputGroup" name="sbd_org" id="sbd_org" size="20"/>
						</div>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="sbd_name" id="sbd_name" size="20"/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:72px; margin-bottom:3px;">FAB作図</h6>
						<div style="padding:0 40px 0 20px;">
							<input type="text" class="inputGroup" name="fsa_org" id="fsa_org" size="20"/>
						</div>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="fsa_name" id="fsa_name" size="20"/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:72px; margin-bottom:3px;">FAB製作</h6>
						<div style="padding:0 40px 0 20px;">
							<input type="text" class="inputGroup" name="fse_org" id="fse_org" size="20"/>
						</div>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="fse_name" id="fse_name" size="20"/>
						</div>
					</div>
				</div>
				<div id="tbBIMImplementationTable2-1">
					<h4 style="font-weight:bold;margin-bottom:5px;">使用するソフトウェア</h4>
					<div style="display:flex;">
						<h6 style="padding-left:70px; margin-bottom:3px;">建築設計</h6>
						<div style="padding:0 20px 0 20px;">
							<input type="text" class="inputGroup" name="ken_sw" id="ken_sw" size="20"/>
						</div>
						<h6 style="padding-left:58px; margin-bottom:3px;">構造設計</h6>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="kou_sw" id="kou_sw" size="20"/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:43px; margin-bottom:3px;">設備設計 空調</h6>
						<div style="padding:0 20px 0 20px;">
							<input type="text" class="inputGroup" name="sku_sw" id="sku_sw" size="20"/>
						</div>
						<h6 style="padding-left:31px; margin-bottom:3px;">設備設計 電気</h6>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="sde_sw" id="sde_sw" size="20"/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:58px; margin-bottom:3px;">ワンモデル</h6>
						<div style="padding:0 20px 0 20px;">
							<input type="text" class="inputGroup" name="mdl_sw" id="mdl_sw" size="10"/>
						</div>
						<h6 style="padding-left:0px; margin-bottom:3px;">施工</h6>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="sek_sw" id="sek_sw" size="10"/>
						</div>
						<h6 style="padding-left:47px; margin-bottom:3px;">生産設計</h6>
						<div style="padding:0 20px 0 20px;">
							<input type="text" class="inputGroup" name="sei_sw" id="sei_sw" size="10"/>
						</div>
					</div>
					<!--<div style="display:flex;">-->
					<!--</div>-->
					<div style="display:flex;">
						<h6 style="padding-left:10px; margin-bottom:3px;">サブコン(施工) 空調</h6>
						<div style="padding:0 20px 0 20px;">
							<input type="text" class="inputGroup" name="sbk_sw" id="sbk_sw" size="20"/>
						</div>
						<h6 style="padding-left:0px; margin-bottom:3px;">サブコン(施工) 電気</h6>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="sbd_sw" id="sbd_sw" size="20"/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:71px; margin-bottom:3px;">FAB作図</h6>
						<div style="padding:0 20px 0 20px;">
							<input type="text" class="inputGroup" name="fsa_sw" id="fsa_sw" size="20"/>
						</div>
						<h6 style="padding-left:60px; margin-bottom:3px;">FAB製作</h6>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="fse_sw" id="fse_sw" size="20"/>
						</div>
					</div>
				</div>
			</div>
			<div>
				<div class="PopupHeader koujiHeaderSize">
					<h4 style="margin-top:0;margin-bottom:0;text-align:center;">(3/3)</h4>
					<!--<a class="close" href="javascript:void(0);" onClick ="CloseCurrentPopup()" style="top:2px;">&times;</a>-->
				</div>

				<div id="tbBIMImplementationTable3">
					<h4 style="font-weight:bold;margin-bottom:3px;">プロジェクトスケジュール</h4>
					<div style="display:flex;">
						<h6 style="padding-left:25px; margin-bottom:3px;">設計モデル作成</h6>
						<div style="padding:0 5px 0 5px;">
							<input type="text" class="inputGroup" name="make_model_start" id="make_model_start" size="15" placeholder="　　年　　月　　日" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
						<h6 style="margin-bottom:3px;">～</h6>
						<div style="padding-left:5px;">
							<input type="text" class="inputGroup" name="make_model_end" id="make_model_end" size="15" placeholder="　　年　　月　　日" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
						<h6 style="padding-left:10px;margin-bottom:3px;">備考</h6>
						<div style="padding-left:5px;">
							<input type="text" class="inputGroup" name="make_model_bikou" id="make_model_bikou" size="25"/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:61px; margin-bottom:3px;">確認申請</h6>
						<div style="padding:0 5px 0 5px;">
							<input type="text" class="inputGroup" name="sinsei_start" id="sinsei_start" size="15" placeholder="　　年　　月　　日" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
						<h6 style="margin-bottom:3px;">～</h6>
						<div style="padding-left:5px;">
							<input type="text" class="inputGroup" name="sinsei_end" id="sinsei_end" size="15" placeholder="　　年　　月　　日" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
						<h6 style="padding-left:10px;margin-bottom:3px;">備考</h6>
						<div style="padding-left:5px;">
							<input type="text" class="inputGroup" name="sinsei_bikou" id="sinsei_bikou" size="25"/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:5px; margin-bottom:3px;">精算見積model統合</h6>
						<div style="padding:0 5px 0 5px;">
							<input type="text" class="inputGroup" name="seisan_start" id="seisan_start" size="15" placeholder="　　年　　月　　日" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
						<h6 style="margin-bottom:3px;">～</h6>
						<div style="padding-left:5px;">
							<input type="text" class="inputGroup" name="seisan_end" id="seisan_end" size="15" placeholder="　　年　　月　　日" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
						<h6 style="padding-left:10px;margin-bottom:3px;">備考</h6>
						<div style="padding-left:5px;">
							<input type="text" class="inputGroup" name="seisan_bikou" id="seisan_bikou" size="25"/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:25px; margin-bottom:3px;">工事従事者決定</h6>
						<div style="padding:0 5px 0 5px;">
							<input type="text" class="inputGroup" name="kouji_start" id="kouji_start" size="15" placeholder="　　年　　月　　日" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
						<h6 style="margin-bottom:3px;">～</h6>
						<div style="padding-left:5px;">
							<input type="text" class="inputGroup" name="kouji_end" id="kouji_end" size="15" placeholder="　　年　　月　　日" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
						<h6 style="padding-left:10px;margin-bottom:3px;">備考</h6>
						<div style="padding-left:5px;">
							<input type="text" class="inputGroup" name="kouji_bikou" id="kouji_bikou" size="25"/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:25px; margin-bottom:3px;">現場工程表決定</h6>
						<div style="padding:0 5px 0 5px;">
							<input type="text" class="inputGroup" name="genba_start" id="genba_start" size="15" placeholder="　　年　　月　　日" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
						<h6 style="margin-bottom:3px;">～</h6>
						<div style="padding-left:5px;">
							<input type="text" class="inputGroup" name="genba_end" id="genba_end" size="15" placeholder="　　年　　月　　日" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
						<h6 style="padding-left:10px;margin-bottom:3px;">備考</h6>
						<div style="padding-left:5px;">
							<input type="text" class="inputGroup" name="genba_bikou" id="genba_bikou" size="25"/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:85px; margin-bottom:3px;">施工</h6>
						<div style="padding:0 5px 0 5px;">
							<input type="text" class="inputGroup" name="sekou_start" id="sekou_start" size="15" placeholder="　　年　　月　　日" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
						<h6 style="margin-bottom:3px;">～</h6>
						<div style="padding-left:5px;">
							<input type="text" class="inputGroup" name="sekou_end" id="sekou_end" size="15" placeholder="　　年　　月　　日" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
						<h6 style="padding-left:10px;margin-bottom:3px;">備考</h6>
						<div style="padding-left:5px;">
							<input type="text" class="inputGroup" name="sekou_bikou" id="sekou_bikou" size="25"/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:73px; margin-bottom:3px;">引渡し</h6>
						<div style="padding:0 5px 0 5px;">
							<input type="text" class="inputGroup" name="hiki_start" id="hiki_start" size="15" placeholder="　　年　　月　　日" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
						<h6 style="margin-bottom:3px;">～</h6>
						<div style="padding-left:5px;">
							<input type="text" class="inputGroup" name="hiki_end" id="hiki_end" size="15" placeholder="　　年　　月　　日" style="color:#a9a9a9;background-color:#f0f0f0;" readonly/>
						</div>
						<h6 style="padding-left:10px;margin-bottom:3px;">備考</h6>
						<div style="padding-left:5px;">
							<input type="text" class="inputGroup" name="hiki_bikou" id="hiki_bikou" size="25"/>
						</div>
					</div>
				</div>
				<div id="tbBIMImplementationTable3-1">
					<h4  style="font-weight:bold;margin-bottom:3px;">BOX Upload File</h4>
					<div style="display:flex;">
						<h6 style="padding-left:72px; margin-bottom:3px;">日時1</h6>
						<div style="padding:0 10px 0 10px;">
							<input type="text" class="inputGroup" name="box_date1" id="box_date1" size="15" placeholder="　　年　　月　　日"/>
						</div>
						<h6 style="padding-left:11px; margin-bottom:3px;">改定者1</h6>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="box_rev_person1" id="box_rev_person1" size="15"/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:35px; margin-bottom:3px;">ファイル名1</h6>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="box_upload_file1" id="box_upload_file1" size="46"/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:72px; margin-bottom:3px;">日時2</h6>
						<div style="padding:0 10px 0 10px;">
							<input type="text" class="inputGroup" name="box_date2" id="box_date2" size="15" placeholder="　　年　　月　　日"/>
						</div>
						<h6 style="padding-left:11px; margin-bottom:3px;">改定者2</h6>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="box_rev_person2" id="box_rev_person2" size="15"/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:35px; margin-bottom:3px;">ファイル名2</h6>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="box_upload_file2" id="box_upload_file2" size="46"/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:72px; margin-bottom:3px;">日時3</h6>
						<div style="padding:0 10px 0 10px;">
							<input type="text" class="inputGroup" name="box_date3" id="box_date3" size="15" placeholder="　　年　　月　　日"/>
						</div>
						<h6 style="padding-left:11px; margin-bottom:3px;">改定者3</h6>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="box_rev_person3" id="box_rev_person3" size="15"/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:35px; margin-bottom:3px;">ファイル名3</h6>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="box_upload_file3" id="box_upload_file3" size="46"/>
						</div>
					</div>
				</div>

				<div id="tbBIMImplementationTable3-2">
					<h4 style="font-weight:bold;margin-bottom:3px;">改訂履歴</h4>
					<div style="display:flex;">
						<h6 style="padding-left:84px; margin-bottom:3px;">版1</h6>
						<div style="padding:0 10px 0 10px;">
							<input type="text" class="inputGroup" name="rev_ver1" id="rev_ver1" size="15"/>
						</div>
						<h6 style="padding-left:11px; margin-bottom:3px;">改定日1</h6>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="rev_date1" id="rev_date1" size="15"  placeholder="　　年　　月　　日"/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:48px; margin-bottom:3px;">改定内容1</h6>
						<div style="padding:0 10px 0 10px;">
							<input type="text" class="inputGroup" name="rev_contents1" id="rev_contents1" size="15"/>
						</div>
						<h6 style="padding-left:11px; margin-bottom:3px;">改定者1</h6>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="rev_name1" id="rev_name1" size="15"/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:84px; margin-bottom:3px;">版2</h6>
						<div style="padding:0 10px 0 10px;">
							<input type="text" class="inputGroup" name="rev_ver2" id="rev_ver2" size="15"/>
						</div>
						<h6 style="padding-left:11px; margin-bottom:3px;">改定日2</h6>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="rev_date2" id="rev_date2" size="15"  placeholder="　　年　　月　　日"/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:48px; margin-bottom:3px;">改定内容2</h6>
						<div style="padding:0 10px 0 10px;">
							<input type="text" class="inputGroup" name="rev_contents2" id="rev_contents2" size="15"/>
						</div>
						<h6 style="padding-left:11px; margin-bottom:3px;">改定者2</h6>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="rev_name2" id="rev_name2" size="15"/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:84px; margin-bottom:3px;">版3</h6>
						<div style="padding:0 10px 0 10px;">
							<input type="text" class="inputGroup" name="rev_ver3" id="rev_ver3" size="15"/>
						</div>
						<h6 style="padding-left:11px; margin-bottom:3px;">改定日3</h6>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="rev_date3" id="rev_date3" size="15"  placeholder="　　年　　月　　日"/>
						</div>
					</div>
					<div style="display:flex;">
						<h6 style="padding-left:48px; margin-bottom:3px;">改定内容3</h6>
						<div style="padding:0 10px 0 10px;">
							<input type="text" class="inputGroup" name="rev_contents3" id="rev_contents3" size="15"/>
						</div>
						<h6 style="padding-left:11px; margin-bottom:3px;">改定者3</h6>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="rev_name3" id="rev_name3" size="15"/>
						</div>
					</div></br>
					<div style="display:flex;">
						<h6 style="padding-left:106px;">BIM実行計画書ドキュメントバージョン</h6>
						<div style="padding-left:10px;">
							<input type="text" class="inputGroup" name="doc_version" id="doc_version" size="15" placeholder="Ver.*.*"/>
						</div>
					</div>
				</div>
			</div>
			
		</div>

		<!--<div class="PopupFooter koujiFooterSize" style="padding-top:5px;">-->
		<!--	&nbsp&nbsp&nbsp&nbsp<input type="button" name="douki" value="更新" onClick = "UpdateImplementationDocument()"/>	-->
		<!--	<div style="float:right;position:relative;padding:0 30px 0 0;">							-->
		<!--		<input type="button" name="RCHariClear" id="RCHariClear" onClick ="CloseCurrentEditImplementDocPopup()" value="閉じる"/>&nbsp&nbsp&nbsp-->
		<!--	</div>				-->
		<!--</div>	-->
	</div>
</div>
<div id="searchDiv" class="col-12" style="margin-bottom:2vh;">
    <h4 class="page-title">プロジェクト管理</h4>             
    <div class="form-group has-search">
        <span class="glyphicon glyphicon-search form-control-feedback"></span>
        <input type="text" class="form-control" id="txtSearch" placeholder="プロジェクト検索">
    </div>
</div>   

<div class="col-10">
	<!--<div class="col-6 centerDesign" align="center">
		<!--プロジェクト名 : <input type="text" name="projName" id= "projName"/>-->
  <!--      <input type="button" name="saveproject" value="追加" onclick ="SaveProject()"/>
        <div class="form-group has-search">
            <span class="glyphicon glyphicon-search form-control-feedback" style="float:left;"></span>
            <input type="text" class="form-control" id="txtSearch" placeholder="プロジェクト検索">
        </div>
    </div>-->
		<table id="tbProject" align="center">
			<thead>
				<tr>
					<th width="10%">No.</th>
					<th width="45%">プロジェクト名</th>
					<!--<th>最新バージョン</th>-->
					<th>プロジェクト情報入力</th>
					<th>工事概要入力</th>
					<th>工事概要出力</th>
					<th>BIM実行計画書出力</th>
					<th>プロジェクト削除</th>
				</tr>
			</thead>
			<tbody>
				@if (count($projects) > 0)
	
				@php($count=0)
					@foreach($projects as $project)
						@php($count++)
						<tr id="{{$project['id']}}">
						<td>{{$count}}</td>
							<td>{{$project["name"]}}</td>
							<!--<td>{{$project["version"]}}</td>-->
							<td><button class="clear-decoration-btn" onclick="EditProjectManagementInfo({{$project['id']}})"><img src='../public/image/edit.png' alt='' height='20' width='20' /></button></td>
							<td><button class="clear-decoration-btn" onclick="EditProject({{$project['id']}})"><img src='../public/image/edit.png' alt='' height='20' width='20' /></button></td>
							<td><button class="clear-decoration-btn" type="submit" onclick="ExcelDownloadKouji({{$project['id']}})"><img src='../public/image/excel.png' alt='' height='20' width='20' /></button></td>
							<td><button class="clear-decoration-btn" type="submit" onclick="WordOutput({{$project['id']}})"><img src='../public/image/word.png' alt='' height='20' width='20' /></button></td>
							<td><button class="clear-decoration-btn" onclick="DeleteProject({{$project['id']}})"><img src='../public/image/trash.png' alt='' height='20' width='15' /></button></td>
						</tr>
					@endforeach
				@else
				<tr>
					<td height= "20"></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
	            @endif

			</tbody>	
		</table>
    </div>

</div>
@endsection