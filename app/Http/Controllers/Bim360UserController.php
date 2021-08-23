<?php

namespace App\Http\Controllers;
use App\Models\CommonModel;
use App\Models\ForgeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class Bim360UserController extends Controller
{

    function Index()
    {
     $forge = new ForgeModel();
     $projects = $forge->GetAllProject();   
     return view('bim360')->with(["projects"=>$projects]);
    }
    
    function GetTwoLeggedToken(){
        try{$conf = new \Autodesk\Auth\Configuration();//escape from current name space by using '/'
        $conf->getDefaultConfiguration()
        ->setClientId(env("FORGE_CLIENT_ID"))//'J0jduCzdsYAbKXqsidxCBt3aWpW5DNv0'
        ->setClientSecret(env("FORGE_CLIENT_SECRET"));//'Hp8X9pxKgYjqJYGE'//bim360local app that is created by yadanar min
        // ->setClientId('1JdTGvw0dhm50GbMqfnocubhm5D70P1X')
        // ->setClientSecret('eMqGzyufSlyBiChD');//Fmori of client_id and secret_key
        $authObj = new \Autodesk\Auth\OAuth2\TwoLeggedAuth();
        $scopes = array("code:all","account:read","data:read","data:write");
        $authObj->setScopes($scopes);

        $authObj->fetchToken();
        $access_token = $authObj->getAccessToken();
        return $access_token;
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    function User(){

           try {  $conf = new \Autodesk\Auth\Configuration();//escape from current name space by using '/'
            $conf->getDefaultConfiguration()
            ->setClientId('1JdTGvw0dhm50GbMqfnocubhm5D70P1X')
            ->setClientSecret('eMqGzyufSlyBiChD');//Fmori of client_id and secret_key
   
            $authObj = new \Autodesk\Auth\OAuth2\TwoLeggedAuth();
            $scopes = array("code:all","account:read","data:read","data:write");
            $authObj->setScopes($scopes);
    
            $authObj->fetchToken();
            $access_token = $authObj->getAccessToken();
            $authObj->setAccessToken($access_token);
            $apiInstance = new \Autodesk\Forge\Client\Api\UserProfileApi($authObj);

            $project_id = "90e307e7-22fb-47b0-9c14-cc020ebf3eb0";
            //$result = $apiInstance->getProjectUsers($project_id);
            
            $hubInstance = new \Autodesk\Forge\Client\Api\HubsApi($authObj);
            $hubs = $hubInstance->getHubs(null, null);
            $hubObj = $hubs['data'];
            foreach($hubObj as $hub){
                $hubId = $hub['id'];
                $hubName = $hub['attributes']['name'];
                if($hubName == "OBAYASHI")continue;
                //print_r($hubName);
                $apiInstance = new \Autodesk\Forge\Client\Api\UserProfileApi($authObj);
                $hubId = substr($hubId, 2);//remove prefix b. from projectId

                $result = $apiInstance->getUsersTest($project_id);
                echo"<pre>";
                print_r($result);
                echo"</pre>";
                //akhu ka 30
                
            }
            echo"<pre>";
            print_r($result);
            echo"</pre>";

        } catch (Exception $e) {
            echo 'Exception when calling forge library function : ', $e->getMessage(), PHP_EOL;
        }

   }

    function ShowPage(){
        //$projectName = $_GET["projectName"];
        //return redirect()->guest(route('permission'));
        $projectId="";
        $projectName="";
        if (session()->has('bim360ProjectId'))
        {
            $projectId = session('bim360ProjectId');
            $projectName = session('bim360PojectName');
        }
        return view('permission')->with(['bim360ProjectId'=>$projectId,'bim360ProjectName'=>$projectName]); 
    }
    
    
    function SetProjectIdToSession(Request $request){
       
        $projectId = $request->projectId;
        $projectName = $request->projectName;
        session(['bim360ProjectId' => $projectId]);
        session(['bim360PojectName' => $projectName]);
        return "success";
       //return  redirect('/iPD/admin/permissionPage');//->with(["projectId"=>$projectId]);
    }
    
    function GetProjectUsers($projectId){
        $conf = new \Autodesk\Auth\Configuration();//escape from current name space by using '/'
        $conf->getDefaultConfiguration()
        ->setClientId('WKK1H6ryRiVP3TnN0enkZ4dRgen0Gedg')
        ->setClientSecret('gcnPNhXtjith3dC3');

        $authObj = new \Autodesk\Auth\OAuth2\TwoLeggedAuth();
        $scopes = array("code:all","account:read","data:read","data:write");
        $authObj->setScopes($scopes);

        $authObj->fetchToken();
        $access_token = $authObj->getAccessToken();
        $authObj->setAccessToken($access_token);
        $apiInstance = new \Autodesk\Forge\Client\Api\UserProfileApi($authObj);

        $projectId = substr($projectId, 2);//remove prefix b. from projectId
        //$projectId = "694576bd-1ae6-4285-b843-3bf773c4cef41";
        $result = $apiInstance->getProjectUsers($projectId);
        return $result;
    }
    
    function GetAllUsers(){
        $conf = new \Autodesk\Auth\Configuration();//escape from current name space by using '/'
        $conf->getDefaultConfiguration()
          ->setClientId('WKK1H6ryRiVP3TnN0enkZ4dRgen0Gedg')//admin account client id and secrect key
            ->setClientSecret('gcnPNhXtjith3dC3');
        $authObj = new \Autodesk\Auth\OAuth2\TwoLeggedAuth();
        $scopes = array("code:all","data:read","data:write","account:read");
        $authObj->setScopes($scopes);

        $authObj->fetchToken();
        $hubInstance = new \Autodesk\Forge\Client\Api\HubsApi($authObj);
        $hubs = $hubInstance->getHubs(null, null);
            $hubObj = $hubs['data'];
            foreach($hubObj as $hub){
                $hubId = $hub['id'];
                $hubName = $hub['attributes']['name'];
                if($hubName == "OBAYASHI")continue;
                //print_r($hubName);
                $apiInstance = new \Autodesk\Forge\Client\Api\UserProfileApi($authObj);
                $hubId = substr($hubId, 2);//remove prefix b. from projectId

                $offset = 0;
                $resultList= array();
                while($offset < 3000){
                    
                    $result = $apiInstance->getUsers($hubId,$offset);
                    $offset += 100;
                    $resultList = array_merge($resultList, $result);
                    
                }
                
                if(sizeof($resultList) > 0){
                    $this->SaveBim360Users($resultList);
                }
                print_r ("success");
                //return $resultList;
                //echo"<pre>";
                //print_r($resultList);
                //echo"</pre>";
                //akhu ka 30
                
            }
    }
    
    function GetPermissionData(Request $request){
        
        $message = $request->message;
        if($message == "getFolders"){
            $projectId = $request->projectId;
            $parmProjectName = $request->projectName;
        
            $conf = new \Autodesk\Auth\Configuration();//escape from current name space by using '/'
            $conf->getDefaultConfiguration()
            ->setClientId('WKK1H6ryRiVP3TnN0enkZ4dRgen0Gedg')//admin account client id and secrect key
            ->setClientSecret('gcnPNhXtjith3dC3');
            $authObj = new \Autodesk\Auth\OAuth2\TwoLeggedAuth();
            $scopes = array("code:all","data:read","data:write","bucket:read");
            $authObj->setScopes($scopes);
    
            $authObj->fetchToken();
            $folders = array();
             //get Hubs
            $hubInstance = new \Autodesk\Forge\Client\Api\HubsApi($authObj);
            $projectInstance = new \Autodesk\Forge\Client\Api\ProjectsApi($authObj);
            $folderIns = new \Autodesk\Forge\Client\Api\FoldersApi($authObj);
            
            $hubs = $hubInstance->getHubs(null, null);
            $hubObj = $hubs['data'];
            foreach($hubObj as $hub){
                $hubId = $hub['id'];
                $hubName = $hub['attributes']['name'];
                if($hubName == "OBAYASHI")continue;
                $projects = $projectInstance->getHubProjects($hubId, null, null);                
                $proObj = $projects['data'];
    
                foreach($proObj as $project){
                
                    $proId = $project['id'];
                    $projectName = $project['attributes']['name'];
                    
                    if(strstr($projectName,$parmProjectName) == false)continue;
                    $topFolders = $projectInstance->getProjectTopFolders($hubId, $proId);
                    $topFolderData = $topFolders['data'];  
                    
                     foreach($topFolderData as $topfolder){  
                         $topFolderId = $topfolder['id'];
                         $folderName = $topfolder['attributes']['display_name'];
                         if($folderName == "Shared" || $folderName == "Consumed")continue;   
                             $foderUnderFolder = $folderIns->getFolderContents($proId, $topFolderId, null, null, null, null,null);
                             $innerFolders = $foderUnderFolder['data']; 
                              
                             foreach($innerFolders as $innerFolder){
                                 if($innerFolder['type'] == "folders"){
                                    //echo"<pre>";
                                     //print_r($innerFolders);
                                     //echo"</pre>";return;
                                     if($innerFolder['attributes']['display_name'] == "Shared" || $innerFolder['attributes']['display_name'] == "Consumed")continue;   
                                      $topFolderId = $innerFolder['id'];
                                      $innerFolderName = $innerFolder['attributes']['display_name'];
                                        $folders[$topFolderId] = $innerFolderName;
                                 }
                                
                             }
                        // array_push($folders,$folderName);
                     }
                }
            }
            return $folders;
        }
        else if($message == "getFolderUsers"){
            $projectId = $request->projectId;
            $folderIds = json_decode($request->folderIdArray);
            $result=array();
            foreach($folderIds as $folderId){
                $users = $this->GetFolderUsers($projectId,$folderId);
                $result[$folderId] = $users;
            }
            return $result;
            
        }else if($message == "getUsers"){
            $result = $this->GetAllUsers();
            $bim360_users = array();
            foreach($result as $row){
                $id = $row["id"];
                $name = $row["name"];
                $email = $row["email"];
                array_push($bim360_users,array("id"=>$id,"name"=>$name,"email"=>$email));
            }
            session(['bim360_users' => $bim360_users]);
            //
            return "success";
        }else if($message == "getProjectUsers"){
            $projectId = $request->projectId;
            $projectUsers = $this->GetProjectUsers($projectId);
            $bim360_users = $this->getBim360UsersFromDB();
            foreach($projectUsers as $key=>$row){
                $user_id = $row["id"];
                foreach($bim360_users as $user){
                    if(trim($user["id"]) == trim($user_id)){
                        $projectUsers[$key]["user_type"] = $user["role"];
                        $projectUsers[$key]["company_name"] = $user["company_name"];
                    }
                }
            }
            return $projectUsers;
        }else if($message == "getBim360Users"){
              $bim360_users = $this->GetBim360UsersFromDB();
              return $bim360_users;
        }else if($message == "getBim360UsersByIds"){
              $userIds = json_decode($request->UserIds);
              $bim360_users = $this->GetBim360UsersByUserIds($userIds);
              return $bim360_users;
        }
        
    }

    function GetFolderUsers($projectId,$folderId){
        try {  $conf = new \Autodesk\Auth\Configuration();//escape from current name space by using '/'
            $conf->getDefaultConfiguration()
            ->setClientId('WKK1H6ryRiVP3TnN0enkZ4dRgen0Gedg')//admin account client id and secrect key
            ->setClientSecret('gcnPNhXtjith3dC3');
   
            $authObj = new \Autodesk\Auth\OAuth2\TwoLeggedAuth();
            $scopes = array("code:all","account:read","data:read","data:write");
            $authObj->setScopes($scopes);
    
            $authObj->fetchToken();
            $access_token = $authObj->getAccessToken();
            $authObj->setAccessToken($access_token);
            $apiInstance = new \Autodesk\Forge\Client\Api\UserProfileApi($authObj);
            $projectId = substr($projectId, 2);//remove prefix b. from projectId
            $result = $apiInstance->getFolderUsers($projectId,$folderId);
            return $result;
            //return sizeof($result);


        } catch (Exception $e) {
            echo 'Exception when calling forge library function : ', $e->getMessage(), PHP_EOL;
        }
    }
   
    function SaveBim360Users($userList){
        try{
            foreach($userList as $user){
                    $user_id = $user["id"];
                    $email = $this->escape_string($user["email"]); 
                    $name = $this->escape_string($user["name"]); 
                    $nickname = $this->escape_string($user["nickname"]); 
                    $first_name = $this->escape_string($user["first_name"]); 
                    $last_name = $this->escape_string($user["last_name"]);
                    $uid = $user["uid"];
                    $image_url = $this->escape_string($user["image_url"]);
                    $address_line_1 = $this->escape_string($user["address_line_1"]);
                    $address_line_2 = $this->escape_string($user["address_line_2"]); 
                    $city = $this->escape_string($user["city"]);
                    $postal_code = $user["postal_code"];
                    $state_or_province = $this->escape_string($user["state_or_province"]);
                    $country = $this->escape_string($user["country"]);
                    $phone = $user["phone"];
                    $company = $this->escape_string($user["company"]);
                    $job_title = $this->escape_string($user["job_title"]); 
                    $industry = $this->escape_string($user["industry"]);
                    $about_me = $this->escape_string(addslashes($user["about_me"]));
                    $created_at = $this->escape_string($user["created_at"]);
                    $updated_at = $this->escape_string($user["updated_at"]);
                    $account_id = $user["account_id"];
                    $role = $user["role"];
                    $status = $user["status"];
                    $company_id = $user["company_id"];
                    $company_name = $user["company_name"];
                    $last_sign_in = $user["last_sign_in"];

                    //echo $item_name."---------".$kouji_name."----------".$client_name."----------".$address;
                    $query = "INSERT INTO tb_bim360_user(id,user_id,account_id,role,status,company_id,company_name,"
                                                        ."last_sign_in,email,name,nickname,first_name,last_name,uid,image_url,"
                                                        ."address_line_1,address_line_2,city,state_or_province,postal_code,country,"
                                                        ."phone,company,job_title,industry,about_me,created_at,updated_at)"
                            ."(SELECT COALESCE(MAX(id), 0) + 1,'$user_id','$account_id','$role','$status','$company_id','$company_name',"
                                                ."'$last_sign_in','$email','$name','$nickname','$first_name','$last_name','$uid','$image_url',"
                                                ."'$address_line_1','$address_line_2','$city','$state_or_province','$postal_code','$country',"
                                                ."'$phone','$company','$job_title','$industry','$about_me','$created_at','$updated_at'"
                            ."FROM tb_bim360_user)"
                            ."ON DUPLICATE KEY UPDATE user_id ='$user_id',account_id ='$account_id',role ='$role',status='$status',company_id='$company_id',company_name='$company_name',"
                                                ."last_sign_in='$last_sign_in',email='$email',name='$name',nickname='$nickname',first_name='$first_name',last_name='$last_name',uid='$uid',image_url='$image_url',"
                                                ."address_line_1='$address_line_1',address_line_2='$address_line_2',city='$city',state_or_province='$state_or_province',postal_code='$postal_code',country='$country',"
                                                ."phone='$phone',company='$company',job_title='$job_title',industry='$industry',about_me='$about_me',created_at='$created_at',updated_at='$updated_at'";
                    DB::insert($query);

                }

        }catch(Exception $e){
            echo 'Exception when project info save : ', $e->getMessage(), PHP_EOL;
        }
    }
    
    function GetBim360UsersFromDB(){
        $query = "SELECT user_id as id,name,role,company_name,email FROM tb_bim360_user";
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }
    
    function GetBim360UsersByUserIds($userIds){
        $userIdsArray = ($userIds == "") ? "'"."ALL_UNCHECK"."'" : "'" . implode ( "', '", $userIds ) . "'";//convert array to string with single code
        $query = "SELECT user_id as id,name,role,company_name,email FROM tb_bim360_user WHERE user_id IN($userIdsArray)";
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }
    
    /**
     * special character escaping single code fun() 
     * given parameter[string]
     * return escape string
     * to save special char to database
     */
    function escape_string($string){
        
        return addslashes($string);
        /*$escape_string = str_replace("'", "\'",$string);
        $escape_string = str_replace("\\'", "\'",$string);
        return $escape_string;*/
    }
    
    function PermissionManagement(Request $request){
        
        $conf = new \Autodesk\Auth\Configuration();//escape from current name space by using '/'
        $conf->getDefaultConfiguration()
          ->setClientId('WKK1H6ryRiVP3TnN0enkZ4dRgen0Gedg')//admin account client id and secrect key
            ->setClientSecret('gcnPNhXtjith3dC3');
        $authObj = new \Autodesk\Auth\OAuth2\TwoLeggedAuth();
        $scopes = array("code:all","data:read","data:write","account:read","account:write");
        $authObj->setScopes($scopes);

        $authObj->fetchToken();
        $apiInstance = new \Autodesk\Forge\Client\Api\UserProfileApi($authObj);
        
         $message = $request->message;
        if($message == "deleteFolderPermission"){
            $projectId = $request->projectId;
            $projectId = substr($projectId, 2);
            $folderId = $request->folderId;
            $userId = $request->userId;
            $result = $apiInstance->DeletePermission($projectId,$folderId,$userId);
            //return $result;
            if($result == "")
                return "success";
             else
                return "error";
                
        }else if($message == "addFolderPermission"){
            $projectId = $request->projectId;
            $projectId = substr($projectId, 2);
            $folderId = $request->folderId;
            $userId = $request->userId;
            $result = $apiInstance->AddPermission($projectId,$folderId,$userId);
            if(!empty($result))
                return "success";
            else
                return "error";
            
        }else if($message == "addNewMember"){
            $projectId = $request->projectId;
            $projectId = substr($projectId, 2);
            $userIds = json_decode($request->UserIds);
            $hubInstance = new \Autodesk\Forge\Client\Api\HubsApi($authObj);
            $hubs = $hubInstance->getHubs(null, null);
            $hubObj = $hubs['data'];
            $account_id = "";
            foreach($hubObj as $hub){
                
                $hubName = $hub['attributes']['name'];
                if($hubName == "OBAYASHI")continue;
                $hubId = $hub['id'];
                $account_id = substr($hubId, 2);
            }
            $result="";

            foreach($userIds as $user_id){
               $result = $apiInstance->AddUserToProject($projectId,$account_id,$user_id);
            }
        
            if(!empty($result))
                return "success";
            else
                return "error";
        }
    }
    
    function Add(){

        $conf = new \Autodesk\Auth\Configuration();//escape from current name space by using '/'
        $conf->getDefaultConfiguration()
          ->setClientId('WKK1H6ryRiVP3TnN0enkZ4dRgen0Gedg')//admin account client id and secrect key
            ->setClientSecret('gcnPNhXtjith3dC3');
        $authObj = new \Autodesk\Auth\OAuth2\TwoLeggedAuth();
        $scopes = array("code:all","data:read","data:write","account:read","account:write");
        $authObj->setScopes($scopes);
        

        $authObj->fetchToken();
        $apiInstance = new \Autodesk\Forge\Client\Api\UserProfileApi($authObj);
        $hubInstance = new \Autodesk\Forge\Client\Api\HubsApi($authObj);
        $hubs = $hubInstance->getHubs(null, null);
        $hubObj = $hubs['data'];
        $account_id = "";
        foreach($hubObj as $hub){
                
            $hubName = $hub['attributes']['name'];
            if($hubName == "OBAYASHI")continue;
            $hubId = $hub['id'];
            $account_id = substr($hubId, 2);
        }
        $projectId = "d84c8dc5-c88b-4efb-9ab1-788f9ce50629";
        $userId = "c817e96e-a342-42e5-aef0-d2e8a46bcd5b";
        $result = $apiInstance->AddUserToProject($projectId,$account_id,$userId);
        print_r($result);
    }
    
    function test(){
        $conf = new \Autodesk\Auth\Configuration();//escape from current name space by using '/'
        $conf->getDefaultConfiguration()
          ->setClientId('WKK1H6ryRiVP3TnN0enkZ4dRgen0Gedg')//admin account client id and secrect key
            ->setClientSecret('gcnPNhXtjith3dC3');
        $authObj = new \Autodesk\Auth\OAuth2\TwoLeggedAuth();
        $scopes = array("code:all","data:read","data:write","account:read","account:write");
        $authObj->setScopes($scopes);

        $authObj->fetchToken();
        $apiInstance = new \Autodesk\Forge\Client\Api\UserProfileApi($authObj);



        $projectId = "d0cc2181-0303-411c-aff7-a3af8c2aec1e";//"694576bd-1ae6-4285-b843-3bf773c4cef4";b.d84c8dc5-c88b-4efb-9ab1-788f9ce50629
        //$projectId = substr($projectId, 2);
        $folderId = "urn:adsk.wipprod:fs.folder:co.KDdRDv_5ScCAty5XaL91BQ";//"urn:adsk.wipprod:fs.folder:co.4ngCWAN9ROmXvh7IAlZwXA";//"urn:adsk.wipprod:fs.folder:co.-jeIEDovSQSrUvD4NAWQ1Q";
        $userId = "c817e96e-a342-42e5-aef0-d2e8a46bcd5b";//"c817e96e-a342-42e5-aef0-d2e8a46bcd5b";//"ca50a253-0b95-4a72-a4df-cb5c68eb3c04"
        $result = $apiInstance->DeletePermission($projectId,$folderId,$userId);
        print_r($result);
        
    }
    
    function Top(){
         $conf = new \Autodesk\Auth\Configuration();//escape from current name space by using '/'
            $conf->getDefaultConfiguration()
             ->setClientId('J0jduCzdsYAbKXqsidxCBt3aWpW5DNv0')
            ->setClientSecret('Hp8X9pxKgYjqJYGE');//bim360local App
            $authObj = new \Autodesk\Auth\OAuth2\TwoLeggedAuth();
            $scopes = array("code:all","data:read","data:write","bucket:read");
            $authObj->setScopes($scopes);
    
            $authObj->fetchToken();
            $folders = array();
            $projectId = "b.d0cc2181-0303-411c-aff7-a3af8c2aec1e";
             //get Hubs
            $hubInstance = new \Autodesk\Forge\Client\Api\HubsApi($authObj);
            $projectInstance = new \Autodesk\Forge\Client\Api\ProjectsApi($authObj);
            $folderIns = new \Autodesk\Forge\Client\Api\FoldersApi($authObj);
            
            $hubs = $hubInstance->getHubs(null, null);
            $hubObj = $hubs['data'];
            foreach($hubObj as $hub){
                $hubId = $hub['id'];
                $hubName = $hub['attributes']['name'];
                if($hubName == "OBAYASHI")continue;
                $projects = $projectInstance->getHubProjects($hubId, null, null);                
                $proObj = $projects['data'];
    
                foreach($proObj as $project){
                
                    $proId = $project['id'];
                    $projectName = $project['attributes']['name'];
                    
                    if(strstr($proId,$projectId) == false)continue;
  
                    $topFolders = $projectInstance->getProjectTopFolders($hubId, $proId);
                    
                    $topFolderData = $topFolders['data'];  
                    
                     foreach($topFolderData as $topfolder){  
                         $topFolderId = $topfolder['id'];
                         $folderName = $topfolder['attributes']['display_name'];
                         $object_count = $topfolder['attributes']['object_count'];
                         if($object_count == 0) continue;
                         if($folderName == "Shared" || $folderName == "Consumed")continue;  
                         echo"<pre>";
                              print_r($topFolderId);
                              print_r($folderName);
                              echo"</pre>";continue;
                             /* $foderUnderFolder = $folderIns->getFolderContents($proId, $topFolderId, null, null, null, null,null);
                             $innerFolders = $foderUnderFolder['data']; 
                              echo"<pre>";
                              print_r($innerFolders);
                              echo"</pre>";continue;
                            foreach($innerFolders as $innerFolder){
                                 if($innerFolder['type'] == "folders"){
                                    //echo"<pre>";
                                     //print_r($innerFolders);
                                     //echo"</pre>";return;
                                     if($innerFolder['attributes']['display_name'] == "Shared" || $innerFolder['attributes']['display_name'] == "Consumed")continue;   
                                      $topFolderId = $innerFolder['id'];
                                      $innerFolderName = $innerFolder['attributes']['display_name'];
                                        $folders[$topFolderId] = $innerFolderName;
                                 }
                                
                             }*/
                        // array_push($folders,$folderName);
                     }
                }
            }
            return $folders;
    }
}
