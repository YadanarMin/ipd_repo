<?php

namespace App\Http\Controllers;
// use App\Models\ForgeModel;
use App\Models\ProcessTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel; 
use PHPExcel;
use PHPExcel_IOFactory;

class ProcessMappingController extends Controller 
{
    function index()
    {
        return view('processMapping');
    }
    
    function SaveData(Request $request)
    {
        $line = $request->get('storeData');

        $process_code_1     = isset($line[0]) ? $line[0] : 0;
        $process_code_2     = isset($line[1]) ? $line[1] : 0;
        $process_code_3     = isset($line[2]) ? $line[2] : 0;
        $process_code_4     = isset($line[3]) ? $line[3] : 0;
        $name               = isset($line[4]) ? $line[4] : "";
        $satellite_model    = isset($line[5]) ? $line[5] : 0;
        $sbs_family_name    = isset($line[6]) ? $line[6] : "";
        $sbs_category       = isset($line[7]) ? $line[7] : "";
        $sbs_type_name      = isset($line[8]) ? $line[8] : "";
        $sbs_type_param     = isset($line[9]) ? $line[9] : "";

        $query = "INSERT INTO tb_process(id,process_code_1,process_code_2,process_code_3,process_code_4
                ,name,satellite_model,sbs_family_name,sbs_category,sbs_type_name,sbs_type_param)
                SELECT COALESCE(MAX(id), 0) + 1,$process_code_1,$process_code_2,$process_code_3,$process_code_4,
                '$name',$satellite_model,'$sbs_family_name','$sbs_category','$sbs_type_name','$sbs_type_param' FROM tb_process
                ON DUPLICATE KEY UPDATE process_code_1 = $process_code_1,process_code_2 = $process_code_2,process_code_3 = $process_code_3,process_code_4 = $process_code_4,
                name = '$name',satellite_model = $satellite_model,sbs_family_name = '$sbs_family_name',sbs_category = '$sbs_category',sbs_type_name = '$sbs_type_name',sbs_type_param = '$sbs_type_param'";
        DB::insert($query);
    }

    function UpdateData(Request $request)
    {
        $column = $request->get('columnData');

        $process_code_1     = (int)$column[1];
        $process_code_2     = (int)$column[2];
        $process_code_3     = (int)$column[3];
        $process_code_4     = (int)$column[4];
        $name               = isset($column[5]) ? $column[5] : "";
        $satellite_model    = isset($column[6]) && $column[6] == "サテライト" ? 1 : 0;
        $sbs_family_name    = isset($column[7]) ? $column[7] : "";
        $sbs_category       = isset($column[8]) ? $column[8] : "";
        $sbs_type_name      = isset($column[9]) ? $column[9] : "";
        $sbs_type_param     = isset($column[10]) ? $column[10] : "";
        
        $query = "UPDATE tb_process SET name = '$name', satellite_model = $satellite_model, 
                    sbs_family_name = '$sbs_family_name', sbs_category = '$sbs_category',
                    sbs_type_name = '$sbs_type_name', sbs_type_param = '$sbs_type_param' 
                    WHERE process_code_1 = $process_code_1 AND process_code_2 = $process_code_2 AND process_code_3 = $process_code_3 AND process_code_4 = $process_code_4";
        DB::insert($query);
    }
    
    function GetData(Request $request)
    {
        $message = $request->get('message');

        if($message == "getAllData"){
            
            $query = "SELECT * FROM tb_process";
            $data = DB::select($query);     
            return json_decode(json_encode($data),true);
            
        }else if($message == "getDataByID"){

            $inputCondition = $request->get('condition');
            
            $condition = "";
            $code_1 = $inputCondition["id_1"];
            $level  = $inputCondition["id_2"];
            $code_2 = $inputCondition["id_3"];
            $code_3 = $inputCondition["id_4"];

            $tmpCondition = "WHERE";
            if($code_1 != 0){
                $tmpCondition .= " process_code_1 = $code_1";
            }
            if($code_2 != 0){
                $tmpCondition .= " AND process_code_2 = $code_2";
            }
            if($code_3 != 0){
                $tmpCondition .= " AND process_code_3 = $code_3";
            }
            
            if($tmpCondition !== "WHERE"){
                $condition = $tmpCondition;
            }

            $query = "SELECT * FROM tb_process $condition";
            $data = DB::select($query);
            $retData["data"] = $data;
            $retData["level"] = $level;
            return json_decode(json_encode($retData),true);
        }
        
    }
    
    function DeleteData()
    {
        $query = "DELETE FROM tb_process";
        DB::delete($query);
        return "success";
    }
    
    function ProcessExcelDownload(){

        $processTable = new ProcessTable();
        $processColumns = $processTable->GetAllProject();

        if($processColumns != null)
        {
            $inputFileName="/var/www/html/iPD/app/Exports/Template/processMappingTemplate.xlsx";
    
            //  Read your Excel workbook
            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $excel = $objReader->load($inputFileName);
            } catch(Exception $e) {
                die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
            }
            $excel->setActiveSheetIndex(0);
            $startRowNum = 7;
            $endRowNum = $excel->setActiveSheetIndex(0)->getHighestRow();
            $sheet = $excel->getActiveSheet();
    
            foreach($processColumns as $column){
                
                
                for($i = $startRowNum; $i <= $endRowNum; $i++){

                    $cell_id1 = $sheet->getCell("H".(string)$i)->getValue();
                    $cell_id2 = $sheet->getCell("I".(string)$i)->getValue();
                    $cell_id3 = $sheet->getCell("J".(string)$i)->getValue();
                    $cell_id4 = $sheet->getCell("K".(string)$i)->getValue();
                    
                    if(($cell_id1 == $column["process_code_1"]) && ($cell_id2 == $column["process_code_2"])
                    && ($cell_id3 == $column["process_code_3"]) && ($cell_id4 == $column["process_code_4"])){

                        $sheet->setCellValue("M".(string)$i, $column["name"]);
                        $sheet->setCellValue("P".(string)$i, $column["satellite_model"]);
                        $sheet->setCellValue("S".(string)$i, $column["sbs_family_name"]);
                        $sheet->setCellValue("U".(string)$i, $column["sbs_category"]);
                        $sheet->setCellValue("W".(string)$i, $column["sbs_type_name"]);
                        $sheet->setCellValue("AA".(string)$i, $column["sbs_type_param"]);
                    }
                }
            }
    
            $sheet->setTitle("Sheet1");
            //出力するファイル名
            $filename = "Process_Classification_Table.xlsx";               

            $writer = PHPExcel_IOFactory::createWriter($excel, "Excel2007");
            header('Content-Type: application/vnd.ms-excel');
            header("Content-Disposition: attachment;filename=\"".$filename."\"");
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
            
        }else{
             print_r($processColumns);
        }
    }

}
