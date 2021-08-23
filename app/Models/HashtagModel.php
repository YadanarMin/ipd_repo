<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Exception;
class HashtagModel extends Model{
    
    private $hashtagList;
    
    public function GetHashtagList(){
        $query = "SELECT hashtags from tb_ipd_report_history
                  UNION ALL
                  SELECT hashtags from tb_bukachoukai_1bu_history
                  UNION ALL
                  SELECT hashtags from tb_bukachoukai_2bu_history
                  UNION ALL
                  SELECT hashtags from tb_bukachoukai_3bu_history
                  UNION ALL
                  SELECT hashtags from tb_bukachoukai_gijutsukanribu_history
                  UNION ALL
                  SELECT hashtags from tb_bukachoukai_setsubi_shinkou_1bu_history
                  UNION ALL
                  SELECT hashtags from tb_bukachoukai_setsubi_shinkou_2bu_history
                  UNION ALL
                  SELECT hashtags from tb_bukachoukai_setsubi_shinkou_3bu_history
                  UNION ALL
                  SELECT hashtags from tb_bukachoukai_doboku_shinkou_history
                  UNION ALL
                  SELECT hashtags from tb_kouzousekkei_history
                  UNION ALL
                  SELECT hashtags from tb_ishousekkei_history
                  UNION ALL
                  SELECT hashtags from tb_mitsumori_sekisanbu_history
                  UNION ALL
                  SELECT hashtags from tb_renewal_history
                  UNION ALL
                  SELECT hashtags from tb_setsubisekkei_history
                  UNION ALL
                  SELECT hashtags from tb_hinshitsukanribu_history
                  UNION ALL
                  SELECT hashtags from tb_koujibu_history
                  UNION ALL
                  SELECT hashtags from tb_seisan_gijutsubu_history
                  UNION ALL
                  SELECT hashtags from tb_seisan_sekkeibu_history
                  UNION ALL
                  SELECT hashtags from tb_kouji_jimusho_history
                  UNION ALL
                  SELECT hashtags from tb_kouji_jimusho_setsubi_history" ;
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
                  
    }
    
    public function GetReportByHashtag($hashtagList, $search_logic){
        $this->hashtagList = $hashtagList;
        $reportTableList = $this->GetReportCategory();
        //print_r($reportTableList);
        $count = count($hashtagList);
        if($search_logic == 1){                                                     //AND
            $query="";
            foreach($reportTableList as $key=>$reportTable){
                $query .= "SELECT * FROM tb_" . $reportTable['default_name']."_history WHERE hashtags LIKE ";
                $query = $this->StringConcatANDLogic($count,$query);
                if($key == count($reportTableList)-1){}
                else{
                    $query.= "UNION ALL \n";
                }
            }
            
        }else{                                                                      //OR
            $query="";
            foreach($reportTableList as $key=>$reportTable){
                $query .= "SELECT * FROM tb_" . $reportTable['default_name']."_history WHERE hashtags LIKE ";
                $query = $this->StringConcatORLogic($count,$query);
                if($key == count($reportTableList)-1){}
                else{
                    $query.= "UNION ALL \n";
                }
            }
        }
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }
    
    public function StringConcatANDLogic($count, $query){
        for($i =0; $i<$count; $i++){
            if($i == $count-1){
                $query.= "'%".$this->hashtagList[$i] ."%'\n";
            }else{
                $query.= "'%".$this->hashtagList[$i] ."%'". " AND hashtags LIKE " ;
            }
        }
        return $query;
    }
    
    public function StringConcatORLogic($count, $query){
        for($i =0; $i<$count; $i++){
            if($i == $count-1){
                $query.= "'%".$this->hashtagList[$i] ."%'\n";
            }else{
                $query.= "'%".$this->hashtagList[$i] ."%'". " OR report LIKE " ;
            }
        }
        return $query;
    }
    
    public function GetReportCategory(){
        try{
            $query = "SELECT * FROM tb_report_category";
            $data = DB::select($query);
            return json_decode(json_encode($data),true);
        }catch(Exception $e){
            return $e->getMessage();
        }
        
        
    }
}

// $query = "SELECT * FROM tb_ipd_report_history WHERE hashtags LIKE ";
            // $query = $this->StringConcatANDLogic($count,$query);
            
            // $query.= " UNION ALL \n";
            // $query.= "SELECT * FROM tb_bukachoukai_1bu_history WHERE hashtags LIKE ";
            // $query = $this->StringConcatANDLogic($count, $query);
            
            // $query.= " UNION ALL \n";
            // $query.= "SELECT * FROM tb_bukachoukai_2bu_history WHERE hashtags LIKE ";
            // $query = $this->StringConcatANDLogic($count, $query);
            
            // $query.= " UNION ALL \n";
            // $query.= "SELECT * FROM tb_bukachoukai_3bu_history WHERE hashtags LIKE ";
            // $query = $this->StringConcatANDLogic($count, $query);
            
            // $query.= " UNION ALL \n";
            // $query.= "SELECT * FROM tb_bukachoukai_gijutsukanribu_history WHERE hashtags LIKE ";
            // $query = $this->StringConcatANDLogic($count, $query);
            
            // $query.= " UNION ALL \n";
            // $query.= "SELECT * FROM tb_bukachoukai_setsubi_shinkou_1bu_history WHERE hashtags LIKE ";
            // $query = $this->StringConcatANDLogic($count, $query);
            
            // $query.= " UNION ALL \n";
            // $query.= "SELECT * FROM tb_bukachoukai_setsubi_shinkou_2bu_history WHERE hashtags LIKE ";
            // $query = $this->StringConcatANDLogic($count, $query);
            
            // $query.= " UNION ALL \n";
            // $query.= "SELECT * FROM tb_bukachoukai_setsubi_shinkou_3bu_history WHERE hashtags LIKE ";
            // $query = $this->StringConcatANDLogic($count, $query);
            
            // $query.= " UNION ALL \n";
            // $query.= "SELECT * FROM tb_bukachoukai_doboku_shinkou_history WHERE hashtags LIKE ";
            // $query = $this->StringConcatANDLogic($count, $query);
            
            // $query.= " UNION ALL \n";
            // $query.= "SELECT * FROM tb_kouzousekkei_history WHERE hashtags LIKE ";
            // $query = $this->StringConcatANDLogic($count, $query);
            
            // $query.= " UNION ALL \n";
            // $query.= "SELECT * FROM tb_ishousekkei_history WHERE hashtags LIKE ";
            // $query = $this->StringConcatANDLogic($count, $query);
            
            // $query.= " UNION ALL \n";
            // $query.= "SELECT * FROM tb_mitsumori_sekisanbu_history WHERE hashtags LIKE ";
            // $query = $this->StringConcatANDLogic($count, $query);
            
            // $query.= " UNION ALL \n";
            // $query.= "SELECT * FROM tb_renewal_history WHERE hashtags LIKE ";
            // $query = $this->StringConcatANDLogic($count, $query);
            
            // $query.= " UNION ALL \n";
            // $query.= "SELECT * FROM tb_setsubisekkei_history WHERE hashtags LIKE ";
            // $query = $this->StringConcatANDLogic($count, $query);
            
            // $query.= " UNION ALL \n";
            // $query.= "SELECT * FROM tb_hinshitsukanribu_history WHERE hashtags LIKE ";
            // $query = $this->StringConcatANDLogic($count, $query);
            
            // $query.= " UNION ALL \n";
            // $query.= "SELECT * FROM tb_koujibu_history WHERE hashtags LIKE ";
            // $query = $this->StringConcatANDLogic($count, $query);
            
            // $query.= " UNION ALL \n";
            // $query.= "SELECT * FROM tb_seisan_gijutsubu_history WHERE hashtags LIKE ";
            // $query = $this->StringConcatANDLogic($count, $query);
            
            // $query.= " UNION ALL \n";
            // $query.= "SELECT * FROM tb_seisan_sekkeibu_history WHERE hashtags LIKE ";
            // $query = $this->StringConcatANDLogic($count, $query);
            
            // $query.= " UNION ALL \n";
            // $query.= "SELECT * FROM tb_kouji_jimusho_history WHERE hashtags LIKE ";
            // $query = $this->StringConcatANDLogic($count, $query);
            
            // $query.= " UNION ALL \n";
            // $query.= "SELECT * FROM tb_kouji_jimusho_setsubi_history WHERE hashtags LIKE ";
            // $query = $this->StringConcatANDLogic($count, $query);