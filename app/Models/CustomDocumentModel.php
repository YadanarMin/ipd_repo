<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class CustomDocumentModel extends Model
{
   
    function GetAllstoreProjectByCode($ipdcode){
        try{
            // $query = "SELECT a_sekou_basyo as 施工場所,
            //                  a_pj_code ,
            //                  ( CASE WHEN b_tmp_pj_name != '' THEN b_tmp_pj_name
            //                   WHEN b_pj_name != '' THEN b_pj_name
            //                   ELSE a_pj_name END ) as プロジェクト名称,
            //                   IF(a_shiten IS NULL,b_shiten,a_shiten) as 支店,
            //                   a_kouji_kubun as 工事区分
            //          FROM tb_allstore_info WHERE a_pj_code = '$ipdcode' ";
            
            $query="SELECT * FROM tb_allstore_info WHERE a_pj_code = '$ipdcode'";
            $data = DB::select($query);     
            return json_decode(json_encode($data),true);
        }catch(Exception $e){
            return "query error when select allstore data.";
        }
    }
    
  
}
