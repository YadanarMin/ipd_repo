<?php

namespace App\Http\Controllers;
use App\Models\AshibaModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel; 
use App\Exports\TekkinExport;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPWord;
use PHPWord_IOFactory;
class AshibaController extends Controller 
{
    function index()
    {
        return view('forge');
    }


    function GetData(Request $request){
         $message = $request->get('message');
         $ashibaModel = new AshibaModel();
         if($message == 'getAshibaData' || $message == 'getAshibaDataByProjectName'){
             $project = $ashibaModel->GetProjects();
             $items = $ashibaModel->GetItems();
             $versions = $ashibaModel->GetVersions();
             
             return array("projects"=>$project, "items"=>$items, "versions"=>$versions);
         }else if($message == "getAshibaDataVersionByProjectAndItem"){
             $projectName = $request->get('projectName');
             $itemName = $request->get('itemName');
             $versions = $ashibaModel->GetVersionsByProject($projectName,$itemName);
             return array( "versions"=>$versions);
         }
         
        
    }
    
    //重仮設取得(2021.03.21)
    //url('/kasetsu/getData')
    function GetKasetsuData(Request $request){
        $message = $request->get('message');
        $ashibaModel = new AshibaModel();
        if($message == 'getKasetsuData' || $message == 'getKasetsuDataByProjectName'){
            $project = $ashibaModel->GetKasetsuProjects();
            $items = $ashibaModel->GetKasetsuItems();
            $versions = $ashibaModel->GetKasetsuVersions();
             
            return array("projects"=>$project, "items"=>$items, "versions"=>$versions);
        }
    }
    
    // function ShowProjectInfo(){
        
    //     $conf = new \Autodesk\Auth\Configuration();//escape from current name space by using '/'
    //     $conf->getDefaultConfiguration()
    //     ->setClientId('1JdTGvw0dhm50GbMqfnocubhm5D70P1X')
    //     ->setClientSecret('eMqGzyufSlyBiChD');//fmori of client_id and secret_key
    //     // ->setClientId('J0jduCzdsYAbKXqsidxCBt3aWpW5DNv0')
    //     //->setClientSecret('Hp8X9pxKgYjqJYGE');//bim360local App

    //     $authObj = new \Autodesk\Auth\OAuth2\TwoLeggedAuth();
    //     $scopes = array("code:all","data:read","data:write","bucket:read");
    //     $authObj->setScopes($scopes);

    //     $authObj->fetchToken();
    //     $token = $authObj->getAccessToken();
    //     $_SESSION['token'] = $token;
    //     //get Hubs
    //     $hubInstance = new \Autodesk\Forge\Client\Api\HubsApi($authObj);
    //     try {

    //         $index =0;
    //         $hubs = $hubInstance->getHubs(null, null);
    //         $hubObj = $hubs['data'];
            
    //         foreach($hubObj as $hub){
    //             $hubId = $hub['id'];
    //             $hubName = $hub['attributes']['name'];
    //             if($hubName == "OBAYASHI")continue;
                          
    //             $authObj = new \Autodesk\Auth\OAuth2\TwoLeggedAuth();
    //             $scopes = array("code:all","data:read","data:write","bucket:read");
    //             $authObj->setScopes($scopes);
        
    //             $authObj->fetchToken();
    //             $token = $authObj->getAccessToken();
    //             $_SESSION['token'] = $token;
                
    //             $projectInstance = new \Autodesk\Forge\Client\Api\ProjectsApi($authObj);
    //             $folderIns = new \Autodesk\Forge\Client\Api\FoldersApi($authObj);
    //             $itemIns = new \Autodesk\Forge\Client\Api\ItemsApi($authObj);
    //             $apiInstance = new \Autodesk\Forge\Client\Api\VersionsApi($authObj);
                
    //             $projects = $projectInstance->getHubProjects($hubId, null, null);                
    //             $proObj = $projects['data'];
    //             unset($projects);
    //             clearstatcache();
    //             gc_collect_cycles();
                
                
    //             //Project
    //             foreach($proObj as $project){
    //                 $proId = $project['id'];
    //                 $projectName = $project['attributes']['name'];
    //                 if($projectName == "SA00000003_サンプルモデル03_2019"){
                        
    //                     $topFolders = $projectInstance->getProjectTopFolders($hubId, $proId);
    //                     $topFolderData = $topFolders['data'];
                        
    //                         //Folder
    //                         foreach($topFolderData as $topfolder){  
    //                              $topFolderId = $topfolder['id'];
    //                              $folderName = $topfolder['attributes']['display_name'];
        
    //                              if($folderName == "Shared" || $folderName == "Consumed")continue; 
                                 
    //                              if($topFolderId == "urn:adsk.wipprod:fs.folder:co.YRixEXQLQ_GEnk4BvzRMEw"){
    //                                 $items = $folderIns->getFolderContents($proId, $topFolderId, null, null, null, null,null);
                                 
    //                                  $itemsData = $items['data']; 
    //                                  unset($items);
    //                                  clearstatcache();
    //                                  gc_collect_cycles();
                                     
    //                                  //Items
    //                                  foreach($itemsData as $item){
    //                                     $itemArray=array();
                                
    //                                     if($item['type'] == "folders"){
    //                                         if($item['attributes']['display_name'] == "Shared" || $item['attributes']['display_name'] == "Consumed")continue;   
                                           
    //                                         $folderId = $item['id'];
    //                                         $tempData = $folderIns->getFolderContents($proId, $folderId, null, null, null, null,null);
    //                                         $data = $tempData['data'];
                                            
    //                                         //Check items or folder
    //                                         foreach($data as $d){
    //                                             if($d['type'] == "items"){
    //                                                 $itemName = $d['attributes']['display_name'];
    //                                                 if(strstr($itemName, '.rvt') == true)
    //                                                 {
    //                                                     $itemId = $d['id']; 
    //                                                     $itemArray[$itemName] = array("itemId"=>$itemId,"projectName"=>$projectName);
    //                                                 }
    //                                             }
    //                                         }
    //                                          if(sizeof($itemArray) > 0){
    //                                             foreach($itemArray as $key=>$item){
    //                                                 $itemName = $key;
    //                                                 $itemId = $item["itemId"];
    //                                                 $versions = $itemIns->getItemVersions($proId, $itemId, null, null, null, null, null, null);
    //                                                 $allVersion = $versions['data'];
    //                                                 echo "<pre>";
    //                                                 print_r($itemArray);
    //                                                 print_r($allVersion);
    //                                             }
    //                                          }
    //                                     }
    //                                  }
    //                              }
                                 
    //                         }
    //                 }
    //             }
    //         }
    //     }catch(Exception $e){
            
    //     }
    // }

    
    
    

    
    
    
   
    

}
