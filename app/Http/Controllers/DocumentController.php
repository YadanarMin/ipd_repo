<?php

namespace App\Http\Controllers;
use App\Http\Controllers\AllstoreController;
use Illuminate\Http\Request;
use App\Models\DocumentModel;
use App\Models\ProjectMgtModel;
use App\Models\CompanyModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PHPExcel;
use PHPExcel_Cell;
use PHPExcel_IOFactory;
use PHPWord;
use PHPWord_IOFactory;
class DocumentController extends Controller 
{
    function index()
    {
        return view('docManagement');
    }

    function indexTest()
    {
        return view('docManagementTest');
    }

    function templateConsole()
    {
        return view('docTemplateConsole');
    }

    function downloadConsole()
    {
        return view('docDownloadConsole');
    }

    function templateConsoleWord()
    {
        return view('docTemplateConsoleWord');
    }

    function downloadConsoleWord()
    {
        return view('docDownloadConsoleWord');
    }
    
    function GetTemplateDataByName($name)
    {
        $query = "SELECT * FROM tb_document_template WHERE name = '$name'";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }

    function GetWordTemplateDataByName($name)
    {
        $query = "SELECT * FROM tb_doc_template_word WHERE name = '$name'";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }
    
    function outputExcelTemplateNew(Request $request)
    {
        $name = $request->get("templateName");
        $pjCode = $request->get("pjCode");
        
        // return $name;

        $result = $this->GetTemplateDataByName($name);
        if($result != null)
        {
            $templateData = $result[0];
            $template_type = $templateData["type"];
            // print_r($result);

            if($template_type == 1){
                return $this->outputExcelTypeSameColumns($templateData);
            }else if($template_type == 2){
                return $this->outputExcelTypeSameRows($templateData);
            }else if($template_type == 3){
                return $this->outputExcelTypeOneProject($templateData, $pjCode);
            }else{
                return 'error:invalid template type'; //NOP
            }
            
        }else{
            return 'error:not found template data';
        }

    }
    
    function outputDefaultExcelTemplate($name)
    {
        $input_file_name="/var/www/html/iPD/app/Exports/UploadedTemplate/".$name;

        //  Read your Excel workbook
        try {
            $input_file_type = PHPExcel_IOFactory::identify($input_file_name);
            $obj_reader = PHPExcel_IOFactory::createReader($input_file_type);
            $excel = $obj_reader->load($input_file_name);
        } catch(Exception $e) {
            die('Error loading file "'.pathinfo($input_file_name,PATHINFO_BASENAME).'": '.$e->getMessage());
        }
        
        $filename = $name;
        
        $writer = PHPExcel_IOFactory::createWriter($excel, "Excel2007");
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"".$filename."\"");
        header('Cache-Control: max-age=0');
        $writer->save("php://output");
    }
    
    function outputExcelTemplate($name)
    {
        $aryName = explode(",", $name);
        if(count($aryName) == 1){
            $result = $this->GetTemplateDataByName($name);
        }else if(count($aryName) == 2){
            $tmpName = $aryName[0];
            $pjCode = $aryName[1];
            $result = $this->GetTemplateDataByName($tmpName);
        }else{
            //NOP
            return;
        }
        // print_r($result);
        
        if($result != null)
        {
            $templateData = $result[0];
            $template_type = $templateData["type"];
            // print_r($result);

            if($template_type == 1){
                $this->outputExcelTypeSameColumns($templateData);
            }else if($template_type == 2){
                $this->outputExcelTypeSameRows($templateData);
            }else if($template_type == 3){
                $this->outputExcelTypeOneProject($templateData, $pjCode);
            }else{
                return; //NOP
            }
            
        }else{
            
            print_r($result);
    
        }
    }
    
    function outputExcelTypeSameRows($templateData)
    {
        $name = $templateData["name"];
        $description = $templateData["name"];
        $file_name = $templateData["file_name"];
        // $file_name = "カスタム_テンプレート_デバッグテスト入力.xlsx";
        $input_file_name="/var/www/html/iPD/app/Exports/UploadedTemplate/".$file_name;

        //  Read your Excel workbook
        try {
            $input_file_type = PHPExcel_IOFactory::identify($input_file_name);
            $obj_reader = PHPExcel_IOFactory::createReader($input_file_type);
            $excel = $obj_reader->load($input_file_name);
        } catch(Exception $e) {
            die('Error loading file "'.pathinfo($input_file_name,PATHINFO_BASENAME).'": '.$e->getMessage());
        }
        
        //template対応表整理
        $template_item = $this->adjustTemplateItem($templateData["item_key"],$templateData["item_val"]);
        if(count($template_item) == 0){
            // print_r($template_item);
            echo "置換文字列読み込みエラー";
            // return 'error:invalid replace string';
            return;
        }

        //PJコードリスト取得
        $pj_code_list = $this->getPjCodesByExcelTemplate($excel);
        if(count($pj_code_list["pj_code"]) == 0){
            // print_r($pj_code_list["pj_code"]);
            echo "テンプレートファイル内にPJコードを見つけられませんでした。</br>";
            echo "テンプレートファイルにPJコードを入力されていない場合、</br>";
            echo "PJコードを入力したテンプレートファイルを再登録してください。";
            // return 'error:PJ code does not exist in template file';
            return;
        }
        
        //全店物件情報取得
        $allstore = new AllstoreController();
        $allstore_data = $allstore->GetDataByPjCodeList($pj_code_list["pj_code"]);
        if($allstore_data == null){
            // print_r($allstore_data);
            echo "PJコードと一致する全店物件情報を取得できませんでした。";
            // return 'error:No matching AllStoreInfo was found for the PJ code';
            return;
        }
        
        // /*echo"<pre>";
        // print_r($allstore_data); 
        // echo"</pre>";return;*/
        // //$allstore_data[0]["プロジェクト名"]["支店"]・・・
        // //$allstore_data[1]["プロジェクト名"]["支店"]・・・
        
        //テンプレートの対応セルに全店物件情報を設定
        $replaceValueList = $this->adjustReplaceList($pj_code_list["pj_code"], $template_item, $allstore_data);
        $set_result = $this->ReplaceCellValueTypeRow($excel, $replaceValueList, $pj_code_list["target_info"]);
        // if($set_result == 0){
        //     echo "ReplaceCellValue error.";
        //     return;
        // }
        
        // $sheet->setTitle(date("Ymd"));
        //出力するファイル名
        $filename = $file_name;
        
        $writer = PHPExcel_IOFactory::createWriter($excel, "Excel2007");
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"".$filename."\"");
        header('Cache-Control: max-age=0');
        $writer->save("php://output");
        
        // return 'success';
    }
    
    function outputExcelTypeSameColumns($templateData)
    {
        $name = $templateData["name"];
        $description = $templateData["name"];
        $file_name = $templateData["file_name"];
        // $file_name = "カスタム_テンプレート_デバッグテスト入力.xlsx";
        $input_file_name="/var/www/html/iPD/app/Exports/UploadedTemplate/".$file_name;

        //  Read your Excel workbook
        try {
            $input_file_type = PHPExcel_IOFactory::identify($input_file_name);
            $obj_reader = PHPExcel_IOFactory::createReader($input_file_type);
            $excel = $obj_reader->load($input_file_name);
        } catch(Exception $e) {
            die('Error loading file "'.pathinfo($input_file_name,PATHINFO_BASENAME).'": '.$e->getMessage());
        }
        
        //template対応表整理
        $template_item = $this->adjustTemplateItem($templateData["item_key"],$templateData["item_val"]);
        if(count($template_item) == 0){
            // print_r($template_item);
            echo "置換文字列読み込みエラー";
            // return 'error:invalid replace string';
            return;
        }

        //PJコードリスト取得
        $pj_code_list = $this->getPjCodesByExcelTemplate($excel);
        if(count($pj_code_list["pj_code"]) == 0){
            // print_r($pj_code_list["pj_code"]);
            echo "テンプレートファイル内にPJコードを見つけられませんでした。</br>";
            echo "テンプレートファイルにPJコードを入力されていない場合、</br>";
            echo "PJコードを入力したテンプレートファイルを再登録してください。";
            // return 'error:PJ code does not exist in template file';
            return;
        }
        
        //全店物件情報取得
        $allstore = new AllstoreController();
        $allstore_data = $allstore->GetDataByPjCodeList($pj_code_list["pj_code"]);
        if($allstore_data == null){
            // print_r($allstore_data);
            echo "PJコードと一致する全店物件情報を取得できませんでした。";
            // return 'error:No matching AllStoreInfo was found for the PJ code';
            return;
        }
        
        //テンプレートの対応セルに全店物件情報を設定
        $replaceValueList = $this->adjustReplaceList($pj_code_list["pj_code"], $template_item, $allstore_data);
        $set_result = $this->ReplaceCellValueTypeColumns($excel, $replaceValueList, $pj_code_list["target_info"]);
        // if($set_result == 0){
        //     echo "置換できる文字列がありませんでした。";
        //     return;
        // }
        // print_r($templateData);
        // return;

        // $sheet->setTitle(date("Ymd"));
        //出力するファイル名
        $filename = $file_name;
        
        $writer = PHPExcel_IOFactory::createWriter($excel, "Excel2007");
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"".$filename."\"");
        header('Cache-Control: max-age=0');
        $writer->save("php://output");
        
        // return 'success';
    }

    function outputExcelTypeOneProject($templateData, $pjCode)
    {
        $name = $templateData["name"];
        $description = $templateData["name"];
        $file_name = $templateData["file_name"];
        // $file_name = "カスタム_テンプレート_デバッグテスト入力.xlsx";
        $input_file_name="/var/www/html/iPD/app/Exports/UploadedTemplate/".$file_name;

        //  Read your Excel workbook
        try {
            $input_file_type = PHPExcel_IOFactory::identify($input_file_name);
            $obj_reader = PHPExcel_IOFactory::createReader($input_file_type);
            $excel = $obj_reader->load($input_file_name);
        } catch(Exception $e) {
            die('Error loading file "'.pathinfo($input_file_name,PATHINFO_BASENAME).'": '.$e->getMessage());
        }
        
        //template対応表整理
        $template_item = $this->adjustTemplateItem($templateData["item_key"],$templateData["item_val"]);
        if(count($template_item) == 0){
            // print_r($template_item);
            echo "置換文字列読み込みエラー";
            // return 'error:invalid replace string';
            return;
        }

        //PJコードリスト取得
        $pj_code_list = array();
        array_push($pj_code_list, $pjCode);
        if(count($pj_code_list) != 1){
            // print_r($pj_code_list);
            echo "テンプレートファイル内にPJコードを見つけられませんでした。</br>";
            echo "テンプレートファイルにPJコードを入力されていない場合、</br>";
            echo "PJコードを入力したテンプレートファイルを再登録してください。";
            // return 'error:PJ code does not exist in template file';
            return;
        }
        
        //全店物件情報取得
        $allstore = new AllstoreController();
        $allstore_data = $allstore->GetDataByPjCodeList($pj_code_list);
        if($allstore_data == null){
            // print_r($allstore_data);
            echo "PJコードと一致する全店物件情報を取得できませんでした。";
            // return 'error:No matching AllStoreInfo was found for the PJ code';
            return;
        }
        
        //テンプレートの対応セルに全店物件情報を設定
        $replaceValueList = $this->adjustReplaceList($pj_code_list, $template_item, $allstore_data);
        $set_result = $this->ReplaceCellValueTypeOneProject($excel, $replaceValueList);
        // if($set_result == 0){
        //     echo "ReplaceCellValue error.";
        //     return;
        // }

        // $sheet->setTitle(date("Ymd"));
        //出力するファイル名
        $filename = $file_name;
        
        $writer = PHPExcel_IOFactory::createWriter($excel, "Excel2007");
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"".$filename."\"");
        header('Cache-Control: max-age=0');
        $writer->save("php://output");

        // return 'success';
    }
    
    
    function SaveTemplateData(Request $request)
    {
        
        $message = $request->get("message");
        if($message == "save_template"){
            try{
                $templateName = $request->get("templateName");
                $description = $request->get("txtA_Description");
                $template_type = $request->get("templateType");
                $variable_keys = $request->get("variable_keys");
                $variable_values = $request->get("variable_values");
                $old_file_name = $request->get("old_file_name");
                $filename="";
                if($request->has('file')){
                    $upload_file = $request->file("file");
                    $filename = $upload_file->getClientOriginalName();
                    $move_filepath = "/var/www/html/iPD/app/Exports/UploadedTemplate/";
                    $upload_file->move($move_filepath, $upload_file->getClientOriginalName());
                    
                    if($old_file_name != ""){//delete old file
                        if(file_exists($move_filepath.$old_file_name) && $filename !== $old_file_name)
                            unlink($move_filepath.$old_file_name);
                    }
                }else{
                    $filename = $old_file_name;
                }
                
                $document_model = new DocumentModel();
                $result = $document_model->SaveTemplateData($templateName,$description,$template_type,$variable_keys,$variable_values,$filename);
                
                return $result;
                
            }catch(Exception $e){
                return "error when saving template data.";
            }
            
        }
    }
    
    function GetTemplateData(Request $request)
    {
        $message = $request->get("message");
        if($message == "get_template_list"){
            $document_model = new DocumentModel();
            $result = $document_model->GetAllTemplateList();
            return $result;
        }else if($message == "get_template_byname"){
            $templateName = $request->get("templateName");
            $document_model = new DocumentModel();
            $result = $document_model->GetTemplateDataByName($templateName);
            
            if(!empty($result)){
                $result[0]["created_user_name"] = $result[0]["created_user_first_name"] . " " . $result[0]["created_user_last_name"];
                $result[0]["updated_user_name"] = $result[0]["updated_user_first_name"] . " " . $result[0]["updated_user_last_name"];
                
                $company = new CompanyModel();
                $retBranch = $result[0]["created_branch_id"] == 0 ? [] : $company->GetBranchById($result[0]["created_branch_id"]);
                $retDept = $result[0]["created_orgainzation_id"] == 0 ? [] : $company->GetDeptById($result[0]["created_orgainzation_id"]);
                $branchName = empty($retBranch) ? "" : $retBranch[0]["name"];
                $deptName = empty($retDept) ? "" : $retDept[0]["name"];
                
                $result[0]["created_orgainzation_name"] = $branchName . " " . $deptName;
                
                $retBranch = $result[0]["updated_branch_id"] == 0 ? [] : $company->GetBranchById($result[0]["updated_branch_id"]);
                $retDept = $result[0]["updated_orgainzation_id"] == 0 ? [] : $company->GetDeptById($result[0]["updated_orgainzation_id"]);
                $branchName = empty($retBranch) ? "" : $retBranch[0]["name"];
                $deptName = empty($retDept) ? "" : $retDept[0]["name"];

                $result[0]["updated_orgainzation_name"] = $branchName . " " . $deptName;
            }
            
            return $result;
        }else if($message == "get_template_byfilename"){
            $file_name = $request->get("file_name");
            $templateName = $request->get("templateName");
            $document_model = new DocumentModel();
            $result = $document_model->GetTemplateDataByFileName($file_name,$templateName);
            return $result;
        }else if($message == "get_allstore_name_pjid"){
            $document_model = new DocumentModel();
            $result = $document_model->GetAllStoreProjectNamesAndPJID();
            return $result;
        }
    }
    
    function DeleteTemplate(Request $request)
    {
        $message = $request->get("message");
        if($message == "delete_template_byname"){
            $templateName = $request->get("name");
            $document_model = new DocumentModel();
            $result = $document_model->DeleteTemplateByName($templateName);
            if(strpos($result,'success') !== false){
                $delete_file_name = $request->get("delete_file_name");
                $filepath = "/var/www/html/iPD/app/Exports/UploadedTemplate/";
                if($delete_file_name != ""){//delete old file
                    if(file_exists($filepath.$delete_file_name))
                        unlink($filepath.$delete_file_name);
                }
            }
            return $result;
        }
    }
    
    function SaveWordTemplateData(Request $request)
    {
        
        $message = $request->get("message");
        if($message == "save_template"){
            try{
                $templateName = $request->get("templateName");
                $description = $request->get("txtA_Description");
                // $template_type = $request->get("templateType");
                $variable_keys = $request->get("variable_keys");
                $variable_values = $request->get("variable_values");
                $old_file_name = $request->get("old_file_name");
                $filename="";
                if($request->has('file')){
                    $upload_file = $request->file("file");
                    $filename = $upload_file->getClientOriginalName();
                    $move_filepath = "/var/www/html/iPD/app/Exports/UploadedTemplate/";
                    $upload_file->move($move_filepath, $upload_file->getClientOriginalName());
                    
                    if($old_file_name != ""){//delete old file
                        if(file_exists($move_filepath.$old_file_name) && $filename !== $old_file_name)
                            unlink($move_filepath.$old_file_name);
                    }
                }else{
                    $filename = $old_file_name;
                }
                
                $document_model = new DocumentModel();
                $result = $document_model->SaveWordTemplateData($templateName,$description,$variable_keys,$variable_values,$filename);
                
                return $result;
                
            }catch(Exception $e){
                return "error when saving template data.";
            }
            
        }
    }

    function GetWordTemplateData(Request $request)
    {
        $message = $request->get("message");
        if($message == "get_template_list"){
            $document_model = new DocumentModel();
            $result = $document_model->GetAllWordTemplateList();
            return $result;
        }else if($message == "get_template_byname"){
            $templateName = $request->get("templateName");
            $document_model = new DocumentModel();
            $result = $document_model->GetWordTemplateDataByName($templateName);
            
            if(!empty($result)){
                $result[0]["created_user_name"] = $result[0]["created_user_first_name"] . " " . $result[0]["created_user_last_name"];
                $result[0]["updated_user_name"] = $result[0]["updated_user_first_name"] . " " . $result[0]["updated_user_last_name"];
                
                $company = new CompanyModel();
                $retBranch = $result[0]["created_branch_id"] == 0 ? [] : $company->GetBranchById($result[0]["created_branch_id"]);
                $retDept = $result[0]["created_orgainzation_id"] == 0 ? [] : $company->GetDeptById($result[0]["created_orgainzation_id"]);
                $branchName = empty($retBranch) ? "" : $retBranch[0]["name"];
                $deptName = empty($retDept) ? "" : $retDept[0]["name"];
                
                $result[0]["created_orgainzation_name"] = $branchName . " " . $deptName;
                
                $retBranch = $result[0]["updated_branch_id"] == 0 ? [] : $company->GetBranchById($result[0]["updated_branch_id"]);
                $retDept = $result[0]["updated_orgainzation_id"] == 0 ? [] : $company->GetDeptById($result[0]["updated_orgainzation_id"]);
                $branchName = empty($retBranch) ? "" : $retBranch[0]["name"];
                $deptName = empty($retDept) ? "" : $retDept[0]["name"];

                $result[0]["updated_orgainzation_name"] = $branchName . " " . $deptName;
            }
            
            return $result;
        }else if($message == "get_template_byfilename"){
            $file_name = $request->get("file_name");
            $templateName = $request->get("templateName");
            $document_model = new DocumentModel();
            $result = $document_model->GetWordTemplateDataByFileName($file_name,$templateName);
            return $result;
        }else if($message == "get_allstore_name_pjid"){
            $document_model = new DocumentModel();
            $result = $document_model->GetAllStoreProjectNamesAndPJID();
            return $result;
        }
    }

    function DeleteWordTemplate(Request $request)
    {
        $message = $request->get("message");
        if($message == "delete_template_byname"){
            $templateName = $request->get("name");
            $document_model = new DocumentModel();
            $result = $document_model->DeleteWordTemplateByName($templateName);
            if(strpos($result,'success') !== false){
                $delete_file_name = $request->get("delete_file_name");
                $filepath = "/var/www/html/iPD/app/Exports/UploadedTemplate/";
                if($delete_file_name != ""){//delete old file
                    if(file_exists($filepath.$delete_file_name))
                        unlink($filepath.$delete_file_name);
                }
            }
            return $result;
        }
    }
    
    function adjustTemplateItem($item_key,$item_val)
    {
        $template_item = array();
        
        $aryKey = explode(",", $item_key);
        $aryVal = explode(",", $item_val);
        
        if(count($aryKey) != count($aryVal)){
            return $template_item;
        }

        for($i = 0; $i < count($aryKey); $i++){
            $key = $aryKey[$i];
            $val = $aryVal[$i];
            $template_item[$key] = $val;
        }
        
        // print_r("adjustTemplateItem");
        // print_r($template_item);
        return $template_item;
    }
    
    function getPjCodesByExcelTemplate($excel)
    {
        
        $pj_code_list = array();
        $target_sheet = array();
        $target_row = -1;
        $target_col = -1;

        //シート数取得
        $sheetsCount = $excel->getSheetCount();

        for($i = 0; $i < $sheetsCount; $i++){
            //シート取得
            $excel->setActiveSheetIndex($i);
            $sheet = $excel->getActiveSheet();
    
            //シート名取得
            $sheet_name = $sheet->getTitle();
            
            //行列の最大値取得
            $rowMax = $sheet->getHighestRow();
            $colMax = $sheet->getHighestColumn();
            $colsno = PHPExcel_Cell::columnIndexFromString($colMax);
            
            $isFirst = true;

            for($r = 1; $r <= $rowMax; $r++) {
                
                for($c = 0; $c < $colsno; $c++) {
                    
                    $value = $sheet->getCellByColumnAndRow($c, $r)->getValue();
                    
                    if('PJ' === substr($value, 0, 2) && mb_strlen($value) == 10){
                        
                        if($isFirst){
                            $target_pj_code = $sheet_name;
                            $target_row = $r;
                            $target_col = $c;
                            $isFirst = false;
                        }
                        
                        array_push($pj_code_list,$value);
                    }
                }
            }
            
            if($target_row != -1 && $target_col != -1){
                $target_info = array('sheet_name'=>$target_pj_code, 'target_row'=>$target_row, 'target_col'=>$target_col);
                array_push($target_sheet,$target_info);
            }
            
        }
        
        $pj_code_list = array_unique($pj_code_list);
        $pj_code_list = array_values($pj_code_list);
        
        $result = ["pj_code"=>$pj_code_list, "target_info"=>$target_sheet];
        // print_r("getPjCodesByExcelTemplate");
        // print_r($result);
        return $result;
    }

    function adjustReplaceList($pj_code_list, $template_item, $allstore_data){
        //$template_item
        //["用途"=>"${youto}","プロジェクト名称"=>"${pj_name}"]
        
        //$allstore_data
        //[0]["用途"=>"病院","プロジェクト名称"=>"クレメントイン今治"]
        //[1]["用途"=>"病院","プロジェクト名称"=>"クレメントイン今治"]
        
        $result = array();
        //ex.["${youto}"=>"病院","${pj_name}"=>"クレメントイン今治"]
        
        for($p = 0; $p < count($allstore_data); $p++){
            $ret_cur_pj = array();
            $cur_pj_data = $allstore_data[$p];
            $cur_pj_code = $cur_pj_data["PJコード"];
            
            $isContinue = true;
            for($s = 0; $s < count($pj_code_list); $s++){
                if($cur_pj_code == $pj_code_list[$s]){
                    $isContinue = false;
                }
            }
            if($isContinue === true){
                continue;
            }
            
            $cur_replace_str = array();

            $keys = array_keys($template_item);
            for($i = 0; $i < count($keys); $i++){
                $key = "";
                $val = "";
                if(array_key_exists($keys[$i], $cur_pj_data)){
                    $val = $cur_pj_data[$keys[$i]];
                }

                if(array_key_exists($keys[$i], $template_item)){
                    $key = $template_item[$keys[$i]];
                    $cur_replace_str[$key] = $val;
                }
            }
            
            $ret_cur_pj["pj_code"] = $cur_pj_code;
            $ret_cur_pj["data"] = $cur_replace_str;
            array_push($result,$ret_cur_pj);
        }
        
        // print_r("adjustReplaceList");
        // print_r($result);
        return $result;
    }
    
    function ReplaceCellValueTypeColumns($excel, $replaceValueList, $target_info)
    {
        
        // print_r($replaceValueList);return 0;
        $replace_count = 0;
        
        //シート数取得
        $sheetsCount = $excel->getSheetCount();

        for($i = 0; $i < $sheetsCount; $i++){
            //シート取得
            $excel->setActiveSheetIndex($i);
            $sheet = $excel->getActiveSheet();
            $sheet_name = $sheet->getTitle();
            
            //現在のシート名が置換対象シート名に含まれていなければ現在のシートをスキップ
            $isSkip = false;
            $current_sheet_info = array();
            for($t = 0; $t < count($target_info); $t++){
                if($sheet_name == $target_info[$t]["sheet_name"]){
                    $isSkip = true;
                    $current_sheet_info = $target_info[$t];
                }
            }
            if($isSkip === false){
                continue;
            }
            
            //行列の最大値取得
            $rowMax = $sheet->getHighestRow();
            $colMax = $sheet->getHighestColumn();
            $colsno = PHPExcel_Cell::columnIndexFromString($colMax);
            
            for($r = 1; $r <= $rowMax; $r++) {
                
                if($r < $current_sheet_info["target_row"]){
                    continue;
                }
                
                for($c = 0; $c < $colsno; $c++) {
                    
                    $value = $sheet->getCellByColumnAndRow($c, $r)->getValue();
                    
                    if('PJ' === substr($value, 0, 2) && mb_strlen($value) == 10){
                        
                        for($s = 0; $s < count($replaceValueList); $s++) {
                            if($value == $replaceValueList[$s]["pj_code"]){
                                //pjコード一致
                                $replace_list = $replaceValueList[$s]["data"];
                                // print_r("PJCode:");print_r($value);
                                
                                //この列の値を置換
                                for($column_cnt = 0; $column_cnt < $colsno; $column_cnt++) {
                                    $replace_value = $sheet->getCellByColumnAndRow($column_cnt, $r)->getValue();
                                    // print_r("c:");print_r($column_cnt);
                                    // print_r("row_cnt:");print_r($r);
                                    // print_r("replace_value:");print_r($replace_value);
                                    
                                    $replace_key_list = array_keys($replace_list);
                                    for($str_cnt = 0; $str_cnt < count($replace_key_list); $str_cnt++) {
                                        $cur_replace_key = $replace_key_list[$str_cnt];

                                        // if($replace_value == $cur_replace_key){
                                        //     //セル値置換
                                        //     // print_r("置換前:");print_r($replace_value);
                                        //     $sheet->setCellValueByColumnAndRow($column_cnt, $r, $replace_list[$cur_replace_key]);
                                        //     // $tt = $sheet->getCellByColumnAndRow($column_cnt, $r)->getValue();
                                        //     // print_r("置換後:");print_r($tt);
                                        //     $replace_count++;
                                        // }
                                        
                                        $pos = strpos($replace_value, $cur_replace_key);
                                        if($pos !== false){
                                            $replace = str_replace($cur_replace_key, $replace_list[$cur_replace_key], $replace_value);
                                            $replace_value = $replace;
                                            $sheet->setCellValueByColumnAndRow($column_cnt, $r, $replace);
                                            $replace_count++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

        }

        return $replace_count;
        // return 0;
    }

    
    function ReplaceCellValueTypeRow($excel, $replaceValueList, $target_info)
    {
        
        // print_r($replaceValueList);
        $replace_count = 0;
        
        //シート数取得
        $sheetsCount = $excel->getSheetCount();

        for($i = 0; $i < $sheetsCount; $i++){
            //シート取得
            $excel->setActiveSheetIndex($i);
            $sheet = $excel->getActiveSheet();
            $sheet_name = $sheet->getTitle();
            
            //現在のシート名が置換対象シート名に含まれていなければ現在のシートをスキップ
            $isSkip = false;
            $current_sheet_info = array();
            for($t = 0; $t < count($target_info); $t++){
                if($sheet_name == $target_info[$t]["sheet_name"]){
                    $isSkip = true;
                    $current_sheet_info = $target_info[$t];
                }
            }
            if($isSkip === false){
                continue;
            }
            
            //行列の最大値取得
            $rowMax = $sheet->getHighestRow();
            $colMax = $sheet->getHighestColumn();
            $colsno = PHPExcel_Cell::columnIndexFromString($colMax);
            
            for($r = 1; $r <= $rowMax; $r++) {
                
                if($r < $current_sheet_info["target_row"]){
                    continue;
                }
                
                for($c = 0; $c < $colsno; $c++) {
                    
                    $value = $sheet->getCellByColumnAndRow($c, $r)->getValue();
                    
                    if('PJ' === substr($value, 0, 2) && mb_strlen($value) == 10){
                        
                        for($s = 0; $s < count($replaceValueList); $s++) {
                            if($value == $replaceValueList[$s]["pj_code"]){
                                //pjコード一致
                                $replace_list = $replaceValueList[$s]["data"];
                                // print_r("PJCode:");print_r($value);
                                
                                //この列の値を置換
                                for($row_cnt = 0; $row_cnt <= $rowMax; $row_cnt++) {
                                    $replace_value = $sheet->getCellByColumnAndRow($c, $row_cnt)->getValue();
                                    // print_r("c:");print_r($c);
                                    // print_r("row_cnt:");print_r($row_cnt);
                                    // print_r("replace_value:");print_r($replace_value);
                                    
                                    $replace_key_list = array_keys($replace_list);
                                    for($str_cnt = 0; $str_cnt < count($replace_key_list); $str_cnt++) {
                                        $cur_replace_key = $replace_key_list[$str_cnt];

                                        // if($replace_value == $cur_replace_key){
                                        //     //セル値置換
                                        //     // print_r("置換前:");print_r($replace_value);
                                        //     $sheet->setCellValueByColumnAndRow($c, $row_cnt, $replace_list[$cur_replace_key]);
                                        //     // $tt = $sheet->getCellByColumnAndRow($c, $row_cnt)->getValue();
                                        //     // print_r("置換後:");print_r($tt);
                                        //     $replace_count++;
                                        // }
                                        
                                        $pos = strpos($replace_value, $cur_replace_key);
                                        if($pos !== false){
                                            $replace = str_replace($cur_replace_key, $replace_list[$cur_replace_key], $replace_value);
                                            $replace_value = $replace;
                                            $sheet->setCellValueByColumnAndRow($c, $row_cnt, $replace);
                                            $replace_count++;
                                        }

                                    }
                                }
                            }
                        }
                    }
                }
            }

        }

        return $replace_count;
        // return 0;
    }
    
    function ReplaceCellValueTypeOneProject($excel, $replaceValueList)
    {
        // print_r($replaceValueList);
        $replace_count = 0;
        
        //シート数取得
        $sheetsCount = $excel->getSheetCount();

        for($i = 0; $i < $sheetsCount; $i++){
            //シート取得
            $excel->setActiveSheetIndex($i);
            $sheet = $excel->getActiveSheet();
            $sheet_name = $sheet->getTitle();
            
            //行列の最大値取得
            $rowMax = $sheet->getHighestRow();
            $colMax = $sheet->getHighestColumn();
            $colsno = PHPExcel_Cell::columnIndexFromString($colMax);
            
            for($r = 1; $r <= $rowMax; $r++) {
                
                for($c = 0; $c < $colsno; $c++) {
                    
                    $value = $sheet->getCellByColumnAndRow($c, $r)->getValue();
                    
                        for($s = 0; $s < count($replaceValueList); $s++) {

                            $replace_list = $replaceValueList[$s]["data"];
                            // print_r("PJCode:");print_r($value);
                            
                            // print_r("c:");print_r($column_cnt);
                            // print_r("row_cnt:");print_r($r);
                            
                            $replace_key_list = array_keys($replace_list);
                            for($str_cnt = 0; $str_cnt < count($replace_key_list); $str_cnt++) {
                                $cur_replace_key = $replace_key_list[$str_cnt];

                                // if($value == $cur_replace_key){
                                //     //セル値置換
                                //     // print_r("置換前:");print_r($replace_value);
                                //     $sheet->setCellValueByColumnAndRow($c, $r, $replace_list[$cur_replace_key]);
                                //     // $tt = $sheet->getCellByColumnAndRow($column_cnt, $r)->getValue();
                                //     // print_r("置換後:");print_r($tt);
                                //     $replace_count++;
                                // }
                                
                                $pos = strpos($value, $cur_replace_key);
                                if($pos !== false){
                                    $replace = str_replace($cur_replace_key, $replace_list[$cur_replace_key], $value);
                                    $value = $replace;
                                    $sheet->setCellValueByColumnAndRow($c, $r, $replace);
                                    $replace_count++;
                                }
                            }
                        }
                }
            }

        }

        return $replace_count;
        // return 0;
    }
    
    function outputDefaultWordTemplate($name)
    {
        $input_file_name="/var/www/html/iPD/app/Exports/UploadedTemplate/".$name;
        $template = new \PhpOffice\PhpWord\TemplateProcessor($input_file_name);
        
        $filename = $name;
        header('Content-Type: application/octet-stream');
        header("Content-Disposition: attachment; filename=\"".$filename."\"");
        $template->saveAs('php://output');
    }
    
    function outputWordTemplate($name)
    {
        $aryName = explode(",", $name);
        $tmpName = $aryName[0];
        $pjCode = $aryName[1];
        $result = $this->GetWordTemplateDataByName($tmpName);

        if($result != null)
        {
            // print_r($result);
            $templateData = $result[0];
            $ret = $this->outputWordTypeOneProject($templateData, $pjCode);
            // return $ret;
        }else{
            print_r($result);
        }
    }
    
    function outputWordTypeOneProject($templateData, $pjCode)
    {
        $name = $templateData["name"];
        $description = $templateData["name"];
        $file_name = $templateData["file_name"];
        $input_file_name="/var/www/html/iPD/app/Exports/UploadedTemplate/".$file_name;

        //template対応表整理
        $template_item = $this->adjustTemplateItem($templateData["item_key"],$templateData["item_val"]);
        if(count($template_item) == 0){
            // print_r($template_item);
            echo "adjustTemplateItem error.";
            return;
        }
        
        //PJコードリスト取得
        $pj_code_list = array();
        array_push($pj_code_list, $pjCode);
        if(count($pj_code_list) != 1){
            // print_r($pj_code_list);
            echo "pj_code_list count error.";
            return;
        }
        
        //全店物件情報取得
        $allstore = new AllstoreController();
        $allstore_data = $allstore->GetDataByPjCodeList($pj_code_list);
        // echo $allstore_data;
        if($allstore_data == null){
            // print_r($allstore_data);
            echo "GetDataByPjCodeList error.";
            return;
        }
        
        //テンプレートの対応セルに全店物件情報を設定
        $replaceValueList = $this->adjustReplaceList($pj_code_list, $template_item, $allstore_data);

        $tmpPjName = "";
        
        // if($allstore_data[0]["プロジェクト名称"] == ""){
        //     if($allstore_data[0]["b_pj_name"] == ""){
        //         $tmpPjName = $allstore_data[0]["a_pj_name"];
        //     }else{
        //         $tmpPjName = $allstore_data[0]["b_pj_name"];
        //     }
        // }else{
        //         $tmpPjName = $allstore_data[0]["b_tmp_pj_name"];
        // }

        //get tb_bimactionplan
        $prjMgt = new ProjectMgtModel();
        $projectName = $allstore_data[0]["プロジェクト名称"];
        
        $ret_pjMgtData = $prjMgt->getImplementationDocByProject($projectName);

        try {
            // templateファイル読込
            $template = new \PhpOffice\PhpWord\TemplateProcessor($input_file_name);

            if($ret_pjMgtData != "" && $ret_pjMgtData != null){
                $pjMgtData = $ret_pjMgtData[0];
                //テンプレートの対応セルにプロジェクト管理情報を設定
                $replacePjMgtList = $this->adjustPjMgtReplaceList($template_item, $pjMgtData);
                
                if($replacePjMgtList == null){
                    // print_r($replacePjMgtList);
                    return;
                }
                
                $replace_pj_mgt_key_list = array_keys($replacePjMgtList);
                for($i=0; $i < count($replace_pj_mgt_key_list); $i++){
                    $currKey = $replace_pj_mgt_key_list[$i];
                    // print_r($currKey. "::" . $replacePjMgtList[$currKey]. "##");
                    $template->setValue($currKey, $replacePjMgtList[$currKey]);
                }
                
            }
            
            $template->setValue('${current_date}',  date("Y-m-d"));

            $replace_list = $replaceValueList[0]['data'];
            $replace_key_list = array_keys($replace_list);
            for($i=0; $i < count($replace_key_list); $i++){
                $curKey = $replace_key_list[$i];
                // print_r($curKey. "::" . $replace_list[$curKey]. "##");
                
                $template->setValue($curKey, $replace_list[$curKey]);
            }

        } catch(Exception $e) {
            die('Error loading file "'.pathinfo($input_file_name,PATHINFO_BASENAME).'": '.$e->getMessage());
        }

        //出力するファイル名
        $filename = $file_name;
        header('Content-Type: application/octet-stream');
        header("Content-Disposition: attachment; filename=\"".$filename."\"");
        $template->saveAs('php://output');

    }
    
    function adjustPjMgtReplaceList($template_item, $pjMgtData)
    {
        
        $result = array();
        $template_key_list = array_keys($template_item);
        for($i=0; $i < count($template_key_list); $i++){
            $template_key = $template_key_list[$i];
            $template_val = $template_item[$template_key];
            
            if($template_key == "プロジェクト基準点X"){
                $result[$template_val] = $pjMgtData["base_linex"];
            }else if($template_key == "プロジェクト基準点Y"){
                $result[$template_val] = $pjMgtData["base_liney"];
            }else if($template_key == "モデル作成(担当者)"){
                $result[$template_val] = $pjMgtData["mdl_name"];
            }else if($template_key == "モデル作成(組織)"){
                $result[$template_val] = $pjMgtData["mdl_org"];
            }else if($template_key == "サブコン空調(担当者)"){
                $result[$template_val] = $pjMgtData["sbk_name"];
            }else if($template_key == "サブコン空調(組織)"){
                $result[$template_val] = $pjMgtData["sbk_org"];
            }else if($template_key == "サブコン電気(担当者)"){
                $result[$template_val] = $pjMgtData["sbd_name"];
            }else if($template_key == "サブコン電気(組織)"){
                $result[$template_val] = $pjMgtData["sbd_org"];
            }else if($template_key == "FAB作図(担当者)"){
                $result[$template_val] = $pjMgtData["fsa_name"];
            }else if($template_key == "FAB作図(組織)"){
                $result[$template_val] = $pjMgtData["fsa_org"];
            }else if($template_key == "FAB製作(担当者)"){
                $result[$template_val] = $pjMgtData["fse_name"];
            }else if($template_key == "FAB製作(組織)"){
                $result[$template_val] = $pjMgtData["fse_org"];
            }else if($template_key == "使用ソフトウェア(建築設計)"){
                $result[$template_val] = $pjMgtData["ken_sw"];
            }else if($template_key == "使用ソフトウェア(構造設計)"){
                $result[$template_val] = $pjMgtData["kou_sw"];
            }else if($template_key == "使用ソフトウェア(設備空調)"){
                $result[$template_val] = $pjMgtData["sku_sw"];
            }else if($template_key == "使用ソフトウェア(設備電気)"){
                $result[$template_val] = $pjMgtData["sde_sw"];
            }else if($template_key == "使用ソフトウェア(ワンモデル)"){
                $result[$template_val] = $pjMgtData["mdl_sw"];
            }else if($template_key == "使用ソフトウェア(施工)"){
                $result[$template_val] = $pjMgtData["sek_sw"];
            }else if($template_key == "使用ソフトウェア(生産設計)"){
                $result[$template_val] = $pjMgtData["sei_sw"];
            }else if($template_key == "使用ソフトウェア(サブコン施工空調)"){
                $result[$template_val] = $pjMgtData["sbk_sw"];
            }else if($template_key == "使用ソフトウェア(サブコン施工電気)"){
                $result[$template_val] = $pjMgtData["sbd_sw"];
            }else if($template_key == "使用ソフトウェア(FAB作図)"){
                $result[$template_val] = $pjMgtData["fsa_sw"];
            }else if($template_key == "使用ソフトウェア(FAB製作)"){
                $result[$template_val] = $pjMgtData["fse_sw"];
            }else if($template_key == "スケジュール備考(設計モデル作成)"){
                $result[$template_val] = $pjMgtData["make_model_bikou"];
            }else if($template_key == "スケジュール備考(確認申請)"){
                $result[$template_val] = $pjMgtData["sinsei_bikou"];
            }else if($template_key == "スケジュール備考(積算model統合)"){
                $result[$template_val] = $pjMgtData["seisan_bikou"];
            }else if($template_key == "スケジュール備考(工事従事者決定)"){
                $result[$template_val] = $pjMgtData["kouji_bikou"];
            }else if($template_key == "スケジュール備考(現場工程決定)"){
                $result[$template_val] = $pjMgtData["genba_bikou"];
            }else if($template_key == "スケジュール備考(施工)"){
                $result[$template_val] = $pjMgtData["sekou_bikou"];
            }else if($template_key == "スケジュール開始(引き渡し)"){
                $result[$template_val] = $pjMgtData["hiki_start"];
            }else if($template_key == "スケジュール完了(引き渡し)"){
                $result[$template_val] = $pjMgtData["hiki_end"];
            }else if($template_key == "スケジュール備考(引き渡し)"){
                $result[$template_val] = $pjMgtData["hiki_bikou"];
            }else if($template_key == "BIM実行計画書バージョン"){
                $result[$template_val] = $pjMgtData["version"];
            }
        }
        
        return $result;
    }

}
