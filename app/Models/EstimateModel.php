<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class EstimateModel extends Model
{
    public function GetEstimateProject(){
        try{
            $query = "select * from tb_allstore_info where display_estimate_flag = 1 AND estimate_during_flag = 0 AND estimate_finished_flag = 0";
            $data = DB::select($query);
            return json_decode(json_encode($data),true); 
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    public function GetEstimateDuringProject(){
        try{
            $query = "select * from tb_allstore_info where display_estimate_flag = 1 AND estimate_during_flag = 1 AND estimate_finished_flag = 0";
            $data = DB::select($query);
            return json_decode(json_encode($data),true); 
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    public function GetEstimateFinishedProject(){
        try{
            $query = "select * from tb_allstore_info where display_estimate_flag = 1 AND estimate_during_flag = 0 AND estimate_finished_flag = 1";
            $data = DB::select($query);
            return json_decode(json_encode($data),true); 
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    public function UpdateFlag($ipdCodeList, $flag){
        $count = count($ipdCodeList);
        try{
            if($flag == "during_estimate"){
                $query = "UPDATE tb_allstore_info SET estimate_during_flag = 1  WHERE a_pj_code IN (";
                for($i =0; $i<$count; $i++){
                    if($i == $count-1){
                        $query.= "'".$ipdCodeList[$i] ."'". ")";
                    }else{
                        $query.= "'".$ipdCodeList[$i] ."'". "," ;
                    }
                }
            }elseif($flag == "finished_estimate"){
                $query = "UPDATE tb_allstore_info SET estimate_finished_flag = 1 , estimate_during_flag = 0 WHERE a_pj_code IN (";
                for($i =0; $i<$count; $i++){
                    if($i == $count-1){
                        $query.= "'".$ipdCodeList[$i] ."'". ")";
                    }else{
                        $query.= "'".$ipdCodeList[$i] ."'". "," ;
                    }
                }
            }
            
            $data = DB::update($query);
            return json_decode(json_encode($data),true);
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    public function GetProjectNameByiPDCode($ipdCodeList){
        $count = count($ipdCodeList);
        try{
            $query = "SELECT a_pj_name, a_pj_code FROM tb_allstore_info WHERE a_pj_code IN (";
            for($i =0; $i<$count; $i++){
                if($i == $count-1){
                    $query.= "'".$ipdCodeList[$i] ."'". ")";
                }else{
                    $query.= "'".$ipdCodeList[$i] ."'". "," ;
                }
            }
            $data = DB::select($query);
            return json_decode(json_encode($data),true);
        }catch(Exception $e){
            return $e->getMessage();
        }
        
    }
    
    public function GetListOfModellingCompany(){
        try{
            $query = "SELECT * FROM tb_company WHERE company_type_id = 4";
            $data = DB::select($query);
            return json_decode(json_encode($data),true);
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
}