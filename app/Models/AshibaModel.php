<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class AshibaModel extends Model
{
    protected $table = '';
    
    // function GetForgeProjects(){
        
    //     $hierarchyArray=  array();
    //     $projectArray=  array();
    //     $versionArray =  array();
        
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
    //             //$count = 0;
    //             foreach($proObj as $project){
    //             //$count++;
    //             //if($count <= 275) continue;
    //             //if($count == 280)  break;
    //                 $proId = $project['id'];
    //                 $projectName = $project['attributes']['name'];
                    
                    
    //               echo "<script> alert($projectName);</script>";
                    
                
    //             }             
    //         }
    //         return "Hello";

            
            
    //     } catch (Exception $e) {
    //         //echo 'Exception when calling forge library function : ', $e->getMessage(), PHP_EOL;
    //     }
    // }
    
    public function GetProjects()
    {
        $query = "SELECT * FROM tb_project WHERE  id = 383 ";//id IN(SELECT project_id FROM tb_forge_item) for map display project remove
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }
    public function GetItems()
    {
        $query = "SELECT * FROM tb_forge_item WHERE  project_id=383 ";//id IN(SELECT project_id FROM tb_forge_item) for map display project remove
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }
    
    public function GetVersions(){
        $query = "SELECT item.name,vrs.* FROM tb_forge_version vrs 
                  LEFT JOIN tb_forge_item item ON item.id = vrs.item_id
                  LEFT JOIN tb_project as tp ON tp.id = item.project_id
                  WHERE tp.id = 383";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }
    
     public function GetVersionsByProject($project,$itemName)
    {
        $item_condition;
        if($itemName == ""){
            $item_condition = $itemName;
        }else{
            $item_condition = " AND item.name='$itemName'";
        }

        $query = "SELECT item.name,vrs.* FROM tb_forge_version vrs
                  LEFT JOIN  tb_forge_item item  ON item.id = vrs.item_id
                  LEFT JOIN tb_project tp ON tp.id = item.project_id
                  WHERE tp.name = '$project'".$item_condition."
                  ORDER BY item.name,vrs.version_number DESC";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }
    
    
    //重仮設取得
    public function GetKasetsuProjects(){
        $query = "SELECT * FROM tb_project WHERE  id = 400 ";//id IN(SELECT project_id FROM tb_forge_item) for map display project remove
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }
    
    public function GetKasetsuItems()
    {
        $query = "SELECT * FROM tb_forge_item WHERE  project_id=400 ";//id IN(SELECT project_id FROM tb_forge_item) for map display project remove
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }
  
    public function GetKasetsuVersions(){
        $query = "SELECT item.name,vrs.* FROM tb_forge_version vrs 
                  LEFT JOIN tb_forge_item item ON item.id = vrs.item_id
                  LEFT JOIN tb_project as tp ON tp.id = item.project_id
                  WHERE tp.id = 400";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }
    
   
    
}
