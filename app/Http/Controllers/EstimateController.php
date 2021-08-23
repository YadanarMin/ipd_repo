<?php

namespace App\Http\Controllers;
use App\Models\EstimateModel;
use App\Models\PersonalModel;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Exception;
use GuzzleHttp\Client as Client;


class EstimateController extends Controller
{
    private $client;
    private $header;
   
    public function __construct(){
        $this->client = new Client();
    }
    
    
    public function index(){
        $estimate = new EstimateModel();
        $projectList = $estimate->GetEstimateProject();
        if(count($projectList)>0){
            return view('estimate')->with("projectList" , $projectList);
        }else{
            return view('estimate');
        }
        
    }
    
    public function UploadPage(){
        return view('estimate_upload');
    }
    
    public function SettingView(){
        return view('estimateSetting');
    }
    
    public function GetData(Request $request){
        $message = $request->get('message');
        $estimate = new EstimateModel();
        if($message == 'get_estimate_project'){
            $projectList = $estimate->GetEstimateProject();
            if(count($projectList)>0){
                return $projectList;
            }
            
        }elseif($message == 'get_estimate_during_project'){
            $projectList = $estimate->GetEstimateDuringProject();
            if(count($projectList)>0){
                return $projectList;
            }
        }elseif($message == 'get_estimate_finished_project'){
            $projectList = $estimate->GetEstimateFinishedProject();
            if(count($projectList)>0){
                return $projectList;
            }
        }elseif($message == 'get_modelling_company'){
            $listOfCompany = $estimate->GetListOfModellingCompany();
            if(count($listOfCompany)>0){
                return $listOfCompany;
            }
        }
    }
    
    public function ProjectSelect($ipdCode){
        $estimate = new EstimateModel();
        $ipdCodeList = explode(',', $ipdCode); 
        $projectNameList = $estimate->GetProjectNameByiPDCode($ipdCodeList);
        // print_r($projectNameList);
        // return;
        $data = [
            'projectNameList' => $projectNameList,
            'ipdCodeList'     => $ipdCode
            ];
        return view('estimateProjectSelect')->with($data);
    }
    
    public function BoxConnect($folder_id){
        try{
            $access_token = session('access_token');
            $folderId =$folder_id; 
            $file_list=array();
            $this->header =[
                        "Authorization" => "Bearer ".$access_token,
                        "Accept" => "application/json",
                        "Access-Control-Allow-Origin" => "*"
                    ];
            $requestURL = "https://api.box.com/2.0/folders/".$folderId."/items/";
            $response = $this->client->request('GET', $requestURL,['headers' => $this->header ]);
            $files = $response->getBody()->getContents();
            $files = json_decode($files);
            return $files;
        }catch(Exception $e){
            return $e->getMessage();
        }
        
    }
    
    public function BoxConnectForFile($file_id){
        try{
            $access_token = session('access_token');
            $fileId =$file_id; 
            $this->header =[
                        "Authorization" => "Bearer ".$access_token,
                        "Accept" => "application/json",
                        "Access-Control-Allow-Origin" => "*"
                    ];
            $requestURL = "https://api.box.com/2.0/files/".$fileId;
            $response = $this->client->request('GET', $requestURL,['headers' => $this->header ]);
            $files = $response->getBody()->getContents();
            $files = json_decode($files);
            return $files;
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    public function CreateFolder(Request $request ){
        $message = $request->get('message');
        
        if($message == 'create_folder'){
            $projectList = $request->get('project_info');
            $companyList = $request->get('company_list');
            $folder_flag = $request->get('folder_flag');
            
            try{
                $access_token = session('access_token');
                $files = $this->BoxConnect("141077390448");
                if(gettype($files) == 'string' && str_contains($files, 'error')){
                    return $files;
                }
                $folders = $files->entries;
                $folders = json_decode(json_encode($folders),true);
                $folders_count = $files->total_count;
                
                foreach($folders as $folder){
                    $folder_name_full = $folder['name'];
                    $pattern = '/【大林組】\_(\d+)\_/';
                    $folder_name = preg_replace($pattern, '', $folder_name_full);
                    $folder_id   = $folder['id'];
                    $request_url = 'https://api.box.com/2.0/folders';
                    $headers =array(
                            'Authorization:Bearer '.$access_token,
                            'Content-Type: application/json',
                            'Accept: application/json');
                    foreach($companyList as $company){
                        if(str_contains($company, $folder_name)){
                            $keys = array_keys($projectList);
                            foreach($keys as $key){
                                $pjCode = $key;
                                $pjName = $projectList[$pjCode];
                                $created_date = date("Ymd");
                                $project_folder = $pjCode."_".$created_date."_".$pjName."【".$folder_flag."】";
                                
                                $json = json_encode(array('name' => $project_folder,'parent' => array('id' => $folder_id) ));
                                
                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_HEADER, 1);
                                curl_setopt($ch, CURLOPT_URL, $request_url);
                                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                                curl_setopt($ch, CURLOPT_POST, true);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                $response = curl_exec($ch);
                                $info = curl_getinfo($ch);
                                
                                // Change Response type string to json
                                $response = [
                                        'headers' => substr($response, 0, $info["header_size"]),
                                        'body' => substr($response, $info["header_size"]),
                                    ];
                                $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                
                                if($responseCode == 201){
                                    $created_folder_id = json_decode($response['body'])->id;
                                    $subFolderList = [
                                                        json_encode(array('name' => '構造図','parent' => array('id' => $created_folder_id) )),
                                                        json_encode(array('name' => '意匠図','parent' => array('id' => $created_folder_id) )),
                                                        json_encode(array('name' => '工程','parent' => array('id' => $created_folder_id) )),
                                                        json_encode(array('name' => '見積条件・ひな形等','parent' => array('id' => $created_folder_id) )),
                                                        json_encode(array('name' => '見積(提出)','parent' => array('id' => $created_folder_id) )),
                                                        json_encode(array('name' => '秘密保持(提出)','parent' => array('id' => $created_folder_id) )),
                                                        json_encode(array('name' => '協力会社その他','parent' => array('id' => $created_folder_id) ))
                                                    ];
                                    foreach($subFolderList as $subFolder){
                                        curl_setopt($ch, CURLOPT_POSTFIELDS, $subFolder);
                                        $response = curl_exec($ch);
                                    }
                                }
                            }
                            curl_close($ch);
                        }
                    }
                    
                    
                }
                return "success";
            }catch(Exception $e){
                return $e->getMessage();
            }
            
        }
        
    }
    
    public function CheckBoxFolder(Request $request){
        $estimate = new EstimateModel();
        $personal = new PersonalModel();
        $message = $request->get("message");
        if($message == 'check_existing_folder'){
            $projectInfo = $estimate->GetEstimateProject();
            $pjCodeList = $request->get('pj_code');
            try{
                
                $files = $this->BoxConnect("141077390448");
                if(gettype($files) == 'string' && str_contains($files, 'error')){
                    return $files;
                }
                $folders = $files->entries;
                $folders = json_decode(json_encode($folders),true);
                $folders_count = $files->total_count;
                // return $folders;
                $result = array();
                $companyList = array();
                //List of company folders
                foreach($folders as $folder){
                    //******Look for project folder in each and every company folder********//
                    
                    $folder_name = $folder['name'];
                    if(strpos($folder_name,'マスター(業者アクセス不可)') !== false){
                        continue;
                    }
                    $pattern = '/【大林組】\_(\d+)\_/';
                    $company_name = preg_replace($pattern, '', $folder_name);
                    $company_folder_id = $folder["id"];
                    $project_folders = $this->BoxConnect($company_folder_id);
                    $project_folders = $project_folders->entries;
                    $project_folders = json_decode(json_encode($project_folders),true);
                    
                    
                    //List of project folders in company folders
                    foreach($project_folders as $project_folder){
                        $project_folder_name = $project_folder['name'];
                        $project_folder_id   = $project_folder['id'];
                        $pattern = '/^PJ(\d+)/';
                        preg_match($pattern, $project_folder_name, $match, PREG_OFFSET_CAPTURE);
                        $pj_code = $match[0][0];
                        foreach($pjCodeList as $pjCode){
                            if($pjCode == $pj_code){
                                // Check file exist or not 
                                $project_item_folders = $this->BoxConnect($project_folder_id);
                                $project_item_folders = $project_item_folders->entries;
                                $project_item_folders = json_decode(json_encode($project_item_folders),true);
                                
                                // print_r($project_item_folders);
                                $items = array();
                                $items_count = array();
                                $max_date="";
                                $user="";
                                $flag = 1;
                                //List of item folders in project
                                foreach($project_item_folders as $project_item){
                                    $project_item_folder_name = $project_item['name'];
                                    $project_item_folder_id  = $project_item['id'];
                                    $item_folders_list = $this->BoxConnect($project_item_folder_id);
                                    
                                    $counts = $item_folders_list->total_count;
                                    $items_count[$pj_code][$project_item_folder_name] = $counts;
                                    
                                    if($counts>0){
                                        $file_list = $item_folders_list->entries;
                                        $file_list = json_decode(json_encode($file_list),true);
                                        foreach($file_list as $file){
                                            $type = $file['type'];
                                            $file_id = $file['id'];
                                            $file_info = $this->BoxConnectForFile($file_id);
                                            $file_info = json_decode(json_encode($file_info),true);
                                            $created_date = strtotime($file_info['created_at']);
                                            $created_person = $file_info['created_by']['login'];
                                            $created_person_info = $personal->GetUserNameByMail($created_person);
                                            if(count($created_person_info)>0){
                                                $created_person_name = $created_person_info[0]['username'];
                                            }else{
                                                $created_person_name = $created_person;
                                            }
                                             
                                            //$created_date = date('Y-m-d H:i:s', $created_date_str);
                                            if($flag == 1){
                                                $max_date = $created_date;
                                                $user = $created_person_name;
                                                $flag++;
                                            }else{
                                                if($created_date>$max_date){
                                                    $max_date = $created_date;
                                                    $user = $created_person_name;
                                                }
                                            }
                                            
                                            
                                        } 
                                    }else{
                                        
                                    }
                                    
                                    //print_r($item_folders_list);
                                    
                                    
                                }
                                if(empty($max_date)){
                                    $items[$pj_code]['created_date'] ='' ;
                                    $items[$pj_code]['created_user'] ='' ;
                                }else{
                                    $items[$pj_code]['created_date'] = date('Y/m/d', $max_date);
                                    $items[$pj_code]['created_user'] = $user;
                                }
                                
                                $companyList[$pj_code][] = [$company_name, $items,$items_count];
                                
                            }
                        }
                        
                    }
                }
                $result[] = $companyList;
                return array('box_folder_info' => $result, 'list_of_estimate_project' => $projectInfo);
            }catch(Exception $e){
                return $e->getMessage();
            }
        }
    }
    
    
    public function UpdateData(Request $request){
        $message = $request->get('message');
        $estimate = new EstimateModel();
        if($message == "move_to_estimatechuu"){
            $pj_code = $request->get('pj_code');
            $update = $estimate->UpdateFlag($pj_code, "during_estimate");
            return $update;
        }elseif($message == "move_to_estimate_finished"){
            $pj_code = $request->get('pj_code');
            $update = $estimate->UpdateFlag($pj_code, "finished_estimate");
            return $update;
        }
    }
    
   
    
}