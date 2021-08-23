<?php

namespace App\Http\Controllers;
use App\Models\CommonModel;
use App\Models\ForgeModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class OBJController extends Controller
{
   function ShowPage(){
       $directory = "/var/www/html/iPD/public/RVT/";
       $files = array_diff(scandir($directory), array('.', '..'));
       return view('OBJConvert')->with(["RvtFiles"=>$files]);;
   }
   
   function Upload(Request $request){
       if(!isset($request->message)){
              $files = $request->file('files');
              if($request->hasFile('files')) {
    
                foreach ($files as $file) {
                    $name = $file->getClientOriginalName();
                    $file->move('/var/www/html/iPD/public/RVT/', $name);
                }
              }
                //return back()->with('Success!','Data Added!');
               
         }
   }
   
    function ConvertOBJ(Request $request){
         $message = $request->get('message');
        if($message == "OBJ"){
            $urn = $request->get('forge_id');//'urn:adsk.wipprod:fs.file:vf.WYZOewvwRQ2RAJ_KBKWk5g?version=35'
            $base64Urn = rtrim( strtr( base64_encode( $urn ), '+/', '-_' ), '=' );
            $conf = new \Autodesk\Auth\Configuration();//escape from current name space by using '/'
            $conf->getDefaultConfiguration()
            ->setClientId('WKK1H6ryRiVP3TnN0enkZ4dRgen0Gedg')//admin account client id and secrect key
            ->setClientSecret('gcnPNhXtjith3dC3');
            //->setClientId('Mt1Tul68redoV5OEMKwRh1aYQnsdmtJW')
            //->setClientSecret('8FOuOTPK6nOp4bOl');
    try {
            $authObj = new \Autodesk\Auth\OAuth2\TwoLeggedAuth();
            $scopes = array("code:all","data:read","data:write","bucket:read");
            $authObj->setScopes($scopes);
    
            $authObj->fetchToken();
            $access_token = $authObj->getAccessToken();
            $authObj->setAccessToken($access_token);
            $derivInst = new \Autodesk\Forge\Client\Api\DerivativesApi($authObj);
            
             $metaDataObj = $derivInst->getMetadata($base64Urn,null);
             $views = $metaDataObj["data"]["metadata"];
             $objectIds = array();
             $guid="";
             foreach($views as $v){
                 $role = $v["role"];
                 if($role != "3d")continue;
                 $guid = $v["guid"];
                 //$properties = $derivInst->getModelviewProperties($base64Urn,$guid,null);
                 $allProperties = $derivInst->getModelviewPropertiesByZip(base64_encode($urn),$guid,"gzip");
                 $properties = json_decode($allProperties,true);//convert json string to object array
        
                 $allProperties = isset($properties['data']['collection']) ?$properties['data']['collection'] :"";//re-use varibale for memory up within process
                 //$allProperties = isset($properties['data']['collection']) ?$properties['data']['collection'] :null; 
                unset($properties);
                clearstatcache();
                gc_collect_cycles();
               
                foreach($allProperties as $pro){
                    $objectId = $pro["objectid"];
                    array_push($objectIds,$objectId);
                }
                
             }
             unset($allProperties);
             clearstatcache();
             gc_collect_cycles();
             if(sizeof($objectIds) > 0){
                 $this->TranslateToOBJ($derivInst,$objectIds,$base64Urn,$guid);
             }
                
           
            
        //}
    } catch (Exception $e) {
            echo 'Exception when calling forge library function : ', $e->getMessage(), PHP_EOL;
        }
   }
   }
   
    function TranslateToOBJ($apiInstance,$objectIds,$base64Urn,$guid){
       $jobInput = array('urn' => $base64Urn);
        $jobPayloadInput = new \Autodesk\Forge\Client\Model\JobPayloadInput( $jobInput );
        
        $jobOutputItem = array('type' => 'obj','advanced' => array('modelGuid' =>$guid,'objectIds'=>$objectIds));
        $jobPayloadItem = new \Autodesk\Forge\Client\Model\JobPayloadItem( $jobOutputItem );
        
        $jobOutput = ['formats' => array( $jobPayloadItem )];
        $jobPayloadOutput = new \Autodesk\Forge\Client\Model\JobPayloadOutput( $jobOutput );
        
        $job = new \Autodesk\Forge\Client\Model\JobPayload();
        $job->setInput( $jobPayloadInput );
        $job->setOutput( $jobPayloadOutput );
        $overwrite = true;
        $result = $apiInstance->translate( $job, $overwrite );
        $result = $apiInstance->getManifest( $base64Urn, null );
        $status = $result["status"];
        $progress = $result["progress"];
      while($status != "success" && $progress != "complete"){
          $result = $apiInstance->getManifest( $base64Urn, null );
          $status = $result["status"];
          $progress = $result["progress"];
      }
      
      $this->DownloadSVF($result,$apiInstance,$base64Urn,$fileName);
   }
   
    function ChangeOBJ($name){
        
        //$message = $request->get('message');
        //if($message == "OBJ"){
        $fileName=$name;//"SF3D20201130.rvt"
         if(!is_file("/var/www/html/iPD/public/RVT/".$fileName))
            return"No file exist!";
           // $fileName = $request->get('fileName');
             //two legged authentication 
            $conf = new \Autodesk\Auth\Configuration();//escape from current name space by using '/'
            $conf->getDefaultConfiguration()
            //->setClientId('WKK1H6ryRiVP3TnN0enkZ4dRgen0Gedg')//admin account client id and secrect key
            //->setClientSecret('gcnPNhXtjith3dC3');
             ->setClientId('LHrkbXlgUiwuHZD9SGkAOMg8lrLvxfY0')
            ->setClientSecret('m3qAIjtSQOS9xRbJ');//bim360local App
    
            $authObj = new \Autodesk\Auth\OAuth2\TwoLeggedAuth();
            $scopes = array("code:all","data:read","data:write","bucket:read","bucket:create");
            $authObj->setScopes($scopes);
    
            $authObj->fetchToken();
            $token = $authObj->getAccessToken();
            //print_r($token);return;
            
            $apiInstance = new \Autodesk\Forge\Client\Api\BucketsApi($authObj);
            $result = $apiInstance->getBuckets();
            //print_r($result);return;
            if(isset($result["items"]) && $result["items"] == "" || $result["items"] == null){
                //create bucket
                $this->CreateBucket($apiInstance);
            }else{
              
                $data_array = $result["items"];
                foreach($data_array as $bucket){
                   $bucketKey = $bucket["bucket_key"];
                   if($bucketKey != "mybucket_ipd")continue;
                   
                        $objectInstance = new \Autodesk\Forge\Client\Api\ObjectsApi($authObj);

                        $this->UploadFile($authObj,$objectInstance,$bucketKey,$fileName);
                        
                        
                }
                
            }
          
            
        //}
    }
    
    function ChangeOBJ2(Request $request){
        
        $message = $request->get('message');
        if($message == "OBJ"){
            $fileNames = json_decode($request->get('fileName'));
            $fileName = $fileNames[0];
           if(!is_file("/var/www/html/iPD/public/RVT/".$fileName))
            return"Not file exist!";
           
             //two legged authentication 
            $conf = new \Autodesk\Auth\Configuration();//escape from current name space by using '/'
            $conf->getDefaultConfiguration()
            //->setClientId('WKK1H6ryRiVP3TnN0enkZ4dRgen0Gedg')//admin account client id and secrect key
            //->setClientSecret('gcnPNhXtjith3dC3');
             ->setClientId('LHrkbXlgUiwuHZD9SGkAOMg8lrLvxfY0')
            ->setClientSecret('m3qAIjtSQOS9xRbJ');//bim360local App
    
            $authObj = new \Autodesk\Auth\OAuth2\TwoLeggedAuth();
            $scopes = array("code:all","data:read","data:write","bucket:read","bucket:create");
            $authObj->setScopes($scopes);
    
            $authObj->fetchToken();
            $token = $authObj->getAccessToken();
            //print_r($token);return;
            
            $apiInstance = new \Autodesk\Forge\Client\Api\BucketsApi($authObj);
            $result = $apiInstance->getBuckets();
            //print_r($result);return;
            if(isset($result["items"]) && $result["items"] == "" || $result["items"] == null){
                //create bucket
                $this->CreateBucket($apiInstance);
            }else{
              
                $data_array = $result["items"];
                foreach($data_array as $bucket){
                   $bucketKey = $bucket["bucket_key"];
                   if($bucketKey != "mybucket_ipd")continue;
                   
                        $objectInstance = new \Autodesk\Forge\Client\Api\ObjectsApi($authObj);

                        $this->UploadFile($authObj,$objectInstance,$bucketKey,$fileName);
                        
                        
                }
                
            }
          
            
        }
    }
    
    function CreateBucket($instance){
        $body_structure = [ 'bucketKey' => 'mybucket_test','access' => 'full','policyKey' => 'transient'];
        $post_buckets = new \Autodesk\Forge\Client\Model\PostBucketsPayload($body_structure); // \Autodesk\Forge\Client\Model\PostBucketsPayload | Body Structure
        //print_r($post_buckets);
        $x_ads_region = "US"; // string | The region where the bucket resides Acceptable values: `US`, `EMEA` Default is `US`
        
        try {
            $result = $instance->createBucket($body_structure);
            //print_r($result);return;
        } catch (Exception $e) {
            echo 'Exception when calling BucketsApi->createBucket: ', $e->getMessage(), PHP_EOL;
        }
    }
    
    function UploadFile($authObj,$apiInstance,$bucketKey,$fileName){
        $filename = "/var/www/html/iPD/public/RVT/".$fileName;
        //print_r($filename);
        $bucket_key = $bucketKey;  //!<< The BucketKey of the bukcet where files/objects will be uploaded to.
        $body = file_get_contents($filename);
        //$file = new \SplFileObject( $body );
        $fileHandle = fopen($filename, 'r+');
        $content_length = filesize($filename);   //!<< Indicates the size of the request body.
        $object_name = $fileName;  //!<< URL-encoded object name

        try {
            //$existingObject = $apiInstance->getObjectDetails($bucket_key, $object_name, null, null);
            
            $existingObject = $apiInstance->getObjects($bucket_key, null, null, null);

            $alreadyUpload = false;
            //$rvtObject = "";
            foreach($existingObject["items"] as $obj){
               
                $object_key = $obj["object_key"];
                //$apiInstance->deleteObject($bucket_key, $object_key);continue;
                 
                if($object_key == $object_name){
                    //$rvtObject = $obj;
                    $alreadyUpload = true;
                    $this->TranslateFile($authObj,$obj,$fileName);
                    
                    break;
                }
            }

            if(!$alreadyUpload){
                
                $result = $apiInstance->uploadObject( $bucket_key, $object_name, $content_length, $body, null, null );
                //print_r( $result ); return; //!<< Print file/object upload response from the Data Management API.
                $this->TranslateFile($authObj,$result,$filename); 
            }

        } catch( Exception $e ) {
            echo 'Exception when calling ObjectsApi->uploadObject: ', $e->getMessage(), PHP_EOL;
        }

    }
    
    function TranslateFile($authObj,$object,$fileName){
        $apiInstance = new \Autodesk\Forge\Client\Api\DerivativesApi( $authObj );

        $urn = $object["object_id"];
        $base64Urn = rtrim( strtr( base64_encode( $urn ), '+/', '-_' ), '=' );

        $jobInput = array('urn' => $base64Urn);
        $jobPayloadInput = new \Autodesk\Forge\Client\Model\JobPayloadInput( $jobInput );
        
        $jobOutputItem = array('type' => 'svf','views' => array('2d','3d'));
        $jobPayloadItem = new \Autodesk\Forge\Client\Model\JobPayloadItem( $jobOutputItem );
        
        $jobOutput = ['formats' => array( $jobPayloadItem )];
        $jobPayloadOutput = new \Autodesk\Forge\Client\Model\JobPayloadOutput( $jobOutput );
        
        $job = new \Autodesk\Forge\Client\Model\JobPayload();
        $job->setInput( $jobPayloadInput );
        $job->setOutput( $jobPayloadOutput );
        $x_ads_force = true;   //!<<`true`: the endpoint replaces previously translated output file types with the newly generated derivatives,  `false` (default): previously created derivatives are not replaced
       // print_r($job);return;
        try {
            
            $result = $apiInstance->translate( $job, $x_ads_force );

            //$base64Urn = base64_encode($result["urn"]);//translated urn
            $this->CheckTranslation($apiInstance,$base64Urn,$fileName);
        } catch( Exception $e ) {
            echo 'Exception when calling DerivativesApi->translate: ', $e->getMessage(), PHP_EOL;
        }
    }
    
    function CheckTranslation($apiInstance,$base64Urn,$fileName){
       try {
          //$result = $apiInstance->deleteManifest($base64Urn);return;
        
          $result = $apiInstance->getManifest( $base64Urn, null );
          if(isset($result["status"])){
              //if($result["status"] == "success")
               // $result = $apiInstance->deleteManifest($base64Urn);
          }


          $status = $result["status"];
          $progress = $result["progress"];
          while($status != "success" && $progress !== "complete"){
              $result = $apiInstance->getManifest( $base64Urn, null );
              $status = $result["status"];
              $progress = $result["progress"];
          }


          if($result){

                $result = $apiInstance->getMetadata($base64Urn);
               
                $data = $result["data"];
                $metaData = $data["metadata"];

                $guid = "";
                foreach($metaData as $meta){
                    if($meta["role"] == "3d"){
                        $guid = $meta["guid"];
                        break;
                    }
                }

                if($guid != ""){
                    $result = $apiInstance->getMetadata($base64Urn, $guid, null);
                    $data = $result["data"];
                    $metaData = $data["metadata"];
                   
                    foreach($metaData as $meta){
                    if($meta["role"] == "3d"){
                        $guid = $meta["guid"];
                        break;
                    }
                    }
                }
                
                if($guid != ""){
                    
                    $objectIds = array();
                   #getModelViewMetada 
                   $properties = $apiInstance->getModelviewMetadata($base64Urn,$guid,null);

                    //if(!isset($properties['data']['objects']))continue;
                    $collection = (isset($properties['data']['objects'])) ? $properties['data']['objects'] :"";
                    unset($properties);
                    clearstatcache();
                    gc_collect_cycles();
                 /*echo"<pre>";
                 print_r($collection);
                 echo"</pre>";return;*/
                    foreach($collection as $vData){                    
                        $categoris = $vData['objects'];
                        foreach($categoris as $category){
                            //$type_ids = array();
                            $category_name = $category['name'];
                             
                            if($category_name == "構造柱" ||$category_name=="柱" ){   //  
                                //print_r($category_name);          
                                $materials = $category["objects"];                              
                                foreach($materials as $material){
                                    $types = $material['objects'];                                  
                                    foreach($types as $type){ 
                                        $type_pro = $type['objects'];
                                        foreach($type_pro as $property) {
                                            $typeID = $property['objectid'];
                                            //if(sizeof($objectIds) == 12000)break;
                                            array_push($objectIds,$typeID);   
                                        }                                                                                                                                                                                         
                                   }
                                }
                               //break; 
                            }
                        }                            
                    }
                    #end
                     
                                     
                    #start get ModelProperties
                    /*if(sizeof($objectIds) > 0){
                        $properties = $apiInstance->getModelviewPropertiesByZip($base64Urn, $guid, "gzip");//getModelviewMetadata
                        $property = json_decode($properties,true);//convert json string to object array
    
                        $collection = isset($property['data']['collection']) ?$property['data']['collection'] :"";//re-use varibale for memory up within process
                        unset($property['data']);
                        //$collection = $properties["data"]["collection"];
                        //unset($properties['data']);
                        clearstatcache();
                        gc_collect_cycles();
                        $objectArray=array();
                        foreach($objectIds as $objID){
                            foreach($collection as $data){
                                $objectId = $data["objectid"];
                                if($objectId != $objID || !isset($data["properties"]["拘束"]))continue;
                                $kousoku = json_decode(json_encode($data["properties"]["拘束"]),true);
                                $level = isset($kousoku["基準レベル"]) ? $kousoku["基準レベル"] : "";
                                //$tmpArr = array();
                                if($level != ""){
                                    if(isset($objectArray[$level])){
                                        array_push($objectArray[$level],$objectId);
                                    }else{
                                        $objectArray[$level] =array($objectId);
                                    }
                                }
                                    
                                //$objectArray[$level] = isset($objectArray[$level]) ? array_push(($objectArray[$level]),$objectId) : array($objectId);
                                
                            
                            }
                        }
                        
    
                        unset($collection);
                        clearstatcache();
                        gc_collect_cycles();
                    }*/
                    
                    #end
                    /*echo"<pre>";
                    print_r(($objectArray));
                    echo"</pre>";return;*/

                    if(sizeof($objectIds) > 0){

                        //foreach($objectArray as $level=>$objectIds){
                            //$count++;
                            //if($count == 2)break;
                            $jobInput = array('urn' => $base64Urn);
                            $jobPayloadInput = new \Autodesk\Forge\Client\Model\JobPayloadInput( $jobInput );
                            
                            $jobOutputItem = array('type' => 'obj','advanced' => array('modelGuid' =>$guid,'objectIds'=>$objectIds));//'distance unit'=>array("value"=>"mm")
                            $jobPayloadItem = new \Autodesk\Forge\Client\Model\JobPayloadItem( $jobOutputItem );
                            
                            $jobOutput = ['formats' => array( $jobPayloadItem )];
                            $jobPayloadOutput = new \Autodesk\Forge\Client\Model\JobPayloadOutput( $jobOutput );
                            
                            $job = new \Autodesk\Forge\Client\Model\JobPayload();
                            $job->setInput( $jobPayloadInput );
                            $job->setOutput( $jobPayloadOutput );
                            $overwrite = true;
                            $result = $apiInstance->translate( $job, $overwrite );
                            
                            $result = $apiInstance->getManifest( $base64Urn, null );
    
                            $status = $result["status"];
                            $progress = $result["progress"];
                          while($status != "success" && $progress != "complete"){
                              $result = $apiInstance->getManifest( $base64Urn, null );
                              $status = $result["status"];
                              $progress = $result["progress"];
                          }
                          
                          $this->DownloadSVF($result,$apiInstance,$base64Urn,$fileName);//."_".$level
    
                        //}
                        
                        //$this->DownloadToBrowserByZip($fileName);
                        
                    }
                    
                }//end of $guid != ""
                    
          }//end of result !=""

        } catch( Exception $e ) {
          echo 'Exception when calling DerivativesApi->getManifest: ', $e->getMessage(), PHP_EOL;
        }
    }
    
    function DownloadSVF($translatedData,$apiInstance,$base64Urn,$fileName){

        $derivatives = $translatedData["derivatives"];
        $derivative_urn = "";
        $derivative_urn = $translatedData["urn"];
        
        foreach($derivatives as $derivative){
            $output_type = $derivative["output_type"];
            if($output_type == "obj"){
                $children = $derivative["children"];
                foreach($children as $child){
                    $role = $child["role"];
                    $urn = $child["urn"];
                    if(strpos($urn, ".obj") !== false){
                        $derivative_urn = $child["urn"];
                    }

                }
            } 
        }

        if($derivative_urn != ""){
               
                try {
                  
                    //$result = $apiInstance->getDerivativeManifest( $base64Urn, $derivative_urn);
                   
                    //print_r($result);return;
                     //$result = $apiInstance->getFormats( );
    
                    //print_r(($result));return;
                    $file = "/var/www/html/iPD/OBJFiles/tempfile.txt";
                    $obj_file = "/var/www/html/iPD/OBJFiles/test.obj";//test.obj
                    $resultList= "";
                    $size = 2048*(1024*1024);//512MB//filesize("/var/www/html/iPD/RVT/".$fileName); 
                    $start_range = 0;
                    $end_range=199*(1024*1024);//100MB
                    $result = "";
                    $resultStr="";
                    $count = 0;
                    while($count == 0 || trim($result) != ""){
                        
                        try{
                            //if($count == 1) $end_range="";
                            $result = $apiInstance->getDerivativeManifest( $base64Urn, $derivative_urn,$start_range.'-'.$end_range);//
                            $count++;
                            $start_range += 200*(1024*1024);
                            $end_range += 200*(1024*1024);
                            
                            //if(trim($result) == "") break;
                            if(trim($result) != "")
                            $resultStr = $resultStr.$result;
                            //file_put_contents($file,trim($result).PHP_EOL , FILE_APPEND | LOCK_EX);
                        }catch( Exception $e ){
                            echo $e->getMessage();
                           // break;
                        }
                       
                        
                    }
                   //print_r(($count));return;
                    file_put_contents($file,$resultStr);
                    //file_put_contents($file, $apiInstance->getDerivativeManifest( $base64Urn, $derivative_urn));
                    copy($file,$obj_file);
                    //file_put_contents($obj_file,$resultStr);

                  $this->DownloadToBrowser($obj_file,$fileName);
                } catch( Exception $e ) {
                echo 'Exception when calling getDerivativeManifest->translate: ', $e->getMessage(), PHP_EOL;
            }
        }
    }
    
    function DownloadToBrowserByZip($fileName){
        
        $directory = "/var/www/html/iPD/OBJFiles/";
        $files = array_diff(scandir($directory), array('.', '..'));
        $zipname = 'OBJFiles.zip';
        $zip = new ZipArchive;
        
        $zip->open($zipname, ZipArchive::CREATE);
        foreach($files as $file) {
          $currentName = basename($file);
          
          if(file_exists($file)){
              if(strpos($currentName,$fileName) == true)
                $zip->addFile($directory,iconv("UTF-8","UTF-8",$file));  
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
    }
    
    function DownloadToBrowser($filepath,$fileName){
        $fn = trim(str_replace('.rvt','',$fileName));
        $filename=$fn.".obj";
        //return response()->download($filepath.);
        if(file_exists($filepath)) {
            if(filesize($filepath) <=0 )return "Converting File is too large!";
            /*$mimeType = "application/octet-stream";
            $headers = array(
                'Content-Type' => $mimeType,
                'Content-Length' => filesize($filepath)
            );
            return response()->download($filepath, $filename, $headers);*/
            //return response()->download($filepath, $filename);
            //unlink($filepath);
            //header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.($filename).'"');
            //header('Expires: 0');
            header('Cache-Control: max-age=0');
            //header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            
            flush(); // Flush system output buffer
            readfile($filepath);
            unlink($filepath);
            $fp = fopen("/var/www/html/iPD/OBJFiles/tempfile.txt", "r+");
            // clear content to 0 bits
            ftruncate($fp, 0);
            //close file
            fclose($fp);
            exit;
        }
    }
    
}
