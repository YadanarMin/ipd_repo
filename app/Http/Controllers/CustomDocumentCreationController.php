<?php

namespace App\Http\Controllers;
use App\Models\CommonModel;
use App\Models\ForgeModel;
use App\Models\CustomDocumentModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Mail\ApprovalEmail;

class CustomDocumentCreationController extends Controller
{
   

    function LoadPage(){
        return view('custom_document_creation');
    }
    
    function Save(Request $request){
        $messaage = $request->get('message');
        if($messaage == "save_format"){
            $pageContent = json_encode(json_decode($request->get('jsonData'),true));
            //$fileName = $request->get('fileName');
            
            //$bladeFilePath="/var/www/html/iPD/resources/views/custom_document_creation.blade.php";//public/CustomFormat/
            //$fileName = "カスタム書類作成."txt";
            $filePath = "/var/www/html/iPD/public/CustomFormat/test.json";
            file_put_contents($filePath,($pageContent));
            return (file_get_contents($filePath));
        }
        
    }
    
    function GetData(Request $request){
        $message = $request->get("message");
        if($message == "get_allstore_bycode"){
            try{
                $ipdCode = $request->get("iPDCode");
                $customModel = new CustomDocumentModel();
                $result = $customModel->GetAllstoreProjectByCode($ipdCode);
                return $result;
            }catch(Exception $e){
                return $e;
            }
            
        }else if($message == "get_format_json"){
            $filePath = "/var/www/html/iPD/public/CustomFormat/test.json";
            return (file_get_contents($filePath));
        }
    }
    
    
    function SendMail(){
        
        \Mail::to('yadanar.min.au@obayashi.co.jp')->send(new ApprovalEmail());
 
        session()->flash('success', '送信いたしました！');
        return back();
    }

}