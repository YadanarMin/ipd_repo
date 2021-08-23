<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;

class AutoBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forge:auto_backup';

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
        $this->AutoBackup();
         //DB::table('tb_project')->delete();
    }
    
   function AutoBackup(){
        try{
            //two legged authentication 
            $conf = new \Autodesk\Auth\Configuration();//escape from current name space by using '/'
            $conf->getDefaultConfiguration()
            ->setClientId('1JdTGvw0dhm50GbMqfnocubhm5D70P1X')
            ->setClientSecret('eMqGzyufSlyBiChD');//fmori of client_id and secret_key
            $twoLeggedAuth = new \Autodesk\Auth\OAuth2\TwoLeggedAuth();
            $scopes = array("code:all","data:read","data:write","bucket:read");
            $twoLeggedAuth->setScopes($scopes);
        
            $twoLeggedAuth->fetchToken();
            $token = $twoLeggedAuth->getAccessToken();
            $projectInstance = new \Autodesk\Forge\Client\Api\ProjectsApi($twoLeggedAuth);
            $folderIns = new \Autodesk\Forge\Client\Api\FoldersApi($twoLeggedAuth);
            $itemIns = new \Autodesk\Forge\Client\Api\ItemsApi($twoLeggedAuth);
            $versionInstance = new \Autodesk\Forge\Client\Api\VersionsApi($twoLeggedAuth);
            $apiInstance = new \Autodesk\Forge\Client\Api\ObjectsApi($twoLeggedAuth); 
            $s3 = new \Aws\S3\S3Client(['region'  => 'ap-northeast-1','version' => 'latest']);
            
            $backupProjects = $this->GetAutoBackupProjects();
            
            
            $hubInstance = new \Autodesk\Forge\Client\Api\HubsApi($twoLeggedAuth);
            $hubs = $hubInstance->getHubs(null, null);
            $hubObj = $hubs['data'];
            foreach($hubObj as $hub){
                $hubId = $hub['id'];
                $hubName = $hub['attributes']['name'];
                if($hubName == "OBAYASHI")continue;

                foreach($backupProjects as $project){
                    $proId = $project["forge_project_id"];
                    $topFolders = $projectInstance->getProjectTopFolders($hubId, $proId);
                    $topFolderData = $topFolders['data'];            
                     foreach($topFolderData as $topfolder){  
                         $topFolderId = $topfolder['id'];
                         $folderName = $topfolder['attributes']['display_name'];
                         if($folderName == "Shared" || $folderName == "Consumed")continue;   
                         $items = $folderIns->getFolderContents($proId, $topFolderId, null, null, null, null,null);
                         $itemsData = $items['data']; 
                        
                         foreach($itemsData as $item){
                            if($item['type'] == "folders"){
                                if($item['attributes']['display_name'] == "Shared" || $item['attributes']['display_name'] == "Consumed")continue;   
                                $itemArray=array();
                                $folderId = $item['id'];
                                $tempData = $folderIns->getFolderContents($proId, $folderId, null, null, null, null,null);
                                $data = $tempData['data'];                   
                                foreach($data as $d){                           
                                    if($d['type'] == "folders"){
                                        if($d['attributes']['display_name'] == "Shared" || $d['attributes']['display_name'] == "Consumed")continue;   
                                        $folderId2 = $d['id'];
                                        $tempData2 = $folderIns->getFolderContents($proId, $folderId2, null, null, null, null,null);
                                        $data2 = $tempData2['data'];
                                        foreach($data2 as $d2){
                                            if($d2['type'] == "folders"){
                                                $folderId3 = $d2['id'];
                                                $tempData3 = $folderIns->getFolderContents($proId, $folderId3, null, null, null, null,null);
                                                $data3 = $tempData3['data'];
                                                foreach($data3 as $d3){
                                                    if($d3['type'] == "items"){
                                                        array_push($itemArray,$d3);
                                                    }
                                                }
                                            }else if ($d2['type'] == "items"){
                                                array_push($itemArray,$d2);
                                            }
                                        }
                                    }else if($d['type'] == "items"){
                                        array_push($itemArray,$d);
                                    }
                                }
                                
                                if(sizeof($itemArray) > 0){
                                    foreach($itemArray as $item){                              
                                        $itemName = $item['attributes']['display_name'];
                                        if(strpos($itemName, '.rvt') == false || !preg_match('/cen/',$itemName))continue;
    
                                        $itemId = $item['id'];     
                                        $versions = $itemIns->getItemVersions($proId, $itemId, null, null, null, null, null, null);
                                        $allVersion = $versions['data'];
                                        
                                        foreach($allVersion as $version){  
                                            $docVersion = $version['attributes']['version_number'];
                                            if($docVersion != sizeof($allVersion))continue;
    
                                            $versionId = $version['id']; 
                                            $docName = $version['attributes']['display_name']; 
                                           
                                            //$version = $versionInstance->getVersion($proId, $versionId);
                                            $storage = $version["relationships"]["storage"];
                                            $storageId = $storage["data"]["id"];
    
                                            $temArr = explode(':',$storageId);
                                            $tempStr = end($temArr);
                                            $storArr = explode('/',$tempStr);
                                            $bucket_key = $storArr[0];//"wip.dm.prod"; // string | URL-encoded bucket key
                                            $object_name = $storArr[1];//"c8c40db2-e039-4826-a1be-cd5787687320.rvt";
                          
                                            $result = $apiInstance->getObject($bucket_key,$object_name,null, null, null,null);  
                                           
                                            $fullPath = $result->getPathname();
    
                                            $newPath = "/var/www/html/iPD/public/Download/".$docName;             
                                            copy($fullPath,$newPath);//change tmp file to rvt file  
                                            $response = $s3->putObject([ 'Bucket' => 'osaka-pd',
                                                            'Key' => "BackupFiles/".$docName,
                                                            'SourceFile' => $newPath,
                                                            'ACL' => 'public-read']);
                                            
                                            //$promise = $uploader->promise();                
                                            $files = glob('/tmp/*'); // get all file names
                                            foreach($files as $file){ // iterate files
                                              if(is_file($file))
                                                unlink($file); // delete file
                                            }
                                            
                                            unset($version);
                                            unset($storage);
                                            unset($files);
                                            unset($result);
                                            unset($response);
                                            if(file_exists($fullPath))array_map("unlink", glob($fullPath));//unlink($fullPath);
                                            if(file_exists($newPath))array_map("unlink", glob($newPath));//unlink($newPath); 
    
                                            gc_collect_cycles();//clear garbage collection
                                        }                                                                                                                
                                    }                                
                                }                                                                                                  
                            
                            }else if($item['type'] == "items"){
    
                                $itemName = $item['attributes']['display_name'];
                                if(strpos($itemName, '.rvt') == false || !preg_match('/cen/',$itemName))continue;
                                $itemId = $item['id']; 
        
                                $versions = $itemIns->getItemVersions($proId, $itemId, null, null, null, null, null, null);
                                $allVersion = $versions['data'];
        
                                foreach($allVersion as $version){ 
                                    $docVersion = $version['attributes']['version_number'];
                                    if($docVersion != sizeof($allVersion))continue;
    
                                    $versionId = $version['id']; 
                                    $docName = $version['attributes']['display_name'];                                                         
                                    //$versionInstance = new Autodesk\Forge\Client\Api\VersionsApi($twoLeggedAuth);
                                    //$version = $versionInstance->getVersion($proId, $versionId);
                                    $storage = $version["relationships"]["storage"];
                                    if(empty($storage["data"]["id"]))continue;
                                    $storageId = $storage["data"]["id"];
                                    $temArr = explode(':',$storageId);
                                    $tempStr = end($temArr);
                                    $storArr = explode('/',$tempStr);
                                    $bucket_key = $storArr[0];//"wip.dm.prod"; // string | URL-encoded bucket key
                                    $object_name = $storArr[1];//"c8c40db2-e039-4826-a1be-cd5787687320.rvt";
                                    //$apiInstance = new Autodesk\Forge\Client\Api\ObjectsApi($twoLeggedAuth);                       
                                    $result = $apiInstance->getObject($bucket_key,$object_name,null, null, null,null); 
                                    $fullPath = $result->getPathname();
    
                                    $newPath = "/var/www/html/iPD/public/Download/".$docName;       
                                    copy($fullPath,$newPath);//change tmp file to rvt file  

                                     $response = $s3->putObject([ 'Bucket' => 'osaka-pd',
                                                    'Key' => "BackupFiles/".$docName,
                                                    'SourceFile' => $newPath,
                                                    'ACL' => 'public-read']);
                                    
                                    $files = glob('/tmp/*'); // get all file names
                                    foreach($files as $file){ // iterate files
                                      if(is_file($file))
                                        unlink($file); // delete file
                                    }
                                    clearstatcache();
                                    unset($version);
                                    unset($storage);
                                    unset($files);
                                    unset($result);
                                    unset($response);
                                            
                                    if(file_exists($fullPath))unlink($fullPath);
                                    if(file_exists($newPath))unlink($newPath);
                                    gc_collect_cycles(); 
                                }                             
                            }                                      
                         }
                     }
                    // break;
                }
            }
                 
            //get autobackup projects from db
            //$backupProjects = $this->GetAutoBackupProjects();
            /*foreach($backupProjects as $project){
                $proId = $project["forge_project_id"];
                $itemId = $project["forge_item_id"];
                
                $versions = $itemIns->getItemVersions($proId, $itemId, null, null, null, null, null, null);
                $allVersion = $versions['data'];

                foreach($allVersion as $version){ 
                    $docVersion = $version['attributes']['version_number'];
                    if($docVersion != sizeof($allVersion))continue;

                    $versionId = $version['id']; 
                    $docName = $version['attributes']['display_name'];                                                         
                    //$versionInstance = new Autodesk\Forge\Client\Api\VersionsApi($twoLeggedAuth);
                    //$version = $versionInstance->getVersion($proId, $versionId);
                    $storage = $version["relationships"]["storage"];
                    $storageId = $storage["data"]["id"];
                    $temArr = explode(':',$storageId);
                    $tempStr = end($temArr);
                    $storArr = explode('/',$tempStr);
                    $bucket_key = $storArr[0];//"wip.dm.prod"; // string | URL-encoded bucket key
                    $object_name = $storArr[1];//"c8c40db2-e039-4826-a1be-cd5787687320.rvt";
                    $result = $apiInstance->getObject($bucket_key,$object_name,null, null, null,null); 
                    $fullPath = $result->getPathname();

                    $newPath = "/var/www/html/iPD/public/Download/".$docName;         
                    copy($fullPath,$newPath);//change tmp file to rvt file  
                    clearstatcache();
                     $response = $s3->putObject([ 'Bucket' => 'osaka-pd',
                                    'Key' => "BackupFiles/".$docName,
                                    'SourceFile' => $newPath,
                                    'ACL' => 'public-read']);
                    
                    $files = glob('/tmp/*'); // get all file names
                    foreach($files as $file){ // iterate files
                      if(is_file($file))
                        unlink($file); // delete file
                    }
                    
                    unset($version);
                    unset($storage);
                    unset($files);
                    unset($result);
                    unset($response);
                            
                    if(file_exists($fullPath))unlink($fullPath);
                    if(file_exists($newPath))unlink($newPath);
                    gc_collect_cycles(); 
                }   
            }*/
            
          
        }catch(Exception $e){
            echo $e->getMessage();return;
        }
    }
    

    function GetAutoBackupProjects(){
        $query = "SELECT fp.name as project_name,fp.forge_project_id as forge_project_id
                    FROM tb_project fp
                    WHERE fp.auto_backup = 1";       
        $result = DB::select($query);
        return json_decode(json_encode($result),true);//change array object to array
    }
    
    
}
