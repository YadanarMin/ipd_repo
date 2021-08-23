/* ajax通信トークン定義 */
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

var g_projectName = "";
var g_storeData = [];

$(document).ready(function(){
	
    var login_user_id = $("#hidLoginID").val();
    var img_src = "../public/image/JPG/会員証のアイコン素材.jpeg";
    var url = "prjmgt/index";
    var content_name = "ﾌﾟﾛｼﾞｪｸﾄ管理";
    recordAccessHistory(login_user_id,img_src,url,content_name);
	
	$('#txtSearch').keyup(function(){
		var textboxValue = $('#txtSearch').val();
		$("#tbProject tr").each(function(index) {
		    if (index !== 0) {
		        $row = $(this);
		        var projectName = $row.find("td:nth-child(2)").text();
		        if(!projectName.includes(textboxValue)){
		            $row.hide();
		        }else{
		            $row.show();
		        }
		    }
		});
	});
	
    $('.BIMImpDocContent').slick({
        arrows: true,
        autoplay: false,
        infinite: false,
        slidesToShow: 1,
        slidesToScroll: 1,
        appendDots: true,
        draggable: false,
        prevArrow:'<img src="../public/image/arrow_left.png" style="height:25px;margin-top:330px;" class="slide-arrow prev-arrow">',
        nextArrow:'<img src="../public/image/arrow_right.png" style="height:25px;margin-top:330px;" class="slide-arrow next-arrow">',
    });
});

function SaveProject()
{	
	var projectName = $("#projName").val();
	if(projectName != "")
	{
		$.ajax({
			type:"POST",
			url:"../prjmgt/saveData",
			data:{_token: CSRF_TOKEN,message:"save_project",name:projectName},
			success:function(data) {
				if(data.includes("success"))
				{
					alert("プロジェクト情報登録しました。");
					location.reload();
				}
			},
	        error:function(err){
	            console.log(err);
	        }
		});
	}
} 

var oldProjectName = "";
function EditProject(paramID)
{
	$("#tbProject  tr").each(function(){
		
		var trID = $(this).attr('id');
		if(trID == 'undefined') return true;

		if(trID == paramID)
		{
			oldProjectName = $(this).find("td:eq(1)").text();
			$("#prjName").val(oldProjectName);
			$("#overlayEditProject").css({ visibility: "visible",opacity: "1"});
		}
	});
	
	$.ajax({
		type:"POST",
		url:"../prjmgt/getData",			   
		data:{_token: CSRF_TOKEN,message:"get_project",name:oldProjectName},
		success:function(data)
		{
			var results = JSON.parse(data);
			var result = results[0];
			$("#koujimeisho").val(result["koujimeisho"]);
			$("#sekoubasho").val(result["sekoubasho"]);
			// $("#hachuusha").val(result["hachuusha"]);			//*****取得元の変更
			// $("#sekkeisha").val(result["sekkeisha"]);			//*****取得元の変更
			$("#koujikanrisha").val(result["koujikanrisha"]);
			$("#sekousha").val(result["sekousha"]);
			// $("#meisho").val(result["meisho"]);					//*****取得元の変更
			$("#shozonchi").val(result["shozonchi"]);
			$("#denwa").val(result["denwa"]);
			$("#fax").val(result["fax"]);
			$("#shokatsurokisho").val(result["shokatsurokisho"]);
			// $("#startTime").val(result["startTime"]);			//*****取得元の変更
			// $("#endTime").val(result["endTime"]);				//*****取得元の変更
			$("#timeInterval").val(result["timeInterval"]);
			// $("#kenchikuyoto").val(result["kenchikuyoto"]);		//*****取得元の変更
			// $("#kozo").val(result["kozo"]);						//*****取得元の変更
			// $("#zouchijyo").val(result["zouchijyo"]);			//*****取得元の変更
			// $("#kaichika").val(result["kaichika"]);				//*****取得元の変更
			$("#kaitouya").val(result["kaitouya"]);
			$("#glPlus").val(result["glPlus"]);
			$("#glMinus").val(result["glMinus"]);
			$("#kussakufukasa").val(result["kussakufukasa"]);
			$("#okujyou").val(result["okujyou"]);
			$("#gaisou").val(result["gaisou"]);
			$("#shikichimenseki").val(result["shikichimenseki"]);
			$("#kenchikumenseki").val(result["kenchikumenseki"]);
			// $("#yukamenseki").val(result["yukamenseki"]);		//*****取得元の変更
			$("#ukeoikin").val(result["ukeoikin"]);
			$("#shikyuzai").val(result["shikyuzai"]);
		},
        error:function(err){
            console.log(err);
        }
	});
	
	$.ajax({
		type:"POST",
		url:"../prjmgt/getData",
		data:{_token: CSRF_TOKEN,message:"getImplementationDocByProject",name:oldProjectName},
		success:function(data)
		{
			var results = JSON.parse(data);
			var result = results[0];
			// console.log(result);
			$("#hachuusha").val(result["orderer"]);
			$("#sekkeisha").val(result["sekkeisya"]);
			$("#meisho").val(result["kouji_jimusyo"]);
			$("#startTime").val(result["tyakkou"]);
			$("#endTime").val(result["syunkou"]);
			$("#kenchikuyoto").val(result["building_use"]);
			$("#kozo").val(result["kouzou"]);
			$("#zouchijyo").val(result["tijou"]);
			$("#kaichika").val(result["tika"]);
			$("#yukamenseki").val(result["total_floor_area"]);
		},
        error:function(err){
            console.log(err);
        }
	});
	
}

function UpdateProjectName()
{
	var projectName = $("#prjName").val();

	var koujimeisho = $("#koujimeisho").val();
	var sekoubasho = $("#sekoubasho").val();
	var hachuusha = $("#hachuusha").val();			//*****保存先の変更
	var sekkeisha = $("#sekkeisha").val();			//*****保存先の変更
	var koujikanrisha = $("#koujikanrisha").val();
	var sekousha = $("#sekousha").val();
	var meisho = $("#meisho").val();				//*****保存先の変更
	var shozonchi = $("#shozonchi").val();
	var denwa = $("#denwa").val();
	var fax = $("#fax").val();
	var shokatsurokisho = $("#shokatsurokisho").val();
	var startTime = $("#startTime").val();			//*****保存先の変更
	var endTime = $("#endTime").val();				//*****保存先の変更
	var timeInterval = $("#timeInterval").val();
	var kenchikuyoto = $("#kenchikuyoto").val();	//*****保存先の変更
	var kozo = $("#kozo").val();					//*****保存先の変更
	var zouchijyo = $("#zouchijyo").val();			//*****保存先の変更
	var kaichika = $("#kaichika").val();			//*****保存先の変更
	var kaitouya = $("#kaitouya").val();
	var glPlus = $("#glPlus").val();
	var glMinus = $("#glMinus").val();
	var kussakufukasa = $("#kussakufukasa").val();
	var okujyou = $("#okujyou").val();
	var gaisou = $("#gaisou").val();
	var shikichimenseki = $("#shikichimenseki").val();
	var kenchikumenseki = $("#kenchikumenseki").val();
	var yukamenseki = $("#yukamenseki").val();		//*****保存先の変更
	var ukeoikin = $("#ukeoikin").val();
	var shikyuzai = $("#shikyuzai").val();

	// var data = [projectName,koujimeisho,sekoubasho,hachuusha,sekkeisha,koujikanrisha,sekousha,meisho,shozonchi,denwa,fax,shokatsurokisho,
	// 			startTime,endTime,timeInterval,kenchikuyoto,kozo,zouchijyo,kaichika,kaitouya,
	// 			glPlus,glMinus,kussakufukasa,okujyou,gaisou,shikichimenseki,kenchikumenseki,yukamenseki,ukeoikin,shikyuzai];
	var data = [projectName,koujimeisho,sekoubasho,koujikanrisha,sekousha,shozonchi,denwa,fax,shokatsurokisho,
				timeInterval,kaitouya,
				glPlus,glMinus,kussakufukasa,okujyou,gaisou,shikichimenseki,kenchikumenseki,ukeoikin,shikyuzai];
				
	var data2 = {"hachuusha":hachuusha,"sekkeisha":sekkeisha,"meisho":meisho,"startTime":startTime,"endTime":endTime,
				 "kenchikuyoto":kenchikuyoto,"kozo":kozo,"zouchijyo":zouchijyo,"kaichika":kaichika,"yukamenseki":yukamenseki};
	
	var saveCnt = 0;
	$.ajax({
		type:"POST",
		url:"../prjmgt/saveData",			   
		data:{_token: CSRF_TOKEN,message:"edit_project",newName:projectName,oldName:oldProjectName,updateData:JSON.stringify(data)},
		success:function(data)
		{			
			saveCnt++;
			if(saveCnt == 2){
				alert("プロジェクト情報を更新しました。");
				$("#overlayEditProject").css({ visibility: "hidden",opacity: "0"});
				location.reload();
			}
		},
        error:function(err){
            console.log(err);
        }
	});
	
	$.ajax({
		type:"POST",
		url:"../prjmgt/saveData",
		data:{_token: CSRF_TOKEN,message:"edit_project2",newName:projectName,oldName:oldProjectName,updateData:data2},
		success:function(data)
		{			
			saveCnt++;
			if(saveCnt == 2){
				alert("プロジェクト情報を更新しました。");
				$("#overlayEditProject").css({ visibility: "hidden",opacity: "0"});
				location.reload();
			}
		},
        error:function(err){
            console.log(err);
        }
	});
}

function DeleteProject(paramID)
{
	var projectName = "";
	$("#tbProject  tr").each(function(){		
		var trID = $(this).attr('id');
		if(trID == 'undefined') return true;
		if(trID == paramID)
		{
		  projectName = $(this).find("td:eq(1)").text();
			return false;			
		}	
	});

	if(projectName != "")
	{
		var result = confirm("プロジェクト名："+projectName+"\nこのプロジェクトを削除しますか？");
		if (result == true) {
			$.ajax({
				type:"POST",
				url:"../prjmgt/deleteData",			   
				data:{_token: CSRF_TOKEN,message:"delete_project",name:projectName},
				success:function(data)
				{
					if(data.includes("success"))
					{
						alert("プロジェクトを削除しました。");					
						location.reload();
					}else{
						alert(data);
					}
				},
		        error:function(err){
		            console.log(err);
		        }
			});								
		} else {
			return false;
		}		
	}
}

function ExcelDownloadKouji(paramID){
	/*var projectName = "";
	$("#tbProject  tr").each(function(){		
		var trID = $(this).attr('id');
		if(trID == 'undefined') return true;
		if(trID == paramID)
		{
		  projectName = $(this).find("td:eq(1)").text();
			return false;			
		}	
	});*/

		window.location="/iPD/forge/excelDownloadKouji?project_id="+paramID;
		/*var formName = "excelOutput"+paramID+"";
		document.forms[formName].action = url;//multiple form submit action
		document.forms[formName].submit();*/
/*	$.ajax({
		type:"POST",
		url:"/RevitWebSystem/Project/getProject.php",			   
		data:{message:"get_project",name:projectName},
		success:function(result)
		{
			var data = JSON.parse(result);
			alert(data);
			
			var url = '/RevitWebSystem/Project/exceloutput.php?data='+JSON.stringify(data[0]);
			var formName = "excelOutput"+paramID+"";
			document.forms[formName].action = url;//multiple form submit action
			document.forms[formName].submit();
			
		}
	});*/
}

function ExcelDownloadMngBIMmnka(paramID){
	window.location="/iPD/forge/excelDownloadMngBIM?project_id="+paramID;
}

function CloseCurrentEditProjectPopup(){
	$("#overlayEditProject").css({ visibility: "hidden" });			
}

function EditProjectManagementInfo(paramID)
{
	oldProjectName = "";
	
	$("#tbProject  tr").each(function(){
		
		var trID = $(this).attr('id');
		if(trID == 'undefined') return true;

		if(trID == paramID)
		{
			oldProjectName = $(this).find("td:eq(1)").text();
			$("#project_name").val(oldProjectName);
			$("#overlayEditBIMImplementationDocument").css({ visibility: "visible",opacity: "1"});			
		}
	});

	$.ajax({
		type:"POST",
		url:"../prjmgt/getData",
		data:{_token: CSRF_TOKEN,message:"getImplementationDocByProject",name:oldProjectName},
		success:function(data)
		{
			var results = JSON.parse(data);
			var result = results[0];
			// console.log(result);
			
			$("#project_name").val(result["project_name"]);
			$("#doc_version").val(result["version"]);
			// $("#hattyuusya").val(result["orderer"]);
			// $("#address").val(result["address"]);
			// $("#building_use").val(result["building_use"]);
			// $("#building_num").val(result["building_num"]);
			// $("#tika").val(result["tika"]);
			// $("#tijou").val(result["tijou"]);
			// $("#total_floor_area").val(result["total_floor_area"]);
			
			// $("#project_code").val(result["project_code"]);
			// $("#kouji_kikan_code").val(result["kouji_kikan_code"]);
			// $("#branch_store").val(result["branch_store"]);
			// $("#construction_type").val(result["construction_type"]);
			// $("#sekkeisya").val(result["sekkeisya"]);
			// $("#tyakkou").val(result["tyakkou"]);
			// $("#syunkou").val(result["syunkou"]);
			// $("#kouzou").val(result["kouzou"]);
			// $("#kouji_jimusyo").val(result["kouji_jimusyo"]);
			
			$("#box_date1").val(result["box_date1"]);
			$("#box_upload_file1").val(result["box_upload_file1"]);
			$("#box_rev_person1").val(result["box_rev_person1"]);
			$("#box_date2").val(result["box_date2"]);
			$("#box_upload_file2").val(result["box_upload_file2"]);
			$("#box_rev_person2").val(result["box_rev_person2"]);
			$("#box_date3").val(result["box_date3"]);
			$("#box_upload_file3").val(result["box_upload_file3"]);
			$("#box_rev_person3").val(result["box_rev_person3"]);
			// $("#ken_org").val(result["ken_org"]);
			// $("#ken_name").val(result["ken_name"]);
			// $("#kou_org").val(result["kou_org"]);
			// $("#kou_name").val(result["kou_name"]);
			// $("#sku_org").val(result["sku_org"]);
			// $("#sku_name").val(result["sku_name"]);
			// $("#sde_org").val(result["sde_org"]);
			// $("#sde_name").val(result["sde_name"]);
			// $("#sek_org").val(result["sek_org"]);
			// $("#sek_name").val(result["sek_name"]);
			// $("#sei_org").val(result["sei_org"]);
			// $("#sei_name").val(result["sei_name"]);
			// $("#koj_org").val(result["koj_org"]);
			// $("#koj_name").val(result["koj_name"]);
			// $("#sgi_org").val(result["sgi_org"]);
			// $("#sgi_name").val(result["sgi_name"]);
			// $("#smi_org").val(result["smi_org"]);
			// $("#smi_name").val(result["smi_name"]);
			// $("#bmn_org").val(result["bmn_org"]);
			// $("#bmn_name").val(result["bmn_name"]);
			// $("#pds_org").val(result["pds_org"]);
			// $("#pds_name").val(result["pds_name"]);
			$("#mdl_org").val(result["mdl_org"]);
			$("#mdl_name").val(result["mdl_name"]);
			$("#sbk_org").val(result["sbk_org"]);
			$("#sbk_name").val(result["sbk_name"]);
			$("#sbd_org").val(result["sbd_org"]);
			$("#sbd_name").val(result["sbd_name"]);
			$("#fsa_org").val(result["fsa_org"]);
			$("#fsa_name").val(result["fsa_name"]);
			$("#fse_org").val(result["fse_org"]);
			$("#fse_name").val(result["fse_name"]);
			// $("#make_model_start").val(result["make_model_start"]);
			// $("#make_model_end").val(result["make_model_end"]);
			$("#make_model_bikou").val(result["make_model_bikou"]);
			// $("#sinsei_start").val(result["sinsei_start"]);
			// $("#sinsei_end").val(result["sinsei_end"]);
			$("#sinsei_bikou").val(result["sinsei_bikou"]);
			// $("#seisan_start").val(result["seisan_start"]);
			// $("#seisan_end").val(result["seisan_end"]);
			$("#seisan_bikou").val(result["seisan_bikou"]);
			// $("#kouji_start").val(result["kouji_start"]);
			// $("#kouji_end").val(result["kouji_end"]);
			$("#kouji_bikou").val(result["kouji_bikou"]);
			// $("#genba_start").val(result["genba_start"]);
			// $("#genba_end").val(result["genba_end"]);
			$("#genba_bikou").val(result["genba_bikou"]);
			// $("#sekou_start").val(result["sekou_start"]);
			// $("#sekou_end").val(result["sekou_end"]);
			$("#sekou_bikou").val(result["sekou_bikou"]);
			// $("#hiki_start").val(result["hiki_start"]);
			// $("#hiki_end").val(result["hiki_end"]);
			$("#hiki_bikou").val(result["hiki_bikou"]);
			$("#ken_sw").val(result["ken_sw"]);
			$("#kou_sw").val(result["kou_sw"]);
			$("#sku_sw").val(result["sku_sw"]);
			$("#sde_sw").val(result["sde_sw"]);
			$("#mdl_sw").val(result["mdl_sw"]);
			$("#sek_sw").val(result["sek_sw"]);
			$("#sei_sw").val(result["sei_sw"]);
			$("#sbk_sw").val(result["sbk_sw"]);
			$("#sbd_sw").val(result["sbd_sw"]);
			$("#fsa_sw").val(result["fsa_sw"]);
			$("#fse_sw").val(result["fse_sw"]);
			$("#base_linex").val(result["base_linex"]);
			$("#base_liney").val(result["base_liney"]);
			$("#rev_ver1").val(result["rev_ver1"]);
			$("#rev_date1").val(result["rev_date1"]);
			$("#rev_contents1").val(result["rev_contents1"]);
			$("#rev_name1").val(result["rev_name1"]);
			$("#rev_ver2").val(result["rev_ver2"]);
			$("#rev_date2").val(result["rev_date2"]);
			$("#rev_contents2").val(result["rev_contents2"]);
			$("#rev_name2").val(result["rev_name2"]);
			$("#rev_ver3").val(result["rev_ver3"]);
			$("#rev_date3").val(result["rev_date3"]);
			$("#rev_contents3").val(result["rev_contents3"]);
			$("#rev_name3").val(result["rev_name3"]);
			
			
			loadInitialData();
		},
        error:function(err){
            console.log(err);
        }
	});
}

function loadInitialData(){
	
    $.ajax({
        url: "../allstore/getData",
        type: 'post',
        data:{_token: CSRF_TOKEN},
        success :function(data) {
            if(data != null){
            	// console.log(data);

			    loadDefaultInputData(data);
            }
        },
        error:function(err){
            console.log(err);
        }
    });
}

function loadDefaultInputData(data){
	
    var src_pj_name = $("#project_name").val();
    
	for(let i = 0; i < data.length; i++) {
		var pj_name = data[i]["b_tmp_pj_name"];
		var pj_code = data[i]["a_pj_code"];
		
		if(src_pj_name === pj_name){
			$("#project_code").val(data[i]["a_pj_code"]);
			$("#kouji_kikan_code").val(data[i]["a_kouji_kikan_code"]);
			$("#branch_store").val(data[i]["a_shiten"]);
			// $("#project_name").val(data[i]["b_tmp_pj_name"]);
			$("#construction_type").val(data[i]["a_kouji_kubun"]);
			var youto = data[i]["b_youto"] === "" ? data[i]["a_youto1"]: data[i]["b_youto"];
			$("#building_use").val(youto);
			var sekou_basyo = data[i]["b_sekou_basyo"] === "" ? data[i]["a_sekou_basyo"]: data[i]["b_sekou_basyo"];
			$("#address").val(sekou_basyo);
			$("#hattyuusya").val(data[i]["b_hattyuusya"]);
			var sekkei = data[i]["b_sekkeisya1"] === "" ? data[i]["a_sekkei"]: data[i]["b_sekkeisya1"];
			$("#sekkeisya").val(sekkei);
			$("#tyakkou").val(data[i]["b_koutei_kouji_start"]);
			$("#syunkou").val(data[i]["b_koutei_kouji_end"]);
			var kouzou = data[i]["b_kouzou"] === "" ? data[i]["a_kouzou"]: data[i]["b_kouzou"];
			$("#kouzou").val(kouzou);
			var tika = data[i]["b_tika"] === "" ? data[i]["a_tika"]: data[i]["b_tika"];
			$("#tika").val(tika);
			var tijou = data[i]["b_tijo"] === "" ? data[i]["a_tijo"]: data[i]["b_tijo"];
			$("#tijou").val(tijou);
			var total_floor_area = data[i]["b_nobe_menseki"] === "" ? data[i]["a_nobe_menseki"]: data[i]["b_nobe_menseki"];
			$("#total_floor_area").val(total_floor_area);
			var kaisuu = data[i]["b_kaisuu"] === "" ? data[i]["a_kaisuu"]: data[i]["b_kaisuu"];
			$("#building_num").val(kaisuu);
			$("#kouji_jimusyo").val(data[i]["b_kouji_jimusyo"]);
			
			$("#ken_org").val(data[i]["b_isyou_syozoku"]);	//建築設計組織
			$("#ken_name").val(data[i]["b_isyou_sekkei"]);	//建築設計担当者
			$("#kou_org").val(data[i]["b_kouzou_syozoku"]);	//構造設計組織
			$("#kou_name").val(data[i]["b_kouzou_sekkei"]);	//構造設計担当者
			$("#sku_org").val(data[i]["b_setubi_kuutyou_syozoku"]);	//設備空調組織
			$("#sku_name").val(data[i]["b_setubi_kuutyou_sekkei"]);	//設備空調担当者
			$("#sde_org").val(data[i]["b_setubi_denki_syozoku"]);	//設備電気組織
			$("#sde_name").val(data[i]["b_setubi_denki_sekkei"]);	//設備電気担当者
			$("#sek_org").val(data[i]["b_sekou_syozoku"]);	//施工組織
			$("#sek_name").val(data[i]["b_sekou_tantou"]);	//施工担当者
			$("#sei_org").val(data[i]["b_seisan_sekkei_syozoku"]);	//生産設計組織
			$("#sei_name").val(data[i]["b_seisan_sekkei_tantou"]);	//生産設計担当者
			$("#koj_org").val(data[i]["b_koujibu_syozoku"]);	//工事部組織
			$("#koj_name").val(data[i]["b_koujibu_tantou"]);	//工事部担当者
			$("#sgi_org").val(data[i]["b_seisan_gijutu_syozoku"]);	//生産技術組織
			$("#sgi_name").val(data[i]["b_seisan_gijutu_tantou"]);	//生産技術担当者
			$("#smi_org").val(data[i]["b_sekisan_mitumori_syozoku"]);	//積算見積組織
			$("#smi_name").val(data[i]["b_sekisan_mitumori_tantou"]);	//積算見積担当者
			$("#bmn_org").val(data[i]["b_bim_maneka_syozoku"]);	//BIMマネ課組織
			$("#bmn_name").val(data[i]["b_bim_maneka_tantou"]);	//BIMマネ課担当者
			$("#pds_org").val(data[i]["b_ipd_center_syozoku"]);	//PDセンター組織
			$("#pds_name").val(data[i]["b_ipd_center_tantou"]);	//PDセンター担当者
			
			$("#make_model_start").val(data[i]["b_koutei_sekkei_model_start"]);
			$("#make_model_end").val(data[i]["b_koutei_sekkei_model_end"]);
			$("#sinsei_start").val(data[i]["b_koutei_kakunin_sinsei_start"]);
			$("#sinsei_end").val(data[i]["b_koutei_kakunin_sinsei_end"]);
			$("#seisan_start").val(data[i]["b_koutei_sekisan_model_tougou_start"]);
			$("#seisan_end").val(data[i]["b_koutei_sekisan_model_tougou_end"]);
			$("#kouji_start").val(data[i]["b_koutei_kouji_juujisya_kettei_start"]);
			$("#kouji_end").val(data[i]["b_koutei_kouji_juujisya_kettei_end"]);
			$("#genba_start").val(data[i]["b_koutei_genba_koutei_kettei_start"]);
			$("#genba_end").val(data[i]["b_koutei_genba_koutei_kettei_end"]);
			var tyakkou = data[i]["b_tyakkou"] === "" ? data[i]["a_tyakkou"]: data[i]["b_tyakkou"];
			$("#sekou_start").val(tyakkou);
			var syunkou = data[i]["b_syunkou"] === "" ? data[i]["a_syunkou"]: data[i]["b_syunkou"];
			$("#sekou_end").val(syunkou);
			$("#hiki_start").val(data[i]["b_handover_start"]);
			$("#hiki_end").val(data[i]["b_handover_end"]);
		}
	}

}

function resetInputValue(){
	
	$("#project_name").val("");
	$("#doc_version").val("");
	$("#hattyuusya").val("");
	$("#address").val("");
	$("#building_use").val("");
	$("#building_num").val("");
	$("#tika").val("");
	$("#tijou").val("");
	$("#total_floor_area").val("");
	
	$("#project_code").val("");
	$("#kouji_kikan_code").val("");
	$("#branch_store").val("");
	$("#construction_type").val("");
	$("#sekkeisya").val("");
	$("#tyakkou").val("");
	$("#syunkou").val("");
	$("#kouzou").val("");
	$("#kouji_jimusyo").val("");
	
	$("#box_date1").val("");
	$("#box_upload_file1").val("");
	$("#box_rev_person1").val("");
	$("#box_date2").val("");
	$("#box_upload_file2").val("");
	$("#box_rev_person2").val("");
	$("#box_date3").val("");
	$("#box_upload_file3").val("");
	$("#box_rev_person3").val("");
	$("#ken_org").val("");
	$("#ken_name").val("");
	$("#kou_org").val("");
	$("#kou_name").val("");
	$("#sku_org").val("");
	$("#sku_name").val("");
	$("#sde_org").val("");
	$("#sde_name").val("");
	$("#sek_org").val("");
	$("#sek_name").val("");
	$("#sei_org").val("");
	$("#sei_name").val("");
	$("#koj_org").val("");
	$("#koj_name").val("");
	$("#sgi_org").val("");
	$("#sgi_name").val("");
	$("#smi_org").val("");
	$("#smi_name").val("");
	$("#bmn_org").val("");
	$("#bmn_name").val("");
	$("#pds_org").val("");
	$("#pds_name").val("");
	$("#mdl_org").val("");
	$("#mdl_name").val("");
	$("#sbk_org").val("");
	$("#sbk_name").val("");
	$("#sbd_org").val("");
	$("#sbd_name").val("");
	$("#fsa_org").val("");
	$("#fsa_name").val("");
	$("#fse_org").val("");
	$("#fse_name").val("");
	$("#make_model_start").val("");
	$("#make_model_end").val("");
	$("#make_model_bikou").val("");
	$("#sinsei_start").val("");
	$("#sinsei_end").val("");
	$("#sinsei_bikou").val("");
	$("#seisan_start").val("");
	$("#seisan_end").val("");
	$("#seisan_bikou").val("");
	$("#kouji_start").val("");
	$("#kouji_end").val("");
	$("#kouji_bikou").val("");
	$("#genba_start").val("");
	$("#genba_end").val("");
	$("#genba_bikou").val("");
	$("#sekou_start").val("");
	$("#sekou_end").val("");
	$("#sekou_bikou").val("");
	$("#hiki_start").val("");
	$("#hiki_end").val("");
	$("#hiki_bikou").val("");
	$("#ken_sw").val("");
	$("#kou_sw").val("");
	$("#sku_sw").val("");
	$("#sde_sw").val("");
	$("#mdl_sw").val("");
	$("#sek_sw").val("");
	$("#sei_sw").val("");
	$("#sbk_sw").val("");
	$("#sbd_sw").val("");
	$("#fsa_sw").val("");
	$("#fse_sw").val("");
	$("#base_linex").val("");
	$("#base_liney").val("");
	$("#rev_ver1").val("");
	$("#rev_date1").val("");
	$("#rev_contents1").val("");
	$("#rev_name1").val("");
	$("#rev_ver2").val("");
	$("#rev_date2").val("");
	$("#rev_contents2").val("");
	$("#rev_name2").val("");
	$("#rev_ver3").val("");
	$("#rev_date3").val("");
	$("#rev_contents3").val("");
	$("#rev_name3").val("");
}

function UpdateImplementationDocument()
{
	var project_name	= $("#project_name").val();
	var version			= $("#doc_version").val();
	var hattyuusya		= $("#hattyuusya").val();
	var address			= $("#address").val();
	var building_use	= $("#building_use").val();
	var building_num	= $("#building_num").val();
	var tika		    = $("#tika").val();
	var tijou	    	= $("#tijou").val();
	var total_floor_area = $("#total_floor_area").val();
	
	var project_code		= $("#project_code").val();
	var kouji_kikan_code	= $("#kouji_kikan_code").val();
	var branch_store		= $("#branch_store").val();
	var construction_type	= $("#construction_type").val();
	var sekkeisya			= $("#sekkeisya").val();
	var tyakkou				= $("#tyakkou").val();
	var syunkou				= $("#syunkou").val();
	var kouzou				= $("#kouzou").val();
	var kouji_jimusyo		= $("#kouji_jimusyo").val();
	
	var box_date1 = $("#box_date1").val();	var box_upload_file1 = $("#box_upload_file1").val();	var box_rev_person1 = $("#box_rev_person1").val();
	var box_date2 = $("#box_date2").val();	var box_upload_file2 = $("#box_upload_file2").val();	var box_rev_person2 = $("#box_rev_person2").val();
	var box_date3 = $("#box_date3").val();	var box_upload_file3 = $("#box_upload_file3").val();	var box_rev_person3 = $("#box_rev_person3").val();
	
	var ken_org = $("#ken_org").val();	var ken_name = $("#ken_name").val();
	var kou_org = $("#kou_org").val();	var kou_name = $("#kou_name").val();
	var sku_org = $("#sku_org").val();	var sku_name = $("#sku_name").val();
	var sde_org = $("#sde_org").val();	var sde_name = $("#sde_name").val();
	var sek_org = $("#sek_org").val();	var sek_name = $("#sek_name").val();
	var sei_org = $("#sei_org").val();	var sei_name = $("#sei_name").val();
	var koj_org = $("#koj_org").val();	var koj_name = $("#koj_name").val();
	var sgi_org = $("#sgi_org").val();	var sgi_name = $("#sgi_name").val();
	var smi_org = $("#smi_org").val();	var smi_name = $("#smi_name").val();
	var bmn_org = $("#bmn_org").val();	var bmn_name = $("#bmn_name").val();
	var pds_org = $("#pds_org").val();	var pds_name = $("#pds_name").val();
	var mdl_org = $("#mdl_org").val();	var mdl_name = $("#mdl_name").val();
	var sbk_org = $("#sbk_org").val();	var sbk_name = $("#sbk_name").val();
	var sbd_org = $("#sbd_org").val();	var sbd_name = $("#sbd_name").val();
	var fsa_org = $("#fsa_org").val();	var fsa_name = $("#fsa_name").val();
	var fse_org = $("#fse_org").val();	var fse_name = $("#fse_name").val();
	
	var make_model_start = $("#make_model_start").val();	var make_model_end = $("#make_model_end").val();	var make_model_bikou = $("#make_model_bikou").val();
	var sinsei_start = $("#sinsei_start").val();	var sinsei_end = $("#sinsei_end").val();	var sinsei_bikou = $("#sinsei_bikou").val();
	var seisan_start = $("#seisan_start").val();	var seisan_end = $("#seisan_end").val();	var seisan_bikou = $("#seisan_bikou").val();
	var kouji_start = $("#kouji_start").val();		var kouji_end = $("#kouji_end").val();		var kouji_bikou = $("#kouji_bikou").val();
	var genba_start = $("#genba_start").val();		var genba_end = $("#genba_end").val();		var genba_bikou = $("#genba_bikou").val();
	var sekou_start = $("#sekou_start").val();		var sekou_end = $("#sekou_end").val();		var sekou_bikou = $("#sekou_bikou").val();
	var hiki_start = $("#hiki_start").val();		var hiki_end = $("#hiki_end").val();		var hiki_bikou = $("#hiki_bikou").val();
	
	var ken_sw = $("#ken_sw").val();	var kou_sw = $("#kou_sw").val();	var sku_sw = $("#sku_sw").val();
	var sde_sw = $("#sde_sw").val();	var mdl_sw = $("#mdl_sw").val();	var sek_sw = $("#sek_sw").val();
	var sei_sw = $("#sei_sw").val();	var sbk_sw = $("#sbk_sw").val();	var sbd_sw = $("#sbd_sw").val();
	var fsa_sw = $("#fsa_sw").val();	var fse_sw = $("#fse_sw").val();
	
	var base_linex = $("#base_linex").val();
	var base_liney = $("#base_liney").val();

	var rev_ver1 = $("#rev_ver1").val();	var rev_date1 = $("#rev_date1").val();	var rev_contents1 = $("#rev_contents1").val();	var rev_name1 = $("#rev_name1").val();
	var rev_ver2 = $("#rev_ver2").val();	var rev_date2 = $("#rev_date2").val();	var rev_contents2 = $("#rev_contents2").val();	var rev_name2 = $("#rev_name2").val();
	var rev_ver3 = $("#rev_ver3").val();	var rev_date3 = $("#rev_date3").val();	var rev_contents3 = $("#rev_contents3").val();	var rev_name3 = $("#rev_name3").val();

	var data = [project_name,version,hattyuusya,address,building_use,building_num,tika,tijou,total_floor_area,
				project_code,kouji_kikan_code,branch_store,construction_type,sekkeisya,tyakkou,syunkou,kouzou,kouji_jimusyo,
				box_date1,box_upload_file1,box_rev_person1,box_date2,box_upload_file2,box_rev_person2,box_date3,box_upload_file3,box_rev_person3,
				ken_org,ken_name,kou_org,kou_name,sku_org,sku_name,sde_org,sde_name,sek_org,sek_name,sei_org,sei_name,koj_org,koj_name,sgi_org,sgi_name,
				smi_org,smi_name,bmn_org,bmn_name,pds_org,pds_name,mdl_org,mdl_name,sbk_org,sbk_name,sbd_org,sbd_name,fsa_org,fsa_name,fse_org,fse_name,
				make_model_start,make_model_end,make_model_bikou,sinsei_start,sinsei_end,sinsei_bikou,seisan_start,seisan_end,seisan_bikou,
				kouji_start,kouji_end,kouji_bikou,genba_start,genba_end,genba_bikou,sekou_start,sekou_end,sekou_bikou,hiki_start,hiki_end,hiki_bikou,
				ken_sw,kou_sw,sku_sw,sde_sw,mdl_sw,sek_sw,sei_sw,sbk_sw,sbd_sw,fsa_sw,fse_sw,base_linex,base_liney,
				rev_ver1,rev_date1,rev_contents1,rev_name1,rev_ver2,rev_date2,rev_contents2,rev_name2,rev_ver3,rev_date3,rev_contents3,rev_name3];
				
	$.ajax({
		type:"POST",
		url:"../prjmgt/saveData",
		data:{_token: CSRF_TOKEN,message:"edit_implementation_doc",newName:project_name,oldName:oldProjectName,updateData:JSON.stringify(data)},
		success:function(data)
		{
			alert("更新が完了しました。");
			$("#overlayEditBIMImplementationDocument").css({ visibility: "hidden",opacity: "0"});
			location.reload();
		},
        error:function(err){
            console.log(err);
        }
	});
}

function CloseCurrentEditImplementDocPopup(){
	
	resetInputValue();
	
	$("#overlayEditBIMImplementationDocument").css({ visibility: "hidden" });			
}

function WordOutput(paramID){
	window.location="/iPD/forge/wordDownloadImplementation?project_id="+paramID;
}

function ImportBuildingInfo(){
	g_projectName = document.getElementById('project_name').value;
	
	console.log("ImportBuildingInfo start");

    $.ajax({
        url: "../allstore/getData",
        type: 'post',
        data:{_token: CSRF_TOKEN},
        success :function(data) {
            if(data != null){
            	
            	// 一致するプロジェクト情報を取得
            	var storeData = getStoreData(g_projectName,data);
            	g_storeData = storeData;
            	
            	console.log(storeData);
            	
            	// 既存のプロジェクト情報と差異チェックし、差異項目リストを生成
            	var append = chkDiffData(storeData);
            	
            	if(append == ""){
	            	alert("追加可能な情報がありません。");
            	}else{
					showConfirmDialog(append, executeTask);
            	}
            }
        },
        error:function(err){
            console.log(err);
        }
    });

}

function ImportForgeInfo(){
	alert("未実装");
}

function executeTask() {

	// 情報取得(プロジェクト名)
	// 支店名/工事区分:searchConsoleフィルタに追加するために必要 -> BIM実行計画書とは別に表示させたほうが良さそうだが検討が必要 *それならもっと多くの情報を集積しておく必要があるのでは？
	// 発注者,用途,階数(地下地上を合算?),延べ床面積
	overwriteImportData(g_projectName,g_storeData);

}

function getStoreData(project_name,allstoreData){
	
	var result = [];
	console.log("getStoreData start");

    for(var i=0; i< allstoreData.length; i++){
        
        var storeData = allstoreData[i];
        
        if(storeData["project_name"] === ""){
        	continue;
        }

		if(project_name.includes(storeData["project_name"])){
			console.log("project_name is match.");
			console.log("project_name:"+project_name+"\nstoreData['project_name']:"+storeData["project_name"]);

			result = storeData;
			break;
		}
    }
    
    return result;
}

function chkDiffData(storeData){
	
	var result = "";
	
	if(storeData.length === 0){
		return result;
	}
	
	var tmptd = chkDiffDataProc(storeData);

	if(Object.keys(tmptd).length !== 0){
		result += "<thead>";
		result +=   "<tr>";
		result +=     "<th></th>";
		result +=     "<th>現在</th>";
		result +=     "<th>インポート後</th>";
		result +=   "</tr>";
		result += "</thead>";
		
		result += "<tbody>";
		for(let key in tmptd) {
			result += "<tr>";
			result +=   "<td>"+ key +"</td>";
			result +=   "<td>"+ tmptd[key][0] +"</td>";
			result +=   "<td>"+ tmptd[key][1] +"</td>";
			result += "</tr>";
		}
		result += "</tbody>";
	}

    return result;
}

function chkDiffDataProc(storeData){
	
	var tmptd = {};
	
	if(storeData["project_code"] !== undefined && storeData["project_code"] !== "なし" && document.getElementById("project_code").value !== storeData["project_code"]){
		var tmpPrjCodeValue = document.getElementById("project_code").value == "" ? "*未設定*" : document.getElementById("project_code").value;
		tmptd["PJコード"] = [tmpPrjCodeValue, storeData["project_code"]];
	}

	if(storeData["kouji_kikan_code"] !== undefined && storeData["kouji_kikan_code"] !== "なし" && document.getElementById("kouji_kikan_code").value !== storeData["kouji_kikan_code"]){
		var tmpKKCodeValue = (document.getElementById("kouji_kikan_code").value == "" || document.getElementById("kouji_kikan_code").value == "なし") ? "*未設定*" : document.getElementById("kouji_kikan_code").value;
		tmptd["工事基幹コード"] = [tmpKKCodeValue, storeData["kouji_kikan_code"]];
	}

	if(storeData["branch_store"] !== undefined && document.getElementById("branch_store").value !== storeData["branch_store"]){
		var tmpBranchStoreValue = document.getElementById("branch_store").value == "" ? "*未設定*" : document.getElementById("branch_store").value;
		tmptd["支店"] = [tmpBranchStoreValue, storeData["branch_store"]];
	}

	if(storeData["construction_type"] !== undefined && document.getElementById("construction_type").value !== storeData["construction_type"]){
		var tmpCTypeValue = document.getElementById("construction_type").value == "" ? "*未設定*" : document.getElementById("construction_type").value;
		tmptd["工事区分"] = [tmpCTypeValue, storeData["construction_type"]];
	}

	if(storeData["hattyuusya"] !== undefined && document.getElementById("hattyuusya").value !== storeData["hattyuusya"]){
		var tmpOrdererValue = document.getElementById("hattyuusya").value == "" ? "*未設定*" : document.getElementById("hattyuusya").value;
		tmptd["発注者"] = [tmpOrdererValue, storeData["hattyuusya"]];
	}

	if(storeData["sekkeisya"] !== undefined && document.getElementById("sekkeisya").value !== storeData["sekkeisya"]){
		var tmpSekkeisyaValue = document.getElementById("sekkeisya").value == "" ? "*未設定*" : document.getElementById("sekkeisya").value;
		tmptd["設計者"] = [tmpSekkeisyaValue, storeData["sekkeisya"]];
	}
	
	if(storeData["youto"] !== undefined && document.getElementById("building_use").value !== storeData["youto"]){
		var tmpBuildUseValue = document.getElementById("building_use").value == "" ? "*未設定*" : document.getElementById("building_use").value;
		tmptd["用途"] = [tmpBuildUseValue, storeData["youto"]];
	}

	if(storeData["jyuusyo"] !== undefined && document.getElementById("address").value !== storeData["jyuusyo"]){
		var tmpAddressValue = document.getElementById("address").value == "" ? "*未設定*" : document.getElementById("address").value;
		tmptd["住所"] = [tmpAddressValue, storeData["jyuusyo"]];
	}
	
	if(storeData["nobe_menseki"] !== undefined && document.getElementById("total_floor_area").value !== storeData["nobe_menseki"]){
		var tmpTotalFloorAreaValue = document.getElementById("total_floor_area").value == "" ? "*未設定*" : document.getElementById("total_floor_area").value;
		tmptd["延床面積[㎡]"] = [tmpTotalFloorAreaValue, storeData["nobe_menseki"]];
	}

	if(storeData["tika"] !== undefined && document.getElementById("tika").value !== storeData["tika"]){
		var tmpTikaValue = document.getElementById("tika").value == "" ? "*未設定*" : document.getElementById("tika").value;
		tmptd["階数(地下)"] = [tmpTikaValue, storeData["tika"]];
	}
	if(storeData["tijou"] !== undefined && document.getElementById("tijou").value !== storeData["tijou"]){
		var tmpTijouValue = document.getElementById("tijou").value == "" ? "*未設定*" : document.getElementById("tijou").value;
		tmptd["階数(地上)"] = [tmpTijouValue, storeData["tijou"]];
	}

	var tmpTyakkou = cnvFormatDate(storeData["tyakkou"]);
	if(tmpTyakkou !== "" && document.getElementById("tyakkou").value !== tmpTyakkou){
		var tmpTyakkouValue = document.getElementById("tyakkou").value == "" ? "*未設定*" : document.getElementById("tyakkou").value;
		tmptd["着工"] = [tmpTyakkouValue, tmpTyakkou];
	}
	
	var tmpSyunkou = cnvFormatDate(storeData["syunkou"]);
	if(tmpSyunkou !== "" && document.getElementById("syunkou").value !== tmpSyunkou){
		var tmpSyunkouValue = document.getElementById("syunkou").value == "" ? "*未設定*" : document.getElementById("syunkou").value;
		tmptd["竣工"] = [tmpSyunkouValue, tmpSyunkou];
	}
	
	if(storeData["kouzou"] !== undefined && document.getElementById("kouzou").value !== storeData["kouzou"]){
		var tmpKouzouValue = document.getElementById("kouzou").value == "" ? "*未設定*" : document.getElementById("kouzou").value;
		tmptd["構造"] = [tmpKouzouValue, storeData["kouzou"]];
	}

	if(storeData["kouji_jimusyo"] !== undefined && document.getElementById("kouji_jimusyo").value !== storeData["kouji_jimusyo"]){
		var tmpKouzouValue = document.getElementById("kouji_jimusyo").value == "" ? "*未設定*" : document.getElementById("kouji_jimusyo").value;
		tmptd["工事事務所"] = [tmpKouzouValue, storeData["kouji_jimusyo"]];
	}
	
	return tmptd;
}

function overwriteImportData(project_name,storeData){

	if(storeData["project_code"] !== "なし"){
		document.getElementById("project_code").value = storeData["project_code"];
	}
	if(storeData["kouji_kikan_code"] !== "なし"){
		document.getElementById("kouji_kikan_code").value = storeData["kouji_kikan_code"];
	}
	document.getElementById("branch_store").value = storeData["branch_store"];
	document.getElementById("construction_type").value = storeData["construction_type"];
	document.getElementById("hattyuusya").value = storeData["hattyuusya"];
	document.getElementById("sekkeisya").value = storeData["sekkeisya"];
	document.getElementById("building_use").value = storeData["youto"];
	document.getElementById("address").value = storeData["address"];
	document.getElementById("tika").value = storeData["tika"];
	document.getElementById("tijou").value = storeData["tijou"];
	document.getElementById("total_floor_area").value = storeData["nobe_menseki"];
	document.getElementById("kouji_jimusyo").value = storeData["kouji_jimusyo"];

}

/**
 * 確認ダイアログを表示します。
 * @param  {String} append append
 * @param  {Function} [okFunction] OKボタンクリック時に実行される関数
 * @param  {Function} [cancelFunction] Cancelボタンクリック時に実行される関数
 */
function showConfirmDialog(append, okFunction, cancelFunction) {
	// Dialogを破棄する関数
	var _destroyDialog = function(dialogElement) {
		dialogElement.dialog('destroy'); // ※destroyなので、closeイベントは発生しない
		dialogElement.remove(); // ※動的に生成された要素を削除する必要がある
	};

	// Dialog要素(呼び出し毎に、動的に生成)
	// var $dialog = $("<div></div>");
	var $dialog = $("<table id='tblDialog'></table>");

	// for(var i=0; i<appendList.length; i++){
	// 	$dialog.append(appendList[i]);
	// }
	$dialog.append(append);

	// 各ボタンに対応する関数を宣言
	// ※Dialogを破棄後、コールバック関数を実行する
	var _funcOk     = function() { _destroyDialog($dialog); if (okFunction)     { okFunction();     } };
	var _funcCancel = function() { _destroyDialog($dialog); if (cancelFunction) { cancelFunction(); } };

	$dialog.dialog({
		modal: true,
		title: 'この内容をインポートしますか？',
		width: 500,
		show: 'drop',
		hide: 'drop',
		
		// 「閉じる」の設定
		// ※Cancel時の処理を「閉じる」に仕込むことで、Cancelと「閉じる」を同一の挙動とする
		closeText: 'Cancel',
		closeOnEscape: true,
		close: _funcCancel,
		
		// 各ボタンの設定
		buttons: [
			{ text: 'OK',     click: _funcOk },
			{ text: 'Cancel', click: function() { $(this).dialog('close'); } } // Dialogのcloseのみ
		]
	});

	$("#tblDialog").css({"width":"491px", "margin-top":"10px"});
	$("#tblDialog th").css({"text-align":"center", "background-color":"white"});
	$("#tblDialog td").css({"padding-left":"10px", "text-align":"center"});
	$("#tblDialog tr:nth-child(even)").css("background-color", "white");
	$("#tblDialog tr:nth-child(odd)").css("background-color", "whitesmoke");
}

function cnvFormatDate(dateString){
	
	var result = "";
	var year = "";
	var month = "";
	var date = "";
	
	if(dateString === undefined){
		return result;
	}
	
	if(dateString.length <= 5){
		
		// フォーマット変換（シリアル値形式->"****年**月**日"）
		var date_tyakkou = dateFromSn(dateString);
		year = date_tyakkou.getFullYear().toString();
		month = (date_tyakkou.getMonth()+1).toString();
		if(month.length === 1){
			month = "0" + month;
		}
		date = date_tyakkou.getDate().toString();
		if(date.length === 1){
			date = "0" + date;
		}
		
		result = year + "年　" + month + "月　" + date + "日";
		
	}else if(dateString.length == 8){
		
		year = dateString.substr(0, 4);
		month = dateString.substr(4, 2);
		date = dateString.substr(6, 2);
		
		result = year + "年　" + month + "月　" + date + "日";
		
	}else{
		//NOP
	}

	return result;
}

// 日時変換定数定義
var COEFFICIENT = 24 * 60 * 60 * 1000;		//日数とミリ秒を変換する係数
var DATES_OFFSET = 70 * 365 + 17 + 1 + 1;	//「1900/1/0」～「1970/1/1」 (日数)
var MILLIS_DIFFERENCE = 9 * 60 * 60 * 1000; //UTCとJSTの時差 (ミリ秒)

function convertUt2Sn(unixTimeMillis){ // UNIX時間(ミリ秒)→シリアル値
  return (unixTimeMillis + MILLIS_DIFFERENCE) / COEFFICIENT + DATES_OFFSET;
}

function convertSn2Ut(serialNumber){ // シリアル値→UNIX時間(ミリ秒)
  return (serialNumber - DATES_OFFSET) * COEFFICIENT - MILLIS_DIFFERENCE;
}

function dateFromSn(serialNumber){ // シリアル値→Date
  return new Date(convertSn2Ut(serialNumber));
}

function dateToSn(date){ // Date→シリアル値
  return convertUt2Sn(date.getTime());
}
