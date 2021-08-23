<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class AllStoreModel extends Model
{

    function GetAllStore(){
      $query = "SELECT * FROM tb_allstore_info";
      $data = DB::select($query);     
      return json_decode(json_encode($data),true);
    }

    function GetIdByProjectCode($pj_code){
      $query = "SELECT id FROM tb_allstore_info WHERE a_pj_code = '$pj_code'";
      $data = DB::select($query);     
      return json_decode(json_encode($data),true);
    }
    
    function GetReportFlagHistory(){
      $query = "SELECT repo.*,allstore.a_pj_code,allstore.a_pj_name,CONCAT(per.first_name,' ',per.last_name) as personal_name
                FROM tb_report_flag_history repo
                LEFT JOIN tb_allstore_info as allstore on allstore.id = repo.allstore_id
                LEFT JOIN tb_personal as per on per.id = repo.personal_id";
      $data = DB::select($query);     
      return json_decode(json_encode($data),true);
    }

    function SetReportFlagHistory($allstore_id,$personal_id,$new_flag){
      $update_time = date("Y-m-d H:i:s");
      $cur_flag = $new_flag == 1 ? 0 : 1;
      try{
        $query = "INSERT INTO tb_report_flag_history(id,allstore_id,personal_id,update_time,cur_flag,new_flag) 
                  SELECT MAX(id) +1,$allstore_id,$personal_id,'$update_time',$cur_flag,$new_flag FROM tb_report_flag_history
                  ON DUPLICATE KEY UPDATE allstore_id=$allstore_id,personal_id=$personal_id,update_time='$update_time',cur_flag=$cur_flag,new_flag=$new_flag";
        DB::insert($query);
        
        return "success";
      }catch(Exception $e){
        return $e->getMessage();
      }
    }

}
