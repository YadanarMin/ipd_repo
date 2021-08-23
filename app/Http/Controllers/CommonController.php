<?php

namespace App\Http\Controllers;
use App\Http\Controllers\AllstoreController;
use App\Models\CommonModel;
use App\Models\LoginModel;
use App\Models\ForgeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use ZipArchive;
use Excel;
use App\Imports\ExcelDataImport;
use Exception;
use GuzzleHttp\Client;

class CommonController extends Controller
{
   
    function DisplayChangedInfoPage(){
        $common = new CommonModel();
        //$data = $common->GetData();  
       
        return view('changedHistory');//->with(["branch"=>$branch,"partner"=>$partner]);
    }
    
    function DisplaySaveRoomPage(){
         $common = new CommonModel();
         $data = $common->GetShiageData();  
         return view('saveRoom')->with(["data"=>$data]);
    }
    function DisplayuserInfoPage(){
         //$common = new CommonModel();
         //$data = $common->GetShiageData();  
         return view('userInfo');//->with(["data"=>$data]);
    }
    
    function DisplayAccessLogPage(){
        return view('accessLog');
    }
    
    function SaveAccessLog(Request $request){
        $message = $request->get('message');
        $loginUser = $request->get('loginUserName');
        $funName = $request->get('functionName');
        $curDateTime = $request->get('currentDateTime');
        $common = new CommonModel();
        if($message == 'saveAccessLog'){
            $insert = $common->InsertAccessLog($loginUser,$funName,$curDateTime);
            return $insert;
        }
    }
    
    function GetAccessLog(Request $request){
         $message = $request->get('message');
         $common = new CommonModel();
         if($message == "getAccessLog"){
             $accessLogList = $common->GetAccessLog();
             return array("AccessLog"=>$accessLogList);
         }else if($message == "searchData"){
             $name =  $request->get('name');
             $startDate =  $request->get('startDate');
             $endDate  =  $request->get('endDate');
             $searchData = $common->SearchAccessLog($name,$startDate,$endDate);
             return array($searchData);
         }
    }
    
    function UploadFilesIndex(){
        $directory = "/var/www/html/iPD/public/UploadedFiles/";
        $files = array_diff(scandir($directory), array('.', '..'));
        //$files = scandir($directory);
        
        return view("upload_file")->with(["files"=>$files]);
    }
    
    
    public function DownloadFiles($fileArray) {
        $files = json_decode($fileArray);
        
        $zipname = 'file.zip';
        $zip = new ZipArchive;
        
        $zip->open($zipname, ZipArchive::CREATE);
        foreach($files as $file) {
          $path="/var/www/html/iPD/public/UploadedFiles/".$file;
          if(file_exists($path)){
            $zip->addFile($path,iconv("UTF-8","UTF-8",$file));  
          }
        }
        $zip->close();
        
        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename='.$zipname);
        header('Content-Length: ' . filesize($zipname));
        readfile($zipname);
        if(file_exists($zipname)){
            unlink($zipname);
        
        }
        exit;
        //$name = basename($file);
        //return response()->download($zip, $zipname);
    }
    
    function UploadFiles(Request $request){
        try{
            $message = $request->get("message");
            if($message=="upload_file"){
                $uploadFiles =json_decode($request->get("fileNames"));
                $tempPath = "/var/www/html/iPD/public/Upload/";
                $uploadPath = "/var/www/html/iPD/public/UploadedFiles/";
                foreach($uploadFiles as $fileName){
                    if(is_file($tempPath.$fileName)){
                        rename($tempPath.$fileName,$uploadPath.$fileName);//move upload files
                    }
                }
                $this->SetDownloadFileToSession();
            }else if($message == "delete_file"){
                $fileName = $request->get("fileName");
                $path = "/var/www/html/iPD/public/UploadedFiles/".$fileName;
                if(is_file($path)){
                    unlink($path);
                    
                    $this->SetDownloadFileToSession();
                }
            }
            
            return "success";
        }catch(Exception $e){
            return $e->getMessage();
        }
       
    }

    function SetDownloadFileToSession(){
        $directory = "/var/www/html/iPD/public/UploadedFiles/";
        $files = array_diff(scandir($directory), array('.', '..'));
        session(['DownloadFiles' =>$files]);
    }

    function BoxLogin(Request $request){
       $boxBtnText = $request->get('btnText');
       //return $forgeBtnText;
        if(strstr($boxBtnText,"LOGOUT") == true){
            session()->forget('access_token');
            return "BOX LOGIN AGAIN";
        }
        $baseUrl = "https://account.box.com/api/oauth2/authorize?";
        $clientId = "bzpjlr7faa0cx16vqijmnv3jx8mxgoih";
        $secrectKey = "6XG9HqruQQhCndybKYqRmAYyLla12q4x";
        $redirect_uri="https://obayashi-ccc.net/iPD/box/callback";
        $url =$baseUrl.'response_type=code' .'&client_id=' . $clientId .'&redirect_uri=' . $redirect_uri;
        
        return $url;
     
    }
    
    function BoxCallBack(){
        $requestURL = \Request::getRequestUri();
        $params = parse_url($requestURL);
        $code = $params['query'];
        $tempArr = explode("=",$code);
        $box_token = $tempArr[1];
        if($box_token != ""){
            
            $client = new \GuzzleHttp\Client();
            $clientId = "bzpjlr7faa0cx16vqijmnv3jx8mxgoih";
            $secrectKey = "6XG9HqruQQhCndybKYqRmAYyLla12q4x";
            $requestToken = "https://api.box.com/oauth2/token/";
            $params =["grant_type"=>"authorization_code","client_id" => $clientId,"client_secret"=>$secrectKey,"code"=>$box_token,"scope"=>"root_readwrite item_upload item_preview base_explorer"];
            $header=["Content-Type"=>"application/x-www-form-urlencoded"];
            $response = $client->request('POST', $requestToken,['form_params' => $params,"headers"=>$header]);
            
            $data = $response->getBody()->getContents();
            // print_r($data);
            // return;
            $access_token = json_decode($data)->access_token;
            if($access_token != ""){
                /*$folderId = "131808756610";//資料/ccc取り込み/画像
                $file_list = $this->GetFilesFromBox($folderId,$access_token);
                session(['box_files'=>$file_list]);*/
                
                if(session()->has('authority_id')){
                    $login = new LoginModel();
                    $result = $login->GetBoxAuthority(session('authority_id'));
                   
                    if($result[0]["box_access"] == 1){
                        $excel_folder_id = "134217819825";//CCC取り込み用/全店物件データ
                        $allStoreList = $this->UpdateAllStoreFromBox($excel_folder_id,$access_token);
                        
                        $allstore = new AllstoreController();
                        $allstore->RecordAllstoreUpdateHistory();
                    }
                }
 
            }
            session(['access_token' =>$access_token]);//set box_token to session
            return redirect('/login/successlogin');//project/report
            
        }
        
    }
    
    function GetBoxData(Request $request){
        /*$message = $request->get("message");
        if($message == "get_top_folder"){
            $client = new \GuzzleHttp\Client();
            
            if(session()->has('access_token')){
                $access_token = session('access_token');
                return array("fileId"=>"770891111773","token"=>$access_token);
                
                $requestURL = "https://api.box.com/2.0/files/770891111773?fields=expiring_embed_link";
                $header =[
                    "Authorization" => "Bearer ".$access_token,
                    "Accept" => "application/json"
                    ];
                $response = $client->request('GET', $requestURL,['headers' => $header ]);
                $data = $response->getBody()->getContents();
                return $data;
                $access_token = json_decode($data);
                return $response;

            }

            
            
        }*/
        /* $access_token = session('access_token');
            if($access_token != ""){
                $folderId = "130980505839";//ccc取り込み/資料/
                $file_list = $this->GetFilesFromBox($folderId,$access_token);
                
                return array("files"=>$file_list,"token"=>$access_token);
            }*/
    }
    
    function UpdateAllStoreFromBox($folderId,$access_token){
        try{
            $file_list=array();
            $client = new \GuzzleHttp\Client();
            $requestURL = "https://api.box.com/2.0/folders/".$folderId."/items/";
            $header =[
                "Authorization" => "Bearer ".$access_token,
                "Accept" => "application/json"
                ];
            $response = $client->request('GET', $requestURL,['headers' => $header ]);
            $items = $response->getBody()->getContents();
            $items = json_decode($items)->entries;
            foreach($items as $item){
                if($item->type == "file"){
                    $fileId = $item->id;
                    $fileName = $item->name;
                    //if(strstr($fileName,"BIMプロジェクト情報一覧表") == true){
                    $requestURL = "https://api.box.com/2.0/files/".$fileId."/content/";
                    $response = $client->request('GET', $requestURL,['headers' => $header ]);
                    $file_content = $response->getBody()->getContents();
                    $filePath= "/var/www/html/iPD/public/Download/allstore_excel.xlsx";
                    file_put_contents($filePath, $file_content);
                    $aaa = $this->LoadExcelData($filePath);
                    return $aaa;
                    //}
                   
                }
               
            }
        }catch(Exception $e){
            return back()->with("error","failed when reading　allstore info from box");
        }
        //return $file_list;
    }
    
    function UpdateAllStoreFromBrowser(Request $request){
        // $filePath= "/var/www/html/iPD/public/Download/upload_file.xlsx";
        // $request->file('file')->storeAs('',$filePath);
        // print_r($request);return;
        // $request->file('file');
        // $this->LoadExcelData($filePath);
        
        // print_r($request->file('file'));
    }
    
    function LoadExcelData($filePath){

        //$data = Excel::load($filePath)->get();
       try{ 
           $import = new ExcelDataImport();
           $data = Excel::toArray( $import, $filePath);
        //   $import->ImportDataToDatabase($data);
            $aaa = $import->ImportDataToDatabase($data);
            // return $aaa;
           unlink($filePath);
            //print_r($data);return;
            /*if($data->count() > 0){
                foreach($data->toArray() as $key=>$value){
                    print_r($value);
                    foreach($value as $row){
                        //$insert_data[] =arary()
                    }
                }
            }*/
       }catch(Exception $e){
           echo $e->getMessage();
       }
    }
    
    
    

    
}
