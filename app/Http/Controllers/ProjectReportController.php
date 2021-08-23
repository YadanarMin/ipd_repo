<?php

namespace App\Http\Controllers;
use App\Models\CommonModel;
use App\Models\LoginModel;
use App\Models\ForgeModel;
use App\Models\ProjectReportModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use ZipArchive;
class ProjectReportController extends Controller
{
   
   function Loadhashtag(){
        //return view('project_report');//->with(["branch"=>$branch,"partner"=>$partner]);
        return view('hashtag');

    }
    
    function LoadPage($projectCode=null,$search_states=null){
        //return view('project_report');//->with(["branch"=>$branch,"partner"=>$partner]);
        //return view('project_report')->with(["param_pj_code"=>$projectCode,"search_states"=>json_decode($search_states)]);
        $common = new CommonModel();
        $report_category = $common->GetReportCategory();
        return view('project_report')->with(["param_pj_code"=>$projectCode,"search_states"=>json_decode($search_states),"report_category"=>$report_category]);

    }
    
    
    function LoadTempPage($projectCode=null,$search_states=null){
        $common = new CommonModel();
        $report_category = $common->GetReportCategory();
        return view('project_report_temp')->with(["param_pj_code"=>$projectCode,"search_states"=>json_decode($search_states),"report_category"=>$report_category]);//->with(["branch"=>$branch,"partner"=>$partner]);
    }
    

    function UploadImagesIndex(){
        $directory = "/var/www/html/iPD/public/ReportImages/";
        $files = array_diff(scandir($directory), array('.', '..'));
        //$files = scandir($directory);
        //$files = $this->GetFilesFromBox("","get_all_item");
        return view("upload_report_images");
    }
    
    function GetData(Request $request){
        $message = $request->get("message");
        if($message == "get_all_report"){
            $order_status = $request->get("status_order");
            $common = new CommonModel();
            $result = $common->GetAllProjectReport($order_status);
            return $result;
        }else if($message == "get_report_byname"){
            $projectName = $request->get('name');
            $projectCode = $request->get('projectCode');
    		try{
    		    $common = new CommonModel();
    		    $result = $common->GetProjectReportByName($projectCode);
    		    return $result;
    		}catch(Exception $e) {
    			echo $e->getMessage();
    		}
        }else if($message == "get_report_byname_temp"){
            $projectName = $request->get('name');
            $projectCode = $request->get('projectCode');
    		try{
    		    $common = new CommonModel();
    		    $result = $common->GetProjectReportByNameTemp($projectCode);
    		    return $result;
    		}catch(Exception $e) {
    			echo $e->getMessage();
    		}
        }else if($message == "get_report_byweekly"){//not using in now
            
    		try{
    		  //  $projectName = $request->get('name');
    		  //  $date = $request->get('date');
    		  //  $projectCode = $request->get('projectCode');
    		  //  $common = new CommonModel();
    		  //  $result = $common->GetProjectReportByWeekly($projectCode,$date);
    		  //  return $result;
    		}catch(Exception $e) {
    			echo $e->getMessage();
    		}
        }else if($message == "get_report_prevReport"){//not using in now
            try{
    		  //  $projectName = $request->get('name');
    		  //  $projectCode = $request->get('projectCode');
    		  //  $common = new CommonModel();
    		  //  $result = $common->GetProjectReportOfPreviousWeek($projectCode);
    		  //  return $result;
    		}catch(Exception $e) {
    			echo $e->getMessage();
    		}
        }else if($message == "get_all_report_by_branch_order"){
            $common = new CommonModel();
            $result = $common->GetAllProjectReportByBranchOrder();
            return $result;
        }else if($message == "get_allstore_projectcode"){
            $common = new CommonModel();
            $result = $common->GetAllstoreProjectCode();
            return $result;
        }else if($message == "get_project_sepcial_feature_byPrjCode"){
            $common = new CommonModel();
            $projectCode = $request->get('projectCode');
            $result = $common->GetProjectSpecialFeature($projectCode);
            return $result;
        }else if($message == "get_file_link"){
            $common = new CommonModel();
            $projectCode = $request->get('projectCode');
            $result = $common->GetFileLink($projectCode);
            return $result;
        }else if($message == "get_current_week_report_history"){
            $common = new CommonModel();
            $projectCode = $request->get('projectCode');
            $tblName = $request->get('tblName');
            $order = $request->get('order_list');
            $result = $common->GetCurrentWeekReportHistory($projectCode,$tblName,$order);
            return $result;
        }else if($message == "get_hashtags"){
            $common = new CommonModel();
            $result = $common->GetHashtags();
            return $result;
        }
    }
    
    function SaveInfo(Request $request){
        if(isset($request->message)){
            $message = $request->get("message");
            if($message == "save_info"){
                $save_info = json_decode($request->get('save_info'));
                $commonModel = new CommonModel();
                try{
                    $result = $commonModel->SaveProjectReportInformation($save_info);
                    return $result;
                }catch(Exception $e){
                    return $e->getMessage();
                }
                
            }else if($message == "save_info_temp"){
                
                $save_info = json_decode($request->get('save_info'));
                $commonModel = new CommonModel();
                try{
                    $result = $commonModel->SaveProjectReportInformationTemp($save_info);
                    return $result;
                }catch(Exception $e){
                    return $e->getMessage();
                }
            }else if($message == "update_project_status"){
                $projectCode = $request->get("projectCode");
                $projectName = $request->get("projectName");
                $chk_ji = $request->get("chk_ji");
                $chk_ki = $request->get("chk_ki");
                $chk_hou = $request->get("chk_hou");
                $chk_tou = $request->get("chk_tou");
                $chk_special = $request->get("chk_special");
                $txtHai = $request->get("txtHai");
                try{
                    $commonModel = new CommonModel();
                    $result = $commonModel->UpdateReportProjectStates($projectCode,$projectName,$chk_ji,$chk_ki,$chk_hou,$chk_tou,$chk_special,$txtHai);
                    return $result;
                }catch(Exception $e){
                    return $e->getMessage();
                }
            }else if($message == "save_special_feature_info"){
                $projectCode = $request->get("projectCode");
                $projectName = $request->get("projectName");
                $special_feature_info = $request->get("special_feature");
                 try{
                    $commonModel = new CommonModel();
                    $result = $commonModel->SaveReportSepcialFeature($projectCode,$projectName,$special_feature_info);
                    return $result;
                }catch(Exception $e){
                    return $e->getMessage();
                }
            }else if($message == "save_file_link"){
                $projectCode = $request->get("projectCode");
                $projectName = $request->get("projectName");
                $icon_name = $request->get("icon_name");
                $file_link = $request->get("file_link");
                 try{
                    $commonModel = new CommonModel();
                    $result = $commonModel->SaveReportFileLink($projectCode,$projectName,$icon_name,$file_link);
                    return $result;
                }catch(Exception $e){
                    return $e->getMessage();
                }
            }else if($message == "update_image_type"){
                $projectCode = $request->get("projectCode");
                $elementId = $request->get("elementId");
                $selectedType = $request->get("selectedType");
                 try{
                    $commonModel = new CommonModel();
                    $result = $commonModel->UpdateImageType($projectCode,$elementId,$selectedType);
                    return $result;
                }catch(Exception $e){
                    return $e->getMessage();
                }
            }else if($message == "update_report_isshow_flag"){
                $param_id = $request->get("param_id");
                $flag = $request->get("flag");
                $tblName = $request->get("tblName");
                 try{
                    $commonModel = new CommonModel();
                    $result = $commonModel->UpdateReportisShowFlag($param_id,$flag,$tblName);
                    return $result;
                }catch(Exception $e){
                    return $e->getMessage();
                }
            }else if($message == "add_report_category"){
                try{
                    $new_report_category = $request->get("new_report_category");
                    $commonModel = new CommonModel();
                    $result = $commonModel->AddReportCategory($new_report_category);
                    return $result;
                }catch(Exception $e){
                    return $e->getMessage();
                }
            }
        }
    }
    
    function DeleteCapture(Request $request){
        $message = $request->get("message");
        $projectCode = $request->get("projectCode");

        if($message == "delete_capture"){
            $path = $request->get("filePath");
            $path = "/var/www/html".$path;
            if(is_file($path)){
                unlink($path);
                //$this->SetDownloadFileToSession();
            }
        }
        $captures = $this->GetCaptureImages($projectCode);
        return array("capture_files"=>$captures);
    }
    
    function UploadReportImage(Request $request){
         try{
            $message = $request->get("message");
            if($message=="upload_image"){
                $uploadFiles =json_decode($request->get("fileNames"));
                $tempPath = "/var/www/html/iPD/public/Upload/";
                $uploadPath = "/var/www/html/iPD/public/ReportImages/";
                foreach($uploadFiles as $fileName){
                    if(is_file($tempPath.$fileName)){
                        rename($tempPath.$fileName,$uploadPath.$fileName);//move upload files
                    }
                }
                //return $uploadFiles;
                //$this->SetDownloadFileToSession();
            }else if($message == "delete_image"){
                $fileName = $request->get("fileName");
                $path = "/var/www/html/iPD/public/ReportImages/".$fileName;
                if(is_file($path)){
                    unlink($path);
                    //$this->SetDownloadFileToSession();
                }
            }
            
            return "success";
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    function UploadCapture(Request $request){
        try{
            $message = $request->get("message");
            if($message=="upload_capture"){
                $image =$request->get("image");
                
                $projectCode = $request->get("projectCode");
                $image = explode(";",$image)[1];
                $image = explode(",",$image)[1];
                $image = str_replace(" ","+",$image);
                $image = base64_decode($image);
                $img_file_name = $projectCode."_".date("Y-m-d_His").".jpeg";
                $url = "/var/www/html/iPD/public/capture/".$img_file_name;
                file_put_contents($url,$image);
                $imagelist = $this->GetCaptureImages($projectCode);
            }
            return array("capture_files"=>$imagelist);
        }catch(Exception $e){
            return $e->getMessage();
        }
       
    }
    
    function RenameCapture(Request $request){
        $message = $request->get("message");
        if($message == "archive_capture"){
            $projectCode = $request->get("projectCode");
            $newFileName = $request->get("newFileName");
            $oldFileName = $request->get("oldFileName");
            $capture_path = "/var/www/html/iPD/public/capture/";
            if(strstr($oldFileName,"_archive") == false)
                rename($capture_path.$oldFileName,$capture_path.$newFileName);
            $captures = $this->GetCaptureImages($projectCode);
            return array("capture_files"=>$captures);
        }
    }
    
    function GetCaptureImages($projectCode){
        $fileList = array();
        $directory = "/var/www/html/iPD/public/capture/";
        $files = array_diff(scandir($directory), array('.', '..'));
        foreach($files as $file){
            $current_flie_name = basename($file);
            //return $projectCode;
            if(strpos($current_flie_name, $projectCode) !== false){
                array_push($fileList,$current_flie_name);
            }
        }
        return $fileList;
    }

    function GetImagesByProjectCode(Request $request){
        $message = $request->get("message");
        
        if($message == "get_images"){
            $projectCode = $request->get("projectCode");
            try{
            //$folderId = "133474774006";//案件報告
            //$image_folderId = "131808756610";//資料/ccc取り込み/画像
            $access_token = session('access_token');
            $imagelist = $this->GetFilesFromBox($projectCode,"description_images");
            $mydomain_images = $this->GetFileFromCurrentDomain($projectCode);

            return array("description_images"=>$imagelist,"current_domain_images"=>$mydomain_images);
            }catch(Exception $e){
                return back()->with("error");
            }
            
        }else if($message == "get_capture_images"){
            try{
                $projectCode = $request->get("projectCode");
                $captures = $this->GetCaptureImages($projectCode);
                return array("capture_files"=>$captures);
            }catch(Exception $e){
                    return back()->with("error");
            }
            
        }else{
            $directory = "/var/www/html/iPD/public/ReportImages/";
            $files = array_diff(scandir($directory), array('.', '..'));
            $perspective_images=array();
            $normal_images=array();
            foreach($files as $fileName){
                $fileNameWithoutExt = preg_replace("/\.[^.]+$/", "", $fileName);
                //return $fileName;
                if(strpos($fileNameWithoutExt, $projectCode)  !== false){
                    //return $fileName;
                    if(strpos($fileNameWithoutExt, "_perspective_") !== false){
                        array_push($perspective_images,$fileName);
                    }else{
                        array_push($normal_images,$fileName);
                    }
                }
            }
           // return $projectCode;
            
            return array("image1"=>$perspective_images,"image2"=>$normal_images);
        }
    }
    
    function GetFilesFromBox($pj_code,$status){
       try{ 
            $access_token = session('access_token');
            $folderId ="131808756610";//ccc取り込み/画像
            $file_list=array();
            $tempFile_list=array();
            $client = new \GuzzleHttp\Client();
            $header =[
                "Authorization" => "Bearer ".$access_token,
                "Accept" => "application/json",
                "Access-Control-Allow-Origin" => "*"
                ];
            $requestURL = "https://api.box.com/2.0/folders/".$folderId."/items/?limit=1000";
            $response = $client->request('GET', $requestURL,['headers' => $header ]);
            $files = $response->getBody()->getContents();
            $files = json_decode($files)->entries;
            //return $files;
            foreach($files as $file){
               
                if($file->type == "file"){
                    $fileId = $file->id;
                    $fileName = $file->name;
                    $tempName = $fileId."-".$pj_code."-".$fileName;//for downloaded file delete
                    array_push($tempFile_list,$tempName);
                    //$ur = $file->shared_link->url;
                    //print_r($url);
                    if((strstr($fileName,$pj_code."_") == true) && $pj_code != ""){
                        if($status == "captures"){//upload capture to box but not using now bcoz it take time
                            $file_info_url = "https://api.box.com/2.0/files/".$fileId."?fields=representations";
                            $res_header = ["Authorization" => "Bearer ".$access_token,"x-rep-hints"=>"[jpg?dimensions=2048x2048]"];
                            $response = $client->request('GET', $file_info_url,['headers' => $res_header ]);
                            $fileInfo = $response->getBody()->getContents();
                            if($fileInfo != ''){
                                $file_obj = json_decode($fileInfo);
                                if(isset( $file_obj->representations)){
                                    $entries =$file_obj->representations->entries[0];
                                    $fileUrl = $entries->content->url_template;
                                    $fileUrl = str_replace('{+asset_path}','1.jpg',$fileUrl);
                                     array_push($file_list,$fileName."=".$fileId."=".$fileUrl);
                                }
                            }
                       
                        }else{
                               $this->DownloadBoxFilesToCurrentDomain($client,$header,$pj_code,$fileId,$fileName);
                               array_push($file_list,$fileName."=".$fileId); 
                        }
                    }else if($pj_code == "" && $status == "get_all_item"){
                         array_push($file_list,$fileName); 
                    }
                }
            }
            
         if($pj_code !== "" && $status !== "get_all_item"){//its work if pj_code exist  
            $this->DeleteTempDownloadedFileFromCurrentDomain($tempFile_list,$pj_code);
         }
        
        return $file_list;
       }catch(Exception $e){
          return back()->with("error",$e->getMessage());
       }
    }
    
    function GetFileFromCurrentDomain($projectCode){
        $fileList = array();
        $directory = "/var/www/html/iPD/public/Download/";
        $files = array_diff(scandir($directory), array('.', '..'));
        foreach($files as $file){
            $current_flie_name = basename($file);
            //return $projectCode;
            if(strpos($current_flie_name, $projectCode) !== false){
                array_push($fileList,$current_flie_name);
            }
        }
        return $fileList;
    }
    
    function DownloadBoxFilesToCurrentDomain($client,$header,$pj_code,$fileId,$fileName){
        try{
            $current_domain_downloaded_file = $this->GetFileFromCurrentDomain($pj_code);
            $tempName = $fileId."-".$pj_code."-".$fileName;
            if(!in_array($tempName,$current_domain_downloaded_file)){
                $requestURL = "https://api.box.com/2.0/files/".$fileId."/content/";
                $response = $client->request('GET', $requestURL,['headers' => $header ]);
                $file_content = $response->getBody()->getContents();
                $filePath= "/var/www/html/iPD/public/Download/";
                $tempName = $fileId."-".$pj_code."-".$fileName;
                file_put_contents($filePath.$tempName, $file_content);
            }
            
        }catch(Exception $e){
            return $e->getMessage();
        }
                        
    }
    
    function DeleteTempDownloadedFileFromCurrentDomain($temp_file_list,$pj_code){
        $current_domain_downloaded_file = $this->GetFileFromCurrentDomain($pj_code);
        $deleted_file_list = array_diff($current_domain_downloaded_file,$temp_file_list);
        foreach($deleted_file_list as $delFile){
            $basic_path = "/var/www/html/iPD/public/Download/";
            unlink($basic_path.$delFile);
        }
    }
    
    function UploadFileToBox(Request $request){
        
        try{//$img_file_name = "PJ20080882_2021-03-22.jpeg";
        if($request->has('file')){
            
            $upload_files = $request->file("file");
            $fileCount = 0;
            $old_new_filename_pair = get_object_vars(json_decode($request->get("old_new_file_pair")));
            foreach($upload_files as $upload_file){
                try{
                    $fileCount++;
                    $org_name = $upload_file->getClientOriginalName();
                    
                    $img_file_name = $old_new_filename_pair[$org_name];

                    $access_token = session('access_token');

                    $folderId ="131808756610";//ccc取り込み/画像
                    $request_url = 'https://upload.box.com/api/2.0/files/content';
                    $json = json_encode(array('name' => $img_file_name,'parent' => array('id' => $folderId) ));

                    $params = array('attributes' => $json,'file'=>new \CurlFile($upload_file,"image/jpeg",$img_file_name));
                    $headers =array("Authorization:Bearer ".$access_token,"Content-Type:multipart/form-data");

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_HEADER, 1);
                    curl_setopt($ch, CURLOPT_URL, $request_url);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    $response = curl_exec($ch);
                    $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    $errNo = curl_errno($ch);
                    $errStr = curl_error($ch);
                    curl_close($ch);
                    if($responseCode !== 201){
                        return $responseCode."failed when uploading file to box";
                    }else{
                        if($responseCode == 201 && $fileCount == sizeof($upload_files)){
                            $file_list = $this->GetFilesFromBox($folderId,$access_token,"","get_all_item");
                            return view("upload_report_images")->with(["files"=>$file_list]);
                        }
                    }
                    
                }catch(Exception $e){
                    return $e->getMessage();
                }
            }
            
        }else{
            return "else";
        }
        

        /*$requestURL = "https://upload.box.com/api/2.0/files/content";
        $client = new \GuzzleHttp\Client();
      
        $response = $client->request('POST', $requestURL,["form_params"=>$params,"headers"=>$headers]);*/
       
        }catch(\GuzzleHttp\Exception\RequestException $e){
             $guzzleResult = $e->getResponse();
             return ($guzzleResult);
        }
    }
    
    function GetReportImageFromBox(Request $request){
        $message = $request->get("message");
        if($message == "get_all_item"){
            $result = $this->GetFilesFromBox("","get_all_item");
            return array("report_images"=>$result);
        }
        
        
    }
}
