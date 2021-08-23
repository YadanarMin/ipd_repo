<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;

class AutoSaveForgeProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forge:save';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->GetForgeProjects();
         //DB::table('tb_project')->delete();
    }
    
    function GetForgeProjects(){
        
        $hierarchyArray=  array();
        $projectArray=  array();
        $versionArray =  array();
        $fix_item_array = array("#cen_meieki4_2019.rvt","cen_NN_2019_v1.rvt","cen_jyosai_2019.rvt","中央_岡波病院_2019.rvt","cen_kounan2_2019.rvt");
        $fix_project_array = array("名駅4丁目PJ","日本生命新潟ビル","城西大学紀尾井町2号館建替計画","岡波病院","港南二丁目プロジェクト","（仮称）青葉通一番町計画");
        
        $conf = new \Autodesk\Auth\Configuration();//escape from current name space by using '/'
        $conf->getDefaultConfiguration()
        ->setClientId('1JdTGvw0dhm50GbMqfnocubhm5D70P1X')
        ->setClientSecret('eMqGzyufSlyBiChD');//fmori of client_id and secret_key
        // ->setClientId('J0jduCzdsYAbKXqsidxCBt3aWpW5DNv0')
        //->setClientSecret('Hp8X9pxKgYjqJYGE');//bim360local App

        $authObj = new \Autodesk\Auth\OAuth2\TwoLeggedAuth();
        $scopes = array("code:all","data:read","data:write","bucket:read");
        $authObj->setScopes($scopes);

        $authObj->fetchToken();
        $token = $authObj->getAccessToken();
        $_SESSION['token'] = $token;
        //get Hubs
        $hubInstance = new \Autodesk\Forge\Client\Api\HubsApi($authObj);
        try {

            $index =0;
            $hubs = $hubInstance->getHubs(null, null);
            $hubObj = $hubs['data'];

            $dbProjects = $this->GetAutoSaveProjects();
            $autoSaveProjects = array_column($dbProjects,"project_name");//array value by given name   

            foreach($hubObj as $hub){
                $hubId = $hub['id'];
                $hubName = $hub['attributes']['name'];
                if($hubName == "OBAYASHI")continue;
                          
                $authObj = new \Autodesk\Auth\OAuth2\TwoLeggedAuth();
                $scopes = array("code:all","data:read","data:write","bucket:read");
                $authObj->setScopes($scopes);
        
                $authObj->fetchToken();
                $token = $authObj->getAccessToken();
                $_SESSION['token'] = $token;
                
                $projectInstance = new \Autodesk\Forge\Client\Api\ProjectsApi($authObj);
                $folderIns = new \Autodesk\Forge\Client\Api\FoldersApi($authObj);
                $itemIns = new \Autodesk\Forge\Client\Api\ItemsApi($authObj);
                $apiInstance = new \Autodesk\Forge\Client\Api\VersionsApi($authObj);
                
                $projects = $projectInstance->getHubProjects($hubId, null, null);                
                $proObj = $projects['data'];
                unset($projects);
                clearstatcache();
                gc_collect_cycles();
                //$count = 0;
                foreach($proObj as $project){
                //$count++;
                //if($count <= 275) continue;
                //if($count == 280)  break;
                    $proId = $project['id'];
                    $projectName = $project['attributes']['name'];
                    //if(!in_array($projectName,$fix_project_array)) continue;
                    //if(strstr($projectName,"博多") == false)continue;(strstr($itemName,"cen")== true || strstr($itemName,"asb")== true)
                   
                    $this->SaveProject($projectName,$proId);//save project
                    
                    if(!in_array($projectName,$autoSaveProjects))continue;
                    /*echo"<pre>";
                    print_r($relatedProjects);
                    echo"</pre>";return;*/
                    //$hierarchyArray[$projectName] = array("hubId"=>$hubId,"projectId"=>$proId);

                    $topFolders = $projectInstance->getProjectTopFolders($hubId, $proId);
                    $topFolderData = $topFolders['data'];            
                     foreach($topFolderData as $topfolder){  
                         $topFolderId = $topfolder['id'];
                         $folderName = $topfolder['attributes']['display_name'];

                         if($folderName == "Shared" || $folderName == "Consumed")continue;   
                         $items = $folderIns->getFolderContents($proId, $topFolderId, null, null, null, null,null);
                         
                         $itemsData = $items['data']; 
                         unset($items);
                         clearstatcache();
                         gc_collect_cycles();
                         
                         foreach($itemsData as $item){
                            $itemArray=array();
                           // print_r($item['attributes']['display_name']."=>".$item['type']."\n");
                            if($item['type'] == "folders"){
                                if($item['attributes']['display_name'] == "Shared" || $item['attributes']['display_name'] == "Consumed")continue;   
                               
                                $folderId = $item['id'];
                                $tempData = $folderIns->getFolderContents($proId, $folderId, null, null, null, null,null);
                                $data = $tempData['data'];
                                unset($tempData);
                                clearstatcache();
                                gc_collect_cycles();
                                foreach($data as $d){                           
                                    if($d['type'] == "folders"){
                                        if($d['attributes']['display_name'] == "Shared" || $d['attributes']['display_name'] == "Consumed")continue;   
                                        $folderId2 = $d['id'];
                                        $tempData2 = $folderIns->getFolderContents($proId, $folderId2, null, null, null, null,null);
                                        $data2 = $tempData2['data'];
                                        unset($tempData2);
                                        clearstatcache();
                                        gc_collect_cycles();
                                        foreach($data2 as $d2){
                                            if($d2['type'] == "folders"){
                                                if($d2['display_name'] == "Shared" || $d2['attributes']['display_name'] == "Consumed")continue;   
                                                $folderId3 = $d2['id'];

                                                $tempData3 = $folderIns->getFolderContents($proId, $folderId3, null, null, null, null,null);
                                            
                                                $data3 = $tempData3['data'];
                                                unset($tempData3);
                                                clearstatcache();
                                                gc_collect_cycles();
                                                foreach($data3 as $d3){
                                                    if($d3['type'] == "items"){
                                                        $itemName = $d3['attributes']['display_name'];
                                                        if((in_array($projectName,$fix_project_array) && in_array($itemName, $fix_item_array)) 
                                                            || (!in_array($projectName,$fix_project_array) && strpos($itemName, '.rvt') == true && strstr($itemName,"cen") == true)){
                                                            $itemId = $d3['id']; 
                                                            $itemArray[$itemName] = array("itemId"=>$itemId,"projectName"=>$projectName);
                                                        }                                                      
                                                   }
                                                }
                                            }else if ($d2['type'] == "items"){
                                                $itemName = $d2['attributes']['display_name'];
                                                if((in_array($projectName,$fix_project_array) && in_array($itemName, $fix_item_array)) 
                                                    || (!in_array($projectName,$fix_project_array) && strpos($itemName, '.rvt') == true && strstr($itemName,"cen") == true)){
                                                    $itemId = $d2['id']; 
                                                    $itemArray[$itemName] = array("itemId"=>$itemId,"projectName"=>$projectName);
                                                }
                                                
                                            }
                                        }
                                    }else if($d['type'] == "items"){
                                        $itemName = $d['attributes']['display_name'];
                                        if((in_array($projectName,$fix_project_array) && in_array($itemName, $fix_item_array)) 
                                            || (!in_array($projectName,$fix_project_array) && strpos($itemName, '.rvt') == true && strstr($itemName,"cen") == true)){
                                            $itemId = $d['id']; 
                                            $itemArray[$itemName] = array("itemId"=>$itemId,"projectName"=>$projectName);
                                        }
                                        
                                    }
                                }
                                                            
                            }else if($item['type'] == "items"){
    
                                $itemName = $item['attributes']['display_name'];                              
                                if((in_array($projectName,$fix_project_array) && in_array($itemName, $fix_item_array)) 
                                    || (!in_array($projectName,$fix_project_array) && strpos($itemName, '.rvt') == true && strstr($itemName,"cen") == true)){
                                    $itemArray[$itemName] = array("itemId"=>$itemId,"projectName"=>$projectName);
                                }                       
                            } 

                            if(sizeof($itemArray) > 0){
                                
                                $this->SaveItem($itemArray);//save item to Database
                                foreach($itemArray as $key=>$item){
                                    $itemName = $key;
                                    $itemId = $item["itemId"];
                                    $versions = $itemIns->getItemVersions($proId, $itemId, null, null, null, null, null, null);
                                    $allVersion = $versions['data'];
                                    $versionArray= array();
                                    if(sizeof($allVersion) <= 1)continue;//skip if just one version
                                    foreach($allVersion as $version){ 
                                        $docVersion = $version['attributes']['version_number'];   
                                        $versionId = $version['id'];
                                        $time = $version['attributes']['updated_time'];
                                        $arr = explode('T',$time);
                                        $updated_time = $arr[0];
                                        $storageSize = empty($version['attributes']['storage_size'])? 0 : $version['attributes']['storage_size'] ;                                                    
                                        $versionArray[$versionId]= array("itemName"=>$itemName,"versionNumber"=>$docVersion,"storageSize"=>$storageSize,"updated_time"=>$updated_time);                           
                                    }
                                    if(sizeof($versionArray) > 0){
                                        $this->SaveVersion($versionArray);
                                    }
                                }
                            }
                         unset($itemArray);
                         clearstatcache();
                         gc_collect_cycles();
                        }   

                    }  
                }             
            }

            
            //save project kouji information
            $this->SaveProjectInfomation();
            
            
            //kosaka
            //$this->SaveForgeProjectInfomation();
            
        } catch (Exception $e) {
            //echo 'Exception when calling forge library function : ', $e->getMessage(), PHP_EOL;
        }
    }

    function SaveProjectInfomation(){
        try{
            $conf = new \Autodesk\Auth\Configuration();//escape from current name space by using '/'
            $conf->getDefaultConfiguration()
            ->setClientId('Mt1Tul68redoV5OEMKwRh1aYQnsdmtJW')
            ->setClientSecret('8FOuOTPK6nOp4bOl');
            
            $authObj = new \Autodesk\Auth\OAuth2\TwoLeggedAuth();
            $scopes = array("code:all","data:read","data:write","bucket:read");
            $authObj->setScopes($scopes);
    
            $authObj->fetchToken();
            $access_token = $authObj->getAccessToken();
            $authObj->setAccessToken($access_token);
            $derivInst = new \Autodesk\Forge\Client\Api\DerivativesApi($authObj);
            
            $project_urns = $this->GetAutoSaveProjectFolderUrns();
            //print_r($project_urns);return;
            foreach($project_urns as $project_urn){
                
                $urn = $project_urn["version_urn"];
                $item_name = $project_urn["project_name"];
                
                $version_number = $project_urn["version_number"];
                $metaDataObj = $derivInst->getMetadata(base64_encode($urn),null);
                if(empty($metaDataObj["data"]["metadata"]))continue;
                $metaData = $metaDataObj["data"]["metadata"];
                
                unset($metaDataObj);
                clearstatcache();
                gc_collect_cycles();
                $guid = "";
                foreach($metaData as $mData){
                     $viewName = $mData["name"];
                     if(strpos($viewName,'新しい建設') === false)continue;
                     $guid = $mData["guid"];
                }
                
                
                if($guid == "")continue;
                
                $properties = $derivInst->getModelviewPropertiesByObjectId(base64_encode($urn),$guid,'1',null);
                //$data = json_decode(json_encode($properties),true);
                $allProperties = isset($properties['data']['collection']) ?$properties['data']['collection'] :null; 
                
                unset($properties);
                clearstatcache();
                gc_collect_cycles();
                //if(strstr($item_name,"ミツトヨ") == false)continue;
               /*echo"<pre>";
                print_r($allProperties);
                echo"</pre>";return;*/
                if($allProperties !== null){
    
                    foreach($allProperties as $property){
                        //yadanar save tb_document
                        if(isset($property['objectid']) || $property['name'] != "Model") {
                            $model_property = json_decode(json_encode($property["properties"]),true);//$property["properties"];
                            if(!isset( $model_property["その他"]))continue;
                            $other = $model_property["その他"];
                            $kouji_name = "";
                            $client_name = "";
                            $address = "";
                            
                            if(isset($other['プロジェクト名']))
                                $kouji_name = is_array($other['プロジェクト名']) ? implode(',',$other['プロジェクト名']) : $other['プロジェクト名'];
                            if(isset($other['クライアント名']))
                                $client_name = is_array(isset($other['クライアント名'])) ? implode(',',isset($other['クライアント名'])) : isset($other['クライアント名']);
                            if(isset($other['計画地住所']))
                                $address = is_array(isset($other['計画地住所'])) ? implode(',',isset($other['計画地住所'])) : isset($other['計画地住所']);
                            
                            //echo $item_name."---------".$kouji_name."----------".$client_name."----------".$address."----------".$version_number;return;
                            $query = "INSERT INTO tb_document(id,name,koujimeisho,hachuusha,sekoubasho,version)
                                    SELECT COALESCE(MAX(id), 0) + 1,'$item_name','$kouji_name','$client_name','$address',$version_number FROM tb_document
                                    ON DUPLICATE KEY UPDATE koujimeisho = '$kouji_name',hachuusha = '$client_name',sekoubasho = '$address',version = $version_number";
                            DB::insert($query);
                        }
                         
                        //kosaka save tb_forge_project_info
                        if(isset($property['objectid']) || $property['name'] != "Model") {
                        $model_property = json_decode(json_encode($property["properties"]),true);//$property["properties"];
                        if(!isset( $model_property["その他"]))continue;
                        $other = $model_property["その他"];
                        
                        $project_title = "";
                        $project_number = "";
                        $address = "";
                        $client_name = "";
                        
                        if(isset($other['プロジェクト名']))
                            $project_title = is_array($other['プロジェクト名']) ? implode(',',$other['プロジェクト名']): $other['プロジェクト名'];
                        if(isset($other['プロジェクト番号'])) 
                            $project_number = is_array($other['プロジェクト番号']) ? implode(',',$other['プロジェクト番号']): $other['プロジェクト番号'];
                        if(isset($other['計画地住所']))
                            $address = is_array($other['計画地住所']) ? implode(',',$other['計画地住所']): "";
                        if(isset($other['クライアント名']))
                            $client_name = is_array($other['クライアント名']) ? implode(',',$other['クライアント名']): $other['クライアント名'];
                        
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
                        if(isset($model_property["データ"])){
                            $data = $model_property["データ"];
                            if(isset($data['PJ_建築士構造1_']))
                                $struct_architect1 = is_array($data['PJ_建築士構造1_']) ? implode(',',$data['PJ_建築士構造1_']): $data['PJ_建築士構造1_'];
                            if(isset($data['PJ_建築士構造2_']))
                                $struct_architect2 = is_array($data['PJ_建築士構造2_']) ? implode(',',$data['PJ_建築士構造2_']): $data['PJ_建築士構造2_'];
                            if(isset($data['PJ_建築士構造3_']))
                                $struct_architect3 = is_array($data['PJ_建築士構造3_']) ? implode(',',$data['PJ_建築士構造3_']): $data['PJ_建築士構造3_'];
                            if(isset($data['PJ_建築士構造4_']))
                                $struct_architect4 = is_array($data['PJ_建築士構造4_']) ? implode(',',$data['PJ_建築士構造4_']): $data['PJ_建築士構造4_'];
                            if(isset($data['PJ_建築士構造5_']))
                                $struct_architect5 = is_array($data['PJ_建築士構造5_']) ? implode(',',$data['PJ_建築士構造5_']): $data['PJ_建築士構造5_'];
                            if(isset($data['PJ_建築士構造6_']))    
                                $struct_architect6 = is_array($data['PJ_建築士構造6_']) ? implode(',',$data['PJ_建築士構造6_']): $data['PJ_建築士構造6_'];
                            if(isset($data['PJ_建築士意匠1_']))
                                $design_architect1 = is_array($data['PJ_建築士意匠1_']) ? implode(',',$data['PJ_建築士意匠1_']): $data['PJ_建築士意匠1_'];
                            if(isset($data['PJ_建築士意匠2_']))
                                $design_architect2 = is_array($data['PJ_建築士意匠2_']) ? implode(',',$data['PJ_建築士意匠2_']): $data['PJ_建築士意匠2_'];
                            if(isset($data['PJ_建築士意匠3_']))
                                $design_architect3 = is_array($data['PJ_建築士意匠3_']) ? implode(',',$data['PJ_建築士意匠3_']): $data['PJ_建築士意匠3_'];
                            if(isset($data['PJ_建築士意匠4_']))
                                $design_architect4 = is_array($data['PJ_建築士意匠4_']) ? implode(',',$data['PJ_建築士意匠4_']): $data['PJ_建築士意匠4_'];
                            if(isset($data['PJ_建築士意匠5_']))
                                $design_architect5 = is_array($data['PJ_建築士意匠5_']) ? implode(',',$data['PJ_建築士意匠5_']): $data['PJ_建築士意匠5_'];
                            if(isset($data['PJ_建築士意匠6_']))
                                $design_architect6 = is_array($data['PJ_建築士意匠6_']) ? implode(',',$data['PJ_建築士意匠6_']): $data['PJ_建築士意匠6_'];
                            if(isset($data['PJ_建築士設備1_']))
                                $facility_architect = is_array($data['PJ_建築士設備1_']) ? implode(',',$data['PJ_建築士設備1_']): $data['PJ_建築士設備1_'];
                        }
    
                        //echo $item_name."---------".$project_title."----------".$project_number."----------".$address."----------".$client_name;
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
                        DB::insert($query);
                        
                        //save project_name into tb_bimactionplan
                        $query = "INSERT IGNORE INTO tb_bimactionplan (project_name) VAlUES('$item_name')";
                        DB::insert($query);
                    }
                    
                    }
                
                }
                
                unset($allProperties);
                clearstatcache();
                gc_collect_cycles();
            }
            
           
        }catch(Exception $e){
            //echo 'Exception when project info save : ', $e->getMessage(), PHP_EOL;
        }
    }

    function GetAutoSaveProjectFolderUrns(){
        $query = "SELECT  result.*, (select (forge_version_id) from tb_forge_version where id = result.lated_id) as version_urn 
                    FROM 
                    (SELECT (temp.name) as project_name,(tv.id) as lated_id,(tv.version_number)as version_number FROM 
                        (SELECT ti.id as item_id,tp.name
                            FROM tb_project tp
                            LEFT JOIN tb_forge_item  ti ON ti.project_id = tp.id
                            WHERE tp.auto_save_properties = 1
                            GROUP BY tp.id) 
                            as temp
                        LEFT JOIN tb_forge_version tv ON tv.item_id = temp.item_id
                        ORDER BY temp.name,tv.version_number DESC)
                    as result
                    GROUP BY result.project_name";       
        $result = DB::select($query);
        return json_decode(json_encode($result),true);//change array object to array
    }
    
    function SaveProject($projectName,$projectId){
        $query = "INSERT IGNORE INTO tb_project(id,name,forge_project_id) 
                  SELECT MAX(id) +1,'$projectName','$projectId' FROM tb_project";//IGNORE when key duplicate
        DB::insert($query);
    }

    function SaveItem($itemArray){
        foreach($itemArray as $key=>$item){
            $itemName = $key;
            $itemId = $item["itemId"];
            $projectName = $item["projectName"];
            $query = "INSERT IGNORE INTO tb_forge_item(id,name,project_id,forge_item_id) 
                        SELECT MAX(id) +1,'$itemName',(SELECT id FROM tb_project WHERE name ='$projectName'),'$itemId' FROM tb_forge_item";//IGNORE when key duplicate
            DB::insert($query);
        }
            
    }

    function SaveVersion($versionArray){
        foreach($versionArray as $key=>$version){
            $versionId = $key;
            $versionNumber = $version["versionNumber"];
            $storageSize = $version["storageSize"];
            $updated_time = $version["updated_time"];
            $itemName = $version["itemName"];
            $query = "INSERT  INTO tb_forge_version(id,item_id,forge_version_id,version_number,storage_size,updated_time) 
                        SELECT MAX(id) +1,(SELECT id FROM tb_forge_item WHERE name ='$itemName'),'$versionId',$versionNumber,$storageSize,'$updated_time' FROM tb_forge_version
                        ON DUPLICATE KEY UPDATE updated_time = '$updated_time'";//IGNORE when key duplicate
            DB::insert($query);
        }
            
    }

    function GetAutoSaveProjects(){
        $query = "SELECT DISTINCT(name) as project_name FROM tb_project WHERE auto_save_properties = 1";       
        $result = DB::select($query);
        return json_decode(json_encode($result),true);//change array object to array
    }
    
    
    /*function SaveForgeProjectInfomation(){
        try{
            $conf = new \Autodesk\Auth\Configuration();
            $conf->getDefaultConfiguration()
            ->setClientId('Mt1Tul68redoV5OEMKwRh1aYQnsdmtJW')
            ->setClientSecret('8FOuOTPK6nOp4bOl');
            
            $authObj = new \Autodesk\Auth\OAuth2\TwoLeggedAuth();
            $scopes = array("code:all","data:read","data:write","bucket:read");
            $authObj->setScopes($scopes);
    
            $authObj->fetchToken();
            $access_token = $authObj->getAccessToken();
            $authObj->setAccessToken($access_token);
            $derivInst = new \Autodesk\Forge\Client\Api\DerivativesApi($authObj);
            
            $project_urns = $this->GetAutoSaveProjectFolderUrns();
            //print_r($project_urns);return;
            foreach($project_urns as $project_urn){
                
                $urn = $project_urn["version_urn"];
                $item_name = $project_urn["project_name"];
                
                $version_number = $project_urn["version_number"];
                $metaDataObj = $derivInst->getMetadata(base64_encode($urn),null);
                if(empty($metaDataObj["data"]["metadata"]))continue;
                $metaData = $metaDataObj["data"]["metadata"];
                
                unset($metaDataObj);
                clearstatcache();
                gc_collect_cycles();
                $guid = "";
                foreach($metaData as $mData){
                     $viewName = $mData["name"];
                     if(strpos($viewName,'新しい建設') === false)continue;
                     $guid = $mData["guid"];
                }
                
                
                if($guid == "")continue;
                
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
                        if(!isset( $model_property["その他"]))continue;
                        $other = $model_property["その他"];
                        
                        $project_title = "";
                        $project_number = "";
                        $address = "";
                        $client_name = "";
                        
                        if(isset($other['プロジェクト名']))
                            $project_title = is_array($other['プロジェクト名']) ? implode(',',$other['プロジェクト名']): $other['プロジェクト名'];
                        if(isset($other['プロジェクト番号'])) 
                            $project_number = is_array($other['プロジェクト番号']) ? implode(',',$other['プロジェクト番号']): $other['プロジェクト番号'];
                        if(isset($other['計画地住所']))
                            $address = is_array($other['計画地住所']) ? implode(',',$other['計画地住所']): "";
                        if(isset($other['クライアント名']))
                            $client_name = is_array($other['クライアント名']) ? implode(',',$other['クライアント名']): $other['クライアント名'];
                        
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
                        if(isset($model_property["データ"])){
                            $data = $model_property["データ"];
                            if(isset($data['PJ_建築士構造1_']))
                                $struct_architect1 = is_array($data['PJ_建築士構造1_']) ? implode(',',$data['PJ_建築士構造1_']): $data['PJ_建築士構造1_'];
                            if(isset($data['PJ_建築士構造2_']))
                                $struct_architect2 = is_array($data['PJ_建築士構造2_']) ? implode(',',$data['PJ_建築士構造2_']): $data['PJ_建築士構造2_'];
                            if(isset($data['PJ_建築士構造3_']))
                                $struct_architect3 = is_array($data['PJ_建築士構造3_']) ? implode(',',$data['PJ_建築士構造3_']): $data['PJ_建築士構造3_'];
                            if(isset($data['PJ_建築士構造4_']))
                                $struct_architect4 = is_array($data['PJ_建築士構造4_']) ? implode(',',$data['PJ_建築士構造4_']): $data['PJ_建築士構造4_'];
                            if(isset($data['PJ_建築士構造5_']))
                                $struct_architect5 = is_array($data['PJ_建築士構造5_']) ? implode(',',$data['PJ_建築士構造5_']): $data['PJ_建築士構造5_'];
                            if(isset($data['PJ_建築士構造6_']))    
                                $struct_architect6 = is_array($data['PJ_建築士構造6_']) ? implode(',',$data['PJ_建築士構造6_']): $data['PJ_建築士構造6_'];
                            if(isset($data['PJ_建築士意匠1_']))
                                $design_architect1 = is_array($data['PJ_建築士意匠1_']) ? implode(',',$data['PJ_建築士意匠1_']): $data['PJ_建築士意匠1_'];
                            if(isset($data['PJ_建築士意匠2_']))
                                $design_architect2 = is_array($data['PJ_建築士意匠2_']) ? implode(',',$data['PJ_建築士意匠2_']): $data['PJ_建築士意匠2_'];
                            if(isset($data['PJ_建築士意匠3_']))
                                $design_architect3 = is_array($data['PJ_建築士意匠3_']) ? implode(',',$data['PJ_建築士意匠3_']): $data['PJ_建築士意匠3_'];
                            if(isset($data['PJ_建築士意匠4_']))
                                $design_architect4 = is_array($data['PJ_建築士意匠4_']) ? implode(',',$data['PJ_建築士意匠4_']): $data['PJ_建築士意匠4_'];
                            if(isset($data['PJ_建築士意匠5_']))
                                $design_architect5 = is_array($data['PJ_建築士意匠5_']) ? implode(',',$data['PJ_建築士意匠5_']): $data['PJ_建築士意匠5_'];
                            if(isset($data['PJ_建築士意匠6_']))
                                $design_architect6 = is_array($data['PJ_建築士意匠6_']) ? implode(',',$data['PJ_建築士意匠6_']): $data['PJ_建築士意匠6_'];
                            if(isset($data['PJ_建築士設備1_']))
                                $facility_architect = is_array($data['PJ_建築士設備1_']) ? implode(',',$data['PJ_建築士設備1_']): $data['PJ_建築士設備1_'];
                        }
    
                        //echo $item_name."---------".$project_title."----------".$project_number."----------".$address."----------".$client_name;
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
                        DB::insert($query);
                        
                        //save project_name into tb_bimactionplan
                        $query = "INSERT IGNORE INTO tb_bimactionplan (project_name) VAlUES('$item_name')";
                        DB::insert($query);
                    }

                    //break;
                }

                unset($allProperties);
                clearstatcache();
                gc_collect_cycles();
                }
            }
            
           
        }catch(Exception $e){
            echo 'Exception when project info save : ', $e->getMessage(), PHP_EOL;
        }
        
    }*/
}
