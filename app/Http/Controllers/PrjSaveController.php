<?php

namespace App\Http\Controllers;
use App\Models\ForgeModel;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel; 
use App\Exports\TekkinExport; 
use PHPExcel;
use PHPExcel_IOFactory;

class PrjSaveController extends Controller
{
    // function index()
    // {
    //  $forge = new ForgeModel();
    //  $projects = $forge->GetProjects();   
    //  return view('admin')->with(["projects"=>$projects]);
    // }
    
     function GetForgeProperties(){
        $conf = new \Autodesk\Auth\Configuration();//escape from current name space by using '/'
        $conf->getDefaultConfiguration()
        ->setClientId('Mt1Tul68redoV5OEMKwRh1aYQnsdmtJW')
        ->setClientSecret('8FOuOTPK6nOp4bOl');

        
        try {

            //get version urns from database
            $version_db_info = $this->GetAutoSaveProjectUrns();
            

            foreach($version_db_info as $version){
                
                $authObj = new \Autodesk\Auth\OAuth2\TwoLeggedAuth();
                $scopes = array("code:all","data:read","data:write","bucket:read");
                $authObj->setScopes($scopes);
        
                $authObj->fetchToken();
                $access_token = $authObj->getAccessToken();
                $authObj->setAccessToken($access_token);
                $derivInst = new \Autodesk\Forge\Client\Api\DerivativesApi($authObj);

       
                $urn = $version["forge_version_id"];//forge_id
                $version_id = $version["id"];//database_id
                $item_id = $version["item_id"];//db_project id
                $item_name = $version["item_name"];
                $version_number = $version["version_number"];
                $storage_size = $version["storage_size"];
                
                if($version_number == 1 )continue; 
                if($storage_size > 209715200 )continue; 
                //if($item_id != 0)continue;
                $check_exist = $this->GetAlreadySavedFlag($item_id,$version_number);

                //if($check_exist[0]["already_saved"] == 1) continue;
                $metaDataObj = $derivInst->getMetadata(base64_encode($urn),null);
                if(empty($metaDataObj["data"]["metadata"]))continue;
                    
                $metaData = $metaDataObj["data"]["metadata"];
                unset($metaDataObj);
                clearstatcache();
                gc_collect_cycles();
                foreach($metaData as $mData){
                    $category_list = array();
                    $viewName = $mData["name"];
                    if(strpos($viewName,'???????????????') === false)continue;
                    //&& strpos($viewName,'??????') == false 
                    /*if(strpos($viewName,'???????????????') === false && strpos($viewName,'??????') === false 
                        && strpos($viewName,'??????') === false 
                        && strpos($viewName,'??????') === false && strpos($viewName,'??????') === false
                        && strpos($viewName,'?????????') === false )continue;*/

                        //echo $viewName;
                        //echo $version_number;//continue;
                    $guid = $mData["guid"];
                    $viewTree = $derivInst->getModelviewMetadata(base64_encode($urn),$guid,null);
                    if(empty($viewTree['data']['objects']))continue;
                    $hirechyData = $viewTree['data']['objects'];
                    unset($viewTree);
                    clearstatcache();
                    gc_collect_cycles();
                    foreach($hirechyData as $vData){                    
                        $categoris = $vData['objects'];
                        foreach($categoris as $category){
                            $type_ids = array();
                            $category_name = $category['name'];
                             
                            if($category_name == "?????????" || $category_name == "??????????????????" || $category_name == "???" || $category_name == "???" || $category_name == "????????????"){   //  
                                //print_r($category_name);          
                                $materials = $category["objects"];                              
                                foreach($materials as $material){
                                    $types = $material['objects'];                                  
                                    foreach($types as $type){ 
                                        $type_pro = $type['objects'];
                                        foreach($type_pro as $property) {
                                            $typeID = $property['objectid'];
                                            array_push($type_ids,$typeID);   
                                        }                                                                                                                                                                                         
                                   }
                                }
                               //break; 
                            }
                         
                            if(sizeof($type_ids) > 0){
                                $category_list[$category_name] = array_unique($type_ids);
                            }
                            
                           
                        }                            
                    }
                    //break;
                    if(sizeof($category_list) > 0){
                        //$this->PrepareProperties($category_list,$derivInst,$urn,$guid,$viewName,$item_id,$version_number,$version_id);
                    }
                   
                    //save kouji project info
                    // $this->SaveProjectInfomation($derivInst,$urn,$guid,$item_name,$version_number);
                    
                    //save forge project info
                    $this->SaveForgeProjectInfomation($derivInst,$urn,$guid,$item_name);
                }
                
               
                unset($metaData);
                clearstatcache();
                gc_collect_cycles();
                //update tb_forge_ver already_saved flag to 1 
                $this->UpdateAlreadySavedFlag($item_id,$version_number);
            }           
            
            
        } catch (Exception $e) {
            echo 'Exception when calling forge library function : ', $e->getMessage(), PHP_EOL;
        }
    }

    function SaveProjectInfomation($derivInst,$urn,$guid,$item_name,$version_number){
        
        try{
            $properties = $derivInst->getModelviewProperties(base64_encode($urn),$guid,null);
            return;
            //print_r($properties);return;
            //$data = json_decode(json_encode($properties),true);
            $allProperties = isset($properties['data']['collection']) ?$properties['data']['collection'] :null; 
            unset($properties);
            clearstatcache();
            gc_collect_cycles();
            if($allProperties !== null){

                foreach($allProperties as $property){

                    if(isset($property['objectid']) || $property['name'] != "Model") {
                        $model_property = json_decode(json_encode($property["properties"]),true);//$property["properties"];
                        if(!isset( $model_property["?????????"]))continue;
                        $other = $model_property["?????????"];
                        $kouji_name = isset($other['?????????????????????']) ? $other['?????????????????????']: "";
                        $client_name = isset($other['?????????????????????']) ? $other['?????????????????????']: "";
                        $address = isset($other['???????????????']) ? $other['???????????????']: "";
    
                        //echo $item_name."---------".$kouji_name."----------".$client_name."----------".$address;
                        $query = "INSERT INTO tb_document(id,name,koujimeisho,hachuusha,sekoubasho,version)
                                SELECT COALESCE(MAX(id), 0) + 1,'$item_name','$kouji_name','$client_name','$address',$version_number FROM tb_document
                                ON DUPLICATE KEY UPDATE koujimeisho = '$kouji_name',hachuusha = '$client_name',sekoubasho = '$address',version = $version_number";
                        // DB::insert($query);
                    }

                    break;
                }

            
            }
           
        }catch(Exception $e){
            echo 'Exception when project info save : ', $e->getMessage(), PHP_EOL;
        }
    }
    
    function SaveForgeProjectInfomation($derivInst,$urn,$guid,$item_name){
        
        try{
            $properties = $derivInst->getModelviewProperties(base64_encode($urn),$guid,null);
            //$data = json_decode(json_encode($properties),true);
            $allProperties = isset($properties['data']['collection']) ?$properties['data']['collection'] :null; 
            unset($properties);
            clearstatcache();
            gc_collect_cycles();

            if($allProperties !== null){

                foreach($allProperties as $property){

                    if(isset($property['objectid']) || $property['name'] != "Model") {
                        $model_property = json_decode(json_encode($property["properties"]),true);//$property["properties"];
                        if(!isset( $model_property["?????????"]))continue;
                        $other = $model_property["?????????"];
                        $project_title = isset($other['?????????????????????']) ? $other['?????????????????????']: "";
                        $project_title = $project_title == "?????????????????????" ? "" : $project_title;
                        $project_number = isset($other['????????????????????????']) ? $other['????????????????????????']: "";
                        $project_number = $project_number == "00-00000-000" ? "" : $project_number;
                        $address = isset($other['???????????????']) ? $other['???????????????']: "";
                        $address = $address == "??????????????????????????????" ? "" : $address;
                        $client_name = isset($other['?????????????????????']) ? $other['?????????????????????']: "";
                        $client_name = $client_name == "????????????" ? "" : $client_name;
                        
                        $struct_architect1 = "";
                        $struct_architect2 = ""; 
                        $struct_architect3 = "";
                        $struct_architect4 = "";
                        $struct_architect5 = "";
                        $struct_architect6 = "";
                        $design_architect1 = "";
                        $design_architect2 = "";
                        $design_architect3 = "";
                        $design_architect4 = "";
                        $design_architect5 = "";
                        $design_architect6 = "";
                        $facility_architect = "";
                        if(isset($model_property["?????????"])){
                            $data = $model_property["?????????"];
                            $struct_architect1 = isset($data['PJ_???????????????1_']) ? $data['PJ_???????????????1_']: "";
                            $struct_architect2 = isset($data['PJ_???????????????2_']) ? $data['PJ_???????????????2_']: "";
                            $struct_architect3 = isset($data['PJ_???????????????3_']) ? $data['PJ_???????????????3_']: "";
                            $struct_architect4 = isset($data['PJ_???????????????4_']) ? $data['PJ_???????????????4_']: "";
                            $struct_architect5 = isset($data['PJ_???????????????5_']) ? $data['PJ_???????????????5_']: "";
                            $struct_architect6 = isset($data['PJ_???????????????6_']) ? $data['PJ_???????????????6_']: "";
                            $design_architect1 = isset($data['PJ_???????????????1_']) ? $data['PJ_???????????????1_']: "";
                            $design_architect2 = isset($data['PJ_???????????????2_']) ? $data['PJ_???????????????2_']: "";
                            $design_architect3 = isset($data['PJ_???????????????3_']) ? $data['PJ_???????????????3_']: "";
                            $design_architect4 = isset($data['PJ_???????????????4_']) ? $data['PJ_???????????????4_']: "";
                            $design_architect5 = isset($data['PJ_???????????????5_']) ? $data['PJ_???????????????5_']: "";
                            $design_architect6 = isset($data['PJ_???????????????6_']) ? $data['PJ_???????????????6_']: "";
                            $facility_architect = isset($data['PJ_???????????????1_']) ? $data['PJ_???????????????1_']: "";
                        }
    
                        // echo $item_name;
                        // echo $item_name."---------".$project_title."----------".$project_number."----------".$address."----------".$client_name;
                        $query = "INSERT INTO tb_forge_project_info(id,project_name,project_title,project_number,address,orderer,
                                structural_architect1,structural_architect2,structural_architect3,structural_architect4,structural_architect5,structural_architect6,
                                design_architect1,design_architect2,design_architect3,design_architect4,design_architect5,design_architect6,facility_architect)
                                SELECT COALESCE(MAX(id), 0) + 1,'$item_name','$project_title','$project_number','$address','$client_name',
                                '$struct_architect1','$struct_architect2','$struct_architect3','$struct_architect4','$struct_architect5','$struct_architect6',
                                '$design_architect1','$design_architect2','$design_architect3','$design_architect4','$design_architect5','$design_architect6','$facility_architect' FROM tb_forge_project_info
                                ON DUPLICATE KEY UPDATE project_title = '$project_title',project_number = '$project_number',address = '$address',orderer = '$client_name',
                                structural_architect1 = '$struct_architect1',structural_architect2 = '$struct_architect2',structural_architect3 = '$struct_architect3',
                                structural_architect4 = '$struct_architect4',structural_architect5 = '$struct_architect5',structural_architect6 = '$struct_architect6',
                                design_architect1 = '$design_architect1',design_architect2 = '$design_architect2',design_architect3 = '$design_architect3',
                                design_architect4 = '$design_architect4',design_architect5 = '$design_architect5',design_architect6 = '$design_architect6',facility_architect = '$facility_architect'";
                        echo $query;
                        // DB::insert($query);
                        // print_r($project_title."\n");
                    }

                    break;
                }

            
            }
           
        }catch(Exception $e){
            echo 'Exception when project info save : ', $e->getMessage(), PHP_EOL;
        }
    }
    
    function PrepareProperties($category_list,$derivInst,$urn,$guid,$viewName,$item_id,$version_number,$version_id){

            $properties = $derivInst->getModelviewProperties(base64_encode($urn),$guid,null);
            $allProperties = $properties['data']['collection'];
            unset($properties);
            clearstatcache();
            gc_collect_cycles();
            $column_properties = array();
            foreach($category_list as $name=>$type_id_list){
                $category_name = $name;
                $save_list = array();
                $tekkin_list = array();

                foreach($type_id_list as $type_id){
                    
                    foreach($allProperties as $property){
                        if($property['objectid'] != $type_id) continue;
                        $element_name = $property['name'];
                        if(!isset($property["properties"]["??????"]) || !isset($property["properties"]["??????"]) || !isset($property["properties"]["????????????"])|| !isset($property["properties"]["??????????????? / ??????"]))continue;
                        $saveData = $this->FilterProperty($property["properties"],$element_name,$category_name,$viewName);
                        array_push($save_list,$saveData);   
                        if($category_name == "?????????" || $category_name == "??????????????????" || $category_name == "????????????"){
                            if(isset($property["properties"]["?????????"])){
                                $kattocho = "";
                                if(isset($property["properties"]["??????"])){
                                    $tempData = $property["properties"]["??????"];
                                    $kouzo = json_decode(json_encode($tempData),true);
                                    $kattocho = isset($kouzo["????????????"]) ? $kouzo["????????????"] : "";
                                    //print_r($kattocho);exit;
                                }
                                $tekkinData = $this->FilterTekkinProperty($property["properties"]["?????????"],$element_name,$category_name,$property["properties"]["??????"],
                                                                          $property["properties"]["??????"],$kattocho,$viewName,$type_id);
                                array_push($tekkin_list,$tekkinData);
                            }
                               
                        }
                        //return;                         
                    }
                }

                if(sizeof($save_list) > 0){
                    switch($category_name){
                        case "?????????" : $this->SaveColumn($save_list,$version_id,$item_id,$version_number);break;
                        case "??????????????????" : $this->SaveBeam($save_list,$version_id,$item_id,$version_number);break;
                        case "???" : $this->SaveFloor($save_list,$version_id,$item_id,$version_number);break;
                        case "???" : $this->SaveWall($save_list,$version_id,$item_id,$version_number);break;
                        case "????????????" : $this->SaveFoundation($save_list,$version_id,$item_id,$version_number);break;
                    }
                    
                }

                if(sizeof($tekkin_list) > 0){
                    switch($category_name){
                        case "?????????" : $this->SaveColumnTekkin($tekkin_list,$version_id,$item_id,$version_number);break;
                        case "??????????????????" : $this->SaveBeamTekkin($tekkin_list,$version_id,$item_id,$version_number);break;                               
                        case "????????????" : $this->SaveFoundationTekkin($tekkin_list,$version_id,$item_id,$version_number);break;
                    }
                }
            } 
            
            unset($allProperties);
            clearstatcache();
            gc_collect_cycles();    
        
    }

    /**
     * special character escaping single code fun() 
     * given parameter[string]
     * return escape string
     * to save special char to database
     */
    function escape_string($string){
        $escape_string = str_replace("'", "\'",$string);
        return $escape_string;
    }

    /**l
     * Update already_saved to 1 
     * for skip next time save
     */
    public function UpdateAlreadySavedFlag($item_id,$version_number)
    {
        $query = "UPDATE  tb_forge_version SET already_saved = 1 WHERE item_id = $item_id AND version_number = $version_number";
        DB::update($query);
    }

    function GetAlreadySavedFlag($item_id,$version_number){
        $query = "SELECT already_saved FROM tb_forge_version WHERE item_id = $item_id AND version_number = $version_number LIMIT 1";
        $result = DB::select($query);
        return json_decode(json_encode($result),true);//change array object to array
    }

    public function FilterProperty($property,$element_name,$category_name,$viewName)
    {
        $material = $property['??????????????? / ??????'];
        $identification_info = $property["????????????"];
        $kosoku = $property['??????'];
        $sunPo = $property['??????'];
       
        $typeName = $identification_info->????????????;
        $workset = $identification_info->??????????????????;
        $kouzouMaterial = isset($material->?????????????????????) ? $material->?????????????????????: $material->???????????????_???????????????;       
        $level = "";
        if(isset($kosoku->???????????????)|| isset($kosoku->???????????????)){
            $level = isset($kosoku->???????????????) ? $kosoku->???????????????: $kosoku->???????????????; 
        } 
        $volume = 0;
        if($category_name != "????????????"){
            if(isset($sunPo->??????))
             $volume =  preg_replace("/[^0-9.]/", "",$sunPo->??????);//get float from string 
        }else{
            $width=0;$length=0;$depth=0;
            if(isset($sunPo->???)|| isset($sunPo->W))
                $width = isset($sunPo->???) ? preg_replace("/[^0-9.]/", "",$sunPo->???) : preg_replace("/[^0-9.]/", "",$sunPo->W);
            if(isset($sunPo->??????)|| isset($sunPo->H))
                $length = isset($sunPo->??????) ? preg_replace("/[^0-9.]/", "",$sunPo->??????) : preg_replace("/[^0-9.]/", "",$sunPo->H);
            if(isset($sunPo->??????)|| isset($sunPo->D))
                $depth = isset($sunPo->??????) ? preg_replace("/[^0-9.]/", "",$sunPo->??????) :  preg_replace("/[^0-9.]/", "",$sunPo->D);
            $volume = ($width/1000) * ($length/1000) * ($depth/1000);
        }
        $tempArr= explode(" ", $element_name);
        $family_name = $tempArr[0];
        $element_id = preg_replace("/[^0-9.]/", "", $tempArr[1]);
        return array("type_name"=>$typeName,"material"=>$kouzouMaterial,"level"=>$level,"volume"=>$volume,"workset"=>$workset,"family_name" =>$family_name,"element_id"=>$element_id,"phase"=>$viewName,"element_db_id"=>$element_name);
        
    }

    function FilterTekkinProperty($tekkinProperty,$element_name,$category_name,$sunpoProperty,$kosokuProperty,$kattocho,$viewName){
        $tekkin = json_decode(json_encode($tekkinProperty),true);
        $sunpo = json_decode(json_encode($sunpoProperty),true);
        $kosoku = json_decode(json_encode($kosokuProperty),true);
        $tempArr= explode(" ", $element_name);
        $family_name = $tempArr[0];
        $element_id = preg_replace("/[^0-9.]/", "", $tempArr[1]);
        if($category_name == "??????????????????"){
            $B = isset($sunpo["B"]) ? $sunpo["B"] : "";
            $H = isset($sunpo["H"]) ? $sunpo["H"] : "";
            //$kattocho = isset($sunpo["????????????"]) ? $sunpo["????????????"] : "";
            $level = isset($kosoku["???????????????"])? $kosoku["???????????????"] : $kosoku["???????????????"]; 

            //??????
            $start_upper_diameter = isset($tekkin["?????? ????????? ??????"]) ? $tekkin["?????? ????????? ??????"] : "";
            $start_upper_firstRowCount = isset($tekkin["?????? ????????? 1??????????????????"]) ? $tekkin["?????? ????????? 1??????????????????"] : "";
            $start_upper_secondRowCount = isset($tekkin["?????? ????????? 2??????????????????"]) ? $tekkin["?????? ????????? 2??????????????????"] : ""; 
            $start_lower_diameter = isset($tekkin["?????? ????????? ??????"]) ? $tekkin["?????? ????????? ??????"] : "";
            $start_lower_firstRowCount = isset($tekkin["?????? ????????? 1??????????????????"]) ? $tekkin["?????? ????????? 1??????????????????"] : ""; 
            $start_lower_secondRowCount = isset($tekkin["?????? ????????? 2??????????????????"]) ? $tekkin["?????? ????????? 2??????????????????"] : "";        
            $start_rib_diameter = isset($tekkin["?????? ?????????"]) ? $tekkin["?????? ?????????"]: "";  
            $start_rib_count = isset($tekkin["?????? ????????????"]) ? $tekkin["?????? ????????????"] :""; 
            $start_rib_pitch = isset($tekkin["?????? ???????????????"]) ? $tekkin["?????? ???????????????"] : ""; 

            //??????
            $center_upper_diameter = isset($tekkin["?????? ????????? ??????"]) ? $tekkin["?????? ????????? ??????"] : "";
            $center_upper_firstRowCount = isset($tekkin["?????? ????????? 1??????????????????"]) ? $tekkin["?????? ????????? 1??????????????????"] : "";
            $center_upper_secondRowCount = isset($tekkin["?????? ????????? 2??????????????????"]) ? $tekkin["?????? ????????? 2??????????????????"] : ""; 
            $center_lower_diameter = isset($tekkin["?????? ????????? ??????"]) ? $tekkin["?????? ????????? ??????"] : "";
            $center_lower_firstRowCount = isset($tekkin["?????? ????????? 1??????????????????"]) ? $tekkin["?????? ????????? 1??????????????????"] : ""; 
            $center_lower_secondRowCount = isset($tekkin["?????? ????????? 2??????????????????"]) ? $tekkin["?????? ????????? 2??????????????????"] : "";        
            $center_rib_diameter = isset($tekkin["?????? ?????????"]) ? $tekkin["?????? ?????????"]: "";  
            $center_rib_count = isset($tekkin["?????? ????????????"]) ? $tekkin["?????? ????????????"] :""; 
            $center_rib_pitch = isset($tekkin["?????? ???????????????"]) ? $tekkin["?????? ???????????????"] : ""; 

            //??????
            $end_upper_diameter = isset($tekkin["?????? ????????? ??????"]) ? $tekkin["?????? ????????? ??????"] : "";
            $end_upper_firstRowCount = isset($tekkin["?????? ????????? 1??????????????????"]) ? $tekkin["?????? ????????? 1??????????????????"] : "";
            $end_upper_secondRowCount = isset($tekkin["?????? ????????? 2??????????????????"]) ? $tekkin["?????? ????????? 2??????????????????"] : ""; 
            $end_lower_diameter = isset($tekkin["?????? ????????? ??????"]) ? $tekkin["?????? ????????? ??????"] : "";
            $end_lower_firstRowCount = isset($tekkin["?????? ????????? 1??????????????????"]) ? $tekkin["?????? ????????? 1??????????????????"] : ""; 
            $end_lower_secondRowCount = isset($tekkin["?????? ????????? 2??????????????????"]) ? $tekkin["?????? ????????? 2??????????????????"] : "";        
            $end_rib_diameter = isset($tekkin["?????? ?????????"]) ? $tekkin["?????? ?????????"]: "";  
            $end_rib_count = isset($tekkin["?????? ????????????"]) ? $tekkin["?????? ????????????"] :""; 
            $end_rib_pitch = isset($tekkin["?????? ???????????????"]) ? $tekkin["?????? ???????????????"] : ""; 

            return array("B"=>$B,"H"=>$H,"kattocho"=>$kattocho,"level"=>$level,
                        "start_upper_diameter"=>$start_upper_diameter,"start_upper_firstRowCount"=>$start_upper_firstRowCount,"start_upper_secondRowCount"=>$start_upper_secondRowCount,
                        "start_lower_diameter"=>$start_lower_diameter,"start_lower_firstRowCount"=>$start_lower_firstRowCount,"start_lower_secondRowCount"=>$start_lower_secondRowCount,
                        "start_rib_diameter"=>$start_rib_diameter,"start_rib_count"=>$start_rib_count,"start_rib_pitch"=>$start_rib_pitch,
                     
                        "end_upper_diameter"=>$end_upper_diameter,"end_upper_firstRowCount"=>$end_upper_firstRowCount,"end_upper_secondRowCount"=>$end_upper_secondRowCount,
                        "end_lower_diameter"=>$end_lower_diameter,"end_lower_firstRowCount"=>$end_lower_firstRowCount,"end_lower_secondRowCount"=>$end_lower_secondRowCount,
                        
                        "center_upper_diameter"=>$center_upper_diameter,"center_upper_firstRowCount"=>$center_upper_firstRowCount,"center_upper_secondRowCount"=>$center_upper_secondRowCount,
                        "center_lower_diameter"=>$center_lower_diameter,"center_lower_firstRowCount"=>$center_lower_firstRowCount,"center_lower_secondRowCount"=>$center_lower_secondRowCount,
                        "center_rib_diameter"=>$center_rib_diameter,"center_rib_count"=>$center_rib_count,"center_rib_pitch"=>$center_rib_pitch,
                       "end_rib_diameter"=>$end_rib_diameter,"end_rib_count"=>$end_rib_count,"end_rib_pitch"=>$end_rib_pitch,"element_id"=>$element_id,"phase"=>$viewName,"element_db_id"=>$element_name);

        }else if($category_name == "?????????"){

            $W = isset($sunpo["W"]) ? $sunpo["W"] : "";
            $D = isset($sunpo["D"]) ? $sunpo["D"] : "";
            $volume = isset($sunpo["??????"]) ? $sunpo["??????"] : "";
            $level = isset($kosoku["???????????????"])? $kosoku["???????????????"] : $kosoku["???????????????"]; 
           
             //??????
            $start_diameter = isset($tekkin["?????? ????????????"]) ? $tekkin["?????? ????????????"] : "";    
            $start_X_firstRowCount  = isset($tekkin["?????? ??????X??????1???????????????"]) ? $tekkin["?????? ??????X??????1???????????????"] : "" ; 
            $start_X_secondRowCount = isset($tekkin["?????? ??????X??????2???????????????"]) ? $tekkin["?????? ??????X??????2???????????????"] : ""; 
            $start_Y_firstRowCount = isset($tekkin["?????? ??????Y??????1?????????????????????"]) ? $tekkin["?????? ??????Y??????1???????????????"] : "";
            $start_Y_secondRowCount = isset($tekkin["?????? ??????Y??????2???????????????"]) ? $tekkin["?????? ??????Y??????2???????????????"] : ""; 
            $start_rib_diameter = isset($tekkin["?????? ?????????"]) ? $tekkin["?????? ?????????"] : "";  
            $start_rib_pitch = isset($tekkin["?????? ???????????????"]) ? $tekkin["?????? ???????????????"] : "";  

             //?????? 
            $end_diameter = isset($tekkin["?????? ????????????"]) ? $tekkin["?????? ????????????"] : "";   
            $end_X_firstRowCount  = isset($tekkin["?????? ??????X??????1?????????????????????"]) ? $tekkin["?????? ??????X??????1???????????????"] : "" ; 
            $end_X_secondRowCount = isset($tekkin["?????? ??????X??????2???????????????"]) ? $tekkin["?????? ??????X??????2???????????????"] : ""; 
            $end_Y_firstRowCount = isset($tekkin["?????? ??????Y??????1???????????????"]) ? $tekkin["?????? ??????Y??????1???????????????"] : "";
            $end_Y_secondRowCount = isset($tekkin["?????? ??????Y??????2???????????????"]) ? $tekkin["?????? ??????Y??????2???????????????"] : ""; 
            $end_rib_diameter = isset($tekkin["?????? ?????????"]) ? $tekkin["?????? ?????????"] : "";  
            $end_rib_pitch = isset($tekkin["?????? ???????????????"]) ? $tekkin["?????? ???????????????"] : "";  

            return array("W"=>$W,"D"=>$D,"volume"=>$volume,"level"=>$level,
                    "start_diameter"=>$start_diameter,"start_X_firstRowCount"=>$start_X_firstRowCount,"start_X_secondRowCount"=>$start_X_secondRowCount,
                    "start_Y_firstRowCount"=>$start_Y_firstRowCount,"start_Y_secondRowCount"=>$start_Y_secondRowCount,"start_rib_diameter"=>$start_rib_diameter,"start_rib_pitch"=>$start_rib_pitch,
                    "end_diameter"=>$end_diameter,"end_X_firstRowCount"=>$end_X_firstRowCount,"end_X_secondRowCount"=>$end_X_secondRowCount,
                    "end_Y_firstRowCount"=>$end_Y_firstRowCount,"end_Y_secondRowCount"=>$end_Y_secondRowCount,"end_rib_diameter"=>$end_rib_diameter,"end_rib_pitch"=>$end_rib_pitch,"element_id"=>$element_id,"phase"=>$viewName,"element_db_id"=>$element_name);

        }else if($category_name == "????????????"){
            $D = isset($sunpo["D"]) ? $sunpo["D"] : "";
            $H = isset($sunpo["H"]) ? $sunpo["H"] : "";
            $W = isset($sunpo["W"]) ? $sunpo["W"] : "";
            $level = isset($kosoku["???????????????"])? $kosoku["???????????????"] : $kosoku["???????????????"]; 

             //?????????
            $upper_X_diameter = isset($tekkin["?????????_X??????_?????????"]) ? $tekkin["?????????_X??????_?????????"] : "";   
            $upper_X_count = isset($tekkin["?????????_X??????_????????????"]) ? $tekkin["?????????_X??????_????????????"] : ""; 
            $upper_Y_diameter = isset($tekkin["?????????_Y??????_?????????"]) ? $tekkin["?????????_Y??????_?????????"] : ""; 
            $upper_Y_count = isset($tekkin["?????????_Y??????_????????????"]) ? $tekkin["?????????_Y??????_????????????"] : "";

            //?????????
            $lower_X_diameter = isset($tekkin["?????????_X??????_?????????"]) ? $tekkin["?????????_X??????_?????????"] : "";   
            $lower_X_count = isset($tekkin["?????????_X??????_????????????"]) ? $tekkin["?????????_X??????_????????????"] : ""; 
            $lower_Y_diameter = isset($tekkin["?????????_Y??????_?????????"]) ? $tekkin["?????????_Y??????_?????????"] : ""; 
            $lower_Y_count = isset($tekkin["?????????_Y??????_????????????"]) ? $tekkin["?????????_Y??????_????????????"] : "";

            return array("D"=>$D,"H"=>$H,"W"=>$W,"level"=>$level,
                        "upper_X_diameter"=>$upper_X_diameter,"upper_X_count"=>$upper_X_count,"upper_Y_diameter"=>$upper_Y_diameter,"upper_Y_count"=>$upper_Y_count,
                        "lower_X_diameter"=>$lower_X_diameter,"lower_X_count"=>$lower_X_count,"lower_Y_diameter"=>$lower_Y_diameter,"lower_Y_count"=>$lower_Y_count,"element_id"=>$element_id,"phase"=>$viewName,"element_db_id"=>$element_name);
        }

    }

    function GetAutoSaveProjectUrns(){
        $query = "SELECT fv.id,fv.forge_version_id,fv.version_number,fi.id as item_id,fi.name as item_name,fv.storage_size from tb_forge_version fv
                    LEFT JOIN  tb_forge_item  fi on fv.item_id = fi.id
                    LEFT JOIN tb_project fp on fi.project_id = fp.id
                    WHERE fp.auto_save_properties = 1 ORDER BY fv.version_number ";       
        $result = DB::select($query);
        return json_decode(json_encode($result),true);//change array object to array
    }

    public function SaveColumn($save_list,$version_id,$item_id,$version_number){
        
       try{
           $save_element_id = array_column($save_list,"element_id");
         
           $current_ver_ids = ($save_element_id == "") ? "'"."ALL_UNCHECK"."'" : "'" . implode ( "', '", $save_element_id ) . "'";//convert array to string with single code
           $select_deleted_query = "SELECT element_id FROM tb_forge_column WHERE item_id = $item_id AND version_number < $version_number
                                    AND element_id NOT IN($current_ver_ids)";

           $deleted_elements = DB::select($select_deleted_query);

            if(sizeof($deleted_elements) > 0){               
                foreach($deleted_elements as $deleted_id){
                   
                    $ele_id = $deleted_id->element_id;
                    $insert_ids_query = "INSERT IGNORE INTO tb_forge_column_deleted (id,element_id,item_id,version_id,version_number)
                                        SELECT MAX(id) +1,$ele_id,$item_id,$version_id,$version_number FROM tb_forge_column_deleted";
                    DB::insert($insert_ids_query);
                }               
            }

            foreach($save_list as $data){
                $type_name =$data["type_name"]; //$this->escape_string($data["type_name"]);
                $material =$data["material"];// $this->escape_string($data["material"]);
                $level = $this->escape_string($data["level"]);
                $volume = $data["volume"];
                $workset = $data["workset"];
                $family_name = $this->escape_string($data["family_name"]);
                $element_id = $data["element_id"];
                $phase = $data["phase"];
                $element_db_id = $data["element_db_id"];
                DB::insert("CALL column_insert_procedure($item_id,$element_id,'$element_db_id','$type_name','$material','$level',$volume,'$family_name','$workset',$version_id,$version_number,'$phase')");
                
            }

       }catch(Exception $e){
           print_r($e->getMessage());
       }
        
    }

    public function SaveBeam($save_list,$version_id,$item_id,$version_number){
        
       try{
           $save_element_id = array_column($save_list,"element_id");
         
           $current_ver_ids = ($save_element_id == "") ? "'"."ALL_UNCHECK"."'" : "'" . implode ( "', '", $save_element_id ) . "'";//convert array to string with single code
           $select_deleted_query = "SELECT element_id FROM tb_forge_beam WHERE item_id = $item_id AND version_number < $version_number
                                    AND element_id NOT IN($current_ver_ids)";

           $deleted_elements = DB::select($select_deleted_query);

            if(sizeof($deleted_elements) > 0){               
                foreach($deleted_elements as $deleted_id){
                   
                    $ele_id = $deleted_id->element_id;
                    $insert_ids_query = "INSERT IGNORE INTO tb_forge_beam_deleted (id,element_id,item_id,version_id,version_number)
                                        SELECT MAX(id) +1,$ele_id,$item_id,$version_id,$version_number FROM tb_forge_beam_deleted";
                    DB::insert($insert_ids_query);
                }               
            }

            foreach($save_list as $data){
                $type_name = $this->escape_string($data["type_name"]);
                $material = $this->escape_string($data["material"]);
                $level = $this->escape_string($data["level"]);
                $volume = $data["volume"];
                $workset = $data["workset"];
                $family_name = $this->escape_string($data["family_name"]);
                $element_id = $data["element_id"];
                $phase = $data["phase"];
                $element_db_id = $data["element_db_id"];
                DB::insert("CALL beam_insert_procedure($item_id,$element_id,'$element_db_id','$type_name','$material','$level',$volume,'$family_name','$workset',$version_id,$version_number,'$phase')");

            }

       }catch(Exception $e){
           print_r($e->getMessage());
       }

    }

    public function SaveFloor($save_list,$version_id,$item_id,$version_number){
        
       try{
           $save_element_id = array_column($save_list,"element_id");
         
           $current_ver_ids = ($save_element_id == "") ? "'"."ALL_UNCHECK"."'" : "'" . implode ( "', '", $save_element_id ) . "'";//convert array to string with single code
           $select_deleted_query = "SELECT element_id FROM tb_forge_floor WHERE item_id = $item_id AND version_number < $version_number
                                    AND element_id NOT IN($current_ver_ids)";

           $deleted_elements = DB::select($select_deleted_query);

            if(sizeof($deleted_elements) > 0){               
                foreach($deleted_elements as $deleted_id){
                   
                    $ele_id = $deleted_id->element_id;
                    $insert_ids_query = "INSERT IGNORE INTO tb_forge_floor_deleted (id,element_id,item_id,version_id,version_number)
                                        SELECT MAX(id) +1,$ele_id,$item_id,$version_id,$version_number FROM tb_forge_floor_deleted";
                    DB::insert($insert_ids_query);
                }               
            }

            foreach($save_list as $data){
                $type_name = $this->escape_string($data["type_name"]);
                $material = $this->escape_string($data["material"]);
                $level = $this->escape_string($data["level"]);
                $volume = $data["volume"];
                $workset = $data["workset"];
                $family_name = $this->escape_string($data["family_name"]);
                $element_id = $data["element_id"];
                $phase = $data["phase"];
                $element_db_id = $data["element_db_id"];

                DB::insert("CALL floor_insert_procedure($item_id,$element_id,'$element_db_id','$type_name','$material','$level',$volume,'$family_name','$workset',$version_id,$version_number,'$phase')");

            }

       }catch(Exception $e){
           print_r($e->getMessage());
       }
        
    }
    
    public function SaveWall($save_list,$version_id,$item_id,$version_number){
        
        try{
            $save_element_id = array_column($save_list,"element_id");
          
            $current_ver_ids = ($save_element_id == "") ? "'"."ALL_UNCHECK"."'" : "'" . implode ( "', '", $save_element_id ) . "'";//convert array to string with single code
            $select_deleted_query = "SELECT element_id FROM tb_forge_wall WHERE item_id = $item_id AND version_number < $version_number
                                     AND element_id NOT IN($current_ver_ids)";
 
            $deleted_elements = DB::select($select_deleted_query);
 
             if(sizeof($deleted_elements) > 0){               
                 foreach($deleted_elements as $deleted_id){
                    
                     $ele_id = $deleted_id->element_id;
                     $insert_ids_query = "INSERT IGNORE INTO tb_forge_wall_deleted (id,element_id,item_id,version_id,version_number)
                                         SELECT MAX(id) +1,$ele_id,$item_id,$version_id,$version_number FROM tb_forge_wall_deleted";
                     DB::insert($insert_ids_query);
                 }               
             }
                    
             foreach($save_list as $data){
                 $type_name = $this->escape_string($data["type_name"]);
                $material = $this->escape_string($data["material"]);
                $level = $this->escape_string($data["level"]);
                $volume = $data["volume"];
                $workset = $data["workset"];
                $family_name = $this->escape_string($data["family_name"]);
                $element_id = $data["element_id"];
                $phase = $data["phase"];
                $element_db_id = $data["element_db_id"];
 
                 DB::insert("CALL wall_insert_procedure($item_id,$element_id,'$element_db_id','$type_name','$material','$level',$volume,'$family_name','$workset',$version_id,$version_number,'$phase')");
 
             }

        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function SaveFoundation($save_list,$version_id,$item_id,$version_number){
        
        try{
            $save_element_id = array_column($save_list,"element_id");
          
            $current_ver_ids = ($save_element_id == "") ? "'"."ALL_UNCHECK"."'" : "'" . implode ( "', '", $save_element_id ) . "'";//convert array to string with single code
            $select_deleted_query = "SELECT element_id FROM tb_forge_foundation WHERE item_id = $item_id AND version_number < $version_number
                                     AND element_id NOT IN($current_ver_ids)";
 
            $deleted_elements = DB::select($select_deleted_query);
 
             if(sizeof($deleted_elements) > 0){               
                 foreach($deleted_elements as $deleted_id){
                    
                     $ele_id = $deleted_id->element_id;
                     $insert_ids_query = "INSERT IGNORE INTO tb_forge_foundation_deleted (id,element_id,item_id,version_id,version_number)
                                         SELECT MAX(id) +1,$ele_id,$item_id,$version_id,$version_number FROM tb_forge_foundation_deleted";
                     DB::insert($insert_ids_query);
                 }               
             }

             foreach($save_list as $data){
                $type_name = $this->escape_string($data["type_name"]);
                $material = $this->escape_string($data["material"]);
                $level = $this->escape_string($data["level"]);
                $volume = $data["volume"];
                $workset = $data["workset"];
                $family_name = $this->escape_string($data["family_name"]);
                $element_id = $data["element_id"];
                $phase = $data["phase"];
                $element_db_id = $data["element_db_id"];
 
                 DB::insert("CALL foundation_insert_procedure($item_id,$element_id,'$element_db_id','$type_name','$material','$level',$volume,'$family_name','$workset',$version_id,$version_number,'$phase')");
 
             }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
         
    }

    
    public function SaveColumnTekkin($save_list,$version_id,$item_id,$version_number){
        
        try{
           
             foreach($save_list as $data){
                 $W = $data["W"];
                 $D = $data["D"];
                 $volume = $data["volume"];
                 $level = $data["level"];
                 $start_diameter =$data["start_diameter"]; 
                 $start_X_firstRowCount =$data["start_X_firstRowCount"];
                 $start_X_secondRowCount = $this->escape_string($data["start_X_secondRowCount"]);
                 $start_Y_firstRowCount = $data["start_Y_firstRowCount"];
                 $start_Y_secondRowCount = $data["start_Y_secondRowCount"];
                 $start_rib_diameter = $this->escape_string($data["start_rib_diameter"]);
                 $start_rib_pitch = $data["start_rib_pitch"];

                 $end_diameter =$data["end_diameter"]; 
                 $end_X_firstRowCount =$data["end_X_firstRowCount"];
                 $end_X_secondRowCount = $this->escape_string($data["end_X_secondRowCount"]);
                 $end_Y_firstRowCount = $data["end_Y_firstRowCount"];
                 $end_Y_secondRowCount = $data["end_Y_secondRowCount"];
                 $end_rib_diameter = $this->escape_string($data["end_rib_diameter"]);
                 $end_rib_pitch = $data["end_rib_pitch"];
                 $element_id = $data["element_id"];
                 $phase = $data["phase"];
                 $element_db_id = $data["element_db_id"];
 
                 $query = "INSERT IGNORE INTO tb_forge_column_tekkin
                 (id,item_id,element_id,element_db_id,W,D,volume,level,start_diameter,start_X_firstRowCount,start_X_secondRowCount,
                 start_Y_firstRowCount,start_Y_secondRowCount,start_rib_diameter,start_rib_pitch,
                 end_diameter,end_X_firstRowCount,end_X_secondRowCount,
                 end_Y_firstRowCount,end_Y_secondRowCount,end_rib_diameter,end_rib_pitch,version_id,version_number,phase)
                 SELECT COALESCE(MAX(id), 0) + 1,$item_id,$element_id,'$element_db_id','$W','$D','$volume','$level','$start_diameter','$start_X_firstRowCount','$start_X_secondRowCount',
                 '$start_Y_firstRowCount','$start_Y_secondRowCount','$start_rib_diameter','$start_rib_pitch',
                 '$end_diameter','$end_X_firstRowCount','$end_X_secondRowCount',
                 '$end_Y_firstRowCount','$end_Y_secondRowCount','$end_rib_diameter','$end_rib_pitch',$version_id,$version_number,'$phase' FROM tb_forge_column_tekkin
                 ON DUPLICATE KEY UPDATE
                 element_db_id = '$element_db_id',
                 W = '$W',
                 D = '$D',
                 volume = '$volume',
                 level = '$level',
                 start_diameter = '$start_diameter',
                 start_X_firstRowCount = '$start_X_firstRowCount',
                 start_X_secondRowCount = '$start_X_secondRowCount',
                 start_Y_firstRowCount = '$start_Y_firstRowCount',
                 start_Y_secondRowCount = '$start_Y_secondRowCount',
                 start_rib_diameter = '$start_rib_diameter',
                 start_rib_pitch = '$start_rib_pitch',
                 end_diameter = '$end_diameter',
                 end_X_firstRowCount = '$end_X_firstRowCount',
                 end_X_secondRowCount = '$end_X_secondRowCount',
                 end_Y_firstRowCount = '$end_Y_firstRowCount',
                 end_Y_secondRowCount = '$end_Y_secondRowCount',
                 end_rib_diameter = '$end_rib_diameter',
                 end_rib_pitch = '$end_rib_pitch',
                 version_id = $version_id,
                 version_number = $version_number,
                 phase = '$phase'";
                
                DB::insert($query);

                //DB::insert("CALL column_insert_procedure($item_id,$element_id,'$type_name','$material','$level',$volume,'$family_name','$workset',$version_id,$version_number)");
                 
             }
 
        }catch(Exception $e){
            print_r($e->getMessage());
        }
         
    }
 
    public function SaveBeamTekkin($save_list,$version_id,$item_id,$version_number){
         
        try{
           
             foreach($save_list as $data){
                 $B = $data["B"];
                 $H = is_array($data["H"])? $data["H"][0]: $data["H"];
                 $kattocho = $data["kattocho"];
                 $level = $data["level"];
                 $start_upper_diameter = $data["start_upper_diameter"];
                 $start_upper_firstRowCount = $data["start_upper_firstRowCount"];
                 $start_upper_secondRowCount = $data["start_upper_secondRowCount"];
                 $start_lower_diameter = $data["start_lower_diameter"];
                 $start_lower_firstRowCount = $data["start_lower_firstRowCount"];
                 $start_lower_secondRowCount = $data["start_lower_secondRowCount"];
                 $start_rib_diameter = $data["start_rib_diameter"];
                 $start_rib_count = $data["start_rib_count"];
                 $start_rib_pitch = $data["start_rib_pitch"];

                 $center_upper_diameter = $data["center_upper_diameter"];
                 $center_upper_firstRowCount = $data["center_upper_firstRowCount"];
                 $center_upper_secondRowCount = $data["center_upper_secondRowCount"];
                 $center_lower_diameter = $data["center_lower_diameter"];
                 $center_lower_firstRowCount = $data["center_lower_firstRowCount"];
                 $center_lower_secondRowCount = $data["center_lower_secondRowCount"];
                 $center_rib_diameter = $data["center_rib_diameter"];
                 $center_rib_count = $data["center_rib_count"];
                 $center_rib_pitch = $data["center_rib_pitch"];

                 $end_upper_diameter = $data["end_upper_diameter"];
                 $end_upper_firstRowCount = $data["end_upper_firstRowCount"];
                 $end_upper_secondRowCount = $data["end_upper_secondRowCount"];
                 $end_lower_diameter = $data["end_lower_diameter"];
                 $end_lower_firstRowCount = $data["end_lower_firstRowCount"];
                 $end_lower_secondRowCount = $data["end_lower_secondRowCount"];
                 $end_rib_diameter = $data["end_rib_diameter"];
                 $end_rib_count = $data["end_rib_count"];
                 $end_rib_pitch = $data["end_rib_pitch"];
                 $element_id = $data["element_id"];
                 $phase = $data["phase"];
                 $element_db_id = $data["element_db_id"];
                
                 $query = "INSERT  INTO tb_forge_beam_tekkin
                            (id,item_id,element_id,element_db_id,B,H,kattocho,level,start_upper_diameter,start_upper_firstRowCount,start_upper_secondRowCount,
                            start_lower_diameter,start_lower_firstRowCount,start_lower_secondRowCount,
                            start_rib_diameter,start_rib_count,start_rib_pitch,

                            center_upper_diameter,center_upper_firstRowCount,center_upper_secondRowCount,
                            center_lower_diameter,center_lower_firstRowCount,center_lower_secondRowCount,
                            center_rib_diameter,center_rib_count,center_rib_pitch,

                            end_upper_diameter,end_upper_firstRowCount,end_upper_secondRowCount,
                            end_lower_diameter,end_lower_firstRowCount,end_lower_secondRowCount,
                            end_rib_diameter,end_rib_count,end_rib_pitch,version_id,version_number,phase)
                            SELECT COALESCE(MAX(id), 0) + 1,$item_id,$element_id,'$element_db_id','$B','$H','$kattocho','$level','$start_upper_diameter','$start_upper_firstRowCount','$start_upper_secondRowCount',
                            '$start_lower_diameter','$start_lower_firstRowCount','$start_lower_secondRowCount',
                            '$start_rib_diameter','$start_rib_count','$start_rib_pitch',

                            '$center_upper_diameter','$center_upper_firstRowCount','$center_upper_secondRowCount',
                            '$center_lower_diameter','$center_lower_firstRowCount','$center_lower_secondRowCount',
                            '$center_rib_diameter','$center_rib_count','$center_rib_pitch',

                            '$end_upper_diameter','$end_upper_firstRowCount','$end_upper_secondRowCount',
                            '$end_lower_diameter','$end_lower_firstRowCount','$end_lower_secondRowCount',
                            '$end_rib_diameter','$end_rib_count','$end_rib_pitch',$version_id,$version_number,'$phase' FROM tb_forge_beam_tekkin
                            ON DUPLICATE KEY UPDATE
                            element_db_id = '$element_db_id',
                            B = '$B',
                            H = '$H',
                            kattocho = '$kattocho',
                            level = '$level',
                            start_upper_diameter = '$start_upper_diameter',
                            start_upper_firstRowCount = '$start_upper_firstRowCount',
                            start_upper_secondRowCount = '$start_upper_secondRowCount',
                            start_lower_diameter = '$start_lower_diameter',
                            start_lower_firstRowCount = '$start_lower_firstRowCount',
                            start_lower_secondRowCount = '$start_lower_secondRowCount',
                            start_rib_diameter = '$start_rib_diameter',
                            start_rib_count = '$start_rib_count',
                            start_rib_pitch = '$start_rib_pitch',

                            center_upper_diameter = '$center_upper_diameter',
                            center_upper_firstRowCount = '$center_upper_firstRowCount',
                            center_upper_secondRowCount = '$center_upper_secondRowCount',
                            center_lower_diameter = '$center_lower_diameter',
                            center_lower_firstRowCount = '$center_lower_firstRowCount',
                            center_lower_secondRowCount = '$center_lower_secondRowCount',
                            center_rib_diameter = '$center_rib_diameter',
                            center_rib_count = '$center_rib_count',
                            center_rib_pitch = '$center_rib_pitch',

                            end_upper_diameter = '$end_upper_diameter',
                            end_upper_firstRowCount = '$end_upper_firstRowCount',
                            end_upper_secondRowCount = '$end_upper_secondRowCount',
                            end_lower_diameter = '$end_lower_diameter',
                            end_lower_firstRowCount = '$end_lower_firstRowCount',
                            end_lower_secondRowCount = '$end_lower_secondRowCount',
                            end_rib_diameter = '$end_rib_diameter',
                            end_rib_count = '$end_rib_count',
                            end_rib_pitch = '$end_rib_pitch',
                            version_id = $version_id,
                            version_number = $version_number,
                            phase = '$phase'";

                 DB::insert($query);
 
                 //DB::insert("CALL beam_insert_procedure($item_id,$element_id,'$type_name','$material','$level',$volume,'$family_name','$workset',$version_id,$version_number)");
 
             }
 
        }catch(Exception $e){
            print_r($e->getMessage());
        }
 
    }

    public function SaveFoundationTekkin($save_list,$version_id,$item_id,$version_number){
        
        try{
           
             foreach($save_list as $data){
                $D = $data["D"];
                $H = $data["H"];
                $W = $data["W"];
                $level = $data["level"];
                $upper_X_diameter = $data["upper_X_diameter"];
                $upper_X_count = $data["upper_X_count"];
                $upper_Y_diameter = $data["upper_Y_diameter"];
                $upper_Y_count = $data["upper_Y_count"];
                $lower_X_diameter = $data["lower_X_diameter"];
                $lower_X_count = $data["lower_X_count"];
                $lower_Y_diameter = $data["lower_Y_diameter"];
                $lower_Y_count = $data["lower_Y_count"];
                $element_id = $data["element_id"];
                $phase = $data["phase"];
                $element_db_id = $data["element_db_id"];

                $query = "INSERT  INTO tb_forge_foundation_tekkin
                (id,item_id,element_id,element_db_id,D,H,W,level,upper_X_diameter,upper_X_count,upper_Y_diameter,upper_Y_count
                ,lower_X_diameter,lower_X_count,lower_Y_diameter,lower_Y_count,version_id,version_number,phase)
                SELECT COALESCE(MAX(id), 0) + 1,$item_id,$element_id,'$element_db_id','$D','$H','$W','$level','$upper_X_diameter','$upper_X_count','$upper_Y_diameter','$upper_Y_count'
                ,'$lower_X_diameter','$lower_X_count','$lower_Y_diameter','$lower_Y_count',$version_id,$version_number,'$phase' FROM tb_forge_foundation_tekkin
                ON DUPLICATE KEY UPDATE 
                element_db_id = '$element_db_id',
                D = '$D',
                H = '$H',
                W = '$W',
                level = '$level',
                upper_X_diameter ='$upper_X_diameter',
                upper_X_count = '$upper_X_count',
                upper_Y_diameter = '$upper_Y_diameter',
                upper_Y_count = '$upper_Y_count',
                lower_X_diameter = '$lower_X_diameter',
                lower_X_count = '$lower_X_count',
                lower_Y_diameter = '$lower_Y_diameter',
                lower_Y_count = '$lower_Y_count',
                version_id = $version_id,
                version_number = $version_number,
                phase = '$phase'";
 
                 DB::insert($query);
                 
             }
 
        }catch(Exception $e){
            print_r($e->getMessage());
        }
         
    }
}
