<?php

namespace App\Http\Controllers;
use App\Models\ForgeModel;
use App\Http\Controllers\AllstoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel; 
use App\Exports\TekkinExport;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPWord;
use PHPWord_IOFactory;
class ForgeController extends Controller 
{
    function index()
    {
        return view('forge');
    }
    
    function ShowVolumePage(){
        return view('forge_volume');
    }

    function DoorWindowPageLoad(){
        return view('doorWindowDetail');
    }
    
    function ShowTekkin(){
        return view('tekkin');
    }
    
    function ShowTekkintest(){
        return view('tekkintest');
    }
    
    function ExcelDownload(){
        
        $url = parse_url($_SERVER['REQUEST_URI']);
        $codeString = explode('=',$url['query']);
        $item_id = $codeString[1];
        return Excel::download(new TekkinExport($item_id,null,null), 'Tekkin.xlsx'); 
       
    }

    function KoujiExcelDownload(){

        $url = parse_url($_SERVER['REQUEST_URI']);
        $codeString = explode('=',$url['query']);
        $id = $codeString[1];
        $forge = new ForgeModel();
        $result = $forge->GetKoujiProjectById($id);
        
        if($result != null)
        {
           $data = $result[0];
            $inputFileName="/var/www/html/iPD/app/Exports/Template/KoujiTemplate.xlsx";
    
            //  Read your Excel workbook
            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $excel = $objReader->load($inputFileName);
            } catch(Exception $e) {
                die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
            }
            $excel->setActiveSheetIndex(0);
            $sheet = $excel->getActiveSheet();
    
            $sheet->setCellValue("E3",$data["koujimeisho"]);
            $sheet->setCellValue("E5",$data["sekoubasho"]);
            $sheet->setCellValue("E7",$data["orderer"]);//hachuusha
            $sheet->setCellValue("E9",$data["sekkeisya"]);//sekkeisha
            $sheet->setCellValue("E11",$data["koujikanrisha"]);
            $sheet->setCellValue("E13",$data["sekousha"]);
            $sheet->setCellValue("F15",$data["kouji_jimusyo"]);//meisho
            $sheet->setCellValue("F17",$data["shozonchi"]);
            $sheet->setCellValue("F19",$data["denwa"]);
            $sheet->setCellValue("J19",$data["fax"]);
            $sheet->setCellValue("E21",$data["shokatsurokisho"]);
            $sheet->setCellValue("E23",$data["tyakkou"]);//startTime
            $sheet->setCellValue("I23",$data["syunkou"]);//endTime
            $sheet->setCellValue("F25",$data["timeInterval"]);
            $sheet->setCellValue("E27",$data["building_use"]);//kenchikuyoto
            $sheet->setCellValue("E29",$data["kouzou"]);//kozo
            $sheet->setCellValue("G29",$data["tijou"]);//zouchijyo
            $sheet->setCellValue("I29",$data["tika"]);//kaichika
            $sheet->setCellValue("K29",$data["kaitouya"]);
            $sheet->setCellValue("F31",$data["glPlus"]);
            $sheet->setCellValue("J31",$data["glMinus"]);
            $sheet->setCellValue("F33",$data["kussakufukasa"]);
            $sheet->setCellValue("F35",$data["okujyou"]);
            $sheet->setCellValue("F37",$data["gaisou"]);
            $sheet->setCellValue("E39",'　　　　'.$data["shikichimenseki"]);//concat wiht space for writing as string in excel
            $sheet->setCellValue("E41",'　　　　'.$data["kenchikumenseki"]);
            $sheet->setCellValue("E43",'　　　　'.$data["total_floor_area"]);//yukamenseki
            $sheet->setCellValue("E45",'￥　　　'.$data["ukeoikin"]);
            $sheet->setCellValue("F47",' '.$data["shikyuzai"]);
    
            $sheet->setTitle("１．工事概要");
            //出力するファイル名
            $filename = $data["name"]."_工事概要.xlsx";               
            
            $writer = PHPExcel_IOFactory::createWriter($excel, "Excel2007");
            header('Content-Type: application/vnd.ms-excel');
            header("Content-Disposition: attachment;filename=\"".$filename."\"");
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        }else{
            
             print_r($result);
    
        }
    }
    
    function MngBIMExcelDownload(){
        
    }
    
    function ImplementationWordDownload(){
        $url = parse_url($_SERVER['REQUEST_URI']);
        $codeString = explode('=',$url['query']);
        $id = $codeString[1];
        $forge = new ForgeModel();
        $tmpresult = $forge->GetKoujiProjectById($id);
       
        $tmpdata = $tmpresult[0];
        $prjname = $tmpdata["name"];
        $result = $forge->GetImplementationProjectById($prjname);
        $allstore = new AllstoreController();
        $ret_allstore = $allstore->GetData();
        
        $targetProject = [];
        for($i=0; $i < count($ret_allstore); $i++){
            $cur_store = $ret_allstore[$i];
            $pjName = "";
            if($cur_store['b_tmp_pj_name'] == ''){
                if($cur_store['b_pj_name'] == ''){
                    $pjName = $cur_store['a_pj_name'];
                }else{
                    if(strpos($cur_store['b_pj_name'],"と同じ") !== false){
                        $pjName = $cur_store['a_pj_name'];    
                    }else{
                        $pjName = $cur_store['b_pj_name'];
                    }
                }
            }else{
                $pjName = $cur_store['b_tmp_pj_name'];
            }
            
            if($prjname == $pjName){
                $targetProject = $cur_store;
                break;
            }
        }
        
        if($result != null && $targetProject != null)
        {
            $data = $result[0];
            $allstore_data = $targetProject;
            $inputfilename="/var/www/html/iPD/app/Exports/Template/ImplementationTemplate.docx";

            // templateファイル読込
            $template = new \PhpOffice\PhpWord\TemplateProcessor($inputfilename);

            // 日時文字列の表示フォーマット置換([*年*月*日] => [*/*/*])
            $box_date1 = $this->ConvertDateFormat($data["box_date1"]);
            $box_date2 = $this->ConvertDateFormat($data["box_date2"]);
            $box_date3 = $this->ConvertDateFormat($data["box_date3"]);
            $rev_date1 = $this->ConvertDateFormat($data["rev_date1"]);
            $rev_date2 = $this->ConvertDateFormat($data["rev_date2"]);
            $rev_date3 = $this->ConvertDateFormat($data["rev_date3"]);
            // $make_model_start = $this->ConvertDateFormat($data["make_model_start"]);    $make_model_end = $this->ConvertDateFormat($data["make_model_end"]);
            // $sinsei_start = $this->ConvertDateFormat($data["sinsei_start"]);    $sinsei_end = $this->ConvertDateFormat($data["sinsei_end"]);
            // $seisan_start = $this->ConvertDateFormat($data["seisan_start"]);    $seisan_end = $this->ConvertDateFormat($data["seisan_end"]);
            // $kouji_start = $this->ConvertDateFormat($data["kouji_start"]);    $kouji_end = $this->ConvertDateFormat($data["kouji_end"]);
            // $genba_start = $this->ConvertDateFormat($data["genba_start"]);    $genba_end = $this->ConvertDateFormat($data["genba_end"]);
            // $sekou_start = $this->ConvertDateFormat($data["sekou_start"]);    $sekou_end = $this->ConvertDateFormat($data["sekou_end"]);
            $hiki_start = $this->ConvertDateFormat($data["hiki_start"]);    $hiki_end = $this->ConvertDateFormat($data["hiki_end"]);

            // タグに値を設定
            $pjName = "";
            if($allstore_data['b_tmp_pj_name'] == ''){
                if($allstore_data['b_pj_name'] == ''){
                    $pjName = $allstore_data['a_pj_name'];
                }else{
                    if(strpos($cur_store['b_pj_name'],"と同じ") !== false){
                        $pjName = $cur_store['a_pj_name'];    
                    }else{
                        $pjName = $cur_store['b_pj_name'];
                    }
                }
            }else{
                $pjName = $allstore_data['b_tmp_pj_name'];
            }
            $template->setValue('project_name', $pjName);
            // $template->setValue('project_name', $data["project_name"]);
            $template->setValue('version', $data["version"]);
            $template->setValue('date', $rev_date1);
            $template->setValue('orderer', $allstore_data['b_hattyuusya']);
            // $template->setValue('orderer', $data["orderer"]);
            $address = $allstore_data['b_sekou_basyo'] == '' ? $allstore_data['a_sekou_basyo'] : $allstore_data['b_sekou_basyo'];
            $template->setValue('address', $address);
            // $template->setValue('address', $data["address"]);
            $youto = $allstore_data['b_youto'] == '' ? $allstore_data['a_youto1'] : $allstore_data['b_youto'];
			$template->setValue('building_use', $youto);
// 			$template->setValue('building_use', $data["building_use"]);
			$template->setValue('building_num', $allstore_data['b_tousuu']);
// 			$template->setValue('building_num', $data["building_num"]);
            $kaisuu = "";
            $tika = $allstore_data['b_tika'] == '' ? $allstore_data['a_tika'] : $allstore_data['b_tika'];
            if($tika != '' && $tika != '-' && $tika != '0'){
                $kaisuu = 'B' . $tika . 'F';
            }
            $tijo = $allstore_data['b_tijo'] == '' ? $allstore_data['a_tijo'] : $allstore_data['b_tijo'];
            if($tijo != '' && $tijo != '-' && $tijo != '0'){
                if($kaisuu == ''){
                    $kaisuu = $kaisuu . ' ';
                }
                $kaisuu = $kaisuu . $tijo . 'F';
            }
			$template->setValue('floor_num', $kaisuu);
// 			$template->setValue('floor_num', $data["tijou"]);
            $nobe_menseki = $allstore_data['b_nobe_menseki'] == '' ? $allstore_data['a_nobe_menseki'] : $allstore_data['b_nobe_menseki'];
			$template->setValue('total_floor_area', $nobe_menseki);
// 			$template->setValue('total_floor_area', $data["total_floor_area"]);
			
			$template->setValue('box_date1', $box_date1);   $template->setValue('box_upload_file1', $data["box_upload_file1"]); $template->setValue('box_rev_person1', $data["box_rev_person1"]);
			$template->setValue('box_date2', $box_date2);	$template->setValue('box_upload_file2', $data["box_upload_file2"]);	$template->setValue('box_rev_person2', $data["box_rev_person2"]);
			$template->setValue('box_date3', $box_date3);	$template->setValue('box_upload_file3', $data["box_upload_file3"]);	$template->setValue('box_rev_person3', $data["box_rev_person3"]);

			$template->setValue('ken_org', $allstore_data['b_isyou_syozoku']);	$template->setValue('ken_name', $allstore_data['b_isyou_sekkei']);
			$template->setValue('kou_org', $allstore_data['b_kouzou_syozoku']);	$template->setValue('kou_name', $allstore_data['b_kouzou_sekkei']);
			$template->setValue('sku_org', $allstore_data['b_setubi_kuutyou_syozoku']);	$template->setValue('sku_name', $allstore_data['b_setubi_kuutyou_sekkei']);
			$template->setValue('sde_org', $allstore_data['b_setubi_denki_syozoku']);	$template->setValue('sde_name', $allstore_data['b_setubi_denki_sekkei']);
			$template->setValue('sek_org', $allstore_data['b_sekou_syozoku']);	$template->setValue('sek_name', $allstore_data['b_sekou_tantou']);
			$template->setValue('sei_org', $allstore_data['b_seisan_sekkei_syozoku']);	$template->setValue('sei_name', $allstore_data['b_seisan_sekkei_tantou']);
			$template->setValue('koj_org', $allstore_data['b_koujibu_syozoku']);	$template->setValue('koj_name', $allstore_data['b_koujibu_tantou']);
			$template->setValue('sgi_org', $allstore_data['b_seisan_gijutu_syozoku']);	$template->setValue('sgi_name', $allstore_data['b_seisan_gijutu_tantou']);
			$template->setValue('smi_org', $allstore_data['b_sekisan_mitumori_syozoku']);	$template->setValue('smi_name', $allstore_data['b_sekisan_mitumori_tantou']);
			$template->setValue('bmn_org', $allstore_data['b_bim_maneka_syozoku']);	$template->setValue('bmn_name', $allstore_data['b_bim_maneka_tantou']);
			$template->setValue('pds_org', $allstore_data['b_ipd_center_syozoku']);	$template->setValue('pds_name', $allstore_data['b_ipd_center_tantou']);
// 			$template->setValue('ken_org', $data["ken_org"]);	$template->setValue('ken_name', $data["ken_name"]);
// 			$template->setValue('kou_org', $data["kou_org"]);	$template->setValue('kou_name', $data["kou_name"]);
// 			$template->setValue('sku_org', $data["sku_org"]);	$template->setValue('sku_name', $data["sku_name"]);
// 			$template->setValue('sde_org', $data["sde_org"]);	$template->setValue('sde_name', $data["sde_name"]);
// 			$template->setValue('sek_org', $data["sek_org"]);	$template->setValue('sek_name', $data["sek_name"]);
// 			$template->setValue('sei_org', $data["sei_org"]);	$template->setValue('sei_name', $data["sei_name"]);
// 			$template->setValue('koj_org', $data["koj_org"]);	$template->setValue('koj_name', $data["koj_name"]);
// 			$template->setValue('sgi_org', $data["sgi_org"]);	$template->setValue('sgi_name', $data["sgi_name"]);
// 			$template->setValue('smi_org', $data["smi_org"]);	$template->setValue('smi_name', $data["smi_name"]);
// 			$template->setValue('bmn_org', $data["bmn_org"]);	$template->setValue('bmn_name', $data["bmn_name"]);
// 			$template->setValue('pds_org', $data["pds_org"]);	$template->setValue('pds_name', $data["pds_name"]);
			$template->setValue('mdl_org', $data["mdl_org"]);	$template->setValue('mdl_name', $data["mdl_name"]);
			$template->setValue('sbk_org', $data["sbk_org"]);	$template->setValue('sbk_name', $data["sbk_name"]);
			$template->setValue('sbd_org', $data["sbd_org"]);	$template->setValue('sbd_name', $data["sbd_name"]);
			$template->setValue('fsa_org', $data["fsa_org"]);	$template->setValue('fsa_name', $data["fsa_name"]);
			$template->setValue('fse_org', $data["fse_org"]);	$template->setValue('fse_name', $data["fse_name"]);

// 			$template->setValue('make_model_start', $make_model_start);	$template->setValue('make_model_end', $make_model_end);	$template->setValue('make_model_bikou', $data["make_model_bikou"]);
// 			$template->setValue('sinsei_start', $sinsei_start);			$template->setValue('sinsei_end', $sinsei_end);			$template->setValue('sinsei_bikou', $data["sinsei_bikou"]);
// 			$template->setValue('seisan_start', $seisan_start);			$template->setValue('seisan_end', $seisan_end);			$template->setValue('seisan_bikou', $data["seisan_bikou"]);
// 			$template->setValue('kouji_start', $kouji_start);			$template->setValue('kouji_end', $kouji_end);			$template->setValue('kouji_bikou', $data["kouji_bikou"]);
// 			$template->setValue('genba_start', $genba_start);			$template->setValue('genba_end', $genba_end);			$template->setValue('genba_bikou', $data["genba_bikou"]);
// 			$template->setValue('sekou_start', $sekou_start);			$template->setValue('sekou_end', $sekou_end);			$template->setValue('sekou_bikou', $data["sekou_bikou"]);
// 			$template->setValue('hiki_start', $hiki_start); 			$template->setValue('hiki_end', $hiki_end); 			$template->setValue('hiki_bikou', $data["hiki_bikou"]);
			$template->setValue('make_model_start', $allstore_data['b_koutei_sekkei_model_start']);	$template->setValue('make_model_end', $allstore_data['b_koutei_sekkei_model_end']);	$template->setValue('make_model_bikou', $data["make_model_bikou"]);
			$template->setValue('sinsei_start', $allstore_data['b_koutei_kakunin_sinsei_start']);			$template->setValue('sinsei_end', $allstore_data['b_koutei_kakunin_sinsei_end']);			$template->setValue('sinsei_bikou', $data["sinsei_bikou"]);
			$template->setValue('seisan_start', $allstore_data['b_koutei_sekisan_model_tougou_start']);			$template->setValue('seisan_end', $allstore_data['b_koutei_sekisan_model_tougou_end']);			$template->setValue('seisan_bikou', $data["seisan_bikou"]);
			$template->setValue('kouji_start', $allstore_data['b_koutei_kouji_juujisya_kettei_start']);			$template->setValue('kouji_end', $allstore_data['b_koutei_kouji_juujisya_kettei_end']);			$template->setValue('kouji_bikou', $data["kouji_bikou"]);
			$template->setValue('genba_start', $allstore_data['b_koutei_genba_koutei_kettei_start']);			$template->setValue('genba_end', $allstore_data['b_koutei_genba_koutei_kettei_end']);			$template->setValue('genba_bikou', $data["genba_bikou"]);
			$template->setValue('sekou_start', $allstore_data['b_koutei_kouji_start']);			$template->setValue('sekou_end', $allstore_data['b_koutei_kouji_end']);			$template->setValue('sekou_bikou', $data["sekou_bikou"]);
			$template->setValue('hiki_start', $hiki_start); 			$template->setValue('hiki_end', $hiki_end); 			$template->setValue('hiki_bikou', $data["hiki_bikou"]);

			$template->setValue('ken_sw', $data["ken_sw"]);
			$template->setValue('kou_sw', $data["kou_sw"]);
			$template->setValue('sku_sw', $data["sku_sw"]);
			$template->setValue('sde_sw', $data["sde_sw"]);
			$template->setValue('mdl_sw', $data["mdl_sw"]);
			$template->setValue('sek_sw', $data["sek_sw"]);
			$template->setValue('sei_sw', $data["sei_sw"]);
			$template->setValue('sbk_sw', $data["sbk_sw"]);
			$template->setValue('sbd_sw', $data["sbd_sw"]);
			$template->setValue('fsa_sw', $data["fsa_sw"]);
			$template->setValue('fse_sw', $data["fse_sw"]);
			
			$template->setValue('base_linex', $data["base_linex"]);
			$template->setValue('base_liney', $data["base_liney"]);

			$template->setValue('rev_ver1', $data["rev_ver1"]); $template->setValue('rev_date1', $rev_date1);   $template->setValue('rev_contents1', $data["rev_contents1"]);	$template->setValue('rev_name1', $data["rev_name1"]);
			$template->setValue('rev_ver2', $data["rev_ver2"]);	$template->setValue('rev_date2', $rev_date2);	$template->setValue('rev_contents2', $data["rev_contents2"]);	$template->setValue('rev_name2', $data["rev_name2"]);
			$template->setValue('rev_ver3', $data["rev_ver3"]);	$template->setValue('rev_date3', $rev_date3);	$template->setValue('rev_contents3', $data["rev_contents3"]);	$template->setValue('rev_name3', $data["rev_name3"]);
            
            // wordファイル自動ダウンロード
            $filename = $data["project_name"]."_BIM実行計画書.docx";
            header('Content-Type: application/octet-stream');
            header("Content-Disposition: attachment; filename=\"".$filename."\"");
            $template->saveAs('php://output');

        }else{
             print_r($result);
        }
    }
    
    function ConvertDateFormat($str){
        $replaceStr = str_replace('年　', '/', $str);
        $replaceStr = str_replace('月　', '/', $replaceStr);
        $replaceStr = str_replace('日', '', $replaceStr);
        $replaceStr = str_replace('/0', '/', $replaceStr);
        return $replaceStr;
    }

    function GetThreeLeggedToken(Request $request){
        
        $forgeBtnText = $request->get('btnText');
       //return $forgeBtnText;
        if(strstr($forgeBtnText,"LOGOUT") == true){
            session()->forget('authCode');
            return "FORGE LOGIN AGAIN";
        }
        
        $conf = new \Autodesk\Auth\Configuration();//escape from current name space by using '\'
        $conf->getDefaultConfiguration()
        ->setClientId(env("FORGE_CLIENT_ID"))//'J0jduCzdsYAbKXqsidxCBt3aWpW5DNv0'
        ->setClientSecret(env("FORGE_CLIENT_SECRET"))//'Hp8X9pxKgYjqJYGE'
        ->setRedirectUrl(env("FORGE_CALLBACK_URI"));//'https://obayashi-ccc.net/iPD/forge/callback'
       
        $threeLeggedAuth = new \Autodesk\Auth\OAuth2\ThreeLeggedAuth();     
        $scopes = array("code:all","data:read","data:write","bucket:read","bucket:update");
        $threeLeggedAuth->addScopes($scopes);

        try{
            $authUrl = $threeLeggedAuth->createAuthUrl();
            return $authUrl;
        }catch(Exception $e){
            return $e->getMessage();
        }      
      
    }

    function ForgeCallBack(){
        $url = parse_url($_SERVER['REQUEST_URI']);
        $codeString = explode('=',$url['query']);
        $authCode = $codeString[1];
        session(['authCode' =>$authCode]);
        
        $conf = new \Autodesk\Auth\Configuration();//escape from current name space by using '\'
        $conf->getDefaultConfiguration()
        ->setClientId(env("FORGE_CLIENT_ID"))//'J0jduCzdsYAbKXqsidxCBt3aWpW5DNv0'
        ->setClientSecret(env("FORGE_CLIENT_SECRET"))//'Hp8X9pxKgYjqJYGE'
        ->setRedirectUrl(env("FORGE_CALLBACK_URI"));//'https://obayashi-ccc.net/iPD/forge/callback'
       
        $threeLeggedAuth = new \Autodesk\Auth\OAuth2\ThreeLeggedAuth();     
        $scopes = array("code:all","data:read","data:write","bucket:read","bucket:update");
        $threeLeggedAuth->addScopes($scopes);
        $threeLeggedAuth->fetchToken($authCode);
        $token = $threeLeggedAuth->getAccessToken();
        session(['token' =>$token]);
        
        $this->GetAllForgeLoggedInUserProjects($threeLeggedAuth);
        
        return redirect('login/successlogin');
    }
    
    function GetAllForgeLoggedInUserProjects($authObj){
        
        try {
            $hubInstance = new \Autodesk\Forge\Client\Api\HubsApi($authObj);
            $hubs = $hubInstance->getHubs(null, null);
            $hubObj = $hubs['data'];

            foreach($hubObj as $hub){
                $hubId = $hub['id'];
                $hubName = $hub['attributes']['name'];
                if($hubName == "OBAYASHI")continue;
                
                $projectInstance = new \Autodesk\Forge\Client\Api\ProjectsApi($authObj);
                
                $projects = $projectInstance->getHubProjects($hubId, null, null);                
                $proObj = $projects['data'];
                
                $loggedinUserProject=array();
                foreach($proObj as $project){

                    $proId = $project['id'];
                    $projectName = $project['attributes']['name'];
                    array_push($loggedinUserProject,$projectName);
                } 
                
               session(['loggedinUserProjects' =>$loggedinUserProject]);  
            }

        } catch (Exception $e) {
            //echo 'Exception when calling forge library function : ', $e->getMessage(), PHP_EOL;
        }
    }

    function GetData(Request $request){
        $message = $request->get('message');
        $forge = new ForgeModel();
        if($message == "getComboData"){        
            $projects = $forge->GetProjects();
            $items = $forge->GetItems();
            $versions = $forge->GetVersions();
            $project_info = $forge->GetProjectInfo();
            return array("projects"=>$projects,"items"=>$items,"versions"=>$versions,"project_info"=>$project_info);
        }else if($message == "getDataByVersion"){
            $version_number = $request->get('version_number');
            $item_id = $request->get('item_id');
            $category_list = $request->get('category_list');
            $material_list = $request->get('material_list');
            $workset_list = $request->get('workset_list');
            $level_list = $request->get('level_list');
            $familyName_list = $request->get('familyName_list');
            $typeName_list = $request->get('typeName_list');
            $typeName_filter = $request->get('typeName_filter');
            // print_r($category_list);exit;
            $data = $forge->GetDataByVersion($version_number,$item_id,$category_list,$material_list,$workset_list,$level_list,$familyName_list,$typeName_list,$typeName_filter);
            return $data;
        }else if($message == "getRoomDataByVersion"){
            $version_number = $request->get('version_number');
            $item_id = $request->get('item_id');
            $workset_list = $request->get('workset_list');
            $level_list = $request->get('level_list');
            $room_list = $request->get('room_list');
            $tenjoShiage_list = $request->get('tenjoShiage_list');
            $kabeShiage_list = $request->get('kabeShiage_list');
            $yukaShiage_list = $request->get('yukaShiage_list');
            $tenjoShitaji_list = $request->get('tenjoShitaji_list');
            $kabeShitaji_list = $request->get('kabeShitaji_list');
            $yukaShitaji_list = $request->get('yukaShitaji_list');
            $habaki_list = $request->get('habaki_list');
            $mawaribuchi_list = $request->get('mawaribuchi_list');
            $data = $forge->GetRoomDataByVersion(
                $version_number,$item_id,$workset_list,$level_list,$room_list,
                $tenjoShiage_list,$kabeShiage_list,$yukaShiage_list,
                $tenjoShitaji_list,$kabeShitaji_list,$yukaShitaji_list,
                $habaki_list,$mawaribuchi_list);
            return $data;
        }else if($message == "getComboDataByPjCode"){
            $projectCode = $request->get('projectCode');
            $itemName = $request->get('itemName');
            $items = $forge->GetItemsByPjCode($projectCode);
            $versions = $forge->GetVersionsByPjCode($projectCode,$itemName);
            $materials = $forge->GetMaterailsByPjCode($projectCode);
            $worksets = $forge->GetWorksetsByPjCode($projectCode);
            $levels = $forge->GetLevelsByPjCode($projectCode);
            $FamilyNames = $forge->GetFamilyNamesByPjCode($projectCode);
            $TypeNames = $forge->GetTypeNamesByPjCode($projectCode);
            return array("items"=>$items,"versions"=>$versions,"levels"=>$levels,"worksets"=>$worksets,"materials"=>$materials,"familyNames"=>$FamilyNames,"typeNames"=>$TypeNames);

        }else if($message == "getComboDataByProject"){
            $projectName = $request->get('projectName');
            $itemName = $request->get('itemName');
            $items = $forge->GetItemsByProject($projectName);
            $versions = $forge->GetVersionsByProject($projectName,$itemName);
            $materials = $forge->GetMaterailsByProject($projectName);
            $worksets = $forge->GetWorksetsByProject($projectName);
            $levels = $forge->GetLevelsByProject($projectName);
            $FamilyNames = $forge->GetFamilyNamesByProject($projectName);
            $TypeNames = $forge->GetTypeNamesByProject($projectName);
            return array("items"=>$items,"versions"=>$versions,"levels"=>$levels,"worksets"=>$worksets,"materials"=>$materials,"familyNames"=>$FamilyNames,"typeNames"=>$TypeNames);
        }else if($message == "getComboDataByVersion"){
            $projectName = $request->get('projectName');
            $versionNum = $request->get('versionNum');
            $levels         = $forge->GetTargetByVersion($projectName,"level",        $versionNum);
            $worksets       = $forge->GetTargetByVersion($projectName,"workset",      $versionNum);
            $materials      = $forge->GetTargetByVersion($projectName,"material_name",$versionNum);
            $FamilyNames    = $forge->GetTargetByVersion($projectName,"family_name",  $versionNum);
            $TypeNames      = $forge->GetTargetByVersion($projectName,"type_name",    $versionNum);
            return array("levels"=>$levels,"worksets"=>$worksets,"materials"=>$materials,"familyNames"=>$FamilyNames,"typeNames"=>$TypeNames);
        }else if($message == "getComboDataByLevel"){
            $projectName    = $request->get('projectName');
            $versionNum     = $request->get('versionNum');
            $levelList      = $request->get('levelList');
            $worksetList    = $request->get('worksetList');
            $materialList   = $request->get('materialList');
            $familyNameList = $request->get('familyNameList');
            $typeNameList   = $request->get('typeNameList');
            $worksets    = $forge->GetTargetByColumnList($projectName,"workset",      $versionNum,$levelList,          [],$materialList,$familyNameList,$typeNameList);
            $materials   = $forge->GetTargetByColumnList($projectName,"material_name",$versionNum,$levelList,$worksetList,           [],$familyNameList,$typeNameList);
            $FamilyNames = $forge->GetTargetByColumnList($projectName,"family_name",  $versionNum,$levelList,$worksetList,$materialList,             [],$typeNameList);
            $TypeNames   = $forge->GetTargetByColumnList($projectName,"type_name",    $versionNum,$levelList,$worksetList,$materialList,$familyNameList,           []);
            return array("worksets"=>$worksets,"materials"=>$materials,"familyNames"=>$FamilyNames,"typeNames"=>$TypeNames);
        }else if($message == "getComboDataByWorkset"){
            $projectName    = $request->get('projectName');
            $versionNum     = $request->get('versionNum');
            $levelList      = $request->get('levelList');
            $worksetList    = $request->get('worksetList');
            $materialList   = $request->get('materialList');
            $familyNameList = $request->get('familyNameList');
            $typeNameList   = $request->get('typeNameList');
            $levels      = $forge->GetTargetByColumnList($projectName,"level",        $versionNum,        [],$worksetList,$materialList,$familyNameList,$typeNameList);
            $materials   = $forge->GetTargetByColumnList($projectName,"material_name",$versionNum,$levelList,$worksetList,           [],$familyNameList,$typeNameList);
            $FamilyNames = $forge->GetTargetByColumnList($projectName,"family_name",  $versionNum,$levelList,$worksetList,$materialList,             [],$typeNameList);
            $TypeNames   = $forge->GetTargetByColumnList($projectName,"type_name",    $versionNum,$levelList,$worksetList,$materialList,$familyNameList,           []);
            return array("levels"=>$levels,"materials"=>$materials,"familyNames"=>$FamilyNames,"typeNames"=>$TypeNames);
        }else if($message == "getComboDataByMaterial"){
            $projectName    = $request->get('projectName');
            $versionNum     = $request->get('versionNum');
            $levelList      = $request->get('levelList');
            $worksetList    = $request->get('worksetList');
            $materialList   = $request->get('materialList');
            $familyNameList = $request->get('familyNameList');
            $typeNameList   = $request->get('typeNameList');
            $levels      = $forge->GetTargetByColumnList($projectName,"level",      $versionNum,        [],$worksetList,$materialList,$familyNameList,$typeNameList);
            $worksets    = $forge->GetTargetByColumnList($projectName,"workset",    $versionNum,$levelList,          [],$materialList,$familyNameList,$typeNameList);
            $FamilyNames = $forge->GetTargetByColumnList($projectName,"family_name",$versionNum,$levelList,$worksetList,$materialList,             [],$typeNameList);
            $TypeNames   = $forge->GetTargetByColumnList($projectName,"type_name",  $versionNum,$levelList,$worksetList,$materialList,$familyNameList,           []);
            return array("levels"=>$levels,"worksets"=>$worksets,"familyNames"=>$FamilyNames,"typeNames"=>$TypeNames);
        }else if($message == "getComboDataByFamilyName"){
            $projectName    = $request->get('projectName');
            $versionNum     = $request->get('versionNum');
            $levelList      = $request->get('levelList');
            $worksetList    = $request->get('worksetList');
            $materialList   = $request->get('materialList');
            $familyNameList = $request->get('familyNameList');
            $typeNameList   = $request->get('typeNameList');
            $levels     = $forge->GetTargetByColumnList($projectName,"level",        $versionNum,        [],$worksetList,$materialList,$familyNameList,$typeNameList);
            $worksets   = $forge->GetTargetByColumnList($projectName,"workset",      $versionNum,$levelList,          [],$materialList,$familyNameList,$typeNameList);
            $materials  = $forge->GetTargetByColumnList($projectName,"material_name",$versionNum,$levelList,$worksetList,           [],$familyNameList,$typeNameList);
            $TypeNames  = $forge->GetTargetByColumnList($projectName,"type_name",    $versionNum,$levelList,$worksetList,$materialList,$familyNameList,           []);
            return array("levels"=>$levels,"worksets"=>$worksets,"materials"=>$materials,"typeNames"=>$TypeNames);
        }else if($message == "getComboDataByTypeName"){
            $projectName    = $request->get('projectName');
            $versionNum     = $request->get('versionNum');
            $levelList      = $request->get('levelList');
            $worksetList    = $request->get('worksetList');
            $materialList   = $request->get('materialList');
            $familyNameList = $request->get('familyNameList');
            $typeNameList   = $request->get('typeNameList');
            $levels      = $forge->GetTargetByColumnList($projectName,"level",        $versionNum,        [],$worksetList,$materialList,$familyNameList,$typeNameList);
            $worksets    = $forge->GetTargetByColumnList($projectName,"workset",      $versionNum,$levelList,          [],$materialList,$familyNameList,$typeNameList);
            $materials   = $forge->GetTargetByColumnList($projectName,"material_name",$versionNum,$levelList,$worksetList,           [],$familyNameList,$typeNameList);
            $FamilyNames = $forge->GetTargetByColumnList($projectName,"family_name",  $versionNum,$levelList,$worksetList,$materialList,             [],$typeNameList);
            return array("levels"=>$levels,"worksets"=>$worksets,"materials"=>$materials,"familyNames"=>$FamilyNames);
        }else if($message == "getComboRoomDataByProject"){
            $projectName = $request->get('projectName');
            $itemName = $request->get('itemName');
            $items  = $forge->GetItemsByProject($projectName);
            $versions = $forge->GetVersionsByProject($projectName,$itemName);
            $roomData = $forge->GetRoomInfoByProject($projectName);
            
            $worksets       = array_unique(array_column($roomData,"workset"));
            $levels         = array_unique(array_column($roomData,"level"));
            $roomName       = array_unique(array_column($roomData,"room_name"));
            $tenjoShiage    = array_unique(array_column($roomData,"shiage_tenjo"));
            $kabeShiage     = array_unique(array_column($roomData,"shiage_kabe"));
            $yukaShiage     = array_unique(array_column($roomData,"shiage_yuka"));
            $tenjoShitaji   = array_unique(array_column($roomData,"tenjo_shitaji"));
            $kabeShitaji    = array_unique(array_column($roomData,"kabe_shitaji"));
            $yukaShitaji    = array_unique(array_column($roomData,"yuka_shitaji"));
            $habaki         = array_unique(array_column($roomData,"habaki"));
            $mawaribuchi    = array_unique(array_column($roomData,"mawaribuchi"));
            
            return array("items"=>$items,"versions"=>$versions,"levels"=>$levels,"worksets"=>$worksets,"roomName"=>$roomName,
                            "tenjoShiage"=>$tenjoShiage,"kabeShiage"=>$kabeShiage,"yukaShiage"=>$yukaShiage,
                            "tenjoShitaji"=>$tenjoShitaji,"kabeShitaji"=>$kabeShitaji,"yukaShitaji"=>$yukaShitaji,
                            "habaki"=>$habaki,"mawaribuchi"=>$mawaribuchi);
        }else if($message == "getTekkinData"){

            $item_id = $request->get('item_id');
            $data = $forge->GetTekkinData($item_id);
            $column_tekkin = array();
            $beam_tekkin = array();
            $foundation_tekkin = array();
            if(sizeof($data["column_tekkin_data"]) > 0)
                $column_tekkin = $this->ColumnTekkinCalculation($data["column_tekkin_data"]);
            if(sizeof($data["beam_tekkin_data"]) > 0)
                $beam_tekkin = $this->BeamTekkinCalculation($data["beam_tekkin_data"]);
            if(sizeof($data["foundation_tekkin_data"]) > 0)
                $foundation_tekkin = $this->FoundationTekkinCalculation($data["foundation_tekkin_data"]);          

            return array_merge($beam_tekkin,$column_tekkin,$foundation_tekkin);
        }else if($message == "getComboDataByImplementationDocInfo"){
            
            $projectNameList    = $request->get('projectNameList');

            $projects = $forge->GetProjectsByProjectNames($projectNameList);
            $items = $forge->GetItemsByProjects($projects);
            $versions = $forge->GetVersionsByItems($items);
            return array("projects"=>$projects,"items"=>$items,"versions"=>$versions);
        }else if($message == "getVersionsDataByProject"){
            $projectName = $request->get('projectName');
            $itemName = $request->get('itemName');
            $versions = $forge->GetVersionsByProject($projectName,$itemName);
            return array("versions"=>$versions);
        }else if($message == "getLatedVersionsDataByItem"){
            $itemIdList = $request->get('itemList');
            $latedVersions = $forge->GetLatedVersionsByItem($itemIdList);
            return array("latedVersion"=>$latedVersions);
        }else if($message == "getComboDataByProjectName"){
            $projectName    = $request->get('projectName');
            $projects = $forge->GetProjectByProjectName($projectName);
            return array("projects"=>$projects);
        }else if($message == "getDoorWindowData"){
            
            $item_id = $request->get('item_id');
            $version_number = $request->get('version_number');
            $category_list = $request->get('category_list');
            $level_list = $request->get('level_list');
            $workset_list = $request->get('workset_list');
            $typename_list = $request->get('typename_list');
            $typepanel_list =  $request->get('typepanel_list');
            
            $result = $forge->GetDoorWindowData($item_id,$version_number,$category_list,$level_list,$workset_list,$typename_list,$typepanel_list);
            return $result;
        }else if($message == "getDoorWindowComboData"){
            $projectName = $request->get('projectName');
            $itemName = $request->get('itemName');
            $items  = $forge->GetItemsByProject($projectName);
            $versions = $forge->GetVersionsByProject($projectName,$itemName);
            $doorData = $forge->GetDoorInfoByProject($projectName);
            $windowData = $forge->GetWorksetsByProject($projectName);
            
            $doorWorksets = array_unique(array_column($doorData,"workset"));
            $windowWorksets = array_unique(array_column($windowData,"workset"));
            $worksets = array_unique(array_merge($doorWorksets,$windowWorksets));
            
            $doorLevels = array_unique(array_column($doorData,"level"));
            $windowLevels = array_unique(array_column($windowData,"level"));
            $levels = array_unique(array_merge($doorLevels,$windowLevels));
            
            $doorTypeName = array_unique(array_column($doorData,"type_name"));
            $windowTypeName = array_unique(array_column($windowData,"type_name"));
            $typeNames = array_unique(array_merge($doorTypeName,$windowTypeName));
            
            $doorTypePanel = array_unique(array_column($doorData,"type_door_panel"));
            $windowTypePanel = array_unique(array_column($windowData,"type_window_panel"));
            $typePanels = array_unique(array_merge($doorTypePanel,$windowTypePanel));
            
            return array("items"=>$items,"versions"=>$versions,"levels"=>$levels,"worksets"=>$worksets,"typeNames"=>$typeNames,"typePanels"=>$typePanels);
        }
    }

    function SaveData(Request $request){
        $message = $request->get('message');
        if($message == "update_project_auto_save"){
            $updateProjects = $request->get('projects');
            //$backupProjects = $request->get('backupProjects');
            $forge = new ForgeModel();
            $result = $forge->UpdateProjectAutoSaveFlag($updateProjects);
            return $result;
        }else if($message = "update_backup_project"){
            $backupProjects = $request->get('backupProjects');
            $forge = new ForgeModel();
            $result = $forge->UpdateProjectBackupFlag($backupProjects);
            return $result;
        }
    }
    
    function ColumnTekkinCalculation($column_tekkin_list){
        try{
            
            $result_list = array();
            $divValue = 162.28;
            foreach($column_tekkin_list as $data){

                $W = $this->get_number($data["W"])/1000;//change mm to m
                $D = $this->get_number($data["D"])/1000;
                $volume = $this->get_number($data["volume"]);
                $level = $data["level"];
                $length = ($W*$D) == 0 ? 0 :($volume/($W * $D));
                $phase = $data["phase"];
    
                $start_diameter = $this->get_number($data["start_diameter"]); 
                $start_X_firstRowCount = $this->get_number($data["start_X_firstRowCount"]);
                $start_X_secondRowCount = $this->get_number($data["start_X_secondRowCount"]);
                $start_Y_firstRowCount = $this->get_number($data["start_Y_firstRowCount"]);
                $start_Y_secondRowCount = $this->get_number($data["start_Y_secondRowCount"]);
                $start_rib_diameter = $this->get_number($data["start_rib_diameter"]);
                $start_rib_pitch = $this->get_number($data["start_rib_pitch"]);
                
                $start_area = pow($start_diameter,2)/$divValue;
                $start_rib_area = pow($start_rib_diameter,2);

                $start_weight = ($start_rib_pitch == 0) ? 0 :(($start_area* ($length/2)) * ($start_X_firstRowCount+$start_X_secondRowCount+$start_Y_firstRowCount+$start_Y_secondRowCount))
                                + (($start_rib_area * ($W+$D) * 2) * ($length/$start_rib_pitch/2));
                $start_weight = $this->two_decimal($start_weight/1000);//change to tons
    
                $end_diameter = $this->get_number($data["end_diameter"]); 
                $end_X_firstRowCount = $this->get_number($data["end_X_firstRowCount"]);
                $end_X_secondRowCount = $this->get_number($data["end_X_secondRowCount"]);
                $end_Y_firstRowCount = $this->get_number($data["end_Y_firstRowCount"]);
                $end_Y_secondRowCount = $this->get_number($data["end_Y_secondRowCount"]);
                $end_rib_diameter = $this->get_number($data["end_rib_diameter"]);
                $end_rib_pitch = $this->get_number($data["end_rib_pitch"]);

                $end_area = pow($end_diameter,2)/$divValue;
                $end_rib_area = pow($end_rib_diameter,2);
    
                $end_weight = ($end_rib_pitch == 0) ? 0 :(($end_area * ($length/2)) * ($end_X_firstRowCount+$end_X_secondRowCount+$end_Y_firstRowCount+$end_Y_secondRowCount))
                              + (($end_rib_area * ($W+$D) * 2) * ($length/$end_rib_pitch/2));
                $end_weight = $this->two_decimal($end_weight/1000);
    
                $element_id = $data["element_id"];
    
                $totalWeight = $start_weight + $end_weight;

                array_push($result_list,array("element_id"=>$element_id,"level"=>$level,"start_weight"=>$start_weight,"center_weight"=>"","end_weight"=>$end_weight,"total"=>$totalWeight,"category"=>"柱","phase"=>$phase));
    
            }
            return $result_list;

        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    function BeamTekkinCalculation($beam_tekkin_list){
        try{
            $result_list = array();
            $divValue = 162.28;
            foreach($beam_tekkin_list as $data){
                $B = $this->get_number($data["B"])/1000;
                $H = $this->get_number($data["H"])/1000;
                $kattocho = $this->get_number($data["kattocho"])/1000;
                $level = $data["level"];
                $phase = $data["phase"];
                $start_upper_diameter = $this->get_number($data["start_upper_diameter"]);
                $start_upper_firstRowCount = $this->get_number($data["start_upper_firstRowCount"]);
                $start_upper_secondRowCount = $this->get_number($data["start_upper_secondRowCount"]);
                $start_lower_diameter = $this->get_number($data["start_lower_diameter"]);
                $start_lower_firstRowCount = $this->get_number($data["start_lower_firstRowCount"]);
                $start_lower_secondRowCount = $this->get_number($data["start_lower_secondRowCount"]);
                $start_rib_diameter = $this->get_number($data["start_rib_diameter"]);
                $start_rib_count = $this->get_number($data["start_rib_count"]);
                $start_rib_pitch = $this->get_number($data["start_rib_pitch"]);

                $start_upper_area = pow($start_upper_diameter,2)/$divValue;
                $start_lower_area = pow($start_lower_diameter,2)/$divValue;
                $start_rib_area = pow($start_rib_diameter,2);
                $start_weight = ($start_rib_pitch == 0)? 0 : (($start_upper_area * ($kattocho/3)) * ($start_upper_firstRowCount+$start_upper_secondRowCount))
                                                                        +(($start_lower_area * ($kattocho/3)) * ($start_lower_firstRowCount+$start_lower_secondRowCount))
                                                                        +(($start_rib_area * ($B+$H)*2) * ($kattocho/$start_rib_pitch/3));
                $start_weight = $this->two_decimal($start_weight/1000);//change to tons

                $center_upper_diameter = $this->get_number($data["center_upper_diameter"]);
                $center_upper_firstRowCount = $this->get_number($data["center_upper_firstRowCount"]);
                $center_upper_secondRowCount = $this->get_number($data["center_upper_secondRowCount"]);
                $center_lower_diameter = $this->get_number($data["center_lower_diameter"]);
                $center_lower_firstRowCount = $this->get_number($data["center_lower_firstRowCount"]);
                $center_lower_secondRowCount = $this->get_number($data["center_lower_secondRowCount"]);
                $center_rib_diameter = $this->get_number($data["center_rib_diameter"]);
                $center_rib_count = $this->get_number($data["center_rib_count"]);
                $center_rib_pitch = $this->get_number($data["center_rib_pitch"]);

                $center_upper_area = pow($center_upper_diameter,2)/$divValue;
                $center_lower_area = pow($center_lower_diameter,2)/$divValue;
                $center_rib_area = pow($center_rib_diameter,2);

                $center_weight = ($center_rib_pitch == 0)? 0 : (($center_upper_area * ($kattocho/3)) * ($center_upper_firstRowCount+$center_upper_secondRowCount))
                                                                            +(($center_lower_area * ($kattocho/3)) * ($center_lower_firstRowCount+$center_lower_secondRowCount))
                                                                            +(($center_rib_area * ($B+$H)*2) * ($kattocho/$center_rib_pitch/3));
                $center_weight = $this->two_decimal($center_weight/1000);                                                            

                $end_upper_diameter = $this->get_number($data["end_upper_diameter"]);
                $end_upper_firstRowCount = $this->get_number($data["end_upper_firstRowCount"]);
                $end_upper_secondRowCount = $this->get_number($data["end_upper_secondRowCount"]);
                $end_lower_diameter = $this->get_number($data["end_lower_diameter"]);
                $end_lower_firstRowCount = $this->get_number($data["end_lower_firstRowCount"]);
                $end_lower_secondRowCount = $this->get_number($data["end_lower_secondRowCount"]);
                $end_rib_diameter = $this->get_number($data["end_rib_diameter"]);
                $end_rib_count = $this->get_number($data["end_rib_count"]);
                $end_rib_pitch = $this->get_number($data["end_rib_pitch"]);

                $end_upper_area = pow($end_upper_diameter,2)/$divValue;
                $end_lower_area = pow($end_lower_diameter,2)/$divValue;
                $end_rib_area = pow($end_rib_diameter,2);

                $end_weight = ($end_rib_pitch == 0)? 0 : (($end_upper_area * ($kattocho/3)) * ($end_upper_firstRowCount+$end_upper_secondRowCount))
                                                                    +(($end_lower_area * ($kattocho/3)) * ($end_lower_firstRowCount+$end_lower_secondRowCount))
                                                                    +(($end_rib_area * ($B+$H)*2) * ($kattocho/$end_rib_pitch/3));
                $end_weight = $this->two_decimal($end_weight/1000);//change to tons

                $element_id = $data["element_id"];

                $totalWeight = $start_weight + $center_weight + $end_weight;
                array_push($result_list,array("element_id"=>$element_id,"level"=>$level,"start_weight"=>$start_weight,"center_weight"=>$center_weight,"end_weight"=>$end_weight,"total"=>$totalWeight,"category"=>"梁","phase"=>$phase));
            }
            return $result_list;
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    function FoundationTekkinCalculation($foundation_tekkin_list){
        try{
            $result_list = array();
            $divValue = 162.28;
            foreach($foundation_tekkin_list as $data){
                $D = $this->get_number($data["D"]);
                $H = $this->get_number($data["H"]);
                $W = $this->get_number($data["W"]);
                $level = $data["level"];
                $phase = $data["phase"];
                $upper_X_diameter = $this->get_number($data["upper_X_diameter"]);
                $upper_X_count = $this->get_number($data["upper_X_count"]);
                $upper_Y_diameter = $this->get_number($data["upper_Y_diameter"]);
                $upper_Y_count = $this->get_number($data["upper_Y_count"]);

                $upper_X_area = pow($upper_X_diameter,2)/$divValue;
                $upper_Y_area = pow($upper_Y_diameter,2)/$divValue;
                $start_weight = (($upper_X_area * ($W+$H+$H) ) * $upper_X_count) + (($upper_Y_area * ($D+$H+$H)) * $upper_Y_count);
                $start_weight = $this->two_decimal($start_weight/1000);//change to tons              

                $lower_X_diameter = $this->get_number($data["lower_X_diameter"]);
                $lower_X_count = $this->get_number($data["lower_X_count"]);
                $lower_Y_diameter = $this->get_number($data["lower_Y_diameter"]);
                $lower_Y_count = $this->get_number($data["lower_Y_count"]);
                
                $lower_X_area = pow($lower_X_diameter,2)/$divValue;
                $lower_Y_area = pow($lower_Y_diameter,2)/$divValue;
                $end_weight = (($lower_X_area * ($W+$H+$H) ) * $lower_X_count) + (($lower_Y_area * ($D+$H+$H)) * $lower_Y_count);
                $end_weight = $this->two_decimal($end_weight/1000);
                $element_id = $data["element_id"];
    
                $totalWeight = $start_weight + $end_weight;
                array_push($result_list,array("element_id"=>$element_id,"level"=>$level,"start_weight"=>$start_weight,"center_weight"=>"","end_weight"=>$end_weight,"total"=>$totalWeight,"category"=>"基礎","phase"=>$phase));
            }
            return $result_list;

        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    function get_number($param_str){
        if($param_str == "")
            return 0;
        else
            return preg_replace("/[^0-9.]/", "", $param_str);
    }
    
    function two_decimal($number){
        return number_format((float)$number, 2, '.', '');
    }
    

}
